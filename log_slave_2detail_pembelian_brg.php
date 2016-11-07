<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##klmpkBrg##kdBrg##tglDr##tanggalSampai";
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['klmpkBrg']==''?$klmpkBrg=$_GET['klmpkBrg']:$klmpkBrg=$_POST['klmpkBrg'];
$_POST['kdBrg']==''?$kdBrg=$_GET['kdBrg']:$kdBrg=$_POST['kdBrg'];;
$_POST['tglDr']==''?$tglDr=tanggalsystem($_GET['tglDr']):$tglDr=tanggalsystem($_POST['tglDr']);
$_POST['tanggalSampai']==''?$tanggalSampai=tanggalsystem($_GET['tanggalSampai']):$tanggalSampai=tanggalsystem($_POST['tanggalSampai']);
$_POST['lokBeli']==''?$lokBeli=$_GET['lokBeli']:$lokBeli=$_POST['lokBeli'];
$nmBrg=$_POST['nmBrg'];
$sKlmpk="select kode,kelompok from ".$dbname.".log_5klbarang order by kode";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $rKelompok[$rKlmpk['kode']]=$rKlmpk['kelompok'];
}

$sTgl="select nopp,tanggal from ".$dbname.".log_prapoht order by tanggal";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
    $rTglNopp[$rTgl['nopp']]=$rTgl['tanggal'];
}

if(($tglDr!='')||($tanggalSampai!=''))
        {
                $where.=" and (tanggal between '".$tglDr."' and '".$tanggalSampai."')";
        }
        if($lokBeli!='')
        {
            $where.=" and lokalpusat='".$lokBeli."'";
        }
        if($klmpkBrg!='')
        {
                $where.=" and substr(kodebarang,1,3)='".$klmpkBrg."'";
        }
        if($kdBrg!='')
        {
                $where.=" and kodebarang='".$kdBrg."'";
        }




switch($proses)
{
        case'getBrg':
        //echo "warning:masuk";
        $optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sOrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='".$klmpkBrg."'";
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optorg.="<option value=".$rOrg['kodebarang'].">".$rOrg['namabarang']."</option>";
        }
        echo $optorg;
        break;
        case'preview':
        if(($tglDr=='')||($tanggalSampai==''))
        {
        echo"warning: Period not correct";
        exit();
        }
        $tab.="<table cellspacing=1 border=0 class=sortable>
        <thead >
        <tr class=rowheader>
                <td>".$_SESSION['lang']['kodebarang']."</td>
                <td>".$_SESSION['lang']['namabarang']."</td>
                <td>".$_SESSION['lang']['nopo']."</td>
                <td>".$_SESSION['lang']['tanggal']."</td>
                <td>".$_SESSION['lang']['jmlhPesan']."</td>
                <td>".$_SESSION['lang']['matauang']."</td>
                <td>".$_SESSION['lang']['hargasatuan']."</td>
                <td>".$_SESSION['lang']['total']."</td>
                <td>".$_SESSION['lang']['namasupplier']."</td>
                <td>".$_SESSION['lang']['nopp']."</td>
                <td>".$_SESSION['lang']['keterangan']."</td>                    
                <td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['prmntaanPembelian']." </td>


        </tr>
        </thead>
        <tbody>";

        $data=array();
        $brs=1;
//	$sData="select distinct a.kodesupplier,a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
//            where a.statuspo>1 ".$where." group by kodebarang";
        //
        $sData="select distinct kodebarang,namasupplier,namabarang,kurs,nopo,jumlahpesan,hargasatuan,nopp,satuan,tanggal,matauang 
                from ".$dbname.".log_po_vw where statuspo>1 ".$where." order by kodebarang asc";
        //echo $sData;
         //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        $kdBrng="";
        while($rData=mysql_fetch_assoc($qData))
        {
              $data[]=$rData;
        }
        foreach($data as $row => $rList)
        {
                $totHrg=0;
            if($rList['kodebarang']!='')
                {
                $no+=1;

                        if($rList['matauang']!='IDR')
                        {
                              if($rList['matauang']!='') 
                              {

                                        $hrg=$rList['kurs']*$rList['hargasatuan'];
                                        $totHrg=$rList['jumlahpesan']*$hrg;

                              }
                              else
                              {
                                  $totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                                  $hrg=$rList['hargasatuan'];
                              }
                        }
                        else
                        {
                                $totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                        }

                        $grandTotal+=$totHrg;
                        if($rList['nopp']!="")
                        {
                                if(($rTglNopp[$rList['nopp']]!="")||($rTglNopp[$rList['nopp']]!="000-00-00"))
                                {
                                                $tglPP=tanggalnormal($rTglNopp[$rList['nopp']]);		
                                }
                                else
                                {
                                        $tglPP="";
                                }
                        }
                        else
                        {
                                $tglPP="";
                        }

                        if($klmpkBarang!=substr($rList['kodebarang'],0,3))
                         {       
                             $brs=1; 
                         }
                        if($brs==1) 
                        {
                            $klmpkBarang=substr($rList['kodebarang'],0,3);
                            $tab.="<tr class='rowcontent'>";
                            $tab.="<td><b>".substr($rList['kodebarang'],0,3)."</b></td><td><b>".$rKelompok[$klmpkBarang]."</b></td>";
                            $tab.="<td colspan=10>&nbsp;</td>";
                            $tab.="</tr>";
                            $brs=0;
                         }
                         //ambil jumlah minta di pp
                            $sJmlh="select distinct jumlah,keterangan from ".$dbname.".log_prapodt 
                                    where nopp='".$rList['nopp']."' and kodebarang='".$rList['kodebarang']."'";
                            $qJmlh=mysql_query($sJmlh) or die(mysql_error());
                            $rJmlh=mysql_fetch_assoc($qJmlh);
                        $tab.="<tr class='rowcontent'>";
                        $tab.="<td>".$rList['kodebarang']."</td>";
                        $tab.="<td>".$rList['namabarang']."</td>";
                        $tab.="<td>".$rList['nopo']."</td>";
                        $tab.="<td>".tanggalnormal($rList['tanggal'])."</td>";
                        $tab.="<td align=center>".$rList['jumlahpesan']."</td>";
                        $tab.="<td align=center>".$rList['matauang']."</td>";
                        $tab.="<td align=right>".number_format($rList['hargasatuan'],2)."</td>";
                        $tab.="<td align=right>".number_format($totHrg,2)."</td>";
                        $tab.="<td>".$rList['namasupplier']."</td>";
                        $tab.="<td>".$rList['nopp']."</td>";
                        $tab.="<td>".$rJmlh['keterangan']."</td>";
                        $tab.="<td>".$tglPP."</td>";

                        $tab.="</tr>";

                    //}	
                }

        }
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td colspan='7' align='right'><b>Sub Total </b></td>";
                $tab .= "<td align=right>".number_format($grandTotal,0)."</td>";
                $tab .= "<td colspan='4' >&nbsp;</td>";
                $tab .= "</tr>";
                $tab.="</tbody></table>";
        echo $tab;
        break;
        case'pdf':
        if(($tglDr=='')||($tanggalSampai==''))
        {
        echo"warning: Period is obligatory";
        exit();
        }

         class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                                global $klmpkBrg;
                                global $kdBrg;
                                global $tglDr;
                                global $tanggalSampai;
                                global $where;
                                global $isi;
                                global $rNamaBarang;
                                global $rNamaSupplier;
                                global $where;

                                $isi=array();

                # Alamat & No Telp

                                $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
                                $qAlamat=mysql_query($sAlmat) or die(mysql_error());
                                $rAlamat=mysql_fetch_assoc($qAlamat);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();	
                $this->Ln();
                                $this->Ln();
                $this->SetFont('Arial','B',11);
                $this->Cell($width,$height, $_SESSION['lang']['detPembBrg'],0,1,'C');	
                                $this->SetFont('Arial','',8);
                                $this->Cell($width,$height, "Periode : ".$_GET['tglDr']." s.d. ".$_GET['tanggalSampai'],0,1,'C');	
                                $this->Ln();$this->Ln();
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);			
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['kodebarang'],1,0,'C',1);		
                                $this->Cell(18/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);		
                                $this->Cell(14/100*$width,$height,$_SESSION['lang']['nopo'],1,0,'C',1);			
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);	
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['matauang'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
                                $this->Cell(18/100*$width,$height,$_SESSION['lang']['namasupplier'],1,1,'C',1);	
                                //$this->Cell(12/100*$width,$height,$_SESSION['lang']['nopp'],1,0,'C',1);	
                                //$this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal']." PP",1,1,'C',1);					

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 11;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);
                $sData="select distinct kodebarang,namasupplier,namabarang,kurs,nopo,jumlahpesan,hargasatuan,nopp,satuan,tanggal,matauang
                        from ".$dbname.".log_po_vw where statuspo>1 ".$where." order by kodebarang asc";
        //exit("Error".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        $kdBrng="";
        while($rData=mysql_fetch_assoc($qData))
        {
              $data[]=$rData;
        }
        $totalAll=array();
        foreach($data as $test => $dt)
        {

                if($dt['kodebarang']!='')
                {

                        if($dt['matauang']!='IDR')
                        {

                                    $hrg=$dt['kurs']*$dt['hargasatuan'];
                                    $totHrg=$dt['jumlahpesan']*$hrg;
                        }
                        else
                        {
                                $totHrg=$dt['jumlahpesan']*$dt['hargasatuan'];
                        }
                        //$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
                        $grandTot['total']+=$totHrg;
                        if($dt['nopp']!="")
                        {
                                if(($rTglNopp[$dt['nopp']]!="")||($rTglNopp[$dt['nopp']]!="000-00-00"))
                                {
                                                $tglPP=tanggalnormal($rTglNopp[$dt['nopp']]);		
                                }
                                else
                                {
                                        $tglPP="";
                                }
                        }
                        else
                        {
                                $tglPP="";
                        }



                        if($klmpkBarang!=substr($dt['kodebarang'],0,3))
                        {       
                        $brs=1; 
                        }
                        if($brs==1) 
                        {
                            $pdf->SetFont('Arial','B',8);
                        $klmpkBarang=substr($dt['kodebarang'],0,3);
                        $pdf->Cell(6/100*$width,$height,substr($dt['kodebarang'],0,3),'TBLR',0,'C',1);
                        $pdf->Cell(18/100*$width,$height,$rKelompok[$klmpkBarang],'TBLR',0,'L',1);
                        $pdf->Cell(67/100*$width,$height,'','TBLR',1,'C',1);
                        $brs=0;
                        }	
                        $pdf->SetFont('Arial','',8);
                        $pdf->Cell(6/100*$width,$height,$dt['kodebarang'],1,0,'C',1);		
                        $pdf->Cell(18/100*$width,$height,$dt['namabarang'],1,0,'L',1);
                        $pdf->Cell(14/100*$width,$height,$dt['nopo'],1,0,'L',1);		
                        $pdf->Cell(6/100*$width,$height,tanggalnormal($dt['tanggal']),1,0,'C',1);			
                        $pdf->Cell(5/100*$width,$height,$dt['jumlahpesan'],1,0,'R',1);	
                        $pdf->Cell(5/100*$width,$height,$dt['satuan'],1,0,'C',1);
                        $pdf->Cell(5/100*$width,$height,$dt['matauang'],1,0,'C',1);
                        $pdf->Cell(7/100*$width,$height,number_format($dt['hargasatuan'],2),1,0,'R',1);		
                        $pdf->Cell(7/100*$width,$height,number_format($totHrg,2),1,0,'R',1);
                        $pdf->Cell(18/100*$width,$height,$dt['namasupplier'],1,1,'L',1);
                        //$pdf->Cell(12/100*$width,$height,$dt['nopp'],1,0,'L',1);	
                        //$pdf->Cell(6/100*$width,$height,$tglPP,1,1,'C',1);	
                }

        }


        $pdf->Output();
        break;
        case'excel':

        $tab.="
        <table>
    <tr><td colspan=10 align=center>".$_SESSION['lang']['detPembBrg']."</td></tr>
    <tr><td colspan=10 align=center>Periode : ".$_GET['tglDr']." s.d. ".$_GET['tanggalSampai']."</td></tr>
    </table>";

        $tab.="<table cellspacing=1 border=1 class=sortable>
        <thead >
        <tr class=rowheader>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopo']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhPesan']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['matauang']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopp']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['prmntaanPembelian']." </td>

        </tr>
        </thead>
        <tbody>";

        $data=array();
        $sData="select distinct kodebarang,namasupplier,namabarang,kurs,nopo,jumlahpesan,hargasatuan,nopp,satuan,tanggal,matauang from ".$dbname.".log_po_vw  
        where statuspo>1 ".$where." order by kodebarang asc";

        $qData=mysql_query($sData) or die(mysql_error($conn));
        $kdBrng="";
        while($rData=mysql_fetch_assoc($qData))
        {
              $data[]=$rData;
        }
        foreach($data as $row => $dt)
        {
                if($dt['kodebarang']!='')
                {
                $no+=1;


                        if($dt['matauang']!='IDR')
                        {
 
                                $hrg=$dt['kurs']*$dt['hargasatuan'];
                                $totHrg=$dt['jumlahpesan']*$hrg;
                        }
                        else
                        {
                                $totHrg=$dt['jumlahpesan']*$dt['hargasatuan'];
                        }
                        $grandTotal+=$totHrg;
                        if($dt['nopp']!="")
                        {
                                if(($rTglNopp[$dt['nopp']]!="")||($rTglNopp[$dt['nopp']]!="000-00-00"))
                                {
                                                $tglPP=$rTglNopp[$dt['nopp']];		
                                }
                                else
                                {
                                        $tglPP="";
                                }
                        }
                        else
                        {
                                $tglPP="";
                        }

                        if($klmpkBarang!=substr($dt['kodebarang'],0,3))
                         {       
                             $brs=1; 
                         }
                        if($brs==1) 
                        {
                            $klmpkBarang=substr($dt['kodebarang'],0,3);
                            $tab.="<tr class='rowcontent'>";
                            $tab.="<td><b>".substr($dt['kodebarang'],0,3)."</b></td><td><b>".$rKelompok[$klmpkBarang]."</b></td>";
                            $tab.="<td colspan=9>&nbsp;</td>";
                            $tab.="</tr>";
                            $brs=0;

                         }
                         //ambil jumlah minta di pp
                            $sJmlh="select distinct jumlah,keterangan  from ".$dbname.".log_prapodt 
                                    where nopp='".$dt['nopp']."' and kodebarang='".$dt['kodebarang']."'";
                            $qJmlh=mysql_query($sJmlh) or die(mysql_error());
                            $rJmlh=mysql_fetch_assoc($qJmlh);

                        $tab.="<tr class='rowcontent'>";
                        $tab.="<td>".$dt['kodebarang']."</td>";
                        $tab.="<td>".$dt['namabarang']."</td>";
                        $tab.="<td>".$dt['nopo']."</td>";
                        $tab.="<td>".$dt['tanggal']."</td>";
                        $tab.="<td align=center>".$dt['jumlahpesan']."</td>";
                        $tab.="<td align=center>".$dt['matauang']."</td>";
                        $tab.="<td align=right>".number_format($dt['hargasatuan'],2)."</td>";
                        $tab.="<td align=right>".number_format($totHrg,2)."</td>";
                        $tab.="<td>".$dt['namasupplier']."</td>";
                        $tab.="<td>".$dt['nopp']."</td>";
                        $tab.="<td>".$rJmlh['keterangan']."</td>";
                        $tab.="<td>".$tglPP."</td>";

                        $tab.="</tr>";

                //}	
                }

        }
                $tab .= "<tr class='rowcontent'>";
                $tab .= "<td colspan='7' align='right'><b>Sub Total </b></td>";
                $tab .= "<td align=right>".number_format($grandTotal,0)."</td>";
                $tab .= "<td colspan='4' >&nbsp;</td>";
                $tab .= "</tr>";
                $tab.="</tbody></table>";


                        //echo "warning:".$strx;
                        //=================================================


                        $tab.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        $thisDate=date("YmdHms");
                        //$nop_="Laporan_Pembelian";
                        $nop_="Laporan_Pembelian_Brg_".$thisDate;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $tab);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";
                        /*if(strlen($tab)>0)
                        {
                        if ($handle = opendir('tempExcel')) {
                        while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                        }
                        }	
                        closedir($handle);
                        }
                        $handle=fopen("tempExcel/".$nop_.".xls",'w');
                        if(!fwrite($handle,$tab))
                        {
                        echo "<script language=javascript1.2>
                        parent.window.alert('Can't convert to excel format');
                        </script>";
                        exit;
                        }
                        else
                        {
                        echo "<script language=javascript1.2>
                        window.location='tempExcel/".$nop_.".xls';
                        </script>";
                        }
                        closedir($handle);
                        }*/
        break;
        case'getTgl':
        if($periode!='')
        {
                $tgl=$periode;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
        elseif($period!='')
        {
                $tgl=$period;
                $tanggal=$tgl[0]."-".$tgl[1];
        }
        if($kdUnit=='')
        {
            $kdUnit=$_SESSION['lang']['lokasitugas'];
        }
        $sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit,0,4)."' and periode='".$tanggal."' ";
        //echo"warning".$sTgl;
        $qTgl=mysql_query($sTgl) or die(mysql_error());
        $rTgl=mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
        break;
         case'getBarang':
               $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";
            $sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where  kelompokbarang='".$klmpkBrg."' and (kodebarang like '%".$nmBrg."%'
            or namabarang like '%".$nmBrg."%')";
       // echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."')\">";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;

        break;

        default:
        break;
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
        $proses=$_POST['proses'];
}
else
{
        $proses=$_GET['proses'];
}
//$arr="##klmpkBrg##kdBrg##tglDr##tanggalSampai";
$_POST['klmpkBrg']==''?$klmpkBrg=$_GET['klmpkBrg']:$klmpkBrg=$_POST['klmpkBrg'];
$_POST['kdBrg']==''?$kdBrg=$_GET['kdBrg']:$kdBrg=$_POST['kdBrg'];
$_POST['tglDr']==''?$tglDr=$_GET['tglDr']:$tglDr=$_POST['tglDr'];
$_POST['tanggalSampai']==''?$tanggalSampai=$_GET['tanggalSampai']:$tanggalSampai=$_POST['tanggalSampai'];
$_POST['lokBeli']==''?$lokBeli=$_GET['lokBeli']:$lokBeli=$_POST['lokBeli'];

if($tglDr==''||$tanggalSampai=='')
{
    exit("Error: Period required");
}
$sKlmpk="select kode,kelompok from ".$dbname.".log_5klbarang order by kode";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $rKelompok[$rKlmpk['kode']]=$rKlmpk['kelompok'];
}
$arrStatus=array("0"=>"Head Office","1"=>"Local");
$sTgl="select nopp,tanggal from ".$dbname.".log_prapoht order by tanggal";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
    $rTglNopp[$rTgl['nopp']]=$rTgl['tanggal'];
}


        if($klmpkBrg!=''&&$kdBrg!='')
        {
                $where.=" and kodebarang='".$kdBrg."'";
        }
        elseif($klmpkBrg!='')
        {
                $where.=" and substr(kodebarang,1,3)='".$klmpkBrg."'";
        }

        if(($tglDr!='')||($tanggalSampai!=''))
        {
            if((strlen($tglDr)>'4')&&(strlen($tanggalSampai)>'4'))
            {
             $where.=" and substr(tanggal,1,7) between '".$tglDr."' and '".$tanggalSampai."'";
             $blnThn="  substr(tanggal,1,7) between '".$tglDr."' and '".$tanggalSampai."'";
            }
            elseif((strlen($tglDr)=='4')&&(strlen($tanggalSampai)=='4'))
            {
                 $where.=" and substr(tanggal,1,4) between '".$tglDr."' and '".$tanggalSampai."'";
                  $blnThn=" substr(tanggal,1,4) between '".$tglDr."' and '".$tanggalSampai."'";
            }
            else
            {
                exit("Error: period required");
            }
        }
        if($lokBeli!='')
        {
            $where.=" and lokalpusat='".$lokBeli."'";
        }
        $sBln="select distinct substring(tanggal,1,7) as periode from ".$dbname.".log_poht where ".$blnThn." order by substring(tanggal,1,7) asc";
        //exit("Error".$sBln);
        $qBln=mysql_query($sBln) or die(mysql_error());
        while($rBln=mysql_fetch_assoc($qBln))
        {
            $dtBLn[]=$rBln['periode'];
            $totPeriode+=1;
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
        //$totPeriode=count($dtBln);

        if(($tglDr=='')||($tanggalSampai==''))
        {
        echo"warning: Period required";
        exit();
        }
        $tab.="<table cellspacing=1 border=0 class=sortable>
        <thead >
        <tr class=rowheader >
                <td rowspan=2 align=center>No.</td>
                <td rowspan=2 align=center>".$_SESSION['lang']['kodebarang']."</td>
                <td rowspan=2 align=center>".$_SESSION['lang']['namabarang']."</td>
                <td rowspan=2 align=center>".$_SESSION['lang']['satuan']."</td>";
        foreach($dtBLn as $brsBln)
        {
            $tab.="<td align=center colspan=5>".$brsBln."</td>";
        }
        $tab.="</tr>";
        $tab.="<tr class=rowheader >";
        for($a=1;$a<=$totPeriode;$a++)
        {
        $tab.="<td align=center>".$_SESSION['lang']['tanggal']."</td><td colspan=2 align=center>".$_SESSION['lang']['harga']."</td><td align=center>".$_SESSION['lang']['namasupplier']."</td><td align=center>".$_SESSION['lang']['status']."</td>";

        }
        $tab.="</tr>";
        $tab.="
        </thead>
        <tbody>";
        $data=array();
        $brs=1;
        $sData="SELECT * FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." order by kodebarang,tanggal asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));

        while($rData=mysql_fetch_assoc($qData))
        {
              $periode=substr($rData['tanggal'],0,7);
              $dataPrice[$periode][$rData['kodebarang']]=$rData['hargasatuan'];
              $dataNmSupplier[$periode][$rData['kodebarang']]=$rData['namasupplier'];
              $dataTgl[$periode][$rData['kodebarang']]=$rData['tanggal'];
              $dataMtUang[$periode][$rData['kodebarang']]=$rData['matauang'];
              $dataStatus[$periode][$rData['kodebarang']]=$rData['lokalpusat'];
        }
        $sData2="SELECT kodebarang,namabarang,satuan FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." group by kodebarang order by kodebarang,tanggal asc";
        //exit("Error".$sData2);
        $qData2=mysql_query($sData2) or die(mysql_error($conn));
         $brsData=mysql_num_rows($qData2);
        if($brsData<1)
        {
            exit("Error: No data found");
        }
        else
        {
            while($rData2=mysql_fetch_assoc($qData2))
            {
                  $dataBrg[$rData2['kodebarang']]=$rData2['kodebarang'];
                  $namaBrg[$rData2['kodebarang']]=$rData2['namabarang'];
                  $satuanBrg[$rData2['kodebarang']]=$rData2['satuan'];
            }
            $sData3="SELECT tanggal FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." order by kodebarang,tanggal asc";
            $qData3=mysql_query($sData3) or die(mysql_error($conn));

            while($rData3=mysql_fetch_assoc($qData3))
            {
                  $dataTgl[]=$rData3['tanggal'];
            }

            foreach($dataBrg as $row => $rList)
            {
                        $no+=1;
                            $tab.="<tr class='rowcontent'>";
                            $tab.="<td>".$no."</td>";
                            $tab.="<td>".$rList."</td>";
                            $tab.="<td>".$namaBrg[$rList]."</td>";
                            $tab.="<td>".$satuanBrg[$rList]."</td>";
                foreach($dtBLn as $brsBln)
                {
                    $tab.="<td>".tanggalnormal($dataTgl[$brsBln][$rList])."</td>";
                    $tab.="<td>".$dataMtUang[$brsBln][$rList]."</td>";
                    $tab.="<td align=right>".number_format($dataPrice[$brsBln][$rList],2)."</td>";
                    $tab.="<td>".$dataNmSupplier[$brsBln][$rList]."</td>";
                     $tab.="<td>".$arrStatus[$dataStatus[$brsBln][$rList]]."</td>";
                }
                    $tab.="</tr>";
             }
                    $tab.="</tbody></table>";
            echo $tab;
        }
        break;
        case'pdf':
        if(($tglDr=='')||($tanggalSampai==''))
        {
        echo"warning: Period required";
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
       /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                    "kodeorganisasi='".$kdPt."'");
                $orgData = fetchData($query);*/
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
                                $this->Cell(20/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);		
                                $this->Cell(14/100*$width,$height,$_SESSION['lang']['nopo'],1,0,'C',1);			
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);	
                                $this->Cell(5/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
                                $this->Cell(4/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['namasupplier'],1,0,'C',1);	
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['nopp'],1,0,'C',1);	
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal']." PP",1,1,'C',1);					

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
                $sData="select distinct kodebarang,namasupplier,namabarang,kurs,nopo,jumlahpesan,hargasatuan,nopp,satuan,tanggal from ".$dbname.".log_po_vw  
        where statuspo>1 ".$where." order by kodebarang ,tanggal asc";
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
                            if($dt['kurs']==1||$dt['kurs']=='')
                            {
                                $sKurs="select kurs from ".$dbname.".setup_matauangrate where kode='".$dt['matauang']."' and daritanggal='".$dt['tanggal']."'";
                                $qKurs=mysql_query($sKurs) or die(mysql_error());
                                $rKurs=mysql_fetch_assoc($qKurs);
                                if($rKurs['kurs']!='')
                                {
                                        $hrg=$rKurs['kurs']*$dt['hargasatuan'];
                                        $totHrg=$dt['jumlahpesan']*$hrg;
                                }
                                else
                                {
                                        if($dt['matauang']=='USD')
                                        {
                                                $hrg=$dt['hargasatuan']*8850;
                                                $totHrg=$dt['jumlahpesan']*$hrg;
                                                $dt['matauang']="IDR";
                                        }
                                        elseif($dt['matauang']=='EUR') 
                                        {
                                                $hrg=$dt['hargasatuan']*12643;
                                                $totHrg=$dt['jumlahpesan']*$hrg;
                                                $dt['matauang']="IDR";
                                        }
                                        elseif(($dt['matauang']=='')||($dt['matauang']=='NULL'))
                                        {
                                                $totHrg=$dt['jumlahpesan']*$dt['hargasatuan'];
                                        }
                                }
                            }
                            else
                            {
                                    $hrg=$dt['kurs']*$dt['hargasatuan'];
                                    $totHrg=$dt['jumlahpesan']*$hrg;
                            }
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
                        $pdf->Cell(6/100*$width,$height,substr($dt['kodebarang'],0,3),'TLR',0,'C',1);
                        $pdf->Cell(20/100*$width,$height,$rKelompok[$klmpkBarang],'TLR',0,'L',1);
                        $pdf->Cell(76/100*$width,$height,'','TLR',1,'C',1);
                        $brs=0;
                        }	
                        $pdf->SetFont('Arial','',8);
                        $pdf->Cell(6/100*$width,$height,$dt['kodebarang'],1,0,'C',1);		
                        $pdf->Cell(20/100*$width,$height,$dt['namabarang'],1,0,'L',1);
                        $pdf->Cell(14/100*$width,$height,$dt['nopo'],1,0,'L',1);		
                        $pdf->Cell(6/100*$width,$height,tanggalnormal($dt['tanggal']),1,0,'C',1);			
                        $pdf->Cell(5/100*$width,$height,$dt['jumlahpesan'],1,0,'R',1);	
                        $pdf->Cell(4/100*$width,$height,$dt['satuan'],1,0,'C',1);
                        $pdf->Cell(7/100*$width,$height,number_format($dt['hargasatuan'],0),1,0,'R',1);		
                        $pdf->Cell(7/100*$width,$height,number_format($totHrg,0),1,0,'R',1);
                        $pdf->Cell(15/100*$width,$height,$dt['namasupplier'],1,0,'L',1);
                        $pdf->Cell(12/100*$width,$height,$dt['nopp'],1,0,'L',1);	
                        $pdf->Cell(6/100*$width,$height,$tglPP,1,1,'C',1);	
                }

        }


        $pdf->Output();
        break;
        case'excel':

        if(($tglDr=='')||($tanggalSampai==''))
        {
        echo"warning: Period required";
        exit();
        }
        $tab.="<table cellspacing=1 border=1 class=sortable>
        <thead >
        <tr class=rowheader >
                <td rowspan=2 bgcolor=#DEDEDE align=center >No.</td>
                <td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
                <td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
                <td rowspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>";
        foreach($dtBLn as $brsBln)
        {
            $tab.="<td bgcolor=#DEDEDE align=center colspan=5>".$brsBln."</td>";
        }
        $tab.="</tr>";
        $tab.="<tr class=rowheader >";
        for($a=1;$a<=$totPeriode;$a++)
        {
        $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td><td colspan=2 bgcolor=#DEDEDE align=center>".$_SESSION['lang']['harga']."</td><td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td><td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['status']."</td>";

        }
        $tab.="</tr>";
        $tab.="
        </thead>
        <tbody>";
        $data=array();
        $brs=1;
        $sData="SELECT * FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." order by kodebarang,tanggal asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));

        while($rData=mysql_fetch_assoc($qData))
        {
              $periode=substr($rData['tanggal'],0,7);
              $dataPrice[$periode][$rData['kodebarang']]=$rData['hargasatuan'];
              $dataNmSupplier[$periode][$rData['kodebarang']]=$rData['namasupplier'];
              $dataTgl[$periode][$rData['kodebarang']]=$rData['tanggal'];
              $dataMtUang[$periode][$rData['kodebarang']]=$rData['matauang'];
              $dataStatus[$periode][$rData['kodebarang']]=$rData['lokalpusat'];
        }
        $sData2="SELECT kodebarang,namabarang,satuan FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." group by kodebarang order by kodebarang,tanggal asc";
        //exit("Error".$sData2);
        $qData2=mysql_query($sData2) or die(mysql_error($conn));
         $brsData=mysql_num_rows($qData2);
        if($brsData<1)
        {
            exit("Error: No data found");
        }
        else
        {
            while($rData2=mysql_fetch_assoc($qData2))
            {
                  $dataBrg[$rData2['kodebarang']]=$rData2['kodebarang'];
                  $namaBrg[$rData2['kodebarang']]=$rData2['namabarang'];
                  $satuanBrg[$rData2['kodebarang']]=$rData2['satuan'];
            }
            $sData3="SELECT tanggal FROM ".$dbname.".`log_po_vw` WHERE statuspo>1 ".$where." order by kodebarang,tanggal asc";
            $qData3=mysql_query($sData3) or die(mysql_error($conn));

            while($rData3=mysql_fetch_assoc($qData3))
            {
                  $dataTgl[]=$rData3['tanggal'];
            }

            foreach($dataBrg as $row => $rList)
            {
                        $no+=1;
                            $tab.="<tr class='rowcontent'>";
                            $tab.="<td>".$no."</td>";
                            $tab.="<td>".$rList."</td>";
                            $tab.="<td>".$namaBrg[$rList]."</td>";
                            $tab.="<td>".$satuanBrg[$rList]."</td>";
                foreach($dtBLn as $brsBln)
                {
                    $tab.="<td>".tanggalnormal($dataTgl[$brsBln][$rList])."</td>";
                    $tab.="<td>".$dataMtUang[$brsBln][$rList]."</td>";
                    $tab.="<td align=right>".number_format($dataPrice[$brsBln][$rList],2)."</td>";
                    $tab.="<td>".$dataNmSupplier[$brsBln][$rList]."</td>";
                     $tab.="<td>".$arrStatus[$dataStatus[$brsBln][$rList]]."</td>";
                }
                    $tab.="</tr>";
             }
                    $tab.="</tbody></table>";
           // echo $tab;
        }   


                        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        $thisDate=date("YmdHms");
                        //$nop_="Laporan_Pembelian";
                        $nop_="Laporan_Pembelian_Terakhir_".$thisDate;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $tab);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";
                       
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

        default:
        break;
}
?>
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
$kdGudang=$_POST['kdGudang'];
//$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
$periode=$_POST['periode'];
$kdVhc=$_POST['kdVhc'];

switch($proses)
{
        case'getPeriode':
        //echo "warning:masuk";
        $optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sOrg="select periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$kdGudang."'";
        $qOrg=mysql_query($sOrg) or die(mysql_error());
        while($rOrg=mysql_fetch_assoc($qOrg))
        {
                $optorg.="<option value=".$rOrg['periode'].">".substr(tanggalnormal($rOrg['periode']),1,8)."</option>";
        }
        echo $optorg;
        break;
        case'preview':
        if($_SESSION['language']=='EN'){
            $zz='namaakun1 as namaakun';
        }else{
            $zz='namaakun';
        }     
        $sAkun="select noakun,".$zz." from ".$dbname.".keu_5akun order by noakun";
        $qAkun=mysql_query($sAkun) or die(mysql_error($conn));
        while($rAkun=  mysql_fetch_assoc($qAkun))    
        {
            $hslAkun[$rAkun['noakun']]=$rAkun['namaakun'];
        }
        
        if($kdGudang==''||$periode=='')
        {
                echo"warning:Gudang  dan Periode Tidak Boleh Kosong";
                exit();
        }

        $tab.="<table cellspacing=1 border=0 class=sortable>
        <thead class=rowheader>
        <tr>
                <td>No.</td>
                <td>".$_SESSION['lang']['nojurnal']."</td>
                <td>".$_SESSION['lang']['tanggal']."</td>
                <td>".$_SESSION['lang']['notransaksi']."</td>
                <td>".$_SESSION['lang']['keterangan']."</td>
                <td>".$_SESSION['lang']['noakundisplay']."</td>
                <td>".$_SESSION['lang']['akun']."</td>
                <td>".$_SESSION['lang']['debet']."</td>
                <td>".$_SESSION['lang']['kredit']."</td>
                <td>".$_SESSION['lang']['blok']."</td>
                <td>".$_SESSION['lang']['kodevhc']."</td>

        </tr>
        </thead>
        <tbody>";


            $where=" tanggal like '%".$periode."%' and noreferensi like '%".$kdGudang."%' 
                and noreferensi in(select distinct notransaksi from ".$dbname.".log_transaksi_vw where kodegudang='".$kdGudang."' and kodeorg='".substr($kdGudang,0,4)."')";
            if ($kdVhc!=''){
                $where.=" and kodevhc='".$kdVhc."'";
            }
        $sData="select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ".$dbname.".keu_jurnaldt_vw 
            where ".$where."";
       // echo $sData;exit();
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rData['nojurnal']."</td>
                <td>".tanggalnormal($rData['tanggal'])."</td>
                <td>".$rData['noreferensi']."</td>
                <td>".$rData['keterangan']."</td>
                <td>".$rData['noakun']."</td>
                <td>".$hslAkun[$rData['noakun']]."</td>
                <td align=right>".number_format($rData['debet'])."</td>
                <td align=right>".number_format($rData['kredit'])."</td>
                <td>".$rData['kodeblok']."</td>
                <td>".$rData['kodevhc']."</td>

        </tr>";
            $totalDebet+=$rData['debet'];
            $totalKredit+=$rData['kredit'];
        }
        $tab.="<tr><td colspan='7' align=right>Total</td><td align=right>".number_format($totalDebet,2)."</td><td align=right>".number_format($totalDebet,2)."</td>";
        $tab.="</tbody></table>";
        echo $tab;
        break;
        case'pdf':
        $kdGudang=$_GET['kdGudang'];
        $periode=$_GET['periode'];
        if($_SESSION['language']=='EN'){
            $zz='namaakun1 as namaakun';
        }else{
            $zz='namaakun';
        }     
        $sAkun="select noakun,".$zz." from ".$dbname.".keu_5akun order by noakun";
        $qAkun=mysql_query($sAkun) or die(mysql_error($conn));
        while($rAkun=  mysql_fetch_assoc($qAkun))    
        {
            $hslAkun[$rAkun['noakun']]=$rAkun['namaakun'];
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
                                global $periode;
                                global $kdGudang;
                                global $hslAkun;
                                global $where;


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
                $this->Cell($width,$height, $_SESSION['lang']['lapAlokasiBrg'],0,1,'C');	
                                $this->SetFont('Arial','',8);
                                $this->Cell($width,$height, $_SESSION['lang']['unit'].": ".$_GET['kdGudang'],0,1,'C');	
                                $this->Cell($width,$height, $_SESSION['lang']['periode'].": ".$_GET['periode'],0,1,'C');	
                                $this->Ln();$this->Ln();
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);

                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['nojurnal'],1,0,'C',1);		
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
                                $this->Cell(12/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);			
                                $this->Cell(21/100*$width,$height,$_SESSION['lang']['keterangan'],1,0,'C',1);	
                                $this->Cell(6/100*$width,$height,$_SESSION['lang']['noakundisplay'],1,0,'C',1);		
                                $this->Cell(13/100*$width,$height,$_SESSION['lang']['akun'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['debet'],1,0,'C',1);
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['kredit'],1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);	
                                $this->Cell(7/100*$width,$height,$_SESSION['lang']['kodevhc'],1,1,'C',1);					

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
        $height = 9;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);
        $where=" tanggal like '%".$periode."%' and noreferensi like '%".$kdGudang."%' 
                and noreferensi in(select distinct notransaksi from ".$dbname.".log_transaksi_vw where kodegudang='".$kdGudang."' and kodeorg='".substr($kdGudang,0,4)."')";
            if ($kdVhc!=''){
                $where.=" and kodevhc='".$kdVhc."'";
            }
        $sData="select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ".$dbname.".keu_jurnaldt_vw 
            where ".$where."";
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $no+=1;
            $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
            $pdf->Cell(13/100*$width,$height,$rData['nojurnal'],1,0,'L',1);		
            $pdf->Cell(6/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
            $pdf->Cell(12/100*$width,$height,$rData['noreferensi'],1,0,'L',1);			
            $pdf->Cell(21/100*$width,$height,$rData['keterangan'],1,0,'L',1);	
            $pdf->Cell(6/100*$width,$height,$rData['noakun'],1,0,'C',1);		
            $pdf->Cell(13/100*$width,$height,$hslAkun[$rData['noakun']],1,0,'L',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['debet']),1,0,'R',1);
            $pdf->Cell(7/100*$width,$height,number_format($rData['kredit']),1,0,'R',1);	
            $pdf->Cell(7/100*$width,$height,$rData['kodeblok'],1,0,'L',1);	
            $pdf->Cell(7/100*$width,$height,$rData['kodevhc'],1,1,'L',1);	+
                        $totalDebet+=$rData['debet'];
                        $totalKredit+=$rData['kredit'];
        }
                $pdf->SetFont('Arial','B',7);
                $pdf->Cell(74/100*$width,$height,"Total",1,0,'R',1);
                $pdf->Cell(7/100*$width,$height,number_format($totalDebet,2),1,0,'R',1);
                $pdf->Cell(7/100*$width,$height,number_format($totalKredit,2),1,0,'R',1);
                $pdf->Cell(14/100*$width,$height,'',1,0,'R',1);




        $pdf->Output();
        break;
        case'excel':

        //$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
        $kdGudang=$_GET['kdGudang'];
        $periode=$_GET['periode'];

        if($_SESSION['language']=='EN'){
            $zz='namaakun1 as namaakun';
        }else{
            $zz='namaakun';
        }     
        $sAkun="select noakun,".$zz." from ".$dbname.".keu_5akun order by noakun";
        $qAkun=mysql_query($sAkun) or die(mysql_error($conn));
        while($rAkun=  mysql_fetch_assoc($qAkun))    
        {
            $hslAkun[$rAkun['noakun']]=$rAkun['namaakun'];
        }
        if($kdGudang==''||$periode=='')
        {
                echo"warning:Gudang  dan Periode Tidak Boleh Kosong";
                exit();
        }
        $tab.="<table border=0 cellpading=1 ><tr><td colspan=11 align=center>".$_SESSION['lang']['lapAlokasiBrg']."</td></tr>
                <tr><td colspan=3>".$_SESSION['lang']['periode']."</td><td colspan=4 align=left>".substr(tanggalnormal($periode),1,9)."</td></tr>    
                <tr><td colspan=3>".$_SESSION['lang']['unit']."</td><td colspan=4 align=left>".($kdGudang!=''?$kdGudang:$_SESSION['lang']['all'])."</td></tr>

                </table>";
        $tab.="<table cellspacing=1 border=1 class=sortable>
        <thead class=rowheader>
        <tr>
                <td bgcolor=#DEDEDE align=center>No.</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nojurnal']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakundisplay']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['akun']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['debet']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kredit']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['blok']."</td>
                <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodevhc']."</td>

        </tr>
        </thead>
        <tbody>";


            $where=" tanggal like '%".$periode."%' and noreferensi like '%".$kdGudang."%' 
                and noreferensi in(select distinct notransaksi from ".$dbname.".log_transaksi_vw where kodegudang='".$kdGudang."' and kodeorg='".substr($kdGudang,0,4)."')";
            if ($kdVhc!=''){
                $where.=" and kodevhc='".$kdVhc."'";
            }
        $sData="select nojurnal,tanggal,noreferensi,keterangan,noakun,debet,kredit,kodeblok,kodevhc from ".$dbname.".keu_jurnaldt_vw 
            where ".$where."";
       // echo $sData;exit();
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $no+=1;
            $tab.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rData['nojurnal']."</td>
                <td>".tanggalnormal($rData['tanggal'])."</td>
                <td>".$rData['noreferensi']."</td>
                <td>".$rData['keterangan']."</td>
                <td>".$rData['noakun']."</td>
                <td>".$hslAkun[$rData['noakun']]."</td>
                <td align=right>".number_format($rData['debet'])."</td>
                <td align=right>".number_format($rData['kredit'])."</td>
                <td>".$rData['kodeblok']."</td>
                <td>".$rData['kodevhc']."</td>

        </tr>";
            $totalDebet+=$rData['debet'];
            $totalKredit+=$rData['kredit'];
        }
        $tab.="<tr><td colspan='7' align=right>Total</td><td align=right>".number_format($totalDebet,2)."</td><td align=right>".number_format($totalDebet,2)."</td>";
        $tab.="</tbody></table>";


                        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                        $tglSkrng=date("Ymd");
                        $nop_="Laporan_Alokasi_Pemakai_Brg".$tglSkrng;
                        if(strlen($tab)>0)
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
                        }
        break;
        default:
        break;
}
?>
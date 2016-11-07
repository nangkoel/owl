<?php
require_once('master_validation.php');
//require_once('config/connection.php');
//require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['tipe']==''?$tipe=$_GET['tipe']:$tipe=$_POST['tipe'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$qwe=explode('-',$periode);
$tahun=$qwe[0];
$bulan=$qwe[1];


$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPt=makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
	
switch($proses)
{
    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

   class PDF extends FPDF {
    }

    $pdf=new PDF('L','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $allwidth = $pdf->w;
    $lmargin=$pdf->lMargin;
    $tmargin=$pdf->tMargin;
    $rmargin=$pdf->rMargin;
    $pdf->SetMargins($lmargin,$tmargin,$rmargin);
    $height = 10;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
    
    //crown + monanga group
    $path='images/lbm_cover1.jpg';
    $pdf->Image($path,0,0,841);	
    $path='images/lbm_cover2.jpg';
    $pdf->Image($path,80,130,470);
    
    //nama pt
    $pdf->SetY(220);   
    $pdf->SetFont('Arial','',30);
    $pdf->SetX(80);   
    $pdf->Cell(140,$height,$optNm[$optPt[$unit]],0,0,'L',1);

    //garis tengah
    $pdf->SetMargins(0,0,0);
    $pdf->SetY(270);   
    $pdf->SetFillColor(255,128,0);
    $pdf->Cell($allwidth,$height,'',0,0,'L',1);
    $pdf->SetY(280);   
    $pdf->SetFillColor(0,192,64);
    $pdf->Cell($allwidth,$height,'',0,0,'L',1);
    
    //LBM
    $pdf->SetY(300);   
    $pdf->SetFillColor(255,255,255);
    $pdf->SetMargins($lmargin,$tmargin,$rmargin);
    $pdf->SetY(350);   
    $pdf->Cell($allwidth,$height,strtoupper($_SESSION['lang']['lbm']),0,0,'C',1);
    
    //managerial report
    $pdf->SetY(380);   
    $pdf->SetFont('Arial','',20);
    $pdf->Cell($width,$height,$_SESSION['lang']['managerialreport'],0,0,'C',1);
    
    //estate, bulan, tahun
    $pdf->SetY(430);   
    $pdf->SetX(150);   
    $pdf->Cell(100,$height,$_SESSION['lang']['unit'],0,0,'L',1);
    $pdf->Cell(140,$height,' : '.$optNm[$unit].' ('.$unit.')',0,0,'L',1);
    $pdf->SetY(460);   
    $pdf->SetX(150); 
    $dert=490;
    if($afdId!='')
    {
        $dert=520;
        $pdf->Cell(100,$height,$_SESSION['lang']['afdeling'],0,0,'L',1);
        $pdf->Cell(140,$height,' : '.$optNm[$afdId].' ('.$afdId.')',0,0,'L',1);
        $pdf->SetY(490);   
        $pdf->SetX(150); 
    }
   
    $pdf->Cell(100,$height,$_SESSION['lang']['bulan'],0,0,'L',1);
    $pdf->Cell(140,$height,' : '.$optBulan[$bulan],0,0,'L',1);
    $pdf->SetY($dert);   
    $pdf->SetX(150);   
    $pdf->Cell(100,$height,$_SESSION['lang']['tahun'],0,0,'L',1);
    $pdf->Cell(140,$height,' : '.$tahun,0,0,'L',1);

    $pdf->Output();	
    break;
    case'getAfdl':
    $optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
           induk='".$unit."' and tipe in ('AFDELING', 'BIBITAN') order by namaorganisasi asc";
    //exit("Error:".$sOrg);
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=mysql_fetch_assoc($qOrg))
    {
        $optAfd.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
    }
    echo $optAfd;
    break;
    case'getKbn':
    $optKbn="<option value=''>".$_SESSION['lang']['all']."</option>";
    if ($tipe=='I'){
        $whr=" and namaorganisasi not like 'PLASMA%'";
    } else {
        $whr=" and namaorganisasi like 'PLASMA%'";
    }
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
           tipe='KEBUN' ".$whr." order by namaorganisasi asc";
    //exit("Error:".$sOrg);
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=mysql_fetch_assoc($qOrg))
    {
        $optKbn.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
    }
    echo $optKbn;
    break;
    case'getKbn2':
    $optKbn="<option value=''>".$_SESSION['lang']['all']."</option>";
    if ($tipe=='I'){
        $whr=" and namaorganisasi not like 'PLASMA%'";
    } else {
        $whr=" and namaorganisasi like 'PLASMA%'";
    }
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
           tipe in ('KEBUN','PABRIK') ".$whr." order by namaorganisasi asc";
    //exit("Error:".$sOrg);
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=mysql_fetch_assoc($qOrg))
    {
        $optKbn.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
    }
    echo $optKbn;
    break;
    case'getKbn3':
    $optKbn="<option value=''>".$_SESSION['lang']['all']."</option>";
    if ($tipe=='I'){
        $whr=" and namaorganisasi not like 'PLASMA%'";
    } else {
        $whr=" and namaorganisasi like 'PLASMA%'";
    }
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
           tipe in ('KEBUN','PABRIK','KANWIL') ".$whr." order by namaorganisasi asc";
    //exit("Error:".$sOrg);
    $qOrg=mysql_query($sOrg) or die(mysql_error($conn));
    while($rOrg=mysql_fetch_assoc($qOrg))
    {
        $optKbn.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
    }
    echo $optKbn;
    break;
    default:
    break;
}
	





// dz: dec 26, 2011 10:06 - pintu besar utara 6-8
?>
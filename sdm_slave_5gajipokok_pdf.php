<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

# Options

$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in(1,2,3,4,5)");
$optComp = makeOption($dbname,'sdm_ho_component','id,name',"type='basic'");

# Get Data
$cols = "tahun,karyawanid,idkomponen,jumlah";
$where=$_GET['cond'];
//exit("Error:$where");
/*
$where= "karyawanid in (";
$i=0;
foreach($optKary as $key=>$row) {
    if($i==0) {
        $where.= $key;
    } else {
        $where.= ",".$key;
    }
    $i++;
}
$where.= ")";
*/
$query = selectQuery($dbname,'sdm_5gajipokok',$cols,$where);
$data = fetchData($query);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['karyawanid'] = $optKary[$row['karyawanid']];
    $dataShow[$key]['idkomponen'] = $optComp[$row['idkomponen']];
	//$dataShow[$key]['namakaryawan'] = $optNmKar[$optKary[$row['karyawanid']]];
}

$title = $_SESSION['lang']['gajipokok'];
$colArr = explode(',',$cols);
$align = explode(",","L,L,L,R");
$length = explode(",","10,30,30,20");

# Print
$pdf=new zPdfMaster('P','pt','A4');
$pdf->_noThead=true;
$pdf->setAttr1($title,$align,$length,array());
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 12;
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',9);
$pdf->Ln();

$pdf->SetFont('Arial','B',9);
#$pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
$pdf->SetFillColor(220,220,220);
$i=0;
foreach($colArr as $column) {
    $pdf->Cell($length[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
    $i++;
}
$pdf->Ln();

# Content
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',9);
foreach($dataShow as $key=>$row) {    
    $i=0;
    foreach($row as $cont) {
        $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
        $i++;
    }
    $pdf->Ln();
}
$pdf->Ln();
$pdf->Output();
?>
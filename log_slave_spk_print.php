<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_POST;


/** Report Prep **/
$cols = 'notransaksi,tanggal,divisi,koderekanan';
$cols2 = 'notransaksi,tanggal,afdeling,koderekanan';
if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
   $_SESSION['empl']['tipelokasitugas']=='HOLDING' or
   $_SESSION['empl']['tipelokasitugas']=='KANWIL') {
    $where = "length(kodeorg)=4";
} else {
    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}
$colArr = explode(',',$cols2);
$query = selectQuery($dbname,'log_spkht',$cols,$where);
$data = fetchData($query);

$title = $_SESSION['lang']['spk'];
$align = explode(",","L,L,L,L");
$length = explode(",","25,25,25,25");

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        foreach($data as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');
#echo "<pre>";
#print_r($_SESSION);
#exit;
# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$order = 'kodeorg';

#====================== Prepare Data
# Prep Where
$arrWhere = explode('^^',$where);
$arrWhere['sep'] = $arrWhere[0];
$arrWhere['list'] = explode('~~',$arrWhere[1]);
foreach($arrWhere['list'] as $key=>$row) {
    $arrWhere['list'][$key] = explode('**',$row);
}
unset($arrWhere[0]);
unset($arrWhere[1]);

$newWhere = "";
foreach($arrWhere['list'] as $key=>$row) {
    if($key==0) {
	$newWhere .= $row[0]."='".$row[1]."'";
    } else {
	$newWhere .= " ".$arrWhere['sep']." ".$row[0]."='".$row[1]."'";
    }
}

$query = selectQuery($dbname,$table,'*',$newWhere,$order);
#echo $query;
$result = fetchData($query);
$header = array();
foreach($result[0] as $key=>$row) {
    $header[] = $key;
}

#====================== Prepare Header PDF
class masterpdf extends FPDF {
    function Header() {
        global $table;
        global $header;
        
        # Panjang, Lebar
        $width = $this->w - $this->lMargin - $this->rMargin;
		$height = 12;
        $this->SetFont('Arial','B',8);
		$this->Cell(20,$height,$_SESSION['org']['namaorganisasi'],'',1,'L');
        $this->SetFont('Arial','B',12);
#		$this->Cell($width,$height,'Tabel : '.$table,'',1,'L');
		$this->Cell($width,$height,strtoupper($_SESSION['lang']['daftarblok']),'',1,'C');
        $this->SetFont('Arial','B',8);
		$this->Cell(615,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(5,$height,':','',0,'L');
		$this->Cell(40,$height,date('d-m-Y H:i'),'',1,'L');
		$this->Cell(615,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(15,$height,$this->PageNo(),'',1,'L');
#        $this->Ln();
		$this->Cell(615,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['user'],'',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(20,$height,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        
        # Generate Header
#        foreach($header as $hName) {
#            $this->Cell($width/count($header),$height,ucfirst($hName),'TBLR',0,'L');
#        }
        $this->Cell(70,1.5*$height,$_SESSION['lang']['kodeblok'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['thntanam'],'TBLR',0,'C');
        $this->Cell(70,1.5*$height,$_SESSION['lang']['luasareaproduktif'],'TBLR',0,'C');
        $this->Cell(90,1.5*$height,$_SESSION['lang']['luasareanonproduktif'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['totalarea'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['jmlpokok'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['statusblok'],'TBLR',0,'C');
        $this->Cell(100,1.5*$height,$_SESSION['lang']['periodemulaipanen'],'TBLR',0,'C');
        $this->Cell(50,1.5*$height,$_SESSION['lang']['jenisbibit'],'TBLR',0,'C');
        $this->Cell(40,1.5*$height,$_SESSION['lang']['kodetanah'],'TBLR',0,'C');
        $this->Cell(80,1.5*$height,$_SESSION['lang']['klasifikasitanah'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['topografi'],'TBLR',0,'C');
        $this->Ln();
        $this->Ln();
    }
}

#====================== Prepare PDF Setting
$pdf = new masterpdf('L','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 12;
$pdf->SetFont('Arial','',8);
$pdf->AddPage();

# Generate Data
#print_r($result);
#exit;
#foreach($result as $row) {
#    foreach($row as $data) {
#        $pdf->Cell($width/count($header),$height,$data,'',0,'L');
#    }
#    $pdf->Ln();
#}
foreach($result as $data) {
	$pdf->Cell(70,$height,$data['kodeorg'],'',0,'C');
	$pdf->Cell(60,$height,$data['tahuntanam'],'',0,'C');
	$pdf->Cell(70,$height,number_format($data['luasareaproduktif'],2),'',0,'R');
	$pdf->Cell(70,$height,number_format($data['luasareanonproduktif'],2),'',0,'R');
	$totalarea = $data['luasareaproduktif'] + $data['luasareanonproduktif'];
	$pdf->Cell(80,$height,number_format($totalarea,2),'',0,'R');
	$pdf->Cell(60,$height,number_format($data['jumlahpokok'],0),'',0,'R');
	$pdf->Cell(60,$height,$data['statusblok'],'',0,'C');
	$pdf->Cell(50,$height,$data['bulanmulaipanen'],'',0,'R');
	$pdf->Cell(10,$height,'/','',0,'R');
	$pdf->Cell(50,$height,$data['tahunmulaipanen'],'',0,'L');
	$pdf->Cell(50,$height,$data['jenisbibit'],'',0,'L');
	$pdf->Cell(50,$height,$data['kodetanah'],'',0,'L');
	$pdf->Cell(50,$height,$data['klasifikasitanah'],'',0,'L');
	$pdf->Cell(50,$height,$data['topografi'],'',0,'L');
    $pdf->Ln();
}


# Print Out
$pdf->Output();
?>
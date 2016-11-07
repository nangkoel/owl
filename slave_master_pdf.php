<?php
session_start();
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

#====================== Prepare Data
$query = selectQuery($dbname,$table);
$result = fetchData($query);
$header = array();
foreach($result[0] as $key=>$row) {
    if($_SESSION['language']=='EN' && $_SESSION['lang'][strtolower($key)]!==''){
        $header[] = $_SESSION['lang'][strtolower($key)];
    }else{
      $header[] = $key;
    }
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
	$this->Cell($width,$height,'Tabel : '.$table,'',1,'L');
        $this->Ln();
        
        # Generate Header
        foreach($header as $hName) {
            $this->Cell($width/count($header),$height,ucfirst($hName),'TBLR',0,'L');
        }
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
foreach($result as $row) {
    foreach($row as $data) {
        $pdf->Cell($width/count($header),$height,$data,'',0,'L');
    }
    $pdf->Ln();
}

# Print Out
$pdf->Output();
?>
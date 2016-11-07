<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

class MinangaHeadOrderPdf extends FPDF{
    public $_apps;
    function Header() {
	# Panjang, Lebar
        $width = $this->w - $this->lMargin - $this->rMargin;
	$height = 12;
	$a=$this->Image('images/logo.jpg',20,10,120,65,'jpg','');
	$this->Cell(120,$height,$a,' ',0,'L');
        $this->SetFont('Arial','B',10);
	$this->Cell(40/100*$width,$height,'PERKEBUNAN MINANGA GROUP','',0,'L');
	$this->Cell(40/100*$width,$height,'KEPADA YTH :','',1,'L');
	$this->Cell(120,$height,' ','',0,'L');
	//$this->Cell(22/100*$width,$height,' ','',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(12/100*$width,$height,'UNIT KERJA','',0,'L');
	$this->Cell(2/100*$width,$height,':','',0,'L');
	$this->Cell(1/100*$width,$height,$_SESSION['unit'],'',0,'L');		
	$this->Cell(25/100*$width,$height,' ','',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(12/100*$width,$height,'PURCHASING DEPARTEMENT','',0,'L');
	$this->Cell(2/100*$width,$height,'','',0,'L');
	$this->Cell(1/100*$width,$height,'','',1,'L');

	//$this->Cell(40/100*$width,$height,strtoupper($_SESSION['org']['namaorganisasi']),'',0,'L');
	$this->Cell(120,$height,' ','',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(12/100*$width,$height,'PP NO','',0,'L');
	$this->Cell(2/100*$width,$height,':','',0,'L');
	$this->Cell(1/100*$width,$height,$_SESSION['nopp'],'',0,'L');		
	$this->Cell(25/100*$width,$height,' ','',0,'L');
	$this->SetFont('Arial','B',10);
	$this->Cell(14/100*$width,$height,'TANGGAL PP','',0,'L');
	$this->Cell(2/100*$width,$height,':','',0,'L');
	$this->Cell(1/100*$width,$height,$_SESSION['tgl'],'',1,'L');
    }
    
    function Footer() {
	$width = $this->w - $this->lMargin - $this->rMargin;
	$height = 12;
	$this->SetFont('Arial','I',8);
	$this->Cell($width,$height,$_SESSION['lang']['page']." ".$this->PageNo(),'',0,'L');
    }
}
?>
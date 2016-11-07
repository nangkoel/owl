<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$barang=$_GET['komodoti'];
$periodePsr=$_GET['periodePsr2'];

//\\exit("Eroor:$komodoti");





	
$namakar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$tahuntanam=makeOption($dbname,'setup_blok','kodeorg,tahuntanam');
$namaafd=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$namakebun=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

class PDF extends FPDF
{
	
	function Header()
	{
		global $conn;
		global $dbname;
		global $userid;
		global $tgl;
		global $blok;
		global $namakar;
		global $tahuntanam;
		global $namaafd;
		global $namakebun;
		global $tipe;
		
		
		
		//alamat PT minanga dan logo
			$query = selectQuery($dbname,'organisasi','alamat,telepon',
					"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 7;
			if($_SESSION['org']['kodeorganisasi']=='HIP')
			{  
				$path='images/hip_logo.jpg'; 
			} 
			else if($_SESSION['org']['kodeorganisasi']=='SIL')
			{  
				$path='images/sil_logo.jpg'; 
			} 
			else if($_SESSION['org']['kodeorganisasi']=='SIP')
			{ 
				 $path='images/sip_logo.jpg'; 
			}
			$this->Image($path,$this->lMargin,$this->tMargin,50);	
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255,255,255);	
			$this->SetX(50);   
			$this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
			$this->SetX(50); 		
			$this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
			$this->SetX(50); 			
			$this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
			$this->Line($this->lMargin,$this->tMargin+($height*4),
			$this->lMargin+$width,$this->tMargin+($height*4));
			
			
			$this->Ln(20);
		
		
		
   
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',10);
	   // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}




			
	$pdf=new PDF('P','mm','A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 7;
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(255,255,255);
	
	
	
	
	$where = '';
$whr="kelompokbarang='400'";
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang',$whr);

if($periodePsr=='') {
    exit("Error:Periode Tidak Boleh Kosong");
}
if($barang=='') {
    exit("Error:Komoditi harus dipilih");
}

// Check Days
$dayInMonth = count(rangeTanggal(tanggalsystem($periodePsr), date('Ymd')));
$tmpTgl = explode('-',$periodePsr);
$tglPeriode = $tmpTgl[2].'-'.$tmpTgl[1].'-'.$tmpTgl[0];
$datetime1 = date_create($tglPeriode);
$datetime2 = date_create(date('Y-m-d'));
$interval = date_diff($datetime1, $datetime2);
$dayInMonth = $interval->format('%a');
if($dayInMonth>91) {
    $date2 = date('Y-m-d',mktime(0,0,0,$tmpTgl[1],$tmpTgl[0]+90,$tmpTgl[2]));
    $dayInMonth = 91;
} else {
    $date2 = date('Y-m-d');
}

$where.=" and tanggal >= '".tanggalsystem($periodePsr)."'";
$where.=" and tanggal <= '".$date2."'";
$where.=" and kodeproduk = '".$barang."'";
$str = "select * from ".$dbname.".pmn_hargapasar where tanggal!='' ".$where." order by `tanggal` asc";
$resHarga = fetchData($str);

$optPasar = makeOption($dbname,'pmn_5pasar','id,namapasar');

// Rearrange Data
foreach($resHarga as $row) {
	$datetime2 = date_create($row['tanggal']);
    $interval = date_diff($datetime1, $datetime2);
    $day = $interval->format('%a');
	$dataHarga[$day+1][$row['pasar']] = $row['harga'];
}

foreach($optPasar as $id=>$nama) {
	$pasarHarga[$id] = 0;
}

for($i=1;$i<=$dayInMonth;$i++) {
	foreach($optPasar as $id=>$nama) {
		if(isset($dataHarga[$i][$id])) {
			$pasarHarga[$id] = $dataHarga[$i][$id];
		}
		$tmpData1[$id][$i] = $pasarHarga[$id];
	}
}

$data1 = array();
foreach($tmpData1 as $pasar=>$row1) {
	$i=1;
	$minggu=1;
	$harga = 0;
	foreach($row1 as $row2) {
		if($i>7) {
			$i=1;
			$data1[$minggu][$pasar] = $harga/7;
			$harga = 0;
			$minggu++;
		}
		$harga+=$row2;
		$i++;
	}
    if($i<=8) {
        $data1[$minggu][$pasar] = $harga/($i-1);
    }
}
	
	$perHead=explode('-',$periodePsr);
	$thnHead=$perHead[2];
	$blnHead=numToMonth($perHead[1],'I','long');
	
	$pdf->SetX(10);
	$pdf->Cell(10/100*$width,$height,'Trend Harga Bulanan',0,0,'L');

	$pdf->Ln(10);
	
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
	foreach($optPasar as $pasar)
	{
    	$pdf->Cell(15/100*$width,$height,$pasar,1,0,'C',1);	
	}
	$pdf->Ln();
	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	
	foreach($data1 as $day=>$row)
	{
		$pdf->Cell(10/100*$width,$height,$day,1,0,'C',1);
		foreach($row as $price)
		{
			$pdf->Cell(15/100*$width,$height,number_format($price),1,0,'R',1);	
		}
		$pdf->Ln();		
	}
	$pdf->Ln(10);
	
	$pdf->Output();
?>

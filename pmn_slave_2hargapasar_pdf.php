<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

$proses=$_GET['proses'];
$barang=$_GET['barang'];
$periodePsr=$_GET['periodePsr'];





	
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
	
	if($periodePsr!='') {
	$where.=" and tanggal like '".$periodePsr."%'";
	} else {
	exit("Error:Periode Tidak Boleh Kosong");
	}
	if($barang!='') {
	$where.=" and kodeproduk = '".$barang."'";
	} else {
	exit("Error:Komoditi harus dipilih");
	}
	$str = "select * from ".$dbname.".pmn_hargapasar where tanggal!='' ".$where." order by `tanggal` asc";
	$resHarga = fetchData($str);
	
	$optPasar = makeOption($dbname,'pmn_5pasar','id,namapasar');
	
	// Rearrange Data
	$dataHarga = $data1 = $data2 = $pasarHarga = array();
	$tmpPeriod = explode('-',$periodePsr);
	$dayInMonth = cal_days_in_month(CAL_GREGORIAN, $tmpPeriod[1], $tmpPeriod[0]);
	
	foreach($resHarga as $row) {
	$tmpTgl = date('d',strtotime($row['tanggal']));
	$dataHarga[$tmpTgl][$row['pasar']] = $row['harga'];
	if(!empty($row['catatan'])) {
		$data2[] = array(
			'tanggal' => tanggalnormal($row['tanggal']),
			'pasar' => $row['pasar'],
			'catatan' => $row['catatan']
		);
	}
	}
	
	foreach($optPasar as $id=>$nama) {
	$pasarHarga[$id] = 0;
	}
	
	for($i=1;$i<=$dayInMonth;$i++) {
	foreach($optPasar as $id=>$nama) {
		
		if(strlen($i)<2)
		{
			$i="0".$i;
		}
		
		if(isset($dataHarga[$i][$id])) {
			$pasarHarga[$id] = $dataHarga[$i][$id];
		}
		else
		{
			$pasarHarga[$id]=0;
		}
		$data1[$i][$id] = $pasarHarga[$id];
	}
	}
	
	$perHead=explode('-',$periodePsr);
	$thnHead=$perHead[0];
	$blnHead=numToMonth($perHead[1],'I','long');
	
	$pdf->Cell(10/100*$width,$height,'Trend Harga Harian Periode : '.$blnHead.' '.$thnHead,0,0,'L');

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

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

	$tmp=explode(',',$_GET['column']);
	$notran=$tmp[0];
	
	//echo $notran;
	
$nmFranco=makeOption($dbname,'setup_franco','id_franco,franco_name');	
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');	
$namakar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');


class PDF extends FPDF
{
	
/*	function Header()
	{
		global $conn;
		global $dbname;
		global $userid;
		
		
		
		
		 $width = $this->w - $this->lMargin - $this->rMargin;
		 $height = 15;	
		 
				
		
		$this->Ln(10);
	}*/
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',10);
	   // $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}


			
	$pdf=new PDF('P','mm','A4');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 5;
	$pdf->AddPage();
	
	
	
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(255,255,255);	
	$pdf->Cell(200,5,$_SESSION['org']['namaorganisasi'],0,1,'C');
	$pdf->Cell(200,5,'PACKING LIST',0,1,'C');
	
	
	$pdf->ln();
	
	
	
	$i="select * from ".$dbname.".log_konosemenht where nokonosemen='".$notran."' ";
	$n=mysql_query($i) or die (mysql_error($conn));
	$d=mysql_fetch_assoc($n);

	$thn=substr($d['tanggal'],0,4);
	$bln=numToMonth(substr($d['tanggal'],5,2),'I','long');
	$hari=substr($d['tanggal'],8,2);//echo $hari;
	
	$isiTgl=$hari.' '.$bln.' '.$thn;
			
	//$pdf->Ln();
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(255,255,255);
	
	$pdf->SetX(15);
	$pdf->Cell(120,$height,$_SESSION['lang']['shipper'].' & '.$_SESSION['lang']['vessel'],0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$d['shipper'].', '.$d['vessel'],0,1,'L');
	
	$pdf->SetX(15);
	$pdf->Cell(120,$height,$_SESSION['lang']['tanggalberangkat'],0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,tanggalnormal($d['tanggalberangkat']),0,1,'L');
	
	$pdf->SetX(15);
	$pdf->Cell(120,$height,$_SESSION['lang']['tujuan'],0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$nmFranco[$d['franco']],0,1,'L');
	
	$pdf->SetX(15);
	$pdf->Cell(120,$height,$_SESSION['lang']['asalbarang'],0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$d['asalbarang'],0,1,'L');
	
	//print_r($_SESSION['standard']);
	//echo $nmBln;
	
	

	
	//$pdf->Ln(10);
	$yAkhir=$pdf->GetY();
	//$pdf->Line(10,$yAkhir,205,$yAkhir);
	$pdf->Line(10,$yAkhir-0.5,205,$yAkhir-0.5);
	$pdf->SetFont('Arial','B',7);
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(5,$height,'NO',1,0,'C',1);
	$pdf->Cell(25,$height,strtoupper($_SESSION['lang']['kodebarang']),1,0,'C',1);
	$pdf->Cell(65,$height,'MATERIAL NAME & STESIFIKASI',1,0,'C',1);
	$pdf->Cell(15,$height,strtoupper($_SESSION['lang']['unit']),1,0,'C',1);
	$pdf->Cell(15,$height,strtoupper($_SESSION['lang']['total']),1,0,'C',1);
	$pdf->Cell(40,$height,strtoupper($_SESSION['lang']['nopo']),1,0,'C',1);
	$pdf->Cell(30,$height,strtoupper($_SESSION['lang']['harga']),1,1,'C',1);
	$pdf->SetFont('Arial','',7);
	$yAkhir=$pdf->GetY();
	$pdf->Line(10,$yAkhir+0.5,205,$yAkhir+0.5);
	
	//$pdf->SetFillColor(255,255,255);
	$x="select * from ".$dbname.".log_konosemendt where nokonosemen='".$notran."' and jenis='PO' ";
	//echo $x;
	$y=mysql_query($x) or die (mysql_error($conn));
	while($z=mysql_fetch_assoc($y))
	{
			$whi="kodebarang='".$z['kodebarang']."' ";
			$harga=makeOption($dbname,'log_po_vw','nopo,hargasatuan',$whi);
			
		$no+=1;
		$pdf->Cell(5,$height,$no,LR,0,'C');
		$pdf->Cell(25,$height,$z['kodebarang'],LR,0,'L');
		$pdf->Cell(65,$height,$nmBarang[$z['kodebarang']],LR,0,'L');
		$pdf->Cell(15,$height,$z['satuanpo'],LR,0,'C');
		$pdf->Cell(15,$height,number_format($z['jumlah'],2),LR,0,'C');
		$pdf->Cell(40,$height,$z['nopo'],LR,0,'C');
		$pdf->Cell(30,$height,number_format($harga[$z['nopo']]*$z['jumlah'],2),LR,1,'R');
		$totalH1+=$harga[$z['nopo']]*$z['jumlah'];
	}
	
	$x1="select * from ".$dbname.".log_konosemendt where nokonosemen='".$notran."' and jenis='PL' ";
	$y1=mysql_query($x1) or die (mysql_error($conn));
	while($z1=mysql_fetch_assoc($y1))
	{
		$a="select * from ".$dbname.".log_packingdt where notransaksi='".$z1['kodebarang']."'";
		//echo $a;
		$b=mysql_query($a) or die (mysql_error($conn));
		while($c=mysql_fetch_assoc($b))
		{
			$no+=1;
			$pdf->Cell(5,$height,$no,LR,0,'C');
			$pdf->Cell(25,$height,$c['kodebarang'],LR,0,'L');
			$pdf->Cell(65,$height,$nmBarang[$c['kodebarang']],LR,0,'L');
			$pdf->Cell(15,$height,$c['satuanpo'],LR,0,'C');
			$pdf->Cell(15,$height,number_format($c['jumlah'],2),LR,0,'C');
			$pdf->Cell(40,$height,$c['nopo'],LR,0,'C');
			$pdf->Cell(30,$height,number_format($c['harga']*$c['jumlah'],2),LR,1,'R');
			//$totalH2+=$harga[$z['nopo']];
			$totalH2+=$c['harga']*$c['jumlah'];
		}
		
	}
	
	$totalSemua=$totalH1+$totalH2;
	
	//echo $totalSemua;
	//}
	//$yAkhir=$pdf->GetY();
//	$pdf->Line(10,$yAkhir,205,$yAkhir);
//	$pdf->Line(10,$yAkhir+0.5,205,$yAkhir+0.5);
	//$pdf->ln();
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(165,$height,strtoupper($_SESSION['lang']['total']),1,0,'C',1);
	$pdf->Cell(30,$height,number_format($totalSemua,2),1,0,'R',1);
	
	
	$yAkhir1=$pdf->GetY();

	$pdf->Line(10,$yAkhir1+$height+0.5,205,$yAkhir1+$height+0.5);
	
	$pdf->Output();
?>

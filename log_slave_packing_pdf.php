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
	
	
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');	
$namakar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$jumlahPkk=makeOption($dbname,'setup_blok','kodeorg,jumlahpokok');
$luasAreal=makeOption($dbname,'setup_blok','kodeorg,luasareaproduktif');

class PDF extends FPDF
{
	
/*	function Header()
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
	
	$i="select * from ".$dbname.".log_packinght where notransaksi='".$notran."' ";
	$n=mysql_query($i) or die (mysql_error($conn));
	$d=mysql_fetch_assoc($n);
	
	$pdf->SetFont('Arial','',10);
	$pdf->SetFillColor(255,255,255);	
	$pdf->Cell(200,5,$nmOrg[$d['kodept']],0,1,'C');
	$pdf->Cell(200,5,'PACKING LIST',0,1,'C');
	
	
	$pdf->ln();
	
	
	
	

	$thn=substr($d['tanggal'],0,4);
	$bln=numToMonth(substr($d['tanggal'],5,2),'I','long');
	$hari=substr($d['tanggal'],8,2);//echo $hari;
	
	$isiTgl=$hari.' '.$bln.' '.$thn;
			
	//$pdf->Ln();
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(255,255,255);
	
	$pdf->SetX(125);
	$pdf->Cell(10,$height,'PL NO',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$d['notransaksi'],0,1,'L');
	
	$pdf->Cell(20,$height,'NO. PETI / KOLI',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$d['keterangan'],0,0,'L');
	
	$pdf->SetX(125);
	$pdf->Cell(10,$height,'DATE',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(20,$height,$isiTgl,0,1,'L');
	

	
	//print_r($_SESSION['standard']);
	//echo $nmBln;
	
	

	
	//$pdf->Ln(10);
	$yAkhir=$pdf->GetY();
	//$pdf->Line(10,$yAkhir,205,$yAkhir);
	$pdf->Line(10,$yAkhir-0.5,205,$yAkhir-0.5);
	
	$pdf->SetFillColor(220,220,220);
	$pdf->Cell(5,$height*2,'NO',TB,0,'C',1);
	$pdf->Cell(20,$height*2,strtoupper($_SESSION['lang']['kodebarang']),TB,0,'C',1);
	$pdf->Cell(50,$height*2,'MATERIAL NAME & SPESIFIKASI',TB,0,'C',1);
	$pdf->Cell(15,$height*2,strtoupper($_SESSION['lang']['unit']),TB,0,'C',1);
	$pdf->Cell(30,$height,strtoupper($_SESSION['lang']['jumlah']),T,0,'C',1);
	//$pdf->Cell(15,$height,'TERIMA',1,0,'C',1);
	$pdf->Cell(35,$height*2,'NOPO',TB,0,'C',1);
	$pdf->Cell(40,$height*2,strtoupper($_SESSION['lang']['nopp']),TB,1,'C',1);
	$pdf->Ln(-$height);
	$pdf->SetX(100);
	//$pdf->Cell(90,$height,'',1,0,'C',1);
	$pdf->Cell(15,$height,strtoupper($_SESSION['lang']['kirim']),B,0,'C',1);
	$pdf->Cell(15,$height,strtoupper($_SESSION['lang']['diterima']),B,1,'C',1);
	
	$yAkhir=$pdf->GetY();
	$pdf->Line(10,$yAkhir+0.5,205,$yAkhir+0.5);
	$pdf->Ln(3);
	
	$pdf->SetFillColor(255,255,255);
	$x="select * from ".$dbname.".log_packingdt where notransaksi='".$notran."' ";
	$y=mysql_query($x) or die (mysql_error($conn));
//        $turunin=11*65;//batesin baris yg tampil
	while($z=mysql_fetch_assoc($y))
	{
		$no+=1;
                 if($no!=1){
                    $pdf->SetY($yAkhir);
                 }
                 $yAkhir=$pdf->GetY();
//                 if($yAkhir>=$turunin){
//                    $akhirY=$pdf->GetY();
//                 }    
                $height2=$height-1;
		$pdf->Cell(5,$height2,$no,0,0,'C');
		$pdf->Cell(20,$height2,$z['kodebarang'],0,0,'L');
                $posisiY=round($pdf->GetY());
//		$pdf->Cell(50,$height2,$nmBarang[$z['kodebarang']],0,0,'L');
		$pdf->MultiCell(55,$height2,$nmBarang[$z['kodebarang']],0,'L',0);
                $yAkhir=$pdf->GetY();

                //naik lagi kursornya
                $pdf->SetY($posisiY);
                $pdf->SetX($pdf->GetX()+70);
                
		$pdf->Cell(17,$height2,$z['satuanpo'],0,0,'R');
		$pdf->Cell(15,$height2,$z['jumlah'],1,0,'R');
		$pdf->Cell(15,$height2,'',1,0,'C');
		$pdf->Cell(35,$height2,$z['nopo'],0,0,'C');
		$pdf->Cell(40,$height2,$z['nopp'],0,1,'C');
	}
	$pdf->Ln(3);
	$yAkhir=$pdf->GetY()+2;
	$pdf->Line(10,$yAkhir,205,$yAkhir);
	$pdf->Line(10,$yAkhir+0.5,205,$yAkhir+0.5);
	$pdf->ln();
	//$pdf->Cell(15,$height,'Transportasi',0,0,'L');
	$pdf->SetX(50);
	$pdf->Cell(15,$height,'Yg Menyerahkan',0,0,'L');
	$pdf->SetX(125);
	$pdf->Cell(15,$height,'Yg Menerima',0,0,'L');
	$pdf->SetX(175);
	//$pdf->Cell(15,$height,'Menyetujui',0,1,'L');
	
	$pdf->Ln(10);
	
	$pdf->Cell(15,$height,'',0,0,'L');
	$pdf->SetX(50);
	$pdf->Cell(15,$height,$namakar[$d['menyerahkan']],0,0,'L');
	$pdf->SetX(125);
	$pdf->Cell(15,$height,$d['menerima'],0,0,'L');
	$pdf->SetX(175);
	$pdf->Cell(15,$height,'	',0,1,'L');
	
	//$pdf->Cell(15,$height,'Date :',0,0,'L');
	$pdf->SetX(50);
	$pdf->Cell(15,$height,'Date :',0,0,'L');
	$pdf->SetX(125);
	$pdf->Cell(15,$height,'Date :',0,1,'L');
	//$pdf->SetX(175);
	//$pdf->Cell(15,$height,'Date	:',0,1,'L');
	
	$yAkhir=$pdf->GetY();
	$pdf->Line(10,$yAkhir,205,$yAkhir);
	$pdf->Line(10,$yAkhir+0.5,205,$yAkhir+0.5);
	
	$pdf->Ln();
	
	/*$pdf->Cell(15,$height,'Distribution :',0,1,'L');
	$pdf->Cell(15,$height,'Original',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(5,$height,'_Accounting',0,1,'L');

	$pdf->Cell(15,$height,'Copy 1',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(5,$height,'_Purchasing',0,1,'L');
	
	$pdf->Cell(15,$height,'Copy 2',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(5,$height,'_Material Control HIP',0,1,'L');
	
	$pdf->Cell(15,$height,'Copy 3',0,0,'L');
	$pdf->Cell(5,$height,':',0,0,'L');
	$pdf->Cell(5,$height,'_Files',0,1,'L');	*/
	
	
	$pdf->Output();
?>

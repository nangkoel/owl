<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

//	$pt=$_POST['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];

//echo"gudang :".$gudang.", periode: ".$periode.", mulai: ".$tanggalmulai.", sampai: ".$tanggalsampai;

$str="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where kodeorganisasi='".$gudang."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=$bar->namaorganisasi;
}


$str="select distinct tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg = '".$gudang."' and periode = '".$periode."'";
$res=mysql_query($str);
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}
while($bar=mysql_fetch_object($res))
{
	$tanggalmulai=$bar->tanggalmulai;
	$tanggalsampai=$bar->tanggalsampai;
}	

$str="select distinct kodebarang, namabarang from ".$dbname.".log_5masterbarang";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang]=$bar->namabarang;
}	
	
if($periode=='')
	$str="select a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan, nopo 
		  from ".$dbname.".log_transaksi_vw a
		  left join ".$dbname.".log_5supplier b on a.idsupplier=b.supplierid
		  where a.kodegudang='".$gudang."' and a.tipetransaksi=1 
		  order by a.tanggal";
else
	$str="select a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan, nopo 
		  from ".$dbname.".log_transaksi_vw a
		  left join ".$dbname.".log_5supplier b on a.idsupplier=b.supplierid
		  where a.kodegudang='".$gudang."' and a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi=1 
		  order by a.tanggal";

//echo"str :".$str;
//=================================================
class PDF extends FPDF {
    function Header() {
       global $namagudang;
	   global $periode;
	   global $gudang;
//        $this->SetFont('Arial','B',8); 
//		$this->Cell(20,5,$namagudang,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,5,strtoupper($_SESSION['lang']['hutangsupplierbpb']),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(20,5,$namagudang,'',0,'L');
		$this->Cell(120,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(10,5,'Periode ','',0,'L');
		$this->Cell(20,5,' :  '.$periode,'',0,'L');
		$this->Cell(110,5,' ','',0,'R');
		$this->Cell(15,5,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$this->PageNo(),'',1,'L');
		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(15,5,'User','',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',7);
        $this->SetX($this->GetX()-7);
		$this->Cell(5,5,'No.',1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['tanggal'],1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['kodebarang'],1,0,'C');
		$this->Cell(45,5,$_SESSION['lang']['namabarang'],1,0,'C');				
		$this->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C');
		$this->Cell(10,5,$_SESSION['lang']['satuan'],1,0,'C');	
		$this->Cell(17,5,$_SESSION['lang']['kodesupplier'],1,0,'C');
		$this->Cell(27,5,$_SESSION['lang']['namasupplier'],1,0,'C');		
		$this->Cell(18,5,$_SESSION['lang']['nopo'],1,0,'C');		
		$this->Cell(18,5,$_SESSION['lang']['hargasatuan'],1,0,'C');
		$this->Cell(20,5,$_SESSION['lang']['total'],1,0,'C');
        $this->Ln();						


    }
}
//================================

	
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo$_SESSION['lang']['tidakditemukan'];
	}
	else
	{
		$pdf=new PDF('P','mm','A4');
		$pdf->AddPage();

	while($bar=mysql_fetch_object($res))
	{
		$no+=1; $total=0;
		$total= $bar->jumlah * $bar->hargasatuan; $totalall+=$total;
                $pdf->SetX($pdf->GetX()-7);
		$pdf->Cell(5,4,$no,0,0,'R');
		$pdf->Cell(15,4,tanggalnormal($bar->tanggal),0,0,'L');
		$pdf->Cell(15,4,$bar->kodebarang,0,0,'R');
		$pdf->Cell(45,4,$barang[$bar->kodebarang],0,0,'L');				
		$pdf->Cell(15,4,number_format($bar->jumlah),0,0,'R');
		$pdf->Cell(10,4,$bar->satuan,0,0,'L');	
		$pdf->Cell(17,4,$bar->idsupplier,0,0,'R');
		$pdf->Cell(27,4,$bar->namasupplier,0,0,'L');
		$pdf->Cell(18,4,substr($bar->nopo,0,11),0,0,'L');
		$pdf->Cell(18,4,number_format($bar->hargasatuan),0,0,'R');
		$pdf->Cell(20,4,number_format($total),0,1,'R');	
		
	}
		$pdf->Cell(170,4,'TOTAL',0,0,'R');
		$pdf->Cell(25,4,number_format($totalall),0,1,'R');	
	$pdf->Output();	
 }
	
?>
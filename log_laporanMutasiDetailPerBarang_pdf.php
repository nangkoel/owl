<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
require_once('lib/nangkoelib.php');
	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=substr($_GET['periode'],0,7);
	$kodebarang=$_GET['kodebarang'];
	$namabarang=$_GET['namabarang'];
	$satuan=$_GET['satuan'];
//======================================
  $x=str_replace("-","",$periode);
  $x=str_replace("/","",$x);
  $x=mktime(0,0,0,(intval(substr($x,4,2))-1),15,substr($x,0,4));
  $prefper=date('Y-m',$x); 
  	
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
//==========================get periode
if($gudang=='')
{
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' and tipe='HOLDING'";
    $res=mysql_query($str);
    $dt=mysql_fetch_assoc($res);
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg='". $dt['kodeorganisasi']."' and periode='".$periode."'";
}
else
{
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg='".$gudang."' and periode='".$periode."'";
}
//exit("error:".$str);
	$awal='';
	$akhir='';	  
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$awal=$bar->tanggalmulai;
	$akhir=$bar->tanggalsampai;
}	  
//ambil saldo awal===============================
    if($gudang=='')
    {
            $str="select  sum(saldoakhirqty) as sawal,
                            sum(nilaisaldoakhir) as sawalrp from 
                            ".$dbname.".log_5saldobulanan
                            where kodebarang='".$kodebarang."'
                            and periode='".$prefper."' and kodeorg='".$pt."'";
            //=========================================
            //ambil transaksi detail
            $strx="select a.*,left(kodegudang,4) as kodebo, b.idsupplier,b.tanggal,b.kodegudang,
                  b.tipetransaksi 
                  from ".$dbname.".log_transaksidt a
                  left join ".$dbname.".log_transaksiht b
                      on a.notransaksi=b.notransaksi
                      where kodebarang='".$kodebarang."'
                      and b.tanggal>='".$awal."'
                      and b.tanggal<='".$akhir."'
                      and b.post=1
                      having kodebo in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."') 
                      order by tanggal,waktutransaksi";
    }
    else
    {
            $str="select  sum(saldoakhirqty) as sawal,
                            sum(nilaisaldoakhir) as sawalrp from 
                            ".$dbname.".log_5saldobulanan
                            where kodebarang='".$kodebarang."'
                            and periode='".$prefper."'
                            and kodegudang='".$gudang."'";		
            //=========================================
            //ambil transaksi detail
            $strx="select a.*,b.idsupplier,b.tanggal,b.kodegudang,
                  b.tipetransaksi
                      from ".$dbname.".log_transaksidt a
                  left join ".$dbname.".log_transaksiht b
                      on a.notransaksi=b.notransaksi
                      where kodebarang='".$kodebarang."'
                      and kodegudang='".$gudang."'
                      and b.tanggal>='".$awal."'
                      and b.tanggal<='".$akhir."'
                      and b.post=1
                      order by tanggal,waktutransaksi";
    }
$sawal=0;
$sawalrp=0;	
$hargasawal=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$sawal=$bar->sawal;
	$sawalrp=$bar->sawalrp;
}
if($sawal>0)
	$hargasawal=$sawalrp/$sawal;


	
	
//=================================================
class PDF extends FPDF {
    function Header() {
       global $namapt;
	   global $pt;
	   global $gudang;
	   global $periode;
	   global $kodebarang;
	   global $namabarang;
	   global $satuan;
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(190,5,strtoupper($_SESSION['lang']['detailtransaksibarang']),0,1,'C');
        $this->SetFont('Arial','',8);

			$this->Cell(35,5,$_SESSION['lang']['pt'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(100,5,$pt,'',0,'L');		
			$this->Cell(15,5,$_SESSION['lang']['tanggal'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
//		$this->Cell(140,5,' ','',0,'R');
		$this->Cell(35,5,$_SESSION['lang']['namabarang'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(100,5,'['.$kodebarang.']'.$namabarang.'('.$satuan.')','',0,'L');		
		$this->Cell(15,5,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$this->PageNo(),'',1,'L');

//			$this->Cell(140,5,' ','',0,'R');
			$this->Cell(35,5,$_SESSION['lang']['periode'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(100,5,$periode,'',0,'L');		
			$this->Cell(15,5,'User','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,$_SESSION['standard']['username'],'',1,'L');
//	        $this->Ln();
     
	    $this->SetFont('Arial','',6);
		$this->Cell(5,5,'No.',1,0,'C');		
		$this->Cell(35,5,$_SESSION['lang']['sloc'],1,0,'C');	
		$this->Cell(20,5,$_SESSION['lang']['tanggal'],1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['tipe'],1,0,'C');						
		$this->Cell(25,5,$_SESSION['lang']['saldoawal'],1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['masuk'],1,0,'C');		
		$this->Cell(25,5,$_SESSION['lang']['keluar'],1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['saldo'],1,1,'C');
    }
}
//================================

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	$resx=mysql_query($strx);
	$no=0;
	$saldo=$sawal;
	$masuk=0;
	$keluar=0;
	while($barx=mysql_fetch_object($resx))
	{
		$no+=1;
		if($barx->tipetransaksi<5)
		 {
		 	$saldo=$saldo+$barx->jumlah;
			$masuk=$barx->jumlah;
			$keluar=0;
		 }
		 else
		 {
		 	$saldo=$saldo-$barx->jumlah;
			$keluar=$barx->jumlah;
			$masuk=0;
		 }
		 
		$pdf->Cell(5,5,$no,0,0,'C');		
		$pdf->Cell(35,5,$barx->kodegudang,0,0,'C');	
		$pdf->Cell(20,5,tanggalnormal($barx->tanggal),0,0,'C');
		$pdf->Cell(25,5,$barx->tipetransaksi,0,0,'C');						
		$pdf->Cell(25,5,number_format($sawal,2,'.',','),0,0,'R');
		$pdf->Cell(25,5,number_format($masuk,2,'.',','),0,0,'R');		
		$pdf->Cell(25,5,number_format($keluar,2,'.',','),0,0,'R');
		$pdf->Cell(25,5,number_format($saldo,2,'.',','),0,1,'R');
		$sawal=$saldo;		
	}
	$pdf->Output();	
	
?>
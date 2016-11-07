<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

$tanggalmulai=$_GET['tanggalmulai'];
$tanggalsampai=$_GET['tanggalsampai'];
$noakun=$_GET['noakun'];
$namakaryawan=$_GET['namakaryawan'];

$str="select a.karyawanid, a.namakaryawan from ".$dbname.".datakaryawan a
                where a.karyawanid = '".$namakaryawan."'
                ";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $namaaslikaryawan.=$bar->namakaryawan;
}

 //         echo $tanggalmulai." ".$tanggalsampai." ".$noakun." ".$namakaryawan."<br>"; exit;
        
$qwe=explode("-",$tanggalmulai); $tanggalmulai=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggalsampai); $tanggalsampai=$qwe[2]."-".$qwe[1]."-".$qwe[0];

//echo $tanggalmulai." ".$tanggalsampai." ".$noakun." ".$namakaryawan." ";

$str="select a.*, b.namaakun from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."'
      and a.noakun = '".$noakun."' and a.nik = '".$namakaryawan."'
";
            
//=================================================
//if($periode=='')
//     $periode=substr($_SESSION['org']['period']['start'],0,7);
class PDF extends FPDF {
    function Header() {
        global $tanggalmulai;
        global $tanggalsampai;
        global $noakun;
        global $namaaslikaryawan;
        $this->SetFont('Arial','B',12);
		$this->Cell(190,3,strtoupper('HUTANG/PIUTANG KARYAWAN'),0,1,'C');
        $this->SetFont('Arial','',8);
		$this->Cell(155,3,$noakun." - ".$namaaslikaryawan,'',0,'L');
		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(155,3,' ','',0,'R');
		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$this->PageNo(),'',1,'L');
		$this->Cell(155,3,' ','',0,'R');
		$this->Cell(15,3,'User','',0,'L');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',6);
		$this->Cell(5,5,'No.',1,0,'C');
		$this->Cell(24,5,$_SESSION['lang']['nojurnal'],1,0,'C');			
		$this->Cell(16,5,$_SESSION['lang']['tanggal'],1,0,'C');	
		$this->Cell(14,5,$_SESSION['lang']['noakun'],1,0,'C');	
		$this->Cell(40,5,$_SESSION['lang']['namaakun'],1,0,'C');	
		$this->Cell(44,5,$_SESSION['lang']['uraian'],1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['debet'],1,0,'C');
		$this->Cell(25,5,$_SESSION['lang']['kredit'],1,0,'C');
        $this->Ln();						
        $this->Ln();						

    }
}
//================================

    $pdf=new PDF('P','mm','A4');
    $pdf->AddPage();

    $salakqty	=0;
    $masukqty	=0;
    $keluarqty	=0;
    $sawalQTY	=0;
    $sdebet	= $skredit = 0; 

	//
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
		$no+=1;
		$tanggal    =$bar->tanggal;
		$noakun	=$bar->noakun;
		$nojurnal=$bar->nojurnal;
		$keterangan =$bar->keterangan;
		$namaakun   =$bar->namaakun;
		$jumlah      =$bar->jumlah;
		if ($jumlah >=0 ){
			$debet	= $jumlah;
			$kredit	= 0;
		}
		else{
			$debet	= 0;
			$kredit	= $jumlah*-1;
		}
			
		
		$pdf->Cell(5,3,$no,0,0,'C');
		$pdf->Cell(24,3,$nojurnal,0,0,'L');
		$pdf->Cell(18,3,tanggalnormal($tanggal),0,0,'C');				
		$pdf->Cell(12,3,$noakun,0,0,'L');	
		$pdf->Cell(40,3,$namaakun,0,0,'L');	
		$pdf->Cell(44,3,$keterangan,0,0,'L');

		$pdf->Cell(25,3,number_format($debet,2,'.',','),0,0,'R');	
		$pdf->Cell(25,3,number_format($kredit,2,'.',','),0,1,'R');	
		$sdebet += $debet;
		$skredit += $kredit;
	}
		$pdf->Cell(143,2,' ',0,0,'L');
		$pdf->Cell(25,2,'-------------------------',0,0,'R');	
		$pdf->Cell(25,2,'-------------------------',0,1,'R');	
		$pdf->Cell(143,3,'T O T A L   : ',0,0,'R');
		$pdf->Cell(25,3,number_format($sdebet,2,'.',','),0,0,'R');	
		$pdf->Cell(25,3,number_format($skredit,2,'.',','),0,1,'R');	
		$pdf->Cell(143,2,' ',0,0,'L');
		$pdf->Cell(25,2,'-------------------------',0,0,'R');	
		$pdf->Cell(25,2,'-------------------------',0,1,'R');	
	$pdf->Output();	
 }
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/nangkoelib.php');

	$tampil=$_GET['tampil'];
	$pabrik=$_GET['pabrik'];
	$periode=$_GET['periode'];
	

class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
	   
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
		$this->Cell(275,5,strtoupper($_SESSION['lang']['rprodksiPabrik']),0,1,'C');
		$this->Cell(275,5,$_SESSION['lang']['periode'].' : '.substr($periode,5,2).'-'.substr($periode,0,4),0,1,'C');				
		$this->SetFont('Arial','',8);
		$this->Cell(230,5,$_SESSION['lang']['tanggal'],0,0,'R');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
		$this->Cell(230,5,$_SESSION['lang']['page'],'',0,'R');
		$this->Cell(2,5,':','',0,'L');
		$this->Cell(35,5,$this->PageNo(),'',1,'L');
		$this->Cell(230,3,'User','',0,'R');
		$this->Cell(2,3,':','',0,'L');
		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',8);
	  		
		$this->SetFont('Arial','B',8);
		$this->Cell(5,10,'No.',1,0,'C');
		$this->Cell(25,10,$_SESSION['lang']['kodeorganisasi'],1,0,'C');
		$this->Cell(20,10,$_SESSION['lang']['periode'],1,0,'C');
		$this->Cell(25,10,$_SESSION['lang']['tersedia'],1,0,'C');	
		$this->Cell(25,10,$_SESSION['lang']['tbsdiolah'],1,0,'C');	
		$this->Cell(25,10,$_SESSION['lang']['sisa'],1,0,'C');	
		$this->Cell(75,5,$_SESSION['lang']['cpo'],1,0,'C');
		$this->Cell(75,5,$_SESSION['lang']['kernel'],1,1,'C');
		$this->setX(135);
		$this->SetFont('Arial','',7);
		$this->Cell(15,5,$_SESSION['lang']['cpo'].'(Kg)',1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['oer'].'(%)',1,0,'C');
		$this->Cell(15,5,'(FFa)(%)',1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['kotoran'].'(%)',1,0,'C');	
		$this->Cell(15,5,$_SESSION['lang']['kadarair'].'(%)',1,0,'C');		

		$this->Cell(15,5,$_SESSION['lang']['kernel'].'(Kg)',1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['oer'].'(%)',1,0,'C');
		$this->Cell(15,5,'(FFa)(%)',1,0,'C');
		$this->Cell(15,5,$_SESSION['lang']['kotoran'].'(%)',1,0,'C');	
		$this->Cell(15,5,$_SESSION['lang']['kadarair'].'(%)',1,1,'C');					

    }
}
//================================

	$pdf=new PDF('L','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);

if(strlen($periode)==4)
{
	//tahunan
	$str="select sum(tbsmasuk) as tbsmasuk,
		  sum(tbsdiolah) as tbsdiolah,
		  sum(oer)  as oer,
		  avg(ffa) as ffa,
		  avg(kadarair) as kadarair,
		  avg(kadarkotoran) as kadarkotoran,
		  sum(oerpk) as oerpk,
		  avg(ffapk) as ffapk,
		  avg(kadarairpk) as kadarairpk,
		  avg(kadarkotoranpk) as kadarkotoranpk,
		  sum(jumlahpk) as jumlahpk,
		  sum(jumlahck) as jumlahck,
		  sum(jumlahjakos) as jumlahjakos,
		  left(tanggal,7) as perio from ".$dbname.".pabrik_produksi
		  where kodeorg='".$pabrik."' and tanggal like '".$periode."%'
		  group by perio order by perio";  
	//ambil sisa tbs hari ini
	$stsisa="select sisahariini from ".$dbname.".pabrik_produksi 
	          where tanggal like '".$periode."%' order by tanggal desc limit 1";
	$ressisa=mysql_query($stsisa);
	$sisa=0;
	while($barsisa=mysql_fetch_object($ressisa))
	{
		$sisa=$barsisa->sisahariini;
	}				  
			  
	//ambil tbs sisa sebelumnya
	$stsedia="select sisahariini from ".$dbname.".pabrik_produksi 
	          where tanggal like '".($periode-1)."%' order by tanggal desc limit 1";
	$ressedia=mysql_query($stsedia);
	$tbskemarin=0;
	while($barsedia=mysql_fetch_object($ressedia))
	{
		$tbskemarin=$barsedia->sisahariini;
	}		  			  
    
	$res=mysql_query($str);
	$no=0;
       while($bar=mysql_fetch_object($res))
        {
		 $no+=1;
		$pdf->Cell(5,5,$no,1,0,'C');
		$pdf->Cell(25,5,$pabrik,1,0,'C');
		$pdf->Cell(20,5,$bar->perio,1,0,'C');
		$pdf->Cell(25,5,number_format($bar->tbsmasuk+$tbskemarin,0,'.',','),1,0,'R');	
		$pdf->Cell(25,5,number_format($bar->tbsdiolah,0,'.',',.'),1,0,'R');	
		$pdf->Cell(25,5,number_format($bar->tbsmasuk+$tbskemarin-$bar->tbsdiolah,0,'.',','),1,0,'R');	
		$pdf->Cell(15,5,number_format($bar->oer,0,'.',','),1,0,'R');
		$pdf->Cell(15,5,(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
		$pdf->Cell(15,5,number_format($bar->ffa,2,'.',','),1,0,'R');
		$pdf->Cell(15,5,number_format($bar->kadarkotoran,2,'.',','),1,0,'R');	
		$pdf->Cell(15,5,number_format($bar->kadarair,2,'.',','),1,0,'R');		
		$pdf->Cell(15,5,number_format($bar->oerpk,0,'.',','),1,0,'R');
		$pdf->Cell(15,5,(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
		$pdf->Cell(15,5,number_format($bar->ffapk,2,'.',','),1,0,'R');
		$pdf->Cell(15,5,number_format($bar->kadarkotoranpk,2,'.',','),1,0,'R');	
		$pdf->Cell(15,5,number_format($bar->kadarairpk,2,'.',','),1,1,'R');
		$tbskemarin=$bar->tbsmasuk+$tbskemarin-$bar->tbsdiolah;
         }	  
}
else
{
	//bulanan
	$str="select * from ".$dbname.".pabrik_produksi where tanggal like '".$periode."%'
	      and kodeorg='".$pabrik."'
		  order by tanggal desc";
    $res=mysql_query($str);
	$no=0;
       while($bar=mysql_fetch_object($res))
        {
         $no+=1;	
		$pdf->Cell(5,5,$no,1,0,'C');
		$pdf->Cell(25,5,$bar->kodeorg,1,0,'C');
		$pdf->Cell(20,5,tanggalnormal($bar->tanggal),1,0,'C');
		$pdf->Cell(25,5,number_format($bar->tbsmasuk+$bar->sisatbskemarin,0,'.',','),1,0,'R');	
		$pdf->Cell(25,5,number_format($bar->tbsdiolah,0,'.',',.'),1,0,'R');	
		$pdf->Cell(25,5,number_format($bar->sisahariini,0,'.',','),1,0,'R');	
		$pdf->Cell(15,5,number_format($bar->oer,0,'.',','),1,0,'R');
		$pdf->Cell(15,5,(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
		$pdf->Cell(15,5,$bar->ffa,1,0,'R');
		$pdf->Cell(15,5,$bar->kadarkotoran,1,0,'R');	
		$pdf->Cell(15,5,$bar->kadarair,1,0,'R');		
		$pdf->Cell(15,5,number_format($bar->oerpk,0,'.',','),1,0,'R');
		$pdf->Cell(15,5,(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
		$pdf->Cell(15,5,$bar->ffapk,1,0,'R');
		$pdf->Cell(15,5,$bar->kadarkotoranpk,1,0,'R');	
		$pdf->Cell(15,5,$bar->kadarairpk,1,1,'R');	
         }	  

}
	$pdf->Output();	
	
?>
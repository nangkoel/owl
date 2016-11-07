<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

 
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

$optnmcust=makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmakun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
        global $userid;
	global $column;
	global $optnmakun;
	global $optnmcust;
        global $bar;
	
			$test=explode(',',$_GET['column']);
			$notransaksi=$test[0];
			$kodevhc=$test[1];
			$str="select * from ".$dbname.".".$_GET['table']."  where noinvoice='".$column."'";
			//echo $str;exit();
			$res=mysql_query($str);
			$bar=mysql_fetch_object($res);
			$posting=$bar->posting;		
			//ambil nama pt
			   $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   }    
	   $sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->updateby."'";
	   $query2=mysql_query($sql2) or die(mysql_error());
	   $res2=mysql_fetch_object($query2);
	   
	   $sql5="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->postingby."'";
	   $query5=mysql_query($sql5) or die(mysql_error());
	   $res5=mysql_fetch_object($query5);
	   
	
	   
	   $sqlJnsVhc="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$bar->jenisvhc ."'";
	   $qJnsVhc=mysql_query($sqlJnsVhc) or die(mysql_error());
	   $rJnsVhc=mysql_fetch_assoc($qJnsVhc);
	   
	   $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->jenisbbm."'";
	   $qBrg=mysql_query($sBrg) or die(mysql_error());
	   $rBrg=mysql_fetch_assoc($qBrg);
	
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	       $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$namapt,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','U',10);
		$this->SetY(35);
		$this->Cell(190,5,$_SESSION['lang']['invoice'],0,1,'C');		
		$this->SetFont('Arial','',6); 
		$this->SetY(27);
		$this->SetX(163);
                $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,27,200,27);	
		$this->Ln();
		
	
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
			
//ambil kelengkapan
//    $arr="##noinvoice##jatuhtempo##kodeorganisasi##nofakturpajak##tanggal##bayarke";
//    $arr.="##kodecustomer##uangmuka##noorder##nilaippn##keterangan##nilaiinvoice##debet##kredit";
           $sql3="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
	   $query3=mysql_query($sql3) or die(mysql_error());
	   $res3=mysql_fetch_object($query3); 
        $pdf->SetFont('Arial','',9); 
        $pdf->Cell(30,4,$_SESSION['lang']['noinvoice'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->noinvoice,0,1,'L'); 				
        $pdf->Cell(30,4,$_SESSION['lang']['jatuhtempo'],0,0,'L'); 
        $pdf->Cell(40,4,": ".tanggalnormal($bar->jatuhtempo),0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['kodeorganisasi'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$res3->namaorganisasi." [".$bar->kodeorg."]",0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['nofaktur'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->nofakturpajak,0,1,'L'); 		  
        $pdf->Cell(30,4,$_SESSION['lang']['tanggal'],0,0,'L'); 
        $pdf->Cell(40,4,": ".tanggalnormal($bar->tanggal),0,1,'L'); 
      
        $pdf->Cell(30,4,$_SESSION['lang']['bayarke'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->bayarke,0,1,'L');
        $pdf->Cell(30,4,$_SESSION['lang']['kodecustomer'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$optnmcust[$bar->kodecustomer],0,1,'L');
        $pdf->Cell(30,4,$_SESSION['lang']['uangmuka'],0,0,'L'); 
        $pdf->Cell(40,4,": ".number_format($bar->uangmuka,2),0,1,'L');  
        $pdf->Cell(30,4,$_SESSION['lang']['noorder'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->noorder,0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['nilaippn'],0,0,'L'); 
        $pdf->Cell(40,4,": ".number_format($bar->nilaippn,2),0,1,'L');  
        $pdf->Cell(30,4,$_SESSION['lang']['nilaiinvoice'],0,0,'L'); 
        $pdf->Cell(40,4,": ".number_format($bar->nilaiinvoice,2),0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['debet'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->debet."-".$optnmakun[$bar->debet],0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['kredit'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->kredit."-".$optnmakun[$bar->kredit],0,1,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['keterangan'],0,0,'L'); 
        $pdf->Cell(40,4,": ".$bar->keterangan,0,1,'L'); 
	
			
//footer================================
 
	
	$pdf->Output();
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
$nodok=$_GET['notransaksi'];

//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
	global $nodok;
        global $userid;
	global $posted;
	global $tanggal;
	global $dibuat;
	global $kodegudang;
	global $untukpt;
	global $untukunit;
	global $catatan;
        global $mengetahui;
        
	
		$pt='';
		$namapt='';
		$alamatpt='';
		$telp='';
		$kodegudang='';
		$status=0;
		$str="select * from ".$dbname.".log_mrisht where notransaksi='".$_GET['notransaksi']."'";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$kodept=$bar->kodept;
			$kodegudang=$bar->kodegudang;
			$userid=$bar->user;
			$posted=$bar->postedby;
			$status=$bar->post;
			$tanggal=$bar->tanggal;
			$dibuat=$bar->dibuat;
                        $mengetahui=$bar->mengetahui;
			$untukpt=$bar->untukpt;	
			$untukunit=$bar->untukunit;	
			$catatan=$bar->keterangan;				
			if($status==0)
			 $status='Not Confirm';
			else
			 $status='Confirmed'; 
			//ambil nama pt
			   
			   $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$kodept."'";
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   } 
		}
                $path="";
                $arrGmbr =array("HIP"=>$path='images/hip_logo.jpg',"SIL"=>$path='images/sil_logo.jpg',"SIP"=>$path='images/sip_logo.jpg',"PMO"=>$path='images/hip_logo.jpg');   
                //$this->Image($arrGmbr[$_SESSION['org']['kodeorganisasi']],15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(10);   
	        $this->Cell(60,5,$namapt,0,1,'L');	 
		
		$this->Line(10,17,200,17);	
	        
        }
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

/*
    print"<pre>";
	print_r($_SESSION);
	print"</pre>";
*/
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
			
//ambil kelengkapan
        $pdf->SetFont('Arial','',6); 
        $pdf->SetX(163);
        $pdf->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
        $pdf->SetFont('Arial','',11);
        $pdf->SetY(25);		
        $pdf->Cell(190,5,strtoupper($_SESSION['lang']['permintaangudangmris']),0,1,'C');
        $pdf->Ln(5);
        $hari=hari($tanggal,$_SESSION['language']);
	 
//	 $resc=str_replace("#DATE_REPARAM#",$hari.", ".$tanggal,$_SESSION['lang']['prebast']);
//	 $resc=str_replace("#SLOC_PARAM#",$kodegudang,$resc);
//	 $resc=str_replace("#VENDOR_PARAM#",$penerima,$resc);
		 
		

                $pdf->SetFont('Arial','',9);		
		$pdf->Cell(30,4,$_SESSION['lang']['sloc'],0,0,'L'); 
		$pdf->Cell(100,4,": ".$kodegudang,0,0,'L'); 
		
		$tanggal=tanggalnormal($tanggal);
		$pdf->Cell(30,4,$_SESSION['lang']['tanggal'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$tanggal,0,1,'L'); 
		
		
		$pdf->Cell(30,4,'No. MRIS',0,0,'L'); 
		$pdf->Cell(100,4,": ".$nodok,0,0,'L'); 
		
		$pdf->Cell(30,4,$_SESSION['lang']['pt'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$untukpt,0,1,'L'); 	
					
		$pdf->Cell(30,4,$_SESSION['lang']['docstatus'],0,0,'L'); 
		$pdf->Cell(100,4,": ".$status,0,0,'L'); 
		
		$pdf->Cell(30,4,$_SESSION['lang']['unit'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$untukunit,0,1,'L'); 
		
		
	 $pdf->Cell(60,4,$_SESSION['lang']['detailsbb'].":",0,1,'L'); 		 		
    $pdf->Ln();
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    $pdf->Cell(30,5,$_SESSION['lang']['kodebarang'],1,0,'C',1);
    $pdf->Cell(85,5,$_SESSION['lang']['namabarang'],1,0,'C',1);	
    $pdf->Cell(18,5,$_SESSION['lang']['satuan'],1,0,'C',1);		
    $pdf->Cell(20,5,$_SESSION['lang']['kuantitas'],1,0,'C',1);	
	$pdf->Cell(25,5,$_SESSION['lang']['kodeblok'],1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	    $pdf->SetFont('Arial','',9);
		
		$str="select * from ".$dbname.".log_mrisdt where notransaksi='".$_GET['notransaksi']."'";
		$res=mysql_query($str);
		$no=0;
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			
			$kodebarang=$bar->kodebarang;
			$satuan=$bar->satuan;
			$jumlah=$bar->jumlah;
		   $namabarang='';
		   $strv="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";	
		   $resv=mysql_query($strv);
		   while($barv=mysql_fetch_object($resv))
		   {
		   	$namabarang=$barv->namabarang;
		   }
			    $pdf->Cell(8,5,$no,1,0,'L',1);
			    $pdf->Cell(30,5,$kodebarang,1,0,'L',1);
			    $pdf->Cell(85,5,$namabarang,1,0,'L',1);	
			    $pdf->Cell(18,5,$satuan,1,0,'L',1);	
				$pdf->Cell(20,5,number_format($jumlah,2,'.',','),1,0,'R',1);
				$pdf->Cell(25,5,$bar->kodeblok,1,1,'C',1);		
			    	   
		}
       $pdf->MultiCell(170,5,"Note: ".$catatan,0,'L');			
//footer================================
        $pdf->Ln();
       if($posted!=''){
	      $posted=namakaryawan($dbname,$conn,$posted);	
       }
       $namakaryawan=namakaryawan($dbname,$conn,$dibuat);
       $namakaryawan2=namakaryawan($dbname,$conn,$mengetahui);
	   
	   
	   $nik=makeOption($dbname,'datakaryawan','karyawanid,nik');
	   
	   
       $pdf->SetFont('Arial','',8);
        $brsAkhir=$pdf->GetY();
        $pdf->SetY($brsAkhir+10);
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['dibuat'],0,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['mengetahui'],0,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,$_SESSION['lang']['managermris'],0,0,'C'); 
		$pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,'Penerima',0,0,'C'); 
       
        $pdf->Cell(10,4,"",0,0,'L'); 
        $pdf->Ln(20);
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,ucwords($namakaryawan),T,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,'',T,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,"",T,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
		$pdf->Cell(30,4,ucwords($namakaryawan2),T,0,'C'); 
		 $pdf->Cell(10,4,"",0,0,'L'); 
		 
        $pdf->Ln();
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,$nik[$dibuat],0,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,'',0,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
        $pdf->Cell(30,4,"",0,0,'C'); 
        $pdf->Cell(15,4,"",0,0,'L'); 
		$pdf->Cell(30,4,$nik[$mengetahui],0,0,'C'); 
//get user;
		
//    $pdf->Cell(20,4,$_SESSION['lang']['dbuat_oleh'],0,0,'L'); 
//    $pdf->Cell(40,4,": ".$namakaryawan,0,1,'L'); 
//    //get posted by
//    if($posted!='')
//    $posted=namakaryawan($dbname,$conn,$posted);		
//    else
//    $posted='';
//    $pdf->Cell(20,4,$_SESSION['lang']['posted'],0,0,'L'); 
//    $pdf->Cell(40,4,": ".$posted,0,1,'L');		
	$pdf->Output();
?>

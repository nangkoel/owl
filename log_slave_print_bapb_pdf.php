<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
$nodok=$_GET['notransaksi'];
$papersz=$_GET['paper'];

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
	global $idsupplier;
	global $nosj;
	global $nofaktur;
	global $kodegudang;
	global $nopo;
	global $namasupplier;
	
		$pt='';
		$namapt='';
		$alamatpt='';
		$telp='';
		$kodegudang='';
		$status=0;
		$str="select * from ".$dbname.".log_transaksiht where notransaksi='".$_GET['notransaksi']."'";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$kodept=$bar->kodept;
			$kodegudang=$bar->kodegudang;
			$userid=$bar->user;
			$posted=$bar->postedby;
			$status=$bar->post;
			$tanggal=$bar->tanggal;
			$idsupplier=$bar->idsupplier;
			$nosj=$bar->nosj;
			$nofaktur=$bar->nofaktur;	
			$nopo=$bar->nopo;		
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
                $path='images/hip_logo.jpg';
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                //exit("error".$_SESSION['org']['kodeorganisasi']);
                $this->Image($path,15,5,40);	
            
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$namapt,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->SetFont('Arial','',15);
		$this->SetY(30);		
	    $this->Cell(190,5,strtoupper($_SESSION['lang']['bapb']),0,1,'C');
		$this->SetFont('Arial','',6); 
		$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,27,200,27);	
	    $this->SetFont('Arial','',9);		
		$this->Cell(30,4,$_SESSION['lang']['sloc'],0,0,'L'); 
		$this->Cell(40,4,": ".$kodegudang,0,1,'L'); 
		$this->Cell(30,4,'Nomor BPB',0,0,'L'); 
		$this->Cell(40,4,": ".$nodok,0,1,'L'); 				
		$this->Cell(30,4,$_SESSION['lang']['docstatus'],0,0,'L'); 
		$this->Cell(40,4,": ".$status,0,1,'L'); 		  
                $this->Cell(30,4,$_SESSION['lang']['tanggal'],0,0,'L'); 
		$this->Cell(40,4,": ".tanggalnormal($tanggal),0,1,'L');
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

    if ($papersz==1){
            $pdf=new PDF('P','mm','A4');
    } else {
            $pdf=new PDF('P','mm','Letter');
    }
	$pdf->AddPage();
			
//ambil kelengkapan
//ambil supplier
     $stry="select * from ".$dbname.".log_5supplier where supplierid='".$idsupplier."'";
	 $namasupplier=$idsupplier;
	 $resy=mysql_query($stry);
	 while($bary=mysql_fetch_object($resy))
	 {
	 	$namasupplier=$bary->namasupplier;
	 }
     $hari=hari($tanggal,$_SESSION['language']);
	
//                $resc=str_replace("#DATE_REPARAM#",$hari.", ".$tanggal,$_SESSION['lang']['prebapb']);
//                $resc=str_replace("#SLOC_PARAM#",$kodegudang,$resc);
//                $resc=str_replace("#VENDOR_PARAM#",$namasupplier,$resc);
//                $pdf->Ln();
//                $pdf->Ln();	
//                $pdf->MultiCell(170,5,$resc,0,'L');	
		$pdf->Cell(30,4,$_SESSION['lang']['nopo'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$nopo,0,1,'L'); 
		$pdf->Cell(30,4,$_SESSION['lang']['namasupplier'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$namasupplier,0,1,'L'); 
		$pdf->Cell(30,4,$_SESSION['lang']['suratjalan'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$nosj,0,1,'L'); 
		$pdf->Cell(30,4,$_SESSION['lang']['faktur'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$nofatur,0,1,'L'); 		
//    $pdf->Ln();
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    $pdf->Cell(20,5,$_SESSION['lang']['nopp'],1,0,'C',1);
    $pdf->Cell(23,5,$_SESSION['lang']['kodebarang'],1,0,'C',1);
    
    $pdf->Cell(100,5,$_SESSION['lang']['namabarang'],1,0,'C',1);	
    $pdf->Cell(20,5,$_SESSION['lang']['satuan'],1,0,'C',1);		
    $pdf->Cell(20,5,$_SESSION['lang']['kuantitas'],1,1,'C',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        $sBrg="select distinct kodebarang,nopp,nopo from ".$dbname.".log_podt where nopo='".$nopo."'";
        $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
        while($rBrg=  mysql_fetch_assoc($qBrg)){
            $dtNopp[$rBrg['nopo'].$rBrg['kodebarang']]=$rBrg['nopp'];
            $lstNopp[$rBrg['nopp']]=$rBrg['nopp'];
        }
		
		$str="select * from ".$dbname.".log_transaksidt where notransaksi='".$_GET['notransaksi']."'";
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
                            $nopp=substr($dtNopp[$nopo.$kodebarang],0,3);
			    $pdf->Cell(8,5,$no,1,0,'L',1);
                            $pdf->Cell(20,5,$nopp,1,0,'C',1);
			    $pdf->Cell(23,5,$kodebarang,1,0,'C',1);
			    $pdf->Cell(100,5,$namabarang,1,0,'L',1);	
			    $pdf->Cell(20,5,$satuan,1,0,'L',1);	
			    $pdf->Cell(20,5,number_format($jumlah,2,'.',','),1,1,'R',1);		
			    	   
		}
//footer================================
        $pdf->Ln();
//get user;
        foreach($lstNopp as $dftPP){
            $sdt="select distinct catatan from ".$dbname.".log_prapoht where nopp='".$dftPP."'";
            $qdt=mysql_query($sdt) or die(mysql_error($conn));
            $rdt=mysql_fetch_assoc($qdt);
            $pdf->MultiCell(191,5,$dftPP." : ".$rdt['catatan'],0,'J'); 
        }
       
       if($posted!=''){
	      $posted=namakaryawan($dbname,$conn,$posted);	
       }
        $brsAkhir=$pdf->GetY();
        $pdf->SetY($brsAkhir+10);
        $pdf->Cell(10,4,"",0,0,'L'); 
        $pdf->Cell(37,4,'Ka. Logistic',0,0,'C'); 
        $pdf->Cell(25,4,"",0,0,'L'); 
        $pdf->Cell(37,4,$_SESSION['lang']['petugasgudang'],0,0,'C'); 
        $pdf->Cell(25,4,"",0,0,'L'); 
        $pdf->Cell(37,4,$_SESSION['lang']['penerimagudang'],0,0,'C');
        $pdf->Cell(10,4,"",0,0,'L'); 
        $pdf->Ln(20);
        $pdf->Cell(10,4,"",0,0,'L'); 
        $pdf->Cell(37,4,"",T,0,'C'); 
        $pdf->Cell(25,4,"",0,0,'L'); 
        $pdf->Cell(37,4,"",T,0,'C'); 
        $pdf->Cell(25,4,"",0,0,'L'); 
        $pdf->Cell(37,4,$posted,T,0,'C');
        $pdf->Cell(10,4,"",0,0,'L'); 
//        $pdf->Cell(27.6666666667,4,"",B,0,'R'); 
//        $pdf->Cell(72.6666666667,4,"",B,0,'R'); 
//        $pdf->Cell(82.6666666667,4,$posted,0,0,'R');
//        $pdf->Cell(30,4,"[ ...........ttd............. ]",0,0,'L'); 
//        $pdf->Cell(40,4,": ".$namakaryawan,0,1,'L'); 
//        
                
//get posted by
//       if($posted!='')
//	      $posted=namakaryawan($dbname,$conn,$posted);		
//	   else
//	      $posted='';
//	   	$pdf->Cell(20,4,$_SESSION['lang']['posted'],0,0,'L'); 
//                $pdf->Cell(30,4,"[ ..........ttd.............. ]",0,0,'L');                 
//		$pdf->Cell(40,4,": ".$posted,0,1,'L');
//                $pdf->Ln();
//                
//	   	$pdf->Cell(20,4,$_SESSION['lang']['mengetahui'],0,0,'L'); 
//                $pdf->Cell(30,4,"[ ..........ttd.............. ]",0,0,'L');      
//                $pdf->Cell(40,4,": Kasie/KTU",0,1,'L');
	$pdf->Output();
?>

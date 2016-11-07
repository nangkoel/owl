<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');
$notransaksi=$_GET['notransaksi'];



//=============

//create Header
class PDF extends FPDF
{

        function Header()
        {
			global $conn;
       		global $dbname;
			global $notransaksi;
			
			$induk=makeOption($dbname,'organisasi','kodeorganisasi,induk');
			
			$x="select kodeorg from ".$dbname.".sdm_pjdinasht  where notransaksi='".$notransaksi."'";
			//echo $str;exit();
			$y=mysql_query($x);
			$z=mysql_fetch_assoc($y);
			
			$i="select namaorganisasi,alamat,wilayahkota,telepon,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$induk[$z['kodeorg']]."'";
			//echo $i;
			
			$n=mysql_query($i);
			$d=mysql_fetch_assoc($n);
				
				$namapt=$d['namaorganisasi'];
				$alamatpt=$d['alamat'].", ".$d['wilayahkota'];
				$telp=$d['telepon'];	
		
			if($d['kodeorganisasi']=='HIP')
			{  
				$path='images/hip_logo.jpg'; 
			} else if($d['kodeorganisasi']=='SIL')
			{  
				$path='images/sil_logo.jpg'; 
			} 
			else if($d['kodeorganisasi']=='SIP')
			{	
				$path='images/sip_logo.jpg'; 
			}
		//	$this->Image($path,15,5,40);		
			
           // $path='images/logo.jpg';
            $this->Image($path,15,5,50);	
        $this->SetFont('Arial','B',9);
        $this->SetFillColor(255,255,255);	
        $this->SetX(40);   
        $this->Cell(60,5,$namapt,0,1,'L');	 
        $this->SetX(40); 		
        $this->Cell(60,5,$alamatpt,0,1,'L');	
        $this->SetX(40); 			
        $this->Cell(60,5,"Tel: ".$telp,0,1,'L');	

        $this->Line(15,35,205,35);	
         

        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }

}

  $str="select * from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";	
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

                $jabatan='';
                $namakaryawan='';
                $bagian='';	
                $karyawanid='';
                 $strc="select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan 
                    from ".$dbname.".datakaryawan a left join  ".$dbname.".sdm_5jabatan b
                        on a.kodejabatan=b.kodejabatan
                        where a.karyawanid=".$bar->karyawanid;

      $resc=mysql_query($strc);
          while($barc=mysql_fetch_object($resc))
          {
                $jabatan=$barc->namajabatan;
                $namakaryawan=$barc->namakaryawan;
                $bagian=$barc->bagian;
                $karyawanid=$barc->karyawanid;
          }

          //===============================	  
				$keterangan=$bar->keterangan;
                $kodeorg=$bar->kodeorg;
                $persetujuan=$bar->persetujuan;
				$persetujuan2=$bar->persetujuan2;
                $hrd=$bar->hrd; 
                $tujuan3=getNamaOrganisasi($bar->tujuan3);
                $tujuan2=getNamaOrganisasi($bar->tujuan2);	
                $tujuan1=getNamaOrganisasi($bar->tujuan1);
                $tanggalperjalanan=tanggalnormal($bar->tanggalperjalanan);
                $tanggalkembali=tanggalnormal($bar->tanggalkembali);
                $uangmuka=$bar->uangmuka;
                $tugas1=$bar->tugas1;
                $tugas2=$bar->tugas2;
                $tugas3=$bar->tugas3;
                $tujuanlain=$bar->tujuanlain;
                $tugaslain=$bar->tugaslain;
                $pesawat=$bar->pesawat;
                $darat=$bar->darat;
                $laut=$bar->laut;
                $dibayar=$bar->dibayar;
                $mess=$bar->mess;
                $hotel=$bar->hotel;	
				$mobilsewa=$bar->mobilsewa;
                $statushrd=$bar->statushrd;
                $tanggalhrd=$bar->tanggalhrd;
                if($statushrd==0){
                    $statushrd=$_SESSION['lang']['wait_approval'];$tanggalhrd='';
                }else if($statushrd==1){
                    $statushrd=$_SESSION['lang']['disetujui'];
                }else{
                    $statushrd=$_SESSION['lang']['ditolak'];
                }
                $statuspersetujuan=$bar->statuspersetujuan;
                $tanggalpersetujuan=$bar->tanggalpersetujuan;
		if($statuspersetujuan==0){
                    $perstatus=$_SESSION['lang']['wait_approval'];$tanggalpersetujuan='';
                }else if($statuspersetujuan==1){
                    $perstatus=$_SESSION['lang']['disetujui'];
                }else {
                    $perstatus=$_SESSION['lang']['ditolak'];
                }		
		$statuspersetujuan2=$bar->statuspersetujuan2;			
                $tanggalpersetujuan2=$bar->tanggalpersetujuan2;
		if($statuspersetujuan2==0){
                    $perstatus2=$_SESSION['lang']['wait_approval'];$tanggalpersetujuan2='';
                }else if($statuspersetujuan2==1){
                    $perstatus2=$_SESSION['lang']['disetujui'];
                }else {
                    $perstatus2=$_SESSION['lang']['ditolak'];			
                }		
        //ambil bagian,jabatan persetujuan
                $perjabatan='';
                $perbagian='';
                $pernama='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$persetujuan;	   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatan=$barf->namajabatan;
                $perbagian=$barf->bagian;
                $pernama=$barf->namakaryawan;
        }	
		
		//persetujuan2
		$perjabatan2='';
		$perbagian2='';
		$pernama2='';
		$strf2="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
	   ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
		   where karyawanid=".$persetujuan2;	 
		   
		 //  echo $strf2;
		     
        $resf2=mysql_query($strf2) or die (mysql_error($conn));
        while($barf2=mysql_fetch_object($resf2))
        {
                $perjabatan2=$barf2->namajabatan;
                $perbagian2=$barf2->bagian;
                $pernama2=$barf2->namakaryawan;
        }	
		 
//ambil jabatan, hrd

        $hjabatan='';
        $hbagian='';
        $hnama='';
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                   where karyawanid=".$hrd;		   
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $hjabatan=$barf->namajabatan;
                $hbagian=$barf->bagian;
                $hnama=$barf->namakaryawan;
        }


  }

        $pdf=new PDF('P','mm','A4');
        $pdf->SetFont('Arial','B',14);
        $pdf->AddPage();
        $pdf->SetY(40);
        $pdf->SetX(20);
        $pdf->SetFillColor(255,255,255); 
    $pdf->Cell(175,5,strtoupper($_SESSION['lang']['spdinas']),0,1,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(175,5,'NO : '.$notransaksi,0,1,'C');	

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();	
        $pdf->SetX(20);			
        $pdf->Cell(30,5,$_SESSION['lang']['nokaryawan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$karyawanid,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['namakaryawan'],0,0,'L');	
                $pdf->Cell(50,5," : ".$namakaryawan,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['bagian'],0,0,'L');	
                $pdf->Cell(50,5," : ".$bagian,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');	
                $pdf->Cell(50,5," : ".$jabatan,0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tanggaldinas'],0,0,'L');	
                $pdf->Cell(50,5," : ".$tanggalperjalanan,0,1,'L');
        $pdf->SetX(20);	
        $pdf->Cell(30,5,$_SESSION['lang']['tanggalkembali'],0,0,'L');	
                $pdf->Cell(50,5," : ".$tanggalkembali,0,1,'L');		

        $pdf->Ln();
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',8);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['tujuandantugas']),0,1,'L');		
        $pdf->SetX(30);
        $pdf->Cell(7,5,strtoupper($_SESSION['lang']['nourut']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['tujuan']),1,0,'C');	
                $pdf->Cell(120,5,strtoupper($_SESSION['lang']['tugas']),1,1,'C');	
        $pdf->SetFont('Arial','',8);
        if ($tujuan1!='' or $tugas1!='') {
        $notugas+=1;
        $pdf->SetX(30);
        $pdf->Cell(7,5,$notugas,1,0,'L');
                $pdf->Cell(30,5,$tujuan1,1,0,'L');	
                $pdf->Cell(120,5,$tugas1,1,1,'L');	
        }
        if ($tujuan2!='' or $tugas2!='') {
        $notugas+=1;
        $pdf->SetX(30);
        $pdf->Cell(7,5,$notugas,1,0,'L');
                $pdf->Cell(30,5,$tujuan2,1,0,'L');	
                $pdf->Cell(120,5,$tugas2,1,1,'L');
        }
        if ($tujuan3!='' or $tugas3!='') {
        $notugas+=1;
        $pdf->SetX(30);
        $pdf->Cell(7,5,$notugas,1,0,'L');
                $pdf->Cell(30,5,$tujuan3,1,0,'L');	
                $pdf->Cell(120,5,$tugas3,1,1,'L');
        }
        if ($tujuanlain!='' or $tugaslain!='') {
        $notugas+=1;
        $pdf->SetX(30);
        $pdf->Cell(7,5,$notugas,1,0,'L');
                $pdf->Cell(30,5,$tujuanlain,1,0,'L');	
                $pdf->Cell(120,5,$tugaslain,1,1,'L');
        }

        $pdf->Ln();
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',8);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['transportasi']."/".$_SESSION['lang']['akomodasi']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['pesawatudara']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['transportasidarat']),1,0,'C');			
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['transportasiair']),1,0,'C');			
                $pdf->Cell(20,5,strtoupper($_SESSION['lang']['mess']),1,0,'C');		
                $pdf->Cell(20,5,strtoupper($_SESSION['lang']['hotel']),1,0,'C');
				$pdf->Cell(27,5,strtoupper('Mobil Sewa'),1,1,'C');		
        $pdf->SetFont('Arial','',8);
        $pdf->SetX(30);
                $pdf->Cell(30,5,$pesawat==1?'X':'',1,0,'C');
                $pdf->Cell(30,5,$darat==1?'X':'',1,0,'C');
                $pdf->Cell(30,5,$laut==1?'X':'',1,0,'C');
                $pdf->Cell(20,5,$mess==1?'X':'',1,0,'C');
                $pdf->Cell(20,5,$hotel==1?'X':'',1,0,'C');	
				$pdf->Cell(27,5,$mobilsewa==1?'X':'',1,1,'C');	
        $pdf->Ln();
        $pdf->Ln();	
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',8);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['approval_status']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(17,5,strtoupper($_SESSION['lang']['bagian']),1,0,'C');
                $pdf->Cell(40,5,strtoupper($_SESSION['lang']['namakaryawan']),1,0,'C');			
                $pdf->Cell(40,5,strtoupper($_SESSION['lang']['functionname']),1,0,'C');
                $pdf->Cell(20,5,strtoupper($_SESSION['lang']['keputusan']),1,0,'C');	 			
                $pdf->Cell(40,5,strtoupper($_SESSION['lang']['tglPersetujuan']),1,1,'C');

        $pdf->SetFont('Arial','',8);
        $pdf->SetX(30);
                $pdf->Cell(17,5,$hbagian,1,0,'L');
                $pdf->Cell(40,5,$hnama,1,0,'L');			
                $pdf->Cell(40,5,$hjabatan,1,0,'L');
                $pdf->Cell(20,5,$statushrd,1,0,'L');	
                $pdf->Cell(40,5,$tanggalhrd,1,1,'L');	
        $pdf->SetX(30);
		
		if($perbagian=='')
		{
		}
		else
		{
			$pdf->Cell(17,5,$perbagian,1,0,'L');
			$pdf->Cell(40,5,$pernama,1,0,'L');			
			$pdf->Cell(40,5,$perjabatan,1,0,'L');
			$pdf->Cell(20,5,$perstatus,1,0,'L');
                        $pdf->Cell(40,5,$tanggalpersetujuan,1,1,'L');
		}
		
		
               
        
		$pdf->SetX(30);
                $pdf->Cell(17,5,$perbagian2,1,0,'L');
                $pdf->Cell(40,5,$pernama2,1,0,'L');			
                $pdf->Cell(40,5,$perjabatan2,1,0,'L');
                $pdf->Cell(20,5,$perstatus2,1,0,'L');
                $pdf->Cell(40,5,$tanggalpersetujuan2,1,1,'L');
        $pdf->Ln();
        $pdf->SetX(20);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['uangmuka']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['diajukan']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['disetujui']),1,1,'C');			
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(30);
                $pdf->Cell(30,5,number_format($uangmuka,2,'.',','),1,0,'R');
                $pdf->Cell(30,5,number_format($dibayar,2,'.',','),1,1,'R');
	$pdf->Ln();
	 $pdf->SetFont('Arial','B',10);			
	 $pdf->SetX(20);			
        $pdf->Cell(30,5,$_SESSION['lang']['keterangan'],0,0,'L');	
		$pdf->SetFont('Arial','',10);	
              // $pdf->Cell(50,5," : ".$keterangan,0,1,'L');	
				$pdf->MultiCell(150,5,$keterangan,0,'L');	
						


   $pdf->Ln();	
   $pdf->Ln();	
        $pdf->SetX(150);  
        $pdf->Cell(50,5,'Receipt By,',0,1,'C');	  
   $pdf->Ln();	
   $pdf->Ln();	
   $pdf->Ln();	
        $pdf->SetX(150);    
   $pdf->Cell(50,5,$namakaryawan,0,1,'C');

//footer================================
    $pdf->Ln();		
        $pdf->Output();

?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";
*/
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optJabatan=makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan');
$optNmJabatan=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');	
$optNmorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arrstatusList=array('1'=>$_SESSION['lang']['disetujui'],'2'=>$_SESSION['lang']['ditolak'],'3'=>$_SESSION['lang']['wait_approval']);
$nmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$satBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');


$nmKeg=makeOption($dbname, 'project_dt', 'kegiatan,namakegiatan');


//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
            global $conn;
            global $dbname;
            global $userid;
            global $notransaksi;
            global $posting;
            global $optNmorg;
	
			$test=explode(',',$_GET['column']);
			$notransaksi=$test[0];
			$userid=$test[1];
			$str="select * from ".$dbname.".".$_GET['table']."  where kode='".$notransaksi."' ";
			//echo $str;exit();
			$res=mysql_query($str);
			$bar=mysql_fetch_object($res);
			$posting=$bar->posting;		
			//ambil nama pt
			   $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
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
		//$this->SetFont('Arial','U',15);
		$this->SetY(35);
		//$this->Cell(190,5,$_SESSION['lang']['header'],0,1,'C');
		$this->SetFont('Arial','',6); 
		$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,27,200,27);	
		$this->Ln();
		$this->SetFont('Arial','',9); 
		$this->Cell(40,4,$_SESSION['lang']['unit'],0,0,'L');
		$this->Cell(40,4,": ".$bar->kodeorg." [".$optNmorg[$bar->kodeorg]."]",0,1,'L');
		$this->Cell(40,4,$_SESSION['lang']['kode'],0,0,'L');
		$this->Cell(40,4,": ".$bar->kode,0,1,'L');
		$this->Cell(40,4,$_SESSION['lang']['notransaksi'].' Internal',0,0,'L');
		$this->Cell(40,4,": ".$bar->notransaksi,0,1,'L');
		
		$this->Cell(40,4,$_SESSION['lang']['kelompok'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->kelompok,0,1,'L');
		
		$this->Cell(40,4,$_SESSION['lang']['nama'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->nama,0,1,'L');
		$this->Cell(40,4,$_SESSION['lang']['tanggalmulai'],0,0,'L');
		$this->Cell(40,4,": ".tanggalnormal($bar->tanggalmulai),0,1,'L');
		$this->Cell(40,4,$_SESSION['lang']['tanggalsampai'],0,0,'L');
		$this->Cell(40,4,": ".tanggalnormal($bar->tanggalselesai),0,1,'L');
		
		$this->Cell(40,4,$_SESSION['lang']['nilai'],0,0,'L'); 
		$this->Cell(40,4,": Rp ".number_format($bar->nilai,2),0,1,'L');
		
		
		$this->Cell(40,4,$_SESSION['lang']['updateby'],0,0,'L');
		$this->Cell(40,4,": ".$res2->namakaryawan,0,1,'L');
		
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
	$pdf->Ln();
        $pdf->SetY(30);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(190,5,$_SESSION['lang']['project'],0,1,'C');
	if($posting<1)
	{
            $pdf->Cell(190,5,'('.$_SESSION['lang']['onprogress'].')',0,1,'C');
	}
        else{
            $pdf->Cell(190,5,'('.$_SESSION['lang']['selesai'].')',0,1,'C');
        }
	//$pdf->SetFont('Arial','U',10);
	$pdf->SetY(70);
	//$pdf->Cell(190,5,$_SESSION['lang']['detail'],0,1,'L');
		$pdf->Ln(20);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(220,220,220);
		$pdf->Cell(1,5,"Kode ".$_SESSION['lang']['project'],0,1,'L',0);
		$pdf->SetFont('Arial','B',7);	
        $pdf->Cell(8,5,'No',1,0,'L',1);
        $pdf->Cell(25,5,"Kode ".$_SESSION['lang']['kegiatan'],1,0,'C',1);
        $pdf->Cell(50,5,$_SESSION['lang']['namakegiatan'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['dari'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['sampai'],1,1,'C',1);
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);

        $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);

        $str="select * from ".$dbname.".project_dt   where kodeproject='".$notransaksi."'"; //echo $str;exit();
        $re=mysql_query($str);
        $no=0;
        while($res=mysql_fetch_assoc($re))
        {
            $no+=1;
            $pdf->Cell(8,5,$no,1,0,'L',1);
            $pdf->Cell(25,5,$res['kegiatan'],1,0,'L',1);
            $pdf->Cell(50,5,$res['namakegiatan'],1,0,'L',1);
            $pdf->Cell(20,5,tanggalnormal($res['tanggalmulai']),1,0,'C',1);
            $pdf->Cell(20,5,tanggalnormal($res['tanggalselesai']),1,1,'C',1);
        }
		
		
		$pdf->Ln();
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(220,220,220);
		$pdf->Cell(1,5,$_SESSION['lang']['persetujuan'],0,1,'L',0);
		$pdf->SetFont('Arial','B',7);	
		
			
			$pdf->Cell(15,5,$_SESSION['lang']['urutan'],1,0,'C',1);
			$pdf->Cell(50,5,$_SESSION['lang']['nama'],1,0,'C',1);
			$pdf->Cell(35,5,$_SESSION['lang']['jabatan'],1,0,'C',1);
			$pdf->Cell(30,5,$_SESSION['lang']['status'],1,0,'C',1);
			$pdf->Cell(20,5,$_SESSION['lang']['tanggal'],1,1,'C',1);
			$pdf->SetFillColor(255,255,255);
			
   	 	$pdf->SetFont('Arial','',7);

		$iApv="select * from ".$dbname.".project   where kode='".$notransaksi."' "; 
		$nApv=mysql_query($iApv) or die (mysql_error($conn));
		$dApv=mysql_fetch_assoc($nApv);
		
		for($i=1;$i<=7;$i++)
		{
			if($dApv['tglpersetujuan'.$i]=='0000-00-00')
				$tgl='';
			else
				$tgl=tanggalnormal($dApv['tglpersetujuan'.$i]);
				
			$pdf->Cell(15,5,$i,1,0,'L',1);
            $pdf->Cell(50,5,$optNmKar[$dApv['persetujuan'.$i]],1,0,'L',1);
            $pdf->Cell(35,5,$optNmJabatan[$optJabatan[$dApv['persetujuan'.$i]]],1,0,'L',1);
			$pdf->Cell(30,5,$arrstatusList[$dApv['stpersetujuan'.$i]],1,0,'L',1);
            $pdf->Cell(20,5,$tgl,1,1,'L',1);
           
		}
	   
	   
	   
	   ############################################
	   ############################################
	   ############################################
	   ############################################
	   
	  
	$pdf->AddPage();
	$pdf->Ln();
        $pdf->SetY(30);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(190,5,$_SESSION['lang']['project'],0,1,'C');
	if($posting<1)
	{
            $pdf->Cell(190,5,'('.$_SESSION['lang']['onprogress'].')',0,1,'C');
	}
        else{
            $pdf->Cell(190,5,'('.$_SESSION['lang']['selesai'].')',0,1,'C');
        }
	
	$pdf->SetY(70);

		$pdf->Ln(20);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetFillColor(220,220,220);
		$pdf->Cell(1,5,$_SESSION['lang']['material'].' '.$_SESSION['lang']['project'],0,1,'L',0);
		$pdf->SetFont('Arial','B',7);	
		
        $pdf->Cell(8,5,'No',1,0,'L',1);
        $pdf->Cell(20,5,$_SESSION['lang']['kodekegiatan'],1,0,'C',1);
		$pdf->Cell(35,5,$_SESSION['lang']['namakegiatan'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['kodebarang'],1,0,'C',1);
        $pdf->Cell(80,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
		$pdf->Cell(15,5,$_SESSION['lang']['satuan'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['jumlah'],1,1,'C',1);
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);

        $pdf->SetFillColor(255,255,255);
  	  	$pdf->SetFont('Arial','',7);
        $iMat="select * from ".$dbname.".project_material   where kodeproject='".$notransaksi."'"; //echo $str;exit();
        $nMat=mysql_query($iMat);
        $no=0;
        while($dMat=mysql_fetch_assoc($nMat))
        {//NO	KODE KEGIATAN	Kode Barang	NAMA BARANG	SATUAN	JUMLAH
			//kodeproject	kodekegiatan	kodebarang	jumlah	updateby	updatetime

            $no+=1;
            $pdf->Cell(8,5,$no,1,0,'L',1);
			$pdf->Cell(20,5,$dMat['kodekegiatan'],1,0,'C',1);
            $pdf->Cell(35,5,$nmKeg[$dMat['kodekegiatan']],1,0,'L',1);
			$pdf->Cell(20,5,$dMat['kodebarang'],1,0,'R',1);
            $pdf->Cell(80,5,$nmBrg[$dMat['kodebarang']],1,0,'L',1);
            $pdf->Cell(15,5,$satBrg[$dMat['kodebarang']],1,0,'C',1);
            $pdf->Cell(20,5,$dMat['jumlah'],1,1,'R',1);
        }
		
		
	
	   
	   

			
//footer================================
 
	
	$pdf->Output();
?>

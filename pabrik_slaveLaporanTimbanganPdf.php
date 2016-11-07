<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";
*/
	$kdBrg=$_GET['kdBrg'];
	$kdPbrk=$_GET['kdPbrk'];
	$tgl=$_GET['tgl'];
	$tgl2=$_GET['tgl2'];	
			$txt_tgl_a=substr($tgl,0,2);
			$txt_tgl_b=substr($tgl,3,2);
			$txt_tgl_c=substr($tgl,6,4);
			$tgl=$txt_tgl_c."-".$txt_tgl_b."-".$txt_tgl_a;

			$txt_tgl_a=substr($tgl2,0,2);
			$txt_tgl_b=substr($tgl2,3,2);
			$txt_tgl_c=substr($tgl2,6,4);
			$tgl2=$txt_tgl_c."-".$txt_tgl_b."-".$txt_tgl_a;			
//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
    global $userid;
	global $kdPbrk;
	global $statId;
	global $tgl;
	global $kdBrg;

				$sOrg="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdPbrk."' ";
				$qOrg=mysql_query($sOrg) or die(mysql_error());
				$rOrg=mysql_fetch_assoc($qOrg);

				
			   $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$rOrg['induk']."'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $nama=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   }    
				$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdPbrk."'";
				$namapt='COMPANY NAME';
				$res=mysql_query($str);
				while($bar=mysql_fetch_object($res))
				{
				$namapt=strtoupper($bar->namaorganisasi);
				}
			
				
	
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	    $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$nama,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','',8);
		$this->Line(10,30,290,30);	
			$this->Cell(35,5,$_SESSION['lang']['pabrik'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(80,5,$namapt,'',0,'L');		
			$this->Cell(20,5,$_SESSION['lang']['tanggal'],'',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(35,5,$tgl,0,1,'L');
			//		$this->Cell(140,5,' ','',0,'R');	
			//			$this->Cell(140,5,' ','',0,'R');
				
			$this->Cell(35,5,'User','',0,'L');
			$this->Cell(2,5,':','',0,'L');
			$this->Cell(80,5,$_SESSION['standard']['username'],'',0,'L');
			if($kdBrg!='0')
			{
				$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
				$qBrg=mysql_query($sBrg) or die(mysql_error());
				$rBrg=mysql_fetch_assoc($qBrg);	
				$this->Cell(20,5,$_SESSION['lang']['namabarang'],'',0,'L');
				$this->Cell(2,5,':','',0,'L');
				$this->Cell(35,5,$rBrg['namabarang'],0,1,'L');
			}
		
			//	        $this->Ln();	
	
     $this->Ln();
	 
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

	$pdf=new PDF('L','mm','A4');
	$pdf->AddPage();

	$pdf->SetFont('Arial','U',15);
	$pdf->SetY(50);
	$pdf->Cell(250,5,$_SESSION['lang']['laporanPabrikTimbangan'],0,1,'C');	
	$pdf->Ln();	
	$pdf->SetFont('Arial','B',7);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(6,4,'No',1,0,'L',1);
	if($kdBrg==0)
	{$pdf->Cell(30,4,$_SESSION['lang']['namabarang'],1,0,'C',1);
	}
	$pdf->Cell(15,4,$_SESSION['lang']['tanggal'],1,0,'C',1);
	$pdf->Cell(15,4,$_SESSION['lang']['noTiket'],1,0,'C',1);	
	$pdf->Cell(22,4,$_SESSION['lang']['kodenopol'],1,0,'C',1);
        $pdf->Cell(25,4,$_SESSION['lang']['transportasi'],1,0,'C',1);
	$pdf->Cell(17,4,$_SESSION['lang']['beratMasuk'],1,0,'C',1);
	$pdf->Cell(17,4,$_SESSION['lang']['beratKeluar'],1,0,'C',1);		
	$pdf->Cell(17,4,$_SESSION['lang']['beratBersih'],1,0,'C',1);
	$pdf->Cell(18,4,$_SESSION['lang']['jammasuk'],1,0,'C',1);
	$pdf->Cell(18,4,$_SESSION['lang']['jamkeluar'],1,0,'C',1);
	$pdf->Cell(15,4,$_SESSION['lang']['unit'],1,0,'C',1);
	$pdf->Cell(38,4,$_SESSION['lang']['supplier'],1,0,'C',1);
	$pdf->Cell(20,4,$_SESSION['lang']['sopir'],1,0,'C',1);
	$pdf->Cell(18,4,$_SESSION['lang']['brondolan'],1,1,'C',1);
	
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',7);

	if($kdBrg=='0')
	{
		$str="select * from ".$dbname.".pabrik_timbangan where tanggal between '".$tgl."' and  '".$tgl2."' and millcode='".$kdPbrk."' order by tanggal asc";
	}
	elseif($kdBrg!='0')
	{
		$str="select * from ".$dbname.".pabrik_timbangan where tanggal between '".$tgl."' and  '".$tgl2."' and millcode='".$kdPbrk."' and kodebarang='".$kdBrg."' order by tanggal asc";
	}
	
		$re=mysql_query($str);
		$no=0;
		while($res=mysql_fetch_assoc($re))
		{
			$no+=1;
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
		   	if($res['kodecustomer']!='')
			{
				if(($res['kodebarang']=='40000001')||($res['kodebarang']=='40000005')||($res['kodebarang']=='40000002')||($res['kodebarang']=='40000004'))
				{
					$sKontrak="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$res['nokontrak']."'";//echo $sKontrak;exit();
					$qKontrak=mysql_query($sKontrak) or die(mysql_error($conn));
					$rKontrak=mysql_fetch_assoc($qKontrak);
					$sSupp="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rKontrak['koderekanan']."'"; //echo $sSupp;exit();
					$qSupp=mysql_query($sSupp) or die(mysql_error());
					$rSupp=mysql_fetch_assoc($qSupp) ;
					$hsl=$rSupp['namacustomer'];
				}
				elseif($res['kodebarang']=='40000003')
				{
					$sSupp="select namasupplier  from ".$dbname.".log_5supplier where supplierid='".$res['kodecustomer']."'"; //echo $sCust;exit();
					$qSupp=mysql_query($sSupp) or die(mysql_error());
					$rSupp=mysql_fetch_assoc($qSupp) ;
					$hsl=$rSupp['namasupplier'];
				}
			}
			$pdf->Cell(6,4,$no,1,0,'L',1);
			$tmb=0;
                         #transporter
                        $rTRP='';
                        $sTRP="select TRPNAME  from ".$dbname.".pabrik_transporter where TRPCODE='".$res['trpcode']."'"; //echo $sCust;exit();
                        $qTRP=mysql_query($sTRP) or die(mysql_error());
                        $rTRP=mysql_fetch_assoc($qTRP) ;
			if($kdBrg==0)
			{
				$pdf->Cell(30,4,$rBrg['namabarang'],1,0,'L',1);
				$tmb=30;
			}	
                        
			$pdf->Cell(15,4,substr($res['tanggal'],0,10),1,0,'L',1);
			$pdf->Cell(15,4,$res['notransaksi'],1,0,'L',1);
			$pdf->Cell(22,4,$res['nokendaraan'],1,0,'L',1);
                        $pdf->Cell(25,4,$rTRP['TRPNAME'],1,0,'L',1);
			$pdf->Cell(17,4,number_format($res['beratmasuk'],2),1,0,'R',1);
			$pdf->Cell(17,4,number_format($res['beratkeluar'],2),1,0,'R',1);		
			$pdf->Cell(17,4,number_format($res['beratbersih'],2),1,0,'R',1);
			$pdf->Cell(18,4,$res['jammasuk'],1,0,'C',1);
			$pdf->Cell(18,4,$res['jamkeluar'],1,0,'C',1);
			$pdf->Cell(15,4,$res['kodeorg'],1,0,'C',1);
			$pdf->Cell(38,4,$hsl,1,0,'L',1);
			$pdf->Cell(20,4,$res['supir'],1,0,'L',1);
			$pdf->Cell(18,4,$res['brondolan'],1,1,'R',1);
			$totBeratMsk+=$res['beratmasuk'];
			$totBeratKlr+=$res['beratkeluar'];
			$totBeratBrs+=$res['beratbersih'];
			$totBrondolan+=$res['brondolan'];
		}
		$pdf->Cell(83+$tmb,4,'Total',1,0,'R',1);
		$pdf->Cell(17,4,number_format($totBeratMsk,2),1,0,'R',1);
		$pdf->Cell(17,4,number_format($totBeratKlr,2),1,0,'R',1);
		$pdf->Cell(17,4,number_format($totBeratBrs,2),1,0,'R',1);
		$pdf->Cell(109,4,'',1,0,'R',1);
		$pdf->Cell(18,4,$totBrondolan,1,1,'R',1);

	
	$pdf->Output();
?>

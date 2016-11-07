<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/fpdf.php');
    require_once('lib/nangkoelib.php');

    $pt=$_GET['pt'];
    $gudang=$_GET['gudang'];
    $tgl1=$_GET['tgl1'];
    $tgl2=$_GET['tgl2'];

    class PDF extends FPDF
    {
        function Header() {
            global $conn;
            global $dbname;
            global $align;
            global $length;
            global $colArr;
            global $title;
            global $pt;
            global $gudang;
            global $periode;
            global $tgl1;
            global $tgl2;

            $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
            $qAlamat=mysql_query($sAlmat) or die(mysql_error());
            $rAlamat=mysql_fetch_assoc($qAlamat);

            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 11;
            if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,$this->lMargin,$this->tMargin,70);	
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255,255,255);	
            $this->SetX(100);   
            $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
            $this->SetX(100); 		
            $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
            $this->SetX(100); 			
            $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
            $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
            $this->Ln();	
            $this->Ln();
            $this->SetFont('Arial','B',11);
            $this->Cell($width,$height, $_SESSION['lang']['laporanpanen'],0,1,'C');	
            $this->Cell($width,$height, $_SESSION['lang']['periode'].":".$tgl1." S/d ".$tgl2 ." ".$_SESSION['lang']['unit'].":" .($gudang!=''?$gudang:$_SESSION['lang']['all']),0,1,'C');	
            $this->SetFont('Arial','',8);

            $this->Ln();
            $this->SetFont('Arial','B',7);	
            $this->SetFillColor(220,220,220);

            $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['afdeling'],1,0,'C',1);
            $this->Cell(12/100*$width,$height,$_SESSION['lang']['lokasi'],1,0,'C',1);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['bloklama'],1,0,'C',1);
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['upahkerja'],1,0,'C',1);	
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['upahpremi'],1,0,'C',1);	
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['jumlahhk'],1,0,'C',1);	
            $this->Cell(10/100*$width,$height,$_SESSION['lang']['penalti'],1,1,'C',1);		            
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    $pdf=new PDF('P','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 9;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
    if($gudang=='')
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
            sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from (".$dbname.".kebun_prestasi_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi) inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg 
        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal,a.kodeorg";
    }
    else
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
            sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a 
                inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg
        where unit = '".$gudang."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." group by a.tanggal, a.kodeorg";
    }  

    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $periode=date('Y-m-d H:i:s');
        $notransaksi=$bar->notransaksi;
        $tanggal=$bar->tanggal; 
        $kodeorg 	=$bar->kodeorg;
        $bloklama 	=$bar->bloklama;
        $no+=1;
        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,tanggalnormal($tanggal),1,0,'C',1);
        $pdf->Cell(8/100*$width,$height,substr($kodeorg,0,6),1,0,'L',1);
        $pdf->Cell(12/100*$width,$height,$kodeorg,1,0,'L',1);
        $pdf->Cell(8/100*$width,$height,$bloklama,1,0,'L',1);
        $pdf->Cell(10/100*$width,$height,$bar->tahuntanam,1,0,'C',1);	
        $pdf->Cell(8/100*$width,$height,number_format($bar->jjg,0),1,0,'R',1);	
        $pdf->Cell(10/100*$width,$height,number_format($bar->berat,2),1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,number_format($bar->upah,2),1,0,'R',1);	
        $pdf->Cell(10/100*$width,$height,number_format($bar->premi,2),1,0,'R',1);	
        $pdf->Cell(8/100*$width,$height,number_format($bar->jumlahhk,0),1,0,'R',1);	
        $pdf->Cell(10/100*$width,$height,number_format($bar->penalty,2),1,1,'R',1);	

        $totberat+=$bar->berat;
        $totUpah+=$bar->upah;
        $totJjg+=$bar->jjg;
        $totPremi+=$bar->premi;
        $totHk+=$bar->jumlahhk;
        $totPenalty+=$bar->penalty;

    }	
    //$pdf->Cell(43/100*$width,$height,"Total",1,0,'R',1);
    $pdf->Cell(51/100*$width,$height,"Total",1,0,'R',1);
    $pdf->Cell(8/100*$width,$height,number_format($totJjg,0),1,0,'R',1);
    $pdf->Cell(10/100*$width,$height,number_format($totberat,2),1,0,'R',1);
    $pdf->Cell(10/100*$width,$height,number_format($totUpah,2),1,0,'R',1);
    $pdf->Cell(10/100*$width,$height,number_format($totPremi,2),1,0,'R',1);
    $pdf->Cell(8/100*$width,$height,number_format($totHk,0),1,0,'R',1);
    $pdf->Cell(10/100*$width,$height,number_format($totPenalty,2),1,1,'R',1);
            
    $pdf->Output();
//#print_r($pt);
//#exit;
////ambil namapt
//$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
//$namapt='COMPANY NAME';
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res))
//{
//	$namapt=strtoupper($bar->namaorganisasi);
//}
//	
//if($periode=='' and $gudang=='')
//{
//		$str="select * from ".$dbname.".kebun_aktifitas a
//		left join ".$dbname.".organisasi c
//		on substr(a.kodeorg,1,4)=c.kodeorganisasi
//		where c.induk = '".$pt."' and tipetransaksi='PNN'
//		";
//}
//else if($periode=='' and $gudang!='')
//{
//		$str="select * from ".$dbname.".kebun_aktifitas a
//		left join ".$dbname.".organisasi c
//		on substr(a.kodeorg,1,4)=c.kodeorganisasi
//		where c.induk = '".$pt."' and tipetransaksi='PNN' and kodeorg='".$gudang."'";
//}
//else{
//	if($gudang=='')
//	{
//		$str="select * from ".$dbname.".kebun_aktifitas a
//		left join ".$dbname.".organisasi c
//		on substr(a.kodeorg,1,4)=c.kodeorganisasi
//		where c.induk = '".$pt."' and tipetransaksi='PNN' and substr(tanggal,1,7)='".$periode."'";
//	}
//	else
//	{
//		$str="select * from ".$dbname.".kebun_aktifitas a
//		left join ".$dbname.".organisasi c
//		on substr(a.kodeorg,1,4)=c.kodeorganisasi
//		where c.induk = '".$pt."' and tipetransaksi='PNN' and substr(tanggal,1,7)='".$periode."' and kodeorg='".$gudang."'";
//	}	
//}
////=================================================
//class PDF extends FPDF {
//    function Header() {
//       global $namapt;
//        $this->SetFont('Arial','B',8); 
//		$this->Cell(20,3,$namapt,'',1,'L');
//        $this->SetFont('Arial','B',12);
//		$this->Cell(190,3,strtoupper($_SESSION['lang']['laporanhasilpanen']),0,1,'C');
//        $this->SetFont('Arial','',8);
//		$this->Cell(155,3,' ','',0,'R');
//		$this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
//		$this->Cell(2,3,':','',0,'L');
//		$this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
//		$this->Cell(155,3,' ','',0,'R');
//		$this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
//		$this->Cell(2,3,':','',0,'L');
//		$this->Cell(35,3,$this->PageNo(),'',1,'L');
//		$this->Cell(155,3,' ','',0,'R');
//		$this->Cell(15,3,'User','',0,'L');
//		$this->Cell(2,3,':','',0,'L');
//		$this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
//        $this->Ln();
//        $this->SetFont('Arial','',6);
//		$this->Cell(5,5,'No.',1,0,'C');
//		$this->Cell(24,5,$_SESSION['lang']['notransaksi'],1,0,'C');
//		$this->Cell(16,5,$_SESSION['lang']['tanggal'],1,0,'C');	
//		$this->Cell(40,5,$_SESSION['lang']['namakaryawan'],1,0,'C');	
//		$this->Cell(15,5,$_SESSION['lang']['blok'],1,0,'C');	
//		$this->Cell(20,5,$_SESSION['lang']['hasilkerja'],1,0,'C');	
//		$this->Cell(15,5,$_SESSION['lang']['penalti1'],1,0,'C');	
//		$this->Cell(15,5,$_SESSION['lang']['penalti2'],1,0,'C');
//		$this->Cell(15,5,$_SESSION['lang']['penalti3'],1,0,'C');
//		$this->Cell(15,5,$_SESSION['lang']['penalti4'],1,0,'C');
//		$this->Cell(15,5,$_SESSION['lang']['penalti5'],1,0,'C');
//        $this->Ln();						
//        $this->Ln();						
//
//    }
//}
////================================
//if($periode=='')
//{
//	 $sawalQTY		='';
//	 $masukQTY		='';
//	 $keluarQTY		='';
//	 $kuantitas=0;
//	$res=mysql_query($str);
//	$no=0;
//	if(mysql_num_rows($res)<1)
//	{
//		echo$_SESSION['lang']['tidakditemukan'];
//	}
//	else
//	{
//	$pdf=new PDF('P','mm','A4');
//	$pdf->AddPage();
//		while($bar=mysql_fetch_object($res))
//		{
//#			$no+=1;
//#			$periode	=date('d-m-Y H:i:s');
//#			$kodebarang	=$bar->kodebarang;
//#			$namabarang	=$bar->namabarang; 
//#			$kuantitas 	=$bar->kuan;
//#			$notransaksi	=$bar->notransaksi;
//#			$tanggal    =$bar->tanggal;
//#			$noakun		=$bar->noakun;
//#			$namaakun	=$bar->namaakun;
//#			$keterangan =$bar->keterangan;
//#			$jumlah 	=$bar->jumlah;
//#		$pdf->Cell(5,3,$no,0,0,'C');
//#		$pdf->Cell(24,3,$notransaksi,0,0,'L');
//#		$pdf->Cell(18,3,tanggalnormal($tanggal),0,0,'C');				
//#		$pdf->Cell(12,3,$noakun,0,0,'L');	
//#		$pdf->Cell(40,3,$namaakun,0,0,'L');	
//#		$pdf->Cell(44,3,$keterangan,0,0,'L');
//#		$pdf->Cell(25,3,number_format($debet,2,'.',','),0,0,'R');	
//#		$pdf->Cell(25,3,number_format($kredit,2,'.',','),0,1,'R');	
//		}	
//		$pdf->Output();	
//	}
//}
//else
//	{
//		$shasilkerja = $spenalti1 = $spenalti2 = $spenalti3 = $spenalti4 = $spenalti5 = 0;
//		$thasilkerja = $tpenalti1 = $tpenalti2 = $tpenalti3 = $tpenalti4 = $tpenalti5 = 0;
//	//
//	$res=mysql_query($str);
//	$no=0;
//	if(mysql_num_rows($res)<1)
//	{
//		echo$_SESSION['lang']['tidakditemukan'];
//	}
//	else
//	{
//		$pdf=new PDF('P','mm','A4');
//		$pdf->AddPage();
//
//	while($bar=mysql_fetch_object($res))
//	{
//		$no+=1;
//		$tanggal    =$bar->tanggal;
//		$notransaksi=$bar->notransaksi;
//		$nikmandor 	=$bar->nikmandor;
//		$nikmandor1	=$bar->nikmandor1;
//		$nikasisten	=$bar->nikasisten;
//		$keranimuat	=$bar->keranimuat;
//		
//		$pdf->Cell(5,3,$no,0,0,'C');
//		$pdf->Cell(24,3,$notransaksi,0,0,'L');
//		$pdf->Cell(18,3,tanggalnormal($tanggal),0,0,'C');				
//		$pdf->Cell(12,3,$nikmandor,0,0,'L');	
//		$pdf->Cell(12,3,$nikmandor1,0,0,'L');	
//		$pdf->Cell(12,3,$nikasisten,0,0,'L');
//		$pdf->Cell(12,3,$keranimuat,0,1,'L');
//		$str1="select *,c.namakaryawan from ".$dbname.".kebun_prestasi a
//		left join ".$dbname.".datakaryawan c
//		on a.nik=c.karyawanid
//		where notransaksi='".$notransaksi."'
//		";
//		$res1=mysql_query($str1);
//		if(mysql_num_rows($res1)>=1)
//		{
//			while($bar1=mysql_fetch_object($res1))
//			{
//				$notransaksi1=$bar1->notransaksi;
//				$nik1=$bar1->nik;
//				$namakaryawan1=$bar1->namakaryawan;
//				$kodeorg1=$bar1->kodeorg;
//				$hasilkerja1=$bar1->hasilkerja;
//				$penalti1=$bar1->penalti1;
//				$penalti2=$bar1->penalti2;
//				$penalti3=$bar1->penalti3;
//				$penalti4=$bar1->penalti4;
//				$penalti5=$bar1->penalti5;
//				$pdf->Cell(32,3,' ',0,0,'L');
//				$pdf->Cell(15,3,$nik1,0,0,'L');
//				$pdf->Cell(40,3,$namakaryawan1,0,0,'L');
//				$pdf->Cell(15,3,substr($kodeorg1,6,4),0,0,'L');
//#				$pdf->Cell(20,3,'0',0,0,'R');
//				$pdf->Cell(15,3,number_format($hasilkerja1,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($penalti1,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($penalti2,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($penalti3,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($penalti4,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($penalti5,0),0,1,'R');
//				$shasilkerja += $hasilkerja1;
//				$spenalti1 += $penalti1;
//				$spenalti2 += $penalti2;
//				$spenalti3 += $penalti3;
//				$spenalti4 += $penalti4;
//				$spenalti5 += $penalti5;
//				$thasilkerja += $hasilkerja1;
//				$tpenalti1 += $penalti1;
//				$tpenalti2 += $penalti2;
//				$tpenalti3 += $penalti3;
//				$tpenalti4 += $penalti4;
//				$tpenalti5 += $penalti5;
//			}
//		}
//		$pdf->Cell(102,2,' ',0,0,'L');
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(17,2,'-------------------------',0,1,'R');	
//		$pdf->Cell(102,3,' ',0,0,'C');
//		$pdf->Cell(15,3,number_format($shasilkerja,0),0,0,'R');
//		$pdf->Cell(15,3,number_format($spenalti1,0),0,0,'R');
//		$pdf->Cell(15,3,number_format($spenalti2,0),0,0,'R');
//		$pdf->Cell(15,3,number_format($spenalti3,0),0,0,'R');
//		$pdf->Cell(15,3,number_format($spenalti4,0),0,0,'R');
//		$pdf->Cell(15,3,number_format($spenalti5,0),0,1,'R');
//		$shasilkerja = $spenalti1 = $spenalti2 = $spenalti3 = $spenalti4 = $spenalti5 = 0;
//	}
//		$pdf->Cell(102,2,' ',0,0,'L');
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(17,2,'-------------------------',0,1,'R');	
//		$pdf->Cell(102,3,'T O T A L   : ',0,0,'C');
//				$pdf->Cell(15,3,number_format($thasilkerja,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($tpenalti1,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($tpenalti2,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($tpenalti3,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($tpenalti4,0),0,0,'R');
//				$pdf->Cell(15,3,number_format($tpenalti5,0),0,1,'R');
//		$pdf->Cell(102,2,' ',0,0,'L');
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(15,2,'-------------------------',0,0,'R');	
//		$pdf->Cell(17,2,'-------------------------',0,1,'R');	
//	$pdf->Output();	
// }
//}	
?>
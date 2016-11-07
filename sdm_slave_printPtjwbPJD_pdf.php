<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
require_once('lib/zMysql.php');
$notransaksi=$_GET['notransaksi'];

//=============

//create Header
class PDF extends FPDF
{

        function Header()
        {
            global $namapt;
            global $pt;
            if($pt[0]['induk']=='HIP'){  $path='images/hip_logo.jpg'; } else if($pt[0]['induk']=='SIL'){  $path='images/sil_logo.jpg'; } else if($pt[0]['induk']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,15,2,40);	
                $this->SetFont('Arial','B',10);
                $this->SetFillColor(255,255,255);	
                $this->SetY(23);   
            $this->Cell(60,5,strtoupper($namapt[0]['namaorganisasi']),0,1,'C');	 
                $this->SetFont('Arial','',15);
            $this->Cell(190,5,'',0,1,'C');
                $this->SetFont('Arial','',6); 
                $this->SetY(30);
                $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
                $this->Line(10,30,200,30);

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
  $closure='Open';
  while($bar=mysql_fetch_object($res))
  {
      $strOrg="select * from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
      $pt=fetchData($strOrg);
      $strOrg="select * from ".$dbname.".organisasi where kodeorganisasi='".$pt[0]['induk']."'";
      $namapt=fetchData($strOrg);
      //echo $pt[0]['induk'];
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
//$strw="select a.namaorganisasi from ".$dbname.".datakaryawan b left join ".$dbname.".organisasi a 
//          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
//$resw=mysql_query($strw);
//while($barw=mysql_fetch_object($resw)){
//    $namapt=$barw->namaorganisasi;
//}
          //===============================	  

                $kodeorg=$bar->kodeorg;
                $persetujuan=$bar->persetujuan;
                $persetujuan2=$bar->persetujuan2;
                $hrd=$bar->hrd; 
                $tujuan3=$bar->tujuan3;
                $tujuan2=$bar->tujuan2;	
                $tujuan1=$bar->tujuan1;
                $tanggalperjalanan=tanggalnormal($bar->tanggalperjalanan);
                $tanggalkembali=tanggalnormal($bar->tanggalkembali);
                $uangmuka=$bar->uangmuka;
                $dibayar=$bar->dibayar;
                $tugas1=$bar->tugas1;
                $tugas2=$bar->tugas2;
                $tugas3=$bar->tugas3;
                $tujuanlain=$bar->tujuanlain;
                $tugaslain=$bar->tugaslain;
                $pesawat=$bar->pesawat;
                $darat=$bar->darat;
                $laut=$bar->laut;
                $mess=$bar->mess;
                $hotel=$bar->hotel;	

                if($bar->lunas==1)
                  $closure='Closed';

                $statushrd=$bar->statushrd;
                if($statushrd==0)
                    $statushrd=$_SESSION['lang']['wait_approval'];
        else if($statushrd==1)
                    $statushrd=$_SESSION['lang']['disetujui'];
        else 
                    $statushrd=$_SESSION['lang']['ditolak'];

                $statuspersetujuan=$bar->statuspersetujuan;
                if($statuspersetujuan==0)
                    $perstatus=$_SESSION['lang']['wait_approval'];
        else if($statuspersetujuan==1)
                    $perstatus=$_SESSION['lang']['disetujui'];
        else 
                    $perstatus=$_SESSION['lang']['ditolak'];
        //ambil bagian,jabatan persetujuan
                $perjabatan='';
                $perbagian='';
                $pernama='';
        if ($hrd==$persetujuan){
            $whr="where karyawanid=".$persetujuan2;
        } else {
            $whr="where karyawanid=".$persetujuan;
        }
        $strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan ".$whr;
        $resf=mysql_query($strf);
        while($barf=mysql_fetch_object($resf))
        {
                $perjabatan=$barf->namajabatan;
                $perbagian=$barf->bagian;
                $pernama=$barf->namakaryawan;
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
        $pdf->SetAutoPageBreak(true,20);
        $pdf->SetY(37);
        $pdf->SetX(20);
        $pdf->SetFillColor(255,255,255); 
    $pdf->Cell(175,5,strtoupper($_SESSION['lang']['spdinas']),0,1,'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(175,5,'NO : '.$notransaksi,0,1,'C');	

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
        $pdf->SetFont('Arial','B',10);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['tujuandantugas']),0,1,'L');		
        $pdf->SetX(30);
        $pdf->Cell(7,5,strtoupper($_SESSION['lang']['nourut']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['tujuan']),1,0,'C');	
                $pdf->Cell(120,5,strtoupper($_SESSION['lang']['tugas']),1,1,'C');	
        $pdf->SetFont('Arial','',10);
        $pdf->SetX(30);
        $pdf->Cell(7,5,'1',1,0,'L');
                $pdf->Cell(30,5,$tujuan1,1,0,'L');	
                $pdf->Cell(120,5,$tugas1,1,1,'L');	
        $pdf->SetX(30);
        $pdf->Cell(7,5,'2',1,0,'L');
                $pdf->Cell(30,5,$tujuan2,1,0,'L');	
                $pdf->Cell(120,5,$tugas2,1,1,'L');
        $pdf->SetX(30);
        $pdf->Cell(7,5,'3',1,0,'L');
                $pdf->Cell(30,5,$tujuan3,1,0,'L');	
                $pdf->Cell(120,5,$tugas3,1,1,'L');
        $pdf->SetX(30);
        $pdf->Cell(7,5,'4',1,0,'L');
                $pdf->Cell(30,5,$tujuanlain,1,0,'L');	
                $pdf->Cell(120,5,$tugaslain,1,1,'L');

        $pdf->Ln();
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',9);		
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['transportasi']."/".$_SESSION['lang']['akomodasi']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['pesawatudara']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['transportasidarat']),1,0,'C');			
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['transportasiair']),1,0,'C');			
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['mess']),1,0,'C');		
                $pdf->Cell(37,5,strtoupper($_SESSION['lang']['hotel']),1,1,'C');		
        $pdf->SetFont('Arial','',9);
        $pdf->SetX(30);
                $pdf->Cell(30,5,$pesawat==1?'X':'',1,0,'C');
                $pdf->Cell(30,5,$darat==1?'X':'',1,0,'C');
                $pdf->Cell(30,5,$laut==1?'X':'',1,0,'C');
                $pdf->Cell(30,5,$mess==1?'X':'',1,0,'C');
                $pdf->Cell(37,5,$hotel==1?'X':'',1,1,'C');	
	
//==========================
//uangmuka
        $pdf->Ln();
        $pdf->SetX(30);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['uangmuka']),0,1,'L');	
        $pdf->SetX(30);
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['diajukan']),1,0,'C');
                $pdf->Cell(30,5,strtoupper($_SESSION['lang']['disetujui']),1,1,'C');			
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(30);
                $pdf->Cell(30,5,number_format($uangmuka,2,'.',','),1,0,'R');
                $pdf->Cell(30,5,number_format($dibayar,2,'.',','),1,1,'R');
//=============================
//pertanggungjawaban
        $pdf->Ln();
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(172,5,strtoupper($_SESSION['lang']['pertanggungjawabandinas']),0,1,'L');	
        $pdf->SetX(20);
                $pdf->Cell(7,5,'NO',1,0,'C');
                $pdf->Cell(38,5,strtoupper($_SESSION['lang']['jenisbiaya']),1,0,'C');	
                $pdf->Cell(18,5,strtoupper($_SESSION['lang']['tanggal']),1,0,'C');
                $pdf->Cell(65,5,strtoupper($_SESSION['lang']['keterangan']),1,0,'C');
                $pdf->Cell(20,5,strtoupper($_SESSION['lang']['jumlah']),1,0,'C');
                $pdf->Cell(20,5,strtoupper($_SESSION['lang']['disetujui']),1,1,'C');
        $pdf->SetFont('Arial','',8);
//========ambil detail
$str="select a.*,b.keterangan as jns from ".$dbname.".sdm_pjdinasdt a
      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id
          where a.notransaksi='".$notransaksi."' order by tanggal";
$res=mysql_query($str);
$no=0;
$total=0;
$total1=0;
$turunin=11*65;//batesin baris yg tampil
while($bar=mysql_fetch_object($res))
{
        $no+=1;$h=5;
        if($no!=1){
           $pdf->SetY($akhirY);
        }
        $akhirY=$pdf->GetY();
        if($akhirY>=$turunin){
           $pdf->AddPage();
           $akhirY=$pdf->GetY();
        }    
        $pdf->SetX(20);
        if (strlen($bar->keterangan)>45) $h=$h*2;
                $pdf->Cell(7,$h,$no,1,0,'L');
                $pdf->Cell(38,$h,$bar->jns,1,0,'L');
                $pdf->Cell(18,$h,tanggalnormal($bar->tanggal),1,0,'L');
                $posisiY=round($pdf->GetY());
                $pdf->MultiCell(65,5,$bar->keterangan,1,'L',0);
                $akhirY=$pdf->GetY();
                $pdf->SetY($posisiY);
                $pdf->SetX($pdf->GetX()+138);
                $pdf->Cell(20,$h,number_format($bar->jumlah,2,'.','.'),1,0,'R');
                $pdf->Cell(20,$h,number_format($bar->jumlahhrd,2,'.','.'),1,1,'R');
        $total+=$bar->jumlah;
        $total1+=$bar->jumlahhrd;		
}
        $balance=$dibayar-$total1;
        $pdf->SetX(20);
    $pdf->Cell(128,5,'TOTAL',1,0,'C');
        $pdf->Cell(20,5,number_format($total,2,'.','.'),1,0,'R');
        $pdf->Cell(20,5,number_format($total1,2,'.','.'),1,1,'R');
        $pdf->Ln();
        $pdf->SetX(30);
        $pdf->Cell(50,5,$_SESSION['lang']['saldo'].": ".number_format($balance,2,'.','.')." *[".$closure."]",0,1,'L');			
//======================						
//footer================================
    $pdf->Ln();
        $pdf->Cell(63,5,$_SESSION['lang']['dibuat'],0,0,'C');
        $pdf->Cell(63,5,$_SESSION['lang']['verifikasi'],0,0,'C');
        $pdf->Cell(63,5,$_SESSION['lang']['dstujui_oleh'],0,1,'C');        
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
        $pdf->SetFont('Arial','U',10);
        $pdf->Cell(63,5,$namakaryawan,0,0,'C');
        $pdf->Cell(63,5,$hnama,0,0,'C');        
        $pdf->Cell(63,5,$pernama,0,1,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(63,5,$_SESSION['lang']['karyawan'],0,0,'C');        
        $pdf->Cell(63,5,'HRD',0,0,'C');             
        $pdf->Cell(63,5,$_SESSION['lang']['atasan'],0,1,'C');
        $pdf->Output();

?>

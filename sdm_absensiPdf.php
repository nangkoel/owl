<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

//=============
        $tmp=explode(',',$_GET['column']);
        $kdOrg=$tmp[0];
        $tgl=$tmp[1];

//create Header
class PDF extends FPDF
{

        function Header()
        {
        global $conn;
        global $dbname;
    global $userid;
        global $kdOrg;
        global $tgl;

                        $sInduk="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
                        $qInduk=mysql_query($sInduk) or die(mysql_error());
                        $rInduk=mysql_fetch_assoc($qInduk);

                  // $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$rInduk['induk']."'"; 
                   $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'"; 
                   $res1=mysql_query($str1);
                   while($bar1=mysql_fetch_object($res1))
                   {
                         $nama=$bar1->namaorganisasi;
                         $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
                         $telp=$bar1->telepon;				 
                   }    

                   $sIsi="select * from ".$dbname.".sdm_absensiht where kodeorg='".$kdOrg."' and tanggal='".tanggalsystem($tgl)."'";
                   $qIsi=mysql_query($sIsi) or die(mysql_error());
                   $rIsi=mysql_fetch_assoc($qIsi);

                        $sOrg="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rIsi['kodeorg']."'";
                        $qOrg=mysql_query($sOrg) or die(mysql_error());
                        $rOrg=mysql_fetch_assoc($qOrg);

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
                $this->Line(10,30,200,30);	

                        $this->Cell(35,5,$_SESSION['lang']['kodeorg'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(75,5,$rIsi['kodeorg'],'',0,'L');		
                        $this->Cell(25,5,$_SESSION['lang']['nm_perusahaan'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(35,5,$rOrg['namaorganisasi'],0,1,'L');
                        $this->Cell(35,5,$_SESSION['lang']['tanggal'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(75,5,$tgl,'',0,'L');		
                        $this->Cell(25,5,$_SESSION['lang']['periode'],'',0,'L');
                        $this->Cell(2,5,':','',0,'L');
                        $this->Cell(35,5,substr(tanggalnormal($rIsi['periode']),1,7),0,1,'L');	
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

        $pdf->Ln();

        $pdf->SetFont('Arial','U',10);
        $pdf->SetY(55);
        $pdf->Cell(190,5,strtoupper($_SESSION['lang']['list']." ".$_SESSION['lang']['absensi']),0,1,'C');	
        $pdf->Ln();	
        $pdf->SetFont('Arial','B',7);	
        $pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
        $pdf->Cell(40,5,$_SESSION['lang']['namakaryawan'],1,0,'C',1);	
        //$pdf->Cell(20,5,$_SESSION['lang']['shift'],1,0,'C',1);
        $pdf->Cell(15,5,$_SESSION['lang']['absensi'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['jam'],1,0,'C',1);
        $pdf->Cell(20,5,$_SESSION['lang']['keluar'],1,0,'C',1); 
        $pdf->Cell(30,5,$_SESSION['lang']['premi'],1,0,'C',1);
        $pdf->Cell(30,5,$_SESSION['lang']['penaltykehadiran'],1,0,'C',1); 
        $pdf->Cell(35,5,$_SESSION['lang']['keterangan'],1,1,'C',1);		


        //$pdf->Cell(25,5,'Total',1,1,'C',1);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',7);
                $str="select * from ".$dbname.".sdm_absensidt where tanggal='".tanggalsystem($tgl)."' and kodeorg='".$kdOrg."' order by tanggal asc";// echo $str;exit();
                $re=mysql_query($str);
                $no=0;
                while($res=mysql_fetch_assoc($re))
                {
                        $sKry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res['karyawanid']."'";
                        $qKry=mysql_query($sKry) or die(mysql_error());
                        $rKry=mysql_fetch_assoc($qKry);

                        $sShift="select keterangan from ".$dbname.".sdm_5absensi where kodeabsen='".$res['absensi']."'";
                        $qShif=mysql_query($sShift) or die(mysql_error());
                        $rShift=mysql_fetch_assoc($qShif);

                        $no+=1;
                        $pdf->Cell(8,5,$no,1,0,'L',1);
                        $pdf->Cell(40,5,$rKry['namakaryawan'],1,0,'L',1);	
                        $pdf->Cell(15,5,$res['absensi'],1,0,'L',1);
                        $pdf->Cell(20,5,$res['jam'],1,0,'C',1);
                        $pdf->Cell(20,5,$res['jamPlg'],1,0,'C',1);   
                        $pdf->Cell(30,5,number_format($res['premi']),1,0,'R',1); 
                        $pdf->Cell(30,5,number_format($res['penaltykehadiran']),1,0,'R',1); 
                        $pdf->Cell(35,5,$res['penjelasan'],1,1,'L',1);	
                }
        $pdf->Output();
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');
$nosp=$_GET['nosp'];


$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$indukOrg=makeOption($dbname,'organisasi','kodeorganisasi,induk');
//=============

  $str="select * from ".$dbname.".sdm_suratperingatan 
        where nomor='".$nosp."'";	
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
          // ..get detail perusahaan untuk kop surat
                $sKop = "select a.namaorganisasi,a.alamat,a.telepon,a.fax,b.alamatnpwp,c.kodeorganisasi
                        from ".$dbname.".organisasi a 
                        left join ".$dbname.".setup_org_npwp b on a.kodeorganisasi=b.kodeorg
                        left join ".$dbname.".datakaryawan c on a.kodeorganisasi=c.kodeorganisasi
                        where karyawanid='".$bar->karyawanid."'";
//                $sKop = "select a.namaorganisasi,a.alamat,a.telepon,a.fax,b.alamatnpwp,c.induk
//                        from ".$dbname.".organisasi a 
//                        left join ".$dbname.".setup_org_npwp b on a.kodeorganisasi=b.kodeorg
//                        left join ".$dbname.".organisasi c on a.kodeorganisasi=c.induk
//                        where c.kodeorganisasi='".$bar->kodeorg."'";
                //echo $sKop;
                $qKop = mysql_query($sKop) or die(mysql_error());
                while ($rKop = mysql_fetch_object($qKop)) {
                    $kdOrg = $rKop->kodeorganisasi;
                    $namaPT = $rKop->namaorganisasi;
                    $addPT = $rKop->alamat;
                    $tlpPT = $rKop->telepon;
                    $faxPT = $rKop->fax;
                    $addKbn = $rKop->alamatnpwp;
                }
                $addModo = "Branch Office    : ".$addKbn.", Telp/Fax. : ".$tlpPT."/".$faxPT;
                $addModo2 = "Plantation    : ".$addKbn.", Telp/Fax. : ".$tlpPT."/".$faxPT;
                $addNunu = "Nunukan Office    : ".$addKbn.", Telp/Fax. : ".$tlpPT."/".$faxPT;
                $addJKT = "Jakarta Office       : ".$addPT.", Telp. : ".$tlpPT.", Fax. : ".$faxPT;
                $paragraf1 = "Pimpinan ".$namaPT.", dengan ini menerangkankan bahwa yang namanya tersebut di bawah ini :";
                $paragraf2 = "Pernah bekerja di ".$namaPT.", dengan kondisi kerja terakhir sebagai berikut :";

      
	  $pt=$nmOrg[$indukOrg[$bar->kodeorg]];
	  

        //===================smbil nama karyawan
          $namakaryawan='';
          $strx="select a.namakaryawan,b.namajabatan,tipekaryawan from ".$dbname.".datakaryawan a 
          left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
          where karyawanid=".$bar->karyawanid;

          $resx=mysql_query($strx);
          //echo mysql_error($conn);
          while($barx=mysql_fetch_object($resx))
          {
                $namakaryawan=$barx->namakaryawan;
                $jabatanybs=$barx->namajabatan;
                $tipex=$barx->tipekaryawan;
          }

          $tanggal=tanggalnormal($bar->tanggal);
          $sampai=tanggalnormal($bar->sampai);
          $tipesp=$bar->jenissp;
          //====================ambil tipe untuk hal
          $ketHal='';
          $str="select keterangan from ".$dbname.".sdm_5jenissp where kode='".$tipesp."'";
          $rekx=mysql_query($str);
          while($barkx=mysql_fetch_object($rekx))
          {
                $ketHal=trim($barkx->keterangan);
          }
          //===============================

          $paragraf1=$bar->paragraf1;
          $pelanggaran=$bar->pelanggaran;
          $paragraf3=$bar->paragraf3;
          $paragraf4=$bar->paragraf4;
          $karyawanid=$bar->karyawanid;

          $penandatangan=$bar->penandatangan;
          $jabatan=$bar->jabatan;
          $tembusan1=$bar->tembusan1;
          $tembusan2=$bar->tembusan2;
          $tembusan3=$bar->tembusan3;
          $tembusan4=$bar->tembusan4;
          $verifikasi=$bar->verifikasi;
          $dibuat=$bar->dibuat;
          $jabatandibuat=$bar->jabatandibuat;
          $jabatanverifikasi=$bar->jabatanverifikasi;
  }

//create Header
class PDF extends FPDF
{

        function Header()
        {
               /* global $conn;
                global $dbname;
                 $this->SetFillColor(255,255,255); 
            $this->SetMargins(15,10,0);
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,15,5,40);	
                $this->SetFont('Arial','B',20);
                $this->SetFillColor(255,255,255);	
                $this->SetX(55);   
                $this->Cell(60,15,'HARDAYA GROUP',0,1,'L');	 	

                $this->Line(15,35,205,35);	
                $this->SetFont('Arial','',6); 	
                //$this->SetY(27);
                $this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');*/

        }



        function Footer()
        {
                /*global $conn;
                global $dbname;
            $str1="select namaorganisasi,alamat,wilayahkota,telepon from ".$dbname.".organisasi where kodeorganisasi='PMO'";
               $res1=mysql_query($str1);
               while($bar1=mysql_fetch_object($res1))
               {
                     $namapt=$bar1->namaorganisasi;
                     $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
                     $telp=$bar1->telepon;				 
               }            
            $this->SetY(-15);
            $this->Line(15,275,205,275);    
            $this->SetFont('Arial','I',8);
            $this->Cell(160,5,$alamatpt.", Tel:".$telp,0,1,'L');*/
            //$this->Cell(10,5,'Page '.$this->PageNo(),0,0,'C');
        }

}
  
        $pdf=new PDF('P','mm','A4');
        $pdf->SetFont('Arial','B',14);
        $pdf->AddPage();
        // .. BEGIN KOP SURAT
        $pdf->SetMargins(15,10,0);
        if ($kdOrg=='SIL') {
            $fontsizePT=21.5;
            $path = 'images/sil_logo.jpg';
            $namaPTPanjang="P T.   S E B A K I S   I N T I   L E S T A R I";
        } else if ($kdOrg=='SIP') {
            $fontsizePT=21.5;
            $path = 'images/sip_logo.jpg';
            $namaPTPanjang="P T.   S E B U K U   I N T I   P L A N T A T I O N";
        } else if ($kdOrg=='HIP') {
            $fontsizePT=19;
            $path = 'images/hip_logo.jpg';
            $namaPTPanjang="P T.  H A R D A Y A  I N T I  P L A N T A T I O N";
        }
        $pdf->Image($path,12,5,45);
        $pdf->SetFont('Times','B',$fontsizePT);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetX(42);
        $pdf->Cell(80,10,$namaPTPanjang,0,1,'L'); 
        $pdf->SetFont('Times','',9);
        //$this->SetY(27);
        $pdf->SetX(42);
        if ($kdOrg=='HIP'){
            $pdf->Cell(0,0,"Head Office       : ".$addPT.", Telp. : ".$tlpPT.", Fax. : ".$faxPT,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,"Branch Office    : Jl. Dewi Sartika II, Lorong Jembolan No.32, Palu - Sulawesi Tengah  Telp & Fax.: 0451-487659",5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,0,"Plantation        : Desa Winangun, Kab Buol - Sulawesi Tengah. Website : www.hardaya.co.id",5,1,'L');
        } else if ($kdOrg=='SIL'){
            $pdf->Cell(0,0,$addNunu,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,$addJKT,5,1,'L');
        } else {
            $pdf->Cell(0,0,$addNunu,5,1,'L');
            $pdf->SetX(42);
            $pdf->Cell(0,7,$addJKT,5,1,'L');
        }
        // ..END OF KOP SURAT
        $pdf->SetY(35);
        $pdf->SetFillColor(255,255,255); 
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(20,5,'No ',0,0,'L');
        $pdf->Cell(5,5,':',0,0,'L');
        $pdf->Cell(100,5,$nosp,0,0,'L');
        $pdf->Cell(40,5,$_SESSION['lang']['tanggal']." : ".$tanggal,0,1,'R');
        $pdf->SetX(20);
        $pdf->Cell(20,5,$_SESSION['lang']['hal1'],0,0,'L');
        $pdf->Cell(5,5,':',0,0,'L');
        $pdf->SetFont('Arial','U',10);
        $pdf->Cell(115,5,$ketHal,0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetX(20);
        $pdf->Cell(20,5,$_SESSION['lang']['kepada'],0,0,'L');
        $pdf->Cell(5,5,':',0,0,'L');	
        $pdf->Cell(100,5,$namakaryawan,0,1,'L');
        $pdf->SetX(20);
        $pdf->Cell(20,5,$_SESSION['lang']['jabatan'],0,0,'L');
        $pdf->Cell(5,5,':',0,0,'L');	
        $pdf->Cell(100,5,$jabatanybs,0,1,'L');
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','U',10);
        $pdf->Cell(115,5,$_SESSION['lang']['ditempat'],0,1,'L');
        $pdf->SetFont('Arial','',10);			
        $pdf->Ln();
        $pdf->Ln();		
        $pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf1,0,'J');	
        $pdf->Ln();
        $pdf->SetX(20);	
        $pdf->SetFont('Arial','I',10);		
    $pdf->MultiCell(170,5,$pelanggaran,0,'J');
        $pdf->Ln();	
        $pdf->SetFont('Arial','',10);				
        $pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf3,0,'J');
        $pdf->Ln();	
        $pdf->SetX(20);			
    $pdf->MultiCell(170,5,$paragraf4,0,'J');			
        $pdf->Ln();	
        $pdf->Ln();
        $pdf->Ln();
        
    if($tipex=='0'){
//=========penandatangan
			
        $pdf->SetX(20);
        $pdf->Cell(40,5,$pt,0,1,'L');
        $pdf->Ln();
        $pdf->Ln();			
        $pdf->Ln();	
        $pdf->SetX(20);
        $pdf->Cell(40,5,"".$penandatangan." ",'B',1,'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);	
        $pdf->Cell(40,5,"".$jabatan." ",0,1,'L');	
    }else{
         $pdf->SetX(20);
        $pdf->Cell(40,5,$_SESSION['lang']['dibuat'],0,0,'C');
        $pdf->Cell(15,5,'',0,0,'C');
        $pdf->Cell(40,5,$_SESSION['lang']['verifikasi'],0,0,'C'); 
        $pdf->Cell(15,5,'',0,0,'C');        
        $pdf->Cell(40,5,$_SESSION['lang']['disetujui'],0,1,'C');        
        $pdf->Ln();
        $pdf->Ln();			
        $pdf->Ln();	
        $pdf->SetX(20);
        $pdf->Cell(40,5,"".$dibuat." ",'B',0,'C');
        $pdf->Cell(15,5,'',0,0,'C');        
        $pdf->Cell(40,5,"".$verifikasi." ",'B',0,'C');
        $pdf->Cell(15,5,'',0,0,'C');        
        $pdf->Cell(40,5,"".$penandatangan." ",'B',1,'C');        
        $pdf->SetX(20);
        $pdf->SetFont('Arial','',10);	
        $pdf->Cell(40,5,"".$jabatandibuat." ",0,0,'C');
        $pdf->Cell(15,5,'',0,0,'C');        
        $pdf->Cell(40,5,"".$jabatanverifikasi." ",0,0,'C');
        $pdf->Cell(15,5,'',0,0,'C');        
        $pdf->Cell(40,5,"".$jabatan." ",0,1,'C');        
    }
//=====================tembusan	
        $pdf->Ln();
        $pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(i)   : ".$tembusan1,0,1,'L');
        $pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(ii)  : ".$tembusan2,0,0,'L');			
        $pdf->Cell(70,5,'',0,0,'C');        
        $pdf->Cell(40,5,$_SESSION['lang']['diterima'],0,1,'C');        
        $pdf->SetX(20);			
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(iii) : ".$tembusan3,0,1,'L');			
        $pdf->SetX(20);
    $pdf->Cell(40,5,$_SESSION['lang']['tembusan']."(iv) : ".$tembusan4,0,1,'L');			
        $pdf->Ln(4);
        $pdf->SetX(20);
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'C');
        $pdf->Cell(40,5,$namakaryawan,'B',1,'C');
        $pdf->SetX(20);
        $pdf->Cell(40,5,'',0,0,'L');
        $pdf->Cell(70,5,'',0,0,'C');
        $pdf->Cell(40,5,$_SESSION['lang']['karyawan'],0,1,'C');

//footer================================
    $pdf->Ln();		
        $pdf->Output();

?>

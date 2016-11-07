<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
$nosk=$_GET['nosk'];
$tipe=strtoupper(substr($nosk,4,2));
//=============

//create Header
class PDF extends FPDF
{

        function Header()
        {
                /*global $conn;
                global $dbname;
                global $tipe;
                 $this->SetFillColor(255,255,255); 

            $this->SetMargins(15,10,0);
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,15,5,40);	
                $this->SetFont('Arial','B',28);
                $this->SetFillColor(255,255,255);	
                $this->SetX(55);   
                $this->SetTextColor(0,150,0);
                $this->Cell(60,15,'CCM GROUP',0,1,'L');
                $this->SetTextColor(0,0,0);
                $this->Line(15,35,205,35);	

                //$this->SetY(27);
                $this->SetX(163);
                $this->SetFont('Arial','',10);       
                if($_SESSION['language']=='EN'){
                    $this->Cell(30,5,'CONFIDENTIAL',0,1,'R');   
                }else{
                    $this->Cell(30,5,'PRIBADI DAN RAHASIA',0,1,'R');   
                }
                $this->SetFont('Arial','',6);                 
                $this->SetX(163);
                $this->Cell(30,5,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'R');*/

        }



        function Footer()
        {
               /* global $conn;
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
           // $this->Cell(10,5,'Page '.$this->PageNo(),0,0,'C');
        }

}
function ambiljabatan($kodejabatan,$conn,$dbname)
{
        $d='';	
                 $strc="select * from ".$dbname.".sdm_5jabatan where 
                kodejabatan=".$kodejabatan;	
      $resc=mysql_query($strc);
          while($barc=mysql_fetch_object($resc))
          {
                $d=$barc->namajabatan;
          }
        return   $d;
}	

function ambiltipekaryawan($idtipe,$conn,$dbname)
{
        $opt='';
        $str="select * from ".$dbname.".sdm_5tipekaryawan where id=".$idtipe;
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
           $opt=$bar->tipe;	
        }
        return $opt;		
}



  $str="select * from ".$dbname.".sdm_riwayatjabatan where nomorsk='".$nosk."'";	
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

        //===================smbil nama 
          $namakaryawan='';
          $strx="select a.namakaryawan,a.tanggalmasuk, a.lokasipenerimaan,b.nama from ".$dbname.".datakaryawan a 
              left join ".$dbname.".sdm_5departemen b on a.bagian=b.kode
              where karyawanid=".$bar->karyawanid;

          $resx=mysql_query($strx);
          while($barx=mysql_fetch_object($resx))
          {
                $namakaryawan=$barx->namakaryawan;
                $tanggalmasuk=$barx->tanggalmasuk;
                $lokasipenerimaan=$barx->lokasipenerimaan;
          }

          $strx="select a.namakaryawan,b.namajabatan from ".$dbname.".datakaryawan a
                 left join  ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
                 where  karyawanid=".$bar->atasanbaru;
      $atasanbaru='';
          $jabatanatasan='';
          $resx=mysql_query($strx);
          while($barx=mysql_fetch_object($resx))
          {
                $atasanbaru=$barx->namakaryawan;
                $jabatanatasan=$barx->namajabatan;
          }	  

          $tanggal=tanggalnormal($bar->tanggalsk);
          $mulaiberlaku=tanggalnormal($bar->mulaiberlaku);
          $tipesk=$bar->tipesk;
          //====================ambil tipe untuk hal
          $ketHal='';
          $str="select keterangan from ".$dbname.".sdm_5tipesk where kode='".$tipesk."'";
          $rekx=mysql_query($str);
          while($barkx=mysql_fetch_object($rekx))
          {
                $ketHal=trim($barkx->keterangan);
          }
          //===============================
      $oldjabatan=ambiljabatan($bar->darikodejabatan,$conn,$dbname);
      $newjabatan=ambiljabatan($bar->kekodejabatan,$conn,$dbname);
          $oldtipe=ambiltipekaryawan($bar->daritipe,$conn,$dbname);
      $newtipe=ambiltipekaryawan($bar->ketipekaryawan,$conn,$dbname);
      $lokasitugasbaru=$bar->kekodeorg;
      // Tambahan  Agung
        $sKop = "select a.namaorganisasi,a.alamat,a.telepon,a.fax,b.alamatnpwp,c.induk
                from ".$dbname.".organisasi a 
                left join ".$dbname.".setup_org_npwp b on a.kodeorganisasi=b.kodeorg
                left join ".$dbname.".organisasi c on a.kodeorganisasi=c.induk
                where c.kodeorganisasi='".$bar->darikodeorg."'";
        //echo $sKop;
        $qKop = mysql_query($sKop) or die(mysql_error());
        while ($rKop = mysql_fetch_object($qKop)) {
            $kdOrg = $rKop->induk;
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

          $str="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE kodeorganisasi='".$bar->darikodeorg."'";
          $rekx=mysql_query($str);
          while($barkx=mysql_fetch_object($rekx))
          {
                $oldlokasitugas=trim($barkx->namaorganisasi);
          }
          $str="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE kodeorganisasi='".$bar->kekodeorg."'";
          $rekx=mysql_query($str);
          while($barkx=mysql_fetch_object($rekx))
          {
                $newlokasitugas=trim($barkx->namaorganisasi);
          }
//          $oldlokasitugas=$bar->darikodeorg;
//          $newlokasitugas=$bar->kekodeorg;
          $darigaji=$bar->darigaji;
          $kegaji=$bar->kegaji;
          $nomorinduk=$bar->karyawanid;
          $oldkodegolongan=$bar->darikodegolongan;
          $newkodegolongan=$bar->kekodegolongan;
          $direksi=$bar->namadireksi;
          $tembusan1=$bar->tembusan1;
          $tembusan2=$bar->tembusan2;
          $tembusan3=$bar->tembusan3;
          $tembusan4=$bar->tembusan4;
          $tembusan5=$bar->tembusan5;
          $tjjabatan=$bar->tjjabatan;
          $ketjjabatan=$bar->ketjjabatan;

        $tjsdaerah  =$bar->tjsdaerah; 
        $ketjsdaerah=$bar->ketjsdaerah;
        $tjmahal    =$bar->tjmahal; 
        $ketjmahal  =$bar->ketjmahal;
        $tjpembantu =$bar->tjpembantu; 
        $ketjpembantu=$bar->ketjpembantu;
        $tjkota     =$bar->tjkota;
        $ketjkota   =$bar->ketjkota; 
        $tjtransport=$bar->tjtransport;
        $ketjtransport=$bar->ketjtransport; 
        $tjmakan    =$bar->tjmakan; 
        $ketjmakan  =$bar->ketjmakan;

          $namajabatan=$bar->namajabatan;
          $pg1=trim($bar->pg1);
          $pg2=trim($bar->pg2);	
          $bagian=$bar->bagian;
          $kebagian=$bar->kebagian;
  }

//===============ambil PT tempat baru
//  $namaptx=  getPT($dbname, $bar->kekodeorg);
//  echo $namaptx['nama'];
                          $strf="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi in(
                                 select induk from ".$dbname.".organisasi where kodeorganisasi='".$lokasitugasbaru."')";

                           $resf=mysql_query($strf);
                           while($barf=mysql_fetch_object($resf))
                           {
                                 $namaptx=$barf->namaorganisasi;				 
                           } 
//  
$paragraf1="Dalam rangka memperoleh hasil guna yang optimum dan sesuai dengan perkembangan perusahaan dewasa ini, dipandang perlu melaksanakan ".$tipesk."  terhadap karyawan lingkungan CCM Group.";
 if($pg1!=='')
     $paragraf1=$pg1;
$paragraf2="Terhitung mulai tanggal ".$mulaiberlaku.", perusahaan melakukan ".$tipesk." terhadap saudara/(i):";	

$paragraf3="Jajaran direksi CCM Group mengucapkan selamat berkarya.";	

$paragraf25   ="Dalam melaksanakan tugas / jabatan tersebut diatas berada dibawah dan bertanggung jawab kepada ".ucwords(strtolower($atasanbaru))." sebagai ".ucwords(strtolower($jabatanatasan))."  di ".$namaptx.".";
$paragrafadd  ="Masa percobaan sebagai Pelaksana Tugas ".$newjabatan." selama 1 (satu) tahun.";
$paragraffadd2="Jika dikemudian hari ternyata terdapat kekeliruan dalam Surat Keputusan ini, akan diadakan perbaikan sebagaimana mestinya.";

if($pg2!==''){
    $paragraf25=$pg2;
    $paragrafadd='';
    $paragraffadd2='';
}

        $pdf=new PDF('P','mm','A4');
        $pdf->SetFont('Arial','B',14);
        $pdf->AddPage();
        // .. BEGIN KOP SURAT
        $pdf->SetMargins(15,10,0);
        $pdf->Ln();
        $pdf->Ln();

        //$pdf=new PDF('P','mm','A4');
        //$pdf->SetFont('Arial','BU',14);
        //$pdf->AddPage();
        //$pdf->SetY(40);
        //$pdf->SetX(20);
        //$pdf->SetFillColor(255,255,255); 
        if($tipe=='PE'){//penyesuaian
            
         $pdf->Cell(175,7,'SURAT KEPUTUSAN - PENYESUAIAN GAJI/TUNJANGAN','0',1,'C');   
         $pdf->SetFont('Arial','',12);
         $pdf->Cell(175,5,"No.".substr($nosk,12,3)." / SK-GAJI / HRD / HO / ".substr($tanggal,3,2)." / ".substr($tanggal,6,4),0,1,'C');
         $pdf->Ln();
         $pdf->Ln();
         $pdf->SetFont('Arial','',10);
         $pdf->Cell(175,5,"Manajemen perusahaan memutuskan sebagai berikut:",0,1,'L');
         $pdf->Ln();
            $pdf->Cell(30,5,$_SESSION['lang']['namakaryawan'],0,0,'L');			
            $pdf->Cell(40,5," : ".$namakaryawan,0,1,'L');		
            $pdf->Cell(30,5,$_SESSION['lang']['nokaryawan'],0,0,'L');			
            $pdf->Cell(40,5," : ".$nomorinduk,0,1,'L');	       
            $pdf->Cell(30,5,'TMK',0,0,'L');			
            $pdf->Cell(40,5," : ".tanggalnormal($tanggalmasuk),0,1,'L');
         $pdf->Ln();
        $pdf->SetFont('Arial','B',12);
         $pdf->Cell(30,5,'A.Status Karyawan',0,1,'L');
        $pdf->SetFont('Arial','B',10);
         $pdf->Cell(20,5,'No',1,0,'C');
         $pdf->Cell(50,5,'Deskripsi',1,0,'C');
         $pdf->Cell(50,5,'Dari',1,0,'C');
         $pdf->Cell(50,5,'Menjadi',1,1,'C');
        $pdf->SetFont('Arial','',10);         
         $pdf->Cell(20,5,'1',1,0,'L');
         $pdf->Cell(50,5,$_SESSION['lang']['functionname'],1,0,'L');
         $pdf->Cell(50,5,$oldjabatan,1,0,'L');
         $pdf->Cell(50,5,$newjabatan,1,1,'L');
         $pdf->Cell(20,5,'2',1,0,'L');         
         $pdf->Cell(50,5,$_SESSION['lang']['levelname'],1,0,'L');
         $pdf->Cell(50,5,$oldkodegolongan,1,0,'L');
         $pdf->Cell(50,5,$newkodegolongan,1,1,'L');
         $pdf->Cell(20,5,'3',1,0,'L');         
         $pdf->Cell(50,5,'Divisi/Dept./Sect./Unit',1,0,'L');
         $pdf->Cell(50,5,$bagian,1,0,'L');
         $pdf->Cell(50,5,$kebagian,1,1,'L');
         $pdf->Cell(20,5,'4',1,0,'L');         
         $pdf->Cell(50,5,$_SESSION['lang']['lokasitugas'],1,0,'L');
         $pdf->Cell(50,5,$oldlokasitugas,1,0,'L');
         $pdf->Cell(50,5,$newlokasitugas,1,1,'L');
         $pdf->Cell(20,5,'5',1,0,'L');         
         $pdf->Cell(50,5,$_SESSION['lang']['poh'],1,0,'L');
         $pdf->Cell(100,5,$lokasipenerimaan,1,1,'L');


         $pdf->Ln();
        $pdf->SetFont('Arial','B',12);
         $pdf->Cell(30,5,'B.	Gaji & Tunjangan (Netto)',0,1,'L');
        $pdf->SetFont('Arial','B',10);
         $pdf->Cell(20,5,'No',1,0,'C');
         $pdf->Cell(50,5,'Deskripsi',1,0,'C');
         $pdf->Cell(50,5,'Dari',1,0,'C');
         $pdf->Cell(50,5,'Menjadi',1,1,'C');  
        $pdf->SetFont('Arial','',10); 
         $pdf->Cell(20,5,'1',1,0,'L');
         $pdf->Cell(50,5,$_SESSION['lang']['gajipokok'],1,0,'L');
         $pdf->Cell(50,5,number_format($darigaji,2),1,0,'R');
         $pdf->Cell(50,5,number_format($kegaji,2),1,1,'R');
         $no=1;
         if($tjjabatan>0 or $ketjjabatan>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjjabatan'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjjabatan,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjjabatan,2),1,1,'R');
         }
         if($tjsdaerah>0 or $ketjsdaerah>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjsdaerah'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjsdaerah,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjsdaerah,2),1,1,'R');
         }
         if($tjmahal>0 or $ketjmahal>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjmahal'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjmahal,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjmahal,2),1,1,'R');
         }
         if($tjpembantu>0 or $ketjpembantu>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjpembantu'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjpembantu,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjpembantu,2),1,1,'R');
         }
         if($tjkota>0 or $ketjkota>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjkota'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjkota,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjkota,2),1,1,'R');
         }
         if($tjtransport>0 or $ketjtransport>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjtransport'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjtransport,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjtransport,2),1,1,'R');
         }
         if($tjmakan>0 or $ketjmakan>0){   
             $no+=1;
             $pdf->Cell(20,5,$no,1,0,'L');
             $pdf->Cell(50,5,$_SESSION['lang']['tjmakan'],1,0,'L');
             $pdf->Cell(50,5,number_format($tjmakan,2),1,0,'R');
             $pdf->Cell(50,5,number_format($ketjmakan,2),1,1,'R');
         }         
         $pdf->Ln();
        $pdf->SetFont('Arial','B',12);
         $pdf->Cell(30,5,'C. Lain Lain',0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(5,5,'',0,0,'L'); 
        $pdf->Cell(160,5,'Surat Keputusan ini berlaku terhitung mulai tanggal :'.$mulaiberlaku,0,1,'L');        
        }
        else {
                // .. BEGIN KOP SURAT
                $pdf->SetMargins(15,10,0);
                    if ($kdOrg=='HIP') {
                        $path = 'images/hip_logo.jpg';
                    } else
                    if ($kdOrg=='SIL') {
                        $path = 'images/sil_logo.jpg';
                    } else
                    if ($kdOrg=='SIP') {
                        $path = 'images/sip_logo.jpg';
                    }
                $pdf->Image($path,12,5,40);
                $pdf->SetFont('Times','B',18);
                $pdf->SetFillColor(255,255,255);
                $pdf->SetX(42);
                $pdf->Cell(80,10,$namaPT,0,1,'L'); 
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
            
                $pdf->Cell(175,5,$_SESSION['lang']['suratkeputusan'],0,1,'C');
                $pdf->SetX(20);
                $pdf->SetFont('Arial','',10);
                $pdf->Cell(175,5,'NO : '.$nosk,0,1,'C');	
                $pdf->SetX(150);
                $pdf->Cell(40,5,$_SESSION['lang']['tanggal']." : ".$tanggal,0,1,'R');
                $pdf->Ln();
                $pdf->Ln();	
                $pdf->SetX(20);
                $pdf->Cell(175,5,$_SESSION['lang']['hal1']." : ".$_SESSION['lang'][$ketHal],0,1,'L');
                $pdf->Ln();
                $pdf->Ln();	
                $pdf->SetX(20);			
            $pdf->MultiCell(170,5,$paragraf1,0,'J');	
                $pdf->Ln();
                $pdf->SetX(20);			
            $pdf->MultiCell(170,5,$paragraf2,0,'J');
        //	$pdf->Ln();	
                $pdf->SetX(20);			
            $pdf->Cell(30,5,$_SESSION['lang']['nama'],0,0,'L');			
            $pdf->Cell(40,5," : ".$namakaryawan,0,1,'L');
                $pdf->SetX(20);			
            $pdf->Cell(30,5,$_SESSION['lang']['nokaryawan'],0,0,'L');			
            $pdf->Cell(40,5," : ".$nomorinduk,0,1,'L');	

                $pdf->Ln();	
                $pdf->SetX(20);		
                $pdf->Cell(40,5,$_SESSION['lang']['dari']." : ",0,1,'L');	
                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['lokasitugas'],0,0,'L');	
                $pdf->Cell(40,5," : ".$oldlokasitugas,0,1,'L');	

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');	
                $pdf->Cell(40,5," : ".$oldjabatan,0,1,'L');

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['tipekaryawan'],0,0,'L');	
                $pdf->Cell(40,5," : ".$oldtipe,0,1,'L');		

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['levelname'],0,0,'L');	
                $pdf->Cell(40,5," : ".$oldkodegolongan,0,1,'L');


        //===============ke
                $pdf->Ln();	
                $pdf->SetX(20);		
                $pdf->Cell(40,5,$_SESSION['lang']['ke']." : ",0,1,'L');	
                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['lokasitugas'],0,0,'L');	
                $pdf->Cell(40,5," : ".$newlokasitugas,0,1,'L');	

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['functionname'],0,0,'L');	
                $pdf->Cell(40,5," : ".$newjabatan,0,1,'L');

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['tipekaryawan'],0,0,'L');	
                $pdf->Cell(40,5," : ".$newtipe,0,1,'L');		

                $pdf->SetX(20);	
                $pdf->Cell(30,5,$_SESSION['lang']['levelname'],0,0,'L');	
                $pdf->Cell(40,5," : ".$newkodegolongan,0,1,'L');

            if($tipesk=='Mutasi')
            {
                    $pdf->Ln();
                    $pdf->SetX(20);			
                $pdf->MultiCell(170,5,$paragraf25,0,'J');

                    $pdf->Ln();
                    $pdf->SetX(20);			
                $pdf->MultiCell(170,5,$paragraf3,0,'J');	
            }
            else
            {
                    $pdf->Ln();
                    $pdf->SetX(20);			
                $pdf->MultiCell(170,5,$paragrafadd." ".$paragraf25." ".$paragrafadd2,0,'J');		
            }

}
//=========penandatangan
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(40,5,"Jakarta, ".$tanggal,'0',1,'L');	
        $pdf->Ln();
        $pdf->Ln();			
        $pdf->Ln();
        $pdf->SetFont('Arial','BU',10);
        $pdf->Cell(40,5,"".$direksi." ",'U',1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(40,5,($namajabatan==''?$_SESSION['lang']['direksi']:$namajabatan),0,1,'L');        
//=====================tembusan	
        $pdf->Ln();		
    $pdf->Cell(25,5,$_SESSION['lang']['tembusan']."(i) : ",0,0,'R');	
    $pdf->Cell(50,5,$tembusan1,0,1,'L');
    $pdf->Cell(25,5,"(ii) : ",0,0,'R');            
    $pdf->Cell(50,5,$tembusan2,0,1,'L');
    $pdf->Cell(25,5,"(iii) : ",0,0,'R');    
    $pdf->Cell(50,5,$tembusan3,0,1,'l');
    $pdf->Cell(25,5,"(iv) : ",0,0,'R');    
    $pdf->Cell(50,5,$tembusan4,0,1,'L');
    $pdf->Cell(25,5,"(v) : ",0,0,'R');    
    $pdf->Cell(50,5,$tembusan5,0,1,'L');

//footer================================


if($tipe!='PE')
{
  $pdf->AddPage();
//========halaman baru

        $pdf->SetX(20);
        $pdf->Cell(40,5,"Lampiran Surat Keputusan Direksi:",0,1,'L');	   
        $pdf->Ln();
        $pdf->SetX(20);
    $pdf->Cell(20,5,"No",0,0,'L');
    $pdf->Cell(5,5,":",0,0,'L');	
        $pdf->Cell(40,5,$nosk,0,1,'L');

        $pdf->SetX(20);
    $pdf->Cell(20,5,$_SESSION['lang']['tanggal'],0,0,'L');
    $pdf->Cell(5,5,":",0,0,'L');		
        $pdf->Cell(40,5,$tanggal,0,1,'L');

        $pdf->SetFont('Arial','',10);	
        $pdf->Ln();	
        $pdf->SetX(20);		
        $pdf->Cell(40,5,$_SESSION['lang']['dari']." : ",0,1,'L');	
        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['gajipokok'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($darigaji==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($darigaji,2,',',','),0,1,'R');
        }
        

        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjjabatan'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($tjjabatan==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($tjjabatan,2,',',','),0,1,'R');
        }


        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjkebun'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($tjkebun==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($tjkebun,2,',',','),0,1,'R');
        }

        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjlokasi'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($tjlokasi==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($tjlokasi,2,',',','),0,1,'R');
        }

        $pdf->SetX(20);	
        $totaldari=$darigaji+$tjjabatan+$tjkebun+$tjlokasi;
        $pdf->Cell(40,5,$_SESSION['lang']['total'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($totaldari==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($totaldari,2,',',','),'T',1,'R');
        }
//================


        $pdf->Ln();	
        $pdf->SetX(20);		
        $pdf->Cell(40,5,$_SESSION['lang']['ke']." : ",0,1,'L');	

        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['gajipokok'],0,0,'L');
        $pdf->Cell(5,5," :Rp.",0,0,'L');	
        if ($kegaji==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($kegaji,2,',',','),0,1,'R');	
        }



        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjjabatan'],0,0,'L');
        $pdf->Cell(5,5," :Rp.",0,0,'L');	
        if ($ketjjabatan==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($ketjjabatan,2,',',','),0,1,'R');
        }

        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjkebun'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($ketjkebun==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($ketjkebun,2,',',','),0,1,'R');	
        }


        $pdf->SetX(20);	
        $pdf->Cell(40,5,$_SESSION['lang']['tjlokasi'],0,0,'L');
        $pdf->Cell(5,5," :Rp.",0,0,'L');	
        if ($ketjlokasi==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($ketjlokasi,2,',',','),0,1,'R');
        }

        $pdf->SetX(20);	
        $totalke=$kegaji+$ketjjabatan+$ketjkebun+$ketjlokasi;
        $pdf->Cell(40,5,$_SESSION['lang']['total'],0,0,'L');	
        $pdf->Cell(5,5," :Rp.",0,0,'L');
        if ($totalke==0){
            $pdf->Ln();
        } else {
            $pdf->Cell(40,5,number_format($totalke,2,',',','),'T',1,'R');
        }


//=========penandatangan
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();			
        $pdf->Ln();	
        $pdf->SetX(20);
        $pdf->Cell(40,5,($namajabatan==''?$_SESSION['lang']['direksi']:$namajabatan).",",0,1,'L');
        $pdf->Ln();
        $pdf->Ln();			
        $pdf->Ln();	
        $pdf->SetX(20);
        $pdf->Cell(40,5," ".$direksi." ",0,1,'L');
}	
        $pdf->Ln();		
        $pdf->Output();

?>

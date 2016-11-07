<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');
if(isset($_GET['proses'])=='cvData'){
    $_POST=$_GET;
}
$param=$_POST;

$optnmkar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$optNmorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optDept=makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optNmlowongan=makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');
$sppl="select distinct nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='TN'";
$qappl=mysql_query($sppl) or die(mysql_error($conn));
$rpremi=mysql_fetch_assoc($qappl); 
$kdKeg=$rpremi['nilai'];



switch($param['proses'])
{
    case'getData':
        $wher="";
        if($param['deptId']!=''){
            $wher=" and departemen='".$param['deptId']."'";
        }
	$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sprd="select distinct notransaksi,namalowongan,tanggal from ".$dbname.".sdm_permintaansdm 
               where stpersetujuanhrd=1 ".$wher." order by tanggal desc";
	$qprd=mysql_query($sprd) or die(mysql_error($conn));
	while($rprd=mysql_fetch_assoc($qprd)){
	$optPeriode.="<option value='".$rprd['notransaksi']."'>".$rprd['namalowongan'].",".$rprd['tanggal']."</option>";
	}
        echo $optPeriode;
    break;
    case'loadData':
        $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
               where lokasi='HRDJKRT'";			
        $qaks=mysql_query($saks) or die(mysql_error($conn));
        $jaks=mysql_fetch_assoc($qaks);
        $uname2=$jaks['username'];
        $passwd2=$jaks['password'];
        $dbserver2=$jaks['ip'];
        $dbport2=$jaks['port'];
        $dbdt=$jaks['dbname'];
      
        //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
        //$conn2=mysql_connect('192.168.1.204','root','dbdev');
        $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
        if (!$conn2)
          {
          die('Could not connect: ' . mysql_error());
          }
//        echo $conn2."__".$dbserver2."__".$uname2."__".$passwd2."\n";
//        echo $param['proses'];
        //@$connd=mysql_connect($host.":".$port,$usr,$pswrd) or die("Error/Gagal :Unable to Connect to database ".$host);
        $arrKond=array("0"=>">","1"=>"<");
        #get pelamar
        if($param['kondId']!=''){
            $whrd.="and (year(curdate())-year(tanggallahir)) ".$arrKond[$param['kondId']].$param['umrNa'];
        }
        //(year(curdate())-year(tanggallahir))
        $sdtPelamar="select distinct a.email from ".$dbdt.".sdm_apply_dt a  
                     left join ".$dbdt.".sdm_lowongan b on a.notransaksi=b.notransaksi
                     left join ".$dbdt.".datacalon c on a.email=c.email
                     where b.nopermintaan='".$param['nmLowongan']."' ".$whrd."";
        //exit("error:".$sdtPelamar);
        $qdtPelamar=mysql_query($sdtPelamar,$conn2) or die(mysql_error());
        $ert=mysql_num_rows($qdtPelamar);
        $dtEm=array();
        while($rdtPelamar=mysql_fetch_assoc($qdtPelamar)){
            $dtEm[$rdtPelamar['email']]=$rdtPelamar['email'];
        }
        if($ert==0){
            exit("error:Belum Ada Pelamar!!");
        }
	if(($param['periode']=='')||($param['nmLowongan']=='')||($param['deptId']=='')){
            exit("error: Semua Field Tidak Boleh Kosong");
	}
	echo"<input type=hidden id=nopermintaan value='".$param['nmLowongan']."' />";
	echo"<table cellpadding=2 cellspacing=1 border=0 class=sortable>
               <thead>
	       <tr class=rowheader>";
	echo"<td>".$_SESSION['lang']['email']."</td>";
	echo"<td>".$_SESSION['lang']['nama']."</td>";
//	echo"<td>".$_SESSION['lang']['telp']."</td>";
        echo"<td>".$_SESSION['lang']['nohp']."</td>";
	echo"<td>".$_SESSION['lang']['jbtndilamar']."</td>";
	echo"<td>".$_SESSION['lang']['action']."</td>";
	echo"</tr></thead><tbody id=listData>"; 
         foreach($dtEm as $lstEmail){
            $nor+=1;
            
            $drt="";
            $scek="select distinct * from ".$dbname.".sdm_testcalon 
                   where email='".$lstEmail."' and idpermintaan='".$param['nmLowongan']."'";
            //echo $scek;
            $qcek=mysql_query($scek,$conn) or die(mysql_error($conn));
            $rdeta=mysql_fetch_assoc($qcek);
            $scek2="select distinct * from ".$dbname.".sdm_testcalon 
                   where email='".$lstEmail."' and idpermintaan='".$param['nmLowongan']."'";
            $qcek2=mysql_query($scek2,$conn) or die(mysql_error($conn));
            $rcek=mysql_num_rows($qcek2);
            $drt="";
            if($rcek==1){
                $drt="checked";
            }
            $dser="";
            $bgdrt="";
            if($rdeta['hasilakhir']=='Hired'){
             $bgdrt="bgcolor=#00FF7F";
             $dser="disabled";
            }
            if($rdeta['hasilakhir']=='Hold'){
             $bgdrt="bgcolor=orange";
              $dser="disabled";
            }
            if($rdeta['hasilakhir']!='Fail'){
                $sdt="select distinct * from ".$dbdt.".datacalon where email='".$lstEmail."'";
                //echo $sdt;
                //exit("error:".$sdt);
                $qdt=mysql_query($sdt,$conn2) or die(mysql_error($connd));
                $rdt=mysql_fetch_assoc($qdt);
                echo"<tr class=rowcontent >";
                echo"<td  ".$bgdrt."  id=emailDt_".$nor.">".$rdt['email']."</td>";
                echo"<td  ".$bgdrt." >".$rdt['namacalon']."</td>";
                //            echo"<td>".$rdt['noteleponrumah']."</td>";
                echo"<td  ".$bgdrt." >".$rdt['nohp']."</td>";
                echo"<td  ".$bgdrt." >".$_POST['nbJbtn']."</td>";
                echo"<td  ".$bgdrt." >
                   <input type=checkbox id=pildt_".$nor." onclick=ricekDt('".$nor."') ".$drt." ".$dser." />
                   <button class=mybutton ".$dser." onclick=tolakDt('".$nor."')>".$_SESSION['lang']['tolak']."</button>
                   <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewCv('sdm_slave_pemanggilantest','".$rdt['email']."','printpdf');\">
                 </td>";
                echo"</tr>";
            } 
            
          }
	
	echo"</tbody></table>";
	 
	break;
        case'insrData':
            $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
            where lokasi='HRDJKRT'";			
            $qaks=mysql_query($saks) or die(mysql_error($conn));
            $jaks=mysql_fetch_assoc($qaks);
            $uname2=$jaks['username'];
            $passwd2=$jaks['password'];
            $dbserver2=$jaks['ip'];
            $dbport2=$jaks['port'];
            $dbdt=$jaks['dbname'];

            //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
            //$conn2=mysql_connect('192.168.1.204','root','dbdev');
            $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
            if (!$conn2)
            {
            die('Could not connect: ' . mysql_error());
            }
            $sntrans="select notransaksi from ".$dbdt.".sdm_lowongan where nopermintaan='".$param['nopermintaan']."'";
            $qntrans=mysql_query($sntrans,$conn2) or die(mysql_error());
            $rntrans=mysql_fetch_assoc($qntrans);
            $sdel="delete from ".$dbname.".sdm_testcalon where email='".$param['emailDt']."' and idpermintaan='".$param['nopermintaan']."'";
            if(mysql_query($sdel,$conn)){
                $degl="update ".$dbdt.".sdm_apply_dt set status=0 where email='".$param['emailDt']."'
                         and notransaksi='".$rntrans['notransaksi']."'";
                if(mysql_query($degl,$conn2)){
                    $sinsrt="insert into ".$dbname.".sdm_testcalon (`email`,`periodetest`,`idpermintaan`,`namalowongan`,`recruiter`) values
                             ('".$param['emailDt']."','".$param['periodetest']."','".$param['nopermintaan']."','".$optNmlowongan[$param['nopermintaan']]."','".$_SESSION['standard']['userid']."')";
                     if(!mysql_query($sinsrt,$conn)){
                         die(mysql_error($conn))."___".$sdel;
                     }else{
                         $supdate="update ".$dbdt.".sdm_apply_dt set status=2 where email='".$param['emailDt']."'
                             and notransaksi='".$rntrans['notransaksi']."'";
                         if(!mysql_query($supdate,$conn2)){
                             die(mysql_error($conn2))."___".$supdate;
                         }
                     }
                }else{
                     die(mysql_error($conn2))."___".$degl;
                }
            }else{
               die(mysql_error($conn))."___".$sdel;
            }
        break;
        case'insrData2':
            $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
            where lokasi='HRDJKRT'";	
            
            $qaks=mysql_query($saks) or die(mysql_error($conn));
            $jaks=mysql_fetch_assoc($qaks);
            $uname2=$jaks['username'];
            $passwd2=$jaks['password'];
            $dbserver2=$jaks['ip'];
            $dbport2=$jaks['port'];
            $dbdt=$jaks['dbname'];

            //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
            //$conn2=mysql_connect('192.168.1.204','root','dbdev');
             $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
            if (!$conn2)
            {
                die('Could not connect: ' . mysql_error());
            }
            $sntrans="select notransaksi from ".$dbdt.".sdm_lowongan where nopermintaan='".$param['nopermintaan']."'";
            $qntrans=mysql_query($sntrans,$conn2) or die(mysql_error());
            $rntrans=mysql_fetch_assoc($qntrans);
            $sdel="delete from ".$dbname.".sdm_testcalon where email='".$param['emailDt']."' and idpermintaan='".$param['nopermintaan']."'";
            if(mysql_query($sdel,$conn)){
                $degl="update ".$dbdt.".sdm_apply_dt set status=3 where email='".$param['emailDt']."'
                         and notransaksi='".$rntrans['notransaksi']."'";
                if(mysql_query($degl,$conn2)){
                    $sinsrt="insert into ".$dbname.".sdm_testcalon (`email`,`periodetest`,`idpermintaan`,`namalowongan`,`recruiter`,`hasilakhir`) values
                             ('".$param['emailDt']."','".$param['periodetest']."','".$param['nopermintaan']."','".$optNmlowongan[$param['nopermintaan']]."','".$_SESSION['standard']['userid']."','Fail')";
                     if(!mysql_query($sinsrt,$conn)){
                         die(mysql_error($conn))."___".$sdel;
                     }    
                }else{
                     die(mysql_error($conn2))."___".$degl;
                }
            }else{
               die(mysql_error($conn))."___".$sdel;
            }
        break;
        case'delData':
             $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
            where lokasi='HRDJKRT'";			
            $qaks=mysql_query($saks) or die(mysql_error($conn));
            $jaks=mysql_fetch_assoc($qaks);
            $uname2=$jaks['username'];
            $passwd2=$jaks['password'];
            $dbserver2=$jaks['ip'];
            $dbport2=$jaks['port'];
            $dbdt=$jaks['dbname'];

            //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
            //$conn2=mysql_connect('192.168.1.204','root','dbdev');
            $conn2=mysql_connect('192.168.1.209','root','recruitment!001');
            if (!$conn2)
            {
                die('Could not connect: ' . mysql_error());
            }
            $snopermintaan="select distinct notransaksi from ".$dbdt.".sdm_lowongan where nopermintaan='".$param['nopermintaan']."'";
            $qnopermintaan=mysql_query($snopermintaan,$conn2) or die(mysql_error($conn2));
            $rnopermintaan=mysql_fetch_assoc($qnopermintaan);
            
            $sdel="delete from ".$dbname.".sdm_testcalon where email='".$param['emailDt']."' and idpermintaan='".$param['nopermintaan']."'";
            if(!mysql_query($sdel,$conn)){
               die(mysql_error($conn))."___".$sdel;
            }else{
                $degl="update ".$dbdt.".sdm_apply_dt set status=0 where email='".$param['emailDt']."'
                         and notransaksi='".$rnopermintaan['notransaksi']."'";
                if(!mysql_query($degl,$conn2)){
                     die(mysql_error($conn2))."___".$degl;
                }
            }
        break;
        case'cvData':
             $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
            where lokasi='HRDJKRT'";			
            $qaks=mysql_query($saks) or die(mysql_error($conn));
            $jaks=mysql_fetch_assoc($qaks);
            $uname2=$jaks['username'];
            $passwd2=$jaks['password'];
            $dbserver2=$jaks['ip'];
            $dbport2=$jaks['port'];
            $dbdt=$jaks['dbname'];

            //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
            //$conn2=mysql_connect('192.168.1.204','root','dbdev');
            $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
            if (!$conn2)
            {
                die('Could not connect: ' . mysql_error());
            }
//create Header
class PDF extends FPDF
{
	function Header()
	{
	//if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	  //  $this->Image($path,15,2,40);	
             global $param;
             global $dbserver2;
		$this->SetFont('Arial','B',15);
		$this->SetFillColor(255,255,255);	
		$this->SetY(12);   
	        $this->Cell(60,5,strtoupper('curriculum vitae'),0,1,'L');	 
		$this->SetFont('Arial','',10);
	        $this->Cell(190,5,$param['emailDt'],0,1,'L');
		$this->SetFont('Arial','',6); 
		$this->SetY(30);
		$this->SetX(163);
                $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,32,200,32);	   
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}
$str="select *,
      case jeniskelamin when 'L' then 'Laki-Laki'
	  else  'Wanita'
	  end as jk
	  from ".$dbdt.".datacalon where email='".$param['emailDt']."' limit 1";
//exit("error:".$str);
$res=mysql_query($str,$conn2);
$defaulsrc='images/user.jpg';
while($bar=mysql_fetch_object($res))
{
	//get pendidikan
	 $pendidikan='';
	  
	//Tipe karyawan
	$tipekaryawan='';
	 

	//jabatan
	$jabatan='';
	$isid==$dbserver2."/".$bar->photo;
	$photo=($bar->photo==''?$defaulsrc:$isid);
         
	 
	$nama=$bar->namacalon;
	$ttlahir=$bar->tempatlahir;
	$tgllahir=tanggalnormal($bar->tanggallahir);
	$wn=$bar->warganegara;
	$jk=$bar->jk;
	$stpkw=$bar->statusperkawinan;
	$tglmenikah=tanggalnormal($bar->tanggalmenikah);
	$agama=$bar->agama;
	$goldar=$bar->golongandarah;
	$pendidikan=$pendidikan;
	$telprumah=$bar->noteleponrumah;
	$hp=$bar->nohp;
	$passpor=$bar->nopaspor;
	$ktp=$bar->noktp;
	$tdarurat=$bar->notelepondarurat;
	 
	 
	$alamataktif=$bar->alamataktif;
	$kota=$bar->kota;
	$provinsi=$bar->provinsi;
	$kodepos=$bar->kodepos;	
	$rekbank=$bar->norekeningbank;
	$bank=$bar->namabank;
	$sisgaji=$bar->sistemgaji;
	$jlhanak=$bar->jumlahanak;
	$tanggungan=$bar->jumlahtanggungan;
	$stpjk=$bar->statuspajak;
	$npwp=$bar->npwp;
	$lokpenerimaan=$bar->lokasipenerimaan;
	$kodeorg=$bar->kodeorganisasi;
	$bagian=$bar->bagian;
	$jabatan=$jabatan;
	$golongan=$bar->kodegolongan;
	$kualifikasi=$bar->kualifikasi;
	$email=$bar->email;
         
//=============pdf

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);
	$pdf->setY(32);		
	$pdf->Cell(25,5,'1. '.strtoupper('Data Pribadi'),0,1,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->setY(40);		
	$pdf->SetX(20);
	$pdf->Image($photo,15,40,35);
 	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,"Nama Lengkap",0,0,'L');
		$pdf->Cell(40,5,': '.$nama,0,0,'L');	
    $pdf->Cell(25,5,'Tempat Lahir',0,0,'L');
		$pdf->Cell(40,5,': '.$ttlahir,0,1,'L');
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'Tanggal Lahir',0,0,'L');
		$pdf->Cell(40,5,': '.$tgllahir,0,0,'L');	
    $pdf->Cell(25,5,'Warga Negara',0,0,'L');
		$pdf->Cell(40,5,': '.$wn,0,1,'L');				
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'Jenis Kelamin',0,0,'L');
		$pdf->Cell(40,5,': '.$jk,0,0,'L');	
    $pdf->Cell(25,5,'Status',0,0,'L');
		$pdf->Cell(40,5,': '.$stpkw,0,1,'L');	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'Tanggal Menikah',0,0,'L');
		$pdf->Cell(40,5,': '.$tglmenikah,0,0,'L');	
    $pdf->Cell(25,5,'Agama',0,0,'L');
		$pdf->Cell(40,5,': '.$agama,0,1,'L');
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'Golongan Darah',0,0,'L');
		$pdf->Cell(40,5,': '.$goldar,0,0,'L');	
    $pdf->Cell(25,5,'Pendidikan',0,0,'L');
		$pdf->Cell(40,5,': '.$pendidikan,0,1,'L');		
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'Telp',0,0,'L');
		$pdf->Cell(40,5,': '.$telprumah,0,0,'L');	
    $pdf->Cell(25,5,'No HP',0,0,'L');
		$pdf->Cell(40,5,': '.$hp,0,1,'L');		
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'No. KTP',0,0,'L');
		$pdf->Cell(40,5,': '.$ktp,0,0,'L');	
    $pdf->Cell(25,5,'No.Paspor',0,0,'L');
		$pdf->Cell(40,5,': '.$passpor,0,1,'L');	
	$pdf->SetX(60);		
    $pdf->Cell(25,5,'No.Telepon Darurat',0,0,'L');
		$pdf->Cell(40,5,': '.$tdarurat,0,0,'L');	
 
    $pdf->Cell(25,5,'Jumlah Anak',0,0,'L');
		$pdf->Cell(40,5,': '.$jlhanak,0,1,'L');

    $pdf->Cell(32,5,'Jumlah Tanggungan',0,0,'L');
		$pdf->Cell(83,5,': '.$tanggungan,0,0,'L');	
  
    $pdf->Cell(25,5,'NPWP',0,0,'L');
		$pdf->Cell(40,5,': '.$npwp,0,1,'L');
    $pdf->Cell(32,5,'Kualifikasi',0,0,'L');
		$pdf->MultiCell(153,5,': '.$kualifikasi,0,'J');
    $ad=$pdf->GetY();
    $er=$pdf->GetX();
    $pdf->SetY($ad);
    $pdf->SetX($er);
    $pdf->Cell(25,5,'Email',0,0,'L');
		$pdf->Cell(40,5,': '.$email,0,0,'L');
		
   	
   
    $pdf->Ln(10);	
}
//=======================Riwayat Pekerjaan
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'2. '.strtoupper("Pengalaman Kerja"),0,1,'L');
	$pdf->SetFont('Arial','',7);												
    
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(6,4,'No',1,0,'L',1);
    $pdf->Cell(30,4,'Nama Organisasi',1,0,'C',1);
    $pdf->Cell(30,4,'Bidang Usaha',1,0,'C',1);	
    $pdf->Cell(15,4,'Bulan Masuk',1,0,'C',1);		
    $pdf->Cell(15,4,'Bulan Keluar',1,0,'C',1);
    $pdf->Cell(20,4,'Jabatan Terakhir',1,0,'C',1);	
    $pdf->Cell(20,4,'Bagian',1,0,'C',1);			
    $pdf->Cell(15,4,'Masa Kerja',1,0,'C',1);
    $pdf->Cell(35,4,'Alamat',1,1,'C',1);
//loop isinya
	 $str="select *,right(bulanmasuk,4) as masup,left(bulanmasuk,2) as busup from ".$dbdt.".pengalaman 
               where email='".$param['emailDt']."' order by masup,busup";
	 $res=mysql_query($str,$conn2);
	 $no=0;
	 $mskerja=0;
	 while($bar=mysql_fetch_object($res))
	 {
		 $no+=1;	
		  $msk=mktime(0,0,0,substr(str_replace("-","",$bar->bulanmasuk),0,2),1,substr($bar->bulanmasuk,3,4));	
		  $klr=mktime(0,0,0,substr(str_replace("-","",$bar->bulankeluar),0,2),1,substr($bar->bulankeluar,3,4));	
		  $dateDiff = $klr - $msk;
	      $mskerja = floor($dateDiff/(60*60*24))/365; 

	    $pdf->Cell(6,4,$no,1,0,'L',0);
	    $pdf->Cell(30,4,$bar->namaperusahaan,1,0,'L',0);
	    $pdf->Cell(30,4,$bar->bidangusaha,1,0,'L',0);	
	    $pdf->Cell(15,4,$bar->bulanmasuk,1,0,'L',0);		
	    $pdf->Cell(15,4,$bar->bulankeluar,1,0,'L',0);
	    $pdf->Cell(20,4,$bar->jabatan,1,0,'L',0);	
	    $pdf->Cell(20,4,$bar->bagian,1,0,'L',0);			
	    $pdf->Cell(15,4,number_format($mskerja,2,',','.')." Yrs",1,0,'R',0);
	    $pdf->Cell(35,4,$bar->alamatperusahaan,1,1,'L',0);
	 }	

//=======================Riwayat Pendidikan
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'3. '.strtoupper('Pendidikan'),0,1,'L');
	$pdf->SetFont('Arial','',7);												
    
    $pdf->Cell(6,4,'No',1,0,'L',1);
    $pdf->Cell(12,4,'Jenjang',1,0,'C',1);
    $pdf->Cell(33,4,'Nama Sekolah',1,0,'C',1);	
    $pdf->Cell(25,4,'Kota',1,0,'C',1);		
    $pdf->Cell(30,4,'Jurusan',1,0,'C',1);
    $pdf->Cell(10,4,'Tahun Lulus',1,0,'C',1);	
    $pdf->Cell(25,4,'Gelar',1,0,'C',1);			
    $pdf->Cell(10,4,'Nilai',1,0,'C',1);
    $pdf->Cell(35,4,'Keterangan',1,1,'C',1);
//loop isinya
	 $str="select a.*,b.kelompok from ".$dbdt.".pendidikan a,".$dbdt.".sdm_5pendidikan b
	 		where a.email='".$param['emailDt']."' 
	 		and a.levelpendidikan=b.levelpendidikan
			order by a.levelpendidikan desc";
	 $res=mysql_query($str,$conn2);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
		 $no+=1;	
	     $pdf->Cell(6,4,$no,1,0,'L',0);
	     $pdf->Cell(12,4,$bar->kelompok,1,0,'L',0);
	     $pdf->Cell(33,4,$bar->namasekolah,1,0,'L',0);	
	     $pdf->Cell(25,4,$bar->kota,1,0,'L',0);		
	     $pdf->Cell(30,4,$bar->spesialisasi,1,0,'L',0);
	     $pdf->Cell(10,4,$bar->tahunlulus,1,0,'L',0);	
	     $pdf->Cell(25,4,$bar->gelar,1,0,'L',0);			
	     $pdf->Cell(10,4,number_format($bar->nilai,2,',','.'),1,0,'R',0);
	     $pdf->Cell(35,4,$bar->keterangan,1,1,'L',0);
	 }	
//=======================Riwayat Kursus
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'4. '.strtoupper('Kursus'),0,1,'L');
	$pdf->SetFont('Arial','',7);												
    
    $pdf->Cell(6,4,'No',1,0,'L',1);
    $pdf->Cell(30,4,'Jenis Kursus',1,0,'C',1);
    $pdf->Cell(50,4,'Judul',1,0,'C',1);	
    $pdf->Cell(50,4,'Penyelenggara',1,0,'C',1);		
    $pdf->Cell(15,4,'Tgl. Mulai',1,0,'C',1);
    $pdf->Cell(15,4,'Sampai Tanggal',1,0,'C',1);	
    $pdf->Cell(20,4,'Sertifikat',1,1,'C',1);			
//loop isinya
	 $str="select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat 
	       from ".$dbdt.".sdm_karyawantraining
	 		where email='".$param['emailDt']."'
			order by bulanmulai desc";	
	 $res=mysql_query($str);
	 $no=0;
	 while($bar=mysql_fetch_object($res))
	 {
	 $no+=1;	
	     $pdf->Cell(6,4,$no,1,0,'L',0);
	     $pdf->Cell(30,4,$bar->jenistraining,1,0,'L',0);
	     $pdf->Cell(50,4,$bar->judultraining,1,0,'L',0);	
	     $pdf->Cell(50,4,$bar->penyelenggara,1,0,'L',0);		
	     $pdf->Cell(15,4,$bar->bulanmulai,1,0,'L',0);
	     $pdf->Cell(15,4,$bar->bulanselesai,1,0,'L',0);	
	     $pdf->Cell(20,4,$bar->bersertifikat,1,1,'L',0);			
	 }	

//=======================Keluarga
    $pdf->Ln();
	$pdf->SetFont('Arial','B',10);		
	$pdf->Cell(25,5,'5. '.strtoupper('Keluarga'),0,1,'L');
	$pdf->SetFont('Arial','',7);												
    
    $pdf->Cell(6,4,'No',1,0,'L',1);
    $pdf->Cell(40,4,'Nama',1,0,'C',1);
    $pdf->Cell(15,4,'Jenis Kelamin',1,0,'C',1);	
    $pdf->Cell(20,4,'Hubungan',1,0,'C',1);		
    $pdf->Cell(15,4,'Status Perkawinan',1,0,'C',1);	
    $pdf->Cell(20,4,'Jenjang',1,0,'C',1);
	$pdf->Cell(30,4,'Pekerjaan',1,0,'C',1);
	$pdf->Cell(20,4,'Umur',1,0,'C',1);	
	$pdf->Cell(20,4,'Tanggungan',1,1,'C',1);	
//loop isinya
		 $str="select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, 
		       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur
			   from ".$dbdt.".keluarga a,".$dbdt.".sdm_5pendidikan b
		 		where a.email='".$param['emailDt']."'
				and a.levelpendidikan=b.levelpendidikan
				order by hubungankeluarga";	
		 $res=mysql_query($str,$conn2);
		 $no=0;
		 while($bar=mysql_fetch_object($res))
		 {
		 $no+=1;	
	     $pdf->Cell(6,4,$no,1,0,'L',0);
	     $pdf->Cell(40,4,$bar->nama,1,0,'L',0);
	     $pdf->Cell(15,4,$bar->jeniskelamin,1,0,'L',0);	
	     $pdf->Cell(20,4,$bar->hubungankeluarga,1,0,'L',0);		
	     $pdf->Cell(15,4,$bar->status,1,0,'L',0);
	     $pdf->Cell(20,4,$bar->kelompok,1,0,'L',0);	
	     $pdf->Cell(30,4,$bar->pekerjaan,1,0,'L',0);
		 $pdf->Cell(20,4,$bar->umur."Th",1,0,'L',0);	
		 $pdf->Cell(20,4,$bar->tanggungan1,1,1,'L',0);			
	 }					
	$pdf->Output();	
    break;
}
?>
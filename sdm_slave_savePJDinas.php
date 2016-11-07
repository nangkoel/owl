<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$karyawanid=$_POST['karyawanid'];
$kodeorg=$_POST['kodeorg'];
$persetujuan=$_POST['persetujuan'];
$persetujuan2=$_POST['persetujuan2'];
$hrd=$_POST['hrd']; 
$tujuan3=$_POST['tujuan3'];
$tujuan2=$_POST['tujuan2'];	
$tujuan1=$_POST['tujuan1'];
$tanggalperjalanan=tanggalsystem($_POST['tanggalperjalanan']);
$tanggalkembali=tanggalsystem($_POST['tanggalkembali']);
$uangmuka=$_POST['uangmuka'];
$tugas1=$_POST['tugas1'];
$tugas2=$_POST['tugas2'];
$tugas3=$_POST['tugas3'];
$tujuanlain=$_POST['tujuanlain'];
$tugaslain=$_POST['tugaslain'];
$pesawat=$_POST['pesawat'];
$darat=$_POST['darat'];
$laut=$_POST['laut'];
$mess=$_POST['mess'];
$hotel=$_POST['hotel'];
$mobilsewa=$_POST['mobilsewa'];		
$method=$_POST['method'];

$ket=$_POST['ket'];


if($persetujuan=='')
 $persetujuan='0000000000';

if($uangmuka=='')
  $uangmuka=0;

if($method=='insert')
{
//get number
$potSK=substr($_SESSION['empl']['lokasitugas'],0,4).date('Y');
$str="select notransaksi from ".$dbname.".sdm_pjdinasht
      where  notransaksi like '".$potSK."%'
	  order by notransaksi desc limit 1";
 
$notrx=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$notrx=substr($bar->notransaksi,10,5);
}
$notrx=intval($notrx);
$notrx=$notrx+1;
$notrx=str_pad($notrx, 5, "0", STR_PAD_LEFT);
$notrx=$potSK.$notrx;
 

 
	$str="insert into ".$dbname.".sdm_pjdinasht (
		  `notransaksi`,`karyawanid`,`tanggalbuat`,
		  `tanggalperjalanan`,`kodeorg`,`tujuan1`,
		  `tugas1`,`tujuan2`,`tugas2`,`tujuan3`,
		  `tugas3`,`tugaslain`,`tujuanlain`,
		  `pesawat`,`darat`,`laut`,
		  `mess`,`hotel`,`mobilsewa`,`tanggalkembali`,`uangmuka`,
		  `hrd`,`persetujuan`,`persetujuan2`,`keterangan`
		  ) values(
				'".$notrx."',".$karyawanid.",".date('Ymd').",
				".$tanggalperjalanan.",'".$kodeorg."','".$tujuan1."',
				'".$tugas1."','".$tujuan2."','".$tugas2."','".$tujuan3."',
				'".$tugas3."','".$tugaslain."','".$tujuanlain."',
				".$pesawat.",".$darat.",".$laut.",
				".$mess.",".$hotel.",".$mobilsewa.",".$tanggalkembali.",".$uangmuka.",
				".$hrd.",".$persetujuan.",".$persetujuan2." ,'".$ket."'
		  )";
		  
		 
        
}
else if($method=='delete')
{
  $notransaksi=$_POST['notransaksi'];
	$str="delete from ".$dbname.".sdm_pjdinasht
	      where karyawanid=".$karyawanid." and notransaksi='".$notransaksi."'"; 
}
else if($method=='update')
{
  $notransaksi=$_POST['notransaksi'];
	$str="update ".$dbname.".sdm_pjdinasht set
		  `tanggalperjalanan`=".$tanggalperjalanan.",
		  `kodeorg`='".$kodeorg."',
		  `tujuan1`='".$tujuan1."',
		  `tugas1`='".$tugas1."',
		  `tujuan2`='".$tujuan2."',
		  `tugas2`='".$tugas2."',
		  `tujuan3`='".$tujuan3."',
		  `tugas3`='".$tugas3."',
		  `tugaslain`='".$tugaslain."',
		  `tujuanlain`='".$tujuanlain."',
		  `pesawat`=".$pesawat.",
		  `darat`=".$darat.",
		  `laut`=".$laut.",
		  `mess`=".$mess.",
		  `hotel`=".$hotel.",
		  mobilsewa=".$mobilsewa.",
		  `tanggalkembali`=".$tanggalkembali.",
		  `uangmuka`=".$uangmuka.",
		  `hrd`=".$hrd.",
		  `persetujuan`=".$persetujuan.", `persetujuan2`=".$persetujuan2.",
		  `keterangan`='".$ket."'
		where karyawanid=".$karyawanid." and notransaksi='".$notransaksi."'"; 	  	
}
//exit("Error:$str");
if(mysql_query($str))
{
    if($method=='update' or $method=='insert')
    {
        $to=getUserEmail($persetujuan.",".$hrd);
        $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
        $subject="[Notifikasi]Persetujuan Perjalanan Dinas a/n ".$namakaryawan;
        $body="<html>
                 <head>
                 <body>
                   <dd>Dengan Hormat,</dd><br>
                   <br>
                   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan surat perjalanan dinas
                   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                   <br>
                   <br>
                   <br>
                   Regards,<br>
                   Owl-Plantation System.
                 </body>
                 </head>
               </html>
               ";
        $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
    }
}
else
   echo " Gagal:".addslashes(mysql_error($conn));


?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$keuser=$_POST['keuser'];
$notransaksi=$_POST['notransaksi'];
$kolom=$_POST['kolom'];
$kolomstatus='status'.$kolom;

$str=" update ".$dbname.".sdm_pjdinasht set ".$kolom."=".$keuser."
       where notransaksi='".$notransaksi."' and ".$kolomstatus."=0";
if(mysql_query($str))
{
        $to=getUserEmail($keuser);
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
else
{
	echo " Gagal,".addslashes(mysql_error($conn));
}	   
?>
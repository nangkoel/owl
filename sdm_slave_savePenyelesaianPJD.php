<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi=$_POST['notransaksi'];
$tanggal=date('Ymd');
$sisa=$_POST['sisa'];
$bytiket=$_POST['bytiket'];
if($sisa=='')
  $sisa=0;


	$str="update ".$dbname.".sdm_pjdinasht
	       set sisa=".$sisa.",
                            bytiket=".$bytiket.",
                            tanggalsisa=".$tanggal.",
                            lunas=1
	      where notransaksi='".$notransaksi."'"; 
	if(mysql_query($str))
		{}
	else
   		{
		 echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
		}
?>
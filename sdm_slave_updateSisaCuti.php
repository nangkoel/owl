<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kodeorg=$_POST['kodeorg'];
$karyawanid=$_POST['karyawanid'];
$periode=$_POST['periode'];
$sisa=$_POST['sisa'];


$str="update ".$dbname.".sdm_cutiht 
      set sisa=".$sisa."
     where 
      kodeorg='".$kodeorg."'
	  and karyawanid=".$karyawanid."
	  and periodecuti='".$periode."'";		      
mysql_query($str);
?>
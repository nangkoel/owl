<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$karyawanid		=$_POST['karyawanid'];
$pt=$_POST['pt'];
$lokasitugas=substr($_POST['lokasitugas'],0,4);
$str="update ".$dbname.".datakaryawan set kodeorganisasi='".$pt."', subbagian='',
      lokasitugas='".$lokasitugas."' where karyawanid=".$karyawanid;
//exit("Error:$str");	  
	  mysql_query($str);	  
if(mysql_affected_rows($conn)==1)
{
}	
else
{
	echo " Gagal:";
}  
?>
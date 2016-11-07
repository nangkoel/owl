<?php
require_once('master_validation.php');
require_once('config/connection.php');
$uname=trim($_POST['uname']);

$str="delete from ".$dbname.".auth
	        where namauser='".$uname."'";
  
if(mysql_query($str))
{
	
}
else
{
	echo " Gagal,".addslashes(mysql_error($conn));
}
?>

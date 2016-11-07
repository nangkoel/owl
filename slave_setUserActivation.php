<?php
require_once('master_validation.php');
require_once('config/connection.php');
$uname=$_POST['uname'];
$setstatus=$_POST['setstatus'];
	$str="update ".$dbname.".user
	      set status=".$setstatus."
		  where namauser='".$uname."'";

   if(mysql_query($str))
   {}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

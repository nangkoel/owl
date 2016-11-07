<?php
require_once('master_validation.php');
require_once('config/connection.php');
$level=trim($_POST['level']);
$id=$_POST['id'];
	$str="update ".$dbname.".menu
	      set access_level=".$level." 
		  where id=".$id;

   if(mysql_query($str))
   {}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

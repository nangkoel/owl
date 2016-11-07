<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$periode		=str_replace("-","",$_POST['val']);
	$org        	=strtoupper(trim($_POST['org']));
    $str2="update ".$dbname.".org set active_period='".$periode."',
	       lastuser='".$_SESSION['standard']['username']."'
          where code='".$org."'";
   if(mysql_query($str2)){}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=$_POST['id'];
$caption=$_POST['caption'];
$action=$_POST['action'];
	$str="update ".$dbname.".menu set action='".$action."',caption='".$caption."'
	       where id=".$id;
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

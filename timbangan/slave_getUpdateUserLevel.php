<?php
require_once('master_validation.php');
require_once('config/connection.php');

$newlevel=$_POST['newlevel'];
$uname=$_POST['un'];
	$str="update ".$dbname.".user set access_level=".$newlevel.",
	      lastuser='".$_SESSION['standard']['username']."',
		  lastupdate='".date('Y-m-d H:i:s')."'
	       where uname='".$uname."'";
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

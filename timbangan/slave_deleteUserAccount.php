<?php
require_once('master_validation.php');
require_once('config/connection.php');
$uname=$_POST['uname'];
$userid=$_POST['userid'];
	$str="delete from ".$dbname.".user
		  where uname='".$uname."'";

   if(mysql_query($str))
   {
		if($userid!=0)//if user is not additional and account has been created
		{
		  $str1='update '.$dbname.'.user_empl set account=2 where userid='.$userid;
		  mysql_query($str1);
		}
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

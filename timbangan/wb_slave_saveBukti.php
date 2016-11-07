<?php
require_once('master_validation.php');
require_once('config/connection.php');

$tiket	=$_POST['tiket'];
$nosegel=$_POST['nosegel'];
$air	=$_POST['air'];
$kotoran=$_POST['kotoran'];
$ffa	=$_POST['ffa'];
$nobuku	=$_POST['nobuku'];
$kapabrik=$_POST['kapabrik'];
$manager=$_POST['manager'];
$bongkar=$_POST['bongkar'];

  //delete first
  $str="delete from ".$dbname.".wb_bukti where nowb='".$tiket."'";
  mysql_query($str);
  
  //then insert
  $str="insert into ".$dbname.".wb_bukti
         (nowb,nosegel,air,kotoran,ffa,nobuku,kapabrik,manager,pbongkar)
		 values('".$tiket."','".$nosegel."',".$air.",".$kotoran.",".$ffa.",'".$nobuku."','".$kapabrik."','".$manager."','".$bongkar."')";
   if(mysql_query($str))
   {}
	else
	{
		echo " Error,".addslashes(mysql_error($conn));
	}		 
 
?>

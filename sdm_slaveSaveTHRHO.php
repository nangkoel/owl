<?php
require_once('master_validation.php');
require_once('config/connection.php');

  $periode= $_SESSION['thrperiode'];
  $userid=$_POST['userid'];
  $val  =$_POST['val'];
  $terbilang=$_POST['terbilang'];
   
   $str="delete from ".$dbname.".sdm_ho_detailmonthly where karyawanid=".$userid." 
         and periode='".$periode."' and type='thr'";
   mysql_query($str);
   $str="insert into ".$dbname.".sdm_ho_detailmonthly
   (karyawanid,component,value,periode,plus,updatedby,type)
   value(".$userid.",1,".$val.",'".$periode."',1,'".$_SESSION['standard']['username']."','thr')";	 
	if(mysql_query($str))
	{
		$str1="delete from ".$dbname.".sdm_ho_payrollterbilang where periode='".$periode."'
		      and userid=".$userid." and type='thr'";
		mysql_query($str1);
		$str2="insert into ".$dbname.".sdm_ho_payrollterbilang (userid,periode,terbilang,type)
		       values(".$userid.",'".$periode."','".$terbilang."','thr')";
		mysql_query($str2);	   	  
	}
	else
	{
		echo " Error: ".addslashes(mysql_error($conn)).$str;
	} 	
?>

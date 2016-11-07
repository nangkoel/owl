<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');


  $userid=$_POST['userid'];
  $nama  =$_POST['nama'];
  $mstatus=$_POST['mstatus'];
  $start =$_POST['start'];
  $resign=$_POST['resign'];
  $npwp =$_POST['npwp'];
	
   $stk="select * from ".$dbname.".sdm_ho_employee where karyawanid=".$userid;
   if(mysql_num_rows(mysql_query($stk))<1)
   {
		$str="insert into ".$dbname.".sdm_ho_employee (karyawanid,startdate,enddate,name,taxstatus,npwp)
		      values(".$userid.",'".tanggalsystem($start)."','".tanggalsystem($resign)."','".$nama."','".$mstatus."','".$npwp."')";		  	
		if(mysql_query($str))
		{}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 
   }
   else
   {
   $stra="update ".$dbname.".sdm_ho_employee set
	        startdate='".tanggalsystem($start)."',
			enddate='".tanggalsystem($resign)."',
			name='".$nama."',
			taxstatus='".$mstatus."',
			npwp='".$npwp."'
			where karyawanid=".$userid;
		if(mysql_query($stra))
		{}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
   }
?>

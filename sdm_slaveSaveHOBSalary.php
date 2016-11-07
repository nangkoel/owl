<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $userid=$_POST['userid'];
  $component  =$_POST['component'];
  $value=$_POST['value'];

   $stra="select * from ".$dbname.".sdm_ho_basicsalary where 
         karyawanid=".$userid." and component=".$component;
   $res=mysql_query($stra);		 
		if(mysql_num_rows($res)>0)
		{
		 //update
		 $str="update ".$dbname.".sdm_ho_basicsalary
		       set value=".$value.",updateby='".$_SESSION['standard']['username']."'
			   where karyawanid=".$userid."
			   and component=".$component;
		}
		else
		{
		 //insert
		 $str="insert into ".$dbname.".sdm_ho_basicsalary (karyawanid,component,value,updateby)
		       values(".$userid.",".$component.",".$value.",'".$_SESSION['standard']['username']."')";	
		}
		if(mysql_query($str))
		{}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
?>

<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $userid=$_POST['userid'];
  $operator  =$_POST['operator'];

   $stra="update ".$dbname.".sdm_ho_employee set
			operator='".$operator."'
			where karyawanid=".$userid;
		if(mysql_query($stra))
		{}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
?>

<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $perusahaan=$_POST['perusahaan'];
  $karyawan =$_POST['karyawan'];
  $pphjms =$_POST['pphjms'];
   $stra="update ".$dbname.".sdm_ho_hr_jms_porsi
			set value=".$perusahaan." where id='perusahaan'";
		if(mysql_query($stra,$conn))
		{		
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
	$stra="update ".$dbname.".sdm_ho_hr_jms_porsi
			set value=".$karyawan." where id='karyawan'";
		if(mysql_query($stra,$conn))
		{		
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 			
	$stra="update ".$dbname.".sdm_ho_hr_jms_porsi
			set value=".$pphjms." where id='pph21'";
		if(mysql_query($stra,$conn))
		{		
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 
?>

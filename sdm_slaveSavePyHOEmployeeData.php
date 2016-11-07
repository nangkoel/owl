<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

  $userid=$_POST['userid'];
  $bank  =$_POST['bank'];
  $bankac=$_POST['bankac'];
  $jms =$_POST['jms'];
  $firstperiod=$_POST['firstperiod'];
  $firstvol =$_POST['firstvol'];
  $lastperiod=$_POST['lastperiod'];
  $lastvol =$_POST['lastvol'];
  $jmsperiod=$_POST['jmsperiod'];
  
   $stra="update ".$dbname.".sdm_ho_employee set
	        bank='".$bank."',
			bankaccount='".$bankac."',
			nojms='".$jms."',
			firstpayment='".$firstperiod."',
			firstvol=".$firstvol.",
			lastpayment='".$lastperiod."',
			lastvol=".$lastvol.",
			jmsstart='".$jmsperiod."'			
			where karyawanid=".$userid;
		if(mysql_query($stra))
		{}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
?>

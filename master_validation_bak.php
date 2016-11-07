<?php
session_cache_expire(25);//25 minutes cache keep by browser
session_start();
//check for liftime session allowed++++++++++++++++++++++
if(time()>intval($_SESSION['DIE']))
{
	echo " [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!";
	session_destroy();
	#echo "<script>alert('Session expired. You'll be redirect to login page');location.reload(true)</script>";
	exit();
}
else
$_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
//++++++++++++++++++++++++++++++++++++++++++++++++++++
if(isset($_SESSION['standard']['username']) AND isset($_SESSION['access_type']))
{
	if(strlen($_SESSION['standard']['username'])>=6 AND ($_SESSION['access_type']=='level' OR $_SESSION['access_type']=='detail'))
	{//Go on
	//print_r($_SESSION);
	}
	else
	exit('Sorry, You entering the system like cracker');
}
else  
   {
   	if($_SESSION['security']=='on')
	   {
	    exit('Not Authorized');
		//echo"<pre>";
		//print_r($_SESSION);//exit('Not Authorized');
		//echo"</pre>";
	   }
	else
	{//doing nothing. Just pass away
	}   
   }
if(!isset($_SESSION['org']['holding'])){
 	echo " [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!";
	session_destroy();
	#echo "<script>alert('Session expired. You'll be redirect to login page');location.reload(true)</script>";
	exit();   
}  
?>

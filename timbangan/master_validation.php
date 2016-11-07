<?php
session_cache_expire(30);//15 minutes cache keep by browser
if(!isset($_SESSION))
{
session_start();
}
//session_start();
//check for liftime session allowed++++++++++++++++++++++
if(time()>intval($_SESSION['DIE']))
{
	echo " [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!";
	session_destroy();
	exit();
}
else
$_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
//++++++++++++++++++++++++++++++++++++++++++++++++++++
if(isset($_SESSION['standard']['username']) AND isset($_SESSION['access_type']))
{
	if(strlen($_SESSION['standard']['username'])>=5 AND ($_SESSION['access_type']=='level' OR $_SESSION['access_type']=='detail'))
	{//Go on
	//print_r($_SESSION);
	}
	else
	exit('Sorry, You entering the system like cracker');
}
else
   {
   	if($_SESSION['security']=='on')
	   exit('Not Authorized');
	else
	{//doing nothing. Just pass away
	}
   }

?>

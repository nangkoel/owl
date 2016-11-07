<?php
session_cache_expire(25);//25 minutes cache keep by browser
session_start();

if(isset($_POST['par'])){
    $bb= explode("/",$_POST['par']);
}else
if(isset($_GET['par'])){
    $bb= explode("/",$_GET['par']);
}

// if(count($_POST)>1 or count($_GET)>1){
  // if(!isset($_POST['par']) or !isset($_POST['par'])){
    // #Jika ada post dan ada get tetapi par tidak ada maka hacker
  	// echo " [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!";
	// session_destroy();
	// exit();    
  // }
// }

if(count($bb)>2 and $bb[2]!=0){
  	echo " [Gagal/Failed/Error], your session has expired, please press refresh button and login again..!";
	session_destroy();
	#echo "<script>alert('Session expired. You'll be redirect to login page');location.reload(true)</script>";
	exit();  
}
//unset the param par
unset($_POST['par']);
unset($_GET['par']);

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

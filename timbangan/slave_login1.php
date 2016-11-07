<?php
session_start();
require_once('config/connection.php');
require_once('lib/detailSession.php');
 $strj="select * from ".$dbname.".access_type where status=1";
 $resj=mysql_query($strj);
 if(mysql_num_rows($resj)>0)
 {
	$_SESSION['security']='on';
 }
 else
 {
	$_SESSION['security']='off'; 	
 }
//load local ini++++++++++++++++++++++++++++++++
$ini_array = parse_ini_file("lib/nangkoel.ini");
$_SESSION['MAXLIFETIME']=$ini_array['MAXLIFETIME'];
$_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
//++++++++++++++++++++++++++++++++++
$uid=0;
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);

$uname=$_POST['uname'];
$password=$_POST['password'];
require_once('lib/nangkoelibrfc.php');
$log=new RFCNANGKOEL;
if($log->login($uname,$password))
{
  //set SAP user into session tu use later on transaction:
  $_SESSION['userSAP']   =$_POST['uname'];
  $_SESSION['pwdSAP']=$_POST['password'];
		//update login status
		$stra="update ".$dbname.".user set 
		       logged=1,
			   lastip='".$_SERVER['REMOTE_ADDR']."',
			   lastcomp='".$hostname."'
			   where uname='".$uname."'";
		if(!mysql_query($stra))
		{
			echo "Error:".mysql_error($conn);
		}
		//set standard session
       $str1    ="select * from ".$dbname.".user
		   where uname='".$uname."'";	
		$res1=mysql_query($str1);   	
		while($bar1=mysql_fetch_object($res1))
		{
			$_SESSION['standard']['username']=$bar1->uname;
			$_SESSION['standard']['access_level']=$bar1->access_level;
			$_SESSION['standard']['lastupdate']=$bar1->lastupdate;			
			$_SESSION['standard']['userid']=$bar1->userid;
			$_SESSION['standard']['status']=$bar1->status;
			$_SESSION['standard']['logged']=$bar1->logged;
			$_SESSION['standard']['lastip']=$bar1->lastip;
			$_SESSION['standard']['lastcomp']=$bar1->lastcomp;
		}
		if($_SESSION['standard']['status']==0)//if user status is inactive
		{
			 print_r($_SESSION);
			 echo" Gagal, Your Account is inactive";
			 session_destroy();
			 exit;
		}
		
		//set other sessio and  variables
		if(isset($_SESSION['standard']['username']))
		{
			if($isPrivillaged=getPrivillageType($conn,$dbname))//get access_type, if nothong then kick
			{}
			else
			{
				 if($_SESSION['security']=='on')//if turned on
				 {
					 echo" Gagal, Sorry, No Privillage available for all\ncontact Administrator";
					 session_destroy();
					 exit;
				 }
				 else
				 {
				 	
				 }
			}
			
			$privable=getPrivillages($conn,$_SESSION['standard']['username'],$dbname);//get user privillages
			if(!$privable AND $_SESSION['access_type']=='detail')// if nothong then kick
			{
				 echo" Gagal, Sorry, No Privillage available for your account";
				 session_destroy();
				 exit;				
			}		
			else if($_SESSION['standard']['access_level']==0 AND $_SESSION['access_type']!='detail')
			{
				 if($_SESSION['security']=='on'){//if security is turned on
				 echo" Gagal, Sorry, System uses Levelization Privillages, but you don't have any.\nContact your Administrator";
				 session_destroy();
				 exit;
				 }
				 else
				 {
				 	//if turned off, grant all privillages
				 }
			}
				
		}
	}
	else
	{
		echo "<font color=#AA3322 style='background-color:#FFFFFF'>Wrong username and/or password</font><br><span   style='background-color:#FFFFFF'>Att: This uses case-sensitif</span>";
	}	


?>
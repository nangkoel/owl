<?php
session_start();
require_once('config/connection.php');
require_once('lib/detailSession.php');
 $strj="select * from ".$dbname.".access_type where status=1";
 //echo $strj;
 $resj=mysql_query($strj);
 if(mysql_num_rows($resj)>0)
 {
	$_SESSION['security']='on';
 }
 else
 {
	$_SESSION['security']='off';
 }
 
$uname   =$_POST['uname'];
$password=$_POST['password'];
 $str1    ="select * from ".$dbname.".user
		   where uname='".$uname."'
		   and password=MD5('".$password."')";
$uid=0;
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if($res1=mysql_query($str1))
{
	if(mysql_num_rows($res1)>0)
	{
            $userid="";
		//update login status
		$stra="update ".$dbname.".user set
		       logged=1,lastupdate='".date("Y-m-d H:i:s")."',
			   lastip='".$_SERVER['REMOTE_ADDR']."',
			   lastcomp='".$hostname."'
			   where uname='".$uname."'";
		mysql_query($stra);
		//set standard session
		while($bar1=mysql_fetch_object($res1))
		{
                        $userid=$bar1->karyawanid;
			$_SESSION['standard']['username']=$bar1->uname;
			$_SESSION['standard']['access_level']=$bar1->access_level;
			$_SESSION['standard']['lastupdate']=$bar1->lastupdate;
			$_SESSION['standard']['userid']=$bar1->karyawanid;
			$_SESSION['standard']['status']=$bar1->status;
			$_SESSION['standard']['logged']=$bar1->logged;
			$_SESSION['standard']['lastip']=$bar1->lastip;
			$_SESSION['standard']['lastcomp']=$bar1->lastcomp;
		}
		if($_SESSION['standard']['status']==0)//if user status is inactive
		{
			 echo" Gagal, Account Anda Tidak Aktif";
			 session_destroy();
			 exit;
		}

		//set other sessio and  variables
		if(isset($_SESSION['standard']['username']))
		{
			//get all data from user_empl table
			//setEmplSession($conn,$_SESSION['standard']['userid'],$dbname);

			if($isPrivillaged=getPrivillageType($conn,$dbname))//get access_type, if nothong then kick
			{}
			else
			{
				 if($_SESSION['security']=='on')//if turned on
				 {
					 echo" Gagal, Maaf, Anda Tidak Mempunyai Privillage\nHubungi Administrator Terkait";
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
				 echo" Gagal, Maaf, Anda Tidak Mempunyai Privillage\nHubungi Administrator Terkait";
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

			//setEmployer($conn,$dbname);//get employer detail and active transaction periode
		}
                //load local ini++++++++++++++++++++++++++++++++
                $ini_array = parse_ini_file("lib/nangkoel.ini");
                $_SESSION['MAXLIFETIME']=$ini_array['MAXLIFETIME'];
                $_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
                
//            $strcek="select * from ".$dbname.".setup_approval where karyawanid='".$userid."'";
//            $rescek=mysql_query($strcek);
//            if(mysql_num_rows($rescek)>0){
//                //load local ini++++++++++++++++++++++++++++++++
//                $ini_array = parse_ini_file("lib/nangkoel.ini");
//                $_SESSION['MAXLIFETIME']=$ini_array['MAXLIFETIME'];
//                $_SESSION['DIE']=time()+$_SESSION['MAXLIFETIME'];
//            }
	}
	else
	{
		echo "<font color=#AA3322 style='background-color:#FFFFFF'>Wrong username and/or password</font><br><span   style='background-color:#FFFFFF'>Att: Case Sensitif</span>";
	}
}
else
{
     echo " Gagal, System meet some difficulties to perform your request.\n
	        Please contact administrator regarding your login problem";
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
$menuid=trim($_POST['menuid']);
$uname=trim($_POST['uname']);
$action=$_POST['action'];
print_r($_POST['uname']);
//check if exist
$status=false;

	$str="select * from ".$dbname.".auth
         where namauser='".$uname."'
		 and menuid=".$menuid; 
	$res=mysql_query($str);
	if(mysql_num_rows($res)>0)
	   $status=true;
	else
	   $status=false;   	 


if(!$status and $action=='remove')//if not exist
   {
 	  $str="insert into ".$dbname.".auth
            (namauser,menuid,status,lastuser) 
	        values('".$uname."',".$menuid.",0,'".$_SESSION['standard']['username']."')
			";		
$s=5;
   }
if($status and $action=='remove')//if exist and set to deactive
   {
 	  $str="update ".$dbname.".auth
            set status=0,
			lastuser='".$_SESSION['standard']['username']."' 
	        where namauser='".$uname."'
			and menuid=".$menuid;			
$s=2;
   }   
else if(!$status and $action=='add')
   {
 	  $str="insert into ".$dbname.".auth
            (namauser,menuid,status,lastuser) 
	        values('".$uname."',".$menuid.",1,'".$_SESSION['standard']['username']."')
			";	
$s=3;
   }
else //if exist and set to active
   {
 	  $str="update ".$dbname.".auth
            set status=1,
			lastuser='".$_SESSION['standard']['username']."'  
	        where namauser='".$uname."'
			and menuid=".$menuid;	
$s=4;
   }   
if(mysql_query($str))
{
	
}
else
{
	echo " Gagal,".addslashes(mysql_error($conn));
}
?>

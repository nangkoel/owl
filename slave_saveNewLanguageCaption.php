<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$legend		=$_POST['legend'];
	$location 	=$_POST['location'];
	$arg	    =explode("##",$_POST['arg']);
	$cont	    =explode("##",$_POST['cont']);
//insert new caption to all language
   $str="insert into ".$dbname.".bahasa(legend,location,";
   for($x=0;$x<count($arg);$x++)
   {
   	 if($x==0)
	  $str.=$arg[$x];
//	 if($x==(count($arg)-1))
//	  $str.=$arg[$x];
	 else 
	  $str.=",".$arg[$x];
   }	
   $str.=") values('".$legend."','".$location."',";
   for($x=0;$x<count($cont);$x++)
   {
   	 if($x==0)
	  $str.="'".$cont[$x]."'";
//	 if($x==(count($cont)-1))
//	  $str.=",'".$cont[$x]."'";	  
	 else 
	  $str.=",'".$cont[$x]."'";
   }
   $str.=")"; 
 
   if(mysql_query($str)){
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

<?php
session_start();
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
$str="update ".$dbname.".user set logged=0 where namauser='".$_SESSION['standard']['username']."'";
$res=mysql_query($str);

//echo $str." Error";
if(mysql_affected_rows($conn)>0)
   session_destroy();   
echo"<script language=javascript1.2>
     window.location='.';
	 </script>";   

?>

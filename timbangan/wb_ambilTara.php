<?php
require_once('master_validation.php');
require_once('config/connection.php');
$kodekend=$_POST['kodekend'];
	$str="select VEHTARMIN,VEHTARMAX from ".$dbname.".msvehicle where VEHNOCODE='".$kodekend."'";
 $x="0 s/d 0";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
 $x=" Tarra:".$bar->VEHTARMIN." s/d ".$bar->VEHTARMAX;
}
echo $x;
?>

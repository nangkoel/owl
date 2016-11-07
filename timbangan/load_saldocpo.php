<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$str1="select * from ".$dbname.".mstrxtbs where ".$dbname.".mstrxtbs.OUTIN='0'and ".$dbname.".mstrxtbs.SIPBNO='".$_POST['key']."' order by id desc limit 1";
//echo $str1;
$res1=mysql_query($str1);
if (mysql_num_rows($res1)==0){
	$str2="select SIPBQTY from ".$dbname.".mssipb where SIPBNO='".$_POST['key']."' order by SIPBNO";
	$res2=mysql_query($str2);
	while($bar1=mysql_fetch_object($res2)){
		$sipbqty=$bar1->SIPBQTY;
	}
	echo $sipbqty;
}
else{
while($bar2=mysql_fetch_object($res1)){
	$sipbqty=$bar2->SIPBQTY;
}

echo $sipbqty;
}
?>

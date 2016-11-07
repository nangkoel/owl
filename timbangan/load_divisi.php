<?php
session_start();
//include_once('connection.php');
//include("connection.php");
require_once('config/connection.php');
if($_POST['key']==''){
$str="select distinct DIVCODE,DIVNAME from ".$dbname.".msdivisi where UNITCODE like '%".$_POST['key']."%' order by DIVCODE";
}
else{
$str="select * from ".$dbname.".msdivisi where ".$dbname.".msdivisi.UNITCODE='".$_POST['key']."'order by DIVCODE";
}
//echo $str;
$res=mysql_query($str);
//print_r($res);
//echo mysql_error($con);
$opt_divisi='';
while($bar=mysql_fetch_array($res))
{
	//$option_company.="<option>".$bar[0]."</option>";
	$opt_divisi.="<option value=".$bar[2].">".$bar[2]."</option>";
}
echo $opt_divisi;
?>
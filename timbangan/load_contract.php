<?php
session_start();
//include_once('connection.php');
require_once('config/connection.php');

if($_POST['key']==''){
$str="select distinct BUYERCODE,BUYERNAME from ".$dbname.".msvendorbuyer,".$dbname.".mscontract
      where ".$dbname.".mscontract.BUYERCODE";
}
else{
$str="select * from ".$dbname.".msvendorbuyer,".$dbname.".mscontract where ".$dbname.".mscontract.CTRNO='".$_POST['key']."' and ".$dbname.".msvendorbuyer.BUYERCODE=".$dbname.".mscontract.BUYERCODE";
}
//echo $str;
$res=mysql_query($str);
//print_r($res);
//echo mysql_error($con);
$opt_company='';
while($bar=mysql_fetch_array($res))
{
	//$option_company.="<option>".$bar[0]."</option>";
	//$opt_company.="<option value='".$bar[0]."'>".$bar[1]."</option>";
	$opt_company=$bar[1];
}
echo $opt_company;
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if($_POST['key']=='')
$str="select distinct COMPCODE,COMPNAME from ".$dbname.".mscompany where WILCODE like '%".$_POST['key']."%' order by COMPCODE";
else
$str="select distinct COMPCODE,COMPNAME from ".$dbname.".mscompany where WILCODE='".$_POST['key']."' order by COMPCODE";
//echo $str;
$res=mysql_query($str);
$opt_company='';
while($bar=mysql_fetch_array($res))
{
	//$option_company.="<option>".$bar[0]."</option>";
	$opt_company.="<option value=".$bar[0].">".$bar[1]."</option>";
}
echo $opt_company;
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

if($_POST['key']=='')
$str="select distinct UNITCODE,UNITNAME from ".$dbname.".msunit where COMPCODE like '%".$_POST['key']."%' order by UNITCODE";
else
$str="select distinct UNITCODE,UNITNAME from ".$dbname.".msunit where COMPCODE='".$_POST['key']."' order by UNITCODE";
//echo $str;
$res=mysql_query($str);
$opt_unit='';
while($bar=mysql_fetch_array($res))
{
	//$option_company.="<option>".$bar[0]."</option>";
	$opt_unit.="<option value=".$bar[0].">".$bar[1]."</option>";
}
echo $opt_unit;
?>

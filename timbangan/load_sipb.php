<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$productcode=$_POST['key'];
	//$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBQTY>0";
	$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."'";
	$res1=mysql_query($str1);
	//print_r($res1);
	$opt_product='';
		while($bar=mysql_fetch_array($res1)){
			$opt_product.="<option value=".$bar[0].">".$bar[1]."</option>";
			$pcode=$bar[3];
			//$opt_transporter.=$bar[1];
		}
 if($_POST['key']=='40000003')
{
	echo "Product yang dipilih tidak termasuk CPO/PK";
	exit("Error .");
}
echo $opt_product.",".$pcode.",".$productcode;
?>

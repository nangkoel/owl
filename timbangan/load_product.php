<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if($_POST['key']=='CPO'){
	$productcode=40000007;
	//$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBQTY>0";
	$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBSTATUS='Aktif'
	       and SIPBQTY>0 order by SIPBDATE desc";
	$res1=mysql_query($str1);
	//print_r($res1);
	$opt_product='';
		while($bar=mysql_fetch_array($res1)){
			$opt_product.="<option value=".$bar[0].">".$bar[1]."</option>";
			$pcode=$bar[3];
			//$opt_transporter.=$bar[1];
		}
}
else if($_POST['key']=='KER')
{
	$productcode=40000006;
	//$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBQTY>0";
	$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBSTATUS='Aktif'
	       and SIPBQTY>0  order by SIPBDATE desc";
	$res1=mysql_query($str1);
	//print_r($res1);
	$opt_product='';
		while($bar=mysql_fetch_array($res1)){
			$opt_product.="<option value=".$bar[0].">".$bar[1]."</option>";
			$pcode=$bar[3];
			//$opt_transporter.=$bar[1];
		}
}
/*else if($_POST['key']=='40000003')
{
	echo "Product yang dipilih tidak termasuk CPO/PK";
}*/
else{
	$productcode=40000005;
	//$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBQTY>0";
	$str1="select * from ".$dbname.".mssipb where ".$dbname.".mssipb.PRODUCTCODE='".$productcode."' and ".$dbname.".mssipb.SIPBSTATUS='Aktif'";
	$res1=mysql_query($str1);
	//print_r($res1);
	$opt_product='';
		while($bar=mysql_fetch_array($res1)){
			$opt_product.="<option value=".$bar[0].">".$bar[1]."</option>";
			$pcode=$bar[3];
			//$opt_transporter.=$bar[1];
		}
}
echo $opt_product.",".$pcode.",".$productcode;
//echo $str1;
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');

$karyawanid=$_POST['karyawanid'];
$val=$_POST['val'];

$str="select * from ".$dbname.".sdm_5jatahbbm where karyawanid=".$karyawanid;
$res=mysql_query($str);
if(mysql_num_rows($res)>0)
{
	//update
	$str="update ".$dbname.".sdm_5jatahbbm set jatah=".$val." where karyawanid=".$karyawanid;	
}
else
{
	$str="insert into ".$dbname.".sdm_5jatahbbm(karyawanid,jatah)
	      values(".$karyawanid.",".$val.")";
}
mysql_query($str);
?>
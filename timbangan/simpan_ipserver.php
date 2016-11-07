<?php
require_once('master_validation.php');
require_once('config/connection.php');
$id=trim($_POST['id']);
$ip=trim($_POST['IP']);
$name=trim(strtoupper($_POST['NAME']));
$port=trim(strtoupper($_POST['PORT']));
$wilayah=trim(strtoupper($_POST['WILAYAH']));
$no=0;
if($id!='simpan')
{
	$str="update ".$dbname.".bpssvr set addr='".$ip."',name='".$name."',port='".$port."'
	where wilayah='".$wilayah."'";

}
else
{
	$str="insert into ".$dbname.".bpssvr(addr,name,port,wilayah)
	values('".$ip."','".$name."','".$port."','".$wilayah."')";

}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
$name=trim(ucwords($_POST['name']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msvehtype set VEHTYPENAME='".$name."' where VEHTYPECODE='".$code."'";
    //echo $str;
}
else
{
	$str="insert into ".$dbname.".msvehtype(VEHTYPECODE,VEHTYPENAME) values ('".$code."','".$name."')";
}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

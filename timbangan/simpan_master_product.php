<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['name']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msproduct set PRODUCTNAME='".$name."' where PRODUCTCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msproduct(PRODUCTCODE,PRODUCTNAME,USERID)
	values('".$code."','".$name."','".$_SESSION['standard']['username']."')";
    //echo $str;
}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

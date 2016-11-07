<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$wilcode=trim(strtoupper($_POST['wilcode']));
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['name']));
$alamat=trim(strtoupper($_POST['alamat']));
$kota=trim(strtoupper($_POST['kota']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".mscompany set COMPNAME='".$name."',COMPADDR='".$alamat."',COMPCITY='".$kota."'
	where COMPCODE='".$code."'";

}
else
{
	$str="insert into ".$dbname.".mscompany(COMPCODE,COMPNAME,WILCODE,COMPADDR,COMPCITY,USERID)
	values('".$code."','".$name."','".$wilcode."','".$alamat."','".$kota."','".$_SESSION['standard']['username']."')";

}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

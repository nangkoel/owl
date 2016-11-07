<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['name']));
$alamat=trim(strtoupper($_POST['alamat']));
$kota=trim(strtoupper($_POST['kota']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msvendortrp set TRPNAME='".$name."',TRPADDR='".$alamat."',TRPCITY='".$kota."'
	where TRPCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msvendortrp(TRPCODE,TRPNAME,TRPADDR,TRPCITY,USERID)
	values('".$code."','".$name."','".$alamat."','".$kota."','".$_SESSION['standard']['username']."')";

}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

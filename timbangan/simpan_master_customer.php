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
	$str="update ".$dbname.".msvendorbuyer set BUYERNAME='".$name."',BUYERADDR='".$alamat."',BUYERCITY='".$kota."'
	where BUYERCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msvendorbuyer(BUYERCODE,BUYERNAME,BUYERADDR,BUYERCITY,USERID)
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

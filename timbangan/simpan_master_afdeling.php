<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$unitcode=trim(strtoupper($_POST['unitcode']));
$compcode=trim(strtoupper($_POST['compcode']));
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['name']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msdivisi set DIVNAME='".$name."' where DIVCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msdivisi(COMPCODE,UNITCODE,DIVCODE,DIVNAME,USERID)
	values('".$compcode."','".$unitcode."','".$code."','".$name."','".$_SESSION['standard']['username']."')";

}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

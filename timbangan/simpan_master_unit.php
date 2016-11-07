<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$wilcode=trim(strtoupper($_POST['wilcode']));
$compcode=trim(strtoupper($_POST['compcode']));
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['name']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msunit set UNITNAME='".$name."' where UNITCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msunit(UNITCODE,UNITNAME,WILCODE,COMPCODE,USERID)
	values('".$code."','".$name."','".$wilcode."','".$compcode."','".$_SESSION['standard']['username']."')";

}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

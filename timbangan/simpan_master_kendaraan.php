<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$trpcode=trim(strtoupper($_POST['trpcode']));
$typecode=trim(strtoupper($_POST['typecode']));
$code=trim(strtoupper($_POST['code']));
$name=trim(strtoupper($_POST['vehtarmin']));
$name2=trim(strtoupper($_POST['vehtarmax']));
$driver=trim(strtoupper($_POST['driver']));
$nosim=trim(strtoupper($_POST['nosim']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".msvehicle set TRPCODE='".$trpcode."',VEHTARMIN='".$name."',VEHTARMAX='".$name2."',VEHDRIVER='".$driver."',VEHDRVSIM='".$nosim."'
		  where VEHNOCODE='".$code."'";
}
else
{
	$str="insert into ".$dbname.".msvehicle(VEHNOCODE,TRPCODE,VEHTYPECODE,VEHTARMIN,VEHTARMAX,VEHDRIVER,VEHDRVSIM,USERID)
	values('".$code."','".$trpcode."','".$typecode."',".$name.",".$name2.",'".$driver."','".$nosim."','".$_SESSION['standard']['username']."')";
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

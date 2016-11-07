<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
//$code=$_POST['code'];
//$code2=substr($code,1,2);
$name=trim(ucwords($_POST['name']));
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".mswilayah set WILNAME='".$name."' where WILCODE='".$code."'";
    //echo $str;
}
else
{
	//$str="insert into ".$dbname.".bagian(kd_seksi,uraian)values('".$code."','".$name."')";
	$str="insert into ".$dbname.".mswilayah(WILCODE,WILNAME) values ('".$code."','".$name."')";
	//
}
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

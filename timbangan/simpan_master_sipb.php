<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
$tgl=tanggalsystem($_POST['tglsipb']);
$ctrno=trim(strtoupper($_POST['ctrno']));
$product=trim(strtoupper($_POST['product']));
$trp=trim(strtoupper($_POST['trp']));
$qty=trim(strtoupper($_POST['qty']));
$ket=trim(strtoupper($_POST['ket']));
$status=$_POST['status'];
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".mssipb set SIPBDATE='".$tgl."',SIPBQTY=".$qty.",DESCRIPTION='".$ket."',SIPBSTATUS='".$status."'
		  where SIPBNO='".$code."'";
}
else
{
	$str="insert into ".$dbname.".mssipb(CTRNO,SIPBNO,SIPBDATE,PRODUCTCODE,TRPCODE,SIPBQTY,DESCRIPTION,SIPBSTATUS,USERID)
	values('".$ctrno."','".$code."','".$tgl."','".$product."','".$trp."',".$qty.",'".$ket."','".$status."','".$_SESSION['standard']['username']."')";
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

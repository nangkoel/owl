<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$id=trim($_POST['id']);
$code=trim(strtoupper($_POST['code']));
$tgl=tanggalsystem($_POST['tglkontrak']);
$buyercode=trim(strtoupper($_POST['buyercode']));
$qty=trim(strtoupper($_POST['qty']));
$ket=trim(strtoupper($_POST['ket']));
$status=$_POST['status'];
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".mscontract set CTRDATE='".$tgl."',CTRQTY=".$qty.",DESCRIPTION='".$ket."',CTRSTATUS='".$status."'
		  where CTRNO='".$code."'";
}
else
{
	$str="insert into ".$dbname.".mscontract(CTRNO,CTRDATE,BUYERCODE,CTRQTY,DESCRIPTION,CTRSTATUS,USERID)
	values('".$code."','".$tgl."','".$buyercode."',".$qty.",'".$ket."','".$status."','".$_SESSION['standard']['username']."')";
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

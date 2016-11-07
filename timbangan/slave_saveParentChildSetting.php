<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$parent=$_POST['parent'];
$child=$_POST['child'];

//get urut
$str="select max(urut) from ".$dbname.".menu where parent=".$parent;
$res=mysql_query($str);

while($bar=mysql_fetch_array($res))
{
	$urut=$bar[0];
}
//next urut is
$urut+=1;

//update menu table
$str1="update ".$dbname.".menu
      set parent=".$parent.", urut=".$urut.",lastuser='".$_SESSION['standard']['username']."'
	  where id=".$child;	  
	if(mysql_query($str1))
	{}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

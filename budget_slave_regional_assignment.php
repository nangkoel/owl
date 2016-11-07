<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

$organisasi=$_POST['organisasi'];
$regional=$_POST['regional'];
$method=$_POST['method'];
//$arrEnum=getEnum($dbname,'bgt_tipe','tipe,nama');
switch($method)
{
case 'update':	
	$str="update ".$dbname.".bgt_regional_assignment set regional='".$regional."'
	       where kodeunit='".$organisasi."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal1,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".bgt_regional_assignment (kodeunit,regional)
	      values('".$organisasi."','".$regional."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal2,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".bgt_regional_assignment 
	where kodeunit='".$organisasi."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal3,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".bgt_regional_assignment order by kodeunit";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodeunit."</td><td>".$bar1->regional."</td><td align=center><img src=images/application/application_delete.png class=resicon  caption='Edit' onclick=\"deleteDep('".$bar1->kodeunit."','".$bar1->regional."');\"></td></tr>";
}	 
}
?>

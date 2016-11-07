<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kode=$_POST['kode'];
$nama=$_POST['nama'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".rencana_gis_jenis set namajenis='".$nama."'
	       where kode='".$kode."'";
               	if( mysql_query($str))
	{}
	else {echo " Gagal,".addslashes(mysql_error($conn));exit();}
	break;
case 'insert':
	$str="insert into ".$dbname.".rencana_gis_jenis (kode,namajenis)
	      values('".$kode."','".$nama."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));exit();}	
	break;
case 'delete':
	$str="delete from ".$dbname.".rencana_gis_jenis 
	where kode='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));exit();}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".rencana_gis_jenis order by kode";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->kode."</td><td>".$bar1->namajenis."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->namajenis."');\"></td></tr>";
}	 
}
?>

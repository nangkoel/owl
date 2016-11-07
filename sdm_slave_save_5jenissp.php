<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kode=$_POST['kode'];
$keterangan=$_POST['keterangan'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5jenissp set keterangan='".$keterangan."'
	       where kode='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5jenissp (kode,keterangan)
	      values('".$kode."','".$keterangan."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5jenissp
	where kodejabatan='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".sdm_5jenissp order by kode";
if($res1=mysql_query($str1))
{
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
	 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['tipe']."</td><td>".$_SESSION['lang']['keterangan']."</td><td  style='width:30px;'>*</td></tr>
	 </thead>
	 <tbody>";
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->kode."</td><td>".$bar1->keterangan."</td><td><img src=images/application/application_edit.png class=resicon caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->keterangan."');\"></td></tr>";
}	 
echo"	 
	 </tbody>
	 <tfoot>
	 </tfoot>
	 </table>";
}
?>

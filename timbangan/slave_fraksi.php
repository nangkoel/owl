<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kode=$_POST['kode'];
$nama=$_POST['nama'];
$potongan=$_POST['potongan'];
$satuan=$_POST['satuan'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".msfraksi set keterangan='".$nama."',potongan='".$potongan."',satuan='".$satuan."'
	       where kodefraksi='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".msfraksi (kodefraksi,keterangan,potongan,satuan)
	      values('".$kode."','".$nama."',".$potongan.",'".$satuan."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".msfraksi 
	where kodefraksi='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
	$str1="select * from ".$dbname.".msfraksi order by kodefraksi";
	$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
 echo"<tr class=rowcontent><td align=center>".$bar1->kodefraksi."</td><td>".$bar1->keterangan."</td><td>".$bar1->potongan."</td><td>".$bar1->satuan."</td><td><img src=images/edit.png style='height:20px'  caption='Edit' onclick=\"fillField('".$bar1->kodefraksi."','".$bar1->keterangan."','".$bar1->potongan."');\"></td></tr>";
}	 

?>

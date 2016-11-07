<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kode=$_POST['kode'];
$keterangan=$_POST['keterangan'];
$jumlahhk=$_POST['jumlahhk'];
$group=$_POST['grup'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5absensi set keterangan='".$keterangan."',
	       kelompok=".$group.",nilaihk=".$jumlahhk."
	       where kodeabsen='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5absensi 
	      (kodeabsen,keterangan,kelompok,nilaihk)
	      values('".$kode."','".$keterangan."',".$group.",".$jumlahhk.")";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5absensi
	where kodeabsen='".$kode."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}

	$str1="select *,
	     case kelompok when 1 then '".$_SESSION['lang']['dibayar']."'
		 when 0 then '".$_SESSION['lang']['tidakdibayar']."'
		 end as ketgroup 
	     from ".$dbname.".sdm_5absensi order by kodeabsen";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeabsen."</td>
				   <td>".$bar1->keterangan."</td>
				   <td>".$bar1->ketgroup."</td>
				   <td>".$bar1->nilaihk."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeabsen."','".$bar1->keterangan."','".$bar1->kelompok."','".$bar1->nilaihk."');\"></td></tr>";
}	 
}
?>

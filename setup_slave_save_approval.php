<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$app=$_POST['app'];
$method=$_POST['method'];
$karyawanid=$_POST['karyawanid'];
switch($method)
{
case 'update':	
	//$str="update ".$dbname.".setup_approval set kodeunit='".$kodeorg."',
	//       where kode='".$kode."'";
	//if(mysql_query($str))
	//{}
	//else
	//{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".setup_approval (kodeunit,applikasi,karyawanid)
	      values('".$kodeorg."','".$app."',".$karyawanid.")";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".setup_approval 
	where kodeunit='".$kodeorg."' and karyawanid=".$karyawanid." and applikasi='".$app."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
	$str1="select a.*,b.namakaryawan from ".$dbname.".setup_approval a
               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               order by kodeunit";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodeunit."</td><td>".$bar1->applikasi."</td><td>".$bar1->namakaryawan."</td>
                    <td>
                   <img src=images/skyblue/delete.png class=resicon  caption='Edit' onclick=\"dellField('".$bar1->kodeunit."','".$bar1->applikasi."','".$bar1->karyawanid."');\">     
                   </td></tr>";
}	 
}
?>

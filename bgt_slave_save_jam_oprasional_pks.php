<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

$thnbudget=$_POST['thnbudget'];
$kdpks=$_POST['kdpks'];
$jamo=$_POST['jamo'];
$jamb=$_POST['jamb'];
$arrEnum=getEnum($dbname,'bgt_jam_operasioal_pks','jamolah,breakdown');
$method=$_POST['method'];

		


switch($method)
{
case 'update':	
	$str="update ".$dbname.".bgt_jam_operasioal_pks set jamolah='".$jamo."',breakdown='".$jamb."'
	       where tahunbudget='".$thnbudget."' and millcode='".$kdpks."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
    $str="select * from ".$dbname.".bgt_jam_operasioal_pks 
	       where tahunbudget='".$thnbudget."' and millcode='".$kdpks."'
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $sudahada="1";
		$pesan=$bar->tahunbudget."-".$bar->millcode."-".$bar->jamolah."-".$bar->breakdown;
    }
    if($sudahada=="1"){
        echo " Gagal, data sudah ada: ".$pesan; exit;
    }

    $str="insert into ".$dbname.".bgt_jam_operasioal_pks (`tahunbudget`,`millcode`,`jamolah`,`breakdown`)
		values ('".$thnbudget."','".$kdpks."','".$jamo."','".$jamb."')";
		
	//	exit ("Error:$str");
		
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".bgt_jam_operasioal_pks 
	       where tahunbudget='".$thnbudget."' and millcode='".$kdpks."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".bgt_jam_operasioal_pks order by tahunbudget";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		$no+=1;
		echo"<tr class=rowcontent>
			<td align=center>".$no."</td>
			<td align=right>".$bar1->tahunbudget."</td>
			<td align=center>".$bar1->millcode."</td>
			<td align=right>".$bar1->jamolah."</td>
			<td align=right>".$bar1->breakdown."</td>			
		<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->millcode."','".$bar1->jamolah."','".$bar1->breakdown."');\"></td></tr>";
}	 
}
?>






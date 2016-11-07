<?php
require_once('master_validation.php');
require_once('config/connection.php');
$namapasar = isset($_POST['namapasar'])? $_POST['namapasar']:'';
$id=isset($_POST['id'])? $_POST['id']:'';
$method = $_POST['method'];
switch($method)
{
case 'insert':
	$str="insert into ".$dbname.".pmn_5pasar (namapasar)
	      values('".$namapasar."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'update':
	$str="update ".$dbname.".pmn_5pasar set namapasar='".$namapasar."'
	where id=".$id;
	if(!mysql_query($str))
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
	$str1="select * from ".$dbname.".pmn_5pasar
        order by namapasar";
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
			<td align=center>".$bar1->namapasar."</td>
			<td>
				<img src=images/skyblue/edit.png class=zImgBtn  caption='Edit' onclick=\"editField(".$bar1->id.",'".$bar1->namapasar."');\">
			</td></tr>";
	}
}
?>

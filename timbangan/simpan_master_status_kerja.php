<?php
require_once('master_validation.php');
require_once('config/connection.php');

$id=trim($_POST['id']);
//$code=trim(strtoupper($_POST['code']));
$code=$_POST['code'];
$code2=substr($code,1,2);
$name=trim(ucwords($_POST['name']));
$pro_l=$_POST['pro_l'];
$pro_p=$_POST['pro_p'];
$divisi=$_POST['divisi'];
$no=0;
//if($id!='new')
if($id!='simpan')
{
	$str="update ".$dbname.".st_kerja set uraian='".$name."',pro_l=".$pro_l.",pro_p=".$pro_p."
	 where kd_sk='".$code2."'";
    //echo $str;
}
else
{
	//$str="insert into ".$dbname.".bagian(kd_seksi,uraian)values('".$code."','".$name."')";
	$str="insert into ".$dbname.".st_kerja(kd_div,uraian,pro_l,pro_p) values ('".$divisi."','".$name."',".$pro_l.",".$pro_p.")";
}
	if(mysql_query($str))
	{   //$no+=1;
		$str="select * from ".$dbname.".st_kerja order by kd_sk";
		$res=mysql_query($str);
		echo"    <table width=500px class=sortable border=0 cellspacing=1>
			     <thead>
				 <tr class=rowheader>
				 <td>No.</td>
				 <td>Kode</td>
				 <td>Uraian</td><td>Edit</td></tr>
				 </thead>
				 <tbody>";
			while($bar=mysql_fetch_object($res))
			 {  $no+=1;
			 	echo "<tr  class=rowcontentClick>
				      <td class=firsttd align=center>".$no."</td>
					  <td>".$bar->kd_sanksi."</td>
					  <td>".$bar->uraian."</td>
					  <td align=center><img title='Click u/ mengedit' class=editbtn src=images/edit.png  onclick=\"changeSDept('".$bar->kd_seksi."','".$bar->uraian."');\"></td>
					  </tr>
					 ";
			 }

			echo"</tbody>
			     </table>";
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

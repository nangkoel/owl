<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$name	=$_POST['name'];	
$add	=$_POST['add']; 
$city	=$_POST['city'];	
$phone	=$_POST['phone'];		
$mail	=$_POST['mail'];	
$status	=$_POST['status'];
$id	=$_POST['id'];

if(isset($_POST['name']) and !isset($_POST['del']) and !isset($_POST['update']))
{
$str="insert into ".$dbname.".sdm_5rs(
	  namars,alamat,telp,kota,email,status)
	  values(
		'".$name."','".$add."','".$phone."',
		'".$city."','".$mail."',".$status."
	  )";
}
else if(isset($_POST['update']))
{
	$str="update ".$dbname.".sdm_5rs
	      set namars='".$name."',
		  alamat='".$add."',
		  email='".$mail."',
		  telp='".$phone."',
		  kota='".$city."',
		  status=".$status."
		  where id=".$id;
}
else if(isset($_POST['del']))
{
$str="delete from ".$dbname.".sdm_5rs where
	  id =".$id;	  
}
else
{
	$str="select 1=1";
}
   if(mysql_query($str))
   {
   	$std="select *, case status when 1 then 'Active' when 0 then 'Black List' end as xstatus
		  from ".$dbname.".sdm_5rs order by namars";
	$res=mysql_query($std);
	$no=0;
	while($bad=mysql_fetch_object($res))
	{
	  $no+=1;
	  echo"<tr class=rowcontent>
			  <td class=firsttd>".$no."</td>
			  <td>".$bad->namars."</td>
			  <td>".$bad->alamat."</td>
			  <td>".$bad->kota."</td>
			  <td>".$bad->telp."</td>
			  <td>".$bad->email."</td>
			  <td>".$bad->xstatus."</td>
		      <td align=center>
			     <img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editHospital('".$bad->id."','".$bad->namars."','".$bad->kota."','".$bad->alamat."','".$bad->telp."','".$bad->email."','".$bad->status."')\">
		         <img src=images/close.png class=dellicon title=delete height=11px onclick=\"deleteHospital('".$bad->id."');\">
			  </td>
			</tr>";		
	}
		 
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');

$name=$_POST['name'];
$id  =$_POST['id'];
$plus  =$_POST['plus'];
$type =$_POST['type'];
$lock =$_POST['lock'];

		
	if(trim($id)!='')
	{
		$str="update ".$dbname.".sdm_ho_component set name='".$name."',
		plus=".$plus.",type='".$type."',`lock`=".$lock." where id=".$id;
	}
	else
	{
		$str="insert into ".$dbname.".sdm_ho_component 
		(name,plus,type,`lock`) values('".$name."','".$plus."','".$type."',".$lock.")";	
	}
	if(mysql_query($str))
	{
	    $str="select * from ".$dbname.".sdm_ho_component order by id";
		$res=mysql_query($str);
		$no=0;
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			echo "<tr class=rowcontent><td class=fisttd>".$no."</td>
			      <td>".$bar->name."</td>
			      <td>".($bar->plus==1?$_SESSION['lang']['penambah']:$_SESSION['lang']['pengurang'])."</td>
				  <td>".$bar->type."</td>
				  <td>".($bar->lock==1?$_SESSION['lang']['dikunci']:$_SESSION['lang']['inputbebas'])."</td>
				  <td align=center><img src=images/tool.png class=dellicon title=Edit height=11px onclick=\"editComp('".$bar->id."','".$bar->name."','".$bar->plus."','".$bar->type."','".$bar->lock."')\"> 
				  <img src=images/close.png  height=11px class=dellicon title=Delete  onclick=\"delComp('".$bar->id."','".$bar->name."')\"></td>
				  </tr>";
		}	
	}
	else
	{
		echo " Error: ".addslashes(mysql_error($conn));
	} 
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$idx		=$_POST['idx'];	
$name		=ucwords($_POST['name']); 

if(trim($idx==''))
{
$str="insert into ".$dbname.".sdm_5diagnosa(diagnosa)values('".$name."')";
}
else
{
$str="update ".$dbname.".sdm_5diagnosa
      set diagnosa='".$name."'
      where 
	  id=".$idx;
}
   if(mysql_query($str))
   {
		$str="select * from ".$dbname.".sdm_5diagnosa order by diagnosa";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			echo"<tr class=rowcontent>
			      <td class=firsttd>".$bar->id."</td>
				  <td>".$bar->diagnosa."</td>
				  <td><img src=images/edit.png align=middle style='cursor:pointer;' onclick=\"editDiagnosa('".$bar->id."','".$bar->diagnosa."');\" height=17px align=right title='Edit data for ".$bar->diagnosa."'></td>
			     </tr>";
		}
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
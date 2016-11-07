<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$str=" select a.* from ".$dbname.".sdm_perumahanht a where kodeorg='".$kodeorg."'";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
     $jlh=0;
	 $str1="select count(karyawanid) as jlh from ".$dbname.".sdm_penghunirumah
	        where kodeorg='".$kodeorg."' and blok='".$bar->blok."'
			and norumah='".$bar->norumah."'";
	 $res1=mysql_query($str1);
	 while($bar1=mysql_fetch_object($res1))
	 {
	 	$jlh=$bar1->jlh;
	 }		
 
 echo"<tr class=rowcontent>
		 <td>".$no."</td>
		 <td>".$kodeorg."</td>
		 <td>".$bar->kompleks."</td>
		 <td>".$bar->blok."</td>
		 <td>".$bar->norumah."</td>
		 <td>".$bar->tipe."</td>
		 <td align=right>".$jlh."</td>
		 <td>
		 <img src=images/zoom.png class=resicon onclick=showTenant('".$kodeorg."','".$bar->blok."','".$bar->norumah."',event)>
		 </td>
		 </tr>";	
}
?>

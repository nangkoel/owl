<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_POST['kodeorg'];
$tipelembur=$_POST['tipelembur'];
$jamaktual=$_POST['jamaktual'];
$jamlembur=$_POST['jamlembur'];
$method=$_POST['method'];

if($jamaktual=='')
   $jamaktual=0;
if($jamlembur=='')
   $jamlembur=0;

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5lembur set jamlembur='".$jamlembur."'
	       where kodeorg='".$kodeorg."' and tipelembur='".$tipelembur."'
		   and jamaktual=".$jamaktual;
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5lembur 
	      (kodeorg,tipelembur,jamaktual,jamlembur)
	      values('".$kodeorg."','".$tipelembur."',".$jamaktual.",".$jamlembur.")";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5lembur
	 where kodeorg='".$kodeorg."' and tipelembur='".$tipelembur."'
	 and jamaktual=".$jamaktual;
	 //exit("Error:$str");
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}

	$str1="select *,
	     case tipelembur when '0' then '".$_SESSION['lang']['haribiasa']."'
		 when '1' then '".$_SESSION['lang']['hariminggu']."'
		 when '2' then '".$_SESSION['lang']['harilibur']."'
		 when '3' then '".$_SESSION['lang']['hariraya']."'
		 end as ketgroup 
	     from ".$dbname.".sdm_5lembur 
		 where LEFT(kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		 order by tipelembur,jamaktual";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeorg."</td>
				   <td>".$bar1->ketgroup."</td>
				   <td align=center>".$bar1->jamaktual."</td>
				   <td align=center>".$bar1->jamlembur."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."','".$bar1->jamlembur."');\"></td>
				    <td><img src=images/application/application_edit.png class=resicon  caption='Delete' onclick=\"del('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."');\"></td>
					</tr>";
}	 
}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');

$type='regular';
$userid=$_POST['userid'];
$component  =$_POST['component'];
$plus  =$_POST['plus'];
$val  =$_POST['val'];
$periode=$_SESSION['pyperiode'];
$terbilang=$_POST['terbilang'];

if($plus==0 or $plus=='0')
{
	$val=$val*-1;
}

if($val==0)
{
//if 0 leave it, do nothing	
}
else
{		
	if(isset($_POST['replace']))
	{
/* update mejadi menghapus semua gaji kary bersangkutan dan menginput ulang
		$str="delete from ".$dbname1.".detailmonthly
	      where userid=".$userid." and component=".$component."
		  and periode='".$periode."'";
*/
		$str="delete from ".$dbname.".sdm_ho_detailmonthly
	      where karyawanid=".$userid."
		  and periode='".$periode."'";
		mysql_query($str,$conn);  
		
		$str="insert into ".$dbname.".sdm_ho_detailmonthly 
		(karyawanid,component,value,periode,plus,updatedby) 
		values(".$userid.",".$component.",".$val.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";	
		if(mysql_query($str,$conn))
		{		
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		}		
	}
	else
	{
	
	$str="select * from ".$dbname.".sdm_ho_detailmonthly
	      where karyawanid=".$userid." and component=".$component."
		  and periode='".$periode."'";  
	$res=mysql_query($str,$conn);		  
	if(mysql_num_rows($res)>0)
	{
       echo " Double";		
	}
	else
	{		  
		$str="insert into ".$dbname.".sdm_ho_detailmonthly 
		(karyawanid,component,value,periode,plus,updatedby) 
		values(".$userid.",".$component.",".$val.",'".$periode."',".$plus.",'".$_SESSION['standard']['username']."')";	
		if(mysql_query($str,$conn))
		{
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		}
	}
//write TERBILANG
	
			if($component==1)//insert or update only on gaji pokok
			{
				$stu="select * from ".$dbname.".sdm_ho_payrollterbilang
				where userid=".$userid." and periode='".$periode."'
				and `type`='".$type."'";
				$resu=mysql_query($stu);
				if(mysql_num_rows($resu)>0)
				{ 
					$stre="update ".$dbname.".sdm_ho_payrollterbilang
					set terbilang='".$terbilang."'
					where userid=".$userid." and periode='".$periode."'
					and `type`='".$type."'";
				}
				else
				{
					$stre="insert into ".$dbname.".sdm_ho_payrollterbilang
					(userid,periode,`type`,terbilang)
					values(".$userid.",'".$periode."','".$type."','".$terbilang."')";
				}
				if(mysql_query($stre,$conn))
				{
				}
				else
				{
					echo " Error: gagal insert TERBILANG ".addslashes(mysql_error($conn));
				}
			}	
	}
}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$kodeorg=$_POST['kodeorg'];
$metodepenggajian=$_POST['metodepenggajian'];
$periode=$_POST['periode'];
$tanggalmulai=tanggalsystem($_POST['tanggalmulai']);
$tanggalsampai=tanggalsystem($_POST['tanggalsampai']);
$tutup=$_POST['tutup'];
$method=$_POST['method'];

switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5periodegaji set
	       tanggalmulai=".$tanggalmulai.",
		   tanggalsampai=".$tanggalsampai.",
		   sudahproses=".$tutup.",tglcutoff='".tanggalsystem($_POST['tanggalctf'])."'
	       where kodeorg='".$kodeorg."' and periode='".$periode."'
		   and jenisgaji='".$metodepenggajian."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5periodegaji 
	      (kodeorg,periode,tanggalmulai,tanggalsampai,sudahproses,jenisgaji,tglcutoff)
	      values('".$kodeorg."','".$periode."',".$tanggalmulai.",".$tanggalsampai.",".$tutup.",'".$metodepenggajian."','".tanggalsystem($_POST['tanggalctf'])."')";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5periodegaji
	      where kodeorg='".$kodeorg."' and periode='".$periode."'
		  and jenisgaji='".$metodepenggajian."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}

	$str1="select *,
	     case jenisgaji when 'H' then '".$_SESSION['lang']['harian']."'
		 when 'B' then '".$_SESSION['lang']['bulanan']."'
		 end as ketgroup, 
		 case sudahproses when '1' then '".$_SESSION['lang']['yes']."'
		 when '0' then '".$_SESSION['lang']['no']."'
		 end as sts
	     from ".$dbname.".sdm_5periodegaji 
		 where LEFT(kodeorg,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		 order by periode desc"; 
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
			echo"<tr class=rowcontent>
			           <td align=center>".$bar1->kodeorg."</td>
					   <td>".$bar1->ketgroup."</td>
					   <td align=center>".$bar1->periode."</td>
					   <td align=center>".tanggalnormal($bar1->tanggalmulai)."</td>
					   <td align=center>".tanggalnormal($bar1->tanggalsampai)."</td>
                                                   <td align=center>".tanggalnormal($bar1->tglcutoff)."</td>
					   <td align=center>".$bar1->sts."</td>
					   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->jenisgaji."','".$bar1->periode."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalsampai)."','".$bar1->sudahproses."');\"></td></tr>";
	}	 
}
?>

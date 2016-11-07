<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');


$oldtahunbudget=$_POST['oldtahunbudget'];
$oldkodeorg=$_POST['oldkodeorg'];
$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
/*$thnbudget=$_POST['thnbudget'];
$kdafd=$_POST['kdafd'];*/
$sb=$_POST['sb'];
$lb=$_POST['lb'];
//$arrEnum=getEnum($dbname,'bgt_borong_panen','siapborong,lebihborong');
$method=$_POST['method'];



switch($method)
{
	
	
		case 'insert':
		$oldtahunbudget==''?$oldtahunbudget=$_POST['tahunbudget']:$oldtahunbudget=$_POST['oldtahunbudget'];
		$oldkodeorg==''?$oldkodeorg=$_POST['kodeorg']:$oldkodeorg=$_POST['oldkodeorg'];
		
		if(strlen($tahunbudget)<4)
		{
			exit("Error:tahun budget belum sesuai");
		}	
		$sRicek="select * from ".$dbname.".bgt_borong_panen where tahunbudget='".$oldtahunbudget."' and kodeorg='".$oldkodeorg."' ";
		//exit("Error:$sRicek");
		
		$qRicek=mysql_query($sRicek) or die(mysql_error($conn));
		$rRicek=mysql_num_rows($qRicek);
		
		if($rRicek>0)
		{
		$sDel="delete from ".$dbname.".bgt_borong_panen
				where tahunbudget='".$oldtahunbudget."' and kodeorg='".$oldkodeorg."'  ";	    
			if(mysql_query($sDel))
			{
			$sDel2="insert into ".$dbname.".bgt_borong_panen (`tahunbudget`,`kodeorg`,`siapborong`,`lebihborong`)
		values ('".$tahunbudget."','".$kodeorg."','".$sb."','".$lb."')";
		
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
			}
			else	
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}	
		}
		else
		{
		$sDel2="insert into ".$dbname.".bgt_borong_panen (`tahunbudget`,`kodeorg`,`siapborong`,`lebihborong`)
		values ('".$tahunbudget."','".$kodeorg."','".$sb."','".$lb."')";
		if(mysql_query($sDel2))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	
	
	
	case'loadData':
		$str1="select * from ".$dbname.".bgt_borong_panen order by tahunbudget desc";
		$no=0;
		$res1=mysql_query($str1);
			
		while($bar1=mysql_fetch_object($res1))
		{
			  $no+=1;
			echo"<tr class=rowcontent>
			<td align=center>".$no."</td>
			<td align=right>".$bar1->tahunbudget."</td>
			<td align=left>".$bar1->kodeorg."</td>
			<td align=right>".$bar1->siapborong."</td>
			<td align=right>".$bar1->lebihborong."</td>
			<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->kodeorg."','".$bar1->siapborong."','".$bar1->lebihborong."');\"></td></tr>";
		}
	break;
default:	
	
	
/*case 'update':	
	$str="update ".$dbname.".bgt_borong_panen set siapborong='".$sb."',lebihborong='".$lb."'
	       where tahunbudget='".$thnbudget."' and kodeorg='".$kdafd."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
    $str="select * from ".$dbname.".bgt_borong_panen 
	       where tahunbudget='".$thnbudget."' and kodeorg='".$kdafd."'
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $sudahada="1";
		$pesan=$bar->tahunbudget."-".$bar->kodeorg."-".$bar->siapborong."-".$bar->lebihborong;
    }
    if($sudahada=="1"){
        echo " Gagal, data sudah ada: ".$pesan; exit;
    }

    $str="insert into ".$dbname.".bgt_borong_panen (`tahunbudget`,`kodeorg`,`siapborong`,`lebihborong`)
		values ('".$thnbudget."','".$kdafd."','".$sb."','".$lb."')";
		
	//	exit ("Error:$str");
		
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".bgt_borong_panen 
	       where tahunbudget='".$thnbudget."' and kodeorg='".$kdafd."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".bgt_borong_panen order by tahunbudget";
if($res1=mysql_query($str1))
{
while($bar1=mysql_fetch_object($res1))
{
		$no+=1;
		echo"<tr class=rowcontent>
			<td align=center>".$no."</td>
			<td align=right>".$bar1->tahunbudget."</td>
			<td align=center>".$bar1->kodeorg."</td>
			<td align=right>".$bar1->siapborong."</td>
			<td align=right>".$bar1->lebihborong."</td>			
		<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->kodeorg."','".$bar1->siapborong."','".$bar1->lebihborong."');\"></td></tr>";
}*/	 
}
?>






<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$proses=$_POST['proses'];

$loksi=$_POST['loksi'];
$ipAdd=$_POST['ipAdd'];
$idRemote=$_POST['idRemote'];
$ipAdd=$_POST['ipAdd'];
$userName=$_POST['userName'];
$passwrd=$_POST['passwrd'];
$port=$_POST['port'];
$dbnm=$_POST['dbnm'];


	switch($proses)
	{
		case'LoadData':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".setup_remotetimbangan order by `id` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$str="select * from ".$dbname.".setup_remotetimbangan order by `id` desc limit ".$offset.",".$limit."";
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
			//echo $minute_selesai; exit();
			echo"<tr class=rowcontent id='tr_".$no."'>
			<td>".$no."</td>
			<td>".$bar->lokasi."</td>
			<td>".$bar->ip."</td>
			<td>".$bar->username."</td>
			<td>".$bar->password."</td>
			<td>".$bar->port."</td>
			<td>".$bar->dbname."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->id."');\"><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldata('".$bar->id."');\"></td>
			</tr>";
			}	 	 
			echo"
			<tr><td colspan=8 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
			<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";     	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	
		break;
		case'insert':
		if(($loksi=='')||($ipAdd=='')||($userName=='')||($port=='')||($passwrd=='')||($dbnm==''))
		{
			echo"warning: Lengkapi Form Inputan";
			exit();
		}
		if(!preg_match("/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",$ipAdd))
		{
			echo"warning:Please Input Valid IP Address";
			exit();
		}

			$sIns="insert into ".$dbname.".setup_remotetimbangan (lokasi, ip, username, password, port,dbname) values ('".$loksi."', '".$ipAdd."', '".$userName."', '".$passwrd."', '".$port."','".$dbnm."')";
			if(mysql_query($sIns))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		
		break;
		case'showData':
		$sql="select* from ".$dbname.".setup_remotetimbangan where id='".$idRemote."'";
		//echo"warning".$sql;id, lokasi, ip, username, password, port
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_assoc($query);
		echo $res['id']."###".$res['lokasi']."###".$res['ip']."###".$res['username']."###".$res['password']."###".$res['port']."###".$res['dbname'];
		break;
		case'update':
		if(($loksi=='')||($ipAdd=='')||($userName=='')||($port=='')||($passwrd=='')||($dbnm==''))
		{
			echo"warning: Lengkapi Form Inputan";
			exit();
		}
		if(!preg_match("/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",$ipAdd))
		{
			echo"warning:Please Input Valid IP Address";
			exit();
		}
			$sUpd="update ".$dbname.".setup_remotetimbangan set   lokasi='".$loksi."', ip='".$ipAdd."', username='".$userName."', password='".$passwrd."', port='".$port."',dbname='".$dbnm."'  where  id='".$idRemote."'";
		//	echo "warning:".$sUpd;exit();
			if(mysql_query($sUpd))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		
		break;
		case'delData':
		$sDel="delete from ".$dbname.".setup_remotetimbangan where id='".$idRemote."'";
		if(mysql_query($sDel))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
		
	
		default:
		break;
	}

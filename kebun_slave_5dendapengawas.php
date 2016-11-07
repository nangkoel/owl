<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$kode=$_POST['kode'];
$nama=$_POST['nama'];
$jabatan=$_POST['jabatan'];
$denda=$_POST['denda'];
$method=$_POST['method'];
?>

<?php
switch($method)
{
	

	case 'insert':
		$i="insert into ".$dbname.".kebun_5dendapengawas (kode,nama,jabatan,denda,updateby)
		values ('".$kode."','".$nama."','".$jabatan."','".$denda."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
	//exit("Error:ASD");
		
		$i="update ".$dbname.".kebun_5dendapengawas set nama='".$nama."',jabatan='".$jabatan."',denda='".$denda."',updateby='".$_SESSION['standard']['userid']."'
		 where kode='".$kode."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
case'loadData':
	echo"
	<div id=container>
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
			 	 <td align=center>".$_SESSION['lang']['nourut']."</td>
				 <td align=center>".$_SESSION['lang']['kode']."</td>
				 <td align=center>".$_SESSION['lang']['nama']."</td>
				 <td align=center>".$_SESSION['lang']['jabatan']."</td>
				 <td align=center>".$_SESSION['lang']['rp']." ".$_SESSION['lang']['denda']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		
		
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_5dendapengawas";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".kebun_5dendapengawas order by kode asc  limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['kode']."</td>";
			echo "<td align=left>".$d['nama']."</td>";
			echo "<td align=left>".$d['jabatan']."</td>";
			echo "<td align=right>".number_format($d['denda'])."</td>";
			echo "<td align=center>
				<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['kode']."','".$d['nama']."','".$d['jabatan']."','".$d['denda']."');\">
				<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kode']."');\"></td>";
			echo "</tr>";
		}
		echo"
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".kebun_5dendapengawas where kode='".$kode."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

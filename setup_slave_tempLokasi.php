<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$kar=$_POST['kar'];
$kdorg=$_POST['kdorg'];
$method=$_POST['method'];

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$lokKar=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas');
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

?>

<?php
switch($method)
{



	case 'update':
		$i="UPDATE  ".$dbname.".`setup_temp_lokasitugas` SET  
			`kodeorg` =  '".$kdorg."' WHERE  `karyawanid` ='".$kar."'";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;


	case 'insert':
		$i="insert into ".$dbname.".setup_temp_lokasitugas (karyawanid,kodeorg)
		values ('".$kar."','".$kdorg."')";
		//exit("Error.$sDel2");
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
				 <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
				 <td align=center>".$_SESSION['lang']['lokasitugas']." Asli</td>
				 <td align=center>".$_SESSION['lang']['lokasitugas']." Sementara</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		$limit=30;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		//cleanup data first
                $strcek="SELECT a.karyawanid FROM ".$dbname.".setup_temp_lokasitugas a LEFT JOIN ".$dbname.".datakaryawan b ON a.`karyawanid`=b.`karyawanid` WHERE kodeorg=lokasitugas";
                $rescek=mysql_query($strcek) or die(mysql_error());
		while($bar=mysql_fetch_object($rescek)){
                    $del=deleteQuery($dbname, setup_temp_lokasitugas, "karyawanid=".$bar->karyawanid);
                    mysql_query($del);
		}

		
		$ql2="select count(*) as jmlhrow from ".$dbname.".setup_temp_lokasitugas";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
	
		
		$i="select * from ".$dbname.".setup_temp_lokasitugas limit ".$offset.",".$limit."";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$nmKar[$d['karyawanid']]."</td>";
			echo "<td align=left>".$nmOrg[$d['kodeorg']]."</td>";
			echo "<td align=left>".$nmOrg[$lokKar[$d['karyawanid']]]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' 
			onclick=\"edit('".$d['karyawanid']."','".$d['kodeorg']."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['karyawanid']."');\"></td>";
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
		$i="delete from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$kar."' ";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

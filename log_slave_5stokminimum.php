<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$id=$_POST['id'];
$kodegudang=$_POST['kdOrg'];
$kodebarang=$_POST['kodebarang'];
$nmcari=$_POST['nmcari'];
$minstok=$_POST['minstok'];
$maxstok=$_POST['maxstok'];
$method=$_POST['method'];
$optNmKar=makeOption($dbname, 'user', 'karyawanid,namauser');
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
?>

<?php
switch($method)
{
	case 'insert':
		$i="insert into ".$dbname.".log_5stokminimum (kodegudang,kodebarang,minstok,maxstok,updateby)
		values ('".$kodegudang."','".$kodebarang."','".$minstok."','".$maxstok."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
	//exit("Error:ASD");
		
		$i="update ".$dbname.".log_5stokminimum set minstok=".$minstok.",maxstok=".$maxstok.",updateby='".$_SESSION['standard']['userid'].
                "' where kodegudang='".$kodegudang."' and kodebarang='".$kodebarang."'";
		//exit("Error.$i");
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
				 <td align=center>".$_SESSION['lang']['kodegudang']."</td>
				 <td align=center>".$_SESSION['lang']['materialcode']."</td>
				 <td align=center>".$_SESSION['lang']['materialname']."</td>
				 <td align=center>".$_SESSION['lang']['minstok']."</td>
				 <td align=center>".$_SESSION['lang']['maxstok']."</td>
				 <td align=center>".$_SESSION['lang']['updateby']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		$limit=15;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		if ($kodegudang!='') $whr.=" and kodegudang='".$kodegudang."'";
		if ($nmcari!='') $whr.=" and namabarang like '%".$nmcari."%'";
		$ql2="select count(*) as jmlhrow from ".$dbname.".log_5stokminimum a left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where 1=1".$whr;// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select a.* from ".$dbname.".log_5stokminimum a left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang where 1=1".$whr." limit ".$offset.",".$limit."";
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['kodegudang']."</td>";
			echo "<td align=left>".$d['kodebarang']."</td>";
			echo "<td align=left>".$optNmBarang[$d['kodebarang']]."</td>";
			echo "<td align=right>".$d['minstok']."</td>";
			echo "<td align=right>".$d['maxstok']."</td>";
			echo "<td align=left>".$optNmKar[$d['updateby']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$d['kodegudang']."','".$d['kodebarang']."','".$d['minstok']."','".$d['maxstok']."','".$optNmBarang[$d['kodebarang']]."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodegudang']."','".$d['kodebarang']."');\"></td>";
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
		$i="delete from ".$dbname.".log_5stokminimum where kodegudang='".$kodegudang."' and kodebarang='".$kodebarang."'";
		//exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$id=$_POST['id'];
$kodeorg=$_POST['kodeorg'];
$hasil=$_POST['hasil'];
$lebihbasis=$_POST['lebihbasis'];
$rupiah=$_POST['rupiah'];
$premirajin=$_POST['premirajin'];
$method=$_POST['method'];
$optNmKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
?>

<?php
switch($method)
{
	case 'insert':
		$i="insert into ".$dbname.".kebun_5premipanen (kodeorg,hasilkg,lebihbasiskg,rupiah,premirajin,updateby)
		values ('".$kodeorg."','".$hasil."','".$lebihbasis."','".$rupiah."','".$premirajin."','".$_SESSION['standard']['userid']."')";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'update':
	//exit("Error:ASD");
		
		$i="update ".$dbname.".kebun_5premipanen set kodeorg='".$kodeorg."',hasilkg=".$hasil.",lebihbasiskg=".$lebihbasis.",rupiah='".$rupiah."',premirajin='".$premirajin."',updateby='".$_SESSION['standard']['userid'].
                "' where id=".$id." ";
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
				 <td align=center>".$_SESSION['lang']['kodeorg']."</td>
				 <td align=center>".$_SESSION['lang']['hasil']."</td>
				 <td align=center>".$_SESSION['lang']['lebihbasis']."</td>
				 <td align=center>".$_SESSION['lang']['rp']."</td>
				 <td align=center>".$_SESSION['lang']['premirajin']."</td>
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
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_5premipanen";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".kebun_5premipanen limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['kodeorg']."</td>";
			echo "<td align=right>".$d['hasilkg']."</td>";
			echo "<td align=right>".$d['lebihbasiskg']."</td>";
			echo "<td align=right>".number_format($d['rupiah'])."</td>";
			echo "<td align=right>".number_format($d['premirajin'])."</td>";
			echo "<td align=left>".$optNmKar[$d['updateby']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$d['id']."','".$d['kodeorg']."','".$d['hasilkg']."','".$d['lebihbasiskg']."','".$d['rupiah']."','".$d['premirajin']."');\">
			<!--<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['id']."');\">--></td>";
			echo "</tr>";
		}
//		echo"
//		<tr class=rowheader><td colspan=18 align=center>
//		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
//		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//		</td>
//		</tr>";
		echo"</tbody></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".kebun_5premipanen where id='".$id."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

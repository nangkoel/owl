<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$regional=$_POST['regional'];
$kodekegiatan=$_POST['kodekegiatan'];
$volume=$_POST['volume'];
$rupiah=$_POST['rupiah'];
$tipe=$_POST['tipe'];
$jumlahhari=$_POST['jumlahhari'];
$method=$_POST['method'];

$optTipe=array('D'=>'Dump Truck','F'=>'Fuso');

$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');

?>

<?php
switch($method)
{



	case 'update':
		$i="UPDATE  ".$dbname.".`kebun_5premimuat` SET  
			`rupiah` =  '".$rupiah."',
			`tipe` =  '".$tipe."',
			`jumlahhari` =  '".$jumlahhari."',
			`updateby` =  '".$_SESSION['standard']['userid']."' WHERE  `kodekegiatan` ='".$kodekegiatan."'  '' AND  `volume` ='".$volume."' ";
		//exit("Error.$sDel2");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;


	case 'insert':
		$i="insert into ".$dbname.".kebun_5premimuat (regional,kodekegiatan,volume,rupiah,tipe,jumlahhari,updateby)
		values ('".$regional."','".$kodekegiatan."','".$volume."','".$rupiah."','".$tipe."','".$jumlahhari."','".$_SESSION['standard']['userid']."')";
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
				 <td align=center>".$_SESSION['lang']['regional']."</td>
				 <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['volume']."</td>
				 <td align=center>".$_SESSION['lang']['rupiahsatuan']."</td>
				 <td align=center>".$_SESSION['lang']['tipe']."</td>
				 <td align=center>".$_SESSION['lang']['jumlahhari']."</td>
				 <td align=center>".$_SESSION['lang']['updateby']."</td>
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
		
		

		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_5premimuat  where regional='".$_SESSION['empl']['regional']."'   ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".kebun_5premimuat where regional='".$_SESSION['empl']['regional']."'  limit ".$offset.",".$limit."";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['regional']."</td>";
			echo "<td align=left>".$d['kodekegiatan']."</td>";
			echo "<td align=left>".$nmKeg[$d['kodekegiatan']]."</td>";
			echo "<td align=right>".$d['volume']."</td>";
			echo "<td align=right>".number_format($d['rupiah'])."</td>";
			echo "<td align=right>".$optTipe[$d['tipe']]."</td>";
			echo "<td align=right>".$d['jumlahhari']."</td>";
			echo "<td align=left>".$nmKar[$d['updateby']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' 
			onclick=\"edit('".$d['regional']."','".$d['kodekegiatan']."','".$d['volume']."','".$d['rupiah']."','".$d['tipe']."','".$d['jumlahhari']."');\">
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodekegiatan']."','".$d['volume']."');\"></td>";
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
		$i="delete from ".$dbname.".kebun_5premimuat where kodekegiatan='".$kodekegiatan."' and volume='".$volume."' ";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

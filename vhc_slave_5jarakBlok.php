<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$divisi=$_POST['divisi'];
$kodeblok=$_POST['kodeblok'];
$regional=$_POST['regional'];
$jarak=$_POST['jarak'];
$divisiSch=$_POST['divisiSch'];


$method=$_POST['method'];

$nmOrg=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');

$nmid=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmen=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan1');
?>

<?php
switch($method)
{
	
	case 'getBlok':
		$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select kodeorg from ".$dbname.".setup_blok where kodeorg like '%".$divisi."%'";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optBlok.="<option value='".$d['kodeorg']."'>".$d['kodeorg']."</option>";
		}
		echo $optBlok;
	break;
	
	case 'insert':
		$i="insert into ".$dbname.".vhc_5jarakblok (regional,kodeorg,kodeblok,jarak,updateby)
		values ('".$regional."','".$divisi."','".$kodeblok."','".$jarak."','".$_SESSION['standard']['userid']."')";
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
				 <td align=center>".$_SESSION['lang']['divisi']."</td>
				 <td align=center>".$_SESSION['lang']['kodeblok']."</td>
				 <td align=center>".$_SESSION['lang']['jarak']."</td>
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
		
		

		if($divisiSch!='')
			$divisiSch="and kodeblok like '%".$divisiSch."%'";
		else
			$divisiSch="and kodeblok!='' ";
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_5jarakblok  where regional='".$_SESSION['empl']['regional']."'  ".$divisiSch."  ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".vhc_5jarakblok where regional='".$_SESSION['empl']['regional']."' ".$divisiSch."  limit ".$offset.",".$limit."";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
		/*$i="select * from ".$dbname.".kebun_5psatuan where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		while($d=mysql_fetch_assoc($n))
		{*/
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['regional']."</td>";
			echo "<td align=right>".$d['kodeorg']."</td>";
			echo "<td align=right>".$d['kodeblok']."</td>";
			echo "<td align=right>".number_format($d['jarak'])."</td>";
			echo "<td align=left>".$nmOrg[$d['updateby']]."</td>";
			echo "<td align=center>
			<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodeblok']."');\"></td>";
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
		$i="delete from ".$dbname.".vhc_5jarakblok where kodeblok='".$kodeblok."' ";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

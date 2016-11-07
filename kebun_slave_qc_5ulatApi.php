<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$ulat=$_POST['ulat'];
$kret=$_POST['kret'];
$maxu=$_POST['maxu'];
$minu=$_POST['minu'];
$method=$_POST['method'];

$arrNm=array("jlhdarnatrima"=>"Darna Trima","jlhsetothosea"=>"Setothosea Asigna","jlhsetothosea"=>"Setora Nitens","jlhulatkantong"=>"Ulat Kantong");


$optUlat="<option value='jlhdarnatrima'>Darna Trima</option>";	
$optUlat.="<option value='jlhsetothosea'>Setothosea Asigna</option>";	
$optUlat.="<option value='jlhsetoranitens'>Setora Nitens</option>";	
$optUlat.="<option value='jlhulatkantong'>Ulat Kantong</option>";	
?>

<?php
switch($method)
{
	

	case 'insert':
		$i="insert into ".$dbname.".kebun_qc_5ulatapi (ulat,kret,maxu,minu,updateby)
		values ('".$ulat."','".$kret."','".$maxu."','".$minu."','".$_SESSION['standard']['userid']."')";
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
			 <td align=center>No</td>
			 	 <td align=center>Ulat</td>
				 <td align=center>Kreteria</td>
				 <td align=center>Minimal</td>
				 <td align=center>Maksimal</td>
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
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_5ulatapi";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".kebun_qc_5ulatapi  limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$arrNm[$d['ulat']]."</td>";
			echo "<td align=left>".$d['kret']."</td>";
			echo "<td align=left>".$d['maxu']."</td>";
			echo "<td align=left>".$d['minu']."</td>";
			
			echo "<td align=center>
				
				<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['ulat']."','".$d['kret']."');\"></td>";
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
		$i="delete from ".$dbname.".kebun_qc_5ulatapi where ulat='".$ulat."' and kret='".$kret."'";
		
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>

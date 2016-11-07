<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

?>	

<?php		
$kodeorg=$_POST['kodeorg'];
$produk=$_POST['produk'];
$id=$_POST['id'];
$inp=$_POST['inp'];
$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$tgl=tanggalsystem($_POST['tgl']);
$tglsch=tanggalsystem($_POST['tglsch']);

$kodeorgEdit=$_POST['kodeorgEdit'];
$tglEdit=tanggalsystem($_POST['tglEdit']);
$idEdit=$_POST['idEdit'];
$inpEdit=$_POST['inpEdit'];


$kodeorgLap=$_POST['kodeorgLap'];
$produkLap=$_POST['produkLap'];
$tglLap=tanggalsystem($_POST['tglLap']);
if($method=='excel')
{
	$kodeorgLap=$_GET['kodeorgLap'];
	$tglLap=tanggalsystem($_GET['tglLap']);
	$produkLap=$_GET['produkLap'];
}
	
$arrProduk=makeOption($dbname,'pabrik_5kelengkapanloses','id,produk');	
$arrItem=makeOption($dbname,'pabrik_5kelengkapanloses','id,namaitem');	
$arrSatuan=makeOption($dbname,'pabrik_5kelengkapanloses','id,satuan');
$arrStandard=makeOption($dbname,'pabrik_5kelengkapanloses','id,standard');	

?>

<?php


$stream="".$_SESSION['lang']['kelengkapanloses']."<br />Tanggal : ".tanggalnormal($tglLap)."";
			
if($method=='excel')
{
	$border="border='1'";
	$bgcolor="bgcolor=#CCCCCC";
}
else	
{
	$border="border='0'";
	$bgcolor="bgcolor=#FFFFFF ";
}

$stream.="<table cellspacing='1' class='sortable'  ".$border.">";
$stream.="<thead class=rowheader>
	  <tr>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['nourut']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['kodeorg']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['tanggal']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['produk']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['namabarang']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['satuan']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['standard']."</td>
		<td align=center ".$bgcolor.">".$_SESSION['lang']['realisasi']."</td>
	  </tr>
</thead>
<tbody>";


$i="select * from ".$dbname.".pabrik_kelengkapanloses where kodeorg='".$kodeorgLap."' and tanggal='".$tglLap."'
			and id in (select id from ".$dbname.".pabrik_5kelengkapanloses where produk='".$produkLap."')";
			
			//exit("Error:$i");


$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$no+=1;
	$stream.="<tr ".$border."  class=rowcontent>";
		$stream.="<td align=center>".$no."</td>";
		$stream.="<td align=left>".$d['kodeorg']."</td>";
		$stream.="<td align=left>".tanggalnormal($d['tanggal'])."</td>";
		$stream.="<td align=right>".$arrProduk[$d['id']]."</td>";//arrproduk
		$stream.="<td align=left>".$arrItem[$d['id']]."</td>";//arritem
		$stream.="<td align=left>".$arrSatuan[$d['id']]."</td>";//arrsatuan
		$stream.="<td align=left>".$arrStandard[$d['id']]."</td>";//arrstandard
		$stream.="<td align=right>".$d['nilai']."</td>";
	$stream.="</tr>";		
}
			   


switch($method)
{
	######HTML
	case 'preview':
	//exit("Error:masuk");
	
		echo $stream;
		
    break;

######EXCEL	
	case 'excel':

		$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Kelengkapan_Loses".$tglSkrg;
		if(strlen($stream)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}           
		break;
	
	

	
	case'getForm':
	
		$i="select distinct namaitem,id from ".$dbname.".pabrik_5kelengkapanloses where kodeorg='".$kodeorg."' and produk='".$produk."'";
		//exit("Error:$i");
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			$a.="
				<tr id=row".$no.">
					<td><input type=hidden value='".$d['id']."'  id=id$no disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\"></td>
					<td>".$d['namaitem']."</td> 
					<td>:</td>
					<td><input type=text id=inp$no onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"> %</td>
				</tr>";
		}
		
		echo $a;
		
		echo"
			<tr>
				<td colspan=3>
					<button class=mybutton onclick=saveAll(".$no.")>Simpan</button>
					<button class=mybutton onclick=cancel()>Hapus</button>
				<td>
			</tr>";//<input type=hidden id=method value='insert'>"
		echo"<input type=hidden id=jml onkeypress=\"return angka_doang(event);\"  value='".$no."' class=myinputtextnumber style=\"width:50px;\">";		
		
	
	break;
	
	
	
	case'savedata':
	
	//exit("Error:MASUK");
		$str="insert into ".$dbname.".pabrik_kelengkapanloses (`kodeorg`,`tanggal`,`id`,`nilai`,`updateby`)
		values ('".$kodeorg."','".$tgl."','".$id."','".$inp."','".$_SESSION['standard']['userid']."')";
		//exit("Error:$str");
		if(mysql_query($str))
		{//case berhasil kosongin aja
		}
		else
		{
			$str="update ".$dbname.".pabrik_kelengkapanloses set nilai='".$inp."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorg."' and tanggal='".$tgl."' and id='".$id."'";
			if(mysql_query($str))
			{//berhasil kosongin aja
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}
		
		}
	break;
	
	
	
	case'loadData'://
	echo"
	<div id=container>
		
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
			 	 <td align=center>".$_SESSION['lang']['nourut']."</td>
			 	 <td align=center>".$_SESSION['lang']['kodeorg']."</td>
				 <td align=center>".$_SESSION['lang']['tanggal']."</td>
				 
				 <td align=center>".$_SESSION['lang']['produk']."</td>
				 <td align=center>".$_SESSION['lang']['namabarang']."</td>
				 
				 <td align=center>".$_SESSION['lang']['satuan']."</td>
				 <td align=center>".$_SESSION['lang']['standard']."</td>
				 
				 <td align=center>".$_SESSION['lang']['realisasi']."</td>
				 <td align=center>".$_SESSION['lang']['updateby']."</td>
				 
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		
		if($tglsch!='')
			$tglsch="and tanggal='".$tglsch."'";
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".pabrik_kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$tglsch." ";// where kodeorg='".$kodeorg."' and periode='".$per."'
		//where kodeorg='".$kodeorg."' and periode='".$per."' order by lastupdate
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".pabrik_kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."'  ".$tglsch." order by tanggal desc limit ".$offset.",".$limit."";
		//exit("Error:$i");
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['kodeorg']."</td>";
			echo "<td align=left>".tanggalnormal($d['tanggal'])."</td>";
			
			echo "<td align=right>".$arrProduk[$d['id']]."</td>";//arrproduk
			echo "<td align=left>".$arrItem[$d['id']]."</td>";//arritem
			
			echo "<td align=left>".$arrSatuan[$d['id']]."</td>";//arrsatuan
			echo "<td align=left>".$arrStandard[$d['id']]."</td>";//arrstandard
			
			
			echo "<td align=right>".$d['nilai']."</td>";
			echo "<td align=left>".$optNmKar[$d['updateby']]."</td>";
					
			echo "<td align=left>
				<img src=images/application/application_edit.png class=resicon  caption='Edit' 
					onclick=\"edit('".$d['kodeorg']."','".tanggalnormal($d['tanggal'])."',
					'".$arrProduk[$d['id']]."','".$arrItem[$d['id']]."','".$d['nilai']."','".$d['id']."');\">
				<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodeorg']."','".tanggalnormal($d['tanggal'])."','".$d['id']."');\"></td>";		
			echo "</tr>";
		}
		echo"
		<tr class=rowheader><td colspan=43 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;
	
	
	case'delete':
		$i="delete from ".$dbname.".pabrik_kelengkapanloses where kodeorg='".$kodeorg."' and tanggal='".$tgl."' and id='".$id."'";
		//exit("Error:$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	case'update':
	
	//exit("Error:masuk");
		$i="update ".$dbname.".pabrik_kelengkapanloses set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorgEdit."' and tanggal='".$tglEdit."' and id='".$idEdit."'";
		//exit("Error:$i");
		if(mysql_query($i))
		{//berhasil kosongin aja
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
	break;
	

	
	
	
	
	
	default;
}
?>

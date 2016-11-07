<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5tempLokasi.js'></script>


<?php



$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct(a.karyawanid) as karyawanid,b.namakaryawan,b.nik from ".$dbname.".user a
	left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'  ";
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optKar.="<option value='".$d['karyawanid']."'>".$d['namakaryawan']." [".$d['nik']."]</option>";
}

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select * from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc";
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optOrg.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
}


$optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTipe.="<option value='D'>Dump Truck</option>";
$optTipe.="<option value='F'>Fuso</option>";								
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend>Temp Organization</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				
				
				
				<tr>
					<td>".$_SESSION['lang']['namakaryawan']."</td>
					<td>:</td>
					<td><select id=kar style=\"width:150px;\">".$optKar."</select></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['lokasitugas']."</td>
					<td>:</td>
					<td><select id=kdorg style=\"width:150px;\">".$optOrg."</select></td>
				</tr>
				
				
				
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR  ".$_SESSION['lang']['divisi']." : <select id=divisiSch style=\"width:100px;\" onchange=loadData()>".$optDivisi."</select>
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
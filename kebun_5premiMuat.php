<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5premiMuat.js'></script>


<?php
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select * from ".$dbname.".setup_kegiatan order by namakegiatan asc";
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optKeg.="<option value='".$d['kodekegiatan']."'>".$d['kodekegiatan']." ".$d['namakegiatan']."</option>";
}


$optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTipe.="<option value='D'>Dump Truck</option>";
$optTipe.="<option value='F'>Fuso</option>";								
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend>".$_SESSION['lang']['premimuat']."</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				
				<tr>
					<td>".$_SESSION['lang']['regional']."</td>
					<td>:</td>
					<td><input type=text  id=regional onkeypress=\"return char_only(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['kodekegiatan']."</td>
					<td>:</td>
					<td><select id=kodekegiatan style=\"width:150px;\">".$optKeg."</select></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['volume']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=volume onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['rupiahsatuan']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=rupiah onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['tipe']."</td>
					<td>:</td>
					<td><select id=tipe style=\"width:150px;\">".$optTipe."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['jumlahhari']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=jumlahhari onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
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
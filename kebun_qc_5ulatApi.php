<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_qc_5ulatApi.js'></script>


<?php

$optKret="<option value='ringan'>Ringan</option>";	
$optKret.="<option value='sedang'>Sedang</option>";	
$optKret.="<option value='berat'>Berat</option>";	

$optUlat="<option value='jlhdarnatrima'>Darna Trima</option>";	
$optUlat.="<option value='jlhsetothosea'>Setothosea Asigna</option>";	
$optUlat.="<option value='jlhsetoranitens'>Setora Nitens</option>";	
$optUlat.="<option value='jlhulatkantong'>Ulat Kantong</option>";	




	
		
	
								
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		
		
	
		echo"<legend>".$_SESSION['lang']['dendapengawas']."</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>Ulat</td>
					<td>:</td>
					<td><select id=ulat style='width:200px;'>".$optUlat."</select></td>
				</tr>
				
				<tr>
					<td>Kriteria</td>
					<td>:</td>
					<td><select id=kret style='width:200px;'>".$optKret."</select></td>
				</tr>
				
				
				<tr>
					<td>Minimal</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=minu onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>Maksimal</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=maxu onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>
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
//ISI UNTUK DAFTAR 
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
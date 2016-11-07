<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5dendapengawas.js'></script>


<?php

$optJabatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJabatan.="<option value='CONDUCTOR'>CONDUCTOR</option>";
$optJabatan.="<option value='KERANI'>KERANI</option>";
$optJabatan.="<option value='MANDOR'>MANDOR</option>";
$optJabatan.="<option value='RECORDER'>RECORDER</option>";


	
		
	
								
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		
		
	
		echo"<legend>".$_SESSION['lang']['dendapengawas']."</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['kode']."</td>
					<td>:</td>
					<td><input type=text maxlength=2 id=kode onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['nama']."</td> 
					<td>:</td>
					<td><input type=text  id=nama onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['jabatan']."</td> 
					<td>:</td>
					<td><select id=jabatan style=\"width:150px;\">".$optJabatan."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['rp']." ".$_SESSION['lang']['denda']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=denda onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>
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
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5premipanen.js'></script>

<?php
OPEN_BOX();
echo"<fieldset style='float:left;'>";
		if($_SESSION['language']=='ID')
		echo"<legend>Premi Panen Bulanan</legend>";
		else
		echo"<legend>Premi Monthly Harvesting</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
                                        <input type=hidden id=dataid value=''>
					<td>".$_SESSION['lang']['kodeorg']."</td>
					<td>:</td>
                                        <td><input type=text id=kodeorg class=myinputtext style=\"width:150px;\" onkeypress=\"return tanpa_kutip(event);\" value=''/></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['hasil']."</td> 
					<td>:</td>
					<td><input type=text id=hasil onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;KG</td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['lebihbasis']."</td> 
					<td>:</td>
					<td><input type=text id=lebihbasis onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;KG</td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['rp']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=rupiah onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['premirajin']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=premirajin onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";


CLOSE_BOX();
?>



<?php
OPEN_BOX();
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
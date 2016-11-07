<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/vhc_5jarakBlok.js'></script>


<?php
		$optDivisi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$i="select * from ".$dbname.".organisasi where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."' order by namaorganisasi asc";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optDivisi.="<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi']."</option>";
		}

								
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend>".$_SESSION['lang']['jarak']." ".$_SESSION['lang']['blok']."</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				
				<tr>
					<td>".$_SESSION['lang']['regional']."</td>
					<td>:</td>
					<td><input type=text  id=regional onkeypress=\"return char_only(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['divisi']."</td>
					<td>:</td>
					<td><select id=divisi onchange=getBlok() style=\"width:150px;\">".$optDivisi."</select></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['kodeblok']."</td>
					<td>:</td>
					<td><select id=kodeblok style=\"width:150px;\">".$optBlok."</select></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['jarak']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=jarak onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
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
		".$_SESSION['lang']['divisi']." : <select id=divisiSch style=\"width:100px;\" onchange=loadData()>".$optDivisi."</select>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
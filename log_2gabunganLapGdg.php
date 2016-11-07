<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_gablapgud.js'></script>
<?php
include('master_mainMenu.php'); 
// ..FORM
OPEN_BOX('', 'Gabungan Laporan Gudang');
// ..divisi
	$optPrd=$optBrg=$optUnit 	= "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	
	$optUnit.= "<option value='".$_SESSION['empl']['regional']."'>".$_SESSION['empl']['regional']."</option>";

	$sUnit		= "select distinct periode
					from ".$dbname.".setup_periodeakuntansi
					where kodeorg 
					in (select kodeunit 
						from ".$dbname.".bgt_regional_assignment 
						where regional='".$_SESSION['empl']['regional']."') 
					order by periode desc";
	$qUnit 		= mysql_query($sUnit) or die(mysql_error());
	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		//$optUnit.= "<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
		$optPrd.= "<option value='".$rUnit['periode']."'>".$rUnit['periode']."</option>";
	}

// ..kelompok barang
    $optBrg=$optKlmpBrg = "<option value=''>".$_SESSION['lang']['all']."</option>";
	$sKlmpBrg = "select kode,kelompok1 
				from ".$dbname.".log_5klbarang";
	$qKlmpBrg = mysql_query($sKlmpBrg) or die(mysql_error());
	while ($rKlmpBrg = mysql_fetch_assoc($qKlmpBrg)) {
		$optKlmpBrg.= "<option value='".$rKlmpBrg['kode']."'>".$rKlmpBrg['kode']." - ".$rKlmpBrg['kelompok1']."</option>";
	}

	// ..form pilihan
		echo "<br />
			<fieldset style=width:350px; float:left;>
				<legend>Gabungan Laporan Gudang</legend>
				<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['divisi']."</td>
					<td>
						<select id=unitDt style='width:150px;'>".$optUnit."</select>
					</td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['periode']."</td>
					<td>
						<select id=periode style='width:150px;' onchange=hideById('printPanel2')>".$optPrd."</select>
					</td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['kelompokbarang']."</td>
					<td>
						<select id=klmpkBrg style='width:150px;' onchange=getBrg('printPanel2')>".$optKlmpBrg."</select>
					</td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['namabarang']."</td>
					<td>
						<select id=kdBrg style='width:150px;'>".$optBrg."</select>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<button class=mybutton onclick=getLaporanFisik2()>".$_SESSION['lang']['preview']."</button>
						<button class=mybutton onclick=fisikKeExcel(event,'log_slave_2getGabLapGdg.php')>".$_SESSION['lang']['excel']."</button>
					</td>
				</tr>
				</table>
			</fieldset>
			";
CLOSE_BOX();

// ..RESULT
OPEN_BOX('','Result:');
	echo "<span id=printPanel2 style='display:none;'>
			<img onclick=fisikKeExcel2(event,'log_slave_2getGabLapGdg.php') src=images/excel.jpg class=resicon title='MS.Excel'>
		</span>
		<div style='width:100%; height:359px; overflow:scroll;'  id=container>
			
		</div>
		";
CLOSE_BOX();
close_body();
?>
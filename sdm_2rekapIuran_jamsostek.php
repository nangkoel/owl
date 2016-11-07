<?php
//@Copy nangkoelframework

require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
	<script language=javascript src=js/zTools.js></script>
	<script language=javascript src=js/zReport.js></script>
	<!--<script language=javascript src='js/sdm_2rekapabsen.js'></script>-->
	<script language=javascript src='js/sdm_bpjs.js'></script>
	<link rel=stylesheet type=text/css href=style/zTable.css>
					<!-- javascript show hide tombol preview -->
					<script type="text/javascript">
						function gantiTombol() {
							pil=document.getElementById('pilForm');
							pil=pil.options[pil.selectedIndex].value;
							for(ada=0;ada<4;ada++){
								if(ada==parseInt(pil)){
									document.getElementById('tombol'+pil).style.display="block";		
								}else{
									document.getElementById('tombol'+ada).style.display="none";	
								}
							}
						}
					</script>
<?php
// ============= periode ==========
	$oPeriode = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sPeriode = "select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by periode desc";
	$qPeriode = mysql_query($sPeriode) or die(mysql_error());
	while ($rPeriode=mysql_fetch_assoc($qPeriode)) {
		$oPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
	}

// ============= Nama PT ==========
	$optPt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
		$sPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
	}else{
	    if($_SESSION['empl']['regional']=='KALIMANTAN'){
		$sPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi in ('SIL','SIP')";
	    } else {
		$sPt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' and kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
	    }
	}

	$qPt=mysql_query($sPt) or die(mysql_error($conn));
	while($rPt=mysql_fetch_assoc($qPt)){
		$optPt.="<option value='".$rPt['kodeorganisasi']."'>".$rPt['namaorganisasi']."</option>";
	}
	echo $rPt['kodeorganisasi'];

// ============= divisi ===========
	$oDivisi = "<option value=''>".$_SESSION['lang']['all']."</option>";
	$sDivisi = "select kodeunit,namaorganisasi from ".$dbname.".bgt_regional_assignment a 
				left join ".$dbname.".organisasi b on a.kodeunit=b.kodeorganisasi  
				where regional='".$_SESSION['empl']['regional']."' 
				order by namaorganisasi";
	$qDivisi = mysql_query($sDivisi) or die(mysql_error());
	while ($rDivisi=mysql_fetch_assoc($qDivisi)) {
		$oDivisi.="<option value=".$rDivisi['kodeunit'].">".$rDivisi['namaorganisasi']."</option>";	
	}

	$arr= "##periode##pt##divisi";
	// ========= array tombol preview ==========
	//$arrPil = array("MutasiUpah", "KaryKeluar", "KaryBaru", "RincianIuran");
	$arrPil = array("MutasiUpah", "KaryawanKeluar", "KaryawanBaru", "RincianIuran");
	$optPil = "<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	foreach ($arrPil as $rowPil => $lstPil) {
		$optPil.="<option value='".$rowPil."'>".$lstPil."</option>";	
	}
?>

	
	<div>
		<fieldset style="float:left;">
			<legend>
				<b>Rekapitulasi Iuran Jamsostek</b>
			</legend>
			<table cellspacing="1" border="0">
				<tr>
					<td>
						<label>
							<?php echo $_SESSION['lang']['periode']; ?>
						</label>
					</td>
					<td>
						<select id="periode" name="periode" style="width:150px">
								<?php echo $oPeriode; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label>
							<?php echo $_SESSION['lang']['pt']?>
						</label>
					</td>
					<td>
						<select id="pt" name="pt" style="width:150px" onchange='filterK()'>
							<?php  echo $optPt; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label>
							<?php echo $_SESSION['lang']['divisi']?>
						</label>
					</td>
					<td>
						<select id="divisi" name="divisi" style="width:150px">
							<?php  echo $oDivisi; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label>Laporan</label></td>
					<td>
						<select id='pilForm' onchange='gantiTombol()'>
							<?php echo $optPil; ?>
						</select>
					</td>
				</tr>
				<tr height="10">
					<td colspan="1">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<div id="tombol0" style="display:none;">
							<button onclick="zPreview('sdm_slave_2mutasiUpah_jamsostek','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
							<button onclick="zExcel(event,'sdm_slave_2mutasiUpah_jamsostek.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
							<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">
							<?php echo $_SESSION['lang']['cancel']?>
						</div>
						<div id="tombol1" style="display:none;">
							<button onclick="zPreview('sdm_slave_2daftarKaryKeluar_jamsostek','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
							<button onclick="zExcel(event,'sdm_slave_2daftarKaryKeluar_jamsostek.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
							<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">
							<?php echo $_SESSION['lang']['cancel']?>
						</div>
						<div id="tombol2" style="display:none;">
							<button onclick="zPreview('sdm_slave_2iuranKaryBaru_jamsostek','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
							<button onclick="zExcel(event,'sdm_slave_2iuranKaryBaru_jamsostek.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
							<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">
							<?php echo $_SESSION['lang']['cancel']?>
						</button>
						</div>
						<div id="tombol3" style="display:none;">
							<button onclick="zPreview('sdm_slave_2rekapIuran_jamsostek','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
							<button onclick="zExcel(event,'sdm_slave_2rekapIuran_jamsostek.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
							<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">
							<?php echo $_SESSION['lang']['cancel']?>
						</button>
						</div>
					</td>
				</tr>
			</table>		
		</fieldset>
	</div>
	<div style="margin-bottom: 30px;">
	</div>
	<fieldset style='clear:both'>
		<legend>
			<b>Print Area</b>
		</legend>
		<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
			<?php
				//echo"<pre>";
				//print_r($_SESSION);
				//echo"</pre>";
			?>
		</div>
	</fieldset>
<?php

CLOSE_BOX();
echo close_body();
?>
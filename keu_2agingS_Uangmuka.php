<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
	<script language=javascript>
		function batal()
		{
			location.reload();	
		}
	</script>
<?php

// ..option for pilihdata & seluruhnya, copy if needed
	$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	//$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
// ..

// ..get namaorganisasi
	if($_SESSION['empl']['tipelokasitugas']=='HOLDING') {
		$sql = "SELECT kodeorganisasi,namaorganisasi 
				FROM ".$dbname.".organisasi 
				where length(kodeorganisasi)=4 
				ORDER BY namaorganisasi";
	} else if($_SESSION['empl']['tipelokasitugas']=='KANWIL') {
		$sql = "SELECT kodeorganisasi,namaorganisasi 
				FROM ".$dbname.".organisasi 
				where kodeorganisasi in (select kodeunit 
										from ".$dbname.".bgt_regional_assignment
			 							where regional='".$_SESSION['empl']['regional']."') 
				and tipe!='HOLDING'
				ORDER BY namaorganisasi";
	} else {
		$sql = "SELECT kodeorganisasi,namaorganisasi 
				FROM ".$dbname.".organisasi 
				where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
	}
	$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
	while ($data=mysql_fetch_assoc($qry)) {
		$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
	}

// ..get namakaryawan
	$sql = "SELECT karyawanid,namakaryawan 
			FROM ".$dbname.".datakaryawan 
			where bagian in ('FIN','ACC') 
			and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
	$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
	$optKar.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	while ($data=mysql_fetch_assoc($qry)) {
		$optKar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']."</option>";
	}			

// ..get namaakun(Tipe Uang Muka) #update AND namaakun like 'UANG MUKA%' 
	/*$a="SELECT noakun, namaakun
		FROM ".$dbname.".keu_5akun
		WHERE LEFT( noakun, 4 ) 
		IN ('1180')
		AND namaakun like 'UANG MUKA%' 
		AND detail =1";*/
	$a="SELECT noakun, namaakun
		FROM ".$dbname.".keu_5akun
		WHERE noakun like '1180%'
		AND namaakun like 'UANG MUKA%' 
		AND detail =1";
	$b=mysql_query($a) ;
	$optTipe.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	while($c=mysql_fetch_assoc($b)) {
		$optTipe.="<option value=".$c['noakun'].">".$c['namaakun']."</option>";
	}

	$arrPil=array("0"=>"Belum Lunas","1"=>"Lunas");
	foreach($arrPil as $rwPl=>$pil){
		$optPil.="<option value='".$rwPl."'>".$pil."</option>";
	}
?>

<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##noakun##tgl##dibuat##diperiksa##pilId";	

// ..FORM PILIH DATA
		echo "<fieldset style='float:left;'>
				<legend>
					<b>Aging Schedule Uang Muka</b>
				</legend>
				<table>
					<tr>
						<td>".$_SESSION['lang']['kodeorg']."</td>
						<td>:</td>
						<td><select id=kdorg style='width:200px;'>".$optorg."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['tipe']." Uang Muka</td>
						<td>:</td>
						<td><select id=noakun style='width:200px;'>".$optTipe."</select></td>
					</tr>
					<tr>
						<td>Sampai ".$_SESSION['lang']['tanggal']."</td>
						<td>:</td>
						<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
						</td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['dibuat']."</td>
						<td>:</td>
						<td><select id=dibuat style='width:200px;'>".$optKar."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['diperiksa']."</td>
						<td>:</td>
						<td><select id=diperiksa style='width:200px;'>".$optKar."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['status']."</td>
						<td>:</td>
						<td><select id=pilId style='width:200px;'>".$optPil."</select></td>
					</tr>	
					<tr>
						<td colspan=100>&nbsp;</td>
					</tr>
					<tr>
						<td colspan=100>
						<button onclick=zPreview('keu_slave_2agingS_Uangmuka','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
						<button onclick=zExcel(event,'keu_slave_2agingS_Uangmuka.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
						<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
						</td>
					</tr>
				</table>
		</fieldset>";
// ..end of FORM PILIH

// ..PRINT AREA
	echo 	"<fieldset style='clear:both'>
				<legend>
					<b>".$_SESSION['lang']['printArea']."</b>
				</legend>
				<div id='printContainer'></div>
			</fieldset>";
// ..end of PRINT AREA

CLOSE_BOX();
echo close_body();
?>
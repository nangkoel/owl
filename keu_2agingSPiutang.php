<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
	<script language=javascript>
		function batal(){
			location.reload();	
		}
		function getPeriode(){
            kdorg=document.getElementById('kdorg');
            kdorg=kdorg.options[kdorg.selectedIndex].value;
			param='kodeorg='+kdorg;
			tujuan='keu_slave_2agingSPiutang.php';
			post_response_text(tujuan+'?proses=getPeriode', param, respog);
			function respog()
			{
					  if(con.readyState==4)
					  {
							if (con.status == 200) {
								busy_off();
								if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
								}
								else {
									//alert(con.responseText);
									document.getElementById('tgl').innerHTML=con.responseText;
									document.getElementById('tgl2').innerHTML=con.responseText;
								}
							}
							else {
								busy_off();
								error_catch(con.status);
							}
					  }	
			 }  
		}
		function copPrd(){
			tgl=document.getElementById('tgl');
            tgl=tgl.options[tgl.selectedIndex].value;
			tgl2=document.getElementById('tgl2');
			for(a=0;a<tgl2.length;a++){
				if(tgl2.options[a].value==tgl)
					{
						tgl2.options[a].selected=true;
					}
			}
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


$ArrAkun=array('01'=>'Piutang Bensin','02'=>'Piutang Alat Kerja','03'=>'Piutang Lain-lain');
$optPer=$optAkun.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach ($ArrAkun as $ArrPil => $valPil) {
	$optAkun.="<option value='".$ArrPil."'>".$valPil."</option>";
	/*if ($ArrPil=='02') {
		$sDet = "select replace(name,'Angsuran','Piutang') as name,id from ".$dbname.".sdm_ho_component where pinjamanid=1";
		$qDet = mysql_query($sDet) or die (mysql_error());
		while ($rDet = mysql_fetch_assoc($qDet)) {
			$optAkun.="<option value='".$ArrPil.".".$rDet['id']."'>".$rDet['name']."</option>";

		}
        unset($ArrAkun[$arrPil]);
	}*/
    
}

	// .. filter lunas dan belum lunas
	$arrPil=array("0"=>"Belum Lunas","1"=>"Lunas");
	foreach($arrPil as $rwPl=>$pil){
		$optPil.="<option value='".$rwPl."'>".$pil."</option>";
	}




?>

<?php
OPEN_BOX();
$arr="##kdorg##noakun##tgl##dibuat##diperiksa##pilId##tgl2";		
// ..FORM PILIH DATA
		echo "<fieldset style='float:left;'>
				<legend>
					<b>Laporan Aging Schedule Piutang</b>
				</legend>
				<table>
					<tr>
						<td>".$_SESSION['lang']['kodeorg']."</td>
						<td>:</td>
						<td><select id=kdorg style='width:200px;' onchange='getPeriode()'>".$optorg."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['tipe']." Pinjaman</td>
						<td>:</td>
						<td><select id=noakun style='width:200px;'>".$optAkun."</select></td>
					</tr>
					<tr  style=display:none>
						<td>".$_SESSION['lang']['periode']." ".$_SESSION['lang']['dari']."</td>
						<td>:</td>
						<td>
						<select id=tgl style='width:200px;' onchange=copPrd() style=display:none>".$optPer."</select>
						<!--<input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >-->
						</td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['periode']." ".$_SESSION['lang']['sampai']."</td>
						<td>:</td>
						<td>
						<select id=tgl2 style='width:200px;'>".$optPer."</select>
						<!--<input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >-->
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
					<tr style='display:none'>
						<td>".$_SESSION['lang']['status']."</td>
						<td>:</td>
						<td><select id=pilId style='width:200px;'>".$optPil."</select></td>
					</tr>		
					<tr>
						<td colspan=100>&nbsp;</td>
					</tr>
					<tr>
						<td colspan=100>
						<button onclick=zPreview('keu_slave_2agingSPiutang','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
						<button onclick=zExcel(event,'keu_slave_2agingSPiutang.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
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
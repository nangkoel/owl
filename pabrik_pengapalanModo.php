<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/pabrik_pengapalanModo.js'></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/iReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<?php

//- disabled dengan format: H01MYYYY, YYYY adalah no.urut diambil dari table_timbangan no transaksi where notransaksi like 'H01M%' desc limit1, kemudian no +1

$a="select notransaksi from ".$dbname.".pabrik_timbangan where notransaksi like 'H01M%' order by notransaksi desc limit 1";
$b=mysql_query($a) or die (mysql_error($conn));
$c=mysql_fetch_assoc($b);
	$noLama=substr($c['notransaksi'],4,4);	
if($noLama=='')
	$noLama=1;
else
	$noLama++;
$nomor=addZero($noLama,4);
$notranBaru="H01M".$nomor;

#bentuk nokontrak sisa
$optNoKontrak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";//kodept
$d="select kuantitaskontrak,selisih,nokontrak from ".$dbname.".pabrik_kontrakjual_vs_timbangan where kodept='HIP' and selisih is NULL or selisih>0 ";
$e=mysql_query($d) or die (mysql_error($conn));
while($f=mysql_fetch_assoc($e))
{
	if($f['selisih']=='' or $f['selisih']==null)
	{
		$sisa=number_format($f['kuantitaskontrak']);
	}
	else
	{
		$sisa=number_format($f['selisih']);
	}
	$optNoKontrak.="<option value='".$f['nokontrak']."'>".$f['nokontrak']." - Sisa Belum Terkirim : ".$sisa."</option>";
}
			
$optCust="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optBarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$optTransp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select supplierid,namasupplier from ".$dbname.".log_5supplier where supplierid like 'K002%'";
	//exit("Error:$i");
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optTransp.="<option value='".$i['supplierid']."'>".$i['namasupplier']."</option>";
}

$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct LEFT( tanggal, 7 ) as tanggal from ".$dbname.".pabrik_timbangan limit 5";
	//exit("Error:$i");
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optPer.="<option value='".$d['tanggal']."'>".$d['tanggal']."</option>";
}

$tgl=date('d-m-Y');
$optTangki="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$arrExcel="##perSch##notranSch##nokontrakSch";



?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend><b>".$_SESSION['lang']['pengapalanmodo']."</b></legend>";
			echo"<table border=0 cellpadding=1 cellspacing=1>";
				echo"
					<tr>
						<td>".$_SESSION['lang']['notransaksi']."</td> 
						<td>:</td>
						<td><input type=text maxlength=10 disabled  value='".$notranBaru."' id=notran onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>
					</tr> 
					<tr>
						<td>".$_SESSION['lang']['tanggal']."</td> 
						<td>:</td>
						<td><input type=text class=myinputtext  id=tgl value=".$tgl." onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:200px;\"/></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['pabrik']."</td> 
						<td>:</td>
						<td><input type=text maxlength=4 disabled value='H01M' id=kodeorg onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>
					</tr> 
					<tr>
						<td>".$_SESSION['lang']['NoKontrak']."</td> 
						<td>:</td>
						<td><select id=nokontrak onchange=getCust() style=\"width:200px;\">".$optNoKontrak."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['nodo']."</td> 
						<td>:</td>
						<td><input type=text maxlength=50   id=nodo onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['nmcust']."</td> 
						<td>:</td>
						<td><select id=kdCust style=\"width:200px;\">".$optCust."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['kodebarang']."</td> 
						<td>:</td>
						<td><select id=kdbarang style=\"width:200px;\">".$optBarang."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['kodekapal']."</td> 
						<td>:</td>
                                                <td><input type=text maxlength=50   id=kdKapal onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['transporter']."</td> 
						<td>:</td>
						<td><select id=transp style=\"width:200px;\">".$optTransp."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['tangki']."</td> 
						<td>:</td>
						<td><select id=kdTangki style=\"width:200px;\">".$optTangki."</select></td>
					</tr>
					<tr>
						<td>".$_SESSION['lang']['beratBersih']."</td> 
						<td>:</td>
						<td><input type=text value=0 id=berat onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:200px;\"> Kg</td>
					</tr>
					<tr>
						<td>
							<button class=mybutton onclick=simpan()>Simpan</button>
							<button class=mybutton onclick=cancel()>Hapus</button>
						</td>
					<tr>
			</table></fieldset><input type=hidden id=method value='insert'>";
CLOSE_BOX();

OPEN_BOX();
echo"<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>
		".$_SESSION['lang']['notransaksi']." : <input type=text id=notranSch onblur=loadData() onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\">
		".$_SESSION['lang']['NoKontrak']." : <input type=text id=nokontrakSch onblur=loadData() onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\">

		
		<img onclick=iExcel(event,'pabrik_slave_pengapalanModo.php','".$arrExcel."') src=images/excel.jpg class=resicon title='MS.Excel'> 
		
		
		
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";

CLOSE_BOX();
	
echo close_body();			
?>
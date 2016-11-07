<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
//ambil periode gaji sesuai dengan lokasi tugas
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$optKary="<option value''>".$_SESSION['lang']['all']."</option>";
//$optKary=$optPeriode;
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode)){
	$optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
}
 
//ambil kodeorgannisasi dan organisasi dibawahnya
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where  kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$sTipe="select id,tipe from ".$dbname.".sdm_5tipekaryawan where id!=0";
$qTipe=mysql_query($sTipe) or die(mysql_error($conn));
while($rTipe=mysql_fetch_assoc($qTipe)){
	$optTipe.="<option value=".$rTipe['id'].">".$rTipe['tipe']."</option>";
}
$arr="##periode##divisiId##tpKary";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php    
echo"<div>
<fieldset style=\"float: left;\">
<legend><b>Slip Traksi</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['divisi']."</label></td><td><select id=\"divisiId\" name=\"divisiId\" style=\"width:150px\">".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tipekaryawan']."</label></td><td><select id=\"tpKary\" name=\"tpKary\" style=\"width:150px\">".$optTipe."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"left\">
<button onclick=\"zPdf('sdm_slave_2sliptraksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
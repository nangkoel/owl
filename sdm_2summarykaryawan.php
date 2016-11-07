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

$arr0="##tanggal"; 
?>
<script language=javascript src='js/zTools.js'></script>
<script type="text/javascript" src="js/sdm_2summarykaryawan.js"></script>
<script>


</script>

<link rel='stylesheet' type='text/css' href='style/zTable.css'>

<?php

$title[0]=$_SESSION['lang']['summary']." ".$_SESSION['lang']['karyawan'];
$title[1]=$_SESSION['lang']['summary']." ".$_SESSION['lang']['karyawan']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['gaji'];

$frm[0].="<fieldset style=\"float: left;\">
<legend><b>".$title[0]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>
</tr>

<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\">
        <button onclick=\"getlevel0()\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'sdm_slave_2summarykaryawan.php','".$arr0."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
Click header to see details...
<div id='printContainer0' style='overflow:auto;height:250px;max-width:1220px'>
</div>
<div id='printContainer1' style='overflow:auto;height:350px;max-width:1220px'>
</div>
</fieldset>";
$optPt.="<option value=''>".$_SESSION['lang']['all']."</option>";
$spt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qpt=mysql_query($spt) or die(mysql_error($conn));
while($rpt=  mysql_fetch_assoc($qpt)){
    $optPt.="<option value='".$rpt['kodeorganisasi']."'>".$rpt['namaorganisasi']."</option>";
}
$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
$sdr="select distinct periodegaji as periode from ".$dbname.".sdm_gaji order by periodegaji desc";
$qdr=mysql_query($sdr) or die(mysql_error($conn));
while($rdr=  mysql_fetch_assoc($qdr)){
    $optPrdDr.="<option value='".$rdr['periode']."'>".$rdr['periode']."</option>";
}
 $arr="##ptId2##unitId2##prdIdDr2";
$frm[1].="<fieldset style=\"float: left;\">
<legend><b>".$title[1]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >";
$frm[1].="<tr><td>".$_SESSION['lang']['pt']."</td>";
$frm[1].="<td><select id=ptId2  onchange='getUnit2()'  style=width:150px;>".$optPt."</select></td>";
$frm[1].="</tr>";
$frm[1].="<tr><td>".$_SESSION['lang']['unit']."</td>
          <td><select id=unitId2 style=width:150px;>".$optUnit."</select></td>
          </tr>";
$frm[1].="<tr><td>".$_SESSION['lang']['periode']."</td>
          <td><select id=prdIdDr2 style=width:150px;>".$optPrdDr."</select></td>
          </tr>";
 
$frm[1].="<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\">
        <button class=mybutton onclick=zPreview('sdm_slave_2summarykaryawan2','".$arr."','printContainer2')>".$_SESSION['lang']['proses']."</button>
        <button onclick=\"zExcel(event,'sdm_slave_2summarykaryawan2.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>

<div id='printContainer2' style='overflow:auto;height:250px;max-width:1220px;'>
</div>

<div id='printContainer5' style='overflow:auto;height:250px;max-width:1220px;'>
</div>
 
</fieldset>";

//========================
$hfrm[0]=$title[0];
$hfrm[1]=$title[1];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1100);
//===============================================


CLOSE_BOX();
echo close_body();
?>
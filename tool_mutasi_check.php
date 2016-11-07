<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$optNamaOrganisasi=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

$sPeriode="select distinct substring(tanggal,1,7) as periode from ".$dbname.".keu_jurnalht group by substring(tanggal,1,7) desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}

$optOrg="<select id=kdOrg name=kdOrg style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optOrg2="<select id=kdOrg2 name=kdOrg2 style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by kodeorganisasi asc ";	
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']." - ".$optNamaOrganisasi[$rOrg['kodeorganisasi']]."</option>";
    $optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']." - ".$optNamaOrganisasi[$rOrg['kodeorganisasi']]."</option>";
}
$optOrg.="</select>";
$optOrg2.="</select>";
$arr="##kdOrg##periode";
$arr2="##kdOrg2##periode2";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function Clear1()
{
    document.getElementById('kdOrg').value='';
    document.getElementById('periode').value='';
    document.getElementById('printContainer').innerHTML='';
}
function Clear2()
{
    document.getElementById('kdOrg2').value='';
    document.getElementById('periode2').value='';
    document.getElementById('printContainer2').innerHTML='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>

<?php
$frm[0].="<fieldset>
<legend><b>Mutasi Checker</b></legend>
<table cellspacing=1 border=0>
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td>".$optOrg."</td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:150px\">".$optPeriode."</select></td></tr>

<tr height=20><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2>
    <button onclick=\"zPreview('tool_slave_mutasi_check','".$arr."','printContainer')\" class=mybutton name=preview id=preview>Preview</button>
    <button onclick=\"zExcel(event,'tool_slave_mutasi_check.php','".$arr."')\" class=mybutton name=preview id=preview>Excel</button>
    <button onclick=\"Clear1()\" class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button></td></tr>
</table>
</fieldset>";
$frm[0].="
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$frm[1].="<fieldset>
<legend><b>Material Checker</b></legend>
<table cellspacing=1 border=0>
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td>".$optOrg2."</td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode2' style=\"width:150px\">".$optPeriode."</select></td></tr>

<tr height=20><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2>
    <button onclick=\"zPreview('tool_slave_mutasi_check2','".$arr2."','printContainer2')\" class=mybutton name=preview2 id=preview2>Preview</button>
    <button onclick=\"zExcel(event,'tool_slave_mutasi_check2.php','".$arr2."')\" class=mybutton name=preview2 id=preview2>Excel</button>
    <button onclick=\"Clear2()\" class=mybutton name=btnBatal2 id=btnBatal2>".$_SESSION['lang']['cancel']."</button></td></tr>
</table>
</fieldset>";
$frm[1].="
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$hfrm[0]=$_SESSION['lang']['selisih'].' '.$_SESSION['lang']['mutasi'];
$hfrm[1]=$_SESSION['lang']['selisih'].' '.$_SESSION['lang']['material'];
drawTab('FRM',$hfrm,$frm,200,1200);

CLOSE_BOX();
echo close_body();
?>
<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$frm[0]='';
$frm[1]='';
$frm[2]='';

$optBatch="<option value=''>".$_SESSION['lang']['all']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi order by batch desc";
    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi asc";
}
else
{
    $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";
}
$qBatch=mysql_query($sBatch) or die(mysql_error());
while($rBatch=mysql_fetch_assoc($qBatch))
{
    $optBatch.="<option value='".$rBatch['batch']."'>".$rBatch['batch']."</option>";
}

$optKodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$qKodeOrg=mysql_query($sKodeorg) or die(mysql_error());
while($rKodeorg=mysql_fetch_assoc($qKodeOrg))
{
    $optKodeorg.="<option value='".$rKodeorg['kodeorganisasi']."'>".$rKodeorg['namaorganisasi']."</option>";
}
$arr="##kdUnit##kdBatch";
?>
<script language=javascript src="js/zTools.js"></script>
<script language=javascript src="js/zReport.js"></script>
<script type="text/javascript" src="js/bibit_2_keluar_masuk.js"></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<?php    
$frm[0].="<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['laporanStockBIbit']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">
".$optKodeorg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['batch']."</label></td><td><select id=\"kdBatch\" name=\"kdBatch\" style=\"width:150px\">
".$optBatch."</select></td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('bibit_2_slave_keluar_masuk','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('bibit_2_slave_keluar_masuk','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'bibit_2_slave_keluar_masuk.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$optpt1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
    where tipe ='pt'
    order by namaorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optpt1.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}

$optkebun1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";


$frm[1].="<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['laporanStockBIbit']."(Recap)</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['pt']."</label></td>
    <td><select id=\"pt1\" name=\"pt1\" style=\"width:150px\" onchange=getkebun()>".$optpt1."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['kebun']."</label></td>
    <td><select id=\"kebun1\" name=\"kebun1\" style=\"width:150px\">".$optkebun1."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['sampai']."</label></td>
    <td><input type='text' class='myinputtext' id='tanggal1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  
    size='10' maxlength='10' style=\"width:150px;\"/></td>
</tr>
<tr>
    <td colspan=\"2\">
        <button class=mybutton id=preview1 name=preview1 onclick=previewdata1()>".$_SESSION['lang']['preview']."</button>
        <button class=mybutton id=excel1 name=excel1 onclick=exceldata1(event,'bibit_2_slave_keluar_masuk.php')>".$_SESSION['lang']['excel']."</button>
    </td>
</tr>
</table>
</fieldset>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='container1' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
$optbatch="<option value=''>".$_SESSION['lang']['all']."</option>";

$optkodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$kodeorg="select distinct kodeorganisasi,namaorganisasi 
    from ".$dbname.".organisasi where tipe='KEBUN' 
    order by namaorganisasi asc";
$query=mysql_query($kodeorg) or die(mysql_error());
while($result=mysql_fetch_assoc($query))
{
    $optkodeorg.="<option value='".$result['kodeorganisasi']."'>".$result['namaorganisasi']."</option>";
}


$frm[2].="

    <fieldset style=\"float: left;\">
    <legend><b>".$_SESSION['lang']['laporanStockBIbit']."</b></legend>
    <table cellspacing=\"1\" border=\"0\" >
    <tr>
        <td><label>".$_SESSION['lang']['unit']."</label></td>
        <td><select id=\"kodeunit\" name=\"kodeunit\" onchange=\"ambilbatch(this.value);\" style=\"width:150px\">".$optkodeorg."</select>
        </td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['batch']."</label></td>
        <td><select id=\"kodebatch\" name=\"kodebatch\" style=\"width:150px\">".$optbatch."</select></td>
    </tr>
    <tr>
        <td colspan=\"2\">
        <button onclick=\"previewdata2()\" class=\"mybutton\" >Preview</button>
        <button onclick=\"exceldata2(event,'bibit_slave_2kartu.php')\" class=\"mybutton\">Excel</button>
        </td>
    </tr>
    </table>
    </fieldset>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
    <div id='printContainer3' style='overflow:auto;height:350px;max-width:1220px'>
    </div>
</fieldset>";

//========================
$hfrm[0]=$_SESSION['lang']['laporanStockBIbit'];
$hfrm[1]=$_SESSION['lang']['laporanStockBIbit']."(Recap)";
$hfrm[2]="Seed Card";
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,150,1200);
//========================

CLOSE_BOX();
echo close_body();
?>
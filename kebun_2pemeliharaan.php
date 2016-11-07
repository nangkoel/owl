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
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

if(($_SESSION['empl']['tipelokasitugas']=='HOLDING')or($_SESSION['empl']['tipelokasitugas']=='KANWIL'))
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN') order by namaorganisasi asc ";	
}
else
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$sTah="select substr(tanggal,1,4) as tahun from ".$dbname.".kebun_aktifitas group by substr(tanggal,1,4) order by tahun asc";
$qTah=mysql_query($sTah) or die(mysql_error($conn));
while($rTah=mysql_fetch_assoc($qTah))
{
	$optTah.="<option value=".$rTah['tahun'].">".$rTah['tahun']."</option>";
}
 
$optKeg="<option value=\"\">".$_SESSION['lang']['all']."</option>";
if($_SESSION['language']=='EN'){
    $zz='namakegiatan1 as namakegiatan';
}else{
    $zz='namakegiatan';
}
$sKeg="select kodekegiatan, ".$zz.", kelompok from ".$dbname.".setup_kegiatan order by kodekegiatan asc";
$qKeg=mysql_query($sKeg) or die(mysql_error($conn));
while($rKeg=mysql_fetch_assoc($qKeg))
{
	$optKeg.="<option value=".$rKeg['kodekegiatan'].">".$rKeg['kodekegiatan']." - ".$rKeg['namakegiatan']." (".$rKeg['kelompok'].")</option>";
}

$optISPO="<option value=''>".$_SESSION['lang']['all']."</option>";
$optISPO.="<option value='1'>ISPO</option>";
$optISPO.="<option value='0'>Non ISPO</option>";

$arr="##kdOrg##kdAfd##tgl1##tgl2##kegiatan##ispo";
$arr1="##kdOrg1##kdAfd1##tahun1##kegiatan1##ispo1";

?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/kebun_2pemeliharaan.js'></script>

<link rel=stylesheet type='text/css' href='style/zTable.css'>
<?php    


$title[0]=$_SESSION['lang']['pemeltanaman'];
$title[1]=$_SESSION['lang']['rotasi']." ".$_SESSION['lang']['pemeltanaman'];

$frm[0].="<fieldset style=\"float: left;\">
<legend><b>".$title[0]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kebun']."</label></td><td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange=\"getAfd()\"><option value=\"\"></option>".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"kdAfd\" name=\"kdAfd\" style=\"width:150px\"><option value=\"\"></option>".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tanggal']."</label></td><td>
<input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /> s.d.
<input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /></td></tr>
<tr><td><label>".$_SESSION['lang']['kegiatan']."</label></td><td><select id=\"kegiatan\" name=\"kegiatan\" style=\"width:150px\">".$optKeg."</select>
<img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariNoGudang class=resicon onclick=cariNoGudang('".$_SESSION['lang']['find']."',event)> 
</td></tr>
<tr><td><label>".$_SESSION['lang']['statusISPO']."</label></td><td><select id=\"ispo\" name=\"ispo\" style=\"width:150px\">".$optISPO."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\">
    <button onclick=\"zPreview('kebun_slave_2pemeliharaan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zExcel(event,'kebun_slave_2pemeliharaan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    <button onclick=\"Clear0()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button></td></tr>
</table>
</fieldset>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto; height:350px; max-width:1220px;'>

</div></fieldset>";

$frm[1].="<fieldset style=\"float: left;\">
<legend><b>".$title[1]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kebun']."</label></td><td><select id=\"kdOrg1\" name=\"kdOrg1\" style=\"width:150px\" onchange=\"getAfd1()\"><option value=\"\">".$_SESSION['lang']['pilihdata']."</option>".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"kdAfd1\" name=\"kdAfd1\" style=\"width:150px\"><option value=\"\"></option>".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tahun']."</label></td><td><select id=\"tahun1\" name=\"tahun1\" style=\"width:150px\"><option value=\"\">".$_SESSION['lang']['pilihdata']."</option>".$optTah."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['kegiatan']."</label></td><td><select id=\"kegiatan1\" name=\"kegiatan1\" style=\"width:150px\">".$optKeg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['statusISPO']."</label></td><td><select id=\"ispo1\" name=\"ispo1\" style=\"width:150px\">".$optISPO."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\">
    <button onclick=\"zPreview('kebun_slave_2pemeliharaan1','".$arr1."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zExcel(event,'kebun_slave_2pemeliharaan1.php','".$arr1."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button></td></tr>
</table>
</fieldset>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer1' style='overflow:auto; height:350px; max-width:1220px;'>

</div></fieldset>";

//========================
$hfrm[0]=$title[0];
$hfrm[1]=$title[1];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1220);
//===============================================

CLOSE_BOX();
echo close_body();
?>
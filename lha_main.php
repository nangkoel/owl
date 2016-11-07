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
$arr="##kdOrg##kdAfd##tgl1##tgl2";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/lha.js'></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lha']; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kebun']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getAfd()"><option value=""></option><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['afdeling']?></label></td><td><select id="kdAfd" name="kdAfd" style="width:150px"><option value=""></option></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td>
<input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;" maxlength="10" style="width:60px;" />
<input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" maxlength="10" style="width:60px;" title="Leave it empty to provide one day report"/>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('lha_slave_print','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="html">Preview</button>
    <button onclick="zExcel(event,'lha_slave_print.php','<?php echo $arr?>')" class="mybutton" name="excel" id="excel">Excel</button>
    <button onclick="zPdf('lha_slave_print','<?php echo $arr?>','printContainer')" class="mybutton" name="pdf" id="pdf"><?php echo $_SESSION['lang']['pdf']?></button>
    <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel'];?></button></td></tr>
</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto; height:350px; max-width:1220px;'>

</div></fieldset>
<?php

CLOSE_BOX();
echo close_body();
?>
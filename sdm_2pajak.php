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
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','TRAKSI','KANWIL') order by namaorganisasi asc ";	
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

$sOrg="select distinct substr(periodegaji,1,4) as periodegaji from ".$dbname.".sdm_gaji order by periodegaji desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optTahun.="<option value=".$rOrg['periodegaji'].">".$rOrg['periodegaji']."</option>";
}

$arr="##kodeorg##tahun";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/sdm_2pajak.js'></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['pajak']."(PPh21)"; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kodeorg" name="kodeorg" style="width:150px"><option value=""></option><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tahun']?></label></td><td><select id="tahun" name="tahun" style="width:150px"><option value=""></option><?php echo $optTahun?></select></td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('sdm_slave_2pajak','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="html">Preview</button>
    <button onclick="zExcel(event,'sdm_slave_2pajak.php','<?php echo $arr?>')" class="mybutton" name="excel" id="excel">Excel</button>
    <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel'];?></button></td></tr>
</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:scroll; height:350px; max-width:1000px;'>

</div></fieldset>
<?php

CLOSE_BOX();
echo close_body();
?>
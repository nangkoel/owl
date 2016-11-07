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
$lokasi=substr($_SESSION['empl']['lokasitugas'],0,4);

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

$optMandor="<option value=\"all\">".$_SESSION['lang']['all']."</option>";
$sMan="select a.nikmandor, b.namakaryawan from ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".datakaryawan b on a.nikmandor=b.karyawanid
    where a.kodeorg = '".$lokasi."'
    group by a.nikmandor
    order by b.namakaryawan";
$qMan=mysql_query($sMan) or die(mysql_error($conn));
while($rMan=mysql_fetch_assoc($qMan))
{
    $optMandor.="<option value=".$rMan['nikmandor'].">".$rMan['namakaryawan']." [".$rMan['nikmandor']."]</option>";
}
$arr="##kebun##mandor##tanggal";

?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/kebun_2kehadiranpermandor.js'></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php 
if($_SESSION['language']=='EN'){
    echo "Foreman Daily Absence"; 
}else{
    echo "Laporan Kehadiran per Mandor"; 
}
?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kebun']?></label></td><td><select id="kebun" name="kebun" style="width:150px"><option value=""></option><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['mandor']?></label></td><td><select id="mandor" name="mandor" style="width:150px"><option value=""></option><?php echo $optMandor?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td>
<input type="text" class="myinputtext" id="tanggal" name="tanggal" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" />
</td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('kebun_slave_2kehadiranpermandor','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
    <button onclick="zExcel(event,'kebun_slave_2kehadiranpermandor.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
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
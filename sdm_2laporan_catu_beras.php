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

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where 
               tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') and CHAR_LENGTH(kodeorganisasi)='4' order by namaorganisasi asc ";	
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
}
else
{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."' order by periode desc";
}
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}


$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optTip="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTipe="select distinct * from ".$dbname.".sdm_5tipekaryawan where id!=0 order by tipe asc";
$qTipe=mysql_query($sTipe) or die(mysql_error($conn));
while($rTipe=mysql_fetch_assoc($qTipe))
{
    $optTip.="<option value='".$rTipe['id']."'>".$rTipe['tipe']."</option>";
}
$arr="##kdOrg##periode##tpKary";
//$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>

<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanCatu']?></b></legend>
<table cellspacing="1" border="0" >
<tr>
	<td><label><?php echo $_SESSION['lang']['unit']?></label></td>
	<td><select id="kdOrg" name="kdOrg" style="width:150px"><?php echo $optOrg?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['periode']?></label></td>
	<td><select id="periode" name="periode" style="width:150px">
		<?php echo $optPeriode?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td>
	<td><select id="tpKary" name="tpKary" style="width:150px">
		<?php echo $optTip?>
	</select></td>
</tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
	<button onclick="zPreview('sdm_slave_2laporan_catu_beras','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
	<!--<button onclick="zPdf('sdm_slave_2laporan_catu_beras','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
	<button onclick="zExcel(event,'sdm_slave_2laporan_catu_beras.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
</td></tr>
</table>
</fieldset>
</div>



<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>
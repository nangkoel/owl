<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if(($_SESSION['empl']['tipelokasitugas']=='HOLDING')||($_SESSION['empl']['tipelokasitugas']=='KANWIL')){
    $whrdt="tipe='KEBUN'";
    $optDt=makeOption($dbname, 'organisasi', 'induk,kodeorganisasi',$whrdt);
    $kdLok=$optDt[$_SESSION['org']['kodeorganisasi']];
    $optRegion=makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
    $regData=$optRegion[$kdLok];
    $sOrg="select distinct namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
           where (kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$regData."') or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."')";
}else{
    $sOrg="select distinct namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
           where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sSup="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qSup=mysql_query($sSup) or die(mysql_error($conn));
while($rSup=mysql_fetch_assoc($qSup)){
	$optPeriode.="<option value=".$rSup['periode'].">".$rSup['periode']."</option>";
}
 
$arr="##periode##kdUnit";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['aruskaslangsung']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" ><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"><?php echo $optOrg ?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('keu_slave_2laporankaslangsung','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zExcel(event,'keu_slave_2laporankaslangsung.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>
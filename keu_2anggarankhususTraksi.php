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
$sKdvhc="select kodevhc from ".$dbname.".vhc_5master order by kodevhc desc";
$qKdvhc=mysql_query($sKdvhc) or die(mysql_error());
while($rKdvhc=mysql_fetch_assoc($qKdvhc))
{
	$optKdvhc.="<option value=".$rKdvhc['kodevhc'].">".$rKdvhc['kodevhc']."</option>";
}
$arr="##thn##kdVhc";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b>Laporan Anggaran Traksi</b><?php //echo $_SESSION['lang']['']?></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['tahun']?></label></td><td><input type="text" class="myinputtextnumber" value="<?php echo date("Y")?>" id="thn" name="thn" onkeypress="return angka_doang(event)" style="width:150px"  /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodevhc']?></label></td><td><select id="kdVhc" name="kdVhc" style="width:150px"><?php echo $optKdvhc?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('keu_slave_2anggarankhususTraksi','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('keu_slave_2anggarankhususTraksi','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'keu_slave_2anggarankhususTraksi.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
























<?php
/*#======Select Prep======
# Get Data
//$where = " length(kodeorganisasi)='4'";
$optOrg = makeOption($dbname,'vhc_5master','kodevhc,kodevhc','','0');
#======End Select Prep======
#=======Form============
$els = array();
# Fields
$els[] = array(
  makeElement('tahun','label',$_SESSION['lang']['tahun']),
  makeElement('tahun','textnum',date(Y),array('style'=>'width:150px','maxlength'=>'16',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:150px'),$optOrg)
);
$els[] = array(
  makeElement('revisi','label',$_SESSION['lang']['revisi']),
  makeElement('revisi','textnum','0',array('style'=>'width:150px','maxlength'=>'80',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

# Button
$param = '##tahun##kodeorg';
$container = 'printContainer';
$els['btn'] = array(
  makeElement('preview','btn','Preview',array('onclick'=>
    "zPreview('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')")).
  makeElement('printPdf','btn','PDF',array('onclick'=>
    "zPdf('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')")).
  makeElement('printExcel','btn','Excel',array('onclick'=>"excelBudKebun()"))
);

# Generate Field
echo "<div style='margin-bottom:30px'>";
echo genElTitle('Laporan Anggaran Traksi',$els);
echo "</div>";
echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>";
echo "<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'></div></fieldset>";
#=======End Form============*/

CLOSE_BOX();
echo close_body();
?>
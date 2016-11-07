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

//$arr="##periode";
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct substr(tanggalkontrak,1,4) as periode from ".$dbname.".pmn_kontrakjual order by substr(tanggalkontrak,1,4) desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
$optPeriode2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode2="select distinct substr(tanggalkontrak,1,4) as periode from ".$dbname.".pmn_kontrakjual order by substr(tanggalkontrak,1,7) desc";
$qPeriode2=mysql_query($sPeriode2) or die(mysql_error($conn));
while($rPeriode2=mysql_fetch_assoc($qPeriode2))
{
    $optPeriode2.="<option value='".$rPeriode2['periode']."'>".$rPeriode2['periode']."</option>";
}

$optBrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$optBrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
		$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
                $optBrg2.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}

$arr="##periode##kdBrg";
$arr2="##thn##kdBrg2";
$arr3="##kdBrg3##tgl_dr##tgl_samp";
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/pmn_laporanPemenuhanKontrak.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['laporanPemenuhanKontrak']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><?php echo $optBrg?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('pmn_slave_laporanPemenuhanKontrak','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('pmn_slave_laporanPemenuhanKontrak','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'pmn_slave_laporanPemenuhanKontrak.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
<fieldset style="float: left;">
<legend><b>Uncomplete Contract</b></legend>
<table cellspacing="1" border="0" >
    <tr><td><label><?php echo $_SESSION['lang']['tahun']?></label></td><td><select id="thn" name="thn" style="width:150px"><?php echo $optPeriode2?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['komoditi']?></label></td><td><select id="kdBrg2" name="kdBrg2" style="width:150px"><?php echo $optBrg2?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview2('pmn_slave_laporanPemenuhanKontrak','<?php echo $arr2?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel2(event,'pmn_slave_laporanPemenuhanKontrak.php','<?php echo $arr2?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
<fieldset style="float: left;">
<legend><b>Range Delivery</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['komoditi']?></label></td><td><select id="kdBrg3" name="kdBrg3" style="width:150px"><?php echo $optBrg2?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tgldari']?></label></td><td><input type=text  style="width:150px" class=myinputtext id=tgl_dr onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type=text  style="width:150px" class=myinputtext id=tgl_samp onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>
<tr><td colspan="2"><button onclick="zPreview3('pmn_slave_laporanPemenuhanKontrak','<?php echo $arr3?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel3(event,'pmn_slave_laporanPemenuhanKontrak.php','<?php echo $arr3?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
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
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

$optPeriode2="<option value=''>".$_SESSION['lang']['all']."</option>";
$sPeriode2="select distinct left(SIPBDATE,7) as periode from ".$dbname.".pabrik_mssipb order by left(SIPBDATE,7) desc";
$qPeriode2=mysql_query($sPeriode2) or die(mysql_error($conn));
while($rPeriode2=mysql_fetch_assoc($qPeriode2))
{
    $optPeriode2.="<option value='".$rPeriode2['periode']."'>".$rPeriode2['periode']."</option>";
}

$optBrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$optBrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sBrg="select distinct b.namabarang,PRODUCTCODE from ".$dbname.".pabrik_mssipb a left join "
        . " ".$dbname.".log_5masterbarang b on a.PRODUCTCODE=b.kodebarang where PRODUCTCODE!=''"
        . "order by namabarang asc";
$qBrg=mysql_query($sBrg) or die(mysql_error()); 
while($rBrg=mysql_fetch_assoc($qBrg))
{
		$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
                $optBrg2.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}

$arr="##periode##kdBrg";
v
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
 
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['sipblist']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode2?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><select id="kdBrg" name="kdBrg" style="width:150px"><?php echo $optBrg?></select></td></tr>
<tr><td colspan="2">
        <button onclick="zPreview('pabrik_slave_2sipblist','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('pmn_slave_laporanPemenuhanKontrak','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
        <button onclick="zExcel(event,'pabrik_slave_2sipblist.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
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
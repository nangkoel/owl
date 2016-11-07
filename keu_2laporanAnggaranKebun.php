<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='HOLDING'";
$optOrg = getOrgBelow($dbname,$_SESSION['empl']['kodeorganisasi'],false,'kebunonly');
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
$param = '##tahun##kodeorg##revisi';
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
echo genElTitle('Laporan Anggaran Kebun',$els);
echo "</div>";
echo "<fieldset style='clear:both'><legend><b>Print Area</b></legend>";
echo "<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'></div></fieldset>";
#=======End Form============

CLOSE_BOX();
echo close_body();
?>
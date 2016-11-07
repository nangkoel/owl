<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

/** Controller **/
# Options
$optPt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
$tmpUnit = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "induk='".$_SESSION['org']['kodeorganisasi']."'");
$optUnit = array(''=>$_SESSION['lang']['all']);
foreach($tmpUnit as $key=>$row) {
    $optUnit[$key]=$row;
}
$optRev['']=$_SESSION['lang']['all'];
for ($i = 0; $i <= 5; $i++) {
    $optRev[$i]=$i;
}

# Period
//$tmpBulan = $_SESSION['org']['period']['bulan'];
//$tmpTahun = $_SESSION['org']['period']['tahun'];
//$tmp2Bulan = $tmpBulan+1;
//if($tmp2Bulan>12) {
//    $tmp2Bulan = 1;
//    $tmp2Tahun = $tmpTahun;
//} else {
//    $tmp2Tahun = $tmpTahun-1;
//}
//$optPeriod = array();
//for($i=0;$i<12;$i++) {
//    if($tmp2Bulan<10) {
//        $tmp2Bulan = '0'.$tmp2Bulan;
//    }
//    $optPeriod[$tmp2Bulan."-".$tmp2Tahun]=$tmp2Bulan."-".$tmp2Tahun;
//    $tmp2Bulan++;
//    if($tmp2Bulan>12) {
//        $tmp2Bulan = 1;
//        $tmp2Tahun++;
//    }
//}
$optPeriod = makeOption($dbname,'setup_periodeakuntansi','periode,periode');

# Element
$els = array();
# Fields
$els[] = array(
  makeElement('pt','label',$_SESSION['lang']['pt']),
  makeElement('pt','select','',array('style'=>'width:200px'),$optPt)
);

$els[] = array(
  makeElement('unit','label',$_SESSION['lang']['unit']),
  makeElement('unit','select','',array('style'=>'width:200px'),$optUnit)
);
$els[] = array(
  makeElement('periode','label',$_SESSION['lang']['periode']),
  makeElement('periode','select','',array('style'=>'width:200px'),$optPeriod)
);
$els[] = array(
  makeElement('revisi','label',$_SESSION['lang']['revisi']),
  makeElement('revisi','select','all',array('style'=>'width:200px'),$optRev)
);

$param = '##pt##unit##periode##revisi';
$container = 'printArea';
$els['btn'] = array(
  makeElement('btnPreview','btn','Preview',array('onclick'=>
    "zPreview('keu_slave_2summaryJMemorial','".$param."','".$container."')")).
  makeElement('btnPDF','btn','PDF',array('onclick'=>
    "zPdf('keu_slave_2summaryJMemorial','".$param."','".$container."')")).
  makeElement('btnExcel','btn','Excel',array('onclick'=>
    "zExcel(event,'keu_slave_2summaryJMemorial.php','".$param."','".$container."')"))
);

/** View **/
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
echo genElTitle('Summary Memorial Journal',$els);
echo "<fieldset style='clear:left'><legend><b>Print Area</b></legend>";
echo "<div id='".$container."' style='overflow:auto;height:60%'></div></fieldset>";
CLOSE_BOX();

echo close_body();
?>
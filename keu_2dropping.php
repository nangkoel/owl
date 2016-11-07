<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');
?><?php

/** Controller **/
# Options
$qRegional = "select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%'";
$optKary1 = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"left(kodegolongan,1)>=4 and lokasitugas in (".$qRegional.")");
$optKary2 = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"left(kodegolongan,1)>=5 and lokasitugas in (".$qRegional.")");
$optKary3 = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',"left(kodegolongan,1)>=5 and lokasitugas like '%HO'");


$optOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi in (".$qRegional.")",'');

$fReport = new formReport('kasharian','keu_slave_2dropping',"Dropping Dana");
$fReport->addPrime('untuk',$_SESSION['lang']['untukunit'],'','select','L',20,$optKary3);
$fReport->addPrime('cc','CC','','select','L',20,$optKary3);

$fReport->addPrime('kodeorg',$_SESSION['lang']['kodeorganisasi'],'','select','L',20,$optOrg);

$fReport->addPrime('periode',$_SESSION['lang']['periode'],date('d-m-Y'),'period','L',15);
$fReport->addPrime('nodok',$_SESSION['lang']['nodok'],'','text','L',20);
$fReport->addPrime('pengaju',"Diajukan Oleh",'','select','L',20,$optKary1);
$fReport->addPrime('pemeriksa',"Diperiksa Oleh",'','select','L',20,$optKary1);
$fReport->addPrime('penyetuju',"Disetujui Oleh",'','select','L',20,$optKary2);
$fReport->_detailHeight = 60;

/** View **/
echo open_body();
?>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>
<script language="JavaScript1.2" src="js/keu_2dropping.js"></script>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>
<script>document.getElementById('btnPDF').style.display = 'none';</script>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
$fReport = new formReport('kasharian','keu_slave_3mcm',$_SESSION['lang']['kasharian']);
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL'){
         $whr="kodeorganisasi in(select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
         $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whr);
         $fReport->addPrime('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',20,$optOrg);
}  

$fReport->addPrime('periode',$_SESSION['lang']['periode'],date('d-m-Y'),'period','L',15);
$fReport->_detailHeight = 60;
$fReport->_pdfButton = false;
$fReport->_excelButton = false;

/** View **/
echo open_body();
?>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>
<script language="JavaScript1.2" src="js/keu_3mcm.js"></script>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>
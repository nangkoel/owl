<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
$as=Array('0'=>$_SESSION['lang']['pilihdata']);
$fReport = new formReport('kasharian','keu_slave_2kasHarianv2',$_SESSION['lang']['kasharian']);
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL'){
         $ckRegional=makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
         $whr="kodeorganisasi in(select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$ckRegional[$_SESSION['empl']['lokasitugas']]."')";
         $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whr);
         $optOrg = $as+$optOrg;
         $fReport->addPrime('kodeorg',$_SESSION['lang']['kodeorg'],'','select','L',20,$optOrg,array("onchange"=>"getNoakun()"));
}  
// update Oct 17, 2011 begin
 //$as=Array('0'=>$_SESSION['lang']['pilihdata']);
 $arrPil=Array('0'=>$_SESSION['lang']['detail'],'1'=>$_SESSION['lang']['total']);
// update Oct 17, 2011 end
$wherd="kasbank=1 and (pemilik='".$_SESSION['empl']['lokasitugas']."' or pemilik='GLOBAL')";
$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$wherd);
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
     $wherd="kasbank=1 and (pemilik='HOLDING' or SUBSTR(pemilik,1,2)='".substr($_SESSION['empl']['lokasitugas'],0,2)."' or pemilik='GLOBAL')";
     $optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$wherd);
}

 $optAkun=$as+$optAkun;

$fReport->addPrime('noakun',$_SESSION['lang']['noakundari'],'','select','L',20,$optAkun);
$fReport->addPrime('noakunsmp',$_SESSION['lang']['noakunsampai'],'','select','L',20,$optAkun);
$fReport->addPrime('periode',$_SESSION['lang']['periode'],date('d-m-Y'),'period','L',15);
$fReport->addPrime('pildt',$_SESSION['lang']['pilih'],'','select','L',20,$arrPil);
$fReport->_detailHeight = 60;

/** View **/
echo open_body();
?>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>
<script language="JavaScript1.2" src="js/keu_2kasharian.js"></script>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>
<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formReport.php');

/** Controller **/
# Options
//$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
//    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $optkebun = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "tipe='KEBUN'",true);
} else {
    $optkebun = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
        "tipe='KEBUN' and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')",'0',true);
}
$optkebun[''] = $_SESSION['lang']['all'];

//$optHGU="<option value=''>".$_SESSION['lang']['all']."</option>";
//$optHGU.="<option value='1'>HGU</option>";
//$optHGU.="<option value='0'>Non HGU</option>";
$optISPO['']= $_SESSION['lang']['all'];
$optISPO[1]="ISPO";
$optISPO[0]="Non ISPO";

//$optTahunTanam = makeOption($dbname,'setup_blok','tahuntanam,tahuntanam',
//    "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'",'0',true);

$optTahunTanam[''] = $_SESSION['lang']['all'];
$optAfdeling = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'afdeling');
$optAfdeling[''] = $_SESSION['lang']['all'];
#$add=array('onchange'=>"getAfdeling(this,'afdeling','kebun_slave_2arealstatement')");
#$add2=array('onchange'=>"getThnTnm('kebun_slave_2arealstatement')");
$fReport = new formReport('arealstatement','kebun_slave_2arealstatmen',$_SESSION['lang']['arealstatement']);
$fReport->addPrime('periode',$_SESSION['lang']['periode'],'','bulantahun','','L',25);
$fReport->addPrime('unit',$_SESSION['lang']['unit'],'','select','L',20,$optkebun);
$fReport->_primeEls[1]->_attr['onchange'] = "getAfdeling(this,'afdeling','kebun_slave_2arealstatement')";
$fReport->addPrime('afdeling',$_SESSION['lang']['afdeling'],'','select','L',20,$optAfdeling);
$fReport->_primeEls[2]->_attr['onchange'] = "getThnTnm('kebun_slave_2arealstatement')";
$fReport->addPrime('tahuntanam',$_SESSION['lang']['tahuntanam'],'','select','L',20,$optTahunTanam);
$fReport->addPrime('ispo',$_SESSION['lang']['statusISPO'],'','select','L',20,$optISPO);

/** View **/
echo open_body();
?>
<script language=javascript src="js/kebun_2arealstatement.js"></script>
<script language="JavaScript1.2" src="js/formReport.js"></script>
<script language="JavaScript1.2" src="js/biReport.js"></script>


<link rel="stylesheet" type="text/css" href="style/zTable.css">
<?php
include('master_mainMenu.php');

OPEN_BOX();
$fReport->render();
CLOSE_BOX();

echo close_body();
?>
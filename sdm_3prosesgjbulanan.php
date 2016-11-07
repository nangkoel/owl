<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

/** Controller **/
# Options
//$optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',
//    "kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");

$str="select periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'
          order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optPeriod[$bar->periode]=$bar->periode;
}

# Element
$els = array();
$els[] = array(
    makeElement('periodegaji','label',$_SESSION['lang']['periodegaji']),
    makeElement('periodegaji','select','',
        array('style'=>'width:150px'),$optPeriod)
);
$els['btn'] = array(
    makeElement('listBtn','btn',$_SESSION['lang']['list'],
        array('onclick'=>"list()"))
    #makeElement('postBtn','btn',$_SESSION['lang']['proses'],
    #    array('onclick'=>"post()",'disabled'=>'disabled'))
);

$form = "";
$form .= "<h3 align='left'>".$_SESSION['lang']['prosesgjbulanan']."</h3>";
$form .= genElementMultiDim($_SESSION['lang']['form'],$els,1);
$form .= "<fieldset style='float:left;clear:left'><legend><b>".$_SESSION['lang']['list']."</b>".
    "</legend><div id='listContainer'></div></fieldset>";

/** View **/
echo open_body();
?>
<script languange=javascript1.2 src='js/sdm_prosesgjbulanan.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
include('master_mainMenu.php');

OPEN_BOX();
echo $form;
CLOSE_BOX();

echo close_body();
?>
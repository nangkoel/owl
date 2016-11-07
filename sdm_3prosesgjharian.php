<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

?>
<script languange=javascript1.2>
    function getperiode(kodeorg) {
        document.getElementById('periodegaji').innerHTML=document.getElementById('per-'+kodeorg).innerHTML;
    }
</script>
<?php
$str="select kodeorg,periode from ".$dbname.".sdm_5periodegaji where sudahproses='0'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optPeriod[$bar->kodeorg][$bar->periode]=$bar->periode;
}
foreach($optPeriod as $org=>$val){
    echo "<div hidden id=per-".$org.">";
    foreach ($optPeriod[$org] as $per){
        echo "<option value=".$per.">".$per."</option>";
    }
    echo "</div>";
}

if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
    $optTipe = array('3'=>'KBL','4'=>'KHT');
    $hrt=" induk='".$_SESSION['org']['kodeorganisasi']."' and kodeorganisasi not like '%HO'";
}else{
    $optTipe = array('4'=>'KHT');
    $hrt=" kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
if($_SESSION['empl']['bagian']=='IT'){
    $optTipe = array('3'=>'KBL','4'=>'KHT');
    $hrt=" kodeorganisasi not like '%HO' and kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
}
$optOrg=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $hrt);
#========= Tipe Karyawan ====================
//$optTipe = array('3'=>'KBL','4'=>'KHT');
#========= Tipe Karyawan ====================

# Element
$els = array();
$els[] = array(
    makeElement('unit','label',$_SESSION['lang']['unit']),
    makeElement('kodeorg','select','', array('style'=>'width:150px','onchange'=>'getperiode(this.value)'),$optOrg)
);
$els[] = array(
    makeElement('periodegaji','label',$_SESSION['lang']['periodegaji']),
    makeElement('periodegaji','select','',
        array('style'=>'width:150px'),$optPeriod[$_SESSION['empl']['lokasitugas']]),
);
$els[] = array(
    makeElement('tipe','label',$_SESSION['lang']['tipekaryawan']),
    makeElement('tipe','select','',array('style'=>'width:150px'),$optTipe),
);
$els['btn'] = array(
    makeElement('listBtn','btn',$_SESSION['lang']['list'],
        array('onclick'=>"list()"))
   // makeElement('postBtn','btn',$_SESSION['lang']['proses'],
   //     array('onclick'=>"post()",'disabled'=>'disabled'))
);

$form = "";
$form .= "<h3 align='left'>".$_SESSION['lang']['prosesgjharian']."</h3>";
$form .= genElementMultiDim($_SESSION['lang']['form'],$els,1);
$form .= "<fieldset style='float:left;clear:left'><legend><b>".$_SESSION['lang']['list']."</b>".
    "</legend><div id='listContainer'></div></fieldset>";

/** View **/
echo open_body();
?>
<script languange=javascript1.2 src='js/sdm_prosesgjharian.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
include('master_mainMenu.php');

OPEN_BOX();
echo $form;
CLOSE_BOX();

echo close_body();
?>
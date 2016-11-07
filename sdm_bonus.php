<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_bonus.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',
    "kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");
$arrData="##periodegaji##jenis##jnsGaji";
# Element
$els = array();
$els[] = array(
    makeElement('periodegaji','label',$_SESSION['lang']['periodebonus']),
    makeElement('periodegaji','select','',
        array('style'=>'width:150px'),$optPeriod)
);
$els[] = array(
    makeElement('jenis','label',$_SESSION['lang']['jenis']),
    makeElement('jenis','select','',
        array('style'=>'width:150px'),array('28'=>'THR','26'=>'Bonus'))
);
$els[] = array(
    makeElement('jnsGaji','label',$_SESSION['lang']['sistemgaji']),
    makeElement('jnsGaji','select','',
        array('style'=>'width:150px'),array('Bulanan'=>$_SESSION['lang']['bulanan'],'Harian'=>$_SESSION['lang']['harian']))
);
$els[] = array(
  makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
  makeElement('tanggal','date','',array('style'=>'width:150px','maxlength'=>'20'))
);
$els[] = array(
  makeElement('tahun','label','Basis Gaji'),
  makeElement('tahun','textnum','',array('style'=>'width:50px','maxlength'=>'20'))
);


$els['btn'] = array(
    makeElement('listBtn','btn',$_SESSION['lang']['list'],
        array('onclick'=>"list()")).
    makeElement('cancelBtn','btn',$_SESSION['lang']['cancel'],
        array('onclick'=>"cancel()",'disabled'=>'disabled')).
    makeElement('excelBtn','btn',"Excel",
        array('onclick'=>"zExcel(event,'sdm_slave_bonus.php','".$arrData."')"))
);

$form = "";
$form .= "<h3 align='left'>".$_SESSION['lang']['bonus']."</h3>";
$form .= genElementMultiDim($_SESSION['lang']['form'],$els,1);

#====== View ======
# Form
OPEN_BOX();
echo $form;
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

echo close_body();
?>
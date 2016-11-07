<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/kebun_5bjr.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  
<?php
#======Select Prep======
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optMonth = optionMonth('I','long');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px','onchange'=>'blokInfo()'),$optOrg)
);
$els[] = array(
  makeElement('tahun','label',$_SESSION['lang']['tahun']),
  makeElement('tahun','textnum','',array('style'=>'width:200px','maxlength'=>'4',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('umr','label',$_SESSION['lang']['umr']),
  makeElement('umr','textnum','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);

# Fields
$fieldStr = '##kodeorg##tahun##umr';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'sdm_5umr',"##kodeorg##tahuntanam",null,'kodeorg##tahun')
);

# Generate Field
echo genElTitle($_SESSION['lang']['umr'],$els);
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='max-height:200px;overflow:auto;clear:both'>";
echo masterTable($dbname,'sdm_5umr',"*",array(),array(),null,array(),
    array('sep'=>'and',array('kodeorg'=>$_SESSION['empl']['lokasitugas'])),'kodeorg##tahun');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
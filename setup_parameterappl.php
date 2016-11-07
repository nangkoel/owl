<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
  <?php
echo"<p align='left'><u><b><font face='Arial' size='5' color='#000080'>".$_SESSION['lang']['parameteraplikasi']."</font></b></u></p>";

#======Select Prep======
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optBin = array(0=>'Character',1=>'Numeric');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeaplikasi','label',$_SESSION['lang']['kodeaplikasi']),
  makeElement('kodeaplikasi','text','',array('style'=>'width:150px','maxlength'=>'2'))
);
$els[] = array(
  makeElement('kodeparameter','label',$_SESSION['lang']['kodeparameter']),
  makeElement('kodeparameter','text','',array('style'=>'width:150px','maxlength'=>'10'))
);
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:150px'),$optOrg)
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:150px','maxlength'=>'50'))
);
$els[] = array(
  makeElement('typenilai','label',$_SESSION['lang']['typenilai']),
  makeElement('typenilai','select','',array('style'=>'width:150px'),$optBin)
);
$els[] = array(
  makeElement('nilai','label',$_SESSION['lang']['nilai']),
  makeElement('nilai','text','',array('style'=>'width:150px','maxlength'=>'255'))
);

# Fields
$fieldStr = '##kodeaplikasi##kodeparameter##kodeorg##keterangan##typenilai##nilai';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'setup_parameterappl',"##kodeaplikasi##kodeparameter")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:290px;overflow:auto;'>";
echo masterTable($dbname,'setup_parameterappl',"*",array(),array(),null,array(),null,'kodeaplikasi##kodeparameter');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
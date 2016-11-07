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
  
<p align="left"><u><b><font face="Arial" size="5" color="#000080">Basis Pemeliharaan</font></b></u></p>
<?php
#======Select Prep======
$optTopografi = makeOption($dbname,'setup_topografi','topografi,keterangan');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('topografi','label',$_SESSION['lang']['topografi']),
  makeElement('topografi','select','',array('style'=>'width:100px'),$optTopografi)
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:250px','maxlength'=>'50',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('batasbawah','label',$_SESSION['lang']['batasbawah']),
  makeElement('batasbawah','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('batasatas','label',$_SESSION['lang']['batasatas']),
  makeElement('batasatas','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('basisboronglaki','label',$_SESSION['lang']['basisboronglaki']),
  makeElement('basisboronglaki','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('basisborongperempuan','label',$_SESSION['lang']['basisborongperempuan']),
  makeElement('basisborongperempuan','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('basistugaslaki','label',$_SESSION['lang']['basistugaslaki']),
  makeElement('basistugaslaki','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('basistugasperempuan','label',$_SESSION['lang']['basistugasperempuan']),
  makeElement('basistugasperempuan','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('satuan','label',$_SESSION['lang']['satuan']),
  makeElement('satuan','text','',array('style'=>'width:50px','maxlength'=>'3',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('nilaipremi','label',$_SESSION['lang']['nilaipremi']),
  makeElement('nilaipremi','text','',array('style'=>'width:100px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);

# Fields
$fieldStr = '##topografi##keterangan##batasbawah##batasatas##basisboronglaki##basisborongperempuan';
$fieldStr .= '##basistugaslaki##basistugasperempuan##satuan##nilaipremi';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'kebun_5basispemeliharaan',"##topografi",null,"topografi")
);

# Generate Field
echo genElement($els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='height:200px;overflow:auto'>";
echo masterTable($dbname,'kebun_5basispemeliharaan',"*",array(),array(),null,array(),null,'topografi');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
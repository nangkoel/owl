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
#======Select Prep======
# Get Data
$whereOrg = "tipe='PT'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg,'1');
$optMatauang = makeOption($dbname,'setup_matauang','kode,matauang',null,'1');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('tahun','label',$_SESSION['lang']['tahun']),
  makeElement('tahun','textnum','',array('style'=>'width:100px','maxlength'=>'4'))
);
$els[] = array(
  makeElement('revisi','label',$_SESSION['lang']['revisi']),
  makeElement('revisi','textnum','',array('style'=>'width:100px','maxlength'=>'2'))
);
$els[] = array(
  makeElement('matauang','label',$_SESSION['lang']['matauang']),
  makeElement('matauang','select','',array('style'=>'width:100px'),$optMatauang)
);
$els[] = array(
  makeElement('nilaikurs','label',$_SESSION['lang']['nilaikurs']),
  makeElement('nilaikurs','textnum','',array('style'=>'width:100px','maxlength'=>'10'))
);

# Fields
$fieldStr = '##kodeorg##tahun##revisi##matauang##nilaikurs';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5kursanggaran',"##kodeorg##tahun##revisi##matauang")
);

# Generate Field
echo genElTitle('Tarif Anggaran',$els);
echo "</div>";
#=======End Form============

#=======Table============
# Display Table
#echo masterTable($dbname,'setup_kegiatan',$fieldArr);
$cols = array('kodeorg','tahun','revisi','matauang','nilaikurs');
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5kursanggaran',$cols,array(),array(),array(),
    array(),null,"kodeorg##tahun##revisi##matauang");
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
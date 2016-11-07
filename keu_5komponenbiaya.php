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
$optKlpBy = array(
  'Karyawan'=>'Karyawan','Mesin'=>'Mesin',
  'Material'=>'Material','Transport'=>'Transport',
  'Kontrak'=>'Kontrak','Supervisi'=>'Supervisi'
);
$whereOrg = "tipe='HOLDING'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg,'1');
#======End Select Prep======
#=======Form============
echo "<div style='margin-bottom:30px'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:250px'),$optOrg)
);
$els[] = array(
  makeElement('kelompokbiaya','label',$_SESSION['lang']['kelompokbiaya']),
  makeElement('kelompokbiaya','select','',array('style'=>'width:150px'),$optKlpBy)
);
$els[] = array(
  makeElement('kodebiaya','label',$_SESSION['lang']['kodebiaya']),
  makeElement('kodebiaya','text','',array('style'=>'width:50px','maxlength'=>'3'))
);
$els[] = array(
  makeElement('keteranganbiaya','label',$_SESSION['lang']['keterangan']),
  makeElement('keteranganbiaya','text','',array('style'=>'width:250px','maxlength'=>'40'))
);

# Fields
$fieldStr = '##kodeorg##kelompokbiaya##kodebiaya##keteranganbiaya';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5komponenbiaya',"##kodebiaya")
);

# Generate Field
echo genElTitle('Komponen Biaya',$els);
echo "</div>";
#=======End Form============

#=======Table============
# Display Table
#echo masterTable($dbname,'setup_kegiatan',$fieldArr);
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5komponenbiaya',"*",array(),array(),null,array(),null,'kodebiaya');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
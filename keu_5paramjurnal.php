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
$where = "`detail`=1";
$optAkun = array(''=>'');
$tmpAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$where,'2');
foreach($tmpAkun as $key=>$row) {
    $optAkun[$key] = $row;
}
$whereOrg = "tipe='HOLDING' and length(kodeorganisasi)=3";
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
  makeElement('kodeaplikasi','label',$_SESSION['lang']['kodeaplikasi']),
  makeElement('kodeaplikasi','text','',array('style'=>'width:50px','maxlength'=>'5'))
);
$els[] = array(
  makeElement('jurnalid','label',$_SESSION['lang']['jurnalid']),
  makeElement('jurnalid','text','',array('style'=>'width:250px','maxlength'=>'5'))
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:250px','maxlength'=>'50'))
);
$els[] = array(
  makeElement('noakundebet','label',$_SESSION['lang']['noakundebet']),
  makeElement('noakundebet','select','',array('style'=>'width:250px'),$optAkun)
);
$els[] = array(
  makeElement('sampaidebet','label',$_SESSION['lang']['sampaidebet']),
  makeElement('sampaidebet','select','',array('style'=>'width:250px'),$optAkun)
);
$els[] = array(
  makeElement('noakunkredit','label',$_SESSION['lang']['noakunkredit']),
  makeElement('noakunkredit','select','',array('style'=>'width:250px'),$optAkun)
);
$els[] = array(
  makeElement('sampaikredit','label',$_SESSION['lang']['sampaikredit']),
  makeElement('sampaikredit','select','',array('style'=>'width:250px'),$optAkun)
);
$els[] = array(
  makeElement('aktif','label',$_SESSION['lang']['aktif']),
  makeElement('aktif','check')
);

# Fields
$fieldStr = '##kodeorg##kodeaplikasi##jurnalid##keterangan##noakundebet##sampaidebet##noakunkredit##sampaikredit##aktif';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5parameterjurnal',"##kodeorg##kodeaplikasi##jurnalid",null,null,true)
);

# Generate Field
echo genElTitle('Parameter Jurnal',$els);
echo "</div>";
#=======End Form============

#=======Table============
# Display Table
#echo masterTable($dbname,'setup_kegiatan',$fieldArr);
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname,'keu_5parameterjurnal',$fieldArr,array(),array(),null,array(),null,'kodeorg##kodeaplikasi##jurnalid');
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
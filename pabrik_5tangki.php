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
$where = "`tipe`='PABRIK' AND kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
$whereKary = "";$i=0;
foreach($optOrg as $key=>$row) {
  if($i==0) {
    $whereKary .= "lokasitugas='".$key."'";
  } else {
    $whereKary .= " or lokasitugas='".$key."'";
  }
  $i++;
}
$optKary = makeOption($dbname,'datakaryawan','nik,namakaryawan',$whereKary,'0');
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
  makeElement('kodetangki','label',$_SESSION['lang']['kodetangki']),
  makeElement('kodetangki','text','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
  makeElement('keterangan','text','',array('style'=>'width:200px','maxlength'=>'50',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('komoditi','label',$_SESSION['lang']['komoditi']),
  makeElement('komoditi','text','',array('style'=>'width:200px','maxlength'=>'3',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('stsuhu','label',$_SESSION['lang']['stsuhu']),
  makeElement('stsuhu','textnum','',array('style'=>'width:200px','maxlength'=>'3',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('luaspenampang','label',$_SESSION['lang']['luaspenampang']),
  makeElement('luaspenampang','textnum','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('satuanpenampang','label',$_SESSION['lang']['satuanpenampang']),
  makeElement('satuanpenampang','text','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('volumekerucut','label',$_SESSION['lang']['volumekerucut']),
  makeElement('volumekerucut','textnum','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('satuankerucut','label',$_SESSION['lang']['satuankerucut']),
  makeElement('satuankerucut','text','',array('style'=>'width:200px','maxlength'=>'10',
    'onkeypress'=>'return tanpa_kutip(event)'))
);

# Fields
$fieldStr = '##kodeorg##kodetangki##keterangan##komoditi##stsuhu##luaspenampang##satuanpenampang##volumekerucut##satuankerucut';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'pabrik_5tangki',"##kodeorg##kodetangki",null,'kodeorg##kodetangki')
);

# Generate Field
echo genElTitle($_SESSION['lang']['tangki'],$els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
echo "<div style='clear:both;float:left'>";
$cols = array('kodeorg','kodetangki','keterangan','komoditi','stsuhu','luaspenampang','satuanpenampang','volumekerucut','satuankerucut');
$where = array("cond1" => array("kodeorg" => $_SESSION['empl']['lokasitugas']));
echo masterTable($dbname,'pabrik_5tangki',$cols,array(),array(),$where,array(),null,'kodeorg##kodetangki',true,null,array(),null,null,true);
echo "</div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
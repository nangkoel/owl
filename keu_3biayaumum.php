<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

#=== Start ===
echo open_body();
?>
<!-- Includes -->
<script language=javascript1.2 src=js/zTools.js></script>
<script language=javascript1.2 src=js/keu_3biayaumum.js></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#====== Controller ======
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$bulantahun = $_SESSION['org']['period']['tahun']."-".$_SESSION['org']['period']['bulan'];
$optPeriod = array($bulantahun=>$bulantahun);

# Fields
$els = array();
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
);
$els[] = array(
  makeElement('periode','label',$_SESSION['lang']['periode']),
  makeElement('periode','select','',array('style'=>'width:200px'),$optPeriod)
);

# Button
$els['btn'] = array(
  makeElement('btnList','button',$_SESSION['lang']['list'],
    array('onclick'=>'listPosting()'))
);

#====== View ======
# Menu
include('master_mainMenu.php');

# Form
OPEN_BOX();
echo genElTitle('Alokasi Biaya Umum',$els);
CLOSE_BOX();

# List
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'],'listPosting',null,true);
CLOSE_BOX();

#=== End ===
close_body();
?>
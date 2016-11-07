<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
#echo "<pre>";
#print_r($_SESSION);
#echo "</pre>";
?>
<script language=javascript src=js/keu_anggaran.js></script>
<script language=javascript src=js/zSearch.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<p align="left"><u><b><font face="Arial" size="3" color="#000080">Anggaran</font></b></u></p>
<?php
# ==Prep Periode Akuntansi
if(!isset($_SESSION['empl']['lokasitugas'])) {
    echo $_SESSION['lang']['errorkaryawan'];
    CLOSE_BOX();
    echo close_body();
    exit;
}

#===== Button Control ========
$headControl = "<img id='addHeaderId' title='Tambah Header' src='images/plus.png'".
  "style='width:20px;height:20px;cursor:pointer' onclick='addHeader(event)' />&nbsp;";
$headControl .= "<img id='editHeaderId' title='Lihat Daftar Header' src='images/edit.png'".
  "style='width:20px;height:20px;cursor:pointer' onclick='showHeadList(event)' />";
  
#===== Header Form ===========
# Prepare Field
$els = array();
$els[] = array(
    makeElement('main_kodeorg','label',$_SESSION['lang']['kodeorg']),
    makeElement('main_kodeorg','text','',array('style'=>'width:70px',
      'disabled'=>'disabled')).'&nbsp;'.
    makeElement('main_nameorg','text','',array('style'=>'width:150px',
      'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_kodeanggaran','label',$_SESSION['lang']['kodeanggaran']),
    makeElement('main_kodeanggaran','text','',array('style'=>'width:70px','maxlength'=>'10',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_keterangan','label',$_SESSION['lang']['keterangan']),
    makeElement('main_keterangan','text','',array('style'=>'width:250px','maxlength'=>'50',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_tipeanggaran','label',$_SESSION['lang']['tipeanggaran']),
    makeElement('main_tipeanggaran','text','',array('style'=>'width:70px','maxlength'=>'10',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_tahun','label',$_SESSION['lang']['tahun']),
    makeElement('main_tahun','text','',array('style'=>'width:70px','maxlength'=>'4',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_matauang','label',$_SESSION['lang']['matauang']),
    makeElement('main_matauang','text','',array('style'=>'width:70px','maxlength'=>'3',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_jumlah','label',$_SESSION['lang']['jumlah']),
    makeElement('main_jumlah','textnum','',array('style'=>'width:70px','maxlength'=>'10',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_revisi','label',$_SESSION['lang']['revisi']),
    makeElement('main_revisi','textnum','',array('style'=>'width:70px','maxlength'=>'2',
        'disabled'=>'disabled'))
);
$els[] = array(
    makeElement('main_tutup','label',$_SESSION['lang']['tutup']),
    makeElement('main_tutup','check','',array('disabled'=>'disabled'))
);

#===== Detail Table =======
$header = array(
    $_SESSION['lang']['kodebagian'],
    $_SESSION['lang']['kodekegiatan'],
    $_SESSION['lang']['kelompok'],
    $_SESSION['lang']['revisi'],
    $_SESSION['lang']['kodebarang'],
    'Z'
);
$data = array();
$tables = makeTable('listDetail','bodyDetail',$header,$data,array(),true,'detail_tr');

#===== Control Button =========
echo "<div>";
echo $headControl;
echo "</div>";

#===== Prepare Header-Detail Container =======
$container = "<fieldset style='float:left;clear:both'>".
  "<legend><b>Header</b></legend><div id='headContainer'>";
$container .= genElement($els);
$container .= "</div></fieldset>";

$container .= "<fieldset style='float:left;clear:both'>".
  "<legend><b>Detail</b></legend><div id='detailContainer'></div></fieldset>";
echo $container;

CLOSE_BOX();
echo close_body();
?>
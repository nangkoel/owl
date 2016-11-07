<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript1.2 src='js/kebun_rencanapanen.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#=== Init ===
$tipe = 'tipetransaksi';
$tipeVal = 'PNN';
$whereCont = "tipetransaksi='PNN'";
$whereContArr = array();

#=== Prep Control & Search
$ctl = array();

# Control
$tmpWhere = json_encode($whereContArr);
$jsWhere = str_replace('"',"'",$tmpWhere);
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".
    $_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new']."</span></div>";
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".
    $_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list']."</span></div>";

# Options
$optAfd = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'afdeling');
#$optAfd = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
#  "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optBulan = optionMonth(substr($_SESSION['language'],0,1),'long');

# Search
$ctl[] = "<fieldset><legend><b>".$_SESSION['lang']['find']."</b></legend>".
  makeElement('sAfdeling','label',$_SESSION['lang']['afdeling']).
  makeElement('sAfdeling','select','',array(),$optAfd)."&nbsp;".
  makeElement('sPeriode','label',$_SESSION['lang']['periode']).
  makeElement('sBulan','select','',array(),$optBulan)."/".
  makeElement('sTahun','text',date('Y'),array('style'=>'width:50px')).
  makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans('".$tipe."','".$tipeVal."')")).
  "</fieldset>";

#=== Table Aktivitas
# Header
$header = array(
  $_SESSION['lang']['afdeling'],
  $_SESSION['lang']['blok'],
  $_SESSION['lang']['tanggal'],
  $_SESSION['lang']['bulan'],$_SESSION['lang']['tahun'],
  $_SESSION['lang']['jumlah'],
  $_SESSION['lang']['jumlahha'],
  $_SESSION['lang']['jumlahpremi'],
  $_SESSION['lang']['jumlahpokok']
);

# Content
$optNamaOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$cols = "kodeorg,kodeblok,tanggal,bulan,tahun,jumlah,jumlahha,jumlahpremi,jumlahpokok";
$query = selectQuery($dbname,'kebun_rencanapanen',$cols,"left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'","",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'kebun_rencanapanen');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
    $data[$key]['kodeorg'] = $optNamaOrg[$row['kodeorg']];
    $data[$key]['kodeblok'] = $row['kodeblok'];
}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data);
#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
#$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
#$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->setWhere($whereContArr);
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['sensusprod']."</h3></div>";
echo "<div><table align='center'><tr>";
foreach($ctl as $el) {
    echo "<td v-align='middle' style='min-width:100px'>".$el."</td>";
}
echo "</tr></table></div>";
CLOSE_BOX();

# List
OPEN_BOX();
echo "<div id='workField'>";
$tHeader->renderTable();
echo "</div>";
CLOSE_BOX();
echo close_body();
?>
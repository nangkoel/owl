<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zSearch.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/formTable.js'></script>
<script language=javascript src='js/sdm_pesangon.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#=== Prep Control & Search
$ctl = array();

# Control
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".
    $_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new']."</span></div>";
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".
    $_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list']."</span></div>";

# Search
$ctl[] = "<fieldset><legend><b>".$_SESSION['lang']['find']."</b></legend>".
    makeElement('sKary','label',$_SESSION['lang']['namakaryawan']).
    makeElement('sKary','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";

#=== Table Aktivitas
# Header
$header = array(
	$_SESSION['lang']['nodok'],
	$_SESSION['lang']['namakaryawan'],
	$_SESSION['lang']['periodegaji'],
	$_SESSION['lang']['masakerja'],
	$_SESSION['lang']['total']
);

# Content
$query = "select a.nodok,a.karyawanid,b.namakaryawan,a.periodegaji,a.masakerja,a.total from ".
	$dbname.".sdm_pesangonht a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid ".
	"order by a.tanggal desc limit 0,10";
$data = fetchData($query);
$dataShow = $data;
$totalRow = getTotalRow($dbname,'sdm_pesangonht');
foreach($data as $key=>$row) {
	$dataShow[$key]['karyawanid'] = $row['namakaryawan'];
	$dataShow[$key]['total'] = number_format($row['total'],2);
	unset($data[$key]['namakaryawan']);
	unset($dataShow[$key]['namakaryawan']);
}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[2]->addAttr('event');
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->_switchException = array('detailPDF');

#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['pesangon']."</h3></div>";
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
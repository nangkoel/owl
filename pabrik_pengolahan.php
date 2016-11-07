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
<script language=javascript1.2 src='js/pabrik_pengolahan.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
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
    makeElement('sNoTrans','label',$_SESSION['lang']['nopengolahan']).
    makeElement('sNoTrans','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    $_SESSION['lang']['nopengolahan'],
    $_SESSION['lang']['pabrik'],
    $_SESSION['lang']['tanggal'],
    $_SESSION['lang']['shift']
);

# Content
$cols = "nopengolahan,kodeorg,tanggal,shift,posting";
$query = selectQuery($dbname,'pabrik_pengolahan',$cols,"kodeorg='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc","",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'pabrik_pengolahan');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);

	if($row['posting']==1) 
	{
		$data[$key]['switched']=true;
	}
	unset($data[$key]['posting']);
}

##############

$x="select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%ka.%' or namajabatan like '%kepala%' ";

$y=mysql_query($x) or die (mysql_error($conn));
while($z=mysql_fetch_assoc($y))
{
	$pos=$z['kodejabatan'];
	if($pos==$_SESSION['empl']['kodejabatan'])
	{
		$flag=1;
	}
}

##############




# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data);
#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");


$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
if($flag!=1) {
    $tHeader->_actions[2]->_name='';
}


$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->_switchException = array('detailPDF');
$tHeader->pageSetting(1,$totalRow,10);
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['operasipabrik']."</h3></div>";
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
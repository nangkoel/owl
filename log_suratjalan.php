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
<script language=javascript src='js/log_suratjalan.js'></script>
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
    makeElement('sNoSj','label',$_SESSION['lang']['nosj']).
    makeElement('sNoSj','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";

#=== Table Aktivitas
# Header
$header = array(
	$_SESSION['lang']['nosj'],
	$_SESSION['lang']['pt'],$_SESSION['lang']['tanggal'],
	$_SESSION['lang']['tgl_kirim'],'postingby',$_SESSION['lang']['tanggaltiba']
);

//cari nama orang
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $nama[$bar->karyawanid]=$bar->namakaryawan;
}

# Content
$cols = "nosj,kodept,tanggal,tanggalkirim,postingby,tanggaltiba,posting";
$order="nosj desc";
$query = selectQuery($dbname,'log_suratjalanht',$cols,null,$order,false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'log_suratjalanht');
	foreach($data as $key=>$row) {
        if($row['posting']==1) {
			$data[$key]['switched']=true;
	    }
        unset($data[$key]['posting']);
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['tanggalkirim'] = tanggalnormal($row['tanggalkirim']);
		if(!empty($row['tanggaltiba'])) {
			$data[$key]['tanggaltiba'] = tanggalnormal($row['tanggaltiba']);
		}
		if($row['postingby']!=0) {
			$data[$key]['postingby'] = $nama[$row['postingby']];
		}
	}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->_colElement = array( //Set Column to shown as Element
	'tanggaltiba' => array('type'=>'date')
);
$tHeader->_switchException = array('detailPDF');

#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>Delivery Order</h3></div>";
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
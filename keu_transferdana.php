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
<script language=javascript1.2 src=js/keu_transferdana.js></script>
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
    makeElement('sNoTrans','label',$_SESSION['lang']['tanggal']).
    makeElement('sNoTrans','date','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    'Tanggal','Pengirim','Penerima','Jumlah','No. Giro'
);

# Content
$cols = "tanggal,kodeorgpengirim,kodeorgpenerima,jumlah,nogiro,postingkirim,postingterima";
$query = selectQuery($dbname,'keu_transferdana',$cols,
    "kodeorgpengirim='".$_SESSION['empl']['lokasitugas']."' or ".
    "(kodeorgpenerima='".$_SESSION['empl']['lokasitugas']."' and postingkirim=1)",
    "",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'pabrik_masukkeluartangki');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
    if($row['kodeorgpengirim']==$_SESSION['empl']['lokasitugas']) {
        if($row['postingkirim']==1) {
            $data[$key]['switched']=1;
        }
    }
    if($row['kodeorgpenerima']==$_SESSION['empl']['lokasitugas']) {
        $data[$key]['switched']=1;
        if($row['postingkirim']==1) {
            if($row['postingterima']!=1) {
                $data[$key]['noSwitchList'] = array('postingData');
            }
        } else {
            $data[$key]['noShow']=1;
        }
    }
    unset($data[$key]['postingkirim']);
    unset($data[$key]['postingterima']);
}

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['jumlah'] = number_format($row['jumlah'],0);
}

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
$tHeader->pageSetting(1,$totalRow,10);
#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['transferdana']."</h3></div>";
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
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
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/keu_vp.js'></script>
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
    makeElement('sNoTrans','label',$_SESSION['lang']['notransaksi']).
    makeElement('sNoTrans','text','',array('onkeypress'=>"return validat(event)")).'&nbsp;'.
    makeElement('sTanggal','label',$_SESSION['lang']['tanggal']).
    makeElement('sTanggal','date','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header & Align
$header = array(
    $_SESSION['lang']['novp'],$_SESSION['lang']['tanggal'],
	$_SESSION['lang']['nopo'],
        $_SESSION['lang']['keterangan'],'Last Update'
);
$align = explode(',','C,C,C,L');

//cari nama orang
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $nama[$bar->karyawanid]=$bar->namakaryawan;
}    

# Content
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$cols = "novp,tanggal,nopo,penjelasan,posting,updateby";
$query = selectQuery($dbname,'keu_vpht',$cols,$where,"tanggal desc, novp desc",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'keu_vpht',$where);
$whereAkun="";$whereOrg="";$i=0;
foreach($data as $key=>$row) {
    if($row['posting']==1) {
         $data[$key]['switched']=true;
    }
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
    $data[$key]['updateby'] = $nama[$row['updateby']];
    unset($data[$key]['posting']);
    $i++;
}

# Posting --> Jabatan
$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='keuangan'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];

# Mask Data Show
$dataShow = $data;

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
if($postJabatan!=$_SESSION['empl']['kodejabatan'] and $_SESSION['empl']['tipelokasitugas']!='HOLDING') {
	$tHeader->_actions[2]->_name='';
}
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->addAction('zoom','Lihat Detail','images/'.$_SESSION['theme']."/zoom.png");
$tHeader->_actions[3]->addAttr('event');
$tHeader->_actions[4]->addAttr('event');
$tHeader->_switchException = array('detailPDF','zoom');
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->setAlign($align);

#=== Display View
# Title & Control
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['vp']."</h3></div>";
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
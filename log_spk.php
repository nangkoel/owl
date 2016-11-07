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
<script language=javascript1.2 src="js/log_spk.js"></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type="text/css" href='style/zTable.css'>
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
    makeElement('sNoTrans','text','',array('onkeypress'=>"return validat(event)")).
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans()")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    $_SESSION['lang']['kodeorg'],
    $_SESSION['lang']['notransaksi'],
    $_SESSION['lang']['tanggal'],
    $_SESSION['lang']['subunit'],
    $_SESSION['lang']['koderekanan'],
    $_SESSION['lang']['nilaikontrak'],
    $_SESSION['lang']['dari'],
    $_SESSION['lang']['sampai'],
    $_SESSION['lang']['jumlahrealisasi'],
   
    $_SESSION['lang']['status']  
);

# Content
$cols = "kodeorg,notransaksi,tanggal,divisi,koderekanan,nilaikontrak,dari,sampai";
if($_SESSION['empl']['tipelokasitugas']=='TRAKSI' or
   $_SESSION['empl']['tipelokasitugas']=='HOLDING' or
   $_SESSION['empl']['tipelokasitugas']=='KANWIL') {
    $where = "length(kodeorg)=4";
} else {
    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}
$query = selectQuery($dbname,'log_spkht',$cols,$where ." order by tanggal desc","",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'log_spkht');
foreach($data as $key=>$row) {
    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
    $data[$key]['dari'] = tanggalnormal($row['dari']);
    $data[$key]['sampai'] = tanggalnormal($row['sampai']);
            //=================ambil realisasi
            $data[$key]['realisasi'] =0;
            $strx="select sum(jumlahrealisasi) from ".$dbname.".log_baspk 
                  where notransaksi='".$data[$key]['notransaksi']."'
                  and blokspkdt = '".$data[$key]['divisi']."'";
            $resx=mysql_query($strx);
            while($barx=mysql_fetch_array($resx))
            {
              $data[$key]['realisasi']= number_format($barx[0]); 
            }   
            //lihat postingan realisasi-=============================
            $data[$key]['status'] ='';
            $strx="select statusjurnal from ".$dbname.".log_baspk 
                  where notransaksi='".$data[$key]['notransaksi']."'
                  and blokspkdt = '".$data[$key]['divisi']."' and statusjurnal=0";
            $resx=mysql_query($strx);           
            if(mysql_num_rows($resx)>0)
                $data[$key]['status'] ='?';
            else if($data[$key]['realisasi']==0 and $data[$key]['status']=='')
                $data[$key]['status'] ='?';
            else                
               $data[$key]['status'] ='Ready for posting'; 
         //cek postingan spkht
            $stru="select posting from ".$dbname.".log_spkht where notransaksi='".$data[$key]['notransaksi']."'
                  and divisi = '".$data[$key]['divisi']."'";
            $resu=mysql_query($stru);
            $post=0;
            while($baru=mysql_fetch_array($resu))
            {
                $post=$baru[0];
            }
            if($post==1)
                $data[$key]['status']='Posted';

}

# Options
if(!empty($data)) {
    $whereSupp = "supplierid in (";
    foreach($data as $key=>$row) {
      if($key==0) {
        $whereSupp .= "'".$row['koderekanan']."'";
      } else {
        $whereSupp .= ",'".$row['koderekanan']."'";
      }
    }
    $whereSupp .= ")";
} else {
    $whereSupp = null;
}
$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',
    $whereSupp);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
  $dataShow[$key]['koderekanan'] = $optSupp[$row['koderekanan']];
  $dataShow[$key]['nilaikontrak'] = number_format($row['nilaikontrak'],0);
}
$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='panen'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];
# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
//if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
//	    $tHeader->_actions[2]->_name='';
//	}
if($_SESSION['empl']['tipelokasitugas'!='HOLDING']){
if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
$tHeader->_actions[2]->_name='';
}
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
echo "<div align='center'><h3>".$_SESSION['lang']['spk']."</h3></div>";
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
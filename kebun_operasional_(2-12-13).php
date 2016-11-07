<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

#==== Status Blok Validation
if(!isset($_SESSION['tmp']['actStat'])) {
    echo "Error : Plant type is missing";
    exit;
} else {
    $blokStatus = $_SESSION['tmp']['actStat'];
}

#==== Parameter yang berbeda untuk tiap status
switch($blokStatus) {
    case "lc":
	$title = "Land Clearing";
	$tipe = 'tipetransaksi';
	$_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TB';
	$whereCont = "tipetransaksi='TB'";
	$whereContArr = array();
	$whereContArr[] = array('tipetransaksi','TB');
	break;
    case "bibit":
	$title = $_SESSION['lang']['pembibitan'];
	$tipe = 'tipetransaksi';
	$_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'BBT';
	$whereCont = "tipetransaksi='BBT'";
	$whereContArr = array();
	$whereContArr[] = array('tipetransaksi','BBT');
	break;
    case "tbm":
	$title = "UPKEEP-".$_SESSION['lang']['tbm'];
	$tipe = 'tipetransaksi';
	$_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TBM';
	$whereCont = "tipetransaksi='TBM'";
	$whereContArr = array();
	$whereContArr[] = array('tipetransaksi','TBM');
	break;
    case "tm":
	$title = "UPKEEP-".$_SESSION['lang']['tm'];
	$tipe = 'tipetransaksi';
	$_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TM';
	$whereCont = "tipetransaksi='TM'";
	$whereContArr = array();
	$whereContArr[] = array('tipetransaksi','TM');
	break;
    default:
	echo "Error : Planting type undefined";
	exit;
	break;
}
if($_SESSION['empl']['subbagian']=='')
{
    $whereCont .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}
else
{
    $whereCont .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."' and updateby='".$_SESSION['standard']['userid']."'";
}
$whereContArr[] = array('kodeorg',$_SESSION['empl']['lokasitugas']);
?>
<script language=javascript1.2>
    function goToPages(page,shows,where) {
	if(typeof where != 'undefined') {
	    var newWhere = where.replace(/'/g,'"');
	}
	var workField = document.getElementById('workField');
	var param = "page="+page;
	param += "&shows="+shows+"&tipe=<?php echo $tipeVal?>";
	if(typeof where != 'undefined') {
	    param+="&where="+newWhere;
	}
	
	function respon() {
	    if (con.readyState == 4) {
		if (con.status == 200) {
		    busy_off();
		    if (!isSaveResponse(con.responseText)) {
			alert('ERROR TRANSACTION,\n' + con.responseText);
		    } else {
			//=== Success Response
			workField.innerHTML = con.responseText;
		    }
		} else {
		    busy_off();
		    error_catch(con.status);
		}
	    }
	}
	
	post_response_text('kebun_slave_operasional.php?proses=showHeadList', param, respon);
    }
</script>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript1.2 src='js/kebun_operasional.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<link rel=stylesheet type=text/css href='style/zTable.css'>
<?php
#=== Prep Control & Search
$ctl = array();

# Control
$tmpWhere = json_encode($whereContArr);
$jsWhere = str_replace('"',"'",$tmpWhere);
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".
    $_SESSION['lang']['new']."' onclick=\"showAdd('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['new']."</span></div>";
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".
    $_SESSION['lang']['list']."' onclick=\"defaultList('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['list']."</span></div>";

# Search
$ctl[] = "<fieldset><legend><b>".$_SESSION['lang']['find']."</b></legend>".
    makeElement('sNoTrans','label',$_SESSION['lang']['notransaksi']).
    makeElement('sNoTrans','text','').
    makeElement('sFind','btn',$_SESSION['lang']['find'],array('onclick'=>"searchTrans('".$tipe."','".$tipeVal."')")).
    "</fieldset>";


#=== Table Aktivitas
# Header
$header = array(
    $_SESSION['lang']['nomor'],$_SESSION['lang']['organisasi'],$_SESSION['lang']['tanggal'],$_SESSION['lang']['nikmandor'],$_SESSION['lang']['nikmandor1'],$_SESSION['lang']['asisten'],$_SESSION['lang']['kerani'],'updateby'
);

# Content
$cols = "notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat,jurnal,updateby";
$query = selectQuery($dbname,'kebun_aktifitas',$cols,$whereCont,
    "tanggal desc, notransaksi desc",false,10,1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'kebun_aktifitas',$whereCont);
if(!empty($data)) {
    $whereKarRow = "karyawanid in (";
    $notFirst = false;
    foreach($data as $key=>$row) {
	if($row['jurnal']==1) {
	    $data[$key]['switched']=true;
	}
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	unset($data[$key]['jurnal']);
	
	if($notFirst==false) {
	    if($row['nikmandor']!='') {
		$whereKarRow .= $row['nikmandor'];
		$notFirst=true;
	    }
	    if($row['nikmandor1']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['nikmandor1'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['nikmandor1'];
		}
	    }
	    if($row['nikasisten']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['nikasisten'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['nikasisten'];
		}
	    }
	    if($row['keranimuat']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['keranimuat'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['keranimuat'];
		}
	    }
            if($row['updateby']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['updateby'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['updateby'];
		}
	    }
	} else {
	    if($row['nikmandor']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['nikmandor'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['nikmandor'];
		}
	    }
	    if($row['nikmandor1']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['nikmandor1'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['nikmandor1'];
		}
	    }
	    if($row['nikasisten']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['nikasisten'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['nikasisten'];
		}
	    }
	    if($row['keranimuat']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['keranimuat'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['keranimuat'];
		}
	    }
             if($row['updateby']!='') {
		if($notFirst==false) {
		    $whereKarRow .= $row['updateby'];
		    $notFirst=true;
		} else {
		    $whereKarRow .= ",".$row['updateby'];
		}
	    }
	}
    }
    $whereKarRow .= ")";
} else {
    $whereKarRow = "";
}
$optKarRow = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKarRow);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    isset($optKarRow[$row['nikmandor']]) ? $dataShow[$key]['nikmandor'] = $optKarRow[$row['nikmandor']]:null;
    isset($optKarRow[$row['nikmandor1']]) ? $dataShow[$key]['nikmandor1'] = $optKarRow[$row['nikmandor1']]:null;
    isset($optKarRow[$row['nikasisten']]) ? $dataShow[$key]['nikasisten'] = $optKarRow[$row['nikasisten']]:null;
    isset($optKarRow[$row['keranimuat']]) ? $dataShow[$key]['keranimuat'] = $optKarRow[$row['keranimuat']]:null;
    isset($optKarRow[$row['updateby']]) ? $dataShow[$key]['updateby'] = $optKarRow[$row['updateby']]:null;
}

# Posting --> Jabatan
$qPosting = selectQuery($dbname,'setup_posting','jabatan',"kodeaplikasi='rawatkebun'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];

# Make Table
$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
$tHeader->_printAttr = array($tipeVal);
#$tHeader->addAction('showDetail','Detail','images/'.$_SESSION['theme']."/detail.png");
$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
$tHeader->_actions[0]->addAttr($tipeVal);
$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
#$tHeader->addAction('approveData','Approve','images/'.$_SESSION['theme']."/approve.png");
$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
if($postJabatan!=$_SESSION['empl']['kodejabatan']) {
    $tHeader->_actions[2]->_name='';
}
$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
$tHeader->_actions[3]->addAttr('event');
$tHeader->_actions[3]->addAttr($tipeVal);
$tHeader->addAction('detailData','Print Data Detail','images/'.$_SESSION['theme']."/zoom.png");
$tHeader->_actions[4]->addAttr('event');
$tHeader->_actions[4]->addAttr($tipeVal);
$tHeader->pageSetting(1,$totalRow,10);
$tHeader->setWhere($whereContArr);
//$tHeader->_switchException = array();
$tHeader->_switchException = array('detailData','detailPDF');



#echo "<pre>";
#print_r($tHeader);
#=== Display View
# Title & Control
OPEN_BOX();
echo "<input type='hidden' id='tipeTransHid' value='".$tipeVal."' />";
echo "<div align='center'><h3>".$title."</h3></div>";
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
?>
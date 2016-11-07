<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?php
$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
    case 'showHeadList':
      
	$where = "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."' ";
	if(isset($param['where'])) {
	   // $arrWhere = json_decode($param['where'],true);
            $tmpW = str_replace('\\','',$param['where']);
           
            $arrWhere = json_decode($tmpW,true);
           
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
			$where .= "and ".$r1[0]." like '%".$r1[1]."%'";
		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    }
		}
	    } else {
		$where .= null;
	    }
	} else {
	    $where .= null;
	}
	// exit("Error".$where);
	# Header
	$header = array(
	    $_SESSION['lang']['afdeling'],
            $_SESSION['lang']['blok'],
            $_SESSION['lang']['tanggal'],
	    $_SESSION['lang']['bulan'],$_SESSION['lang']['tahun'],
            $_SESSION['lang']['jumlah'],
			$_SESSION['lang']['jumlahbungabetina'],
            //$_SESSION['lang']['jumlahha'],
            //$_SESSION['lang']['jumlahpremi'],
            $_SESSION['lang']['jumlahpokok']
	);
	
	# Content
        $optNamaOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
	//$cols = "kodeorg,kodeblok,tanggal,bulan,tahun,jumlah,jumlahha,jumlahpremi,jumlahpokok";
	$cols = "kodeorg,kodeblok,tanggal,bulan,tahun,jumlah,jumlahbungabetina,jumlahpokok";
	$query = selectQuery($dbname,'kebun_rencanapanen',$cols,$where,"",false,$param['shows'],$param['page']);
        //exit("error".$query);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'kebun_rencanapanen',$where);
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
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	if(isset($arrWhere)) {
            $tHeader->setWhere($arrWhere);
        }
	
	# View
	$tHeader->renderTable();
	break;
    # Form Add Header
    case 'showAdd':
	// View
	echo formHeader('add',array());
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Form Edit Header
    case 'showEdit':
	$where = "kodeorg='".substr($param['kodeblok'],0,6)."' and bulan=".$param['bulan'].
	    " and tahun=".$param['tahun'];
	$query = selectQuery($dbname,'kebun_rencanapanen',"*",$where);
        //exit("Error".$query);
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	$data['tipetransaksi'] = $_GET['tipe'];
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$cols = array('notransaksi','kodeorg','tanggal','nikmandor',
	    'nikmandor1','nikasisten','keranimuat','tipetransaksi');
	$query = insertQuery($dbname,'kebun_rencanapanen',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "notransaksi='".$data['notransaksi']."'";
	unset($data['notransaksi']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$query = updateQuery($dbname,'kebun_rencanapanen',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "kodeorg='".$param['kodeorg']."' and tahun=".$param['tahun'].
	    " and bulan=".$param['bulan']." and kodeblok='".$param['kodeblok']."'";
	$query = "delete from `".$dbname."`.`kebun_rencanapanen` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$data['kodeorg'] = '';
	$data['bulan'] = date('m');
	$data['tahun'] = date('Y');
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optAfd = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas'],false,'afdeling');
    $optBulan = optionMonth(substr($_SESSION['language'],0,1),'long');
    
    $els = array();
    $els[] = array(
	makeElement('period','label',$_SESSION['lang']['periode']),
	makeElement('bulan','select',$data['bulan'],array(),$optBulan)."&nbsp;/&nbsp;".
            makeElement('tahun','text',$data['tahun'],array('style'=>'width:50px'))
    );
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optAfd)
    );
    $els['btn'] = array(
        makeElement('showDetBtn','btn',$_SESSION['lang']['detail'],
            array('onclick'=>"showDetail()"))
    );
    
    return genElementMultiDim($_SESSION['lang']['control'],$els);
}
?>
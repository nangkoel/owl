<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    # Daftar Header
    case 'showHeadList':
	if(isset($param['where'])) {
	    $arrWhere = json_decode($param['where'],true);
	    $where = "";
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
			$where .= $r1[0]." like '%".$r1[1]."%'";
		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%'";
		    }
		}
	    } else {
		$where = null;
	    }
	} else {
	    $where = null;
	}
	
	# Header
	$header = array(
	    $_SESSION['lang']['kodeorg'],
            $_SESSION['lang']['namalaporan'],
            $_SESSION['lang']['periode'],
            $_SESSION['lang']['ket1']
	);
	
	# Content
	$cols = "kodeorg,namalaporan,periode,ket1";
	$query = selectQuery($dbname,'keu_5mesinlaporanht',$cols,$where,"",false,$param['shows'],$param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname,'keu_5mesinlaporanht',$where);
	
	# Make Table
	$tHeader = new rTable('headTable','headTableBody',$header,$data);
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	$tHeader->pageSetting($param['page'],$totalRow,$param['shows']);
	if(isset($param['where'])) {
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
        $where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."'";
	$query = selectQuery($dbname,'keu_5mesinlaporanht',"*",$where);
	$data = fetchData($query);
	echo formHeader('edit',$data[0]);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	
	// Error Trap
	$warning = "";
	if($data['namalaporan']=='') {$warning .= "Nama Laporan harus diisi\n";}
	if($warning!=''){echo "Warning :\n".$warning;exit;}
	
	$cols = array('kodeorg','namalaporan','periode','ket1',
	    'ket2','ket3','ket4','ket5','ket6');
	$query = insertQuery($dbname,'keu_5mesinlaporanht',$data,$cols);
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."'";
	unset($data['kodeorg']);
        unset($data['namalaporan']);
	$query = updateQuery($dbname,'keu_5mesinlaporanht',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."'";
	$query = "delete from `".$dbname."`.`keu_5mesinlaporanht` where ".$where;
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
	$data['namalaporan'] = '';
	$data['periode'] = 'Bulanan';
	$data['ket1'] = '';
	$data['ket2'] = '';
        $data['ket3'] = '';
        $data['ket4'] = '';
        $data['ket5'] = '';
        $data['ket6'] = '';
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='HOLDING'");
    $optPeriode = getEnum($dbname,'keu_5mesinlaporanht','periode');
    
    $els = array();
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optOrg)
    );
    $els[] = array(
	makeElement('namalaporan','label',$_SESSION['lang']['namalaporan']),
	makeElement('namalaporan','text',$data['namalaporan'],
	    array('style'=>'width:150px','maxlength'=>'30',$disabled=>$disabled))
    );
    $els[] = array(
	makeElement('periode','label',$_SESSION['lang']['periode']),
	makeElement('periode','select',$data['periode'],
	    array('style'=>'width:150px'),$optPeriode)
    );
    $els[] = array(
	makeElement('ket1','label',$_SESSION['lang']['ket1']),
	makeElement('ket1','text',$data['ket1'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    $els[] = array(
	makeElement('ket2','label',$_SESSION['lang']['ket2']),
	makeElement('ket2','text',$data['ket2'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    $els[] = array(
	makeElement('ket3','label',$_SESSION['lang']['ket3']),
	makeElement('ket3','text',$data['ket3'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    $els[] = array(
	makeElement('ket4','label',$_SESSION['lang']['ket4']),
	makeElement('ket4','text',$data['ket4'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    $els[] = array(
	makeElement('ket5','label',$_SESSION['lang']['ket5']),
	makeElement('ket5','text',$data['ket5'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    $els[] = array(
	makeElement('ket6','label',$_SESSION['lang']['ket6']),
	makeElement('ket6','text',$data['ket6'],
	    array('style'=>'width:150px','maxlength'=>'45'))
    );
    
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable()"))
	);
    } elseif($mode=='edit') {
	$els['btn'] = array(
	    makeElement('editHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"editDataTable()"))
	);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>
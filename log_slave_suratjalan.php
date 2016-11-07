<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
?>

<?php
$proses = $_GET['proses'];
$param = $_POST;
unset($param['par']);

switch($proses) {
    # Daftar Header
    case 'showHeadList':    
		$where = "";
		if(isset($param['where'])) {
			$tmpW = str_replace('\\','',$param['where']);
			$arrWhere = json_decode($tmpW,true);
			if(!empty($arrWhere)) {
				foreach($arrWhere as $key=>$r1) {
					if($where!='') {$where .= " and ";}
					$where .= $r1[0]." like '%".$r1[1]."%'";
				}
			} 
		}
		
		//cari nama orang
		$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan";
		$res=mysql_query($str);
		while($bar= mysql_fetch_object($res))
		{
		   $nama[$bar->karyawanid]=$bar->namakaryawan;
		}
		
		# Header
		$header = array(
			$_SESSION['lang']['nosj'],
			$_SESSION['lang']['pt'],$_SESSION['lang']['tanggal'],
			$_SESSION['lang']['tgl_kirim'],'postingby',$_SESSION['lang']['tanggaltiba']
		);
		
		# Content
		$cols = "nosj,kodept,tanggal,tanggalkirim,postingby,tanggaltiba,posting";
		$order="nosj desc";
		$query = selectQuery($dbname,'log_suratjalanht',$cols,$where,$order,false,$param['shows'],$param['page']);
		$data = fetchData($query);
		
		if(empty($where)) $where = null;
		$totalRow = getTotalRow($dbname,'log_suratjalanht',$where);
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
		$tHeader->_colElement = array( //Set Column to shown as Element
			'tanggaltiba' => array('type'=>'date')
		);
		$tHeader->_switchException = array('detailPDF');
		
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
		$query = selectQuery($dbname,'log_suratjalanht',"*","nosj='".$param['nosj']."'");
		$tmpData = fetchData($query);
		$data = $tmpData[0];
		echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		$data = $_POST;
		
		// Error Trap
		$warning = "";
		if($data['tanggal']=='') {$warning .= "Date is obligatory\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tanggalkirim'] = isset($data['tanggalkirim'])? tanggalsystem($data['tanggalkirim']) : '';
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		
		unset($data['par']);
		
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		$query = insertQuery($dbname,'log_suratjalanht',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "nosj='".$data['nosj']."'";
		unset($data['nosj']);
		unset($data['par']);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['tanggalkirim'] = tanggalsystem($data['tanggalkirim']);
		$query = updateQuery($dbname,'log_suratjalanht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
		unset($param['par']);
		$where = "nosj='".$param['nosj']."'";
		$query = "delete from `".$dbname."`.`log_suratjalanht` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
	case 'posting':
		$data = array(
			'tanggaltiba' => tanggalsystem($param['tanggaltiba']),
			'posting' => 1,
			'postingby' => $_SESSION['standard']['userid']
		);
		$where = "nosj='".$param['nosj']."'";
		$query = updateQuery($dbname,'log_suratjalanht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		} else {
			//cari nama orang
			$nama = array();
			$where = "karyawanid=".$_SESSION['standard']['userid'];
			$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan where ".$where;
			$res=mysql_query($str);
			while($bar= mysql_fetch_object($res))
			{
			   $nama[$bar->karyawanid]=$bar->namakaryawan;
			}
			echo $nama[$_SESSION['standard']['userid']];
		}
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
		$data['nosj'] = 'SJ'.date('Ymdhi');
		$data['kodept'] = '';
        $data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
		$data['tanggal'] = '';
		$data['tanggalkirim'] = '';
		$data['expeditor'] = '';
		$data['pic'] = '';
		$data['nopol'] = '';
		$data['jeniskend'] = '';
		$data['driver'] = '';
		$data['hpdriver'] = '';
		$data['pengirim'] = '';
		$data['penerima'] = '';
		$data['checkedby'] = '';
		$data['franco'] = '';
		$data['transportasi'] = 'DARAT';
    } else {
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		$data['tanggalkirim'] = tanggalnormal($data['tanggalkirim']);
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optPT = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='PT'");
    $optFranco = makeOption($dbname,'setup_franco','id_franco,franco_name');
	$optKend = makeOption($dbname,'vhc_5jenisvhc','jenisvhc,namajenisvhc',"kelompokvhc='KD'");
	$optSupp = makeOption($dbname,'log_5supplier','supplierid,namasupplier',"kodekelompok in('K002','S003')");
	$optTrans = array(
		'DARAT' => $_SESSION['lang']['darat'],
		'UDARA' => $_SESSION['lang']['udara'],
		'LAUT' => $_SESSION['lang']['laut']
	);
	//$qKary = "select a.karyawanid,a.namakaryawan from ".$dbname.".datakaryawan a inner join ".
		//$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where b.namajabatan like '%logistik%' or b.namajabatan like '%logistic%'";
	if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
		$qKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan  where bagian='LOG' and lokasitugas like '%HO%' ";
	else
		$qKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan  where bagian='LOG' and lokasitugas='".$_SESSION['empl']['lokasitugas']."' ";
	$resKary = fetchData($qKary);
	$optKary = array();
	foreach($resKary as $row) {
		$optKary[$row['karyawanid']] = $row['namakaryawan'];
	}
	
	// Tambah Kendaraan
	$tmpKend = array('Colt Diesel','Fuso','Tronton','Buildup','Trailer','Kereta Api','Pesawat');
	foreach($tmpKend as $det) {
		$optKend[$det] = $det;
	}
	unset($optKend['DUMPTRUCK']);
    
    $els = array();
    $els[] = array(
	makeElement('nosj','label',$_SESSION['lang']['nosj']),
	makeElement('nosj','text',$data['nosj'],
	    array('style'=>'width:150px','maxlength'=>'20','disabled'=>'disabled'))
    );
	$els[] = array(
	makeElement('kodept','label',$_SESSION['lang']['kodept']),
	makeElement('kodept','select',$data['kodept'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optPT)
    );
	$els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
	makeElement('kodeorg','text',$data['kodeorg'],array('style'=>'width:150px','disabled'=>'disabled'))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
	$els[] = array(
	makeElement('tanggalkirim','label',$_SESSION['lang']['tgl_kirim']),
	makeElement('tanggalkirim','text',$data['tanggalkirim'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('expeditor','label',$_SESSION['lang']['expeditor']),
	makeElement('expeditor','select',$data['expeditor'],
	    array('style'=>'width:150px'),$optSupp)
    );
	// $els[] = array(
	// makeElement('pic','label',$_SESSION['lang']['pic']),
	// makeElement('pic','text',$data['pic'],array('style'=>'width:150px'))
    // );
	$els[] = array(
	makeElement('nopol','label',$_SESSION['lang']['nopol']),
	makeElement('nopol','text',$data['nopol'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('jeniskend','label',$_SESSION['lang']['jeniskend']),
	makeElement('jeniskend','select',$data['jeniskend'],
	    array('style'=>'width:150px'),$optKend)
    );
	$els[] = array(
	makeElement('driver','label',$_SESSION['lang']['supir']),
	makeElement('driver','text',$data['driver'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('hpdriver','label',$_SESSION['lang']['nohp'].' '.$_SESSION['lang']['supir']),
	makeElement('hpdriver','textnum',$data['hpdriver'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('pengirim','label',$_SESSION['lang']['pengirim']),
	makeElement('pengirim','select',$data['pengirim'],
	    array('style'=>'width:150px'),$optKary)
    );
	$els[] = array(
	makeElement('penerima','label',$_SESSION['lang']['penerima']),
	makeElement('penerima','text',$data['penerima'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('checkedby','label',$_SESSION['lang']['cek']),
	makeElement('checkedby','text',$data['checkedby'],array('style'=>'width:150px'))
    );
	$els[] = array(
	makeElement('franco','label',$_SESSION['lang']['franco']),
	makeElement('franco','select',$data['franco'],
	    array('style'=>'width:150px'),$optFranco)
    );
    $els[] = array(
	makeElement('transportasi','label',$_SESSION['lang']['transportasi']),
	makeElement('transportasi','select',$data['transportasi'],
	    array('style'=>'width:150px'),$optTrans)
    );
    
    if($mode=='add') {
	$els['btn'] = array(
	    makeElement('addHead','btn',$_SESSION['lang']['save'],
		array('onclick'=>"addDataTable()"))
	);
    } elseif($mode=='edit') {
		$els['btn'] = array(
			makeElement('editHead','btn',$_SESSION['lang']['save'],
				array('onclick'=>"editDataTable()")).
			makeElement('detailPo','btn',"Add Detail from PO",
				array('onclick'=>"showPO(event)")).
			makeElement('detailPl','btn',"Add Detail from Package List",
				array('onclick'=>"showPL(event)")).
			makeElement('detailManual','btn',"Add Detail from Material List",
				array('onclick'=>"showMaterial(event)"))
		);
    }
    
    if($mode=='add') {
	return genElementMultiDim($_SESSION['lang']['addheader'],$els,2);
    } elseif($mode=='edit') {
	return genElementMultiDim($_SESSION['lang']['editheader'],$els,2);
    }
}
?>
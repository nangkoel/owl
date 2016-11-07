<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	
	#posting
	
	case 'posting':
		//exit("Error:IND"); 
		$data = $_POST;
		$where = "nopengolahan='".$data['nopengolahan']."'";
		
		$query = updateQuery($dbname,'pabrik_pengolahan',array('posting'=>'1'),$where);
		//exit("Error:$query");
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
	break;
	
    # Daftar Header
    case 'showHeadList':
	$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
	if(isset($param['where'])) {
              $tmpW = str_replace('\\','',$param['where']);
	    $arrWhere = json_decode($tmpW,true);
	    if(!empty($arrWhere)) {
		foreach($arrWhere as $key=>$r1) {
		    if($key==0) {
//			$where .= $r1[0]." like '%".$r1[1]."%'";
//		    } else {
			$where .= " and ".$r1[0]." like '%".$r1[1]."%' order by tanggal desc";
		    }
		}
	    } else {
		$where .= null;
	    }
	} else {
	    $where .= null;
	}
	
	# Header
	$header = array(
	    $_SESSION['lang']['nopengolahan'],
	    $_SESSION['lang']['pabrik'],
	    $_SESSION['lang']['tanggal'],
	    $_SESSION['lang']['shift']
	);
	
	# Content
	$cols = "nopengolahan,kodeorg,tanggal,shift,posting";
	$query = selectQuery($dbname,'pabrik_pengolahan',$cols,$where,"",false,$param['shows'],$param['page']);
        $data = fetchData($query);
	$totalRow = getTotalRow($dbname,'pabrik_pengolahan',$where);
	foreach($data as $key=>$row) {
	    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		
		if($row['posting']==1) {
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
	$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
	$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
	
	$tHeader->addAction('postingData','Posting','images/'.$_SESSION['theme']."/posting.png");
	$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme']."/posted.png");
	if($flag!=1) {
    $tHeader->_actions[2]->_name='';
	}

	
	$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
	$tHeader->_actions[3]->addAttr('event');
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
	$query = selectQuery($dbname,'pabrik_pengolahan',"*","nopengolahan='".$param['nopengolahan']."'");
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	echo formHeader('edit',$data);
	echo "<div id='detailField' style='clear:both'></div>";
	break;
    # Proses Add Header
    case 'add':
	$data = $_POST;
	
	// Error Trap
	$warning = "";
	#if($data['nopengolahan']=='') {$warning .= "No Pengolahan harus diisi\n";}
	if($data['tanggal']=='') {$warning .= "Tanggal harus diisi\n";}
	if($warning!=''){echo "Warning :\n".$warning;exit;}
	
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	unset($data['nopengolahan']);
	$cols = array('kodeorg','tanggal','shift',
	    'jammulai','jamselesai','mandor','asisten','jamdinasbruto',
	    'jamstagnasi','jumlahlori','tbsdiolah');
	$query = insertQuery($dbname,'pabrik_pengolahan',$data,$cols);
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    $w = "kodeorg='".$data['kodeorg'].
	    "' and tanggal='".$data['tanggal'].
	    "' and shift=".$data['shift'].
	    " and jammulai='".$data['jammulai'].
	    "' and jamselesai='".$data['jamselesai'].
	    "' and mandor='".$data['mandor'].
	    "' and asisten='".$data['asisten'].
	    "' and jamdinasbruto=".$data['jamdinasbruto'].
	    " and jamstagnasi=".$data['jamstagnasi'].
	    " and jumlahlori=".$data['jumlahlori'].
	    " and tbsdiolah=".$data['tbsdiolah'];
	    $q = selectQuery($dbname,'pabrik_pengolahan','nopengolahan',$w);
	    $res = fetchData($q);
	    echo $res[0]['nopengolahan'];
	}
	break;
    # Proses Edit Header
    case 'edit':
	$data = $_POST;
	$where = "nopengolahan='".$data['nopengolahan']."'";
	unset($data['nopengolahan']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$query = updateQuery($dbname,'pabrik_pengolahan',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    case 'delete':
	$where = "nopengolahan=".$param['nopengolahan'];
	$query = "delete from `".$dbname."`.`pabrik_pengolahan` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    case 'updMandorAst':
	$mode=$param['mode'];
	$shift = $param['shift'];
	if($mode=='tanggal') {
	    $optShift = makeOption($dbname,'pabrik_5shift','shift,shift',
		"kodeorg='".$_SESSION['empl']['lokasitugas']."' and berlakusdtgl>='".
		tanggalsystem($param['tanggal'])."'");
	    if(empty($optShift)) {
		echo 'Warning : Tidak ada shift yang berlaku pada tanggal tersebut';
		exit;
	    }
	    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift in (";
	    $i=0;
	    foreach($optShift as $row) {
		if($i==0) {
		    $where .= $row;
		} else {
		    $where .= ",".$row;
		}
		$i++;
	    }
	    $where .= ")";
	    $cols = 'shift,mandor,asisten';
	} else {
	    $cols = 'mandor,asisten';
	    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and shift=".$param['shift'];
	}
	$query = selectQuery($dbname,'pabrik_5shift',$cols,$where);
	$res = fetchData($query);
	
	$whereKary = "karyawanid in (".$res[0]['mandor'].",".$res[0]['asisten'].")";
	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary);
	// Hasil Options
	$resShift = array();
	$resMandor = array($res[0]['mandor']=>$optKary[$res[0]['mandor']]);
	$resAst = array($res[0]['asisten']=>$optKary[$res[0]['asisten']]);
	
	if($mode=='tanggal') {
	    foreach($res as $row) {
		$resShift[$row['shift']] = $row['shift'];
	    }
	} else {
	    $resShift = 'empty';
	}
	
	$result = array(
	    'shift'=>$resShift,
	    'mandor'=>$resMandor,
	    'asisten'=>$resAst
	);
	
	#echo 'error';
	#print_r($res);
	echo json_encode($result);
	break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
	$new = true;
	$data['kodeorg'] = '';
	$data['nopengolahan'] = '0';
	$data['tanggal'] = '';
	$data['shift'] = '1';
	$data['jammulai'] = '00:00:00';
	$data['jamselesai'] = '00:00:00';
	$data['mandor'] = '';$data['asisten'] = '';
        $data['jamdinasbruto'] = '0';
        $data['jamstagnasi'] = '0';
        $data['jumlahlori'] = '0';
	/*$data['kodebarang'] = '';
	$data['kapasitaslori'] = '0';
	$data['mutuolah'] = '';
	$data['randemen'] = '0';*/
	$data['tbsdiolah'] = '0';
    } else {
	$new = false;
    }
    
    # Disabled Primary
    if($mode=='edit') {
	$disabled = 'disabled';
    } else {
	$disabled = '';
    }
    
    # Options
    $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    #$whereBarang = "kelompokbarang='400'";
    #$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
    $qShift = selectQuery($dbname,'pabrik_5shift','shift,mandor,asisten',"kodeorg='".
	$_SESSION['empl']['lokasitugas']."'");
    $tmpShift = fetchData($qShift);
    $optShift = array();
    $whereKary = "";$whereKaryNew = "";
    
    # OptShift
    foreach($tmpShift as $key=>$row) {
	$optShift[$row['shift']] = $row['shift'];
	if($key==0) {
	    $whereKaryNew .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
	    $whereKary .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
	} else {
	    $whereKaryNew .= " or karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
	}
    }
	
	
	
    
    # OptKary
    //if($new==false) {
//	$whereKary = "";
//	foreach($tmpShift as $key=>$row) {
//	    $optShift[$row['shift']] = $row['shift'];
//	    if($key==0) {
//		$whereKary .= "karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
//	    } else {
//		$whereKary .= " or karyawanid='".$row['mandor']."' or karyawanid='".$row['asisten']."'";
//	    }
//	}
//	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKaryNew);
//	$data['mandor']=$tmpShift[0]['mandor'];
//	$data['asisten']=$tmpShift[0]['asisten'];
//    } else {
//	$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary);
//    }
	//$whereKary=" tipekaryawan in (0,1,2)";
  //  $optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary);
	
	
	
	
	$whereKaryMandor = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan 
						where  namajabatan like '%KEPALA%' or namajabatan like '%mandor%' or namajabatan like '%foreman%' or namajabatan like '%foreman%'
						or namajabatan like '%pjs. ka%' or namajabatan like '%pengawas%')";
	$optKaryMandor = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKaryMandor);
	
	
	//$whereKary = "karyawanid in (".$res[0]['mandor'].",".$res[0]['asisten'].")";
	$whereKaryAsst = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where namajabatan like '%ASST%'"
                . " or namajabatan like '%KASUB%')";
	$optKaryAsst = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKaryAsst);
	
	//exit("Error:$whereKaryAsst");
	
    $els = array();
    $els[] = array(
	makeElement('kodeorg','label',$_SESSION['lang']['pabrik']),
	makeElement('kodeorg','select',$data['kodeorg'],
	    array('style'=>'width:150px'),$optOrg)
    );
    $els[] = array(
	makeElement('nopengolahan','label',$_SESSION['lang']['nopengolahan']),
	makeElement('nopengolahan','text',$data['nopengolahan'],
	    array('style'=>'width:150px','maxlength'=>'15','disabled'=>'disabled'))
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	    'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
    $els[] = array(
	makeElement('shift','label',$_SESSION['lang']['shift']),
	makeElement('shift','select',$data['shift'],
	   // array('style'=>'width:150px','onchange'=>"updMandorAst('shift')"),$optShift)
	    array('style'=>'width:150px'),$optShift)
    );
    $els[] = array(
	makeElement('jammulai','label',$_SESSION['lang']['jammulai']),
	makeElement('jammulai','jammenit',$data['jammulai'])
    );
    $els[] = array(
	makeElement('jamselesai','label',$_SESSION['lang']['jamselesai']),
	makeElement('jamselesai','jammenit',$data['jamselesai'])
    );
    $els[] = array(
	makeElement('mandor','label',$_SESSION['lang']['mandor']),
	makeElement('mandor','select',$data['mandor'],
	  //  array('style'=>'width:150px','disabled'=>'disabled'),$optKary)
	    array('style'=>'width:150px'),$optKaryMandor)
    );
    $els[] = array(
	makeElement('asisten','label',$_SESSION['lang']['asisten']),
	makeElement('asisten','select',$data['asisten'],
	    array('style'=>'width:150px'),$optKaryAsst)
    );
    $els[] = array(
	makeElement('jamdinasbruto','label',$_SESSION['lang']['jamdinasbruto']),
	makeElement('jamdinasbruto','textnum',$data['jamdinasbruto'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('jamstagnasi','label',$_SESSION['lang']['jamstagnasi']),
	makeElement('jamstagnasi','textnum',$data['jamstagnasi'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('jumlahlori','label',$_SESSION['lang']['jumlahlori']),
	makeElement('jumlahlori','textnum',$data['jumlahlori'],array('style'=>'width:150px'))
    );
    /*$els[] = array(
	makeElement('kodebarang','label',$_SESSION['lang']['kodebarang']),
	makeElement('kodebarang','select',$data['kodebarang'],array('style'=>'width:150px'),$optBarang)
    );
    $els[] = array(
	makeElement('kapasitaslori','label',$_SESSION['lang']['kapasitaslori']),
	makeElement('kapasitaslori','textnum',$data['kapasitaslori'],array('style'=>'width:150px'))." kg"
    );
    $els[] = array(
	makeElement('mutuolah','label',$_SESSION['lang']['mutuolah']),
	makeElement('mutuolah','textnum',$data['mutuolah'],array('style'=>'width:150px'))
    );
    $els[] = array(
	makeElement('randemen','label',$_SESSION['lang']['randemen']),
	makeElement('randemen','textnum',$data['randemen'],array('style'=>'width:150px'))." kg"
    );*/
    $els[] = array(
	makeElement('tbsdiolah','label',$_SESSION['lang']['tbsdiolah']),
	makeElement('tbsdiolah','textnum',$data['tbsdiolah'],array('style'=>'width:150px'))." kg"
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
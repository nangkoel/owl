<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

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
		if(!empty($where)) {
			$where = 'where '.$where;
		}
		
		# Header
		$header = array(
			$_SESSION['lang']['nodok'],
			$_SESSION['lang']['namakaryawan'],
			$_SESSION['lang']['periodegaji'],
			$_SESSION['lang']['masakerja'],
			$_SESSION['lang']['total']
		);
		
		# Content
		$limit = ($param['page']-1)*$param['shows'];
		$query = "select a.nodok,a.karyawanid,b.namakaryawan,a.periodegaji,a.masakerja,a.total from ".
			$dbname.".sdm_pesangonht a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid ".$where.
			" order by a.tanggal desc limit ".$limit.",".$param['shows'];
		$data = fetchData($query);
		
		if(empty($where)) {
			$where = null;
		} else {
			$where = str_replace('where','',$where);
		}
		$joinLeft = array(
			array(
				'table' => 'datakaryawan',
				'refCol' => 'karyawanid',
				'targetCol' => 'karyawanid'
			)
		);
		$totalRow = getTotalRow($dbname,'sdm_pesangonht',$where,$joinLeft);
		$dataShow = $data;
		foreach($data as $key=>$row) {
			$dataShow[$key]['karyawanid'] = $row['namakaryawan'];
			$dataShow[$key]['total'] = number_format($row['total'],2);
			unset($data[$key]['namakaryawan']);
			unset($dataShow[$key]['namakaryawan']);
		}
		
		# Make Table
		$tHeader = new rTable('headTable','headTableBody',$header,$data,$dataShow);
		$tHeader->addAction('showEdit','Edit','images/'.$_SESSION['theme']."/edit.png");
		$tHeader->addAction('deleteData','Delete','images/'.$_SESSION['theme']."/delete.png");
		$tHeader->addAction('detailPDF','Print Data Detail','images/'.$_SESSION['theme']."/pdf.jpg");
		$tHeader->_actions[2]->addAttr('event');
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
		$query = selectQuery($dbname,'sdm_pesangonht',"*","karyawanid='".$param['karyawanid']."'");
		$tmpData = fetchData($query);
		$data = $tmpData[0];
		echo formHeader('edit',$data);
		echo "<div id='detailField' style='clear:both'></div>";
		break;
    # Proses Add Header
    case 'add':
		// Init Data
		$data = array(
			'tanggal' => $param['tanggal'],
			'periodegaji' => $param['periodegaji'],
			'masakerja' => $param['masakerja'],
			'lembur' => $param['lembur'],
			'karyawanid' => $param['karyawanid'],
			'alasankeluar' => $param['alasankeluar'],
			'tanggalkeluar' => $param['tanggalkeluar'],
			'nodok' => $param['nodok'],
			'pesangon' => 0,
			'penghargaan' => 0,
			'pengganti' => 0,
			'perusahaan' => 0,
			'kesalahanbiasa' => 0,
			'kesalahanberat' => 0,
			'uangpisah' => 0,
			'total' => 0,
			'pph' => 0,
			'updateby' => $_SESSION['standard']['userid'],
		);
		
		$tglArr = explode('-',$param['tanggal']);
		
		// Get Gaji
		$qGaji = selectQuery($dbname,'sdm_5gajipokok','jumlah',
			"karyawanid='".$param['karyawanid']."' and tahun=".$tglArr[2]." and idkomponen=1");
		$resGaji = fetchData($qGaji);
		if(!empty($resGaji)) {
			$gaji = $resGaji[0]['jumlah'];
		} else {
			$gaji = 0;
		}
		
		// Get Rumus dari Setup Pesangon
		$qPes = selectQuery($dbname,'sdm_5pesangon','*',"masakerja=".$param['masakerja']);
		$resPes = fetchData($qPes);
		if(!empty($resPes)) {
			$pes = $resPes[0];
		} else {
			$pes = array(
				'pesangon' => 0,
				'penghargaan' => 0,
				'pengganti' => 0,
				'perusahaan' => 0,
				'kesalahanbiasa' => 0,
				'kesalahanberat' => 0,
				'uangpisah' => 0,
			);
		}
		
		// Hitung Biaya Kompensasi
		$total = 0;
		switch($param['alasankeluar']) {
			case 'perusahaan':
				$total += $gaji * $pes['perusahaan'];
				$data['pesangon'] = $gaji * $pes['pesangon'];
				$data['penghargaan'] = $gaji * $pes['penghargaan'];
				$data['pengganti'] = $gaji * $pes['pengganti'];
				$data['perusahaan'] = $gaji * $pes['perusahaan'];
				break;
			case 'salahkecil':
				$total += $gaji * $pes['kesalahanbiasa'];
				$data['kesalahanbiasa'] = $gaji * $pes['kesalahanbiasa'];
				break;
			case 'salahbesar':
				$total += $gaji * $pes['kesalahanberat'];
				$data['kesalahanberat'] = $gaji * $pes['kesalahanberat'];
				break;
		}
		if($param['lembur']==1) {
			$total += $gaji * $pes['uangpisah'];
			$data['uangpisah'] = $gaji * $pes['uangpisah'];
		}
		$data['total'] = $total;
		
		// Error Trap
		$warning = "";
		if($data['tanggal']=='') {$warning .= "Date is obligatory\n";}
		if($data['tanggalkeluar']=='') {$warning .= "Resignation Date is obligatory\n";}
		if($warning!=''){echo "Warning :\n".$warning;exit;}
		
		$data['tanggalkeluar'] = tanggalsystem($data['tanggalkeluar']);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		
		$cols = array();
		foreach($data as $key=>$row) {
			$cols[] = $key;
		}
		$query = insertQuery($dbname,'sdm_pesangonht',$data,$cols);
                //echo "error:".$query;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    # Proses Edit Header
    case 'edit':
		$data = $_POST;
		$where = "karyawanid='".$data['karyawanid']."'";
		unset($data['karyawanid']);
		$data['tanggal'] = tanggalsystem($data['tanggal']);
		$data['tanggalkeluar'] = tanggalsystem($data['tanggalkeluar']);
		$query = updateQuery($dbname,'sdm_pesangonht',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
		}
		break;
    case 'delete':
		$where = "karyawanid='".$param['karyawanid']."'";
		$query = "delete from `".$dbname."`.`sdm_pesangonht` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
	case 'changeKary':
		// Get Karyawan Info
		$qKaryInfo = "select lokasitugas,tanggalmasuk,tanggalkeluar from ".$dbname.".datakaryawan where karyawanid=".$param['karyawanid'];
		$resKaryInfo = fetchData($qKaryInfo);
		$tglMasukArr = explode('-',$resKaryInfo[0]['tanggalmasuk']);
		$tglKeluarArr = explode('-',$resKaryInfo[0]['tanggalkeluar']);
		$optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',"kodeorg='".$resKaryInfo[0]['lokasitugas']."'");
		
		// Value
		$masaKerja = 0;
                if (($tglKeluarArr[0] - $tglMasukArr[0]>0)){
                    if (($tglKeluarArr[1] - $tglMasukArr[1]>=0) and ($tglKeluarArr[2] - $tglMasukArr[2]>=0)){
                        $masaKerja = $tglKeluarArr[0] - $tglMasukArr[0];
                    } else {
                        $masaKerja = $tglKeluarArr[0] - $tglMasukArr[0] - 1;
                    }
                }
		
		$res = array(
			'period' => $optPeriod,
			'masakerja' => $masaKerja,
			'tanggalmasuk' => tanggalnormal($resKaryInfo[0]['tanggalmasuk']),
			'tanggalkeluar' => tanggalnormal($resKaryInfo[0]['tanggalkeluar'])
		);
		echo json_encode($res);
		break;
    default:
	break;
}

function formHeader($mode,$data) {
    global $dbname;
    
    # Default Value
    if(empty($data)) {
		$data['karyawanid'] = 0;
		$data['periodegaji'] = date('Y-m');
		$data['tanggal'] = date('d-m-Y');
        $data['masakerja'] = 0;
		$data['lembur'] = 0;
		$data['alasankeluar'] = 'perusahaan';
		$data['tanggalkeluar'] = '';
		$data['nodok'] = '';
		$data['pesangon'] = 0;
		$data['penghargaan'] = 0;
		$data['pengganti'] = 0;
		$data['perusahaan'] = 0;
		$data['kesalahanbiasa'] = 0;
		$data['kesalahanberat'] = 0;
		$data['uangpisah'] = 0;
		$data['total'] = 0;
    } else {
		$data['tanggal'] = tanggalnormal($data['tanggal']);
		$data['tanggalkeluar'] = tanggalnormal($data['tanggalkeluar']);
    }
    
    # Disabled Primary
    if($mode=='edit') {
		$disabled = 'disabled';
    } else {
		$disabled = '';
    }
    
    # Options
	$qKary = "select a.karyawanid,a.namakaryawan,a.nik from ".$dbname.".datakaryawan a join ".$dbname.
		".bgt_regional_assignment b on a.lokasitugas=b.kodeunit where b.regional='".
		$_SESSION['empl']['regional']."' and a.tanggalkeluar!='0000-00-00'";
	$resKary = fetchData($qKary);
    $optKary = array();
	foreach($resKary as $row) {
		$optKary[$row['karyawanid']] = $row['namakaryawan']." [".$row['nik']."]";
	}
	if(empty($data['karyawanid'])) {
		$data['karyawanid'] = key($optKary);
	}
	
	// Get Karyawan Info
	$qKaryInfo = "select lokasitugas,tanggalmasuk,tanggalkeluar from ".$dbname.".datakaryawan where karyawanid=".$data['karyawanid'];
	$resKaryInfo = fetchData($qKaryInfo);
	$tglMasukArr = explode('-',$resKaryInfo[0]['tanggalmasuk']);
        $tglKeluarArr = explode('-',$resKaryInfo[0]['tanggalkeluar']);
	
	$optPeriod = makeOption($dbname,'sdm_5periodegaji','periode,periode',"kodeorg='".$resKaryInfo[0]['lokasitugas']."'");
	$optMasa = array();
	for($i=0;$i<41;$i++) {
		$optMasa[$i] = $i;
	}
	$optMasa[0] = '<1';
	$optAlasan = array(
		'perusahaan' => 'Keinginan Perusahaan',
		'salahkecil' => 'Kesalahan Kecil',
		'salahbesar' => 'Kesalahan Besar / Mengundurkan Diri'
	);
	
	// Value
        $masaKerja = 0;
        if (($tglKeluarArr[0] - $tglMasukArr[0]>0)){
            if (($tglKeluarArr[1] - $tglMasukArr[1]>=0) and ($tglKeluarArr[2] - $tglMasukArr[2]>=0)){
                $masaKerja = $tglKeluarArr[0] - $tglMasukArr[0];
            } else {
                $masaKerja = $tglKeluarArr[0] - $tglMasukArr[0] - 1;
            }
        }
	
	// Elements
	$els = array();
    $els[] = array(
	makeElement('karyawanid','label',$_SESSION['lang']['namakaryawan']),
	makeElement('karyawanid','select',$data['karyawanid'],
	    array('style'=>'width:150px','onchange'=>'changeKary()',$disabled=>$disabled),$optKary)
    );
	$els[] = array(
	makeElement('periodegaji','label',$_SESSION['lang']['periodegaji']),
	makeElement('periodegaji','select',$data['periodegaji'],
	    array('style'=>'width:150px'),$optPeriod)
    );
    $els[] = array(
	makeElement('tanggal','label',$_SESSION['lang']['tanggal']),
	makeElement('tanggal','text',$data['tanggal'],array('style'=>'width:150px',
	'readonly'=>'readonly','onmousemove'=>'setCalendar(this.id)'))
    );
	$els[] = array(
	makeElement('alasankeluar','label','Alasan Keluar'),
	makeElement('alasankeluar','select',$data['alasankeluar'],
	    array('style'=>'width:150px',$disabled=>$disabled),$optAlasan)
    );
	$els[] = array(
	makeElement('tanggalkeluar','label',$_SESSION['lang']['tanggalkeluar']),
	makeElement('tanggalkeluar','text',tanggalnormal($resKaryInfo[0]['tanggalkeluar']),array('style'=>'width:150px',
	'disabled'=>'disabled'))
    );
	$els[] = array(
	makeElement('masakerja','label',$_SESSION['lang']['masakerja']),
	makeElement('masakerja','selectlabel',$masaKerja,array('style'=>'width:110px','disabled'=>'disabled'),$optMasa,$_SESSION['lang']['tahun'])
    );
    $els[] = array(
	makeElement('lembur','label',$_SESSION['lang']['lembur']),
	makeElement('lembur','checkbox',$data['lembur'],array($disabled=>$disabled))
    );
	$els[] = array(
	makeElement('nodok','label',$_SESSION['lang']['nodok']),
	makeElement('nodok','text',$data['nodok'],array('style'=>'width:150px','maxlength'=>30))
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
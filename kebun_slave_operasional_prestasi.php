<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
	
	$cols = array(
	    'kodekegiatan','kodeorg','jjg','hasilkerja','jumlahhk','upahkerja','umr',
	    'upahpremi','notransaksi','tahuntanam','norma','statusblok',
	    'pekerjaanpremi','penalti1','penalti2','penalti3','penalti4','penalti5','nik'
	);
	$data = $param;
	unset($data['numRow']);
	# Additional Default Data
	$data['tahuntanam'] = 0;$data['norma'] = 0;$data['statusblok'] = '0';
	$data['pekerjaanpremi'] = '0';
	$data['penalti1'] = 0;$data['penalti2'] = 0;$data['penalti3'] = 0;
	$data['penalti4'] = 0;$data['penalti5'] = 0;$data['nik'] = '-';
	
	if($param['kodekegiatan']=='')
		exit("Error:Activity empty");
	
	if($param['kodeorg']=='')
		exit("Error:Organization code empty");	
	
	$query = insertQuery($dbname,'kebun_prestasi',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
	unset($data['notransaksi']);unset($data['tahuntanam']);unset($data['norma']);
	unset($data['statusblok']);unset($data['pekerjaanpremi']);
	unset($data['penalti1']);unset($data['penalti2']);unset($data['penalti3']);
	unset($data['penalti4']);unset($data['penalti5']);unset($data['nik']);
	
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
	
	$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
	
	
	
    case 'edit':
	
	// Set Default
	if(empty($param['hasilkerja'])) {$param['hasilkerja']=0;}
	if(empty($param['jumlahhk'])) {$param['jumlahhk']=0;}
	if(empty($param['jjg'])) {$param['jjg']=0;}
	
	$data = $param;
	unset($data['notransaksi']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	
	// Cek Sum Detail
	$qDet = "SELECT `notransaksi`,sum(`hasilkerja`) as hasilkerja,sum(`jhk`) as jhk, sum(`jjg`) as jjg 
		FROM ".$dbname.".`kebun_kehadiran` WHERE notransaksi='".$param['notransaksi']."' group by notransaksi";
	$resDet = fetchData($qDet);
	if(!empty($resDet)) {
		if($resDet[0]['hasilkerja']>$param['hasilkerja']) {
			exit("Warning: Jumlah Hasil Kerja di kehadiran sudah diassign sebesar ".$resDet[0]['hasilkerja']);
		}
		if(number_format($resDet[0]['jhk'],2)>number_format($param['jumlahhk'],2)) {
			exit("Warning: Jumlah HK di kehadiran sudah diassign sebesar ".number_format($resDet[0]['jhk'],2));
		}
		if($resDet[0]['jjg']>$param['jjg']) {
			exit("Warning: Jumlah Janjang di kehadiran sudah diassign sebesar ".number_format($resDet[0]['jjg'],2));
		}
	}
	
	$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
	    $param['cond_kodekegiatan']."' and kodeorg='".$param['cond_kodeorg']."'";
	$query = updateQuery($dbname,'kebun_prestasi',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
	$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
	    $param['kodekegiatan']."' and kodeorg='".$param['kodeorg']."'";
	$query = "delete from `".$dbname."`.`kebun_prestasi` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
    break;
}
?>
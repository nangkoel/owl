<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
	$cols = array(
	    'kodekegiatan','kodeorg','hasilkerja','jumlahhk','upahkerja','umr',
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
	$data = $param;
	unset($data['notransaksi']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
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
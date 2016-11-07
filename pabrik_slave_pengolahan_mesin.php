<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
        $cols = array(
	    'tahuntanam','jammulai','jamselesai','jamstagnasi','downstatus','keterangan',
	    'nopengolahan','kodeorg'
	);
	$data = $param;
        $data['kodeorg'] = $data['station'];
	unset($data['numRow']);
        unset($data['station']);
	$query = insertQuery($dbname,'pabrik_pengolahanmesin',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
        $data = $param;
	unset($data['nopengolahan']);
        unset($data['numRow']);
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
        $result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
    case 'edit':
	$data = $param;
        $data['kodeorg'] = $data['station'];
        unset($data['station']);
	unset($data['nopengolahan']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".
	    $param['cond_station']."' and tahuntanam='".$param['cond_tahuntanam']."'";
	$query = updateQuery($dbname,'pabrik_pengolahanmesin',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
	$where = "nopengolahan='".$param['nopengolahan']."' and kodeorg='".
	    $param['station']."' and tahuntanam='".$param['tahuntanam']."'";
	$query = "delete from `".$dbname."`.`pabrik_pengolahanmesin` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
    break;
}
?>
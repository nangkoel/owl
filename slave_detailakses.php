<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];

if($proses=='updContent') {
    # Get Param
    $user = $_POST['user'];
    
    # Get Menu List
    $menuIdList = makeOption($dbname,'auth','menuid,detail',"namauser='".$user."'");
    
    # Create Condition
    $tDetail = array();
    $fDetail = array();
    if(empty($menuIdList)) {
	$menuList = array();
    } else {
	$where = "type='list' and class='click' and (";
	$i=0;
	foreach($menuIdList as $list=>$detail) {
	    $tDetail[$list] = str_split($detail);
	    if($i==0) {
		$fDetail[$list] = str_split($detail);
		$where .= "id=".$list;
	    } else {
		$where .= " or id=".$list;
	    }
	    $i++;
	}
	$where .= ")";
	$menuList = makeOption($dbname,'menu','id,caption',$where);
    }
    
    # Rearrange tDetail
    foreach($tDetail as $k1=>$detail) {
	foreach($detail as $k2=>$con) {
	    switch($k2) {
		case 0:
		    $tDetail[$k1]['input'] = $con;
		    break;
		case 1:
		    $tDetail[$k1]['edit'] = $con;
		    break;
		case 2:
		    $tDetail[$k1]['delete'] = $con;
		    break;
		case 3:
		    $tDetail[$k1]['print'] = $con;
		    break;
		case 4:
		    $tDetail[$k1]['approve'] = $con;
		    break;
		case 5:
		    $tDetail[$k1]['posting'] = $con;
		    break;
		default:
		    break;
	    }
	}
    }
    
    $resp = array();
    # Status
    empty($menuList) ? $resp['stat'] = 'failed' : $resp['stat'] = 'success';
    $resp['menuList'] = $menuList;
    $resp['firstDetail'] = $fDetail;
    $resp['detail'] = $tDetail;
    echo json_encode($resp);
} elseif($proses=='save') {
    $param = $_POST;
    
    if($param['menu']=='') {
	echo "Error : Menu harus dipilih";
    }
    
    # Merge Detail
    $detail = $param['input'].$param['edit'].$param['delete'].
	$param['print'].$param['approve'].$param['posting'];
    
    # Prep Data
    $data = array();
    $data['namauser'] = $param['user'];
    $data['menuid'] = $param['menu'];
    if((int)$detail==0) {
	$data['detail'] = '0';
    } else {
	$data['detail'] = $detail;
    }
    
    # Update
    $where = "namauser='".$param['user']."' and menuid='".$param['menu']."'";
    $sql = updateQuery($dbname,'auth',$data,$where);
    if(!mysql_query($sql)) {
	echo "DB Error : ".mysql_error();
    }
} elseif($proses=='updCheck') {
    # Get Data
    $query = selectQuery($dbname,'auth','detail',"namauser='".$_POST['user']."' and menuid='".$_POST['menu']."'");
    $res = fetchData($query);
    $tmp = str_split($res[0]['detail']);
    echo json_encode($tmp);
}
?>
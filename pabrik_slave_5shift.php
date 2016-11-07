<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$param = $_POST;
$proses = $_GET['proses'];

switch($proses) {
    case 'showDetail':
        # Options
        $where = "`tipe`='PABRIK'";
        $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
        $whereKary = "";$i=0;
        foreach($optOrg as $key=>$row) {
          if($i==0) {
            $whereKary .= "lokasitugas='".$key."'";
          } else {
            $whereKary .= " or lokasitugas='".$key."'";
          }
          $i++;
        }
        $optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKary,'0');
        
        # Get Data
        $where = "kodeorg='".$param['kodeorg']."' and shift=".$param['shift'];
        $cols = "nik";
        $query = selectQuery($dbname,'pabrik_5shiftanggota',$cols,$where);
        $data = fetchData($query);
        $dataShow = $data;
        foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['nik'] = $optKary[$row['nik']];
	}
        
        # Form
        $theForm2 = new uForm('kasbankForm',$_SESSION['lang']['form']." ".$_SESSION['lang']['anggotashif']);
        $theForm2->addEls('nik',$_SESSION['lang']['nik'],'','select','L',20,$optKary);
        
        # Table
        $theTable2 = new uTable('kasbankTable',$_SESSION['lang']['tabel']." ".$_SESSION['lang']['anggotashif'],$cols,$data,$dataShow);
        
        # FormTable
        $formTab2 = new uFormTable('ftPrestasi',$theForm2,$theTable2,null,array('kodeorg##shift'));
        $formTab2->_target = "pabrik_slave_5shift";
        
        #== Display View
        # Draw Tab
        echo "<fieldset><legend><b>Detail</b></legend>";
        $formTab2->render();
        echo "</fieldset>";
        break;
    case 'add':
	$cols = array(
	    'nik','kodeorg','shift'
	);
	$data = $param;
	unset($data['numRow']);
	
	$query = insertQuery($dbname,'pabrik_5shiftanggota',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
        
        unset($data['kodeorg']);unset($data['shift']);
	
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
	
	$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
    case 'edit':
	$data = $param;
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "kodeorg='".$param['kodeorg']."' and shift=".
	    $param['shift']." and nik='".$param['cond_nik']."'";
	$query = updateQuery($dbname,'pabrik_5shiftanggota',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
	$where = "kodeorg='".$param['kodeorg']."' and shift=".
	    $param['shift']." and nik='".$param['nik']."'";
	$query = "delete from `".$dbname."`.`pabrik_5shiftanggota` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}
?>
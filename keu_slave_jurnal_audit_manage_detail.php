<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/tanaman.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
        /*
	# Cek jika ada noakun di kolom yang sama
	$whereCek = "nojurnal='".$param['nojurnal'].
	    "' and noakun='".$param['noakun']."'";
	if($param['jumlah']<0) {
	    $whereCek .= " and jumlah<0";
	    $dk = 'Kredit';
	} else {
	    $whereCek .= " and jumlah>=0";
	    $dk = 'Debet';
	}
	$cekQuery = selectQuery($dbname,'keu_jurnaldt','*',$whereCek);
	$resCek = fetchData($cekQuery);
	if(!empty($resCek)) {
	    echo 'Warning : No Akun '.$param['noakun'].' di kolom '.$dk.' sudah ada';
	    exit;
	}
	*/
	# Search No urut
	$selQuery = selectQuery($dbname,'keu_jurnaldt','nourut',"nojurnal='".$param['nojurnal']."'");
	$nourut = fetchData($selQuery);
	$maxNoUrut = 1;
	if(!empty($nourut)) {
	    foreach($nourut as $row) {
		$row['nourut']>=$maxNoUrut ? $maxNoUrut=$row['nourut'] : false;
	    }
	    $maxNoUrut++;
	}

	$cols = array('nourut','noakun','keterangan','jumlah','matauang','kurs','noaruskas',
	    'kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
	    'kodesupplier','kodevhc','nodok','kodeblok','revisi','nojurnal','tanggal','kodeorg');
//	$cols = array('nourut','noakun','keterangan','jumlah','matauang','kurs','noaruskas',
//	    'kodekegiatan','kodeasset','kodebarang','nik','kodecustomer',
//	    'kodesupplier','kodevhc','nodok','nojurnal','tanggal','kodeorg');
	$data = $param;
	$data['nourut'] = $maxNoUrut;
	$data['kodeorg'] = $_SESSION['empl']['lokasitugas'];
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['jumlah'] = str_replace(',','',$data['jumlah']);
	unset($data['numRow']);
	unset($data['kodejurnal']);
        
        //=====tambahan ginting
	#periksa apakah akun tanaman, dan jika akun tanaman maka harus ada kodeblok
        $blk=str_replace(" ","",$param['kodeblok']);
        if(cekAkun($param['noakun']) and $blk=='')
        {
            exit("[ Error ]: Organization code is obligatory to this account.");
        }
        //=====end tambahan ginting
        
	$query = insertQuery($dbname,'keu_jurnaldt',$data,$cols);
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
//        echo $query;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
	unset($data['nojurnal']);
	unset($data['kodejurnal']);
	unset($data['tanggal']);
	unset($data['kodeorg']);
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
	
	$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
    case 'edit':
        /*
	# Cek jika ada noakun di kolom yang sama
	$whereCek = "nojurnal='".$param['nojurnal'].
	    "' and noakun='".$param['noakun'].
	    "' and nourut<>'".$param['nourut']."'";
	if($param['jumlah']<0) {
	    $whereCek .= " and jumlah<0";
	    $dk = 'Kredit';
	} else {
	    $whereCek .= " and jumlah>=0";
	    $dk = 'Debet';
	}
	$cekQuery = selectQuery($dbname,'keu_jurnaldt','*',$whereCek);
	$resCek = fetchData($cekQuery);
	if(!empty($resCek)) {
	    echo 'Warning : No Akun '.$param['noakun'].' di kolom '.$dk.' sudah ada';
	    exit;
	}
	*/
	$data = $param;
	unset($data['nojurnal']);
	unset($data['kodejurnal']);
	unset($data['nourut']);
	$data['tanggal'] = tanggalsystem($data['tanggal']);
	$data['jumlah'] = str_replace(',','',$data['jumlah']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "nojurnal='".$param['nojurnal']."' and nourut='".$param['nourut']."'";
	$query = updateQuery($dbname,'keu_jurnaldt',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
//        echo "warning: <pre>".$param."</pre>";
	break;
    case 'delete':
	$where = "nojurnal='".$param['nojurnal']."' and nourut='".$param['nourut']."'";
	$query = "delete from `".$dbname."`.`keu_jurnaldt` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
    break;
}
?>
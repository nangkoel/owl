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
	    'kodesupplier','kodevhc','nodok','kodeblok','nojurnal','tanggal','kodeorg');
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
        $nik=str_replace(" ","",$param['nik']);
        $sup=str_replace(" ","",$param['kodesupplier']);
        $vhc=str_replace(" ","",$param['kodevhc']);
        if(cekAkun($param['noakun']) and $blk=='')
        {
            exit("[ Error ]: Akun tanaman harus dilengkapi dengan kode blok.");
        }else if(cekAkun($data['noakun']) and $data['kodekegiatan']==''){
            exit("[ Error ]: Kode kegiatan harus dilengkapi.");
        }else  if(cekAkunPiutang($data['noakun']) and $nik=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan ID Karyawan.");
        }else if(cekAkunHutang($data['noakun']) and $sup=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan Kode Supplier.");
        }else if(cekAkunTrans($data['noakun']) and $vhc=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan Kode Alat/Kend.");
        }
        //=====end tambahan ginting
        
//        exit("qwe: ".$data['noakun']);
        
	//=====tambahan dz, fungsi cekAkun ada di lib/tanaman.php
	#periksa apakah akun hutang, dan jika akun hutang maka harus ada nik/pelanggan/supplier
//        $nik=str_replace(" ","",$data['nik']);
//        $kodesupplier=str_replace(" ","",$data['kodesupplier']);
//        if(cekAkunSupplier($data['noakun']) and $kodesupplier=='')
//        {
//            exit("[ Error ]: Akun ".$data['noakun']." harus dilengkapi dengan kode/nama Supplier.");
//        }
//        if(cekAkunNik($data['noakun']) and $nik=='')
//        {
//            exit("[ Error ]: Akun ".$data['noakun']." harus dilengkapi dengan NIK/nama Karyawan.");
//        }
        //=====end tambahan dz        
        
	$query = insertQuery($dbname,'keu_jurnaldt',$data,$cols);
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
        
        //=====tambahan ginting
	#periksa apakah akun tanaman, dan jika akun tanaman maka harus ada kodeblok
        $blk=str_replace(" ","",$param['kodeblok']);
        $nik=str_replace(" ","",$param['nik']);
        $sup=str_replace(" ","",$param['kodesupplier']);
        $vhc=str_replace(" ","",$param['kodevhc']);
        if(cekAkun($param['noakun']) and $blk=='')
        {
            exit("[ Error ]: Akun tanaman harus dilengkapi dengan kode blok.");
        }else if(cekAkun($data['noakun']) and $data['kodekegiatan']==''){
            exit("[ Error ]: Kode kegiatan harus dilengkapi.");
        }else  if(cekAkunPiutang($data['noakun']) and $nik=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan ID Karyawan.");
        }else if(cekAkunHutang($data['noakun']) and $sup=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan Kode Supplier.");
        }else if(cekAkunTrans($data['noakun']) and $vhc=='')
        {
            exit("[ Error ]: Akun  harus dilengkapi dengan Kode Alat/Kend.");
        }
        //=====end tambahan ginting
        
//        exit("qwe: ".$data['noakun']);
        
	//=====tambahan dz, fungsi cekAkun ada di lib/tanaman.php
	#periksa apakah akun hutang, dan jika akun hutang maka harus ada nik/pelanggan/supplier
//        $nik=str_replace(" ","",$data['nik']);
//        $kodesupplier=str_replace(" ","",$data['kodesupplier']);
//        if(cekAkunSupplier($data['noakun']) and $kodesupplier=='')
//        {
//            exit("[ Error ]: Akun ".$data['noakun']." harus dilengkapi dengan kode/nama Supplier.");
//        }
//        if(cekAkunNik($data['noakun']) and $nik=='')
//        {
//            exit("[ Error ]: Akun ".$data['noakun']." harus dilengkapi dengan NIK/nama Karyawan.");
//        }
        //=====end tambahan dz        
        
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
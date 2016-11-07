<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;

/** Posting Biaya Kendaraan **/
#=== Extract Data ====
# Get PT
$pt = getPT($dbname,substr($param['param_kodeorg'],0,4));
if($pt==false) {
    $pt = getHolding($dbname,substr($param['param_kodeorg'],0,4));
}

# Get Tanggal Akhir Periode
$qPeriod = selectQuery($dbname,'setup_periodeakuntansi','tanggalsampai',
    "kodeorg='".substr($param['param_kodeorg'],0,4)."' and periode='".
    $param['param_periode']."'");
$resPeriod = fetchData($qPeriod);
$tanggalSampai = $resPeriod[0]['tanggalsampai'];

# Define Kode Jurnal
if(substr($param['kodeblok'],3,1)=='M') {
    $kodejurnal = 'VHC3';
} else {
    $qBlok = selectQuery($dbname,'setup_blok','statusblok',
        "kodeorg='".$param['kodeblok']."'");
    $resBlok = fetchData($qBlok);
    if($resBlok[0]['statusblok']=='TBM') {
        $kodejurnal = 'VHC1';
    } elseif($resBlok[0]['statusblok']=='TM') {
        $kodejurnal = 'VHC2';
    }
}

# Get Akun
$qParam = selectQuery($dbname,'keu_5parameterjurnal','noakundebet,noakunkredit',
    "kodeaplikasi='VHC' and jurnalid='".$kodejurnal."'");
$tmpParam = fetchData($qParam);
$akunDebet = $tmpParam[0]['noakundebet'];
$akunKredit = $tmpParam[0]['noakunkredit'];

#==================== Journal Counter ==================
$queryC = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
    "kodeorg='".$pt['kode']."' and kodekelompok='".$kodejurnal."'");
$tmpC = fetchData($queryC);
if(empty($tmpC)) {
    echo "Warning : Kode Jurnal belum disetting untuk PT anda";
    exit;
}
$konter = addZero($tmpC[0]['nokounter']+1,3);
$tanggalJ = tanggalsystem(tanggalnormal($tanggalSampai));
$nojurnal = $tanggalJ."/".substr($param['param_kodeorg'],0,4).
    "/".$kodejurnal."/".$konter;
#==================== Journal Counter ==================

# Prep Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>$kodejurnal,
    'tanggal'=>$tanggalSampai,
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$param['kodevhc'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1'
);

# Data Detail
$noUrut = 1;

# Debet
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$tanggalSampai,
    'nourut'=>$noUrut,
    'noakun'=>$akunDebet,
    'keterangan'=>'Biaya Kendaraan dari '.$param['tipe'].' untuk '.$param['kodevhc'],
    'jumlah'=>$param['jumlah'],
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>substr($param['param_kodeorg'],0,4),
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>'',
    'noaruskas'=>'',
    'kodevhc'=>$param['kodevhc'],
    'nodok'=>'',
    'kodeblok'=>$param['kodeblok']
);
$noUrut++;

# Kredit
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$tanggalSampai,
    'nourut'=>$noUrut,
    'noakun'=>$akunKredit,
    'keterangan'=>'Biaya Kendaraan dari '.$param['tipe'].' untuk '.$param['kodevhc'],
    'jumlah'=>-1*$param['jumlah'],
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>substr($param['param_kodeorg'],0,4),
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>'',
    'noaruskas'=>'',
    'kodevhc'=>$param['kodevhc'],
    'nodok'=>'',
    'kodeblok'=>$param['kodeblok']
);
$noUrut++;

#========================== Proses Insert dan Update ==========================
#>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Header
$headErr = '';
$insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
if(!mysql_query($insHead)) {
    $headErr .= 'Insert Header Error : '.mysql_error()."\n";
}

if($headErr=='') {
    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
    $detailErr = '';
    foreach($dataRes['detail'] as $row) {
        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
        if(!mysql_query($insDet)) {
            $detailErr .= "Insert Detail Error : ".mysql_error()."\n";
            break;
        }
    }
    
    if($detailErr=='') {
        # Header and Detail inserted
        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
            "' and kodekelompok='".$kodejurnal."'");
        if(!mysql_query($updJurnal)) {
            echo "Update Kode Jurnal Error : ".mysql_error()."\n";
            # Rollback if Update Failed
            $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
            if(!mysql_query($RBDet)) {
                echo "Rollback Delete Header Error : ".mysql_error()."\n";
                exit;
            }
            exit;
        } else {
            #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Jurnal di log_transaksiht
            /*$updTrans = updateQuery($dbname,'log_transaksiht',
                array('statusjurnal'=>1),"notransaksi='".$param['notransaksi']."'");
            if(!mysql_query($updTrans)) {
                echo "Update Status Jurnal Error : ".mysql_error()."\n";
                # Rollback if Update Failed
                $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                if(!mysql_query($RBDet)) {
                    echo "Rollback Delete Header Error : ".mysql_error()."\n";
                    exit;
                }
                $RBJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter-1),
                    "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                    "' and kodekelompok='".$kodejurnal."'");
                if(!mysql_query($RBJurnal)) {
                    echo "Rollback Update Jurnal Error : ".mysql_error()."\n";
                    exit;
                }
                exit;
            } else {
                #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Success
                echo '1';
            }*/
            echo '1';
        }
    } else {
        echo $detailErr;
        # Rollback, Delete Header
        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
        if(!mysql_query($RBDet)) {
            echo "Rollback Delete Header Error : ".mysql_error();
            exit;
        }
    }
} else {
    echo $headErr;
    exit;
}
?>
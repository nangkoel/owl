<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;

#=== Get Data ===
# Header
$queryH = selectQuery($dbname,'kebun_aktifitas',"*","notransaksi='".
    $param['notransaksi']."'");
$dataH = fetchData($queryH);

# Prestasi
$queryD = selectQuery($dbname,'kebun_prestasi',"*","notransaksi='".
    $param['notransaksi']."'");
$dataD = fetchData($queryD);

# Absensi
$queryAbs = selectQuery($dbname,'kebun_kehadiran','jhk,umr,insentif',"notransaksi='".
    $param['notransaksi']."'");
$dataAbs = fetchData($queryAbs);

#=== Cek if posted ===
$error0 = "";
if($dataH[0]['jurnal']==1) {
    $error0 .= $_SESSION['lang']['errisposted'];
}
if($error0!='') {
    echo "Data Error :\n".$error0;
    exit;
}

#=== Cek if data not exist ===
$error1 = "";
if(count($dataH)==0) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}
if(count($dataD)==0) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}
if($error1!='') {
    echo "Data Error :\n".$error1;
    exit;
}

#=== Hitung Cost dari Absensi (Perawatan) ===
$costRawat = 0;
$totalHk = 0;
if(!empty($dataAbs)) {
    foreach($dataAbs as $row) {
        $costRawat += ($row['jhk']*$row['umr']) + $row['insentif'];
        $totalHk += $row['jhk'];
    }
}

#=== Cek if HK belum sama ===
if(empty($dataAbs) or ($totalHk!=$dataD[0]['jumlahhk'])) {
    echo 'Warning : HK Prestasi belum teralokasi dengan lengkap';
    exit;
}

#======================== Nomor Jurnal =============================
$kodeJurnal = 'M0';
$queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit',
    "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
$resParam = fetchData($queryParam);

# Get Journal Counter
$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter']+1,3);

# Transform No Jurnal dari No Transaksi
$tmpNoJurnal = explode('/',$param['notransaksi']);
$nojurnal = $tmpNoJurnal[0]."/".$tmpNoJurnal[1]."/".$kodeJurnal."/".$konter;
#======================== Nomor Jurnal =============================

#=== Transform Data ===
$dataRes['header'] = array();
$dataRes['detail'] = array();

#1. Data Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>'M0',
    'tanggal'=>$dataH[0]['tanggal'],
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$dataH[0]['notransaksi'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1'
);

#2. Data Detail
# Get Data from Kegiatan
$i = 0;
$whereKeg = "";
foreach($dataD as $row) {
    if($i==0) {
        $whereKeg .= "kodekegiatan='".$row['kodekegiatan']."'";
    } else {
        $whereKeg .= " or kodekegiatan='".$row['kodekegiatan']."'";
    }
    $i++;
}

$queryKeg = selectQuery($dbname,'setup_kegiatan',"kodekegiatan,namakegiatan,noakun",$whereKeg);
$tmpRes = fetchData($queryKeg);
$resKeg = array();
foreach($tmpRes as $row) {
    $resKeg[$row['kodekegiatan']]['nama'] = $row['namakegiatan'];
    $resKeg[$row['kodekegiatan']]['akun'] = $row['noakun'];
}

# Detail (Debet)
$noUrut = 1;
$totalJumlah = 0;
foreach($dataD as $row) {
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$resKeg[$row['kodekegiatan']]['akun'],
        'keterangan'=>'Pemeliharaan '.$resKeg[$row['kodekegiatan']]['nama'],
        'jumlah'=>$costRawat + $row['upahpremi'] + $row['upahkerja'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$row['kodeorg'],
        'kodekegiatan'=>$row['kodekegiatan'],
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>'',
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
    );
    $totalJumlah += ($row['jumlahhk'] * $row['umr']) + $row['upahpremi'] + $row['upahkerja'];
    $noUrut++;
}

# Detail (Kredit)
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'nourut'=>$noUrut,
    'noakun'=>$resParam[0]['noakunkredit'],
    'keterangan'=>'Pemeliharaan '.$dataH[0]['tipetransaksi'],
    'jumlah'=>$totalJumlah*(-1),
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>'',
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>$dataH[0]['notransaksi'],
    'noaruskas'=>'',
    'kodevhc'=>'',
    'nodok'=>'',
);

# Total D/K
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;

#=== Insert Data ===
$errorDB = "";

# Header
$queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
if(!mysql_query($queryH)) {
    $errorDB .= "Header :".mysql_error()."\n";
}

# Detail
if($errorDB=='') {
    foreach($dataRes['detail'] as $key=>$dataDet) {
        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
        if(!mysql_query($queryD)) {
            $errorDB .= "Detail ".$key." :".mysql_error()."\n";
        }
    }

    #=== Switch Jurnal to 1 ===
    # Cek if already posted
    $queryJ = selectQuery($dbname,'kebun_aktifitas',"jurnal","notransaksi='".
        $param['notransaksi']."'");
    $isJ = fetchData($queryJ);
    if($isJ[0]['jurnal']==1) {
        $errorDB .= "Data posted by another user";
    } else {
        $queryToJ = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>1),
            "notransaksi='".$dataH[0]['notransaksi']."'");
        if(!mysql_query($queryToJ)) {
            $errorDB .= "Posting Mark Error :".mysql_error()."\n";
        }
        $queryKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+1),
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if(!mysql_query($queryKonter)) {
            $errorDB .= "Update Counter Error :".mysql_error()."\n";
        }
    }
}

if($errorDB!="") {
    // Rollback
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n";
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n";
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n";
    }
    echo "DB Error :\n".$errorDB;
    exit;
} else {
    // Posting Success
    
}
?>
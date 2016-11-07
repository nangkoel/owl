<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;

/** Tutup Tahun **/
#=== Extract Data ====
# Get PT
$pt = getPT($dbname,$param['kodeorg']);
if($pt==false) {
    $pt = getHolding($dbname,$param['kodeorg']);
}

# Tanggal dan Kode Jurnal
$tgl = $param['tahun']."1231";
$kodejurnal = 'CLSY';


#==================== Journal Counter ==================
$nojurnal = $tgl."/".$param['kodeorg'].
    "/".$kodejurnal."/999";
#==================== Journal Counter ==================

# Cek apakah tahun sudah ditutup
$qCek = selectQuery($dbname,'keu_jurnalht','*',
    "nojurnal='".$nojurnal."'");
$resCek = fetchData($qCek);
if(!empty($resCek)) {
    echo 'Warning : Unit ini sudah melakukan tutup tahun';
    exit;
}

# Get Sum dari Jurnal
$query = selectQuery($dbname,'keu_jurnaldt_vw','substr(nojurnal,10,4) as kodeorg,sum(jumlah) as jumlah',
    "substr(nojurnal,10,4)='".$param['kodeorg']."' and left(nojurnal,4)='".$param['tahun']."'").
    "group by substr(nojurnal,10,4)";
$data = fetchData($query);
if(empty($data)) {
    echo 'Warning : Data untuk Unit ini tidak ada';
    exit;
}

# Get Akun
if($data[0]['jumlah']>0) {
    # Rugi
    $akunDebet = '3110300';
    $akunKredit = '3110400';
} else {
    # Laba
    $akunDebet = '3110400';
    $akunKredit = '3110300';
}

# Prep Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>$kodejurnal,
    'tanggal'=>$tgl,
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>'TUTUP/'.$param['kodeorg'].'/'.$param['tahun'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1'
);

# Data Detail
$noUrut = 1;

# Debet
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$tgl,
    'nourut'=>$noUrut,
    'noakun'=>$akunDebet,
    'keterangan'=>'Tutup Tahun '.$param['tahun'].' Unit '.$param['kodeorg'],
    'jumlah'=>$data[0]['jumlah'],
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>$pt['kode'],
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>'',
    'noaruskas'=>'',
    'kodevhc'=>'',
    'nodok'=>'',
    'kodeblok'=>''
);
$noUrut++;

# Kredit
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$tgl,
    'nourut'=>$noUrut,
    'noakun'=>$akunKredit,
    'keterangan'=>'Tutup Tahun '.$param['tahun'].' Unit '.$param['kodeorg'],
    'jumlah'=>-1*$data[0]['jumlah'],
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>$pt['kode'],
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>'',
    'noaruskas'=>'',
    'kodevhc'=>'',
    'nodok'=>'',
    'kodeblok'=>''
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
        echo '1';
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
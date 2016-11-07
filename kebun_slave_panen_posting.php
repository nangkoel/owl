<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;

#=== Get Data ===
# Header
$queryH = selectQuery($dbname,'kebun_aktifitas',"*","notransaksi='".
    $param['notransaksi']."'");
	
$dataH = fetchData($queryH);

#====cek periode===============================
$tgl = str_replace("-","",$dataH[0]['tanggal']);
if($_SESSION['org']['period']['start']>$tgl)
    exit('Error:Tanggal diluar periode aktif');

# Detail
$queryD = selectQuery($dbname,'kebun_prestasi',"*","notransaksi='".
    $param['notransaksi']."'");
$dataD = fetchData($queryD);

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

// Cek Kebun Karyawan
$nikKary = "";
foreach($dataD as $row) {
	if($nikKary!='') {$nikKary .= ",";}
	$nikKary .= $row['nik'];
}

$karyList = makeOption($dbname,'datakaryawan',"karyawanid,lokasitugas",
	"karyawanid in (".$nikKary.")");

#======================== Kegiatan Panen ===========================
$kodeJurnal = 'PNN01';
$queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit,noakundebet',
    " jurnalid='".$kodeJurnal."'");
$resParam = fetchData($queryParam);

      $akunkredit=$resParam[0]['noakunkredit']; 
      $akundebet =$resParam[0]['noakundebet'];
//default kodekegiatan panen/potong buah      
$kodekegiatan= $akundebet."01";     
      
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
    'kodejurnal'=>$kodeJurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$dataH[0]['notransaksi'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1',
    'revisi'=>'0'
);

#2. Data Detail
# Get Data from Kegiatan
$i = 0;

# Detail (Debet)
$noUrut = 1;
$totalJumlah = 0;
$totalDetail = array(); // Total per Kebun
foreach($dataD as $row) {
	//$tmpJumlah = ($row['jumlahhk'] * $row['umr']) + $row['upahpremi'] + $row['upahkerja'];
	$tmpJumlah = $row['umr'] + $row['upahpremi'] + $row['upahkerja'];
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$akundebet,
        'keterangan'=>'Potong Buah',
        'jumlah'=>$tmpJumlah,
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>substr($row['kodeorg'],0,4),
        'kodekegiatan'=>$kodekegiatan,
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>$row['notransaksi'],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
		'kodeblok'=>$row['kodeorg'],
		'revisi'=>'0'
    );
    //$totalJumlah += ($row['jumlahhk'] * $row['umr']) + $row['upahpremi'] + $row['upahkerja'];
	$totalJumlah+=$tmpJumlah;
	if(!isset($totalDetail[$karyList[$row['nik']]]))
		$totalDetail[$karyList[$row['nik']]] = $tmpJumlah;
	else
		$totalDetail[$karyList[$row['nik']]] += $tmpJumlah;
    $noUrut++;
}

// Akun Intraco
$kebunList = '';
foreach($totalDetail as $kebun=>$cost) {
	if($kebunList!='') {$kebunList .= ',';}
	$kebunList .= "'".$kebun."'";
}
$whereAkun = "jenis='intra' and kodeorg in (".$kebunList.")";
$queryAkun = selectQuery($dbname,'keu_5caco',"kodeorg,akunpiutang,akunhutang",$whereAkun);
$akunIntraco = fetchData($queryAkun);
$optAkunIntra = array();
foreach($akunIntraco as $row) {
	$optAkunIntra[$row['kodeorg']] = array(
		'piutang' => $row['akunpiutang'],
		'hutang' => $row['akunhutang']
	);
}

foreach($totalDetail as $kebun=>$cost) {
	if($kebun==$_SESSION['empl']['lokasitugas']) {
		# Detail (Kredit)
		$dataRes['detail'][] = array(
			'nojurnal'=>$nojurnal,
			'tanggal'=>$dataH[0]['tanggal'],
			'nourut'=>$noUrut,
			'noakun'=>$akunkredit,
			'keterangan'=>'Potong Buah',
			'jumlah'=>$cost*(-1),
			'matauang'=>'IDR',
			'kurs'=>'1',
			'kodeorg'=>'',
			'kodekegiatan'=>$kodekegiatan,
			'kodeasset'=>'',
			'kodebarang'=>'',
			'nik'=>'',
			'kodecustomer'=>'',
			'kodesupplier'=>'',
			'noreferensi'=>$dataH[0]['notransaksi'],
			'noaruskas'=>'',
			'kodevhc'=>'',
			'nodok'=>'',
			'kodeblok'=>'',
			'revisi'=>'0'
		);
	} else {
		$dataRes['detail'][] = array(
			'nojurnal'=>$nojurnal,
			'tanggal'=>$dataH[0]['tanggal'],
			'nourut'=>$noUrut,
			'noakun'=>$optAkunIntra[$kebun]['hutang'],
			'keterangan'=>'Potong Buah',
			'jumlah'=>$cost*(-1),
			'matauang'=>'IDR',
			'kurs'=>'1',
			'kodeorg'=>'',
			'kodekegiatan'=>$kodekegiatan,
			'kodeasset'=>'',
			'kodebarang'=>'',
			'nik'=>'',
			'kodecustomer'=>'',
			'kodesupplier'=>'',
			'noreferensi'=>$dataH[0]['notransaksi'],
			'noaruskas'=>'',
			'kodevhc'=>'',
			'nodok'=>'',
			'kodeblok'=>'',
			'revisi'=>'0'
		);
	}
	$noUrut++;
}

# Total D/K
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;

foreach($totalDetail as $kebun=>$cost) {
	if($kebun!=$_SESSION['empl']['lokasitugas']) {
		if(!isset($optAkunIntra[$kebun])) {
			exit("Warning, Account Intraco for ".$kebun." is not set.\nData couldn't be posted");
		}
	}
}

#=== Insert Data ===
$errorDB = "";

# Header
$queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);

if(!mysql_query($queryH)) {
    $errorDB .= "Header :".mysql_error()."\n test";
}

# Detail
if($errorDB=='') {
    foreach($dataRes['detail'] as $key=>$dataDet) {
        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
		if(!mysql_query($queryD)) {
            $errorDB .= "Detail ".$key." :".mysql_error()."\n ini yang error";
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
            $errorDB .= "Update Counter Error :".mysql_error()."\n".$errorDB."___".$queryKonter;
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
    echo "DB Error :\n".$errorDB."___".$queryRB2;
    exit;
} else {
    // Posting Success
    jurnalIntraco($dbname, $param, $totalDetail, $optAkunIntra, $kodekegiatan, $akundebet, "Potong Buah", $dataRes);
}

/**
 * jurnalIntraco
 * Melakukan jurnal intraco jika ada karyawan yang diluar kebun
 * @param	array	$costRawatDetail	Detail Cost Perawatan per Kebun
 * @param	array	$optAkunIntra		Akun Intraco yang sudah didefinisikan sebelumnya
 * @param	string	$kodeKeg			Kode Kegiatan
 * @param	string	$akunKeg			Akun Gaji yg harus dibayar
 * @param	string	$nameKeg			Nama Kegiatan
 * @param	array	$dataRes			Data untuk Jurnal Kegiatan
 */
function jurnalIntraco($dbname, $param, $costDetail, $optAkunIntra, $kodeKeg, $akunKeg, $nameKeg, $dataRes) {
	$dataIntraco = array();
	
	$i=0;
	foreach($costDetail as $kebun=>$cost) {
		if($kebun!=$_SESSION['empl']['lokasitugas']) {
			$dataIntraco[$kebun]['header'] = $dataRes['header'];
			
			#======================== Nomor Jurnal =============================
			$kodeJurnal = 'PNN01';
			$queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit',
				"kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
			$resParam = fetchData($queryParam);

			# Get Journal Counter
			$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
				"kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
			$tmpKonter = fetchData($queryJ);
			//if(empty($tmpKonter)) {exit("Warning, Journal Group ".$kodeJurnal." Counter for ".$kebun." not set");}
			$konter = addZero($tmpKonter[0]['nokounter']+1+$i,3);

			# Transform No Jurnal dari No Transaksi
			$tmpNoJurnal = explode('/',$param['notransaksi']);
			$nojurnal = $tmpNoJurnal[0]."/".$kebun."/".$kodeJurnal."/".$konter;
			#======================== Nomor Jurnal =============================
			
			// Modify Data Header untuk Jurnal
			$dataIntraco[$kebun]['header']['nojurnal'] = $nojurnal;
			$dataIntraco[$kebun]['header']['totaldebet']= $cost;
			$dataIntraco[$kebun]['header']['totalkredit']= $cost;
			
			// Data Detail (Debet)
			$dataIntraco[$kebun]['detail'][] = array(
				'nojurnal'=>$nojurnal,
				'tanggal'=>$dataIntraco[$kebun]['header']['tanggal'],
				'nourut'=>1,
				'noakun'=>$optAkunIntra[$kebun]['piutang'],
				'keterangan'=>$nameKeg.' '.$_SESSION['empl']['lokasitugas'],
				'jumlah'=>$cost,
				'matauang'=>'IDR',
				'kurs'=>'1',
				'kodeorg'=>$kebun,
				'kodekegiatan'=>$kodeKeg,
				'kodeasset'=>'',
				'kodebarang'=>'',
				'nik'=>'',
				'kodecustomer'=>'',
				'kodesupplier'=>'',
				'noreferensi'=>$param['notransaksi'],
				'noaruskas'=>'',
				'kodevhc'=>'',
				'nodok'=>'',
				'kodeblok'=>'',
				'revisi'=>'0'
			);
			
			// Data Detail (Kredit)
			$dataIntraco[$kebun]['detail'][] = array(
				'nojurnal'=>$nojurnal,
				'tanggal'=>$dataIntraco[$kebun]['header']['tanggal'],
				'nourut'=>2,
				'noakun'=>$akunKeg,
				'keterangan'=>$nameKeg.' '.$_SESSION['empl']['lokasitugas'],
				'jumlah'=>$cost*(-1),
				'matauang'=>'IDR',
				'kurs'=>'1',
				'kodeorg'=>$kebun,
				'kodekegiatan'=>$kodeKeg,
				'kodeasset'=>'',
				'kodebarang'=>'',
				'nik'=>'',
				'kodecustomer'=>'',
				'kodesupplier'=>'',
				'noreferensi'=>$param['notransaksi'],
				'noaruskas'=>'',
				'kodevhc'=>'',
				'nodok'=>'',
				'kodeblok'=>'',
				'revisi'=>'0'
			);
			$i++;
		}
	}
	
	$errorDB='';
	foreach($dataIntraco as $dataRes) {
		# Header
		$queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
		if(!mysql_query($queryH)) {
			$errorDB .= "Header :".mysql_error()."\n".$queryH;
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
			// $queryJ = selectQuery($dbname,'kebun_aktifitas',"jurnal","notransaksi='".
				// $param['notransaksi']."'");
			// $isJ = fetchData($queryJ);
			// if($isJ[0]['jurnal']==1) {
				// $errorDB .= "Data posted by another user";
			// } else {
				$queryToJ = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>1),
					"notransaksi='".$param['notransaksi']."'");
				if(!mysql_query($queryToJ)) {
					$errorDB .= "Posting Mark Error :".mysql_error()."\n";
				}
				$queryKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+$i),
					"kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
				if(!mysql_query($queryKonter)) {
					$errorDB .= "Update Counter Error :".mysql_error()."\n";
				}
			// }
		}
	}
	echo $errorDB;
}
?>
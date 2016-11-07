<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;

// Validation: Total Detail must be 0
$qVp = "select sum(jumlah) as total from ".$dbname.".keu_vpdt where novp='".
	$param['novp']."' group by novp";
$resVp = fetchData($qVp);
if(empty($resVp)) {
	exit("Warning: Detail Empty");
}

if(abs(intval($resVp[0]['total']))>0.01) {
	exit("Warning: Detail is not balance\nBalance: ".$resVp[0]['total']);
}

#=== Get Data ===
# Header
$queryH = selectQuery($dbname,'keu_vpht',"*","novp='".
    $param['novp']."'");
$dataH = fetchData($queryH);

# Detail
$queryD = selectQuery($dbname,'keu_vpdt',"*","novp='".
    $param['novp']."'");
$dataD = fetchData($queryD);

#=== Cek if posted ===
$error0 = "";
if($dataH[0]['posting']==1) {
    $error0 .= $_SESSION['lang']['errisposted'];
}
if($error0!='') {
    echo "Warning :\n".$error0;
    exit;
}
#====cek periode
$tgl = str_replace("-","",$dataH[0]['tanggal']);
if($_SESSION['org']['period']['start']>$tgl)
    exit('Warning: Date beyond active period');

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

#=== Transform Data ===
$dataRes['header'] = array();
$dataRes['detail'] = array();
$dataResoto['header'] = array();
$dataResoto['detail'] = array();

$kodeJurnal = 'VP';

// Get Supplier from PO
$qPO = selectQuery($dbname,'keu_tagihanht','kodesupplier',"nopo='".$dataH[0]['nopo']."'");
$resPO = fetchData($qPO);
if(empty($resPO)) {
	exit("Warning: Data Invalid, Invoice Document not found");
}
$supp = $resPO[0]['kodesupplier'];

#1. Data Header
# Get Journal Counter
$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter']+1,3);

# Prep No Jurnal
$nojurnal = str_replace('-','',$dataH[0]['tanggal'])."/".$dataH[0]['kodeorg']."/".
    $kodeJurnal."/".$konter;

# Prep Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>$kodeJurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$dataH[0]['novp'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1',
    'revisi'=>'0'
);

#2. Data Detail
# Detail (Many)
$noUrut = 1;
$totalDebet = 0;
$totalKredit = 0;
foreach($dataD as $row) {    
if($row['matauang']!='IDR'){
	if(($row['kurs']=='0')||($row['kurs']=='')){
		exit("error: Using ".$row['matauang']." currency can't zero or empty");
	}
}else{
	$row['kurs']=1;
}
$cekMtuang[$row['matauang']]=$row['matauang'];
	$row['jumlah']=$row['jumlah']*$row['kurs'];
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$row['noakun'],
        'keterangan'=>$dataH[0]['penjelasan'],
        'jumlah'=>$row['jumlah'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$dataH[0]['kodeorg'],
        'kodekegiatan'=>'',
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>$supp,
        'noreferensi'=>$dataH[0]['novp'],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>$dataH[0]['nopo'],
        'kodeblok'=>'',
		'revisi'=>'0'       
    );
	if($row['jumlah']<0) {
		$totalKredit += $row['jumlah'];
	} else {
		$totalDebet += $row['jumlah'];
	}
    $noUrut++;
}
if(count($cekMtuang)>1){
	echo"<pre>";
	print_r($cekMtuang);
	echo"</pre>";
	exit("error: Mata uang harus seragam");
}
# Total D/K
$dataRes['header']['totaldebet']=$totalDebet; 
$dataRes['header']['totalkredit']=$totalKredit*(-1); 

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
    $queryJ = selectQuery($dbname,'keu_vpht',"posting","novp='".
        $param['novp']."'");
    $isJ = fetchData($queryJ);
    if($isJ[0]['posting']==1) {
        $errorDB .= "Data changed by other user";
    } else {
        $queryToJ = updateQuery($dbname,'keu_vpht',array('posting'=>1),
            "novp='".$dataH[0]['novp']."'");
        if(!mysql_query($queryToJ)) {
            $errorDB .= "Posting Flag Error :".mysql_error()."\n";
        }
    }
}

if($errorDB!="") {
    // Rollback
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'keu_vpht',array('posting'=>0),
        "novp='".$dataH[0]['novp']."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n";
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n";
    }
	exit("DB Error: ".$errorDB);
} else {
    // Posting Success
    #=== Add Counter Jurnal ===
    $queryJ = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+1),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    $errCounter = "";
    if(!mysql_query($queryJ)) {
        $errCounter.= "Update Counter Parameter Jurnal Error :".mysql_error()."\n";
    }
    if($errCounter!="") {
        $queryJRB = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        $errCounter = "";
        if(!mysql_query($queryJRB)) {
            $errorJRB .= "Rollback Parameter Jurnal Error :".mysql_error()."\n";
        }
        echo "DB Error :\n".$errorJRB;
        exit;
    }
    
}
?>
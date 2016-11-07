<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>

<?php

$param = $_POST;
 
$kegiatan="SELECT * FROM ".$dbname.". setup_parameterappl WHERE kodeaplikasi = 'TX'";
$query=mysql_query($kegiatan) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $excludeacc[$res['nilai']]=$res['nilai'];
}

#=== Get Data ===
# Header
$queryH = selectQuery($dbname,'keu_kasbankht',"*","notransaksi='".
    $param['notransaksi']."' and kodeorg='".$param['kodeorg'].
    "' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."' limit 1");
$dataH = fetchData($queryH);

# Detail
$queryD = selectQuery($dbname,'keu_kasbankdt',"*","notransaksi='".
    $param['notransaksi']."' and kodeorg='".$param['kodeorg'].
    "' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'");
$dataD = fetchData($queryD);

#=== Cek Jumlah Detail dan Header harus sama ===
$tmpJml = 0;
foreach($dataD as $row) {
    $tmpJml += $row['jumlah'];
}
if($tmpJml-$dataH[0]['jumlah']>0.0000001) {
    echo "Warning : Amount on header difference to the amount in detail\n";
    echo "Header: ".$dataH[0]['jumlah']."\n";
    echo "Detail: ".$tmpJml."\n";
    echo "Posting Failed";
    exit;
}
#=== Cek Detail yang timbul selisih kurs ===
if ($dataH[0]['jumlah']==0 && $dataH[0]['matauang']!='IDR'){
    foreach($dataD as $row) {
        $tmpJml += ($row['jumlah']*$row['kurs']);
    }
    if ($tmpJml>0){
        echo "Jumlah pada detail belum balance karena selisih kurs.\n";
        exit('Error');
    }
}

#=== Cek if posted ===
$error0 = "";
if($dataH[0]['posting']==1) {
    $error0 .= $_SESSION['lang']['errisposted'];
}
if($error0!='') {
    echo "Data Error :\n".$error0;
    exit;
}
#====cek periode
$dataH[0]['tanggal']=tanggaldgnbar($param['tglpost']);

$tgl = str_replace("-","",$dataH[0]['tanggal']);
if($_SESSION['org']['period']['start']>$tgl){
    exit('Error:Date beyond active period');
}
//if($_SESSION['org']['period']['end']<$tgl){
//    exit('Error:Date above active period');
//}
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
$scekakun="select * from ".$dbname.".keu_kasbankdt where noakun='' and notransaksi='".$param['notransaksi']."'";
$qcekakun=  mysql_query($scekakun) or die(mysql_error($conn));
$rcekakun=  mysql_num_rows($qcekakun);
if($rcekakun>0){
    exit("warning: ada data dengan ".$_SESSION['lang']['noakun']." yang kosong");
}

/** Update Nomor Pembayaran */
$data = array('nobayar'=>$param['nobayar']);
$scek="select * from ".$dbname.".keu_kasbankht where nobayar='".$param['nobayar']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";//tambahan jamhari cek pembayaran
$qcek=  mysql_query($scek) or die(mysql_error($conn));
$rcek=  mysql_num_rows($qcek);
if($rcek<1){
    $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".
            $param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".
            $param['tipetransaksi']."'";
    $query = updateQuery($dbname,'keu_kasbankht',$data,$where);
    if(!mysql_query($query)) {
            echo "DB Error Update Transaction : ".mysql_error();
    }
}else{
    exit("warning: ".$_SESSION['lang']['nobayar']." sudah tersimpan di notransaksi yang lain");
}
//update tanggal posting
$data = array('tanggalposting'=>tanggalsystem($param['tglpost']));
$where = "notransaksi='".$param['notransaksi']."' and kodeorg='".
	$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".
	$param['tipetransaksi']."'";
$query = updateQuery($dbname,'keu_kasbankht',$data,$where);
if(!mysql_query($query)) {
	echo "DB Error Update Transaction : ".mysql_error();
}

#=== Cek if hutang unit ========================================================
if($dataH[0]['hutangunit']==1) {
    $pembayarhutang=$param['kodeorg'];    
    $pemilikhutang=$dataH[0]['pemilikhutang'];
    
    // kalo periode akuntansi unit beda, ga bisa diposting...
    $periodepembayar=makeOption($dbname,'setup_periodeakuntansi','kodeorg,periode',"kodeorg = '".$pembayarhutang."' and tutupbuku = 0");
    $periodepemilik=makeOption($dbname,'setup_periodeakuntansi','kodeorg,periode',"kodeorg = '".$pemilikhutang."' and tutupbuku = 0");
    if($periodepembayar[$pembayarhutang]!=$periodepemilik[$pemilikhutang]){
        echo "Warning : ".$_SESSION['lang']['periodeakuntansi']." do not match.\n".$pembayarhutang." : ".$periodepembayar[$pembayarhutang]."\n".$pemilikhutang." : ".$periodepemilik[$pemilikhutang];
        exit;
    }
    
    $noakunhutang=$dataH[0]['noakunhutang'];
    $kodejurnal='M';
    $tanggal=$dataH[0]['tanggal'];
    $tanggal=tanggalnormal($tanggal);
    $tanggal=tanggalsystem($tanggal);

    #=============== Get Induk Pemilik Hutang
    $whereNomilhut = "kodeorganisasi='".
        $pemilikhutang."'";
    $query = selectQuery($dbname,'organisasi','induk',
        $whereNomilhut);
    $noKon = fetchData($query);
    $indukpemilikhutang = $noKon[0]['induk'];
    
    #=============== Get Induk Pembayar Hutang
    $whereNoyarhut = "kodeorganisasi='".
        $param['kodeorg']."'";
    $query = selectQuery($dbname,'organisasi','induk',
        $whereNoyarhut);
    $noKon = fetchData($query);
    $indukpembayarhutang = $noKon[0]['induk'];
    
    if($indukpemilikhutang==$indukpembayarhutang)$jenisinduk='intra'; else $jenisinduk='inter';

    #=============== Get Nomor Jurnal Otomatis (pemilikhutang)
//    $whereNo = "kodekelompok='".$kodejurnal."' and kodeorg='".
//        $pemilikhutang."'";
    $whereNoindukph = "kodekelompok='".$kodejurnal."' and kodeorg='".
        $indukpemilikhutang."'";
    $query = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
        $whereNoindukph);
    
    $noKon = fetchData($query);
    $tmpC = $noKon[0]['nokounter'];
    $tmpC++;
    $konteroto = addZero($tmpC,3);
    $nojuroto = $tanggal."/".
        $pemilikhutang."/".$kodejurnal."/".
        $konteroto;
        
    #=============== Get Nomor Akun Caco
    // ini ga dipake soale dipilih secara manual sama usernya pas nginput kasbank
    $whereNocaco = "jenis='".$jenisinduk."' and kodeorg='".
        $pemilikhutang."'";
    $query = selectQuery($dbname,'keu_5caco','akunpiutang',
        $whereNocaco);
    $noKon = fetchData($query);
    $noakuncaco = $noKon[0]['akunpiutang'];

    #=============== Get Nomor Akun Caco Lawannya
    // ini yang dipake
    $whereNocacol = "jenis='".$jenisinduk."' and kodeorg='".
        $pembayarhutang."'";
    $query = selectQuery($dbname,'keu_5caco','akunpiutang',
        $whereNocacol);
    $noKon = fetchData($query);
    $noakuncacol = $noKon[0]['akunpiutang'];
    
}   


#=== Transform Data ===
$dataRes['header'] = array();
$dataRes['detail'] = array();
$dataResoto['header'] = array();
$dataResoto['detail'] = array();

#1. Data Header
# Get Journal Counter
$queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter']+1,3);

# Prep No Jurnal
$nojurnal = str_replace('-','',$dataH[0]['tanggal'])."/".$dataH[0]['kodeorg']."/".
    $dataD[0]['kode']."/".$konter;

# Prep Header
$dataRes['header'] = array(
    'nojurnal'=>$nojurnal,
    'kodejurnal'=>$dataD[0]['kode'],
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

# Prep Header Otomatis =========================================================
$dataResoto['header'] = array(
    'nojurnal'=>$nojuroto,
    'kodejurnal'=>$kodejurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'tanggalentry'=>date('Ymd'),
    'posting'=>'0',
    'totaldebet'=>'0',
    'totalkredit'=>'0',
    'amountkoreksi'=>'0',
    'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
    'autojurnal'=>'1',
    'matauang'=>'IDR',
    'kurs'=>'1',
    'revisi'=>'0'    
);

#2. Data Detail
# Detail (Many)
$noUrut = 1;
$totalJumlah = 0;
foreach($dataD as $row) {    
    if(substr($row['kode'],1,1)=='M') {
        $jumlah = $row['jumlah']*(-1);
    } else {
        $jumlah = $row['jumlah'];
    }

    $dKurs=1;
    $dMtUang='IDR';
    if($row['matauang']!='IDR')
    {
		if(($row['kurs']=='')||intval($row['kurs'])==0){
			exit("error: Currency can't empty or zero");
		}else{
			//$dMtUang=$row['matauang'];
			$dKurs=$row['kurs'];
			$jumlah=$jumlah*$dKurs;
		}
    }else{
		$jumlah=$jumlah*$dKurs;
	}
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$row['noakun'],
        'keterangan'=>$row['keterangan2'],
        'jumlah'=>$jumlah,
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>$row['kodeorg'],
        'kodekegiatan'=>$row['kodekegiatan'],
        'kodeasset'=>$row['kodeasset'],
        'kodebarang'=>$row['kodebarang'],
        'nik'=>$row['nik'],
        'kodecustomer'=>$row['kodecustomer'],
        'kodesupplier'=>$row['kodesupplier'],
        'noreferensi'=>$dataH[0]['notransaksi'],
        'noaruskas'=>$row['noaruskas'],
        'kodevhc'=>$row['kodevhc'],
        'nodok'=>$row['nodok'],
        'kodeblok'=>$row['orgalokasi'],
     'revisi'=>'0'       
    );
    $totalJumlah += $jumlah;
    $noUrut++;
}


# Detail (One)
$dataRes['detail'][] = array(
    'nojurnal'=>$nojurnal,
    'tanggal'=>$dataH[0]['tanggal'],
    'nourut'=>$noUrut,
    'noakun'=>$dataH[0]['noakun'],
    'keterangan'=>$dataH[0]['keterangan'],
    'jumlah'=>$totalJumlah*(-1),
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>$dataH[0]['kodeorg'],
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
    'kodeblok'=>'',
    'revisi'=>'0'    
);

#2. Data Detail Otomatis =======================================================
# Detail (Many)
$noUrut = 1;
$totalJumlahOto = 0;
foreach($dataD as $row) {
    
    // default: lempar ke unit
    $ok=true;
    if(!empty($excludeacc))foreach($excludeacc as $acc){
        if(substr($row['noakun'],0,3)==$acc){
            // kalo exclude, jangan lempar ke unit
            $ok=false;
        }
    }
    
    // kalo detailnya bukan hutang unit, jangan lempar ke unit
    if($row['hutangunit1']==0)$ok=false;
    
//    echo "error ff:".$row['hutangunit1'];
//    exit;
    
    // kalo OK, lempar ke unit
    if($ok){
        if(substr($row['kode'],1,1)=='M') {
            $jumlah = $row['jumlah']*(-1);
        } else {
            $jumlah = $row['jumlah'];
        }
        $dKurs=1;
        $dMtUang='IDR';
        if($row['matauang']!='IDR')
        {
            //$dMtUang=$row['matauang'];
            $dKurs=$row['kurs'];
            $jumlah=$jumlah*$dKurs;
        }
        $dataResoto['detail'][] = array(
            'nojurnal'=>$nojuroto,
            'tanggal'=>$dataH[0]['tanggal'],
            'nourut'=>$noUrut,
            'noakun'=>$noakunhutang,
            'keterangan'=>$row['keterangan2'],
            'jumlah'=>$jumlah,
            'matauang'=>'IDR',
            'kurs'=>'1',
            'kodeorg'=>$pemilikhutang,
            'kodekegiatan'=>$row['kodekegiatan'],
            'kodeasset'=>$row['kodeasset'],
            'kodebarang'=>$row['kodebarang'],
            'nik'=>$row['nik'],
            'kodecustomer'=>$row['kodecustomer'],
            'kodesupplier'=>$row['kodesupplier'],
            'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
            'noaruskas'=>$row['noaruskas'],
            'kodevhc'=>$row['kodevhc'],
            'nodok'=>$row['nodok'],
            'kodeblok'=>$row['orgalokasi'],
        'revisi'=>'0'        
        );
        $totalJumlahOto += $jumlah;
        $noUrut++;                    
    }
}


# Detail (One) Otomatis ========================================================
$dataResoto['detail'][] = array(
    'nojurnal'=>$nojuroto,
    'tanggal'=>$dataH[0]['tanggal'],
    'nourut'=>$noUrut,
    'noakun'=>$noakuncacol,
    'keterangan'=>$dataH[0]['keterangan'],
    'jumlah'=>$totalJumlahOto*(-1),
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>$pemilikhutang,
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>'',
    'nik'=>'',
    'kodecustomer'=>'',
    'kodesupplier'=>'',
    'noreferensi'=>$pembayarhutang.$dataH[0]['notransaksi'],
    'noaruskas'=>'',
    'kodevhc'=>'',
    'nodok'=>'',
    'kodeblok'=>'',
    'revisi'=>'0'    
);

# Total D/K
//foreach($dataD as $row){
//    if($row['jumlah']>0)
//    {
//        if($row['matauang']!='IDR')
//        {
//            $row['jumlah']=$row['jumlah']*$row['kurs'];
//        }
       
       $dataRes['header']['totaldebet']=$totalJumlah; 
       $dataRes['header']['totalkredit']=$totalJumlah*(-1); 
       $dataResoto['header']['totaldebet']=$totalJumlahOto; 
       $dataResoto['header']['totalkredit']=$totalJumlahOto*(-1); 
//    }  
//}

//$qweqwe.=$totalJumlahOto.'+';  
//echo "warning : ".$qweqwe;
//echo "Warning :<pre>".print_r($dataResoto)."</pre>";
//exit;

/*
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;
*/
    
//    echo "Data Error :qweqeqeqweqwe\n".$pemilikhutang." ".$noakunhutang." ".$nojur.'\n<pre>'.($dataH).'</pre>';    
//    echo "Data Error :".$pemilikhutang." ".$noakunhutang." ".$nojuroto." ".$indukpemilikhutang." ".$indukpembayarhutang." ".$noakuncaco;
//    exit;

#=== Insert Data ===
$errorDB = "";

# Header
$queryH = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
if(!mysql_query($queryH)) {
    $errorDB .= "Header :".mysql_error()."\n";
}

# Header Otomatis ==============================================================
if($dataH[0]['hutangunit']==1) { 
    $queryH = insertQuery($dbname,'keu_jurnalht',$dataResoto['header']);
    if(!mysql_query($queryH)) {
        $errorDB .= "Header :".mysql_error()."\n";
    }    
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
    $queryJ = selectQuery($dbname,'keu_kasbankht',"posting","notransaksi='".
        $param['notransaksi']."' and kodeorg='".$param['kodeorg']."'");
    $isJ = fetchData($queryJ);
    if($isJ[0]['posting']==1) {
        $errorDB .= "Data changed by other user";
    } else {
        $queryToJ = updateQuery($dbname,'keu_kasbankht',array('posting'=>1),
            "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."'");
        if(!mysql_query($queryToJ)) {
            $errorDB .= "Posting Flag Error :".mysql_error()."\n";
        }
    }
}

# Detail Otomatis ==============================================================
if($dataH[0]['hutangunit']==1) { 
if($errorDB=='') {
    foreach($dataResoto['detail'] as $key=>$dataDet) {
        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
        if(!mysql_query($queryD)) {
            $errorDB .= "Detail ".$key." :".mysql_error()."\n";
        }
    }
}    
}

if($errorDB!="") {
    // Rollback
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'keu_kasbankht',array('posting'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n";
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n";
    }
    
    // Rollback Otomatis =======================================================
if($dataH[0]['hutangunit']==1) {    
    $whereoto = "nojurnal='".$nojuroto."'";
    $queryRBoto = "delete from `".$dbname."`.`keu_jurnalht` where ".$whereoto;
    if(!mysql_query($queryRBoto)) {
        $errorDB .= "Rollback 3 Error :".mysql_error()."\n";
    }
}    
    echo "DB Error :\n".$errorDB;
    exit;
} else {
    // Posting Success
    #=== Add Counter Jurnal ===
    $queryJ = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+1),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
    $errCounter = "";
    if(!mysql_query($queryJ)) {
        $errCounter.= "Update Counter Parameter Jurnal Error :".mysql_error()."\n";
    }
    if($errCounter!="") {
        $queryJRB = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
        $errCounter = "";
        if(!mysql_query($queryJRB)) {
            $errorJRB .= "Rollback Parameter Jurnal Error :".mysql_error()."\n";
        }
        echo "DB Error :\n".$errorJRB;
        exit;
    }
    #=== Add Counter Jurnal Otomatis === =======================================
if($dataH[0]['hutangunit']==1) {    
    $queryJ = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konteroto),
        "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
    $errCounter = "";
    if(!mysql_query($queryJ)) {
        $errCounter.= "Update Counter Parameter Jurnal Error :".mysql_error()."\n";
    }
    
    if($errCounter!="") {
        $queryJRB = updateQuery($dbname,'keu_5kelompokjurnal',array($noKon[0]['nokounter']),
            "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
        $errCounter = "";
        if(!mysql_query($queryJRB)) {
            $errorJRB .= "Rollback Parameter Jurnal Error :".mysql_error()."\n";
        }
        echo "DB Error :\n".$errorJRB;
        exit;
    }
}    
}
?>
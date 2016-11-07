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

if ($param['metode']=='konfirmData'){
    $queryKonfirm = updateQuery($dbname,'kebun_aktifitas',array('konfirm'=>1),
        "notransaksi='".$param['notransaksi']."'");
    if(!mysql_query($queryKonfirm)) {
        echo "DB Error :\nConfirm Error :".mysql_error()."\n".$queryKonfirm;
    }
    exit;
}

# Prestasi
$queryD = selectQuery($dbname,'kebun_prestasi',"*","notransaksi='".
    $param['notransaksi']."'");
$dataD = fetchData($queryD);

# Absensi
$queryAbs = selectQuery($dbname,'kebun_kehadiran','nik,jhk,umr,insentif,hasilkerja,jjg',"notransaksi='".$param['notransaksi']."'");
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
if(count($dataD)>1) {
    $error1 .= $_SESSION['lang']['errdetailkelebihan']."\n";
}
if($error1!='') {
    echo "Data Error :\n".$error1;
    exit;
}

#=== Hitung Cost dari Absensi (Perawatan) ===
$costRawat = 0;
$costRawatDetail = array(); // Init Cost Rawat per Kebun
$totalHk = 0;
$nikKary = "";
if(!empty($dataAbs)) {
	foreach($dataAbs as $row) {
	if($row['nik']!='0000000000'){
	    if($nikKary!='') {$nikKary .= ",";}
		$nikKary .= $row['nik'];
	}else{
		#hapus jika karyawan=0000000000
		$sdelData="delete from ".$dbname.".kebun_kehadiran where nik='".$row['nik']."' and notransaksi='".$param['notransaksi']."'";
		mysql_query($sdelData);
	}
	
	}
	
	$qKary = selectQuery($dbname,'datakaryawan',"karyawanid,lokasitugas,kodeorganisasi",
		"karyawanid in (".$nikKary.")");
	$resKary = fetchData($qKary);
	$karyList = array();
	$kodePt = array();
	foreach($resKary as $row) {
		$karyList[$row['karyawanid']] = array(
			'lokasitugas' => $row['lokasitugas'],
			'pt' => $row['kodeorganisasi']
		);
		$kodePt[$row['lokasitugas']] = $row['kodeorganisasi'];
	}
	//$karyList = makeOption($dbname,'datakaryawan',"karyawanid,lokasitugas",
	//	"karyawanid in (".$nikKary.")");
	
	$totalHk=0;
	$totalHkerD=0;
	$totalJjg=0;
    foreach($dataAbs as $row) {
        // Cost Total
		//$costRawat += ($row['jhk']*$row['umr']) + $row['insentif'];
		$costRawat += $row['umr'] + $row['insentif'];
        $totalHk += $row['jhk'];
		$totalHkerD+=$row['hasilkerja'];
		$totalJjg+=$row['jjg'];
		
		// Cost Rawat Per Kebun
		if(isset($costRawatDetail[$karyList[$row['nik']]['lokasitugas']])) {
			$costRawatDetail[$karyList[$row['nik']]['lokasitugas']] +=($row['umr']+$row['insentif']);
		} else {
			$costRawatDetail[$karyList[$row['nik']]['lokasitugas']] =($row['umr']+$row['insentif']);
		}
    }
}

#=== Cek if HK belum sama ===
//$qwe=$totalHk-$dataD[0]['jumlahhk']; buat ngecek pengurangan, bisa koma2 sampe e-16. dz april 27, 2012 10:13 kpw samarinda
$totalHk=round($totalHk,2);                             // diround hingga 2 desimal
$dataD[0]['jumlahhk']=round($dataD[0]['jumlahhk'],2);   // diround hingga 2 desimal
$qwe=$totalHk-$dataD[0]['jumlahhk'];
if(empty($dataAbs) or ($totalHk!=$dataD[0]['jumlahhk'])) {
    echo 'Warning : HK Prestasi belum teralokasi dengan lengkap '.$qwe.'.';
    exit;
}


#============ cek if hasil kerja sama jjg(untuk yg memakai jjg, hasik kerja untuk yang pakai hasil kerja) =====================
#=== logika : jika jjg=0 berarti itu adalah kegiatan bkm biasa, jika jjg isi maka itu yang memakai satuan konversi
#=== jika jjg !=0 maka itu adalah pekerjaan yang memakai konversi, maka validasi memakai jjg saja karna hasil kerja mengikuti jjg
#=== jika jjg =0 maka itu adalah pekerjaan yang tidak dipengaruhi Jjg maka validasi menggunakan hasil kerja.
#untuk hasil kerja biasa
$hkerH=number_format($dataD[0]['hasilkerja'],2);
$hkerD=number_format($totalHkerD,2);

#untuk jjg
$totalJjgH=$dataD[0]['jjg'];
$totalJjgD=$totalJjg;


if($totalJjgH!=0)
{
	if($totalJjgH!=$totalJjgD)
	{
		exit("Error:Jumlah Janjang antara header dan total detail belum sama\nJjg kop/header : ".$totalJjgH."\nTotal detail Jjg :  ".$totalJjgD." ");
	}
}
else
{
	if($hkerH!=$hkerD)
	{
		exit("Error:Jumlah Hasil Kerja antara header dan total detail belum sama\nKop/header : ".$hkerH."\n Total detail :  ".$hkerD." ");
	}
}

//exit("Error:$totalJjgH.__.$totalJjgD");



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
    'posting'=>'1',
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
$kodeblok='';
$kodekegiatan='';
foreach($dataD as $row) {
if($row['kodeorg']==''){
    exit("error: Kode blok can't empty");//jamhari
}

    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$resKeg[$row['kodekegiatan']]['akun'],
        'keterangan'=>'Pemeliharaan '.$resKeg[$row['kodekegiatan']]['nama'],
        //'jumlah'=>$costRawat + $row['upahpremi'] + $row['upahkerja'],
		'jumlah'=>$costRawat, // Upah dari Prestasi tidak ikut ditambahkan
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>substr($row['kodeorg'],0,4),
        'kodekegiatan'=>$row['kodekegiatan'],
        'kodeasset'=>'',
        'kodebarang'=>'',
        'nik'=>'',
        'kodecustomer'=>'',
        'kodesupplier'=>'',
        'noreferensi'=>$dataH[0]['notransaksi'],
        'noaruskas'=>'',
        'kodevhc'=>'',
        'nodok'=>'',
        'kodeblok'=>$row['kodeorg'],
		'revisi'=>'0'
    );
	//$totalJumlah +=$costRawat + $row['upahpremi'] + $row['upahkerja'];
	$totalJumlah +=$costRawat; // Upah dari Prestasi tidak ikut ditambahkan
    $noUrut++;
    $kodeblok=$row['kodeorg'];
    $kodekegiatan=$row['kodekegiatan'];
}

// Akun Intraco
$kebunList = '';
$kebunListInter = '';
foreach($costRawatDetail as $kebun=>$cost) {
	if($kodePt[$kebun]!=$_SESSION['empl']['kodeorganisasi']) {
		if($kebunListInter!='') {$kebunListInter.= ',';}
		$kebunListInter .= "'".$kebun."'";
	} else {
		if($kebunList!='') {$kebunList .= ',';}
		$kebunList .= "'".$kebun."'";
	}
}

// Akun Intraco
$optAkunIntra = array();
$statIntraco=0;
$statInteraco=0;
if(!empty($kebunList)) {
	$whereAkun = "jenis='intra' and kodeorg in (".$kebunList.")";
	$queryAkun = selectQuery($dbname,'keu_5caco',"kodeorg,akunpiutang,akunhutang",$whereAkun);
	$akunIntraco = fetchData($queryAkun);
	foreach($akunIntraco as $row) {
		$optAkunIntra[$row['kodeorg']] = array(
			'piutang' => $row['akunpiutang'],
			'hutang' => $row['akunhutang']
		);
	}
}

// Akun Interco
$optAkunInter = array();
if(!empty($kebunListInter)) {
	$whereAkunInter = "jenis='inter' and kodeorg in (".$kebunListInter.")";
	$queryAkunInter = selectQuery($dbname,'keu_5caco',"kodeorg,akunpiutang,akunhutang",$whereAkunInter);
	$akunInterco = fetchData($queryAkunInter);	
	foreach($akunInterco as $row) {
		$optAkunInter[$row['kodeorg']] = array(
			'piutang' => $row['akunpiutang'],
			'hutang' => $row['akunhutang']
		);
	}
}

foreach($costRawatDetail as $kebun=>$cost) {
	if($_SESSION['empl']['lokasitugas']==$kebun) {
		$noAkun = $resParam[0]['noakunkredit'];
		$keterangan = 'Pemeliharaan '.$dataH[0]['tipetransaksi'];
	} else {
		if($kodePt[$kebun]!=$_SESSION['empl']['kodeorganisasi']) {
			if(!isset($optAkunInter[$kebun])) {
				echo "Gagal, Interco Account for division ".$kebun." not exist. Please set interco account for division ".$kebun;
				exit;
			}
			$noAkun = $optAkunInter[$kebun]['hutang'];
			$keterangan = 'Pemeliharaan '.$dataH[0]['tipetransaksi'].
				' oleh '.$kebun;
                        $statInteraco=1;
		} else {
			if(!isset($optAkunIntra[$kebun])) {
				echo "Gagal, Intraco Account for division ".$kebun." not exist. Please set intraco account for division ".$kebun;
				exit;
			}
			$noAkun = $optAkunIntra[$kebun]['hutang'];
			$keterangan = 'Pemeliharaan '.$dataH[0]['tipetransaksi'].
				' oleh '.$kebun;
                        $statIntraco=1;
		}
	}
	
	# Detail (Kredit)
	$dataRes['detail'][] = array(
		'nojurnal'=>$nojurnal,
		'tanggal'=>$dataH[0]['tanggal'],
		'nourut'=>$noUrut,
		'noakun'=>$noAkun,
		'keterangan'=>$keterangan,
		'jumlah'=>$cost*(-1),
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
		'kodeblok'=>'',
		'revisi'=>'0'    
	);
	$noUrut++;
}

# Total D/K
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;

#=== Insert Data ===
$errorDB = "";

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
            $errorDB .= "Detail ".$key." :".mysql_error()."\n";//indz
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
            "notransaksi='".$dataH[0]['notransaksi']."'");
        if(!mysql_query($queryToJ)) {
            $errorDB .= "Posting Mark Error :".mysql_error()."\n";
        }
        $queryKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']+1),
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if(!mysql_query($queryKonter)) {
            $errorDB .= "Update Counter Error :".mysql_error()."\n";
        }
    // }
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
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
    echo "DB Error:\n".$errorDB;//indz
    exit;
} else {
    if($statIntraco==1){
        // Jurnal INTRACO
        jurnalIntraco($dbname, $param, $costRawatDetail, $optAkunIntra, $kodekegiatan, $resKeg[$kodekegiatan]['akun'], $resKeg[$kodekegiatan]['nama'],$dataRes);
    }
    if($statInteraco==1){
        // Jurnal INTERCO
        jurnalIntraco($dbname, $param, $costRawatDetail, $optAkunInter, $kodekegiatan, $resKeg[$kodekegiatan]['akun'], $resKeg[$kodekegiatan]['nama'],$dataRes);
    }

#proses jurnal materaial   MATERIAL==============MATERIAL=====================MATERIAL=================
#periksa apakah transaksi ini pernah di unposting
 $str="select * from ".$dbname.".log_transaksiht where notransaksireferensi='".$param['notransaksi']."'";
 $res=mysql_query($str);
 if(mysql_num_rows($res)>0)
 {
     exit(" Error: Posting ulang kegiatan berhasil, namun untuk material pada kegiatan tsb tidak dapat di posting ulang");
 }
    
#ambil notransaksi gudang
$nomor=Array();    
$str="select distinct kodegudang from ".$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."' and kodegudang!=''";
$resc=mysql_query($str);
$rowcek=  mysql_num_rows($resc);
if($rowcek==0){
    exit();
}
while($bar1=mysql_fetch_object($resc)){
        $gudang =$bar1->kodegudang;

        $num=1;//default value 
        $str="select max(notransaksi) as notransaksi from ".$dbname.".log_transaksiht where tipetransaksi=5 and kodegudang='".$gudang."' and
            tanggal>=".$_SESSION['gudang'][$gudang]['start']." and tanggal<=".$_SESSION['gudang'][$gudang]['end']." and notransaksireferensi!=''
                  order by notransaksi desc limit 1";
        //exit("error".$str);
        if($res=mysql_query($str))
        {
            while($bar=mysql_fetch_object($res))
            {
                    $num=$bar->notransaksi;
                    if($num!='')
                    {
                            $num=substr($num,7,4);
                            $num=1+intval($num);
                            $num=str_pad($num,4,"0",STR_PAD_LEFT);
                    }	
                    else
                    {
                            $num="0001";
                    }
            }    
        }
        $nomor[$bar1->kodegudang]=date('Ym')."M".$num;
        //exit("error:".$nomor[$bar1->kodegudang]);
        #ambil periode akintansi masing-masing gudang
        $strd="select periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$bar1->kodegudang."' and tutupbuku=0";
        $resd=mysql_query($strd);
        while($bard=mysql_fetch_object($resd))
        {
            $periode[$bar1->kodegudang]=$bard->periode;
        }
  }
  $brg=Array();
  $gud=Array();
  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
  $resa=mysql_query($str);
   #ambil saldo dan harga rata
  while($barf=mysql_fetch_object($resa)){
      $stru="select saldoakhirqty,hargarata,nilaisaldoakhir,qtykeluar,qtykeluarxharga from ".$dbname.".log_5saldobulanan where kodegudang='".$barf->kodegudang."'
                and kodebarang='".$barf->kodebarang."' and periode='".$periode[$barf->kodegudang]."'";
     
      $resu=mysql_query($stru);
      $saldo[$barf->kodegudang][$barf->kodebarang]=0;
      $harga[$barf->kodegudang][$barf->kodebarang]=0;
      while($baru=mysql_fetch_object($resu)){
          $saldo[$barf->kodegudang][$barf->kodebarang]=$baru->saldoakhirqty;
          $harga[$barf->kodegudang][$barf->kodebarang]=$baru->hargarata;
          $xkeluar[$barf->kodegudang][$barf->kodebarang]=$baru->qtykeluarxharga;
          $qtykeluar[$barf->kodegudang][$barf->kodebarang]=$baru->qtykeluar;
          $nilaisaldoakhir[$barf->kodegudang][$barf->kodebarang]=$baru->nilaisaldoakhir;
      }
   $brg[$barf->kodegudang][$barf->kodebarang]=$barf->kodebarang;
   $gud[$barf->kodegudang]=$barf->kodegudang;     
  }

  #ambil akun barang
  $akunbarang=Array();
  $stk="select kode,noakun from ".$dbname.".log_5klbarang where noakun!=''";
  $rek=mysql_query($stk);
  while($bak=mysql_fetch_object($rek))
  {
      $akunbarang[$bak->kode]=$bak->noakun;
  }
  
    #======================== Nomor Jurnal material=============================
    $kodeJurnal1 = 'INVK1';
    ##$queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit',
     ##   "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
    ##$resParam = fetchData($queryParam);

    # Get Journal Counter
    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal1."'");
    $tmpKonter1 = fetchData($queryJ);
    if(count($tmpKonter1)==0)#INVK1 belum diseting di kelompok jurnal
    {
            #rollback jurnal kegiatan    
            $where = "nojurnal='".$nojurnal."'";
            $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
            $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
                "notransaksi='".$dataH[0]['notransaksi']."'");
            $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if(!mysql_query($queryRB)) {
                $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
            }
            if(!mysql_query($queryRB2)) {
                $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
            }
            if(!mysql_query($queryRBKonter)) {
                $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
            }       
        exit("Error: Kelompok jurnal untuk ".$kodeJurnal1." belum ada dan ".$errorDB);
    }
    $konter1 = addZero($tmpKonter1[0]['nokounter']+1,3);

    # Transform No Jurnal dari No Transaksi
    $tmpNoJurnal = explode('/',$param['notransaksi']);
    $nojurnal1 = $tmpNoJurnal[0]."/".$tmpNoJurnal[1]."/".$kodeJurnal1."/".$konter1;//no jurnal material
    #======================== Nomor Jurnal =============================

    #=== Transform Data ===
    $dataResMat['header'] = array();
    $dataResMat['detail'] = array();

    #1. Data Header
    $dataResMat['header'] = array(
        'nojurnal'=>$nojurnal1,
        'kodejurnal'=>$kodeJurnal1,
        'tanggal'=>$dataH[0]['tanggal'],
        'tanggalentry'=>date('Ymd'),
        'posting'=>'1',
        'totaldebet'=>'0',
        'totalkredit'=>'0',
        'amountkoreksi'=>'0',
        'noreferensi'=>$dataH[0]['notransaksi'],
        'autojurnal'=>'1',
        'matauang'=>'IDR',
        'kurs'=>'1',
        'revisi'=>'0'
    );    
# Detail (kredit)
  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
  $resx=mysql_query($str);  
$noUrut = 1;
$totalJumlah = 0;
$errAkunBarang='';
$namabarang='';
$rowCek=  mysql_num_rows($resx);
if($rowCek==0){
   exit();
}
while($bab=mysql_fetch_object($resx)) {
if(($bab->kwantitas<=0)||($bab->kwantitas=='')){
	$pesanError="Kuantitas Barang Tidak Boleh Kosong atau nol\n :";
	$where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
	exit("error: ".$pesanError."\n".$errorDB);
}
    #kredit
    $namabarang=substr($bab->namabarang,0,25)." ".$bab->kwantitas." ".$bab->satuan;
    if($harga[$bab->kodegudang][$bab->kodebarang]=='' or $harga[$bab->kodegudang][$bab->kodebarang]==0)
    {
        $errAkunBarang.=" Error: Belum ada harga rata-rata barang ".$bab->kodebarang;
        break;        
    }
    if(isset($akunbarang[substr($bab->kodebarang,0,3)]) and $akunbarang[substr($bab->kodebarang,0,3)]!=''){
$dataResMat['detail'][] = array(
    'nojurnal'=>$nojurnal1,
    'tanggal'=>$dataH[0]['tanggal'],
    'nourut'=>$noUrut,
    'noakun'=>$akunbarang[substr($bab->kodebarang,0,3)],
    'keterangan'=>'Material BKM '. $dataH[0]['notransaksi']." ".$namabarang,
    'jumlah'=>$harga[$bab->kodegudang][$bab->kodebarang]*$bab->kwantitas*(-1),
    'matauang'=>'IDR',
    'kurs'=>'1',
    'kodeorg'=>'',
    'kodekegiatan'=>'',
    'kodeasset'=>'',
    'kodebarang'=>$bab->kodebarang,
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
    $noUrut++;
    $totalJumlah +=$harga[$bab->kodegudang][$bab->kodebarang]*$bab->kwantitas;
    }
    else
    {
        $errAkunBarang.=" Error: Belum ada akun untuk barang ".$bab->kodebarang;
        break;
    }
}

#debet
if($totalJumlah>0){
		$dataResMat['detail'][] = array(
			'nojurnal'=>$nojurnal1,
			'tanggal'=>$dataH[0]['tanggal'],
			'nourut'=>$noUrut,
			'noakun'=>$resKeg[$kodekegiatan]['akun'],
			'keterangan'=>'Material BKM '.$dataH[0]['notransaksi'],
			'jumlah'=>$totalJumlah,
			'matauang'=>'IDR',
			'kurs'=>'1',
			'kodeorg'=>substr($kodeblok,0,4),
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
			'kodeblok'=>$kodeblok,
		'revisi'=>'0'        
		);
}else{
	$pesanError="Detail tidak berhasil terbentuk \n".$totalJumlah;
	$where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
	exit("error: ".$pesanError."\n".$errorDB);

}
if($namabarang!=''){ // kalo transaksi BKM tanpa material, ga usah eksekusi jurnal barangnya
        
# Total D/K
$dataResMat['header']['totaldebet'] = $totalJumlah;
$dataResMat['header']['totalkredit'] = $totalJumlah;

if($errAkunBarang!='')
{
    echo $errAkunBarang;
    #rollback jurnal kegiatan    
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
    echo "DB Error :\n".$errorDB;    
    exit();
}

#=== Insert Data jurnal material ===
$errorDBX = "";    
# Header
$queryH = insertQuery($dbname,'keu_jurnalht',$dataResMat['header']);
if(!mysql_query($queryH)) {
    $errorDBX .= " Error Header jurnal material:".mysql_error()."\n".$queryH;
}
# Detail
if($errorDBX=='') {
    foreach($dataResMat['detail'] as $key=>$dataDet) {
        $queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
        if(!mysql_query($queryD)) {
            $errorDBX .= "Error Detail jurnal material ".$key." :".mysql_error()."\n";
            #rollback jurnal material
            $where = "nojurnal='".$nojurnal1."'";
            $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
            if(!mysql_query($queryRB)) {
                $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
            }
            #rollback jurnal kegiatan
            echo   $errorDBX;
            $where = "nojurnal='".$nojurnal."'";
            $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
            $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
                "notransaksi='".$dataH[0]['notransaksi']."'");
            $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if(!mysql_query($queryRB)) {
                $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
            }
            if(!mysql_query($queryRB2)) {
                $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
            }
            if(!mysql_query($queryRBKonter)) {
                $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
            }
            echo "DB Error :\n".$errorDB;
            exit;
        }
        else
        {
            $queryKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter1[0]['nokounter']+1),
                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal1."'");
            if(!mysql_query($queryKonter)) {
                $errorDB .= "Update Counter jurnal material Error :".mysql_error()."\n";
            }
        }
    }
}
else
 {
    #rollback jurnal material
    $where = "nojurnal='".$nojurnal1."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
    }    
    #rollback jurnal kegiatan    
    echo   $errorDBX;
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
    echo "DB Error :\n".$errorDB;
    exit;     
 }
    
#prosess material first
#=====================================================================    
  $awlheader=1;
  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
  $resp=mysql_query($str);
  $dataMat['header']=Array();
  $dataMat['detail']=Array();
  while($bar=mysql_fetch_object($resp)){ 
      //if($awlheader==1){
      $num=$nomor[$bar->kodegudang]."-GI-".$bar->kodegudang;
      //exit("error:".$num);
      $dataMat['header'][$bar->kodegudang] = array(
                   'tipetransaksi'=>'5',
                   'notransaksi'=>$num, 
				   'nomirs'=>'',
                   'tanggal'=>$dataH[0]['tanggal'], 
                    'kodept'=>$_SESSION['empl']['kodeorganisasi'], 
                    'untukpt'=>$_SESSION['empl']['kodeorganisasi'], 
                    'nopo'=>'', 
                    'nosj'=>'', 
                    'keterangan'=>'Material BKM ', 
                    'statusjurnal'=>'1', 
                    'kodegudang'=>$bar->kodegudang, 
                    'user'=>$_SESSION['standard']['userid'], 
                    'namapenerima'=>'0', 
                    'mengetahui'=>$_SESSION['standard']['userid'], 
                    'idsupplier'=>'', 
                    'nofaktur'=>'', 
                    'post'=>'1', 
                    'postedby'=>$_SESSION['standard']['userid'], 
                    'untukunit'=>$_SESSION['empl']['lokasitugas'], 
                    'notransaksireferensi'=>$param['notransaksi'], 
                    'gudangx'=>'',
                    'lastupdate'=>date('Y-m-d H:i:s')
          );
      //}
      $dataMat['detail'][]=array(
                'notransaksi'=>$num, 
                'kodebarang'=>$bar->kodebarang, 
                'satuan'=>$bar->satuan, 
                'jumlah'=>$bar->kwantitas, 
                'jumlahlalu'=>$saldo[$bar->kodegudang][$bar->kodebarang], 
                'hargasatuan'=>'0', 
                'kodeblok'=>$kodeblok,
                'waktutransaksi'=>date('Y-m-d H:i:s'),
                'updateby'=>$_SESSION['standard']['userid'], 
                'kodekegiatan'=>$kodekegiatan, 
                'kodemesin'=>'', 
                'statussaldo'=>1, 
                'hargarata'=>$harga[$bar->kodegudang][$bar->kodebarang],
				 'nomirs'=>'',
				 'nopo'=>'',
                                 'nopp'=>''
      );
      $awlheader++;
    }

  #periksa apakah saldo mencukupi:
  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";  
  $resku=mysql_query($str);   
  $errsaldo='';
  while($barf=mysql_fetch_object($resku)){
      if($saldo[$barf->kodegudang][$barf->kodebarang]>=$barf->kwantitas)
      {}
      else {
            $errsaldo=" Error: Tidak cukup saldo untuk barang ".$barf->kodebarang." pada gudang ".$barf->kodegudang." pada periode ".$periode[$barf->kodegudang];
            break;#keluar dari loop
        }
       if($harga[$barf->kodegudang][$barf->kodebarang]>0)
       {}
       else
       {
           $errsaldo=" Error: Tidak cukup saldo untuk barang ".$barf->kodebarang." pada gudang ".$barf->kodegudang." pada periode ".$periode[$barf->kodegudang];
           break;#keluar dari loop
       }  
    $jumlah[$barf->kodegudang][$barf->kodebarang]=$barf->kwantitas;   
  }

  if($errsaldo!='')
  {
    #rollback jurnal material
    $where = "nojurnal='".$nojurnal1."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
    }  
      #rollback jurnal
    echo   $errsaldo;
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
    $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
        "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if(!mysql_query($queryRB)) {
        $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
    }
    if(!mysql_query($queryRB2)) {
        $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
    }
    if(!mysql_query($queryRBKonter)) {
        $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
    }
    echo "DB Error :\n".$errorDB;
    exit;      
}
  else
  {
		$errorY='';
		$errorX='';
		#insert transaksi gudang ht
		foreach($dataMat['header'] as $key=>$dataX) {
        $queryD = insertQuery($dbname,'log_transaksiht',$dataX);
			if(!mysql_query($queryD)) {
				$errorX = " Error insert header material :".$queryD.":".mysql_error()."\n";
			}
        }
        if($errorX=='')
        {
			#insert transaksi gudang dt
            foreach($dataMat['detail'] as $key=>$dataY) {
                $queryD = insertQuery($dbname,'log_transaksidt',$dataY);
                if(!mysql_query($queryD)) {
                    $errorY = " Error insert detail material :".$queryD.":".mysql_error()."\n";
                }
            }
            if($errorY==''){
                #bentuk saldo akhir
                $errSal='';
                foreach($gud as $keygud=>$valgud){
                    foreach($brg[$valgud] as $keybrg=>$valbrg){
                        $sth="update ".$dbname.".log_5saldobulanan set saldoakhirqty=".($saldo[$valgud][$valbrg]-$jumlah[$valgud][$valbrg]).",
                                nilaisaldoakhir=".($nilaisaldoakhir[$valgud][$valbrg]-($jumlah[$valgud][$valbrg]*$harga[$valgud][$valbrg])).",
                                qtykeluar=".($qtykeluar[$valgud][$valbrg]+$jumlah[$valgud][$valbrg]).",
                                qtykeluarxharga=".(($qtykeluar[$valgud][$valbrg]+$jumlah[$valgud][$valbrg])*$harga[$valgud][$valbrg])."
                                where periode='".$periode[$valgud]."' and kodegudang='".$valgud."' and kodebarang='".$valbrg."'"; 
                        if(!mysql_query($sth)){
                            $errSal.=" Error update saldo bulanan".addslashes(mysql_error($conn)).$sth;
                        }
                        $stup="update ".$dbname.".kebun_pakaimaterial set hargasatuan=".$harga[$valgud][$valbrg]." where kodegudang='".$valgud."'
                                   and kodebarang='".$valbrg."' and notransaksi='".$param['notransaksi']."'";
                        mysql_query($stup);
                    }
                }
                  if($errSal=='')
                  {
                      #update log_5masterbarangdt
                  foreach($gud as $keygud=>$valgud){
                          foreach($brg[$valgud] as $keybrg=>$valbrg){ 
                      $strg="update ".$dbname.".log_5masterbarangdt set saldoqty=".($saldo[$valgud][$valbrg]-$jumlah[$valgud][$valbrg]).",
                                hargalastout=".$harga[$valgud][$valbrg]." where kodegudang='".$valgud."' and kodebarang='".$valbrg."'";
                      mysql_query($strg);
                       }  
                      }
                  }
                  else
                  {
                     #rollack saldo gudang 
                  foreach($gud as $keygud=>$valgud){
                          foreach($brg[$valgud] as $keybrg=>$valbrg){ 
                        $sth="update ".$dbname.".log_5saldobulanan set saldoakhirqty=".$saldo[$valgud][$valbrg].",
                                nilaisaldoakhir=".$nilaisaldoakhir[$valgud][$valbrg].",
                                qtykeluar=".$qtykeluar[$gb][$valbrg].",qtykeluarxharga=".$xkeluar[$valgud][$valbrg]."
                                where periode='".$periode[$valgud]."' and kodegudang='".$valgud."' and kodebarang='".$valbrg."'"; 
                        mysql_query($sth);
                        }  
                    }
                        #rollback jurnal material
                        $where = "nojurnal='".$nojurnal1."'";
                        $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                        if(!mysql_query($queryRB)) {
                            $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
                        }  
                        #batalkan transaksi gudang
                        foreach($dataMat['header'] as $key=>$dataX) {
                            $queryD =" delete from ".$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                            mysql_query($queryD);
                            }   
                        #rollback jurnal
                        echo   $errSal;
                        $where = "nojurnal='".$nojurnal."'";
                        $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                        $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
                            "notransaksi='".$dataH[0]['notransaksi']."'");
                        $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                        if(!mysql_query($queryRB)) {
                            $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
                        }
                        if(!mysql_query($queryRB2)) {
                            $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
                        }
                        if(!mysql_query($queryRBKonter)) {
                            $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
                        }
                        echo "DB Error :\n".$errorDB;
                        exit;
                            
                  }   
            } 
            else
            {
                #rollback jurnal material
                $where = "nojurnal='".$nojurnal1."'";
                $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                if(!mysql_query($queryRB)) {
                    $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
                }  
                #hapus transaksi gudang
                    foreach($dataMat['header'] as $key=>$dataX) {
                        $queryD =" delete from ".$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                        mysql_query($queryD);
                        }                
                #rollback jurnal
                echo   $errorY;
                $where = "nojurnal='".$nojurnal."'";
                $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
                    "notransaksi='".$dataH[0]['notransaksi']."'");
                $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
                    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                if(!mysql_query($queryRB)) {
                    $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
                }
                if(!mysql_query($queryRB2)) {
                    $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
                }
                if(!mysql_query($queryRBKonter)) {
                    $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
                }
                echo "DB Error :\n".$errorDB;
                exit;                  
            }   
        }
        else
        {
            #rollback jurnal material
            $where = "nojurnal='".$nojurnal1."'";
            $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
            if(!mysql_query($queryRB)) {
                $errorDB .= "Rollback jurnal material Error :".mysql_error()."\n";
            }  
            #batalkan transaksi gudang
            foreach($dataMat['header'] as $key=>$dataX) {
                $queryD =" delete from ".$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                mysql_query($queryD);
                } 
            #rollback jurnal
                echo   $errorX;
                $where = "nojurnal='".$nojurnal."'";
                $queryRB = "delete from `".$dbname."`.`keu_jurnalht` where ".$where;
                $queryRB2 = updateQuery($dbname,'kebun_aktifitas',array('jurnal'=>0),
                    "notransaksi='".$dataH[0]['notransaksi']."'");
                $queryRBKonter = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$tmpKonter[0]['nokounter']),
                    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                if(!mysql_query($queryRB)) {
                    $errorDB .= "Rollback 1 Error :".mysql_error()."\n".$queryRB;
                }
                if(!mysql_query($queryRB2)) {
                    $errorDB .= "Rollback 2 Error :".mysql_error()."\n".$queryRB2;
                }
                if(!mysql_query($queryRBKonter)) {
                    $errorDB .= "Rollback Counter Error :".mysql_error()."\n".$queryRBKonter;
                }
                echo "DB Error :\n".$errorDB;
                exit;       
        }
  }         
        
        
        
        
    } // else if namabarang
    

} // else 256

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
function jurnalIntraco($dbname, $param, $costRawatDetail, $optAkunIntra, $kodeKeg, $akunKeg, $nameKeg, $dataRes) {
	$dataIntraco = array();
	
	$i=0;
	foreach($costRawatDetail as $kebun=>$cost) {
		if($kebun!=$_SESSION['empl']['lokasitugas'] and isset($optAkunIntra[$kebun])) {
			$dataIntraco[$kebun]['header'] = $dataRes['header'];
			
			#======================== Nomor Jurnal =============================
			$kodeJurnal = 'M0';
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
				'keterangan'=>'Pemeliharaan '.$nameKeg.' '.$_SESSION['empl']['lokasitugas'],
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
				'noreferensi'=>'',
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
				'keterangan'=>'Pemeliharaan '.$nameKeg.' '.$_SESSION['empl']['lokasitugas'],
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
				'noreferensi'=>'',
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
			$errorDB .= "Header Inter / Intra :".mysql_error()."\n".$queryH;
		}

		# Detail
		if($errorDB=='') {
			foreach($dataRes['detail'] as $key=>$dataDet) {
				$queryD = insertQuery($dbname,'keu_jurnaldt',$dataDet);
				if(!mysql_query($queryD)) {
					$errorDB .= "Detail Inter / Intra ".$key." :".mysql_error()."\n";
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
				$queryToJ = updateQuery($dbname,'kebun_aktifitas',array('konfirm'=>1,'jurnal'=>1),
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
		} else {
			// Rollback
			$qRB = deleteQuery($dbname,'keu_jurnalht',"noreferensi='".$param['notransaksi']."'");
			if(!mysql_query($qRB)) {
				$errorDB .= "Inter / Intra Rollback Error :".mysql_error()."\n";
			}
		}
	}
	echo $errorDB;
}
?>
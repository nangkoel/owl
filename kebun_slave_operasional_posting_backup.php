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

# Prestasi
$queryD = selectQuery($dbname,'kebun_prestasi',"*","notransaksi='".
    $param['notransaksi']."'");
$dataD = fetchData($queryD);

# Absensi
$queryAbs = selectQuery($dbname,'kebun_kehadiran','jhk,umr,insentif',"notransaksi='".$param['notransaksi']."'");
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
//$qwe=$totalHk-$dataD[0]['jumlahhk']; buat ngecek pengurangan, bisa koma2 sampe e-16. dz april 27, 2012 10:13 kpw samarinda
$totalHk=round($totalHk,2);                             // diround hingga 2 desimal
$dataD[0]['jumlahhk']=round($dataD[0]['jumlahhk'],2);   // diround hingga 2 desimal
$qwe=$totalHk-$dataD[0]['jumlahhk'];
if(empty($dataAbs) or ($totalHk!=$dataD[0]['jumlahhk'])) {
    echo 'Warning : HK Prestasi belum teralokasi dengan lengkap '.$qwe.'.';
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
    $dataRes['detail'][] = array(
        'nojurnal'=>$nojurnal,
        'tanggal'=>$dataH[0]['tanggal'],
        'nourut'=>$noUrut,
        'noakun'=>$resKeg[$row['kodekegiatan']]['akun'],
        'keterangan'=>'Pemeliharaan '.$resKeg[$row['kodekegiatan']]['nama'],
        'jumlah'=>$costRawat + $row['upahpremi'] + $row['upahkerja'],
        'matauang'=>'IDR',
        'kurs'=>'1',
        'kodeorg'=>substr($row['kodeorg'],0,4),
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
        'kodeblok'=>$row['kodeorg'],
    'revisi'=>'0'        
    );
//  $totalJumlah += ($row['jumlahhk'] * $row['umr']) + $row['upahpremi'] + $row['upahkerja'];
    $totalJumlah +=$costRawat + $row['upahpremi'] + $row['upahkerja'];
    $noUrut++;
    $kodeblok=$row['kodeorg'];
    $kodekegiatan=$row['kodekegiatan'];
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
    'kodeblok'=>'',
    'revisi'=>'0'    
);

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
} else {
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
while($bar1=mysql_fetch_object($resc)){
        $gudang =$bar1->kodegudang;

        $num=1;//default value 
        $str="select max(notransaksi) as notransaksi from ".$dbname.".log_transaksiht where tipetransaksi=5 and kodegudang='".$gudang."'
                  order by notransaksi desc limit 1";
        if($res=mysql_query($str))
        {
            while($bar=mysql_fetch_object($res))
            {
                    $num=$bar->notransaksi;
                    if($num!='')
                    {
                            $num=substr($num,0,11);
                    }	
                    else
                    {
                            $num=date('Ym')."00001";
                    }
            }    
        }
        $nomor[$bar1->kodegudang]=$num;
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
   $brg[]=$barf->kodebarang;
   $gud[]=$barf->kodegudang;     
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
    $nojurnal1 = $tmpNoJurnal[0]."/".$tmpNoJurnal[1]."/".$kodeJurnal1."/".$konter1;
    #======================== Nomor Jurnal =============================

    #=== Transform Data ===
    $dataResMat['header'] = array();
    $dataResMat['detail'] = array();

    #1. Data Header
    $dataResMat['header'] = array(
        'nojurnal'=>$nojurnal1,
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
# Detail (kredit)
  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
  $resx=mysql_query($str);  
$noUrut = 1;
$totalJumlah = 0;
$errAkunBarang='';
$namabarang='';
while($bab=mysql_fetch_object($resx)) {
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

  $str="select a.*,b.namabarang,b.satuan from ".$dbname.".kebun_pakaimaterial a
          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
  $resp=mysql_query($str);
  $dataMat['header']=Array();
  $dataMat['detail']=Array();
  while($bar=mysql_fetch_object($resp))
  { 
      if($nomor[$bar->kodegudang]=='')
          $nomor[$bar->kodegudang]=date('Ym')."00001";
      $nomor[$bar->kodegudang]+=1; 
      $num=$nomor[$bar->kodegudang]."-GI-".$bar->kodegudang;
      $dataMat['header'][] = array(
                   'tipetransaksi'=>'5',
                   'notransaksi'=>$num, 
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
                'hargarata'=>$harga[$bar->kodegudang][$bar->kodebarang]
      );
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
                          foreach($brg as $keybrg=>$valbrg){
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
                          foreach($brg as $keybrg=>$valbrg){ 
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
                          foreach($brg as $keybrg=>$valbrg){ 
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
?>
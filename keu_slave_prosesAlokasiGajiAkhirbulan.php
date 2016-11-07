<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tanggal=$param['periode']."-25";

if($param['row']=='1'){
#periksa dan hapus transaksi untuk data yang sudah di proses pada periode yang sama    
    $str="delete from ".$dbname.".keu_jurnalht where kodejurnal in ('KBNB0','KBNB1','KBNB2','KBNB3','KBNB4','KBNB5',
             'KBNL0','KBNL1','KBNL2','KBNL3','PKS01','PKS02','SIPL1','VHCG0','VHCG1','VHCG2','VHCG3','VHCG4',
             'VHCG5','WSG0','WSG1','WSG2','WSG3','WSG4','WSG5') and tanggal='".$tanggal."' 
    and nojurnal like '%/".$_SESSION['empl']['lokasitugas']."/%'";
   mysql_query($str);
   // exit ("Error".$str);
}

#==========================================konfigurasi database
# KBNB0	Gaji BTL Kebun/Pabrik
# KBNB1	Premi/Lebur BTL Kebun/Pabrik
# KBNB2	Tunjangan Lain
# KBNB3	THR BTL
# KBNB4	Bonus BTL
# KBNB5	Pengobatan BTL
# VHCG0	Gaji Kendaraan/A.Berat
# VHCG1	Biaya Lebur Kendaraan/A.Berat
# VHCG2	Biaya Tunjangan Lain Kend./A.Berat
# VHCG3	THR Kend./A.Berat
# VHCG4	Bonus Kend. A.Berat
# VHCG5	Pengobatan Kend./A.Berat
# WSG0	Biaya Gaji Bengkel
# WSG1	Biaya Premi/Lembur Bengkel
# WSG2	Tunjangan Lain Bengkel
# WSG3	THR Traksi
# WSG4	Bonus Traksi
# WSG5	Pengobatan Traksi
# KBNL0	Biaya pengawasan BBT
# KBNL1	Biaya pengawasan TBM
# KBNL2	Biaya pengawasan TM
# KBNL3	Biaya Pengawasan Panen
#============================================konfigurasi database

#==Komfigurasi komponen gaji
# 1	Gaji Pokok
# 2	Tunjangan Jabatan
# 14	Rapel
# 16	Premi Pengawasan
# 21	Klaim Pengobatan
# 26	Bonus
# 27	Tunjangan Fasilitas
# 28	THR
# 30	Tunjangan Profesi
# 31	Tunjangan Masa Kerja
# 32	Premi
# 33	Lembur
# 34	Penalti
#
#=======================================================
#parameter
#   namakaryawan  
#   karyawanid   
#   komponen     
#   namakomponen  
#   subbagian      
#   mesin       
#   jumlah         
#   tipeorganisasi 
#   periode

#=================================================
#periksa jika tipe unit adalah traksi
$str="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$res=mysql_query($str);

$tip='';
while($bar=mysql_fetch_object($res))
{
    $tip=$bar->tipe;    
}
$param['subbagian']=str_replace(" ","",$param['subbagian']);

if($tip=='PABRIK' and $param['subbagian']!='')
    prosesGajiPabrik(); 
else if($param['tipeorganisasi']=='WORKSHOP')
   prosesGajiWs(); 
else if($param['tipeorganisasi']=='SIPIL')
   prosesGajiSipil();
else if($param['tipeorganisasi']=='AFDELING' or $param['tipeorganisasi']=='BIBITAN')
   prosesGajiAfdeling();
else if($param['tipeorganisasi']=='TRAKSI' && $param['mesin']=='')
   prosesGajiWs();   
else if($param['tipeorganisasi']=='TRAKSI')
   prosesGajiTraksi();
else if($tip=='TRAKSI')//jika tipe yang dilempar kosong
   prosesGajiTraksi();
else if( $tip=='WORKSHOP')//jika tipe yang dilempar kosong
   prosesGajiWs();
else                 #Karyawan kantor (BTL) kebun dan pabrik
   prosesGajiKebun();   

//=====================================
function prosesGajiSipil(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
    #WSG0	Gaji Bengkel	
    #WSG1	Biaya Lebur Bengkel
    #WSG2	Biaya Tunjangan Lain Bengkel
    #WSG3	THR Bengkel	
    #WSG4	Bonus Bengkel
    #WSG5	Pengobatan Bengkel
    
    #output pada jurnal kolom noreferensi ALK_SIPL_GYMH  
      $group='SIPL1';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
		if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
                                        $akundebet='';
                                        $akunkredit='';
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
		}
    }

       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$param['jumlah'],
                    'totalkredit'=>-1*$param['jumlah'],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_SIPL_GYMH',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'
                );
                # Data Detail
                $noUrut = 1;
				
                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akundebet,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan']." By. Perumahan",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_SIPL_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'                    
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Perumahan",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_SIPL_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'                    
                );
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header SIPIL Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail SIPIL Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
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
}
function prosesGajiWs(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
    #WSG0	Gaji Bengkel	
    #WSG1	Biaya Lebur Bengkel
    #WSG2	Biaya Tunjangan Lain Bengkel
    #WSG3	THR Bengkel	
    #WSG4	Bonus Bengkel
    #WSG5	Pengobatan Bengkel
    
    #output pada jurnal kolom noreferensi ALK_WS_GYMH  
    if($param['komponen']==1 or $param['komponen']==14)
      $group='WSG0';
    elseif($param['komponen']==16 or $param['komponen']==32 or $param['komponen']==33)
      $group='WSG1';
    elseif($param['komponen']==28)
      $group='WSG3';  
    elseif($param['komponen']==26)
      $group='WSG4';  
    elseif($param['komponen']==21)
      $group='WSG5';
    else
      $group='WSG2';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
		if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
		}
    }

       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$param['jumlah'],
                    'totalkredit'=>-1*$param['jumlah'],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_WS_GYMH',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
                $noUrut = 1;

                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akundebet,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan']." By.Bengkel",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_WS_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'                    
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Bengkel",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_WS_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'                    
                );
                $noUrut++; 
 /*                
            #periksa apakah sudah pernah diproses dengan karyawan yang sama
            $str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
                  like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
                  and noakun='".$akundebet."' and nik='".$param['karyawanid']."'";
            if(mysql_num_rows(mysql_query($str))>0)
                exit("Error: Data sudah pernah di proses");
 */       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header WS Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail WS Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
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
}
function prosesGajiTraksi(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname; 
    #VHCG0	Gaji Kendaraan/A.Berat		
    #VHCG1	Biaya Lebur Kendaraan/A.Berat	
    #VHCG2	Biaya Tunjangan Lain Kend./A.Berat	
    #VHCG3	THR Kend./A.Berat	
    #VHCG4	Bonus Kend. A.Berat	
    #VHCG5	Pengobatan Kend./A.Berat
    
    #output pada jurnal kolom noreferensi ALK_TRK_GYMH  
    if($param['komponen']==1 or $param['komponen']==14)
      $group='VHCG0';
    elseif($param['komponen']==16 or $param['komponen']==32 or $param['komponen']==33)
      $group='VHCG1';
    elseif($param['komponen']==28)
      $group='VHCG3';  
    elseif($param['komponen']==26)
      $group='VHCG4';  
    elseif($param['komponen']==21)
      $group='VHCG5';
    else
      $group='VHCG2';  //defaultnya tunjangan

    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
		if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
		}
    }

       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>$tanggal,
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$param['jumlah'],
                    'totalkredit'=>-1*$param['jumlah'],
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
       #periksa apakan dia sebagai operator kendaraan
        $str="select * from ".$dbname.".vhc_5operator where karyawanid=".$param['karyawanid'];
        $res=mysql_query($str);
        
        #ambil kendaraan
        $kodekend=''; 
        while($bas=mysql_fetch_object($res))
         {
             $kodekend=$bas->vhc;
         }
        if($kodekend!='')
        {
                # Data Detail
                $noUrut = 1;

                # Debet
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akundebet,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan']." By.Kendaraan",
                    'jumlah'=>$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>$kodekend,
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'                    
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>$tanggal,
                    'nourut'=>$noUrut,
                    'noakun'=>$akunkredit,
                    'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'] ." By.Kendaraan",
                    'jumlah'=>-1*$param['jumlah'],
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>$param['karyawanid'],
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'ALK_TRK_GYMH',
                    'noaruskas'=>'',
                    'kodevhc'=>$kodekend,
                    'nodok'=>'',
                    'kodeblok'=>'',
                     'revisi'=>'0'                   
                );
                $noUrut++; 
       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header Traksi Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Traksi Error : ".mysql_error()."\n";
                            break;
                        }
                    }

                    if($detailErr=='') {
                        # Header and Detail inserted
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                        $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                            "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                            "' and kodekelompok='".$kodeJurnal."'");
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
    }  
    else
    {
      #jika tidak maka jika workshop proses ke workshop, jika tidak maka miaya umum
        if($param['tipeorganisasi']=='WORKSHOP')
            prosesGajiWs();
        else
            prosesGajiKebun();
    }   
}
function prosesGajiAfdeling(){
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
   // KBNL0	Biaya pengawasan BBT
   // KBNL1	Biaya pengawasan TBM
   // KBNL2	Biaya pengawasan TM
   // KBNL3     Pengawasan Panen
  #karyawan afdelin pengawasan
  #output pada jurnal kolom noreferensi ALK_WAS  
  #pastikan jenis pekerjaan untuk karyawan bersangkutan apakah PNN TM TBM atau BBT
    #ambil tanggal periode gaji
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
          where periode='".$param['periode']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'
          and jenisgaji='H'";#bulanan
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: Belum ada periode gaji untuk unit ".$_SESSION['empl']['lokasitugas']);
    
    while($bar=mysql_fetch_object($res))
    {
        $tanggalmulai=$bar->tanggalmulai;
        $tanggalsampai=$bar->tanggalsampai;
    } 
    
    $str="select distinct a.tipetransaksi,b.kodeorg from ".$dbname.".kebun_aktifitas a left join " 
         .$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
         where 
         (nikmandor='".$param['karyawanid']."' or nikmandor1='".$param['karyawanid']."'
         or keranimuat='".$param['karyawanid']."' or nikasisten='".$param['karyawanid']."')
         and a.tanggal >='".$tanggalmulai."' and tanggal <='".$tanggalsampai."' having kodeorg is not null";

    $res=mysql_query($str);
    $numblk=mysql_num_rows($res);
/*
    if($param['karyawanid']=='0000001660')
    {
        echo $param['jumlah']."|".$numblk."|".$str;
        exit("Error");
    }
    else
    {
        exit();
    }    
  */
 
    $porsi=0;
 
    if($numblk>0)
    {
        $porsi=$param['jumlah']/$numblk;
 
        
        $noUrut=0;
        while($bar=mysql_fetch_object($res))
        {
           $dataRes['header']='';
           $dataRes['detail']='';
            //buat header
            if($bar->tipetransaksi=='BBT')
               $group='KBNL0';
            else if($bar->tipetransaksi=='TBM' or $bar->tipetransaksi=='TB')
               $group='KBNL1';
            else if($bar->tipetransaksi=='TM')
               $group='KBNL2';
            else if($bar->tipetransaksi=='PNN')
               $group='KBNL3';    
            else{                      
                    #lempar ke biaya umum
                    prosesGajiKebun(); 
            }
            if($bar->tipetransaksi!='BBT' and strlen($bar->kodeorg)<7)
            {
                //jika lokasinya bukan blok
                //belum dialokasi
            }
            $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
                  where jurnalid='".$group."' limit 1";
            $res1=mysql_query($str);
            if(mysql_num_rows($res1)<1)
                exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$group);
            else
            {
                $akundebet='';
                $akunkredit='';
                $bar1=mysql_fetch_object($res1);
                $akundebet=$bar1->noakundebet;
                $akunkredit=$bar1->noakunkredit;
				if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
				}
            }

               #proses data
                $kodeJurnal = $group;
                #======================== Nomor Jurnal =============================
                # Get Journal Counter
                $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                    "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                $tmpKonter = fetchData($queryJ);
                $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                # Transform No Jurnal dari No Transaksi
                $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
                #======================== /Nomor Jurnal ============================


                # Prep Header
                    $dataRes['header'] = array(
                        'nojurnal'=>$nojurnal,
                        'kodejurnal'=>$kodeJurnal,
                        'tanggal'=>$tanggal,
                        'tanggalentry'=>date('Ymd'),
                        'posting'=>1,
                        'totaldebet'=>$porsi,
                        'totalkredit'=>-1*$porsi,
                        'amountkoreksi'=>'0',
                        'noreferensi'=>'ALK_WAS',
                        'autojurnal'=>'1',
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                    'revisi'=>'0'                        
                    );
            # Data Detail
                    $noUrut++;
                    # Debet
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akundebet,
                        'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'].' (ALK)',
                        'jumlah'=>$porsi,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$param['karyawanid'],
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_WAS',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar->kodeorg,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akunkredit,
                        'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'].' (ALK)',
                        'jumlah'=>-1*$porsi,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$param['karyawanid'],
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_WAS',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar->kodeorg,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;   
 /*
        #periksa apakah sudah pernah diproses dengan karyawan yang sama
        $str2="select * from ".$dbname.".keu_jurnaldt where nojurnal 
              like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
              and noakun='".$akundebet."' and nik='".$param['karyawanid']."'
              and kodeblok='".$bar->kodeorg."' and jumlah=".$porsi;
        if(mysql_num_rows(mysql_query($str2))>0)
            exit("Error: Data sudah pernah di proses untuk karyawan ".$param['namakaryawan'].$str2);
  */      
                        $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                        if(!mysql_query($insHead)) {
                            $headErr .= 'Insert Header AFD Error : '.mysql_error()."\n";
                        }
                    
                    if($headErr=='') {
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                        $detailErr = '';
                        foreach($dataRes['detail'] as $row) {
                            $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                            if(!mysql_query($insDet)) {
                                $detailErr .= "Insert Detail AFD Error : ".mysql_error()."\n";
                                break;
                            }
                        }

                        if($detailErr=='') {
                            # Header and Detail inserted
                            #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                            $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                                "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                                "' and kodekelompok='".$kodeJurnal."'");
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
        }        
    }   
    else
    {
        #lempar ke biaya umum
        prosesGajiKebun();
    }    
    
              
}
function prosesGajiPabrik(){ 
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
  #karyawan kebun
  #output pada jurnal kolom noreferensi ALK_GAJI  
    if($param['komponen']==33)
      $group='PKS02';
    else
      $group='PKS01';
    
    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
		if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
		}
    }
    
       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        
        
        # Prep Header
            $dataRes['header'] = array(
                'nojurnal'=>$nojurnal,
                'kodejurnal'=>$kodeJurnal,
                'tanggal'=>$tanggal,
                'tanggalentry'=>date('Ymd'),
                'posting'=>1,
                'totaldebet'=>$param['jumlah'],
                'totalkredit'=>-1*$param['jumlah'],
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1',
                    'revisi'=>'0'                
            );

            # Data Detail
            $noUrut = 1;

            # Debet
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$akundebet,
                'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'],
                'jumlah'=>$param['jumlah'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$param['karyawanid'],
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['subbagian'],
                    'revisi'=>'0'                
            );
            $noUrut++;

            # Kredit
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$akunkredit,
                'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'],
                'jumlah'=>-1*$param['jumlah'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$param['karyawanid'],
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['subbagian'],
                    'revisi'=>'0'                
            );
            $noUrut++;      
/*
#periksa apakah sudah pernah diproses dengan karyawan yang sama
$str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
      like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
      and noakun='".$akundebet."' and nik='".$param['karyawanid']."'";
if(mysql_num_rows(mysql_query($str))>0)
    exit("Error: Data sudah pernah di proses");
 * 
 */
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
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
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header BTL Error : ".mysql_error()."\n";
                            exit;
                        }
                        exit;
                    } else {
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
}
function prosesGajiKebun(){ 
    global $conn;
    global $tanggal;
    global $param;
    global $dbname;
  #karyawan kebun
  #output pada jurnal kolom noreferensi ALK_GAJI  
    if($param['komponen']==1 or $param['komponen']==14)
      $group='KBNB0';
    elseif($param['komponen']==16 or $param['komponen']==32 or $param['komponen']==33)
      $group='KBNB1';
    elseif($param['komponen']==28)
      $group='KBNB3';  
    elseif($param['komponen']==26)
      $group='KBNB4';  
    elseif($param['komponen']==21)
      $group='KBNB5';
    else
      $group='KBNB2';  //defaultnya tunjangan
    
    $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal
          where jurnalid='".$group."' limit 1";
    $res=mysql_query($str);
    if(mysql_num_rows($res)<1)
        exit("Error: No.Akun pada parameterjurnal belum ada untuk ".$param['namakomponen']);
    else
    {
        $akundebet='';
        $akunkredit='';
        $bar=mysql_fetch_object($res);
        $akundebet=$bar->noakundebet;
        $akunkredit=$bar->noakunkredit;
		if($param['komponen']==60){
					$sGet="select noakunkredit,noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='SDMSM'";
					$qGet=mysql_query($sGet) or die(mysql_error($conn));
					$rGet=mysql_fetch_assoc($qGet);
					$akunkredit=$rGet['noakunkredit'];
					$akundebet=$rGet['noakundebet'];
					/*$akunkredit=$akundebet;
					$akundebet=$rGet['noakunkredit'];*/
		}
    }
    
       #proses data
        $kodeJurnal = $group;
        #======================== Nomor Jurnal =============================
        # Get Journal Counter
        $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
            "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
        $tmpKonter = fetchData($queryJ);
        $konter = addZero($tmpKonter[0]['nokounter']+1,3);

        # Transform No Jurnal dari No Transaksi
        $nojurnal = str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
        #======================== /Nomor Jurnal ============================
        
        
        # Prep Header
            $dataRes['header'] = array(
                'nojurnal'=>$nojurnal,
                'kodejurnal'=>$kodeJurnal,
                'tanggal'=>$tanggal,
                'tanggalentry'=>date('Ymd'),
                'posting'=>1,
                'totaldebet'=>$param['jumlah'],
                'totalkredit'=>-1*$param['jumlah'],
                'amountkoreksi'=>'0',
                'noreferensi'=>'ALK_GAJI',
                'autojurnal'=>'1',
                'matauang'=>'IDR',
                'kurs'=>'1',
                    'revisi'=>'0'                
            );

            # Data Detail
            $noUrut = 1;

            # Debet
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$akundebet,
                'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'],
                'jumlah'=>$param['jumlah'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$param['karyawanid'],
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['subbagian'],
                    'revisi'=>'0'                
            );
            $noUrut++;

            # Kredit
            $dataRes['detail'][] = array(
                'nojurnal'=>$nojurnal,
                'tanggal'=>$tanggal,
                'nourut'=>$noUrut,
                'noakun'=>$akunkredit,
                'keterangan'=> $param['namakomponen'].' '.$param['namakaryawan'],
                'jumlah'=>-1*$param['jumlah'],
                'matauang'=>'IDR',
                'kurs'=>'1',
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'kodekegiatan'=>'',
                'kodeasset'=>'',
                'kodebarang'=>'',
                'nik'=>$param['karyawanid'],
                'kodecustomer'=>'',
                'kodesupplier'=>'',
                'noreferensi'=>'ALK_GAJI',
                'noaruskas'=>'',
                'kodevhc'=>'',
                'nodok'=>'',
                'kodeblok'=>$param['subbagian'],
                    'revisi'=>'0'                
            );
            $noUrut++;      
/*
#periksa apakah sudah pernah diproses dengan karyawan yang sama
$str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
      like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
      and noakun='".$akundebet."' and nik='".$param['karyawanid']."'";
if(mysql_num_rows(mysql_query($str))>0)
    exit("Error: Data sudah pernah di proses");
 * 
 */
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n".$insHead;
            }

            if($headErr=='') {
                #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".mysql_error()."\n".$insDet;
                        break;
                    }
                }

                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$_SESSION['org']['kodeorganisasi'].
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".mysql_error()."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header BTL Error : ".mysql_error()."\n";
                            exit;
                        }
                        exit;
                    } else {
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
}                    
?>
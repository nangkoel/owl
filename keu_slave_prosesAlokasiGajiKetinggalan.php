<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$param = $_POST;
$tanggal=$param['sampai'];
$param['karyawanid']=str_replace("#","'",$param['karyawanid']);

#parameter
#   namakaryawan  
#   karyawanid   
#   subbagian         
#   jumlah         
#   tipeorganisasi 
#   dari
#   sampai


  #karyawan kebun
  #output pada jurnal kolom noreferensi ALK_GAJI_LBR  
   
#periksa di perawatan
$str="select distinct b.kodekegiatan,b.kodeorg,c.noakun from ".$dbname.".kebun_kehadiran_vw a 
      left join ".$dbname.".kebun_perawatan_vw b on a.notransaksi=b.notransaksi 
      left join ".$dbname.".setup_kegiatan c on b.kodekegiatan=c.kodekegiatan    
      where a.tanggal between '".$param['dari']."' and '".$param['sampai']."'
      and a.karyawanid in(".$param['karyawanid'].") and a.unit='".$_SESSION['empl']['lokasitugas']."' 
      having noakun!=''";
$res=mysql_query($str);
#panen
$str2="select distinct kodeorg from ".$dbname.".kebun_prestasi_vw a   
      where tanggal between '".$param['dari']."' and '".$param['sampai']."'
      and karyawanid in(".$param['karyawanid'].") and unit='".$_SESSION['empl']['lokasitugas']."'";
$res2=mysql_query($str2);
$jum=mysql_num_rows($res)+mysql_num_rows($res2);
$param['jumlah']=$param['jumlah']/$jum;


#================HAPUS DULU YANG LAMA 
if($param['row']=='1'){ //dilakukan hanya pada loop baris pertama
        $nr=str_replace("-", "", $tanggal)."/".$_SESSION['empl']['lokasitugas']."/M0/";
        $stw="delete from ".$dbname.".keu_jurnalht where nojurnal like '".$nr."%' and noreferensi='ALK_GAJI_LBR'";
        mysql_query($stw);
}
#===================================
#==========================================
#perawatan
if(mysql_num_rows($res)>0)
{
    #======================== Nomor Jurnal =============================
    $kodeJurnal = 'M0';
    $queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit',
    " jurnalid='".$kodeJurnal."'");
        $resParam = fetchData($queryParam);
      $akunkredit=$resParam[0]['noakunkredit'];  
        
    # Get Journal Counter
    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    $tmpKonter = fetchData($queryJ);
    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

    # Transform No Jurnal dari No Transaksi
    $nojurnal = str_replace("-", "", $tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
    #======================== Nomor Jurnal =============================

 #prep header
        # Prep Header
        $dataResPerawatan['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>$kodeJurnal,
            'tanggal'=>$tanggal,
            'tanggalentry'=>date('Ymd'),
            'posting'=>1,
            'totaldebet'=>$param['jumlah'],
            'totalkredit'=>-1*$param['jumlah'],
            'amountkoreksi'=>'0',
            'noreferensi'=>'ALK_GAJI_LBR',
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1',
            'revisi'=>'0'
        );
#====================================
#execute
        # Data Detail
        $noUrut = 1; 
        while($bar=mysql_fetch_object($res))
        {
            if($param['jumlah']<0)
            {
               $akundebet=$akunkredit;
               $param['jumlah']=$param['jumlah']*-1;
            }
            else
               $akundebet=$bar->noakun;
            
                  # Debet
                    $dataResPerawatan['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akundebet,
                        'keterangan'=> 'Alokasi Gaji(Unalocated) '.$tanggal,
                        'jumlah'=>$param['jumlah'],
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>$bar->kodekegiatan,
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>'',
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_GAJI_LBR',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar->kodeorg,
            'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataResPerawatan['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akunkredit,
                        'keterangan'=> 'Alokasi Gaji(Unalocated) '.$tanggal,
                        'jumlah'=>-1*$param['jumlah'],
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>$bar->kodekegiatan,
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>'',
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_GAJI_LBR',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar->kodeorg,
            'revisi'=>'0'                        
                    );
                    $noUrut++;           
        }
        #hantam=========================
        #periksa apakah sudah pernah diproses dengan karyawan yang sama
//        $str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
//              like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
//              and noreferensi='ALK_GAJI_LBR'";
//        if(mysql_num_rows(mysql_query($str))>0)
//            exit("Error: Data sudah pernah di proses");
//        else
//        {  
                    $insHead = insertQuery($dbname,'keu_jurnalht',$dataResPerawatan['header']);
                    if(!mysql_query($insHead)) {
                        $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
                    }

                    if($headErr=='') {
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                        $detailErr = '';
                        foreach($dataResPerawatan['detail'] as $row) {
                            $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                            if(!mysql_query($insDet)) {
                                $detailErr .= "Insert Detail Perawatan Error : ".mysql_error()."\n";
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
       // }         
}

#+=========================panen
if(mysql_num_rows($res2)>0)
{
   #======================== Nomor Jurnal =============================
    $kodeJurnal = 'PNN01';#panen
    $queryParam = selectQuery($dbname,'keu_5parameterjurnal','noakunkredit,noakundebet',
    " jurnalid='".$kodeJurnal."'");
        $resParam = fetchData($queryParam);
        
      $akunkredit=$resParam[0]['noakunkredit']; 
      $akundebet =$resParam[0]['noakundebet']; 
      $kegpanen=$akundebet."01";
        
    # Get Journal Counter
    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    $tmpKonter = fetchData($queryJ);
    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

    # Transform No Jurnal dari No Transaksi
    $nojurnal = str_replace("-", "", $tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/".$konter;
    #======================== Nomor Jurnal =============================

     #prep header
        # Prep Header
        $dataResPanen['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>$kodeJurnal,
            'tanggal'=>$tanggal,
            'tanggalentry'=>date('Ymd'),
            'posting'=>1,
            'totaldebet'=>$param['jumlah'],
            'totalkredit'=>-1*$param['jumlah'],
            'amountkoreksi'=>'0',
            'noreferensi'=>'ALK_GAJI_LBR',
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1',
            'revisi'=>'0'            
        );
#=================
        # Data Detail
        $noUrut = 1; 
        while($bar2=mysql_fetch_object($res2))
        {
            if($param['jumlah']<0)
            {
                $x=$akundebet;
                $akundebet=$akunkredit;
                $akunkredit=$x;
                $param['jumlah']=$param['jumlah']*-1;
            }
            
                  # Debet
                    $dataResPanen['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akundebet,
                        'keterangan'=> 'Alokasi Gaji(Unalocated) '.$tanggal,
                        'jumlah'=>$param['jumlah'],
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>$kegpanen,
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>'',
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_GAJI_LBR',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar2->kodeorg,
            'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataResPanen['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$akunkredit,
                        'keterangan'=> 'Alokasi Gaji(Unalocated) '.$tanggal,
                        'jumlah'=>-1*$param['jumlah'],
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>$kegpanen,
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>'',
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'ALK_GAJI_LBR',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$bar2->kodeorg,
            'revisi'=>'0'                        
                    );
                    $noUrut++;           
        }
        #hantam=========================
        #periksa apakah sudah pernah diproses dengan karyawan yang sama
//        $str="select * from ".$dbname.".keu_jurnaldt where nojurnal 
//              like '".str_replace("-","",$tanggal)."/".$_SESSION['empl']['lokasitugas']."/".$kodeJurnal."/%'
//              and noreferensi='ALK_GAJI_LBR'";
//        if(mysql_num_rows(mysql_query($str))>0)
//            exit("Error: Data sudah pernah di proses");
//        else
//        {  
                    $insHead = insertQuery($dbname,'keu_jurnalht',$dataResPanen['header']);
                    if(!mysql_query($insHead)) {
                        $headErr .= 'Insert Header BTL Error : '.mysql_error()."\n";
                    }

                    if($headErr=='') {
                        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                        $detailErr = '';
                        foreach($dataResPanen['detail'] as $row) {
                            $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                            if(!mysql_query($insDet)) {
                                $detailErr .= "Insert Detail panen Error : ".mysql_error()."\n";
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
        //}        
}              
?>
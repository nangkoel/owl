<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$bylistrik  =$_POST['bylistrik'];
$byair      =$_POST['byair'];
$byklinik   =$_POST['byklinik'];
$bysosial   =$_POST['bysosial'];
$perumahan   =$_POST['perumahan'];
$natura      =$_POST['natura'];
$jms         =$_POST['jms'];

$karyawanid =$_POST['karyawanid'];
$subbagian  =$_POST['subbagian'];
$periode    =$_POST['periode'];
$kodeorg    =$_SESSION['empl']['lokasitugas'];
$method     =$_POST['method'];

$namakaryawan=$_POST['namakaryawan'];
$bylistrik==''?$bylistrik=0:'';
$byair==''?$byair=0:'';
$byklinik==''?$byklinik=0:'';
$bysosial==''?$bysosial=0:'';
$perumahan==''?$perumahan=0:'';
$natura==''?$natura=0:'';
$jms==''?$jms=0:'';
$tanggal=$periode."-28";

     #periksa apakah sudah tutup buku
     $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' 
           and kodeorg='".$kodeorg."' and tutupbuku=0";
     if(mysql_num_rows(mysql_query($str))>0)
     {        
     }
     else
     {
        exit("Error: Period is closed");
     }   
     
$notransaksi=$periode."-".$karyawanid;
switch ($method)
{
case 'save':
    $str=" delete from ".$dbname.".keu_byunalocated where notransaksi='".$notransaksi."'";
    if(mysql_query($str))
    {
        
        $str="insert into ".$dbname.".keu_byunalocated(periode, karyawanid, listrik, air, klinik, perumahan,natura,jms,sosial, posting, updateby, kodeorg, subbagian,notransaksi)
              values('".$periode."',".$karyawanid.",".$bylistrik.",".$byair.",".$byklinik.",".$perumahan.",".$natura.",".$jms.",".$bysosial.",0,".$_SESSION['standard']['userid'].",'".$kodeorg."','".$subbagian."','".$notransaksi."'
              )";
        if($bylistrik>0 or $byair>0 or $byklinik>0 or $bysosial>0 or $perumahan>0 or $natura>0 or $jms>0){
            if(!mysql_query($str))
            {   
                echo "Gagal:".mysql_error($conn);
            }
        }
         else {
           echo "deleted";
        }
    }
    else
    {
        echo " Error:".mysql_error($conn);
    } 
    break;
    
case 'post':
        
         #ambil datanya:
         $str="select * from ".$dbname.".keu_byunalocated where notransaksi='".$notransaksi."'";
         $res=mysql_query($str);
         $airlistrik=0;
         while($bar=mysql_fetch_object($res))
         {
            $bylistrik  =$bar->listrik;
            $byair      =$bar->air;
            
            $airlistrik =$bylistri+$byair;#digabung dalam akun
            $perumahan  =$bar->perumahan;
            $byklinik   =$bar->klinik;
            $bysosial   =$bar->sosial;
            $natura     =$bar->natura;
            $jms        =$bar->jms;
         }
         
        #ambil periode gaji
        $str="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
              where periode='".$periode."' and jenisgaji='H' and kodeorg='".$kodeorg."'";
        $rex=mysql_query($str);
        if(mysql_num_rows($rex)>0)
        {
           while($bax=mysql_fetch_object($rex))
           {
             $dari=$bax->tanggalmulai;
             $sampai=$bax->tanggalsampai;
           }
        } 
        else
        {
            exit("Error: The payroll period has not been setup in that period");
        }   
        
        #periksa apakah di kebun perawatan
        $str1="SELECT distinct b.kodekegiatan,b.kodeorg,c.noakun FROM ".$dbname.".kebun_kehadiran_vw a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".setup_kegiatan c on b.kodekegiatan=c.kodekegiatan
            where a.karyawanid=".$karyawanid." and tanggal between '".$dari."' and '".$sampai."'
            and unit='".$kodeorg."'";
        $resu=mysql_query($str1);
        while($baru=mysql_fetch_object($resu))
        {
            $blok['debet'][]=$baru->noakun;
            $blok['blok'][]=$baru->kodeorg;
        }
        #periksa panen
        #====================================
        $strp="select noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='PNN01'";
        $resp=mysql_query($strp);
        $akunpanen='';
        while($barp=mysql_fetch_object($resp))
        {
            $akunpanen=$barp->noakundebet;
        }
        #=====================================        
        $str1="SELECT kodeorg FROM ".$dbname.".kebun_prestasi_vw
            where karyawanid=".$karyawanid." and tanggal between '".$dari."' and '".$sampai."'
            and unit='".$kodeorg."'";
        $resu=mysql_query($str1);
        while($baru=mysql_fetch_object($resu))
        {
            if($akunpanen=='')
                exit('Error: PNN01 has not been registred in journal parameter');
            else{
            $blok['debet'][]=$akunpanen;
            $blok['blok'][]=$baru->kodeorg;
            }
        }  
        #periksa pengawasan
         $str1="select distinct a.tipetransaksi,b.kodeorg from ".$dbname.".kebun_aktifitas a left join " 
         .$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
         where 
         (nikmandor='".$karyawanid."' or nikmandor1='".$karyawanid."'
          or keranimuat='".$karyawanid."' or nikasisten='".$karyawanid."')
          and a.tanggal >='".$dari."' and tanggal <='".$sampai."' having kodeorg is not null";
         $resu=mysql_query($str1);
        while($baru=mysql_fetch_object($resu))
        {
            #cari akun debet pengawasan
                    if(str_replace(" ","",$baru->tipetransaksi)=='BBT')
                       $group='KBNL0';
                    else if(str_replace(" ","",$baru->tipetransaksi)=='TBM')
                       $group='KBNL1';
                    else if(str_replace(" ","",$baru->tipetransaksi)=='TM')
                       $group='KBNL2';
                    else if(str_replace(" ","",$baru->tipetransaksi)=='PNN') 
                       $group='KBNL3';
                    
                $strp="select noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='".$group."'";
                $resp=mysql_query($strp);
                $akund='';
                while($barp=mysql_fetch_object($resp))
                {
                    $akund=$barp->noakundebet;
                }
                if($akund==''){
                    exit('Error:  '.$group.' has not been registred in journal parameter');
                }
                
            $blok['debet'][]=$akund;        
            $blok['blok'][]=$baru->kodeorg;
        }          
       
        #hitung rupiah/blok
            $jumlahblok=count($blok['blok']);
         if($jumlahblok>0){   
            $perumahan  =$perumahan/($jumlahblok);
            $byklinik   =$byklinik/($jumlahblok);
            $bysosial   =$bysosial/($jumlahblok);
            $natura     =$natura/($jumlahblok);
            $jms        =$jms/($jumlahblok);
            $airlistrik =$airlistrik/($jumlahblok);
         }
         else
         {
            $blok['debet'][0]='';        
            $blok['blok'][0]='';  
         }   
      #ambil noakun dari parameter jurnal untuk
      #BUN01=perumahan
      #BUN02=pengobatan
      #BUN03=sosial
      #BUN04=Natura
      #BUN05=Air Listrik   
      #BUN06=jamsostek      
            
       $strf="select noakundebet,noakunkredit,jurnalid from ".$dbname.".keu_5parameterjurnal 
             where jurnalid in('BUN01','BUN02','BUN03','BUN04','BUN05','BUN06')";
       $resf=mysql_query($strf);
       while($barf=mysql_fetch_object($resf))
       {
           if($barf->jurnalid=='BUN01')
           {
              $BUN01['debet']=$barf->noakundebet;
              $BUN01['kredit']=$barf->noakunkredit;
           }    
           if($barf->jurnalid=='BUN02')
           {
              $BUN02['debet']=$barf->noakundebet;
              $BUN02['kredit']=$barf->noakunkredit;
           }  
           if($barf->jurnalid=='BUN03')
           {
              $BUN03['debet']=$barf->noakundebet;
              $BUN03['kredit']=$barf->noakunkredit;
           }  
           if($barf->jurnalid=='BUN04')
           {
              $BUN04['debet']=$barf->noakundebet;
              $BUN04['kredit']=$barf->noakunkredit;
           }  
           if($barf->jurnalid=='BUN05')
           {
              $BUN05['debet']=$barf->noakundebet;
              $BUN05['kredit']=$barf->noakunkredit;
           }
           if($barf->jurnalid=='BUN06')
           {
              $BUN06['debet']=$barf->noakundebet;
              $BUN06['kredit']=$barf->noakunkredit;
           }            
       }
       #periksa jika belum lengkap kode jurnal 
       if($BUN01['debet']=='' or $BUN02['debet']=='' or $BUN03['debet']=='' or $BUN04['debet']=='' or $BUN05['debet']=='' or $BUN06['debet']=='')
          exit("Error: setup parameter jurnal belum lengkap BUN01 - BUN06");
       if($BUN01['kredit']=='' or $BUN02['kredit']=='' or $BUN03['kredit']=='' or $BUN04['kredit']=='' or $BUN05['kredit']=='' or $BUN06['kredit']=='')
          exit("Error: setup parameter jurnal belum lengkap BUN01 - BUN06");       
       
     #============================================
      #proses data perumahan
        $kodeJurnal = 'BUN01';
        if($perumahan>1){
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
                    'totaldebet'=>$jumlahblok>0?$perumahan*$jumlahblok:$perumahan,
                    'totalkredit'=>-1*($jumlahblok>0?$perumahan*$jumlahblok:$perumahan),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN01['debet'],
                        'keterangan'=> 'Alokasi Perumahan '. $namakaryawan .' (BUN01-D)',
                        'jumlah'=>$perumahan,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN01['kredit'],
                        'keterangan'=> 'Alokasi Perumahan '. $namakaryawan .' (BUN01-C)',
                        'jumlah'=>-1*$perumahan,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses perumahan============================
        
#============================================
      #proses data pengobatan
        $kodeJurnal = 'BUN02';
        if($byklinik>1){
          unset($dataRes);  
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
                    'totaldebet'=>$jumlahblok>0?$byklinik*$jumlahblok:$byklinik,
                    'totalkredit'=>-1*($jumlahblok>0?$byklinik*$jumlahblok:$byklinik),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN02['debet'],
                        'keterangan'=> 'Alokasi Klinik '. $namakaryawan .' (BUN02-D)',
                        'jumlah'=>$byklinik,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN02['kredit'],
                        'keterangan'=> 'Alokasi Klinik '. $namakaryawan .' (BUN02-C)',
                        'jumlah'=>-1*$byklinik,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses Klinik============================   

#============================================
      #proses data sosial
        $kodeJurnal = 'BUN03';
        if($bysosial>1){
          unset($dataRes);  
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
                    'totaldebet'=>$jumlahblok>0?$bysosial*$jumlahblok:$bysosial,
                    'totalkredit'=>-1*($jumlahblok>0?$bysosial*$jumlahblok:$bysosial),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN03['debet'],
                        'keterangan'=> 'Alokasi BySosial '. $namakaryawan .' (BUN03-D)',
                        'jumlah'=>$bysosial,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN03['kredit'],
                        'keterangan'=> 'Alokasi BySosial '. $namakaryawan .' (BUN03-C)',
                        'jumlah'=>-1*$bysosial,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses Sosial============================         
 
#============================================
      #proses data sosial
        $kodeJurnal = 'BUN04';
        if($natura>1){
          unset($dataRes);  
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
                    'totaldebet'=>$jumlahblok>0?$natura*$jumlahblok:$natura,
                    'totalkredit'=>-1*($jumlahblok>0?$natura*$jumlahblok:$natura),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN04['debet'],
                        'keterangan'=> 'Alokasi Natura '. $namakaryawan .' (BUN04-D)',
                        'jumlah'=>$natura,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN04['kredit'],
                        'keterangan'=> 'Alokasi Natura '. $namakaryawan .' (BUN04-C)',
                        'jumlah'=>-1*$natura,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses Natura============================         
#============================================
      #proses data Air Listrik
        $kodeJurnal = 'BUN05';
        if($airlistrik>1){
          unset($dataRes);  
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
                    'totaldebet'=>$jumlahblok>0?$airlistrik*$jumlahblok:$airlistrik,
                    'totalkredit'=>-1*($jumlahblok>0?$airlistrik*$jumlahblok:$airlistrik),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN05['debet'],
                        'keterangan'=> 'Alokasi AirListrik '. $namakaryawan .' (BUN05-D)',
                        'jumlah'=>$airlistrik,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN05['kredit'],
                        'keterangan'=> 'Alokasi AirListrik '. $namakaryawan .' (BUN05-C)',
                        'jumlah'=>-1*$airlistrik,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses Air Listrik============================      

#============================================
      #proses data JMS
        $kodeJurnal = 'BUN06';
        if($jms>1){
          unset($dataRes);  
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
                    'totaldebet'=>$jumlahblok>0?$jms*$jumlahblok:$jms,
                    'totalkredit'=>-1*($jumlahblok>0?$jms*$jumlahblok:$jms),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'                    
                );
                # Data Detail
               $noUrut = 0;
               foreach($blok['blok'] as $key=>$val){
                    # Debet
                    $noUrut ++;
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$blok['debet'][$key]!=''?$blok['debet'][$key]:$BUN06['debet'],
                        'keterangan'=> 'Alokasi Jamsostek '. $namakaryawan .' (BUN06-D)',
                        'jumlah'=>$jms,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
                    $noUrut++;

                    # Kredit
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>$tanggal,
                        'nourut'=>$noUrut,
                        'noakun'=>$BUN06['kredit'],
                        'keterangan'=> 'Alokasi Jamsostek '. $namakaryawan .' (BUN06-C)',
                        'jumlah'=>-1*$jms,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>$karyawanid,
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>$notransaksi,
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>$val,
                    'revisi'=>'0'                        
                    );
               }
                $noUrut++;       
                $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
                if(!mysql_query($insHead)) {
                    $headErr .= 'Insert Header unalocated Error : '.mysql_error()."\n";
                }

                if($headErr=='') {
                    #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
                    $detailErr = '';
                    foreach($dataRes['detail'] as $row) {
                        $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                        if(!mysql_query($insDet)) {
                            $detailErr .= "Insert Detail Unalocated Error : ".mysql_error()."\n";
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
        }#end proses Air JMS============================   
        #set posting=1 when no error
        if($headErr=='' and $detailErr=='')
        {
            $str="update ".$dbname.".keu_byunalocated set posting=1 where notransaksi='".$notransaksi."'";
            mysql_query($str);
        }
    break;     
}
?>
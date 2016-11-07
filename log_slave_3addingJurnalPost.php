<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

if(isTransactionPeriod())//check if transaction period is normal
{
    $tipetransaksi    =$_POST['tipetransaksi'];
    $tanggal	=$_POST['tanggal'];
    $kodebarang =$_POST['kodebarang'];
    $satuan	=$_POST['satuan'];
    $jumlah	=$_POST['jumlah'];
    $kodept	=$_POST['kodept'];
    $gudangx	=$_POST['gudangx'];
    $untukpt	=$_POST['untukpt'];
    $gudang	=$_POST['gudang'];
    $blok       =$_POST['kodeblok'];
    $notransaksi=$_POST['notransaksi'];
    $user	=$_SESSION['standard']['userid'];
    $hargasatuan=$_POST['hargasatuan'];
    $nopo	=$_POST['nopo'];
    $supplier	=$_POST['supplier'];
    $kodekegiatan	=$_POST['kodekegiatan'];
    $kodemesin	=$_POST['kodemesin'];  
    $nilaitotal=$jumlah*$hargasatuan;
    	
        //==============periksa apakah sudah tutup buku:
        //unit sendiri
        $periode=$_SESSION['gudang'][$gudang]['tahun']."-".$_SESSION['gudang'][$gudang]['bulan'];
        $str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and kodeorg='".substr($gudang, 0,4)."'";
        $res=mysql_query($str);
        $close=0;
        while($bar=mysql_fetch_object($res))
        {
            $close=$bar->tutupbuku;
        }
        if($close=='1')
        {
//            exit (" Error: Keuangan sudah tutup buku");
            exit (" Error: Accounting Period has been closed.");
        }
        //unit tujuan
        if($gudangx!='' and (substr($gudang, 0,4)!=substr($gudangx,0,4)))//jika mutasi dan gudang tujuan ada di unit berbeda
        {
            $str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and kodeorg='".substr($gudangx, 0,4)."'";
            $res=mysql_query($str);
            $close=0;
            while($bar=mysql_fetch_object($res))
            {
                $close=$bar->tutupbuku;
            }
            if($close=='1' and $tipetransaksi!='3' )#khusus penerimaan mutasi dikecualikan boleh di jurnal walau pengirim sudah utup bk
            {
//                exit (" Error: Keuangan unit tujuan sudah tutup buku");
                exit (" Error: Receiver Accounting Period has been closed.");
            }           
        }      
       
        #ambil nama barang
        $str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$kodebarang."'";
        $res=mysql_query($str);
        $namabarang='';
        while($bar=mysql_fetch_object($res))
        {
            $namabarang=$bar->namabarang;
        }
        if($namabarang=='')
            $namabarang=$kodebarang;           
            
        if($tipetransaksi=='1')//penerimaan dari supplier=============================================================
        {
           
            //prepare jurnal
            
       #ambil noakun supplier
            $kodekl=substr($supplier,0,4);
            $str="select noakun from ".$dbname.".log_5klsupplier where kode='".$kodekl."'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->noakun;
            }
        #ambil noakun barang
            $klbarang=substr($kodebarang,0,3);
            $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
            $res=mysql_query($str);
            $akunbarang='';
            while($bar=mysql_fetch_object($res))
            {
                $akunbarang=$bar->noakun;
            }   
            if(($akunbarang=='' or $akunspl=='') and ($klbarang<'400' or substr($kodebarang,0,1)=='9'))
            {    
//                exit("Error: Noakun  Noakun barang atau supplier  belum ada untuk transaksi ".$notransaksi); 
                exit("Error: Account no. for material or supplier not available yet for ".$notransaksi); 
            }
                 #proses data
            $kodeJurnal = 'INVM1';
            #======================== Nomor Jurnal =============================
            # Get Journal Counter
//            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."' ");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

            # Transform No Jurnal dari No Transaksi
            $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
            #======================== /Nomor Jurnal ============================

            # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>tanggalsystem($tanggal),
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$nilaitotal,
                    'totalkredit'=>-1*$nilaitotal,
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
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
                    'tanggal'=>tanggalsystem($tanggal),
                    'nourut'=>$noUrut,
                    'noakun'=>$akunbarang,
                    'keterangan'=>'Pembelian barang '.$namabarang.' '.$jumlah." ".$satuan,
                    'jumlah'=>$nilaitotal,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>substr($gudang,0,4),
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>$kodebarang,
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>$supplier,
                    'noreferensi'=>$notransaksi,
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>$nopo,
                    'kodeblok'=>'',
                    'revisi'=>'0'                
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>tanggalsystem($tanggal),
                    'nourut'=>$noUrut,
                    'noakun'=>$akunspl,
                    'keterangan'=>'Pembelian barang '.$namabarang.' '.$jumlah." ".$satuan,
                    'jumlah'=>-1*$nilaitotal,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>substr($gudang,0,4),
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>$kodebarang,
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>$supplier,
                    'noreferensi'=>$notransaksi,
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>$nopo,
                    'kodeblok'=>'',
                    'revisi'=>'0'                
                );
                $noUrut++;      
#=========================================                
//   $updflagststussaldo="update ".$dbname.". log_transaksidt set statussaldo=1,hargarata=".$newhargarata.",jumlahlalu=".$cursaldo."
//   where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";        
 #==================================execute
    if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){   
             $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
             if(!mysql_query($insHead)) {
                 $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
             }
             if($headErr=='') {
                 $detailErr = '';
                 foreach($dataRes['detail'] as $row) {
                     $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                     if(!mysql_query($insDet)) {
                         $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                         break;
                     }
                 }
                 if($detailErr=='') {
                     # Header and Detail inserted
                     #>>> Update Kode Jurnal
                     $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                         "kodeorg='".$kodept.
                         "' and kodekelompok='".$kodeJurnal."'");
                     if(!mysql_query($updJurnal)) {
                         echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                         # Rollback if Update Failed
                         $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                         if(!mysql_query($RBDet)) {
                             echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                             exit;
                         }
                         exit;
                     }
                 } else {
                     echo $detailErr;
                     # Rollback, Delete Header
                     $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                     if(!mysql_query($RBDet)) {
                         echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                         exit;
                     }
                 }
             } else {
                 echo $headErr;
                 exit;  
             }
          #============================================================================          
      }else{
          
      }
    
      } // end of tipetransaksi = 1
        if($tipetransaksi=='6')//Pengembalian ke supplier=============================================================
        {
            //prepare jurnal
            
       #ambil noakun supplier
            $kodekl=substr($supplier,0,4);
            $str="select noakun from ".$dbname.".log_5klsupplier where kode='".$kodekl."'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->noakun;
            }
        #ambil noakun barang
            $klbarang=substr($kodebarang,0,3);
            $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
            $res=mysql_query($str);
            $akunbarang='';
            while($bar=mysql_fetch_object($res))
            {
                $akunbarang=$bar->noakun;
            }   
        if(($akunbarang=='' or $akunspl=='') and ($klbarang<'400' or substr($kodebarang,0,1)=='9'))
            {    
//                exit("Error: Noakun  Noakun barang atau supplier  belum ada untuk transaksi ".$notransaksi); 
                exit("Error: Account number for material or supplier not available yet on ".$notransaksi); 
            }
                 #proses data
            $kodeJurnal = 'INVK1';
            #======================== Nomor Jurnal =============================
            # Get Journal Counter
//            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
            $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                "kodeorg='".$kodept."' and kodekelompok='".$kodeJurnal."' ");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter']+1,3);

            # Transform No Jurnal dari No Transaksi
            $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
            #======================== /Nomor Jurnal ============================

            # Prep Header
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodeJurnal,
                    'tanggal'=>tanggalsystem($tanggal),
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>1,
                    'totaldebet'=>$nilaitotal,
                    'totalkredit'=>-1*$nilaitotal,
                    'amountkoreksi'=>'0',
                    'noreferensi'=>$notransaksi,
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
                    'tanggal'=>tanggalsystem($tanggal),
                    'nourut'=>$noUrut,
                    'noakun'=>$akunspl,
                    'keterangan'=>'ReturSupplier '.$namabarang.' '.$jumlah." ".$satuan,
                    'jumlah'=>$nilaitotal,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>substr($gudang,0,4),
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>$kodebarang,
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>$supplier,
                    'noreferensi'=>$notransaksi,
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>$nopo,
                    'kodeblok'=>'',
                    'revisi'=>'0'                
                );
                $noUrut++;

                # Kredit
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>tanggalsystem($tanggal),
                    'nourut'=>$noUrut,
                    'noakun'=>$akunbarang,
                    'keterangan'=>'ReturSupplier '.$namabarang.' '.$jumlah." ".$satuan,
                    'jumlah'=>-1*$nilaitotal,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>substr($gudang,0,4),
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>$kodebarang,
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>$supplier,
                    'noreferensi'=>$notransaksi,
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>$nopo,
                    'kodeblok'=>'',
                    'revisi'=>'0'                
                );
                $noUrut++;      
#=========================================                
//   $updflagststussaldo="update ".$dbname.". log_transaksidt set statussaldo=1,hargarata=".$newhargarata.",jumlahlalu=".$cursaldo."
//   where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";        
 #==================================execute
   if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){        
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
//                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi'].
//                        "' and kodekelompok='".$kodeJurnal."'");
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$kodept.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }
     else{
         
       
        }
      } // end of tipetransaksi=6     
      
else if($tipetransaksi=='2')//pengembalian ke gudang disini tidak mempengaruhi harga rata-rata di gudang, karena harga satuan yang digunakan adalah harga rata2
    {
    
     #=======================================================
     #periksa apakah dari satu PT
     $pengguna=substr($_POST['untukunit'],0,4);
    
     $ptpengguna='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptpengguna=$bar->induk;
     }
      $str="select akunhutang,jenis from ".$dbname.".keu_5caco where 
           kodeorg='".$pengguna."'";
     $res=mysql_query($str);
     $intraco='';
     $interco='';
     while($bar=mysql_fetch_object($res)){
         if($bar->jenis=='intra')
            $intraco=$bar->akunhutang;
         else
            $interco=$bar->akunhutang; 
     }
     
     
     $ptGudang='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudang,0,4)."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptGudang=$bar->induk;
     }
     #jika pt tidak sama maka pakai akun interco
     $akunspl='';
     if($ptGudang !=$ptpengguna)
     {
         #ambil akun interco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='inter'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            }
         $inter=$interco;   
        if($akunspl=='')
//           exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna); 
           exit("Error: Account for intraco or interco not available yet for ".$pengguna); 
     }
     else if($pengguna!=substr($gudang,0,4)){ #jika satu pt beda kebun
          #ambil akun intraco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='intra'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            } 
          $inter=$intraco;  
         if($akunspl=='')
//            exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna);    
            exit("Error: Account for intraco or interco not available yet for ".$pengguna);    
     }
     
     
    #ambil akun pekerjaan atau kendaraan atau ab
     #periksa ke table setup blok
     $statustm='';
     $str="select statusblok from ".$dbname.".setup_blok where kodeorg='".$blok."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res)){
         $statustm=$bar->statusblok;
     }
         $str="select noakun from ".$dbname.".setup_kegiatan where 
                kodekegiatan='".$kodekegiatan."'";
     $akunpekerjaan='';
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res)){
         $akunpekerjaan=$bar->noakun;
     }
     #jika akun kegiatan tidak ada maka exit
     if($akunpekerjaan=='')
//         exit("Error: Akun pekerjaan belum ada untuk kegiatan ".$kodekegiatan);
         exit("Error: Account not available yet for activity ".$kodekegiatan);
     
    #ambil noakun barang
    $klbarang=substr($kodebarang,0,3);
    $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
    $res=mysql_query($str);
    $akunbarang='';
    while($bar=mysql_fetch_object($res))
    {
        $akunbarang=$bar->noakun;
    }   
    if($akunbarang=='')
//        exit("Error: Noakun barang belum ada untuk transaksi".$notransaksi);
        exit("Error: Material account not available yet on ".$notransaksi);
    else{
          
        $updflagststussaldo="update ".$dbname.". log_transaksidt set statussaldo=1,jumlahlalu=".$saldoakhirqty.", hargarata=".$newhargarata."
                                        where notransaksi='".$notransaksi."' and kodebarang='".$kodebarang."' and kodeblok='".$blok."'";        
        
        //penggunaan internal
        if($pengguna==substr($gudang,0,4)){
                    $kodeJurnal = 'INVM1';
                    #======================== Nomor Jurnal =============================
                    # Get Journal Counter
//                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                    # Prep Header
                        $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'revisi'=>'0'                            
                        );

                        # Data Detail
                        $noUrut = 1;
                         $keterangan="ReturGudang barang ".$namabarang." ".$jumlah." ".$satuan;
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=> $keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                            'revisi'=>'0'                            
                        );
                        $noUrut++;

                        # Kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunpekerjaan,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>$kodekegiatan,
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                             'revisi'=>'0'                            
                        );
                        $noUrut++; 
#========================================= 
                                      
   if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){        
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptpengguna.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                    else{#berhasil di jurnal
                      #proses gudang
                        
                    }
               
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }
     else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
            #proses gudang
                
         }                
        }        
         else{
             #jika inter atau intraco 
                  #proses data sisi pemilik====================================================
                    $kodeJurnal = 'INVM1';
                    #======================== Nomor Jurnal =============================
                    # Get Journal Counter
//                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                      $header1pemilik=$nojurnal;   //no header pemilik    
                    # Prep Header
                        $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                            
                        );

                        # Data Detail
                        $noUrut = 1;
                         $keterangan="ReturGudang barang ".$namabarang." ".$jumlah." ".$satuan;
                         $keterangan=substr($keterangan,0,150);
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=>$keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>'',
                            'revisi'=>'0'                            
                        );
                        $noUrut++;

                        # Kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$inter,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>'',
                'revisi'=>'0'                            
                        );
   if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){       
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptGudang.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }              
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }  
#proses data sisi pengguna====================================================
                    $kodeJurnal = 'INVM1';
                    #======================== Nomor Jurnal =============================
                    #ambil tanggal terkecil periode pengguna
                    #query sbnrnya sblm di ganti gara2 gawat darurat
                    /*$stri="select tanggalmulai from ".$dbname.".setup_periodeakuntansi
                           where kodeorg='".$pengguna."' and tutupbuku=0";*/
                    $cktipe="select tipe from ".$dbname.".organisasi where kodeorganisasi='".substr($pengguna,0,4)."'";
                    $qcktipe=  mysql_query($cktipe) or die(mysql_error($conn));
                    $rcktipe=  mysql_fetch_assoc($qcktipe);
                    if($rcktipe['tipe']!='HOLDING'){
                        $pengguna=substr($pengguna,0,4)."WH";
                    }else{
                        $pengguna=substr($pengguna,0,4)."01";
                    }
                    $stri="select tanggalmulai from ".$dbname.".setup_periodeakuntansi
                           where kodeorg='".$pengguna."' and tutupbuku=0";
                    //exit("errro".$stri);
                    $tanggalsana='';
                    $resi=mysql_query($stri);
                    while($bari=mysql_fetch_object($resi))
                    {
                        $tanggalsana=$bari->tanggalmulai;
                    }
                    if($tanggalsana=='' or substr($tanggalsana,0,7)==(substr(tanggalsystem($tanggal),0,4)."-".substr(tanggalsystem($tanggal),4,2))){#jika periode sama maka biarkan
                        $tanggalsana=tanggalsystem($tanggal);
                        $pengguna=substr($pengguna,0,4);
                    }else{//rollback header sisi pemilik
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
//                                exit(" Error: periode pembukuan pengguna material tidak sama dengan gudang");
                                exit(" Error: Receivers accounting period not the same as warehouse.");
                            }else{
                                exit(" Error: Receivers accounting period not the same as warehouse.");
                            }  
                    }
                    # Get Journal Counter
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",$tanggalsana)."/".$pengguna."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                    # Prep Header
                    unset($dataRes['header']);//ganti header    
                    $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>$tanggalsana,
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                        
                        );

                        # Data Detail
                         $keterangan="ReturGudang barang ".$namabarang." ".$jumlah." ".$satuan." ".substr($_POST['tanggal'],0,7);
                         $keterangan=substr($keterangan,0,150);
                        $noUrut = 1;
                        unset($dataRes['detail']);//ganti detail 
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>$tanggalsana,
                            'nourut'=>$noUrut,
                            'noakun'=>$akunspl,
                            'keterangan'=>$keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                'revisi'=>'0'                            
                        );
                        $noUrut++;

                        # Kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>$tanggalsana,
                            'nourut'=>$noUrut,
                            'noakun'=>$akunpekerjaan,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>$kodekegiatan,
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                'revisi'=>'0'                       
                        );
                        $noUrut++; 
                    #===========EXECUTE
   if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){       
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptpengguna.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                    else{#berhasil di jurnal
                      #proses gudang
                    }
               
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        //hapus juga sisi pemilik
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header pemilik Error : ".addslashes(mysql_error($conn))."\n";
                                exit;
                            }
                    }
                }
            } else {
                echo $headErr;
                $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
            }
         #============================================================================          
     }
     else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
            #proses gudang      
         }  
        }//disini=====
    }        
    } // end of tipetransaksi = 2
else if($tipetransaksi=='3')//penerimaan mutasi
{
  
     #=======================================================
     #periksa apakah dari satu PT
     $pengguna=substr($gudang,0,4);//ini sebenarnya pemilik
    
     $ptpengguna='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$pengguna."'";//ini sebenarnya pemilik
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptpengguna=$bar->induk;
     }
     
     $ptGudang='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudangx,0,4)."'";//ini yang pengguna
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptGudang=$bar->induk;
     }
     #jika pt tidak sama maka pakai akun interco
     $akunspl='';
     if($ptGudang !=$ptpengguna)
     {
         #ambil akun interco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudangx,0,4)."' and jenis='inter'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            }  
        if($akunspl=='')
//           exit(" Error: Akun intraco  atau interco belum ada untuk unit ".substr($gudangx,0,4)); 
           exit(" Error: Account intraco or interco not available for ".substr($gudangx,0,4)); 
     }
     else if($pengguna!=substr($gudangx,0,4)){ #jika satu pt beda kebun
          #ambil akun intraco
         $str="select akunpiutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudangx,0,4)."' and jenis='intra'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunpiutang;
            } 
         if($akunspl=='')
//            exit(" Error: Akun intraco  atau interco belum ada untuk unit ".substr($gudangx,0,4));    
            exit(" Error: Account intraco / interco not available for ".substr($gudangx,0,4));    
     }
    #ambil noakun barang
    $klbarang=substr($kodebarang,0,3);
    $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
    $res=mysql_query($str);
    $akunbarang='';
    while($bar=mysql_fetch_object($res))
    {
        $akunbarang=$bar->noakun;
    }   
    if($akunbarang=='')
//        exit(" Error: Noakun barang belum ada untuk transaksi".$notransaksi);
        exit(" Error: Account for material not available for ".$notransaksi);
    else{
                         
       
#proses data sisi pengguna====================================================
                    $kodeJurnal = 'INVM1';
                    #======================== Nomor Jurnal =============================
                    # Get Journal Counter
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = tanggalsystem($tanggal)."/".$pengguna."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                    # Prep Header
                    unset($dataRes['header']);//ganti header    
                    $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>$nilaitotal,
                            'totalkredit'=>(-1*$nilaitotal),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                        
                        );

                        # Data Detail
                         $keterangan="Terima Mutasi barang ".$namabarang." ".$jumlah." ".$satuan." ".substr(tanggaldgnbar($_POST['tanggal']),0,7);
                         $keterangan=substr($keterangan,0,150);
                        $noUrut = 1;
                        unset($dataRes['detail']);//ganti detail 
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=>$keterangan,
                            'jumlah'=>$nilaitotal,
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
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
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunspl,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$nilaitotal),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                'revisi'=>'0'                       
                        );
                        $noUrut++; 
                    #===========EXECUTE
                        
if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){  #hanya barang stok yang dijurnal      dan mutasi keluar kebun
    //exit("error:masuk");
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n".$insHead;
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn)).$insDet."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptpengguna.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                    else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
                            #proses gudang        
                        }
               
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        //hapus juga sisi pemilik
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header pemilik Error : ".addslashes(mysql_error($conn))."\n";
                                exit;
                            }
                    }
                }
            } else {
                echo $headErr;
                $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
            }
         #============================================================================          
     }
     else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
            #proses gudang
         exit("error:masuk"."___".$akunbarang."___".substr($pengguna,0,4)."__".substr($gudangx,0,4));
         }  
    }
} // end of tipetransaksi = 3
else if($tipetransaksi=='7')//pengeluaran mutasi gudang
    {
     
     #=======================================================
     #periksa apakah dari satu PT
     $pengguna=substr($gudangx,0,4);//gudang tujuan
    
     $ptpengguna='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptpengguna=$bar->induk;
     }
      $str="select akunpiutang,jenis from ".$dbname.".keu_5caco where 
           kodeorg='".$pengguna."'";
     $res=mysql_query($str);
     $intraco='';
     $interco='';
     while($bar=mysql_fetch_object($res)){
         if($bar->jenis=='intra')
            $intraco=$bar->akunpiutang;
         else
            $interco=$bar->akunpiutang; 
     }
     
     
     $ptGudang='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudang,0,4)."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptGudang=$bar->induk;
     }
     #jika pt tidak sama maka pakai akun interco
     $akunspl='';
     if($ptGudang !=$ptpengguna)
     {
         #ambil akun interco
         $str="select akunhutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='inter'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunhutang;
            }
         $inter=$interco;   
        if($akunspl=='')
//           exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna); 
           exit("Error: Account intraco or interco not available for ".$pengguna); 
     }
     else if($pengguna!=substr($gudang,0,4)){ #jika satu pt beda kebun
          #ambil akun intraco
         $str="select akunhutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='intra'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunhutang;
            } 
          $inter=$intraco;  
         if($akunspl=='')
//            exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna);    
            exit("Error: Account intraco or interco not available for ".$pengguna);    
     }
        
    #ambil noakun barang
    $klbarang=substr($kodebarang,0,3);
    $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
    $res=mysql_query($str);
    $akunbarang='';
    while($bar=mysql_fetch_object($res))
    {
        $akunbarang=$bar->noakun;
    }   
    if($akunbarang=='')
//        exit("Error: Noakun barang belum ada untuk transaksi".$notransaksi);
        exit("Error: Account for material not available for  ".$notransaksi);
    else{
    
             #jika inter atau intraco 
                  #proses data sisi pemilik====================================================
                    $kodeJurnal = 'INVK1';
                    #======================== Nomor Jurnal =============================
                    # Get Journal Counter
//                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                      $header1pemilik=$nojurnal;   //no header pemilik    
                    # Prep Header
                        $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                            
                        );

                        # Data Detail
                        $noUrut = 1;
                         $keterangan="Mutasi barang ".$namabarang." ".$jumlah." ".$satuan;
                         $keterangan=substr($keterangan,0,150);
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$inter,
                            'keterangan'=>$keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
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
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                'revisi'=>'0'                            
                        );
                          
 if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){    
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptGudang.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                   else{#berhasil di jurnal
                      #proses gudang
                    }  
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }else{#barang aktiva hanya update saldo saja
                               #update masterbarangdt
                      
     }  
     //}//disini=====
    }        
    } // end of tipetransaksi = 7   
else if($tipetransaksi=='5')//pengeluaran
    {
    
     #=======================================================
     #periksa apakah dari satu PT
     $pengguna=substr($_POST['untukunit'],0,4);
    
     $ptpengguna='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".$pengguna."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptpengguna=$bar->induk;
     }
      $str="select akunpiutang,jenis from ".$dbname.".keu_5caco where 
           kodeorg='".$pengguna."'";
     $res=mysql_query($str);
     $intraco='';
     $interco='';
     while($bar=mysql_fetch_object($res)){
         if($bar->jenis=='intra')
            $intraco=$bar->akunpiutang;
         else
            $interco=$bar->akunpiutang; 
     }
     
     
     $ptGudang='';
     $str="select induk from ".$dbname.".organisasi where kodeorganisasi='".substr($gudang,0,4)."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $ptGudang=$bar->induk;
     }
     #jika pt tidak sama maka pakai akun interco
     $akunspl='';
     if($ptGudang !=$ptpengguna)
     {
         #ambil akun interco
         $str="select akunhutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='inter'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunhutang;
            }
         $inter=$interco;   
        if($akunspl=='')
//           exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna); 
           exit("Error: Account intraco or interco not available for ".$pengguna); 
     }
     else if($pengguna!=substr($gudang,0,4)){ #jika satu pt beda kebun
          #ambil akun intraco
         $str="select akunhutang from ".$dbname.".keu_5caco where kodeorg='".substr($gudang,0,4)."' and jenis='intra'";
            $res=mysql_query($str);
            $akunspl='';
            while($bar=mysql_fetch_object($res))
            {
                $akunspl=$bar->akunhutang;
            } 
          $inter=$intraco;  
         if($akunspl=='')
//            exit("Error: Akun intraco  atau interco belum ada untuk unit ".$pengguna);    
            exit("Error: Account intraco or interco not available for ".$pengguna);    
     }
     
     
    #ambil akun pekerjaan atau kendaraan atau ab
     #periksa ke table setup blok
     $statustm='';
     $str="select statusblok from ".$dbname.".setup_blok where kodeorg='".$blok."'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res)){
         $statustm=$bar->statusblok;
     }
         $str="select noakun from ".$dbname.".setup_kegiatan where 
                kodekegiatan='".$kodekegiatan."'";
     $akunpekerjaan='';
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res)){
         $akunpekerjaan=$bar->noakun;
     }
     #untuk project aktiva dalam konstruksi maka akun diambil dari kolom kodekegiatan
     $kodeasset='';
     if(substr($blok,0,2)=='AK' or substr($blok,0,2)=='PB'){
            $akunpekerjaan=substr($kodekegiatan,0,7);
            $kodeasset=$blok;
//            $sblk="select distinct kodeorg from ".$dbname.".project where kode='".$blok."'";
//            $qblk=mysql_query($sblk) or die(mysql_error($conn));
//            $rblk=mysql_fetch_assoc($qblk);#menambahkan untuk mengisi kodeblok
            $blok="";#pemindahan kodeblok ke kode asset
     }
     #jika akun kegiatan tidak ada maka exit
     if($akunpekerjaan=='')
//         exit("Error: Akun pekerjaan belum ada untuk kegiatan ".$kodekegiatan);
         exit("Error: Account not available for activity ".$kodekegiatan);
     
    #ambil noakun barang
    $klbarang=substr($kodebarang,0,3);
    $str="select noakun from ".$dbname.".log_5klbarang where kode='".$klbarang."'";
	//exit("Error:$str");
    $res=mysql_query($str);
    $akunbarang='';
    while($bar=mysql_fetch_object($res))
    {
        $akunbarang=$bar->noakun;
    }   
	
	//if(($akunbarang=='' or $akunspl=='') and ($klbarang<'400' or substr($kodebarang,0,1)=='9')) [origin]
    if(($akunbarang=='' or $akunspl=='') and ($klbarang>='400' or substr($kodebarang,0,1)=='9'))
//        exit("Error: Noakun barang belum ada untuk transaksi".$notransaksi);
        exit("Error: Account for material not available for ".$notransaksi);//ini tes indra
    else{
          
    
        
        //penggunaan internal$ptGudang$ptpengguna
        if($pengguna==substr($gudang,0,4)){
                    $kodeJurnal = 'INVK1';
                    #======================== Nomor Jurnal =============================
                    # Get Journal Counter
//                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                    # Prep Header
                        $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'revisi'=>'0'                            
                        );

                        # Data Detail
                        $noUrut = 1;
                         $keterangan="Pemakaian barang ".$namabarang." ".$jumlah." ".$satuan;
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunpekerjaan,
                            'keterangan'=> $keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>$kodekegiatan,
                            'kodeasset'=>$kodeasset,
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                            'revisi'=>'0'                            
                        );
                        $noUrut++;

                        # Kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                             'revisi'=>'0'                            
                        );
                        $noUrut++; 
#=========================================                                
 if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){    
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptpengguna.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                    else{#berhasil di jurnal
                      #proses gudang
                        
                    }
               
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }
     else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
            #proses gudang   
         }                
        }        
         else{                 
             #jika inter atau intraco 
                  #proses data sisi pemilik====================================================
                    $kodeJurnal = 'INVK1';
                    #======================== Nomor Jurnal =============================
//                    # Get Journal Counter
//                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
                    # Get Journal Counter
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptGudang."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",tanggalsystem($tanggal))."/".substr($gudang,0,4)."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                      $header1pemilik=$nojurnal;   //no header pemilik    
                    # Prep Header
                        $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                            
                        );

                        # Data Detail
                        $noUrut = 1;
                         $keterangan="Pemakaian barang ".$namabarang." ".$jumlah." ".$satuan;
                         $keterangan=substr($keterangan,0,150);
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$inter,
                            'keterangan'=>$keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
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
                            'tanggal'=>tanggalsystem($tanggal),
                            'nourut'=>$noUrut,
                            'noakun'=>$akunbarang,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>substr($gudang,0,4),
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                'revisi'=>'0'                            
                        );                      
 if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){       
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptGudang.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }              
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
                }
            } else {
                echo $headErr;
                exit;  
            }
         #============================================================================          
     }  
        //                    echo "warning: header1pemilik_".$header1pemilik."\n".$updJurnal;
        //                    exit;
        //exit('trap!');     
        #proses data sisi pengguna====================================================
                    $kodeJurnal = 'INVK1';
                    #======================== Nomor Jurnal =============================
                    #ambil tanggal terkecil periode pengguna
//                    $stri="select tanggalmulai from ".$dbname.".setup_periodeakuntansi
//                           where kodeorg='".$pengguna."' and tutupbuku=0";
                     #query sbnrnya sblm di ganti gara2 gawat darurat
//                    $stri="select tanggalmulai from ".$dbname.".setup_periodeakuntansi
//                           where kodeorg='".$pengguna."' and tutupbuku=0";
                    $cktipe="select tipe from ".$dbname.".organisasi where kodeorganisasi='".substr($pengguna,0,4)."'";
                    $qcktipe=  mysql_query($cktipe) or die(mysql_error($conn));
                    $rcktipe=  mysql_fetch_assoc($qcktipe);
                    if($rcktipe['tipe']!='HOLDING'){
                        $pengguna=substr($pengguna,0,4)."WH";
                    }else{
                        $pengguna=substr($pengguna,0,4)."01";
                    }
                    $stri="select tanggalmulai from ".$dbname.".setup_periodeakuntansi
                           where kodeorg='".$pengguna."' and tutupbuku=0";
                    $tanggalsana='';
                    $resi=mysql_query($stri);
                    while($bari=mysql_fetch_object($resi))
                    {
                        $tanggalsana=$bari->tanggalmulai;
                    }
                    if($tanggalsana=='' or substr($tanggalsana,0,7)==(substr(tanggalsystem($tanggal),0,4)."-".substr(tanggalsystem($tanggal),4,2))){#jika periode sama maka biarkan
                        $tanggalsana=tanggalsystem($tanggal);
                        $pengguna=substr($pengguna,0,4);
                    }else{//rollback header sisi pemilik
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
//                                exit(" Error: periode pembukuan pengguna material tidak sama dengan gudang");
                                exit(" Error: Receivers accounting period not the same as warehouse");
                            }else{
                                exit(" Error: Receivers accounting period not the same as warehouse.\nGudang pemilik dalam periode "
                                        .substr($tanggal,3,7)."\nGudang tujuan "
                                        .$pengguna." dalam periode ".substr(tanggalnormal($tanggalsana),3,7));
                            }  
                    }
                    # Get Journal Counter
                    $queryJ = selectQuery($dbname,'keu_5kelompokjurnal','nokounter',
                        "kodeorg='".$ptpengguna."' and kodekelompok='".$kodeJurnal."' ");
                    $tmpKonter = fetchData($queryJ);
                    $konter = addZero($tmpKonter[0]['nokounter']+1,3);

                    # Transform No Jurnal dari No Transaksi
                    $nojurnal = str_replace("-","",$tanggalsana)."/".$pengguna."/".$kodeJurnal."/".$konter;
                    #======================== /Nomor Jurnal ============================
                    # Prep Header
                    unset($dataRes['header']);//ganti header    
                    $dataRes['header'] = array(
                            'nojurnal'=>$nojurnal,
                            'kodejurnal'=>$kodeJurnal,
                            'tanggal'=>$tanggalsana,
                            'tanggalentry'=>date('Ymd'),
                            'posting'=>1,
                            'totaldebet'=>($jumlah*$hargarata),
                            'totalkredit'=>(-1*$jumlah*$hargarata),
                            'amountkoreksi'=>'0',
                            'noreferensi'=>$notransaksi,
                            'autojurnal'=>'1',
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                'revisi'=>'0'                        
                        );

                        # Data Detail
                         $keterangan="Pemakaian barang ".$namabarang." ".$jumlah." ".$satuan." ".substr($_POST['tanggal'],0,7);
                         $keterangan=substr($keterangan,0,150);
                        $noUrut = 1;
                        unset($dataRes['detail']);//ganti detail 
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>$tanggalsana,
                            'nourut'=>$noUrut,
                            'noakun'=>$akunpekerjaan,
                            'keterangan'=>$keterangan,
                            'jumlah'=>($jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>$kodekegiatan,
                            'kodeasset'=>$kodeasset,
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                'revisi'=>'0'                            
                        );
                        $noUrut++;

                        # Kredit
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>$tanggalsana,
                            'nourut'=>$noUrut,
                            'noakun'=>$akunspl,
                            'keterangan'=>$keterangan,
                            'jumlah'=>(-1*$jumlah*$hargarata),
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$pengguna,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>$kodebarang,
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>$notransaksi,
                            'noaruskas'=>'',
                            'kodevhc'=>$kodemesin,
                            'nodok'=>'',
                            'kodeblok'=>$blok,
                'revisi'=>'0'                       
                        );
                        $noUrut++; 
                    #===========EXECUTE                      
 if((substr($kodebarang,0,3)<'400' or substr($kodebarang,0,1)=='9') and trim($akunbarang)!=''){      
            $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
            if(!mysql_query($insHead)) {
                $headErr .= "Insert Header Error : ".addslashes(mysql_error($conn))."\n";
            }
            if($headErr=='') {
                $detailErr = '';
                foreach($dataRes['detail'] as $row) {
                    $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                    if(!mysql_query($insDet)) {
                        $detailErr .= "Insert Detail Error : ".addslashes(mysql_error($conn))."\n";
                        break;
                    }
                }
                if($detailErr=='') {
                    # Header and Detail inserted
                    #>>> Update Kode Jurnal
//                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
//                        "kodeorg='".$_SESSION['org']['kodeorganisasi'].
//                        "' and kodekelompok='".$kodeJurnal."'");
                    $updJurnal = updateQuery($dbname,'keu_5kelompokjurnal',array('nokounter'=>$konter),
                        "kodeorg='".$ptpengguna.
                        "' and kodekelompok='".$kodeJurnal."'");
                    if(!mysql_query($updJurnal)) {
                        echo "Update Kode Jurnal Error : ".addslashes(mysql_error($conn))."\n";
                        # Rollback if Update Failed
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                        if(!mysql_query($RBDet)) {
                            echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                            exit;
                        }
                        exit;
                    }
                    else{#berhasil di jurnal
//                    echo "warning: \nheader1pemilik_".$header1pemilik."\nnojurnal_".$nojurnal."\n".$insHead;
//                    exit; 

                        #proses gudang
                       
                    }
               
                } else {
                    echo $detailErr;
                    # Rollback, Delete Header
                    $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        //hapus juga sisi pemilik
                        $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                            if(!mysql_query($RBDet)) {
                                echo "Rollback Delete Header pemilik Error : ".addslashes(mysql_error($conn))."\n";
                                exit;
                            }
                    }
                }
            } else {
                echo $headErr;
                $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$header1pemilik."'");
                    if(!mysql_query($RBDet)) {
                        echo "Rollback Delete Header Error : ".addslashes(mysql_error($conn))."\n";
                        exit;
                    }
            }
         #============================================================================          
     }
     else{#jika aktiva hanya proses data gudang saja tanpa masuk ke jurnal
            #proses gudang     
         }  
        }//disini=====
    }        
    }
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
//=============================================
if(isTransactionPeriod())//check if transaction period is normal
{
$param=$_POST;
$optSupp=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optSatBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');

switch($param['proses']){
    case'getKonosemen':
       echo"<table class=sortable cellspacing=1 border=0>
            <thead>
            <tr class=rowheader>
              <td>No</td>
              <td>".$_SESSION['lang']['nopacking1']."</td>
              <td>".$_SESSION['lang']['nokonosemen']."</td>
              <td>".$_SESSION['lang']['namasupplier']."</td>
              <td>".$_SESSION['lang']['tanggalberangkat']."</td>
              <td>".$_SESSION['lang']['tanggaltiba']."</td>
            </tr>
            </thead>
            <tbody>";
        
    
        $sKono="select distinct nokonosemen,nokonosemenexp,shipper,tanggalberangkat,tanggaltiba from ".$dbname.".log_konosemenht where (nokonosemen like '%".$param['txtcari']."%' or nokonosemenexp like '%".$param['txtcari']."%')
                and postingkirim=1 and kodept='".$param['pemilikbarang']."' and statusmutasi=0 and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        //echo $sKono;
        $qKono=mysql_query($sKono) or die(mysql_error($conn));
        while($rKono=  mysql_fetch_assoc($qKono)){
                $spacking="select distinct nokonosemen,nopackl from ".$dbname.".log_rinciankono where nokonosemen='".$rKono['nokonosemen']."'";
                $qPacking=  mysql_query($spacking) or die(mysql_error($conn));
                while($rPacking=  mysql_fetch_assoc($qPacking)){
                $no+=1;
                    echo"<tr class=rowcontent style='cursor:pointer;' onclick=saveKono('".$rPacking['nopackl']."','".$param['gudang']."','".$param['gdngTujuan']."','".$param['pemilikbarang']."','".$param['tngl']."','".$param['nodok']."') 
                          title='".$_SESSION['lang']['save']." ".$_SESSION['lang']['mutasi']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['nokonosemen']." ".$rKono['nokonosemen']."'>";
                    echo"<td>".$no."</td>";
                    echo"<td>".$rPacking['nopackl']."</td>";
                    echo"<td>".$rKono['nokonosemen']."</td>";
                    echo"<td>".$optSupp[$rKono['shipper']]."</td>";
                    echo"<td>".tanggalnormal($rKono['tanggalberangkat'])."</td>";
                    echo"<td>".tanggalnormal($rKono['tanggaltiba'])."</td>";
                    echo"</tr>";
                }
        }
        
        echo"</tbody></table>";
        break;
        case'saveKonosemen':
            #hapus jika ada data dengan notransaksi yang sama
            $sdel="delete from ".$dbname.".log_transaksidt where notransaksi='".$param['notransaksiGdng']."'";
            if(!mysql_query($sdel)){
                exit("error: db bermasalah pas delete".mysql_error($conn)."__".$sdel);
            }
            $scek2="select * from ".$dbname.".log_transaksidt where statussaldo=1 and nopo='".$param['nokonsemen']."'";
            $qcek2=  mysql_query($scek2) or die(mysql_error());
            $rcek2=  mysql_num_rows($qcek2);
            if($rcek2==1){
                exit("error: Notransaksi ini sudah terposting");
            }
            
            #query string awal 
            $strDet="insert into ".$dbname.".log_transaksidt (`notransaksi`,`kodebarang`,`satuan`,`jumlah`,`jumlahlalu`,`updateby`,nopo)
                     values ";
            
            #proses simpan
            $nod=1;
            $awal=0;
            $statAda=0;
            $isiDetailAja=0;
            $sDataBrg="select * from ".$dbname.".log_rinciankono where nopackl='".$param['nokonsemen']."'  order by kodebarang desc";
            //exit("error:".$sDataBrg);
            $qDataBrg=mysql_query($sDataBrg) or die(mysql_error($conn));
            $rowDtBrg=  mysql_num_rows($qDataBrg);
            if($rowDtBrg==0){
                exit("error: This  ".$_SESSION['lang']['nokonosemen']." don't have PO from OWL system");
            }
            while($rDataBrg=  mysql_fetch_assoc($qDataBrg)){
                #klo belum pernah diterimakan jadiin error
                $scekPenerimaan="select * from ".$dbname.".log_transaksi_vw "
                               . "where nopo='".$rDataBrg['nopo']."' and kodebarang='".$rDataBrg['kodebarang']."'"
                               . " and  tipetransaksi=1 and post=1 and statussaldo=1 ";
                $qcekPenerimaan=  mysql_query($scekPenerimaan) or die(mysql_error($conn));
                $rcekPenerimaan=  mysql_num_rows($qcekPenerimaan);
                if($rcekPenerimaan!=1){
                    $errBrg[$rDataBrg['kodebarang']]+=1;
                    continue;
                }
                $whrSat="nopo='".$rDataBrg['nopo']."' and kodebarang='".$rDataBrg['kodebarang']."'";
                $optSat=makeOption($dbname, 'log_podt', 'kodebarang,satuan', $whrSat);
                $whrKonv="kodebarang='".$rDataBrg['kodebarang']."' and satuankonversi='".$optSat[$rDataBrg['kodebarang']]."'";
                $optKonv=makeOption($dbname, 'log_5stkonversi', 'kodebarang,jumlah', $whrKonv);
                $qty[$rDataBrg['kodebarang']]=$rDataBrg['jumlah'];
                if(isset($optKonv[$rDataBrg['kodebarang']])!=''){
                    $qty[$rDataBrg['kodebarang']]=$rDataBrg['jumlah']/$optKonv[$rDataBrg['kodebarang']];
                }
    
                
                #bentuk insert header dan detail mutasi sejumlah barang
                #1.ambil jumlah lalu
                $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi 
                      from ".$dbname.".log_transaksidt a,".$dbname.".log_transaksiht b where a.notransaksi=b.notransaksi 
                      and a.kodebarang='".$rDataBrg['kodebarang']."' and a.notransaksi<='".$param['notransaksiGdng']."' 
                      and tipetransaksi>4 and b.kodegudang='".$param['gdngPengirim']."'
                      order by notransaksi desc, waktutransaksi desc limit 1";   
                $res=mysql_query($str);
                $bar=mysql_fetch_object($res);
                if($bar->jumlah==''){
                    $bar->jumlah=0;
                }
                $jumlahlalu[$rDataBrg['kodebarang']]=$bar->jumlah;
                
                #2. ambil pemasukan barang yang belum di posting
                $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                       b on a.notransaksi=b.notransaksi where kodept='".$param['pemilikBrg']."' and b.kodebarang='".$rDataBrg['kodebarang']."' 
                       and a.tipetransaksi<5 and a.kodegudang='".$param['gdngPengirim']."' and a.post=0 group by kodebarang";
                //echo $str2; exit;
                $res2=mysql_query($str2);
                $bar2=mysql_fetch_object($res2);
                $qtynotpostedin[$rDataBrg['kodebarang']]=$bar2->jumlah;
                if($qtynotpostedin[$rDataBrg['kodebarang']]==''){
                   $qtynotpostedin[$rDataBrg['kodebarang']]=0;
                }
                #3. transaksi blm di  posting
                $qtynotposted=0;
                $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                b on a.notransaksi=b.notransaksi where kodept='".$param['pemilikBrg']."' and b.kodebarang='".$rDataBrg['kodebarang']."' 
                   and a.tipetransaksi>4
                   and a.kodegudang='".$param['gdngPengirim']."'
                   and a.post=0		   
                   group by kodebarang";

                $res2=mysql_query($str2);
                $bar2=mysql_fetch_object($res2);
                $qtynotposted[$rDataBrg['kodebarang']]=$bar2->jumlah;
                #4. ambil saldo qty
                $strs="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$rDataBrg['kodebarang']."'
                       and kodeorg='".$param['pemilikBrg']."' and kodegudang='".$param['gdngPengirim']."'";   
                $ress=mysql_query($strs);
                $bars=mysql_fetch_object($ress);
                $saldoqty[$rDataBrg['kodebarang']]=$bars->saldoqty;
                
                if($nod=='1'){
                    //exit("error:masukkkkkk ".$nod);
                    $nod=2;
                    #cross cek data 
                    #cek data sudah ada atau belum
                    $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."'";
                    
                    $res=mysql_query($str);
                    if(mysql_num_rows($res)==1){
                        $statAda=1;
                    }
                    
                    $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."' and post=1";
                    if(mysql_num_rows(mysql_query($str))>0){
                            $status=3;
                    }	
                    if($param['pemilikBrg']==''){
                        $status=4;
                    }
                    if($status==4){
                         echo " Gagal: Company code of the Recipient is not defined";
                         exit(0);
                    }
                    if($status==3){
                          echo " Gagal: Data has been posted";
                          exit(0);
                    }
                    $sKdPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($param['gdngTujuan'],0,4)."'";
                    $qKdPt=mysql_query($sKdPt) or die(mysql_error($sKdPt));
                    $rKdpt=mysql_fetch_assoc($qKdPt);
                    if($rKdpt['induk']==''){
                        exit("Kode PT Penerima Kosong");
                    }	
                   
                    #bentuk sql insert hedaer dan detail
                    if($statAda==0){
                    $strHead="insert into ".$dbname.".log_transaksiht (
                          `tipetransaksi`,`notransaksi`,
                          `tanggal`,`kodept`,`untukpt`,
                          `gudangx`,`keterangan`,
                          `kodegudang`,`user`,
                          `post`)
                          values('7','".$param['notransaksiGdng']."','".tanggaldgnbar($param['tanggal'])."','".$param['pemilikBrg']."','".$rKdpt['induk']."',
                          '".$param['gdngTujuan']."','".$catatan."',
                          '".$param['gdngPengirim']."',".$_SESSION['standard']['userid'].",'0')";	
                    
                         if(!mysql_query($strHead)){
                             exit("error:db bermasalah 1 :".mysql_error($conn)." ".$strHead);
                         }
                        #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
                        $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
                        $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
                        $qCek=mysql_query($sCek) or die(mysql_error($conn));
                        $rCek=mysql_fetch_assoc($qCek);
                        if($rCek['notransaksi']==$param['notransaksiGdng']){
                            $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                            $lbhSatuBrg[$rDataBrg['kodebarang']]+=1;
                            continue;
                        } 
                        if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
//                            echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup']." ".$saldoqty[$rDataBrg['kodebarang']]."+".$qtynotpostedin[$rDataBrg['kodebarang']]."-".$qtynotposted[$rDataBrg['kodebarang']]."=".$qty[$rDataBrg['kodebarang']];
//                            $status=6;//status ngeles
//                            exit(0);
                            $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                            continue;
                        }else{ 
                            $strDet.="('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$param['nokonsemen']."')";
                            $awal=1;
                        }
                    }else{
                        #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
                        $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
                        $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
                        $qCek=mysql_query($sCek) or die(mysql_error($conn));
                        $rCek=mysql_fetch_assoc($qCek);
                        if($rCek['notransaksi']==$param['notransaksiGdng']){
                            $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                            $lbhSatuBrg[$rDataBrg['kodebarang']]+=1;
                            continue;
                        }
    
                         if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
                            $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                            continue;
                        }else{
                         $strDet.="('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$param['nokonsemen']."')";
                         $awal=1;
                         $isiDetailAja+=1;
                        }
                    
                    }
                }else{
                   #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
                    $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
                    $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
                    $rCek=mysql_fetch_assoc($qCek);
                    if($rCek['notransaksi']==$param['notransaksiGdng']){
                        $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                        $lbhSatuBrg[$rDataBrg['kodebarang']]+=1;
                        continue;
                    }
                    
                        if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
                            $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
                            continue;
                        }else{
                            
                            if($awal==0){
                                $strDet.=" ('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$param['nokonsemen']."')";
                                $awal=1;
                                $isiDetailAja+=1;
                            }else{
                                $strDet.=",('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$param['nokonsemen']."')";
                                $isiDetailAja+=1;
                            }
                        }
                   
                }
            }
            if($isiDetailAja!=0){
                $strDet.=";";
                if(!mysql_query($strDet)){
                         //exit("error: ".$strDet);
                         exit("error:detail kosong :".mysql_error($conn)." ".$strDet);
                }
                    $strj="select a.* from ".$dbname.".log_transaksidt a where a.notransaksi='".$param['notransaksiGdng']."'";	
                    //exit("error:".$strj);
                    $resj=mysql_query($strj);
                    $no=0;
                    while($barj=mysql_fetch_object($resj)){
                        $no+=1;
                        //ambil namabarang
                        $namabarangk='';
                        $strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
                        $resk=mysql_query($strk);
                        $bark=mysql_fetch_object($resk);
                        $namabarangk=$bark->namabarang;
                        $bg="class=rowcontent";
    
                        $tab.="<tr ".$bg." >
                                    <td>".$no."</td>
                                        <td>".$barj->kodebarang."</td>
                                        <td>".$namabarangk."</td>
                                        <td>".$barj->satuan."</td>
                                        <td align=right>".number_format($barj->jumlah,2,'.',',')."</td>
                                        <td>
                                        &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delMutasi('".$param['notransaksiGdng']."','".$barj->kodebarang."');\">
                                        </td>
                                   </tr>";
                    }
            }else{
                #hapus jika ada transaksi kosong atau tidak ada data sama sekali
                $sdel="delete from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."'";
                if(!mysql_query($sdel)){
                    exit("error: db bermasalah pas delete".mysql_error($conn)."__".$sdel);
                }
                 $tab.="<tr ".$bg.">
                                <td colspan=6>Data Kosong 
                                    </td>
                               </tr>";
            }
             
          $isidet=count($errBrg);
          if($isidet!=0){
              foreach($errBrg as $lstBrg){
                  $no+=1;
                    //ambil namabarang
                    $namabarangk='';
                    $strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$lstBrg."'";
                    $resk=mysql_query($strk);
                    $bark=mysql_fetch_object($resk);
                    $namabarangk=$bark->namabarang;
                    $bg="bgcolor=red";
                    if($lbhSatuBrg[$lstBrg]>0){
                                $bg="bgcolor=orange";
                     }
                     $tab.="<tr ".$bg.">
                                <td>".$no."</td>
                                    <td>".$lstBrg."</td>
                                    <td>".$namabarangk."</td>
                                    <td>".$optSatBrg[$lstBrg]."</td>
                                    <td align=right>0</td>
                                    <td>
                                    &nbsp; 
                                    </td>
                               </tr>";
              }
          }
           $tab.="<tr class=rowcontent><td colspan=6>
                   *row berwarna merah dikarenakan saldo tidak mencukupi dan tidak tersimpan ke dalam database<br />
                   *row berwarna orange dikarenakan ada dua barang yang sama dalam satu konosemen, silakan dimutasi ulang dgn notransaksi yang berbeda<br />
                      </td></tr>";
          echo $tab;    
            
        break;
        
        
        
        
        
        
        
        
        
//        case'saveKonosemen':
//            $scek="select * from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."' and post=1";
//            $qcek=  mysql_query($scek) or die(mysql_error());
//            $rcek=  mysql_num_rows($qcek);
//            if($rcek==1){
//                exit("error: Notransaksi ini sudah terposting");
//            }
//            $scek2="select * from ".$dbname.".log_transaksidt where statussaldo=1 and nopo='".$param['nokonsemen']."'";
//            $qcek2=  mysql_query($scek2) or die(mysql_error());
//            $rcek2=  mysql_num_rows($qcek2);
//            if($rcek2==1){
//                exit("error: Notransaksi ini sudah terposting");
//            }
//            
//            #hapus jika ada data dengan notransaksi yang sama
//            $sdel="delete from ".$dbname.".log_transaksidt where notransaksi='".$param['notransaksiGdng']."'";
//            if(!mysql_query($sdel)){
//                exit("error: db bermasalah pas delete".mysql_error($conn)."__".$sdel);
//            }
//            #query string awal 
//            $strDet="insert into ".$dbname.".log_transaksidt (`notransaksi`,`kodebarang`,`satuan`,`jumlah`,`jumlahlalu`,`updateby`,nopo)
//                     values ";
//            
//            #proses simpan
//            $nod=1;
//            $awal=0;
//            $statAda=0;
//            $sDataBrg="select * from ".$dbname.".log_rinciankono where "
//                    . "nokonosemen='".$param['nokonsemen']."' and nopo!='' order by nopo desc";
//            //exit("error:".$sDataBrg);
//            $qDataBrg=mysql_query($sDataBrg) or die(mysql_error($conn));
//            $rowDtBrg=  mysql_num_rows($qDataBrg);
//            if($rowDtBrg==0){
//                exit("error: This  ".$_SESSION['lang']['nokonosemen']." don't have PO from OWL system");
//            }
//            while($rDataBrg=  mysql_fetch_assoc($qDataBrg)){
//                #klo sudah pernah diterimakan jadiin error
////                $scekPenerimaan="select * from ".$dbname.".log_transaksi_vw "
////                               . "where nopo='".$rDataBrg['nopo']."' and kodebarang='".$rDataBrg['kodebarang']."'"
////                               . " and tipetransaksi=1 and post=1 and statussaldo=1 ";
////                $qcekPenerimaan=  mysql_query($scekPenerimaan) or die(mysql_error($conn));
////                $rcekPenerimaan=  mysql_num_rows($qcekPenerimaan);
////                if($rcekPenerimaan!=1){
////                    $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
////                   // exit("error:".$scekPenerimaan);
////                    continue;
////                }else{
//                 
//                        $whrSat="nopo='".$rDataBrg['nopo']."' and kodebarang='".$rDataBrg['kodebarang']."'";
//                        $optSat=makeOption($dbname, 'log_podt', 'kodebarang,satuan', $whrSat);
//                        $whrKonv="kodebarang='".$rDataBrg['kodebarang']."' and satuankonversi='".$optSat[$rDataBrg['kodebarang']]."'";
//                        $optKonv=makeOption($dbname, 'log_5stkonversi', 'kodebarang,jumlah', $whrKonv);
//                        $qty[$rDataBrg['kodebarang']]=$rDataBrg['jumlah'];
//                        if(isset($optKonv[$rDataBrg['kodebarang']])!=''){
//                            $qty[$rDataBrg['kodebarang']]=$rDataBrg['jumlah']/$optKonv[$rDataBrg['kodebarang']];
//                        }
//                        #array cek kode barang lebih dari satu
//                        $lbhSatuBrg[$rDataBrg['kodebarang']]+=1;
//
//                        #bentuk insert header dan detail mutasi sejumlah barang
//                        #1.ambil jumlah lalu
//                        $str="select a.jumlah as jumlah,b.nopo as nopo,a.notransaksi as notransaksi,a.waktutransaksi 
//                              from ".$dbname.".log_transaksidt a,".$dbname.".log_transaksiht b where a.notransaksi=b.notransaksi 
//                              and a.kodebarang='".$rDataBrg['kodebarang']."' and a.notransaksi<='".$param['notransaksiGdng']."' 
//                              and tipetransaksi>4 and b.kodegudang='".$param['gdngPengirim']."'
//                              order by notransaksi desc, waktutransaksi desc limit 1";   
//                        //exit("error".$str);
//                        $res=mysql_query($str);
//                        $bar=mysql_fetch_object($res);
//                        if($bar->jumlah==''){
//                            $bar->jumlah=0;
//                        }
//                        $jumlahlalu[$rDataBrg['kodebarang']]=$bar->jumlah;
//
//                        #2. ambil pemasukan barang yang belum di posting
//                        $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
//                               b on a.notransaksi=b.notransaksi where kodept='".$param['pemilikBrg']."' and b.kodebarang='".$rDataBrg['kodebarang']."' 
//                               and a.tipetransaksi<5 and a.kodegudang='".$param['gdngPengirim']."' and a.post=0 group by kodebarang";
//                        //echo $str2; exit;
//                        $res2=mysql_query($str2);
//                        $bar2=mysql_fetch_object($res2);
//                        $qtynotpostedin[$rDataBrg['kodebarang']]=$bar2->jumlah;
//                        if($qtynotpostedin[$rDataBrg['kodebarang']]==''){
//                           $qtynotpostedin[$rDataBrg['kodebarang']]=0;
//                        }
//                        #3. transaksi blm di  posting
//                        $qtynotposted=0;
//                        $str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
//                        b on a.notransaksi=b.notransaksi where kodept='".$param['pemilikBrg']."' and b.kodebarang='".$rDataBrg['kodebarang']."' 
//                           and a.tipetransaksi>4
//                           and a.kodegudang='".$param['gdngPengirim']."'
//                           and a.post=0		   
//                           group by kodebarang";
//
//                        $res2=mysql_query($str2);
//                        $bar2=mysql_fetch_object($res2);
//                        $qtynotposted[$rDataBrg['kodebarang']]=$bar2->jumlah;
//                        #4. ambil saldo qty
//                        $strs="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$rDataBrg['kodebarang']."'
//                               and kodeorg='".$param['pemilikBrg']."' and kodegudang='".$param['gdngPengirim']."'";   
//                        $ress=mysql_query($strs);
//                        $bars=mysql_fetch_object($ress);
//                        $saldoqty[$rDataBrg['kodebarang']]=$bars->saldoqty;
//
//                        if($nod=='1'){
//                            //exit("error:masukkkkkk ".$nod);
//                            $nod=2;
//                            #cross cek data 
//                            #cek data sudah ada atau belum
//                            $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."'";
//                            $res=mysql_query($str);
//                            if(mysql_num_rows($res)==1){
//                                $statAda=1;
//                            }
//
//                            $str="select * from ".$dbname.".log_transaksiht where notransaksi='".$param['notransaksiGdng']."' and post=1";
//                            if(mysql_num_rows(mysql_query($str))>0){
//                                    $status=3;
//                            }	
//                            if($param['pemilikBrg']==''){
//                                $status=4;
//                            }
//                            if($status==4){
//                                 echo " Gagal: Company code of the Recipient is not defined";
//                                 exit(0);
//                            }
//                            if($param['gdngTujuan']==''){
//                                exit("error:Destination is obligatory");
//                            }
//                            if($status==3){
//                                  echo " Gagal: Data has been posted";
//                                  exit(0);
//                            }
//                            $sKdPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($param['gdngTujuan'],0,4)."'";
//                            $qKdPt=mysql_query($sKdPt) or die(mysql_error($sKdPt));
//                            $rKdpt=mysql_fetch_assoc($qKdPt);
//                            if($rKdpt['induk']==''){
//                                exit("Kode PT Penerima Kosong");
//                            }	
//
//                            #bentuk sql insert hedaer dan detail
//                            if($statAda==0){
//                            $strHead="insert into ".$dbname.".log_transaksiht (
//                                  `tipetransaksi`,`notransaksi`,
//                                  `tanggal`,`kodept`,`untukpt`,
//                                  `gudangx`,`keterangan`,
//                                  `kodegudang`,`user`,
//                                  `post`)
//                                  values('7','".$param['notransaksiGdng']."','".tanggaldgnbar($param['tanggal'])."','".$param['pemilikBrg']."','".$rKdpt['induk']."',
//                                  '".$param['gdngTujuan']."','".$catatan."',
//                                  '".$param['gdngPengirim']."',".$_SESSION['standard']['userid'].",'0')";	
//                            //exit("error".$strHead);
//                                 if(!mysql_query($strHead)){
//                                     exit("error:db bermasalah 1 :".mysql_error($conn)." ".$strHead);
//                                 }
//                                    #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
//                                    $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
//                                    $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
//                                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
//                                    $rCek=mysql_fetch_assoc($qCek);
//                                    if($rCek['nopo']!=''){
//                                        $lbhSatuBrg[$rDataBrg['kodebarang']]=0;
//                                        //continue;
//                                    }
//                                    if($lbhSatuBrg[$rDataBrg['kodebarang']]>1){
//                                        continue;
//                                    }else{
//                                     if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
//                                        $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
//                                        continue;
//                                    }else{
//                                     $strDet.="('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$rDataBrg['nokonosemen']."')";
//                                     $awal=1;
//                                    }
//                                }
////                            if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
////                                    //echo " Error: ".$_SESSION['lang']['saldo']." ".$_SESSION['lang']['tidakcukup']." ".$saldoqty[$rDataBrg['kodebarang']]."+".$qtynotpostedin[$rDataBrg['kodebarang']]."-".$qtynotposted[$rDataBrg['kodebarang']]."=".$qty[$rDataBrg['kodebarang']];
////        //                            $status=6;//status ngeles
////                                     //exit(0);
////                                    $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
////                                    continue;
////                                }else{
////                                $strDet.="('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$rDataBrg['nokonosemen']."')";
////                                //exit("error:".$strDet);
////                                $awal=1;
////                                }
//                            }else{
//                                    #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
//                                    $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
//                                    $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
//                                    $qCek=mysql_query($sCek) or die(mysql_error($conn));
//                                    $rCek=mysql_fetch_assoc($qCek);
//                                    if($rCek['nopo']!=''){
//                                        $lbhSatuBrg[$rDataBrg['kodebarang']]=0;
//                                        //continue;
//                                    }
//                                    if($lbhSatuBrg[$rDataBrg['kodebarang']]>1){
//                                        continue;
//                                    }else{
//                                     if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
//                                        $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
//                                        continue;
//                                    }else{
//                                     $strDet.="('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$rDataBrg['nokonosemen']."')";
//                                     $awal=1;
//                                    }
//                                }
//                            }
//                        }else{
//                            #lanjutkan jika detail sudah terinput di notransaksi mutasi sebelumnya
//                            $whrDetBrg="nopo='".$param['nokonsemen']."' and kodebarang='".$rDataBrg['kodebarang']."'";
//                            $sCek="select distinct nopo from ".$dbname.".log_transaksidt where ".$whrDetBrg."";
//                            $qCek=mysql_query($sCek) or die(mysql_error($conn));
//                            $rCek=mysql_fetch_assoc($qCek);
//                            if($rCek['nopo']!=''){
//                                $lbhSatuBrg[$rDataBrg['kodebarang']]=0;
//                                //continue;
//                            }
//                            if($lbhSatuBrg[$rDataBrg['kodebarang']]>1){
//                                    continue;
//                            }else{
//                                if(($qty[$rDataBrg['kodebarang']]+$qtynotposted[$rDataBrg['kodebarang']])>($saldoqty[$rDataBrg['kodebarang']]+$qtynotpostedin[$rDataBrg['kodebarang']])){
//                                    $errBrg[$rDataBrg['kodebarang']]=$rDataBrg['kodebarang'];
//                                    continue;
//                                }else{
//
//                                    if($awal==0){
//                                        $strDet.=" ('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$rDataBrg['nokonosemen']."')";
//                                        $awal=1;
//                                    }else{
//                                        $strDet.=",('".$param['notransaksiGdng']."','".$rDataBrg['kodebarang']."','".$optSatBrg[$rDataBrg['kodebarang']]."',".$qty[$rDataBrg['kodebarang']].",".$jumlahlalu[$rDataBrg['kodebarang']].",'".$_SESSION['standard']['userid']."','".$rDataBrg['nokonosemen']."')";
//                                    }
//                                }
//                           }
//                        }
//                    //}
//            }
//                    if(!mysql_query($strDet)){
//                                       //exit("error: ".$strDet);
//                                       exit("error:db bermasalah 3 :".mysql_error($conn)." ".$strDet);
//                    }
////                    if($statAda==1){
////                       
////                           if(!mysql_query($strDet)){
////                             //exit("error: ".$strDet);
////                             exit("error:db bermasalah 2 :".mysql_error($conn)." ".$strDet);
////                           }
////                    }else{
////
////                           if(!mysql_query($strDet)){
////                               //exit("error: ".$strDet);
////                               exit("error:db bermasalah 3 :".mysql_error($conn)." ".$strDet);
////                            }
////                    }
//                     $strj="select a.* from ".$dbname.".log_transaksidt a where a.notransaksi='".$param['notransaksiGdng']."'";	
//                     //exit("error:".$strj);
//                     $resj=mysql_query($strj);
//                     $no=0;
//                     while($barj=mysql_fetch_object($resj)){
//                            $no+=1;
//                            //ambil namabarang
//                            $namabarangk='';
//                            $strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$barj->kodebarang."'";
//                            $resk=mysql_query($strk);
//                            $bark=mysql_fetch_object($resk);
//                            $namabarangk=$bark->namabarang;
//                            $bg="class=rowcontent";
//                            if($lbhSatuBrg[$barj->kodebarang]>1){
//                                $bg="bgcolor=orange";
//                            }
//                            $tab.="<tr ".$bg." >
//                                        <td>".$no."</td>
//                                            <td>".$barj->kodebarang."</td>
//                                            <td>".$namabarangk."</td>
//                                            <td>".$barj->satuan."</td>
//                                            <td align=right>".number_format($barj->jumlah,2,'.',',')."</td>
//                                            <td>
//                                            &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delMutasi('".$param['notransaksiGdng']."','".$barj->kodebarang."');\">
//                                            </td>
//                                       </tr>";
//                  }
//            
//          $isidet=count($errBrg);
//          if($isidet!=0){
//              foreach($errBrg as $lstBrg){
//                  $no+=1;
//                    //ambil namabarang
//                    $namabarangk='';
//                    $strk="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$lstBrg."'";
//                    $resk=mysql_query($strk);
//                    $bark=mysql_fetch_object($resk);
//                    $namabarangk=$bark->namabarang;
//                     $tab.="<tr bgcolor=red>
//                                <td>".$no."</td>
//                                    <td>".$lstBrg."</td>
//                                    <td>".$namabarangk."</td>
//                                    <td>".$optSatBrg[$lstBrg]."</td>
//                                    <td align=right>0</td>
//                                    <td>
//                                    &nbsp 
//                                    </td>
//                               </tr>";
//              }
//               $tab.="<tr class=rowcontent><td colspan=6>
//                   *row berwarna merah dikarenakan saldo tidak mencukupi dan tidak tersimpan ke dalam database<br />
//                   *row berwarna orange dikarenakan ada dua barang yang sama dalam satu konosemen, silakan dimutasi ulang dgn notransaksi yang berbeda<br />
//                   
//                      </td></tr>";
//          }else{
//               $tab.="<tr class=rowcontent><td colspan=6>
//                   *row berwarna orange dikarenakan ada dua barang yang sama dalam satu konosemen, silakan dimutasi ulang dgn notransaksi yang berbeda<br />                   
//                      </td></tr>";
//          }
//          echo $tab;
//    break;
    
}
}
?>
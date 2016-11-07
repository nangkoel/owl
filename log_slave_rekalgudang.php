<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$param=$_POST;
if(isset($_GET['proses'])!=''){
    $param['proses']=$_GET['proses'];
}
$optKlmk=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

 
switch($param['proses']){
       case'getPeriode':
           $optKlmpk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
           $sPrd="select distinct periode from ".$dbname.".setup_periodeakuntansi 
                  where kodeorg='".$param['gdngId']."' 
                  order by periode desc";
           //exit("error:".$sPrd);
           $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
           while($rPrd=mysql_fetch_assoc($qPrd)){
               $optKlmpk.="<option value=".$rPrd['periode'].">".$rPrd['periode']."</option>";
           }
           echo $optKlmpk;
       break;
       case'getKlmmpkBrg':
           $optKlmpk="<option value=''>".$_SESSION['lang']['all']."</option>";
           $sPrd="select distinct left(kodebarang,3) as periode from ".$dbname.".log_5saldobulanan 
                  where kodegudang='".$param['gdngId']."' and periode='".$param['periodeGdng']."' 
                  order by left(kodebarang,3) desc";
           $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
           while($rPrd=mysql_fetch_assoc($qPrd)){
               $optKlmpk.="<option value=".$rPrd['periode'].">".$rPrd['periode']."-".$optKlmk[$rPrd['periode']]."</option>";
           }
           echo $optKlmpk;
       break;
       case'preview':
           $wer="";
        if($param['kdBrg']!=''){
            $wer="and kodebarang='".$param['kdBrg']."'";
        }else{
            exit("error: kodebarang tidak boleh kosong");
        }
        if($param['periodeGdng']==''){
            exit("error: periode tidak boleh kosong");
        }
        if($param['gdngId']==''){
            exit("error: periode tidak boleh kosong");
        }
        if (substr($param['periodeGdng'],0,4)=='2014' and substr($param['periodeGdng'],5,2)=='12'){
            exit("error:\n\rPeriode Desember 2014 tidak bisa dilakukan rekalkulasi karena sudah dilakukan CUT OFF.");
        }
        $sPrd="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$param['gdngId']."' 
               and periode='".$param['periodeGdng']."'";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        $rPrd=mysql_fetch_object($qPrd);
       #masuk transaksi
       $stransaksi="select sum(jumlah) as jumlah,kodebarang,kodegudang from ".$dbname.".log_transaksi_vw
                    where tanggal between '".$rPrd->tanggalmulai."' and '".$rPrd->tanggalsampai."' and kodegudang='".$param['gdngId']."'
                    ".$wer." and tipetransaksi<5 and statussaldo in (1,2)
                    group by kodebarang";
       //exit("error:".$stransaksi);
       $qtransaksi=mysql_query($stransaksi) or die(mysql_error($conn));
       while($rTransaksi=  mysql_fetch_assoc($qtransaksi)){
           $dtBrg[$rTransaksi['kodebarang']]=$rTransaksi['kodebarang'];
           $dtMasuk[$rTransaksi['kodebarang']]=$rTransaksi['jumlah'];
       }
       #keluar transaksi
       $stransaksi="select sum(jumlah) as jumlah,kodebarang,kodegudang from ".$dbname.".log_transaksi_vw
                    where tanggal between '".$rPrd->tanggalmulai."' and '".$rPrd->tanggalsampai."' and kodegudang='".$param['gdngId']."'
                    ".$wer." and tipetransaksi>4 and statussaldo in (1,2)
                    group by kodebarang";
       //exit("error:".$stransaksi);
       $qtransaksi=mysql_query($stransaksi) or die(mysql_error($conn));
       while($rTransaksi=  mysql_fetch_assoc($qtransaksi)){
           $dtBrg[$rTransaksi['kodebarang']]=$rTransaksi['kodebarang'];
           $dtKeluar[$rTransaksi['kodebarang']]=$rTransaksi['jumlah'];
       }
       #log saldo bulanan
       $sSaldo="select distinct kodebarang,saldoakhirqty,hargarata,qtymasuk,qtykeluar,saldoawalqty
                from ".$dbname.".log_5saldobulanan where 
                kodegudang='".$param['gdngId']."' ".$wer."
                and periode='".$param['periodeGdng']."'";
       //exit("error:".$sSaldo);
       $qSaldo=mysql_query($sSaldo) or die(mysql_error($conn));
       while($rSaldo=  mysql_fetch_assoc($qSaldo)){
           $dtBrg[$rSaldo['kodebarang']]=$rSaldo['kodebarang'];
           $drSalMasuk[$rSaldo['kodebarang']]=$rSaldo['qtymasuk'];
           $drSalAwal[$rSaldo['kodebarang']]=$rSaldo['saldoawalqty'];
           $drSalKeluar[$rSaldo['kodebarang']]=$rSaldo['qtykeluar'];
           $drHrgRata[$rSaldo['kodebarang']]=$rSaldo['hargarata'];
           $drSalAkhir[$rSaldo['kodebarang']]=$rSaldo['saldoakhirqty'];
       }
       $saldoAKhir=($dtMasuk[$param['kdBrg']]+$drSalAwal[$param['kdBrg']])-$dtKeluar[$param['kdBrg']];
       if($dtMasuk[$param['kdBrg']]==''){
           $dtMasuk[$param['kdBrg']]=0;
       }
       if($dtKeluar[$param['kdBrg']]==''){
           $dtKeluar[$param['kdBrg']]=0;
       }
       $srekal="update ".$dbname.".log_5saldobulanan set
                saldoakhirqty='".$saldoAKhir."',
                nilaisaldoakhir='".($saldoAKhir*$drHrgRata[$param['kdBrg']])."',
                qtymasuk='".$dtMasuk[$param['kdBrg']]."',
                qtymasukxharga='".($dtMasuk[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."',
                qtykeluar='".$dtKeluar[$param['kdBrg']]."',
                qtykeluarxharga='".($dtKeluar[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."'
                where kodegudang='".$param['gdngId']."' ".$wer." and periode='".$param['periodeGdng']."'";
       if(!mysql_query($srekal)){
           exit("error:\n Rekalkulasi tidak berhasil___".$srekal);
       }else{
            //cek saldo bulan berikutnya lalu update saldo awalnya
            $cekNextSaldo="select periode from ".$dbname.".log_5saldobulanan where 
                     kodegudang='".$param['gdngId']."' ".$wer."
                     and periode>'".$param['periodeGdng']."' limit 1";
            $resx=mysql_query($cekNextSaldo);
            while($barx=mysql_fetch_object($resx)){
                if (substr($barx->periode,0,4)=='2014' and substr($barx->periode,5,2)=='12'){
                    // Khusus Des 2014 tidak boleh karena sudah CUT OFF
                } else {
                    $updateNextSaldo="update ".$dbname.".log_5saldobulanan set 
                        saldoawalqty=".$saldoAKhir.", 
                        hargaratasaldoawal=".$drHrgRata[$param['kdBrg']].",
                        nilaisaldoawal=".($saldoAKhir*$drHrgRata[$param['kdBrg']])."
                        where kodegudang='".$param['gdngId']."' ".$wer."
                        and periode='".$barx->periode."'";
                    if(!mysql_query($updateNextSaldo)){
                        exit("error:\n Rekalkulasi tidak berhasil __".$updateNextSaldo);
                    }
                }
            }
                
            $supdatedata="update ".$dbname.".log_5masterbarangdt set saldoqty='".$saldoAKhir."' where
            kodebarang='".$param['kdBrg']."' and kodegudang='".$param['gdngId']."'";
            if(!mysql_query($supdatedata)){
                exit("error:\n Rekalkulasi tidak berhasil __".$supdatedata);
            }
            
           $tab.="<table>";
           $tab.="<tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td>".tanggalnormal($rPrd->tanggalmulai)." s.d ".tanggalnormal($rPrd->tanggalsampai)."</td></tr>";
           $tab.="<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>".$param['kdBrg']."</td></tr>";
           $tab.="<tr><td>".$_SESSION['lang']['namabarang']."</td><td>:</td><td>".$optBrg[$param['kdBrg']]."</td></tr>";
           $tab.="<tr><td>".$_SESSION['lang']['kodegudang']."</td><td>:</td><td>".$param['gdngId']."</td></tr></table>";
           
           $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
           $tab.="<tr class=rowheader><td colspan=4>".$_SESSION['lang']['sebelum']."</td></tr>";
           $tab.="<tr class=rowheader>";
           $tab.="<td>".$_SESSION['lang']['saldoawal']."</td>";
           $tab.="<td>".$_SESSION['lang']['masuk']."</td>";
           $tab.="<td>".$_SESSION['lang']['keluar']."</td>";
           $tab.="<td>".$_SESSION['lang']['saldoakhir']."</td></tr>";
            $lstBrg=$param['kdBrg'];
           $salAkhir[$lstBrg]=($drSalAwal[$lstBrg]+$dtMasuk[$lstBrg])-$dtKeluar[$lstBrg];
           $tab.="<tr  class=rowcontent>";
           $tab.="<td align=right>".$drSalAwal[$lstBrg]."</td>";
           $tab.="<td align=right>".$drSalMasuk[$lstBrg]."</td>";
           $tab.="<td align=right>".$drSalKeluar[$lstBrg]."</td>";
           $tab.="<td align=right>".$drSalAkhir[$lstBrg]."</td>";
           $tab.="</tr>";
           $tab.="<tr class=rowheader><td colspan=4>".$_SESSION['lang']['sesudah']."</td></tr>";
           $tab.="<tr  class=rowcontent>";
           $tab.="<td align=right>".$drSalAwal[$lstBrg]."</td>";
           $tab.="<td align=right>".$dtMasuk[$lstBrg]."</td>";
           $tab.="<td align=right>".$dtKeluar[$lstBrg]."</td>";
           $tab.="<td align=right>".$salAkhir[$lstBrg]."</td>";
           $tab.="</tr>";
           $tab.="</table>";
           echo $tab."#####1";
       }      
       break;
       case'getNmbrg':
        
        if($param['periodeGdng']==''){
            exit("error: periode tidak boleh kosong");
        }
        if($param['gdngId']==''){
            exit("error: periode tidak boleh kosong");
        }
        echo"<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                <div style=\"overflow:auto;height:295px;width:455px;\">
                <table cellpading=1 border=0 class=sortbale>
                <thead>
                <tr class=rowheader>
                <td>No.</td>
                <td>".$_SESSION['lang']['kodebarang']."</td>
                <td>".$_SESSION['lang']['namabarang']."</td>
                </tr><tbody>
                ";
//         $sSupplier="select distinct a.namabarang,b.kodebarang from ".$dbname.".log_5masterbarang a
//                     inner join ".$dbname.".log_transaksi_vw b on a.kodebarang=b.kodebarang
//                     where namabarang like '%".$param['nmBarang']."%' or b.kodebarang like '%".$param['nmBarang']."%'  
//                      and left(tanggal,7)='".$param['periodeGdng']."' and kodegudang='".$param['gdngId']."'
//                     order by namabarang asc";
         $sSupplier="select namabarang,kodebarang from ".$dbname.".log_5masterbarang a
                     where namabarang like '%".$param['nmBarang']."%' or kodebarang like '%".$param['nmBarang']."%'  
                     order by namabarang asc";
         $qSupplier=mysql_query($sSupplier) or die(mysql_error($conn));
         while($rSupplier=mysql_fetch_assoc($qSupplier))
         {
             $no+=1;
             echo"<tr class=rowcontent onclick=\"setData('".$rSupplier['kodebarang']."','".$rSupplier['namabarang']."')\">
                 <td>".$no."</td>
                 <td>".$rSupplier['kodebarang']."</td>
                 <td>".$rSupplier['namabarang']."</td>
            </tr>";
         }
            echo"</tbody></table></div>";
       break;
       case'preview2':
       if($param['periodeGdng2']==''){
           exit("error:\n warehouse period can't empty!!");
       }
       if($param['gdngId2']==''){
           exit("error:\n warehouse can't empty!!");
       }
          $tab.="<table cellpadding=1 cellspacing=1 class=sortable border=0><thead><tr>";
          $tab.="<td>".$_SESSION['lang']['kodebarang']."</td>";
          $tab.="<td>".$_SESSION['lang']['namabarang']."</td>";
          $tab.="<td>".$_SESSION['lang']['saldoawal']."</td>";
          $tab.="<td>".$_SESSION['lang']['masuk']."</td>";
          $tab.="<td>".$_SESSION['lang']['keluar']."</td>";
          $tab.="<td>".$_SESSION['lang']['saldoakhir']."</td>
                 <td>".$_SESSION['lang']['action']."</td>
                </tr></thead><tbody>";
          //and  periode='".$param['periodeGdng2']."' 
          $sdata="select distinct kodebarang,saldoakhirqty,saldoawalqty,qtymasuk,qtykeluar,periode,(saldoawalqty+qtymasuk-qtykeluar) as pembanding from 
                   ".$dbname.".log_5saldobulanan where kodegudang='".$param['gdngId2']."' and  periode='".$param['periodeGdng2']."' 
                   and (saldoawalqty+qtymasuk-qtykeluar-saldoakhirqty)!=0 ";
          //exit("error:".$sdata);
          $qdata=mysql_query($sdata) or die(mysql_error($conn));
          while($rdata=  mysql_fetch_assoc($qdata)){
              if((number_format($rdata['saldoakhirqty'],2))!=(number_format($rdata['pembanding'],2))){
              $Er+=1;
            $tab.="<tr class=rowcontent id=guaikutaja_".$Er.">
                   <td >".$rdata['kodebarang']."</td>";
            $tab.="<td>".$optBrg[$rdata['kodebarang']]."</td>";
            $tab.="<td align=right id=sawal_".$Er.">".$rdata['saldoawalqty']."</td>";
            $tab.="<td align=right id=qtymsk_".$Er.">".$rdata['qtymasuk']."</td>";
            $tab.="<td align=right id=qtyklr_".$Er.">".$rdata['qtykeluar']."</td>";
            $tab.="<td align=right id=salak_".$Er.">".$rdata['saldoakhirqty']."__".$rdata['pembanding']."</td>
                   <td><button class=mybutton onclick=reklasDt('".$rdata['kodebarang']."','".$param['gdngId2']."','".$rdata['periode']."','".$Er."') >".$_SESSION['lang']['rekalkulasi']."</button></td>
                   </tr>";
              }
          }
          $tab.="</tbody></table>";
          echo $tab;
       break;
       case'reklasData':
           $sPrd="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$param['gdngId']."' 
                  and periode='".$param['periodeGdng']."'";
           $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
           $rPrd=mysql_fetch_object($qPrd);
        #masuk transaksi
       $stransaksi="select sum(jumlah) as jumlah,kodebarang,kodegudang from ".$dbname.".log_transaksi_vw
                    where tanggal between '".$rPrd->tanggalmulai."' and '".$rPrd->tanggalsampai."' and kodegudang='".$param['gdngId']."'
                    and kodebarang='".$param['kdBrg']."' and tipetransaksi<5 and statussaldo=1
                    group by kodebarang";
       //exit("error:".$stransaksi);
       $qtransaksi=mysql_query($stransaksi) or die(mysql_error($conn));
       while($rTransaksi=  mysql_fetch_assoc($qtransaksi)){
           $dtBrg[$rTransaksi['kodebarang']]=$rTransaksi['kodebarang'];
           $dtMasuk[$rTransaksi['kodebarang']]=$rTransaksi['jumlah'];
       }
       #keluar transaksi
       $stransaksi="select sum(jumlah) as jumlah,kodebarang,kodegudang from ".$dbname.".log_transaksi_vw
                    where tanggal between '".$rPrd->tanggalmulai."' and '".$rPrd->tanggalsampai."' and kodegudang='".$param['gdngId']."'
                    and kodebarang='".$param['kdBrg']."' and tipetransaksi>4 and statussaldo=1
                    group by kodebarang";
       //exit("error:".$stransaksi." disni");
       $qtransaksi=mysql_query($stransaksi) or die(mysql_error($conn));
       while($rTransaksi=  mysql_fetch_assoc($qtransaksi)){
           $dtBrg[$rTransaksi['kodebarang']]=$rTransaksi['kodebarang'];
           $dtKeluar[$rTransaksi['kodebarang']]=$rTransaksi['jumlah'];
       }
       #log saldo bulanan
       $sSaldo="select distinct kodebarang,saldoakhirqty,hargarata,qtymasuk,qtykeluar,saldoawalqty,(saldoawalqty+qtymasuk-qtykeluar) as pembanding,
                hargaratasaldoawal
                from ".$dbname.".log_5saldobulanan where 
                kodegudang='".$param['gdngId']."' and kodebarang='".$param['kdBrg']."'
                and periode='".$param['periodeGdng']."'";
        //exit("error:".$sSaldo);
       $qSaldo=mysql_query($sSaldo) or die(mysql_error($conn));
       while($rSaldo=  mysql_fetch_assoc($qSaldo)){
           $dtBrg[$rSaldo['kodebarang']]=$rSaldo['kodebarang'];
           $drSalMasuk[$rSaldo['kodebarang']]=$rSaldo['qtymasuk'];
           $drSalAwal[$rSaldo['kodebarang']]=$rSaldo['saldoawalqty'];
           $drSalKeluar[$rSaldo['kodebarang']]=$rSaldo['qtykeluar'];
           $drHrgRata[$rSaldo['kodebarang']]=$rSaldo['hargarata'];
           $drSalAkhir[$rSaldo['kodebarang']]=$rSaldo['saldoakhirqty'];
           $dtPmbnding[$rSaldo['kodebarang']]=abs($rSaldo['pembanding']);
           $dtHrgRata[$rSaldo['kodebarang']]=$rSaldo['hargaratasaldoawal'];
       }
       $saldoAKhir=($dtMasuk[$param['kdBrg']]+$drSalAwal[$param['kdBrg']])-$dtKeluar[$param['kdBrg']];
       if($dtMasuk[$param['kdBrg']]==''){
           $dtMasuk[$param['kdBrg']]=0;
       }
       if($dtKeluar[$param['kdBrg']]==''){
           $dtKeluar[$param['kdBrg']]=0;
       }
       if($saldoAKhir<0){
           $saldoAKhir2=($dtMasuk[$param['kdBrg']]+$drSalAwal[$param['kdBrg']]+$dtPmbnding[$param['kdBrg']])-$dtKeluar[$param['kdBrg']];
           $srekal="update ".$dbname.".log_5saldobulanan set
                saldoakhirqty='".$saldoAKhir2."',
                nilaisaldoakhir='".($saldoAKhir2*$drHrgRata[$param['kdBrg']])."',
                qtymasuk='".$dtMasuk[$param['kdBrg']]."',
                qtymasukxharga='".($dtMasuk[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."',
                qtykeluar='".$dtKeluar[$param['kdBrg']]."',
                qtykeluarxharga='".($dtKeluar[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."',
                saldoawalqty='".($dtPmbnding[$param['kdBrg']]+$drSalAwal[$param['kdBrg']])."',
                nilaisaldoawal='".(($dtPmbnding[$param['kdBrg']]+$drSalAwal[$param['kdBrg']])*$dtHrgRata[$param['kdBrg']])."'
                where kodegudang='".$param['gdngId']."' and kodebarang='".$param['kdBrg']."' and periode='".$param['periodeGdng']."'";
            //exit("error:".$srekal);
           if(!mysql_query($srekal)){
               exit("error:\n Rekalkulasi tidak berhasil".$srekal);
           }
           $saldoAKhir=$saldoAKhir2;   
       }else       
       $srekal="update ".$dbname.".log_5saldobulanan set
                saldoakhirqty='".$saldoAKhir."',
                nilaisaldoakhir='".($saldoAKhir*$drHrgRata[$param['kdBrg']])."',
                qtymasuk='".$dtMasuk[$param['kdBrg']]."',
                qtymasukxharga='".($dtMasuk[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."',
                qtykeluar='".$dtKeluar[$param['kdBrg']]."',
                qtykeluarxharga='".($dtKeluar[$param['kdBrg']]*$drHrgRata[$param['kdBrg']])."'
                where kodegudang='".$param['gdngId']."' and kodebarang='".$param['kdBrg']."' and periode='".$param['periodeGdng']."'";
       //exit("error:".$srekal);
       if(!mysql_query($srekal)){
           exit("error:\n Rekalkulasi tidak berhasil".$srekal);
       }

       echo $saldoAKhir."####".$dtMasuk[$param['kdBrg']]."####".$dtKeluar[$param['kdBrg']]."####".$drSalAwal[$param['kdBrg']];
       break;
}
?>
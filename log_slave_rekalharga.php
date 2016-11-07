<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tmpPeriod = explode('-',$param['periode']);
$tahunbulan = implode("",$tmpPeriod);
if($tmpPeriod[1]==12) {
    $bulanLanjut = 1;
    $tahunLanjut = $tmpPeriod[0]+1;
} else {
    $bulanLanjut = $tmpPeriod[1]+1;
    $tahunLanjut = $tmpPeriod[0];
}

$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$param['periode']."' and kodeorg='".$param['kodegudang']."'";
$res=mysql_query($str);
$currstart='';
$currend='';
while($bar=mysql_fetch_object($res))
{
    $currstart=$bar->tanggalmulai;
    $currend=$bar->tanggalsampai;
}
if($currstart=='' or $currend=='') {
    exit('Error: '.$_SESSION['lang']['accperiodwrong'].' '.$param['kodegudang']);
}


if ($param['metode']=='getList'){
    $sawal=Array();
    $mtdebet=Array();
    $mtkredit=Array();
    $salak=Array();
    $nmtipe=array('1'=>'Masuk','2'=>'Retur','3'=>'Masuk','5'=>'Keluar','6'=>'Retur','7'=>'Mutasi');
    $nmBarang=makeOption($dbname, "log_5masterbarang", "kodebarang,namabarang");
    if ($param['kodebarang']!='')
        $whr=" and kodebarang=".$param['kodebarang'];
    #ambil saldo awal bulan berjalan
    $str="select saldoawalqty,kodebarang,hargaratasaldoawal from ".$dbname.".log_5saldobulanan
          where periode='".$param['periode']."' and kodegudang='".$param['kodegudang']."'".$whr;
    //exit('error'.$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_array($res))
    {
        $sawal[$bar[1]]=$bar[0];
        $haratawal[$bar[1]]=$bar[2];
        $mtkredit[$bar[1]]=0;
        $salak[$bar[1]]=0;
    }
    #ambil transaksi transaksi bln berjalan
    $str="select kodebarang,tipetransaksi,notransaksi,jumlah,hargasatuan,hargarata,keterangan,nopo,nopp,notransaksireferensi from ".$dbname.".log_transaksi_vw 
          where tanggal between '".$currstart."' and '".$currend."' and kodegudang='".$param['kodegudang']."'
          and statussaldo in (1,2) ".$whr." order by kodebarang,waktutransaksi";
    //exit('error'.$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res)){
        $trx[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->notransaksi;
        $tipe[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->tipetransaksi;
        $nopo[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->nopo;
        $nopp[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->nopp;
        $notrx[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->notransaksireferensi;
        $jumlah[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->jumlah;
        $keterangan[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->keterangan;
        $harga[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->hargasatuan;
        $hargarata[$bar->kodebarang][$bar->notransaksi.$bar->nopo.$bar->nopp]=$bar->hargarata;
    }
//    echo"<pre>";
//    print_r($trx);
//    echo"</pre>";
//    #ambil nojurnal yang terkait
//    $str="select distinct kodebarang,noreferensi,nojurnal from ".$dbname.".keu_jurnaldt_vw
//          where nojurnal LIKE '%/INVK1/%' and tanggal between '".$currstart."' and '".$currend."' and noreferensi like '%".$param['kodegudang']."'".$whr;
//    //exit('error'.$str);
//    $res=mysql_query($str);
//    while($bar=mysql_fetch_array($res))
//    {
//        $nojurnal[$bar[1].$bar[2]]=$bar[0];
//    }

    echo"<button class=mybutton onclick=prosesRekalkulasi(1) id=btnproses>Process</button>&nbsp;
         <i>(Hanya untuk Data yang berwarna merah)</i><br>
            <div style='width:1200px;display:fixed;'>
            <table class=sortable cellspacing=1 border=0 style='width:100%'>
            <thead>
            <tr class=rowheader>
            <td rowspan=2 align=center width=35>Tipe</td>
            <td rowspan=2 align=center width=130>No Transaksi</td>
            <td rowspan=2 align=center width=300>Keterangan</td>
            <td rowspan=2 align=center width=35>Qty</td>
            <td rowspan=2 align=center width=60>Saldo</td>
            <td rowspan=2 align=center width=60>Harga Satuan</td>
            <td colspan=2 align=center>Sebelum Rekalkulasi</td>
            <td colspan=2 align=center>Setelah Rekalkulasi</td>
            </tr>
            <tr class=rowheader>
            <td align=center width=70>Harga Rata</td>
            <td align=center width=100>Nilai Persediaan</td>
            <td align=center width=70>Harga Rata</td>
            <td align=center width=100>Nilai Persediaan</td>
            </tr>
            </thead>
            <tbody></tbody></table></div>";

    $adasalah=false;
    echo "<div style='overflow:scroll;height:320px;width:1215px;display:fixed;'>
         <table cellspacing=1 border=0 class=sortable style='width:100%'>
         <thead class=rowheader></thead><tbody>";

    $nobrg=0;
    foreach($sawal as $brg=>$val)
    {
        if (array_count_values($trx[$brg])>0){
            $nobrg+=1;
            echo"<tr class=rowheader><td colspan=4>".$brg." - ".$nmBarang[$brg]."</td>";
            echo"<td align=right>".number_format($sawal[$brg],2,'.','')."</td><td>
                 <td align=right>".number_format($haratawal[$brg],2,'.','')."</td><td></td><td></td><td></td>
                 <input type=hidden id=brg".$nobrg." value='".$brg."' />";
        }
        $no=0;
        foreach ($trx[$brg] as $trx2=>$val2){
            $no+=1;
            if ($no==1) {
                $nilaihitung=$sawal[$brg]*$haratawal[$brg];
                $saldo=$sawal[$brg];
                $harathitung=($saldo==0)?0:$haratawal[$brg];
            }
            if ($nmtipe[$tipe[$brg][$trx2]]=='Masuk'){
                $saldo=$saldo+$jumlah[$brg][$trx2];
                $totalnilai=$nilaihitung+($jumlah[$brg][$trx2]*$harga[$brg][$trx2]);
                $harathitung=$totalnilai/$saldo;
            } else { // Barang Keluar,Mutasi dan Retur gunakan harga rata-rata terakhir
                $saldo=$saldo-$jumlah[$brg][$trx2];
            }
            $harathitung=round($harathitung,2);
            $nilaihitung=$saldo*$harathitung;
            
            if (round($hargarata[$brg][$trx2],2)!=round($harathitung,2)){
                $merahharat="style=\"background-color:red; color:#fff;\"";
                $adasalah=true;
            } else {
                $merahharat="";
            }
            if (round(($jumlah[$brg][$trx2]*$hargarata[$brg][$trx2]),2)!=round($jumlah[$brg][$trx2]*$harathitung,2)){
                $merahnilai="style=\"background-color:red; color:#fff;\"";
                $adasalah=true;
            } else {
                $merahnilai="";
            }
            if ($nmtipe[$tipe[$brg][$trx2]]=='Masuk'){
                $tanda="style=\"background-color:green; color:#fff;\"";
            } else {
                $tanda="";
            }
            echo"<tr class=rowcontent id='row".$brg.$no."' ".$tanda.">
            <td width=35 id='tipe".$brg.$no."'>".$nmtipe[$tipe[$brg][$trx2]]."</td>
            <td width=130 id='notransaksi".$brg.$no."'>".$val2."</td>
            <td width=300>".$keterangan[$brg][$trx2]."</td>
            <td align=right width=35>".$jumlah[$brg][$trx2]."</td>
            <td align=right width=60 id='saldo".$brg.$no."'>".number_format($saldo,2,'.','')."</td>
            <td align=right width=60>".number_format($harga[$brg][$trx2],2,'.','')."</td>
            <td align=right width=70 id='harat".$brg.$no."'>".number_format($hargarata[$brg][$trx2],2,'.','')."</td>
            <td align=right width=100 id='nilai".$brg.$no."'>".number_format(($jumlah[$brg][$trx2]*$hargarata[$brg][$trx2]),2,'.','')."</td>
            <td align=right width=70 id='harat2".$brg.$no."' ".$merahharat.">".number_format($harathitung,2,'.','')."</td>
            <td align=right width=100 id='nilai2".$brg.$no."' ".$merahnilai.">".number_format($jumlah[$brg][$trx2]*$harathitung,2,'.','')."</td>
            <input type=hidden id=nopo".$brg.$no." value='".$nopo[$brg][$trx2]."' />
            <input type=hidden id=nopp".$brg.$no." value='".$nopp[$brg][$trx2]."' />
            <input type=hidden id=notrx".$brg.$no." value='".$notrx[$brg][$trx2]."' />
            </tr>";
        }
        
//        echo "<input type=hidden id=totalbrg".$brg." value='".$no."' />";

    }
        
    echo"</tbody><tfoot></tfoot></table></div>####";
    if ($adasalah) echo "salah";
} else if ($param['metode']=='updateSaldo'){ // Diakhir proses untuk mengupdate harga dan saldo stok pada gudangnya
    $srekal="update ".$dbname.".log_5saldobulanan set
             saldoakhirqty='".$param['saldo']."',
             hargarata='".$param['harat2']."',
             nilaisaldoakhir='".($param['saldo']*$param['harat2'])."',
             qtymasukxharga=qtymasuk*".$param['harat2'].",
             qtykeluarxharga=qtykeluar*".$param['harat2']."
             where kodegudang='".$param['kodegudang']."' and kodebarang='".$param['kodebarang']."' and periode='".$param['periode']."'";
    if(!mysql_query($srekal)){
        exit("error:\n Update saldo bulan ini tidak berhasil___".$srekal);
    }else{
        //cek saldo bulan berikutnya lalu update saldo awalnya
        $cekNextSaldo="select periode from ".$dbname.".log_5saldobulanan where 
                 kodegudang='".$param['kodegudang']."' and kodebarang='".$param['kodebarang']."'
                 and periode>'".$param['periode']."' limit 1";
        $resx=mysql_query($cekNextSaldo);
        while($barx=mysql_fetch_object($resx)){
            if ($barx->periode=='2014-12'){
                // Khusus Des 2014 tidak boleh karena sudah CUT OFF
            } else {
                $updateNextSaldo="update ".$dbname.".log_5saldobulanan set 
                    saldoawalqty=".$param['saldo'].", 
                    hargaratasaldoawal=".$param['harat2'].",
                    nilaisaldoawal=".($param['saldo']*$param['harat2'])."
                    where kodegudang='".$param['kodegudang']."' and kodebarang='".$param['kodebarang']."'
                    and periode='".$barx->periode."'";
                if(!mysql_query($updateNextSaldo)){
                    exit("error:\n Update saldo bulan berikutnya tidak berhasil __".$updateNextSaldo);
                }
            }
        }
    }
} else { // Update hanya data yang salah
    if (round($param['harat'],2)==round($param['harat2'],2) and round($param['nilai'],2)==round($param['nilai2'],2)){
        //exit('error:Data sudah sama');
    } else {
        // Update data hargarata pada log_transaksidt
        $temp="update ".$dbname.".log_transaksidt 
             set hargarata=".$param['harat2']."
             where notransaksi='".$param['notransaksi']."'
             and kodebarang='".$param['kodebarang']."' and nopp='".$param['nopp']."';";
        if(!mysql_query($temp))
        {
            exit("Error proses rekalkulasi ".mysql_error($conn));
        }   
        // Khusus mutasi, update data hargasatuan pada transaksi penerimaannya
        if ($param['tipe']=='Mutasi' and trim($param['notrx'])!='')
        $temp="update ".$dbname.".log_transaksidt 
             set hargasatuan=".$param['harat2']."
             where notransaksi='".$param['notrx']."'
             and kodebarang='".$param['kodebarang']."' and nopp='".$param['nopp']."' and nopo='".$param['nopo']."';";
        if(!mysql_query($temp))
        {
            exit("Error proses rekalkulasi mutasi ".mysql_error($conn));
        }   
//        // Update data harga persediaan pada jurnalnya (Header)
//           $temp2="update ".$dbname.".keu_jurnalht 
//                set totaldebet=".$param['nilai2'].",totalkredit=".($param['nilai2']*-1)." where nojurnal in 
//                (select nojurnal from ".$dbname.".keu_jurnaldt_vw where noreferensi='".$param['notransaksi']."' and kodebarang='".$param['kodebarang']."')";
//           if(!mysql_query($temp2))
//           {
//               exit("Error proses rekalkulasi ".mysql_error($conn));
//           }   
        // Update data harga persediaan pada detail jurnalnya (Debet dan kredit)
           $temp2="update ".$dbname.".keu_jurnaldt 
                set jumlah=".$param['nilai2']."
                where noreferensi='".$param['notransaksi']."'
                and kodebarang='".$param['kodebarang']."' and jumlah>0";
           if(!mysql_query($temp2))
           {
               exit("Error proses rekalkulasi jurnal ".mysql_error($conn));
           }   
           $temp2="update ".$dbname.".keu_jurnaldt 
                set jumlah=".($param['nilai2']*-1)."
                where noreferensi='".$param['notransaksi']."'
                and kodebarang='".$param['kodebarang']."' and jumlah<0";
           if(!mysql_query($temp2))
           {
               exit("Error proses rekalkulasi jurnal ".mysql_error($conn));
           }   
    }
}
?>
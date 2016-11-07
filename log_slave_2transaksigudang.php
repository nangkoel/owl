<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
    $unit=$_POST['unit'];
    $periode=$_POST['periode'];
    $jenis=$_POST['jenis'];
    $kodebarang=$_POST['kodebarang'];

//echo "unit: ".$unit." periode: ".$periode." jenis: ".$jenis." kodebarang: ".$kodebarang;
    $kamusjenis['0']='Mutasi dalam perjalanan';
    $kamusjenis['1']='Penerimaan';
    $kamusjenis['2']='Pengembalian pengeluaran';
    $kamusjenis['3']='Penerimaan mutasi';
    $kamusjenis['5']='Pengeluaran';
    $kamusjenis['6']='Pengembalian penerimaan';
    $kamusjenis['7']='Pengeluaran mutasi';

if($unit==''){
    echo "Warning: Period is missing"; exit;
}
if($periode==''){
    echo "Warning: Period is missing"; exit;
}
if($jenis==''){
    echo "Warning: trancstion type is missing"; exit;
}

if($jenis=='9')$jenis='';
$tipetransaksi = "a.tipetransaksi like '%".$jenis."%'";

$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
    where periode ='".$periode."' and kodeorg='".$unit."'";
if($unit=='sumatera')
    $str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
        where periode ='".$periode."' and kodeorg in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')";
if($unit=='kalimantan')
    $str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
        where periode ='".$periode."' and kodeorg in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $tanggalmulai=$bar->tanggalmulai;
    $tanggalsampai=$bar->tanggalsampai;
}

if($kodebarang==''){
    if($unit=='sumatera'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, 
        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')
        order by a.tanggal";
    }
    else if($unit=='kalimantan'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, 
        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')
        order by a.tanggal";
    }
    else{
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' 
        and ".$tipetransaksi." and a.kodegudang = '".$unit."'
        order by a.tanggal";
    }
}
else{
    if($unit=='sumatera'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi." 
        and a.kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10') and a.kodebarang = '".$kodebarang."'
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang in ('MRKE10','SKSE10','SOGM20','SSRO21','WKNE10')
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";
    }
    else if($unit=='kalimantan'){
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi."
        and a.kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10') and a.kodebarang = '".$kodebarang."'
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang in ('SBME10','SBNE10','SMLE10','SMTE10','SSGE10','STLE10')
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";
    }
    else{
    $str="select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, 
        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, a.keterangan, a.notransaksireferensi 
        from ".$dbname.".log_transaksi_vw a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang  
        left join ".$dbname.".log_5supplier c on a.idsupplier=c.supplierid  
        left join ".$dbname.".log_transaksiht d on a.notransaksi=d.notransaksi  
        where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and ".$tipetransaksi."
        and a.kodegudang = '".$unit."' and a.kodebarang = '".$kodebarang."' 
        order by a.tanggal";
    $str22="select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ".$dbname.".log_5saldobulanan where kodegudang = '".$unit."'
        and kodebarang = '".$kodebarang."' and periode = '".$periode."'";                
    }
$res22=mysql_query($str22);
if(mysql_num_rows($res22)>0)
while($bar22=mysql_fetch_object($res22))
{    
    $saldoawalqty=$bar22->saldoawalqty;
    $hargaratasaldoawal=$bar22->hargaratasaldoawal;
    $nilaisaldoawal=$bar22->nilaisaldoawal;
}

}

$str44="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang where kodebarang = '".$kodebarang."'";                
$res44=mysql_query($str44);
if(mysql_num_rows($res44)>0)
while($bar44=mysql_fetch_object($res44))
{    
    $namabarang=$bar44->namabarang;
    $satuan=$bar44->satuan;
}

$res=mysql_query($str);
if(mysql_num_rows($res)<1)
{
    echo"<tr class=rowcontent><td colspan=14>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
    echo"<thead><tr>
        <td align=center>No.</td>";
        echo"<td align=center>".$_SESSION['lang']['tipetransaksi']."</td>";
        echo"<td align=center>".$_SESSION['lang']['tanggal']."</td>";
        echo"<td align=center>".$_SESSION['lang']['kodebarang']."</td>";
        echo"<td align=center>".$_SESSION['lang']['namabarang']."</td>";
        if($jenis==''){
            echo"<td align=center>".$_SESSION['lang']['masuk']."</td>";
            echo"<td align=center>".$_SESSION['lang']['keluar']."</td>";
            echo"<td align=center>".$_SESSION['lang']['saldo']."</td>";
        }else{
            echo"<td align=center>".$_SESSION['lang']['jumlah']."</td>";
        }
        echo"<td align=center>".$_SESSION['lang']['satuan']."</td>";
        if($jenis==''){
            
        }else{
            echo"<td align=center>".$_SESSION['lang']['hargasatuan']."</td>";
            echo"<td align=center>".$_SESSION['lang']['totalharga']."</td>";
        }
        if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td align=center>".$_SESSION['lang']['nopo']."</td>";
        if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td align=center>".$_SESSION['lang']['supplier']."</td>";
        if($jenis=='')echo"<td align=center>".$_SESSION['lang']['tujuan']."/".$_SESSION['lang']['sumber']."</td>";
        if($jenis=='7')echo"<td align=center>".$_SESSION['lang']['tujuan']."</td>";
        if(($jenis=='5')or($jenis=='6'))echo"<td align=center>".$_SESSION['lang']['kodeblok']."</td>";
        if(($jenis=='5')or($jenis=='6'))echo"<td align=center>".$_SESSION['lang']['kodevhc']."</td>";
        echo"<td align=center>".$_SESSION['lang']['notransaksi']."</td>";
        echo"<td align=center>".$_SESSION['lang']['keterangan']."</td>";
        echo"<td align=center>".$_SESSION['lang']['noreferensi']."</td>";
    echo"</tr></thead><tbody>";  
        if($jenis==''){
        $no=1;
        $saldo=$saldoawalqty;
        $masuk=0;
        $keluar=0;
        echo"<tr class=rowcontent>
            <td align=right>".$no."</td>";
            echo"<td>Saldo Awal</td>";
            echo"<td>".tanggalnormal($periode."-01")."</td>";
            echo"<td>".$kodebarang."</td>";
            echo"<td nowrap>".$namabarang."</td>";
            if($saldoawalqty>=0){
                $masuk=$saldoawalqty;
                $totmas+=$masuk;
            }
            else{
                $keluar=$saldoawalqty*(-1);
                $totkel+=$keluar;
            }
            echo"<td align=right>".number_format($masuk,2)."</td>";
            echo"<td align=right>".number_format($keluar,2)."</td>";
            echo"<td align=right>".number_format($saldoawalqty,2)."</td>";
            echo"<td>".$satuan."</td>";
            if($jenis==''){
                
            }else{
                echo"<td align=right>".number_format($hargaratasaldoawal)."</td>";
                echo"<td align=right>".number_format($nilaisaldoawal)."</td>";
            }
            echo"<td></td>";
            echo"<td></td>";
            echo"<td></td>";
            echo"<td></td>";
        echo"</tr>";
        }    
        // 0 = Mutasi dalam perjalanan
        // 1 = Masuk
        // 2 = Pengembalian pengeluaran
        // 3 = Penerimaan mutasi
        // 5 = Pengeluaran
        // 6 = Pengembalian penerimaan
        // 7 = Pengeluaran mutasi
        while($bar=mysql_fetch_object($res))
        {
            $no+=1; $total=0;
            if(($jenis=='0')or($jenis=='1')or($jenis=='3')) 
                $total=$bar->jumlah*$bar->hargasatuan; 
            else
                $total=$bar->jumlah*$bar->hargarata;
            echo"<tr class=rowcontent>
            <td align=right>".$no."</td>";
            echo"<td>".$kamusjenis[$bar->tipetransaksi]."</td>";
            echo"<td>".tanggalnormal($bar->tanggal)."</td>";
            echo"<td>".$bar->kodebarang."</td>";
            echo"<td nowrap>".$bar->namabarang."</td>";
            if($jenis==''){
                $masuk=0;
                $keluar=0;
                if($bar->tipetransaksi<4)$masuk=$bar->jumlah;
                if($bar->tipetransaksi>4)$keluar=$bar->jumlah;
                $totmas+=$masuk;
                $totkel+=$keluar;
                echo"<td align=right>".number_format($masuk,2)."</td>";
                echo"<td align=right>".number_format($keluar,2)."</td>";
                $saldo+=$masuk-$keluar;
                echo"<td align=right>".number_format($saldo,2)."</td>";
            }else{
                echo"<td align=right>".number_format($bar->jumlah,2)."</td>";
            }
            echo"<td>".$bar->satuan."</td>";
            if($jenis==''){
                
            }else{
                if(($jenis=='0')or($jenis=='1')or($jenis=='3'))
                    echo"<td align=right>".number_format($bar->hargasatuan)."</td>"; 
                else
                    echo"<td align=right>".number_format($bar->hargarata)."</td>";
                echo"<td align=right>".number_format($total)."</td>";
            }
            if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td nowrap>".$bar->nopo."</td>";
            if(($jenis=='0')or($jenis=='1')or($jenis=='2')or($jenis=='3'))echo"<td nowrap>".$bar->namasupplier."</td>";
            if($jenis=='7')echo"<td>".$bar->gudangx."</td>";
            if(($jenis=='5')or($jenis=='6'))echo"<td>".$bar->kodeblok."</td>";
            if(($jenis=='5')or($jenis=='6'))echo"<td>".$bar->kodemesin."</td>";
            if($jenis==''){
                if($bar->tipetransaksi<4)$keluarmasuk=$bar->nopo." ".$bar->namasupplier;
                if($bar->tipetransaksi>4)$keluarmasuk=$bar->kodeblok." ".$bar->kodemesin." ".$bar->gudangx;
                echo"<td nowrap>".$keluarmasuk."</td>";
            }
            echo"<td nowrap>".$bar->notransaksi."</td>";
            echo"<td nowrap>".$bar->keterangan."</td>";
            echo"<td nowrap>".$bar->notransaksireferensi."</td>";
            echo"</tr>";
        } 
        if($jenis==''){
        echo"<tr class=rowcontent>
            <td align=center colspan=5>Total</td>";
            echo"<td align=right>".number_format($totmas,2)."</td>";
            echo"<td align=right>".number_format($totkel,2)."</td>";
            echo"<td align=right>".number_format($saldo,2)."</td>";
            echo"<td colspan=5>".$satuan."</td>";
        echo"</tr>";
        }    
    echo"</tbody<tfoot></tfoot>";

}
?>
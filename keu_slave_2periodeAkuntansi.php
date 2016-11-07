<?php 
// file creator: dhyaz aug 3, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kodept=$_POST['kodept'];
$kodeunit=$_POST['kodeunit'];

//kamus nama unit
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where tipe in('KEBUN','PABRIK','GUDANG','GUDANGTEMP','TRAKSI','KANWIL') or (tipe='HOLDING' and length(kodeorganisasi)=4)
    order by kodeorganisasi";
$res=mysql_query($str);
$kamus=array();
while($bar=mysql_fetch_object($res))
{
    $kamus[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

//ambil anak-anak
$jumlahanak=0;
$str="select kodeorganisasi,namaalias from ".$dbname.".organisasi
    where LEFT(induk,4) = '".$kodeunit."' and tipe like 'gudang%'
    order by kodeorganisasi";
$res=mysql_query($str);
$anak=array();
while($bar=mysql_fetch_object($res))
{
    $anak[$bar->kodeorganisasi]=$bar->kodeorganisasi;
    $nmanak[$bar->kodeorganisasi]=$bar->namaalias;
    $jumlahanak+=1;
}

//ambil unit holding
$jumlahunit=0;
$str="select kodeorganisasi,namaalias from ".$dbname.".organisasi 
    where induk='".$kodept."' and kodeorganisasi like '".$kodeunit."%' and tipe = 'HOLDING'
    order by tipe desc";
$res=mysql_query($str);
$unit=array();
while($bar=mysql_fetch_object($res))
{
    $unit[$bar->kodeorganisasi]=$bar->kodeorganisasi;
    $nmunit[$bar->kodeorganisasi]=$bar->namaalias;
    $jumlahunit+=1;
}
$str="select kodeorganisasi,namaalias from ".$dbname.".organisasi 
    where induk='".$kodept."' and kodeorganisasi like '".$kodeunit."%' and tipe != 'HOLDING'
    order by tipe desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $unit[$bar->kodeorganisasi]=$bar->kodeorganisasi;
    $nmunit[$bar->kodeorganisasi]=$bar->namaalias;
    $jumlahunit+=1;
}

// ambil data
$arr=Array();
$str1="select * from ".$dbname.".keu_setup_watu_tutup order by periode desc, kodeorg";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
    $arr[$bar1->periode][$bar1->kodeorg]['username']=$bar1->username;
    $arr[$bar1->periode][$bar1->kodeorg]['waktu']=$bar1->waktu;
}

$no=1;
$str="select * from ".$dbname.".setup_periodeakuntansi order by periode desc, kodeorg";
$res=mysql_query($str);
while($baris= mysql_fetch_object($res))
{
    if ($kodept=='HIP'){
        if (substr($baris->periode,0,4)==2014 and substr($baris->periode,5)<04) continue;
    } else {
        if (substr($baris->periode,0,4)==2014 and substr($baris->periode,5)<06) continue;
    }
    $periode[$baris->periode]=$baris->periode;    
    $mulai[$baris->periode][$baris->kodeorg]=$baris->tanggalmulai;
    $sampai[$baris->periode][$baris->kodeorg]=$baris->tanggalsampai;
    $tutup[$baris->periode][$baris->kodeorg]=$baris->tutupbuku;
    $waktu[$baris->periode][$baris->kodeorg]=$arr[$baris->periode][$baris->kodeorg]['waktu'];
    $pelaku[$baris->periode][$baris->kodeorg]=$arr[$baris->periode][$baris->kodeorg]['username'];
}

if(!empty($periode))foreach($periode as $per){
    if(!empty($unit))foreach($unit as $uni){
        if ($mulai[$per][$uni]==''){
            if (substr($per,5,2)-1==0)
                $perlalu=(substr($per,0,4)-1)."-12";
            else
                $perlalu=substr($per,0,4)."-".addZero(substr($per,5,2)-1,2);
            if ($sampai[$perlalu][$uni]!=''){
                $mulai[$per][$uni]=nambahHari($sampai[$perlalu][$uni],1,1);
                $sampai[$per][$uni]=date('Y-m-d');
            }
        }
        
        // kasbank total and total posted
        $str="select count(notransaksi) as jumlah from ".$dbname.".keu_kasbankht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."'";
        //echo $str;
        $res=fetchData($str);
        $kasbank[$per][$uni]=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".keu_kasbankht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."' and posting=1";
        $res=fetchData($str);
        $kasbankp[$per][$uni]=$res[0]['jumlah'];
        
        // bkm total and total posted
        $str="select count(notransaksi) as jumlah from ".$dbname.".kebun_aktifitas where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."'";
        $res=fetchData($str);
        $bkm[$per][$uni]=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".kebun_aktifitas where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."' and jurnal=1";
        $res=fetchData($str);
        $bkmp[$per][$uni]=$res[0]['jumlah'];

        // traksi running total and total posted
        $str="select count(notransaksi) as jumlah from ".$dbname.".vhc_runht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."'";
        $res=fetchData($str);
        $traksi[$per][$uni]=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".vhc_runht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."' and posting=1";
        $res=fetchData($str);
        $traksip[$per][$uni]=$res[0]['jumlah'];
        // traksi service total and total posted
        $str="select count(notransaksi) as jumlah from ".$dbname.".vhc_penggantianht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."'";
        $res=fetchData($str);
        $traksi[$per][$uni]+=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".vhc_penggantianht where kodeorg='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."' and posting=1";
        $res=fetchData($str);
        $traksip[$per][$uni]+=$res[0]['jumlah'];

        // bapp total and total posted
        $str="select count(notransaksi) as jumlah from ".$dbname.".log_baspk where substr(kodeblok,1,4)='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."'";
        $res=fetchData($str);
        $bapp[$per][$uni]=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".log_baspk where substr(kodeblok,1,4)='".$uni."' 
                and tanggal between '".$mulai[$per][$uni]."' and '".$sampai[$per][$uni]."' and statusjurnal=1";
        $res=fetchData($str);
        $bappp[$per][$uni]=$res[0]['jumlah'];
    }
    if(!empty($anak))foreach($anak as $data){
        // gudang total
        $str="select count(notransaksi) as jumlah from ".$dbname.".log_transaksiht where kodegudang='".$data."' 
                and tanggal between '".$mulai[$per][$data]."' and '".$sampai[$per][$data]."'";
        $res=fetchData($str);
        $gudang[$per][$data]=$res[0]['jumlah'];
        $str="select count(notransaksi) as jumlah from ".$dbname.".log_transaksiht where kodegudang='".$data."' 
                and tanggal between '".$mulai[$per][$data]."' and '".$sampai[$per][$data]."' and post=1";
        $res=fetchData($str);
        $gudangp[$per][$data]=$res[0]['jumlah'];
    }
}


//echo "<pre>";
//print_r($waktu);
//echo "</pre>";

echo"<table class=sortable cellspacing=1 border=0 width=100%>
    <thead>
    <tr>
        <td align=center rowspan=2>".$_SESSION['lang']['periode']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['namaorganisasi']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['status']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['waktu']."</td>
        <td align=center rowspan=2>".$_SESSION['lang']['nama']."</td>  
        <td align=center rowspan=2>".$_SESSION['lang']['kasbank']." (posted)</td>  
        <td align=center rowspan=2>".$_SESSION['lang']['traksi']." (posted)</td>  
        <td align=center rowspan=2>BAPP (posted)</td>  
        <td align=center rowspan=2>BKM (posted)</td>";  
if ($jumlahanak>0){
        $jumlahanak=$jumlahanak*2;
        echo"<td align=center colspan=".$jumlahanak.">".strtoupper($_SESSION['lang']['gudang'])."</td></tr><tr>";
}
if(!empty($anak))foreach($anak as $data){
    $nmgudang=str_replace("GUDANG", "", $kamus[$data]);
    echo"<td align=center colspan=2 title=\"".$kamus[$data]."\">".$nmgudang."</td>";
}
    echo"</tr>  
    </thead>
    <tbody>";

if(!empty($periode))foreach($periode as $per){
    $tamper=true;
    if(!empty($unit))foreach($unit as $uni){
        if($tamper){
            $tampil=$per;
        }else{
            $tampil='';
        }
        $tamtut='';
        $warna="<tr class=rowcontent>";
        if($tutup[$per][$uni]=='1'){ $tamtut='closed'; }
        if($tutup[$per][$uni]=='0'){ $tamtut='__active'; $warna="<tr bgcolor=lightgreen>"; }
        echo $warna;
        if($tamper)echo"<td align=center rowspan=".$jumlahunit." nowrap>".$tampil."</td>";
        echo"<td>".$nmunit[$uni]."</td>";
        echo"<td>".$tamtut."</td>";
        echo"<td>".$waktu[$per][$uni]."</td>";
        echo"<td>".$pelaku[$per][$uni]."</td>";
        @$persen=$kasbankp[$per][$uni]*100/$kasbank[$per][$uni];
        echo"<td align=right nowrap>".$kasbank[$per][$uni]." (".number_format($persen)."%)</td>";
        @$persen=$traksip[$per][$uni]*100/$traksi[$per][$uni];
        echo"<td align=right nowrap>".$traksi[$per][$uni]." (".number_format($persen)."%)</td>";
        @$persen=$bappp[$per][$uni]*100/$bapp[$per][$uni];
        echo"<td align=right nowrap>".$bapp[$per][$uni]." (".number_format($persen)."%)</td>";
        @$persen=$bkmp[$per][$uni]*100/$bkm[$per][$uni];
        echo"<td align=right nowrap>".$bkm[$per][$uni]." (".number_format($persen)."%)</td>";
if(!empty($anak))foreach($anak as $data){
//    echo"<td align=center>".$data."</td>";
        $tamtud='';
        if($tutup[$per][$data]=='1')$tamtud='closed';
        if($tutup[$per][$data]=='0')$tamtud='__active';
        echo"<td>".$tamtud."</td>";
        @$persen=$gudangp[$per][$data]*100/$gudang[$per][$data];
        echo"<td align=right nowrap>".$gudang[$per][$data]." (".number_format($persen)."%)</td>";
        
}
        echo"</tr>";
        $tamper=false;
    }        
}
    echo"</tbody>
    <tfoot>
    </tfoot>		 
    </table>
";
    
?>

<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
require_once('config/connection.php');

$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());

$updatetime=date('d M Y H:i:s', time());

//                $hariini = '2013-07-08';
//                $bulan = '12';
//                $tahun = '2012';
 
$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt-86400);

//ambil query untuk data karyawan
$skaryawan="select a.tanggallahir, a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
    where a.lokasitugas like '%HO' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar='0000-00-00')
    order by namakaryawan asc";    
$res=mysql_query($skaryawan);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
    $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
    $karyawan[$bar->karyawanid]['lahir']=$bar->tanggallahir;
//    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
//    $jabakar[$kar['karyawanid']]=$kar['namajabatan'];
//    $bagikar[$kar['karyawanid']]=$kar['bagian'];
}  

// karyawan ijin
$str="SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.keperluan 
    FROM ".$dbname.".sdm_ijin a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
    WHERE substr(a.darijam,1,10) <= '".$hariini."' and substr(a.sampaijam,1,10) >= '".$hariini."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'
    ORDER BY a.darijam, a.sampaijam";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    if(substr($bar->lokasitugas,2,2)=='HO'){
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
    }    
//    $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
//    $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
    $presensi[$bar->karyawanid]['waktuijin']=$bar->daritanggal." - ".$bar->sampaitanggal;
    $presensi[$bar->karyawanid]['kehadiran']=$bar->jenisijin;
    $presensi[$bar->karyawanid]['keterangan']=$bar->keperluan;
}

//// karyawan cuti
//$str="SELECT a.karyawanid, a.daritanggal, a.sampaitanggal, a.keterangan, c.namakaryawan, c.lokasitugas FROM ".$dbname.".sdm_cutidt a
//    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
//    WHERE a.daritanggal <= '".$hariini."' and a.sampaitanggal >= '".$hariini."' order by a.daritanggal, a.sampaitanggal";
//$res=mysql_query($str);
//echo mysql_error($conn);
//while($bar=mysql_fetch_object($res))
//{
//    if(substr($bar->lokasitugas,2,2)=='HO'){
//        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
//        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
//    }    
////    $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
////    $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
//    $presensi[$bar->karyawanid]['waktucuti']=$bar->daritanggal." - ".$bar->sampaitanggal;
//    $presensi[$bar->karyawanid]['kehadiran']=$bar->keterangan;
//}

// karyawan dinas
$str="SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tugas1, a.tugas2, a.tugas3, c.namakaryawan, a.kodeorg FROM ".$dbname.".sdm_pjdinasht a
    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        
    WHERE a.tanggalperjalanan <= '".$hariini."' and a.tanggalkembali >= '".$hariini."' order by a.tanggalperjalanan, a.tanggalkembali";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    if($bar->kodeorg=='MJHO'){
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
    }    
//    $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
//    $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
    $presensi[$bar->karyawanid]['waktudinas']=$bar->tanggalperjalanan." - ".$bar->tanggalkembali;
    $presensi[$bar->karyawanid]['kehadiran']='DINAS';
    $presensi[$bar->karyawanid]['keterangan']=$bar->tugas1." ".$bar->tugas2." ".$bar->tugas3;
}

// karyawan masuk
$str="SELECT a.pin, a.scan_date, b.karyawanid, c.namakaryawan FROM ".$dbname.".att_log a
    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin
    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        
    WHERE scan_date like '".$hariini."%'  and scan_date < '".$hariini." 12:00:00'
    ORDER BY scan_date desc";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){
//        $karyawan[$bar->pin]['id']=$bar->pin;
//        $karyawan[$bar->pin]['nama']='_Belum Terdaftar: '.$bar->pin;
//        $presensi[$bar->pin]['waktumasuk']=$bar->scan_date;
//        $presensi[$bar->pin]['kehadiran']='MASUK';
    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['waktumasuk']=$bar->scan_date;
        $presensi[$bar->karyawanid]['kehadiran']='MASUK';
    }
}

// karyawan keluar
$str="SELECT a.pin, a.scan_date, b.karyawanid, c.namakaryawan FROM ".$dbname.".att_log a
    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin
    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        
    WHERE scan_date like '".$hariini."%' and scan_date >= '".$hariini." 12:00:00'
    ORDER BY scan_date asc";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    if(!isset($bar->karyawanid)){
//        $karyawan[$bar->pin]['id']=$bar->pin;
//        $karyawan[$bar->pin]['nama']='_Belum Terdaftar: '.$bar->pin;
//        $presensi[$bar->pin]['waktukeluar']=$bar->scan_date;
//        $presensi[$bar->pin]['kehadiran']='PULANG';
    }else{
        $karyawan[$bar->karyawanid]['id']=$bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama']=$bar->namakaryawan;
        $presensi[$bar->karyawanid]['waktukeluar']=$bar->scan_date;
        $presensi[$bar->karyawanid]['kehadiran']='PULANG';
    }
}

// ambil jumlah karyawan
$jumlahkaryawan=0;
if(!empty($karyawan))foreach($karyawan as $kar){
    $jumlahkaryawan+=1;
}

// sort berdasarkan nama
if(!empty($karyawan)) foreach($karyawan as $c=>$key) {
    $sort_nama[] = $key['nama'];
}
if(!empty($karyawan))array_multisort($sort_nama, SORT_ASC, $karyawan);

$qwe="PRESENSI HO ".$tanggal." = ".number_format($jumlahkaryawan)." orang";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <tr class=rowcontent>
    <td>".$qwe."</td>
    <td align=right width=1% nowrap>".$updatetime."</td>
    </tr>
    </table>";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <thead>
    <tr class=rowtitle>
        <td align=center rowspan=2 style='width:120px;'>Nama Karyawan</td>
        <td align=center style='width:80px;'>Kehadiran</td>
        <td align=center style='width:160px;'>Waktu</td>
        <td align=center style='width:120px;'>Keterangan</td>
    </tr>  
    </thead>
    <tbody></tbody></table>";

echo"<marquee height=120 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>
    <table class=sortable cellspacing=1 border=0 width=480px>
    <tbody>";

if(!empty($karyawan))foreach($karyawan as $kar){
    echo"<tr class=rowcontent>";
    if(substr($kar['lahir'],5,5)==substr($hariini,5,5)){
        $tahunini=substr($hariini,0,4);
        $tahunlahir=substr($kar['lahir'],0,4);
        $umur=$tahunini-$tahunlahir;
        echo"<td style='width:120px;'>".$kar['nama']." (".$umur.")</td>";
    }else{
        echo"<td style='width:120px;'>".$kar['nama']."</td>";
    }
    echo"<td style='width:80px;'>".$presensi[$kar['id']]['kehadiran']."</td>";
    
    $warning=false;
    $waktu='';
    if(($presensi[$kar['id']]['waktumasuk'])or($presensi[$kar['id']]['waktukeluar'])){
        $presensi[$kar['id']]['keterangan']='';
        $waktu=substr($presensi[$kar['id']]['waktumasuk'],11,8);
        if($presensi[$kar['id']]['waktukeluar'])$waktu.=" - ".substr($presensi[$kar['id']]['waktukeluar'],11,8);
        if(($hariini>='2013-07-10')and($hariini<='2013-08-08')){                // puasa 2013
            if($presensi[$kar['id']]['waktumasuk'])if(substr($presensi[$kar['id']]['waktumasuk'],11,5)>'07:30')$warning=true;
            if($presensi[$kar['id']]['waktukeluar'])if(substr($presensi[$kar['id']]['waktukeluar'],11,5)<'16:00')$warning=true;
        }else
        if($hariini=='2013-10-14'){                                             // idul adha 2013 -1
            if($presensi[$kar['id']]['waktumasuk'])if(substr($presensi[$kar['id']]['waktumasuk'],11,5)>'08:00')$warning=true;
            if($presensi[$kar['id']]['waktukeluar'])if(substr($presensi[$kar['id']]['waktukeluar'],11,5)<'15:00')$warning=true;
        }else{
            if($presensi[$kar['id']]['waktumasuk'])if(substr($presensi[$kar['id']]['waktumasuk'],11,5)>'08:00')$warning=true;
            if($presensi[$kar['id']]['waktukeluar'])if(substr($presensi[$kar['id']]['waktukeluar'],11,5)<'17:00')$warning=true;
        }
    }else
        if($presensi[$kar['id']]['waktudinas'])$waktu=$presensi[$kar['id']]['waktudinas'];
    else
        if($presensi[$kar['id']]['waktucuti'])$waktu=$presensi[$kar['id']]['waktucuti'];        
    else
        if($presensi[$kar['id']]['waktuijin'])$waktu=$presensi[$kar['id']]['waktuijin'];        
    
    if($warning)$waktu="<font color=red>".$waktu."</font>";
    
    echo"<td align=center style='width:160px;'>".$waktu."</td>";
    echo"<td style='width:120px;'>".substr($presensi[$kar['id']]['keterangan'],0,25)."</td>";
    echo"</tr>";
}

echo"</tbody>
    </table>
    * sumber data: fingerprint + OWL
    </marquee>";
?>
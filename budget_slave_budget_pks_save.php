<?php
// file creator: dhyaz sep 20, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tab=$_POST['tab'];
//save tab0
if($tab=='0'){
	
	//exit("Error:MASUK");

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $kodebudget0=$_POST['kodebudget0'];
    $jumlahpertahun0=$_POST['jumlahpertahun0'];
	
	

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$mesin."' and kodebudget ='".$kodebudget0."'";
    $res=mysql_query($str);
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebudget." ".$bar->rupiah." ";
    }
    if($hkef!=''){
        $hkef='Data sudah ada : '.$hkef;
        echo $hkef;
        exit;
    }
    $str="select * from ".$dbname.".bgt_kode
        where kodebudget = '".$kodebudget0."'";
    $res=mysql_query($str);
    $akun='';
    while($bar= mysql_fetch_object($res))
    {
        $akun=$bar->noakun;
    }

    if($akun==''){
        $akun="No Akun ".$kodebudget0." belum diset. Silakan diset terlebih dahulu.";
        echo $akun;
        exit;
    }
            
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `noakun` ,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$mesin."', '".$kodebudget0."', '".$akun."', '".$jumlahpertahun0."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
    
}












if($tab=='cekclose'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $lokasi=substr($_SESSION['empl']['lokasitugas'],0,4);

    $str="select * from ".$dbname.".bgt_budget
        where tutup = 1 and tipebudget = '".$tipebudget."' and kodebudget != 'UMUM' and tahunbudget ='".$tahunbudget."' and kodeorg like '".$lokasi."%' limit 0, 1";
    $res=mysql_query($str);
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.="Error: Data sudah ditutup.";
    }
    if($hkef!='')
       echo $hkef;
}

//tutup
if($tab=='tutup'){

    $pabrik=$_POST['pabrik'];
    $tahuntutup=$_POST['tahuntutup'];
    
        //proteksi sebaran harus benar
    $strx="select noakun,kodeorg
                FROM bgt_budget_detail where tahunbudget=".$tahuntutup." and kodeorg like '".$pabrik."%'
                and abs((rp01+rp02+rp03+rp04+rp05+rp06+rp07+rp08+rp09+rp10+rp11+rp12)-rupiah)>90";
    $res=mysql_query($strx);
    if(mysql_num_rows($res)>0){
        $cap="Sebaran masih salah untuk data dibawah:\n";
        while($bar=mysql_fetch_object($res)){
            $cap+="Noakun:".$bar->noakun." | Kegiatan:".$bar->kodeorg."\n";                      
        }
        exit(" Error: ".$cap);
    }
    //======================


    $str="update ".$dbname.".bgt_budget set tutup = '1'
        where tahunbudget = '".$tahuntutup."' and tipebudget ='MILL' and kodebudget != 'UMUM' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
    $res=mysql_query($str);
//echo $str;
    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
}

//save tab1
if($tab=='1'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $kodebudget1=$_POST['kodebudget1'];
    $jenis1=$_POST['jenis1'];
    $totalharga1=$_POST['totalharga1'];
    $kodebarang1=$_POST['kodebarang1'];
    $regional1=$_POST['regional1'];
    $jumlah1=$_POST['jumlah1'];
    $satuan1=$_POST['satuan1'];
    $anggaranKd=$_POST['anggaranKd'];
    if($anggaranKd=='')
    {
        exit("Error:".$_SESSION['lang']['kodeanggaran']." tidak boleh kosong");
    }

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and 
        kodebudget like 'M%' and tahunbudget ='".$tahunbudget."' 
        and kodeorg ='".$mesin."' and kodebarang ='".$kodebarang1."'
        and noakun='".$anggaranKd."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebarang." ".$bar->jumlah." ".$bar->satuanj;
    }
    if($hkef!=''){
        $hkef='Data sudah ada : '.$hkef;
        echo $hkef;
        exit;
    }
//    $str="select noakun from ".$dbname.".log_5klbarang
//        where kode = '".substr($kodebudget1,2,3)."'";
//    $res=mysql_query($str);
//    $akun='';
//    while($bar= mysql_fetch_object($res))
//    {
//        $akun=$bar->noakun;
//    }
//   if($akun==''){
//        $akun="No Akun ".$kodebudget0." belum diset. Silakan diset terlebih dahulu.";
//        echo $akun;
//        exit;
//    }
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `noakun`,
    `regional` ,
    `kodebarang` ,
    `jumlah` ,
    `satuanj` ,
    `rupiah` ,
    `keterangan` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$mesin."', '".$kodebudget1."','".$anggaranKd."','".$regional1."', '".$kodebarang1."', '".$jumlah1."', '".$satuan1."', '".$totalharga1."', '".$jenis1."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }	
    
}

//save tab0
if($tab=='2'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $kodebudget2=$_POST['kodebudget2'];
    $jumlahpertahun2=$_POST['jumlahpertahun2'];

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$mesin."' and kodebudget ='".$kodebudget2."'";
    $res=mysql_query($str);
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebudget." ".$bar->rupiah." ";
    }
    if($hkef!=''){
        $hkef='Data sudah ada : '.$hkef;
        echo $hkef;
        exit;
    }
    $str="select * from ".$dbname.".bgt_kode
        where kodebudget = 'PKSM'";
    $res=mysql_query($str);
    $akun='';
    while($bar= mysql_fetch_object($res))
    {
        $akun.=$bar->noakun;
    }
    if($akun==''){
        $akun='No Akun PKSM belum diset. Silakan diset terlebih dahulu.';
        echo $akun;
        exit;
    }
            
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `noakun` ,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$mesin."', '".$kodebudget2."', '".$akun."', '".$jumlahpertahun2."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
    
}
//save tab3
if($tab=='3'){  
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $mesin=$_POST['mesin'];
    $kodebudget3=$_POST['kodebudget3'];
    $totalbiaya3=$_POST['totalbiaya3'];
    $kodevhc3=$_POST['kodevhc3'];
    $jumlahjam3=$_POST['jumlahjam3'];
    $satuan3=$_POST['satuan3'];

    $str="select * from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi
        where tahunbudget ='".$tahunbudget."' and kodevhc ='".$kodevhc3."'";
    $res=mysql_query($str);
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $sisajam.=($bar->jamsetahun)-($bar->teralokasi);
        $setahunjam=$bar->jamsetahun;
        $teralokasijam=$bar->teralokasi;
    }
    if($jumlahjam3>$sisajam){
        $hkef="Error: Kendaraan ".$kodevhc3." sudah teralokasi sebesar ".$teralokasijam." jam dari total ".$setahunjam." jam, hanya dapat digunakan sebanyak ".$sisajam." jam.";
        echo $hkef;
        exit;
    }
    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and kodebudget like 'VHC%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$mesin."' and kodevhc ='".$kodevhc3."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodevhc." ".$bar->rupiah;
    }
    if($hkef!=''){
        $hkef='Error: Data sudah ada : '.$hkef;
        echo $hkef;
        exit;
    }

    $str="select * from ".$dbname.".bgt_kode
        where kodebudget = 'VHC'";
    $res=mysql_query($str);
    $akun='';
    while($bar= mysql_fetch_object($res))
    {
        $akun.=$bar->noakun;
    }
    if($akun==''){
        $akun='Error:No Akun VHC untuk PKS belum ada. Silakan diset terlebih dahulu.';
        echo $akun;
        exit;
    }
    
    
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `kodevhc` ,
    `volume` ,
    `satuanv`,
    `noakun`,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$mesin."', '".$kodebudget3."', '".$kodevhc3."', '".$jumlahjam3."', 
    '".$satuan3."','".$akun."','".$totalbiaya3."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
    
}

//save sebaran
if($tab=='9'){

    $kunci=$_POST['kunci'];
    $rp01=$_POST['rp01'];
    $rp02=$_POST['rp02'];
    $rp03=$_POST['rp03'];
    $rp04=$_POST['rp04'];
    $rp05=$_POST['rp05'];
    $rp06=$_POST['rp06'];
    $rp07=$_POST['rp07'];
    $rp08=$_POST['rp08'];
    $rp09=$_POST['rp09'];
    $rp10=$_POST['rp10'];
    $rp11=$_POST['rp11'];
    $rp12=$_POST['rp12'];
    $fis01=$_POST['fis01'];
    $fis02=$_POST['fis02'];
    $fis03=$_POST['fis03'];
    $fis04=$_POST['fis04'];
    $fis05=$_POST['fis05'];
    $fis06=$_POST['fis06'];
    $fis07=$_POST['fis07'];
    $fis08=$_POST['fis08'];
    $fis09=$_POST['fis09'];
    $fis10=$_POST['fis10'];
    $fis11=$_POST['fis11'];
    $fis12=$_POST['fis12'];

    $str="select * from ".$dbname.".bgt_distribusi
        where kunci = '".$kunci."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kunci;
    }
    if($hkef!=''){
        $hkef='Error:Data sudah ada : '.$hkef;
//        echo $hkef;
//        exit;
    }

    // udah dicek di js pas mau ngesave
//    $str="select * from ".$dbname.".bgt_budget
//        where kunci = '".$kunci."'";
//    $res=mysql_query($str);
//    //$no=1;
//    $cektotal='';
//    while($bar= mysql_fetch_object($res))
//    {
//        $totalah=($rp01)+($rp02)+($rp03)+($rp04)+($rp05)+($rp06)+($rp07)+($rp08)+($rp09)+($rp10)+($rp11)+($rp12);
//        $totalfisah=($fis01)+($fis02)+($fis03)+($fis04)+($fis05)+($fis06)+($fis07)+($fis08)+($fis09)+($fis10)+($fis11)+($fis12);
//        $r=floor($totalah);
//        if($r>$bar->rupiah)
//            $cektotal.='rupiah: '.number_format($totalah,2)." > ".number_format($bar->rupiah,2);
//        $r=floor($totalfisah);
//        if($r>$bar->jumlah)
//            $cektotal.='<br>jumlah: '.number_format($totalfisah,2)." > ".number_format($bar->jumlah,2);
//    }
//    if($cektotal!=''){
//        $cektotal='Error:Total sebaran melebihi tahunan.<br>'.$cektotal;
//        echo $cektotal;
//        exit;
//    }
    
    
    if($hkef=='')
    $str="INSERT INTO ".$dbname.".`bgt_distribusi` (
    `kunci` ,
    `rp01` ,
    `rp02` ,
    `rp03` ,
    `rp04` ,
    `rp05` ,
    `rp06` ,
    `rp07` ,
    `rp08` ,
    `rp09` ,
    `rp10` ,
    `rp11` ,
    `rp12` ,
    `fis01` ,
    `fis02` ,
    `fis03` ,
    `fis04` ,
    `fis05` ,
    `fis06` ,
    `fis07` ,
    `fis08` ,
    `fis09` ,
    `fis10` ,
    `fis11` ,
    `fis12` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$kunci."', '".$rp01."', '".$rp02."', '".$rp03."', '".$rp04."', '".$rp05."', '".$rp06."', '".$rp07."', '".$rp08."', '".$rp09."', '".$rp10."', '".$rp11."', '".$rp12."', 
        '".$fis01."', '".$fis02."', '".$fis03."', '".$fis04."', '".$fis05."', '".$fis06."', '".$fis07."', '".$fis08."', '".$fis09."', '".$fis10."', '".$fis11."', '".$fis12."',
        '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )"; else
    $str="UPDATE ".$dbname.".`bgt_distribusi` SET `rp01` = '".$rp01."', `rp02` = '".$rp02."', `rp03` = '".$rp03."', `rp04` = '".$rp04."', `rp05` = '".$rp05."', `rp06` = '".$rp06."', `rp07` = '".$rp07."', `rp08` = '".$rp08."', `rp09` = '".$rp09."', `rp10` = '".$rp10."', `rp11` = '".$rp11."', `rp12` = '".$rp12."',
        `fis01` = '".$fis01."', `fis02` = '".$fis02."', `fis03` = '".$fis03."', `fis04` = '".$fis04."', `fis05` = '".$fis05."', `fis06` = '".$fis06."', `fis07` = '".$fis07."', `fis08` = '".$fis08."', `fis09` = '".$fis09."', `fis10` = '".$fis10."', `fis11` = '".$fis11."', `fis12` = '".$fis12."' WHERE kunci = '".$kunci."'";
//    UPDATE `owl`.`bgt_distribusi` SET `fis01` = '1' WHERE `bgt_distribusi`.`kunci` =77;    
 
    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
}
?>

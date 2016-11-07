<?php
// file creator: dhyaz sep 14, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tab=$_POST['tab'];

//save tab0
if($tab=='cekclose'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];

    $str="select * from ".$dbname.".bgt_budget
        where tutup = 1 and tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."'
        limit 0, 1    
        ";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.="Budget has been closed.";
    }
    if($hkef!='')echo $hkef;    
}

//save tab0
if($tab=='0'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $kodebudget0=$_POST['kodebudget0'];
    $hkefektif0=$_POST['hkefektif0'];
    $jumlahpersonel0=$_POST['jumlahpersonel0'];
    $totalbiaya0=$_POST['totalbiaya0'];

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebudget ='".$kodebudget0."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebudget." ".$bar->jumlah." orang ";
    }
    if($hkef!=''){
        $hkef='Data already exist : '.$hkef;
        echo $hkef;
        exit;
    }
            
    $volume0=$hkefektif0*$jumlahpersonel0;

    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `volume` ,
    `satuanv` ,
    `jumlah` ,
    `satuanj` ,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget0."', '".$volume0."', 'hk' , '".$jumlahpersonel0."', 'orang' , '".$totalbiaya0."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }	
    
}

//save tab1
if($tab=='1'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $kodebudget1=$_POST['kodebudget1'];
    $totalharga1=$_POST['totalharga1'];
    $kodebarang1=$_POST['kodebarang1'];
    $regional1=$_POST['regional1'];
    $jumlah1=$_POST['jumlah1'];
    $satuan1=$_POST['satuan1'];

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and kodebudget like 'M%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebarang ='".$kodebarang1."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebarang." ".$bar->jumlah." ".$bar->satuanj;
    }
    if($hkef!=''){
        $hkef='Data already exist : '.$hkef;
        echo $hkef;
        exit;
    }
    
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `regional` ,
    `kodebarang` ,
    `jumlah` ,
    `satuanj` ,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget1."', '".$regional1."', '".$kodebarang1."', '".$jumlah1."', '".$satuan1."', '".$totalharga1."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }	
    
}

//save tab2
if($tab=='2'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $kodebudget2=$_POST['kodebudget2'];
    $totalharga2=$_POST['totalharga2'];
    $kodebarang2=$_POST['kodebarang2'];
    $regional2=$_POST['regional2'];
    $jumlah2=$_POST['jumlah2'];
    $satuan2=$_POST['satuan2'];

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and kodebudget like 'TOOL%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebarang ='".$kodebarang2."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kodebarang." ".$bar->jumlah." ".$bar->satuanj;
    }
    if($hkef!=''){
        $hkef='Data already exist : '.$hkef;
        echo $hkef;
        exit;
    }
    
    $str="INSERT INTO ".$dbname.".`bgt_budget` (
    `tipebudget` ,
    `tahunbudget` ,
    `kodeorg` ,
    `kodebudget` ,
    `regional` ,
    `kodebarang` ,
    `jumlah` ,
    `satuanj` ,
    `rupiah` ,
    `updateby` ,
    `lastupdate` 
    )
    VALUES (
    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget2."', '".$regional2."', '".$kodebarang2."', '".$jumlah2."', '".$satuan2."', '".$totalharga2."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }	
    
}

//save tab3
if($tab=='3'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $kodebudget3=$_POST['kodebudget3'];
    $totalbiaya3=$_POST['totalbiaya3'];
    $kodeakun3=$_POST['kodeakun3'];

    $str="select * from ".$dbname.".bgt_budget
        where tipebudget = '".$tipebudget."' and kodebudget like 'TRANSIT%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and noakun ='".$kodeakun3."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->noakun." ".$bar->rupiah;
    }
    if($hkef!=''){
        $hkef='Data already exist : '.$hkef;
        echo $hkef;
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
    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget3."', '".$kodeakun3."', '".$totalbiaya3."', '".$_SESSION['standard']['userid']."',
    CURRENT_TIMESTAMP 
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }	
    
}

//save tab4
if($tab=='4'){

    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $kunci=$_POST['kunci'];

    $str="update ".$dbname.".bgt_budget set tutup='1'
        where kunci ='".$kunci."'";
  //  $res=mysql_query($str);
    //$no=1;
//    $hkef='';
    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
    
}
?>

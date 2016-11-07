<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['pt']==''?$pt=$_GET['pt']:$pt=$_POST['pt'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['afdeling']==''?$afdeling=$_GET['afdeling']:$afdeling=$_POST['afdeling'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['inti']==''?$inti=$_GET['inti']:$inti=$_POST['inti'];

//$proses=$_POST['proses'];
//$pt=$_POST['pt'];
//$unit=$_POST['unit'];
//$afdeling=$_POST['afdeling'];
//$periode=$_POST['periode'];

//echo "proses:".$proses." pt:".$pt." unit:".$unit." afdeling:".$afdeling." periode:".$periode;
$nmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');

// dapatkan tahun bulan
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$unitunit="('')";

// default: pt
$kodeorg=" like '".$pt."%'";

// kalo pt doang, dapatkan unit-unitnya
if($unit==''){
    $unitunit="(";
    $str="select kodeorganisasi from ".$dbname.".organisasi 
        where induk='".$pt."' and tipe='KEBUN'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=  mysql_fetch_assoc($query))
    {
        $unitunit.="'".$res['kodeorganisasi']."',";
    }    
    $unitunit=substr($unitunit,0,-1);
    $unitunit=$unitunit.")";
    $kodeorg=" in ".$unitunit;
}else if($afdeling=='')$kodeorg=" like '".$unit."%'"; // semua afdeling
    else if($afdeling!='')$kodeorg=" like '".$afdeling."%'"; // afdeling tertentu
    
if($pt=='')$kodeorg=" like '".$pt."%'";    

// selain preview excel pdf
if($proses=='getkebun'||$proses=='getafdeling'){ 
    if($proses=='getkebun'){
        $optkebun="<option value=''>".$_SESSION['lang']['all']."</option>";
        if ($inti=='inti'){
            $whr=" and namaorganisasi not like 'PLASMA%'";
        } else if ($inti=='plasma'){
            $whr=" and namaorganisasi like 'PLASMA%'";
        } else {
            $whr="";
        }
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
            where induk='".$pt."'".$whr." and tipe='KEBUN'";
        $query=mysql_query($str) or die(mysql_error($conn));
        while($res=  mysql_fetch_assoc($query))
        {
            $optkebun.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>";
        }
        if($pt=='')$optkebun="<option value=''></option>";
        echo $optkebun;
    }
    if($proses=='getafdeling'){
        $optkebun="<option value=''>".$_SESSION['lang']['all']."</option>";
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
            where induk='".$unit."' and tipe='AFDELING'";
        $query=mysql_query($str) or die(mysql_error($conn));
        while($res=  mysql_fetch_assoc($query))
        {
            $optkebun.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>";
        }
        if($unit=='')$optkebun="<option value=''></option>";
        echo $optkebun;
    }
// preview excel pdf    
}else{ 
    if($afdeling!='')$kode_org=" kodeorg ".$kodeorg; else $kode_org=" substr(kodeorg,1,4) ".$kodeorg;
    if($afdeling!='')$kode_blk=" kodeblok ".$kodeorg; else $kode_blk=" substr(kodeblok,1,4) ".$kodeorg;
    // $kode_blk2 khusus untuk data bulan ini dan sd bulan ini
    // kalo pilihan afdeling kosong, pake kodeorg, kalo ada afdeling pake kodeblok
    if($afdeling!='')$kode_blk2=" kodeblok ".$kodeorg; else $kode_blk2=" substr(kodeorg,1,4) ".$kodeorg;
    if($afdeling!='')$blk=" blok ".$kodeorg; else $blk=" substr(blok,1,4) ".$kodeorg;    
    
////    echo $kode_org.'<br>';
////    echo $kode_blk.'<br>';
////    echo $kode_blk2.'<br>';
////    echo $blk.'<br>';
//    
//    // luas budget
//    $str="SELECT kodeblok, intiplasma, (hathnini) as luas FROM ".$dbname.".bgt_blok
//        WHERE ".$kode_blk." and statusblok='TM'";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $luasinmaangg+=$res['luas'];
//        if($res['intiplasma']=='I')$luasintiangg+=$res['luas'];
//        if($res['intiplasma']=='P')$luasplasangg+=$res['luas'];
//    }    
//
//    //produksi budget bulan ini
//    $str="SELECT kodeblok, intiplasma, (kg".$bulan.") as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
//        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $prodinmaangg+=$res['luas'];
//        if($res['intiplasma']=='I')$prodintiangg+=$res['luas'];
//        if($res['intiplasma']=='P')$prodplasangg+=$res['luas'];
//    }    
//    
//    $addstr="(";
//    for($W=1;$W<=intval($bulan);$W++)
//    {
//        if($W<10)$jack="kg0".$W;
//        else $jack="kg".$W;
//        if($W<intval($bulan))$addstr.=$jack."+";
//        else $addstr.=$jack;
//    }
//    $addstr.=")";
//    
//    //produksi budget sd bulan ini
//    $str="SELECT kodeblok, intiplasma, ".$addstr." as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
//        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'"; 
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $prsdinmaangg+=$res['luas'];
//        if($res['intiplasma']=='I')$prsdintiangg+=$res['luas'];
//        if($res['intiplasma']=='P')$prsdplasangg+=$res['luas'];
//    }    
//    
//    $addstr="(";
//    for($W=1;$W<=12;$W++)
//    {
//        if($W<10)$jack="kg0".$W;
//        else $jack="kg".$W;
//        if($W<12)$addstr.=$jack."+";
//        else $addstr.=$jack;
//    }
//    $addstr.=")";
//    
//    //produksi budget setahun
//    $str="SELECT kodeblok, intiplasma, ".$addstr." as luas FROM ".$dbname.".bgt_produksi_kbn_kg_vw
//        WHERE ".$kode_blk." and tahunbudget = '".$tahun."'";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $prthinmaangg+=$res['luas'];
//        if($res['intiplasma']=='I')$prthintiangg+=$res['luas'];
//        if($res['intiplasma']=='P')$prthplasangg+=$res['luas'];
//    }        
//
//    // data aktual bulan ini (total)
//    $str="SELECT kodeblok, intiplasma, substr(noakun,1,5) as noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
//        WHERE ".$kode_blk2." and periode = '".$periode."' and noakun between '6110000' and '7199999'";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $datainmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
//        if($res['intiplasma']=='I')$dataintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
//        if($res['intiplasma']=='P')$dataplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
//        $dataundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
//        if(substr($res['noakun'],0,3)=='711'){
//            $d7tainmaaktu[711]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[711]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[711]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[711]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='712'){
//            $d7tainmaaktu[712]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[712]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[712]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[712]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='713'){
//            $d7tainmaaktu[713]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[713]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[713]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[713]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='714'){
//            $d7tainmaaktu[714]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[714]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[714]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[714]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71501'){
//            $d7tainmaaktu[71501]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[71501]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[71501]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[71501]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='716'){
//            $d7tainmaaktu[716]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7taintiaktu[716]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7taplasaktu[716]+=($res['debet']-$res['kredit']); else
//            $d7taundeaktu[716]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71502'){
//            $d9tainmaaktu[71502]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d9taintiaktu[71502]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d9taplasaktu[71502]+=($res['debet']-$res['kredit']); else
//            $d9taundeaktu[71502]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71999'){
//            $d9tainmaaktu[71999]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d9taintiaktu[71999]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d9taplasaktu[71999]+=($res['debet']-$res['kredit']); else
//            $d9taundeaktu[71999]+=($res['debet']-$res['kredit']);
//        }
//    }
//
//    
////    // data aktual bulan ini (detail) : khusus untuk 7199999
////    $str="SELECT kodeblok, intiplasma, noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
////        WHERE ".$kode_blk." and periode = '".$periode."' and noakun like '7199999%' and length(noakun)=7";
////    $query=mysql_query($str) or die(mysql_error($conn));
////    while($res=mysql_fetch_assoc($query))
////    {
////        $datainmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
////        if($res['intiplasma']=='I')$dataintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
////        if($res['intiplasma']=='P')$dataplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
////        $dataundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
////    }
//    
//    // data aktual sd bulan ini (total)
//    $str="SELECT kodeblok, intiplasma, substr(noakun,1,5) as noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
//        WHERE ".$kode_blk2." and periode between '".$tahun."-01' and '".$periode."' and (noakun like '621%' or noakun like '611%' or noakun like '7%')";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $dasdinmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
//        if($res['intiplasma']=='I')$dasdintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
//        if($res['intiplasma']=='P')$dasdplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
//        $dasdundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
//        if(substr($res['noakun'],0,3)=='711'){
//            $d7sdinmaaktu[711]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[711]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[711]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[711]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='712'){
//            $d7sdinmaaktu[712]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[712]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[712]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[712]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='713'){
//            $d7sdinmaaktu[713]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[713]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[713]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[713]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='714'){
//            $d7sdinmaaktu[714]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[714]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[714]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[714]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71501'){
//            $d7sdinmaaktu[71501]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[71501]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[71501]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[71501]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,3)=='716'){
//            $d7sdinmaaktu[716]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d7sdintiaktu[716]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d7sdplasaktu[716]+=($res['debet']-$res['kredit']); else
//            $d7sdundeaktu[716]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71502'){
//            $d9sdinmaaktu[71502]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d9sdintiaktu[71502]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d9sdplasaktu[71502]+=($res['debet']-$res['kredit']); else
//            $d9sdundeaktu[71502]+=($res['debet']-$res['kredit']);
//        }
//        if(substr($res['noakun'],0,5)=='71999'){
//            $d9sdinmaaktu[71999]+=($res['debet']-$res['kredit']);
//            if($res['intiplasma']=='I')$d9sdintiaktu[71999]+=($res['debet']-$res['kredit']); else
//            if($res['intiplasma']=='P')$d9sdplasaktu[71999]+=($res['debet']-$res['kredit']); else
//            $d9sdundeaktu[71999]+=($res['debet']-$res['kredit']);
//        }
//    }
//
////    // data aktual sd bulan ini (detail) : khusus untuk 7199999
////    $str="SELECT kodeblok, intiplasma, noakun, debet, kredit FROM ".$dbname.".keu_jurnalsum_blok_vw
////        WHERE ".$kode_blk." and periode between '".$tahun."-01' and '".$periode."' and noakun like '7199999%' and length(noakun)=7";
////    $query=mysql_query($str) or die(mysql_error($conn));
////    while($res=mysql_fetch_assoc($query))
////    {
////        $dasdinmaaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
////        if($res['intiplasma']=='I')$dasdintiaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
////        if($res['intiplasma']=='P')$dasdplasaktu[$res['noakun']]+=($res['debet']-$res['kredit']); else
////        $dasdundeaktu[$res['noakun']]+=($res['debet']-$res['kredit']);
////    }
//
//    // data budget bulan ini (total) dan setahun
//    $str="SELECT kodeorg, intiplasma, substr(noakun,1,5) as noakun, (rp".$bulan.") as rp, rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
//        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and (noakun like '621%' or noakun like '611%' or noakun like '7%')";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $datainmaangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='I')$dataintiangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='P')$dataplasangg[$res['noakun']]+=$res['rp'];        
//        $dathinmaangg[$res['noakun']]+=$res['rupiah'];
//        if($res['intiplasma']=='I')$dathintiangg[$res['noakun']]+=$res['rupiah'];
//        if($res['intiplasma']=='P')$dathplasangg[$res['noakun']]+=$res['rupiah'];        
//        if(substr($res['noakun'],0,3)=='711'){
//            $d7tainmaangg[711]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[711]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[711]+=$res['rp'];        
//            $d7thinmaangg[711]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[711]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[711]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,3)=='712'){
//            $d7tainmaangg[712]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[712]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[712]+=$res['rp'];        
//            $d7thinmaangg[712]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[712]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[712]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,3)=='713'){
//            $d7tainmaangg[713]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[713]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[713]+=$res['rp'];        
//            $d7thinmaangg[713]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[713]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[713]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,3)=='714'){
//            $d7tainmaangg[714]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[714]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[714]+=$res['rp'];        
//            $d7thinmaangg[714]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[714]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[714]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,5)=='71501'){
//            $d7tainmaangg[71501]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[71501]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[71501]+=$res['rp'];        
//            $d7thinmaangg[71501]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[71501]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[71501]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,3)=='716'){
//            $d7tainmaangg[716]+=$res['rp'];
//            if($res['intiplasma']=='I')$d7taintiangg[716]+=$res['rp'];
//            if($res['intiplasma']=='P')$d7taplasangg[716]+=$res['rp'];        
//            $d7thinmaangg[716]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d7thintiangg[716]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d7thplasangg[716]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,5)=='71502'){
//            $d9tainmaangg[71502]+=$res['rp'];
//            if($res['intiplasma']=='I')$d9taintiangg[71502]+=$res['rp'];
//            if($res['intiplasma']=='P')$d9taplasangg[71502]+=$res['rp'];        
//            $d9thinmaangg[71502]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d9thintiangg[71502]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d9thplasangg[71502]+=$res['rupiah'];        
//        }
//        if(substr($res['noakun'],0,5)=='71999'){
//            $d9tainmaangg[71999]+=$res['rp'];
//            if($res['intiplasma']=='I')$d9taintiangg[71999]+=$res['rp'];
//            if($res['intiplasma']=='P')$d9taplasangg[71999]+=$res['rp'];        
//            $d9thinmaangg[71999]+=$res['rupiah'];
//            if($res['intiplasma']=='I')$d9thintiangg[71999]+=$res['rupiah'];
//            if($res['intiplasma']=='P')$d9thplasangg[71999]+=$res['rupiah'];        
//        }
//    }
    
//// jamhari
$str="select (rp".$bulan.") as rp,substr(noakun,1,5) as noakun,rupiah from ".$dbname.".bgt_budget_detail 
          where ".$kode_org." and tahunbudget='".$tahun."' and substr(noakun,1,1)='7'";
    $query=mysql_query($str) or die(mysql_error($conn));
    //exit("error".$str);
    while($res=mysql_fetch_assoc($query))
    {
        if(substr($res['noakun'],0,3)=='711'){
            $d7tainmaangg[711]+=$res['rp'];
            $d7taintiangg[711]+=$res['rp'];
            $d7taplasangg[711]+=$res['rp'];        
            $d7thinmaangg[711]+=$res['rupiah'];
            $d7thintiangg[711]+=$res['rupiah'];
            $d7thplasangg[711]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,3)=='712'){
            $d7tainmaangg[712]+=$res['rp'];
            $d7taintiangg[712]+=$res['rp'];
            $d7taplasangg[712]+=$res['rp'];        
            $d7thinmaangg[712]+=$res['rupiah'];
            $d7thintiangg[712]+=$res['rupiah'];
            $d7thplasangg[712]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,3)=='713'){
            $d7tainmaangg[713]+=$res['rp'];
            $d7taintiangg[713]+=$res['rp'];
            $d7taplasangg[713]+=$res['rp'];        
            $d7thinmaangg[713]+=$res['rupiah'];
            $d7thintiangg[713]+=$res['rupiah'];
            $d7thplasangg[713]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,3)=='714'){
            $d7tainmaangg[714]+=$res['rp'];
            $d7taintiangg[714]+=$res['rp'];
            $d7taplasangg[714]+=$res['rp'];        
            $d7thinmaangg[714]+=$res['rupiah'];
            $d7thintiangg[714]+=$res['rupiah'];
            $d7thplasangg[714]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,5)=='71501'){
            $d7tainmaangg[71501]+=$res['rp'];
            $d7taintiangg[71501]+=$res['rp'];
            $d7taplasangg[71501]+=$res['rp'];        
            $d7thinmaangg[71501]+=$res['rupiah'];
            $d7thintiangg[71501]+=$res['rupiah'];
            $d7thplasangg[71501]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,3)=='716'){
            $d7tainmaangg[716]+=$res['rp'];
            $d7taintiangg[716]+=$res['rp'];
            $d7taplasangg[716]+=$res['rp'];        
            $d7thinmaangg[716]+=$res['rupiah'];
            $d7thintiangg[716]+=$res['rupiah'];
            $d7thplasangg[716]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,5)=='71502'){
            $d9tainmaangg[71502]+=$res['rp'];
            $d9taintiangg[71502]+=$res['rp'];
            $d9taplasangg[71502]+=$res['rp'];        
            $d9thinmaangg[71502]+=$res['rupiah'];
            $d9thintiangg[71502]+=$res['rupiah'];
            $d9thplasangg[71502]+=$res['rupiah'];        
        }
        if(substr($res['noakun'],0,5)=='71999'){
            $d9tainmaangg[71999]+=$res['rp'];
            $d9taintiangg[71999]+=$res['rp'];
            $d9taplasangg[71999]+=$res['rp'];        
            $d9thinmaangg[71999]+=$res['rupiah'];
            $d9thintiangg[71999]+=$res['rupiah'];
            $d9thplasangg[71999]+=$res['rupiah'];        
        }
    }    
//// jamhari    

//    // data budget bulan ini (detail) dan setahun : khusus untuk 7199999
//    $str="SELECT kodeorg, intiplasma, noakun, (rp".$bulan.") as rp, rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
//        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '7199999%' and length(noakun)=7";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $datainmaangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='I')$dataintiangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='P')$dataplasangg[$res['noakun']]+=$res['rp'];        
//        $dathinmaangg[$res['noakun']]+=$res['rupiah'];
//        if($res['intiplasma']=='I')$dathintiangg[$res['noakun']]+=$res['rupiah'];
//        if($res['intiplasma']=='P')$dathplasangg[$res['noakun']]+=$res['rupiah'];        
//    }

    $addstr="(";
    for($W=1;$W<=intval($bulan);$W++)
    {
        if($W<10)$jack="rp0".$W;
        else $jack="rp".$W;
        if($W<intval($bulan))$addstr.=$jack."+";
        else $addstr.=$jack;
    }
    $addstr.=")";

    // data budget sd bulan ini (total)
    $str="SELECT kodeorg, intiplasma, substr(noakun,1,5) as noakun, ".$addstr." as rp, rupiah as rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and (noakun like '621%' or noakun like '611%' or noakun like '7%')";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $dasdinmaangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='I')$dasdintiangg[$res['noakun']]+=$res['rp'];
        if($res['intiplasma']=='P')$dasdplasangg[$res['noakun']]+=$res['rp'];        
        if(substr($res['noakun'],0,3)=='711'){
            $d7sdinmaangg[711]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[711]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[711]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='712'){
            $d7sdinmaangg[712]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[712]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[712]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='713'){
            $d7sdinmaangg[713]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[713]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[713]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='714'){
            $d7sdinmaangg[714]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[714]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[714]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71501'){
            $d7sdinmaangg[71501]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[71501]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[71501]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='716'){
            $d7sdinmaangg[716]+=$res['rp'];
            if($res['intiplasma']=='I')$d7sdintiangg[716]+=$res['rp'];
            if($res['intiplasma']=='P')$d7sdplasangg[716]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71502'){
            $d9sdinmaangg[71502]+=$res['rp'];
            if($res['intiplasma']=='I')$d9sdintiangg[71502]+=$res['rp'];
            if($res['intiplasma']=='P')$d9sdplasangg[71502]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71999'){
            $d9sdinmaangg[71999]+=$res['rp'];
            if($res['intiplasma']=='I')$d9sdintiangg[71999]+=$res['rp'];
            if($res['intiplasma']=='P')$d9sdplasangg[71999]+=$res['rp'];        
        }
    }    
    
//// jamhari
$str="select ".$addstr." as rp,substr(noakun,1,5) as noakun,rupiah as rupiah from ".$dbname.".bgt_budget_detail 
          where ".$kode_org." and tahunbudget='".$tahun."' and substr(noakun,1,1)='7'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        if(substr($res['noakun'],0,3)=='711'){
            $d7sdinmaangg[711]+=$res['rp'];
            $d7sdintiangg[711]+=$res['rp'];
            $d7sdplasangg[711]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='712'){
            $d7sdinmaangg[712]+=$res['rp'];
            $d7sdintiangg[712]+=$res['rp'];
            $d7sdplasangg[712]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='713'){
            $d7sdinmaangg[713]+=$res['rp'];
            $d7sdintiangg[713]+=$res['rp'];
            $d7sdplasangg[713]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='714'){
            $d7sdinmaangg[714]+=$res['rp'];
            $d7sdintiangg[714]+=$res['rp'];
            $d7sdplasangg[714]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71501'){
            $d7sdinmaangg[71501]+=$res['rp'];
            $d7sdintiangg[71501]+=$res['rp'];
            $d7sdplasangg[71501]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,3)=='716'){
            $d7sdinmaangg[716]+=$res['rp'];
            $d7sdintiangg[716]+=$res['rp'];
            $d7sdplasangg[716]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71502'){
            $d9sdinmaangg[71502]+=$res['rp'];
            $d9sdintiangg[71502]+=$res['rp'];
            $d9sdplasangg[71502]+=$res['rp'];        
        }
        if(substr($res['noakun'],0,5)=='71999'){
            $d9sdinmaangg[71999]+=$res['rp'];
            $d9sdintiangg[71999]+=$res['rp'];
            $d9sdplasangg[71999]+=$res['rp'];        
        }
    }        
////    
    
//    // data budget sd bulan ini (detail) : khusus untuk 7199999
//    $str="SELECT kodeorg, intiplasma, noakun, ".$addstr." as rp, rupiah as rupiah FROM ".$dbname.".bgt_budget_kebun_perblok_vw
//        WHERE ".$kode_org." and tahunbudget = '".$tahun."' and noakun like '7199999%' and length(noakun)=7";
//    $query=mysql_query($str) or die(mysql_error($conn));
//    while($res=mysql_fetch_assoc($query))
//    {
//        $dasdinmaangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='I')$dasdintiangg[$res['noakun']]+=$res['rp'];
//        if($res['intiplasma']=='P')$dasdplasangg[$res['noakun']]+=$res['rp'];        
//    }    
    
//    echo "<pre>";
//    print_r($dataintiaktu);
//    print_r($dasdintiaktu);
//    echo "</pre>";
    if($unit!=''){
        $kode_org2=" kodeorg like '".$unit."%'";
    } else {
        $kode_org2=" kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk ='".$pt."'";
        if ($inti=='inti'){
            $kode_org2.=" and namaorganisasi not like '%PLASMA%'";
        }
        if ($inti=='plasma'){
            $kode_org2.=" and namaorganisasi like '%PLASMA%'";
        }
        $kode_org2.=")";
    }
    $per=" AND periode>='".$tahun."-01' AND periode<='".$tahun."-".$bulan."'";
    $str="SELECT kodekegiatan,SUM(jumlah) AS jumlah FROM ".$dbname.".keu_jurnaldt_vw WHERE kodekegiatan<>'' AND nojurnal LIKE '%M0%' 
            AND jumlah>0 AND ".$kode_org2.$per." GROUP BY kodekegiatan";
    //echo $str;
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
        $actupahthismonth[$res['kodekegiatan']]=$res['jumlah'];
    }
    $str="SELECT kodekegiatan,SUM(jumlah) AS jumlah FROM ".$dbname.".keu_jurnaldt_vw WHERE kodekegiatan<>'' AND nojurnal LIKE '%INV%' 
            AND jumlah>0 AND ".$kode_org2.$per." GROUP BY kodekegiatan";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
        $actmatthismonth[$res['kodekegiatan']]=$res['jumlah'];
    }
    $str="SELECT kodekegiatan,SUM(jumlah) AS jumlah FROM ".$dbname.".keu_jurnaldt_vw WHERE kodekegiatan<>'' AND nojurnal LIKE '%VHC1%'
            AND jumlah>0 AND ".$kode_org2.$per." GROUP BY kodekegiatan";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
        $actabthismonth[$res['kodekegiatan']]=$res['jumlah'];
    }
    $str="SELECT kodekegiatan,SUM(jumlah) AS jumlah FROM ".$dbname.".keu_jurnaldt_vw WHERE kodekegiatan<>'' AND nojurnal NOT LIKE '%INV%' AND nojurnal NOT LIKE '%M0%' AND nojurnal NOT LIKE '%VHC1%' 
            AND jumlah>0 AND ".$kode_org2.$per." GROUP BY kodekegiatan";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
        $actotherthismonth[$res['kodekegiatan']]=$res['jumlah'];
    }
    $str="SELECT kegiatan,SUM(rupiah) AS jumlah FROM ".$dbname.".bgt_budget_detail WHERE kodebudget LIKE '%SDM%' 
            AND kegiatan IS NOT NULL AND ".$kode_org." AND tahunbudget=".$tahun." GROUP BY kegiatan";
    //echo $str;
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kegiatan']]=$res['kegiatan'];
        $bgtupahthismonth[$res['kegiatan']]=$res['jumlah'];
    }
    $str="SELECT kegiatan,SUM(rupiah) AS jumlah FROM ".$dbname.".bgt_budget_detail WHERE kodebudget LIKE 'M%' 
            AND kegiatan IS NOT NULL AND ".$kode_org." AND tahunbudget=".$tahun." GROUP BY kegiatan";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query)){
        $kegiatan[$res['kegiatan']]=$res['kegiatan'];
        $bgtmatthismonth[$res['kegiatan']]=$res['jumlah'];
    }
    if($inti=='inti'){
        $tampilinti='INTI';
    }
    else if($inti=='plasma'){
        $tampilinti='PLASMA';
    }
    else{
        $tampilinti='INTI DAN PLASMA';
    } 
    if($proses=='excel')
    {
        $bg=" bgcolor=#DEDEDE";
        $brdr=1;
        if($pt=='')$tab.='PT (seluruhnya)'; else $tab.='PT '.$pt;
        if($unit!='')$tab.=', UNIT '.$unit;
        if($afdeling!='')$tab.=', Afdeling '.$afdeling;
        $tab.='<br>LAPORAN REKAP BIAYA PER KEGIATAN ('.$tampilinti.')<br>01-'.$tahun.' S/D '.$bulan.'-'.$tahun;
    }
    else
    { 
        echo 'LAPORAN REKAP BIAYA PER KEGIATAN ('.$tampilinti.')<br>01-'.$tahun.' S/D '.$bulan.'-'.$tahun;
        $bg="";
        $brdr=0;
    } 
    
//echo "<pre>";
//print_r($batal);
//echo "</pre>";
//    
//exit;
        
    $lebkolkiri=200;
    $lebar=95;
    $tab.="<div style='width:1200px;display:fixed;'>
        <table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%'>
        <thead class=rowheader>
        <tr>
        <td align=center rowspan=2 ".$bg.">Kegiatan</td>
        <td align=center colspan=4 ".$bg.">Actual This Month</td>
        <td align=center colspan=4 ".$bg.">Budget This Month</td>
        </tr>";
        $tab.="<tr><td align=center ".$bg." width=".$lebar.">Upah</td>
        <td align=center ".$bg." width=".$lebar.">Material</td>
        <td align=center ".$bg." width=".$lebar.">Alat Berat</td>
        <td align=center ".$bg." width=".$lebar.">Other</td>
        <td align=center ".$bg." width=".$lebar.">Upah</td>
        <td align=center ".$bg." width=".$lebar.">Material</td>
        <td align=center ".$bg." width=".$lebar.">Alat Berat</td>
        <td align=center ".$bg." width=".$lebar.">Other</td>
        </tr>";
        
        $tab.="</thead><tbody></tbody></table></div>
    ";    
    $tab.="<div style='overflow:scroll;height:240px;width:1215px;display:fixed;'>
        <table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%'>
        <thead class=rowheader></thead><tbody>";
    $totin=array();
    $totat=array();
    $bg="";
    if(!empty($kegiatan))foreach($kegiatan as $keg){
        if ($nmKeg[$keg]!=''){
        $tab.="<tr class=rowcontent>
        <td align=left ".$bg.">".$nmKeg[$keg]."</td>";
            $tab.="
            <td align=right ".$bg." width=".$lebar.">".number_format($actupahthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format($actmatthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format($actabthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format($actotherthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format($bgtupahthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format($bgtmatthismonth[$keg])."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format(0)."</td>
            <td align=right ".$bg." width=".$lebar.">".number_format(0)."</td>
                ";
            $tab.="</tr>";
        }
    }
  
    $tab.="</tbody></table></div>";    
    switch($proses)
    {
        case'preview': 
        echo $tab;
        break;    
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHis");
        $nop_="mr_biayaProduksiTbs_".$pt.$unit.$afdeling.$periode; 
        if(strlen($tab)>0)
        {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                    }
                }	
               closedir($handle);
            }
            $handle=fopen("tempExcel/".$nop_.".xls",'w');
            if(!fwrite($handle,$tab))
            {
            echo "<script language=javascript1.2>
                parent.window.alert('Can't convert to excel format');
                </script>";
                exit;
            }
            else
            {
                echo "<script language=javascript1.2>
                window.location='tempExcel/".$nop_.".xls';
                </script>";
            }
            closedir($handle);
        }
        break;
        default:
        break;        
        
    }    
    
}
	
?>

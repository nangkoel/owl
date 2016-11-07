<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($unit==''||$periode=='')
{
    exit("Error:Field required");
}

$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

$tahunlalu=$tahun-1;

$talabuni="tanggal like '".$tahunlalu."-".$bulan."%' and kodeorg like '".$unit."%'"; // tahun lalu bulan ini
$talasabuni="(tanggal between '".$tahunlalu."-01-01' and LAST_DAY('".$tahunlalu."-".$bulan."-15')) and kodeorg like '".$unit."%'"; // tahun lalu sampai dengan bulan ini
$tanibuni="tanggal like '".$tahun."-".$bulan."%' and kodeorg like '".$unit."%'"; // tahun ini bulan ini
$tanisabuni="(tanggal between '".$tahun."-01-01' and LAST_DAY('".$tahun."-".$bulan."-15')) and kodeorg like '".$unit."%'"; // tahun ini sampai dengan bulan ini
 
$do="jumlahdo";
$terima="jumlahterima";
$afkir="jumlahafkir";

$jumlah="jumlah";

// UNTUK MELIHAT KAMUS VARIABEL, LIHAT SPEK LBM 16 AREAL STATEMENT BIBITAN

// A
$str="SELECT sum(".$do.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $A=$res['hasil'];
}   

// B
$str="SELECT sum(".$do.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talasabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $B=$res['hasil'];
}   

// C
$str="SELECT sum(".$do.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanibuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $C=$res['hasil'];
}   

// D
$str="SELECT sum(".$do.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanisabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $D=$res['hasil'];
}   

// E
$str="SELECT sum(".$terima.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $E=$res['hasil'];
}   

// F
$str="SELECT sum(".$terima.") as hasil  FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talasabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $F=$res['hasil'];
}   

// G
$str="SELECT sum(".$terima.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanibuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $G=$res['hasil'];
}   

// H
$str="SELECT sum(".$terima.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanisabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $H=$res['hasil'];
}   

// I
$str="SELECT sum(".$afkir.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $I=$res['hasil'];
}   

// J
$str="SELECT sum(".$afkir.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$talasabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $J=$res['hasil'];
}   

// K
$str="SELECT sum(".$afkir.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanibuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $K=$res['hasil'];
}   

// L
$str="SELECT sum(".$afkir.") as hasil FROM ".$dbname.".bibitan_batch_vw
    WHERE ".$tanisabuni."
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $L=$res['hasil'];
}   

// M
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag!='AUTO'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $M=$res['hasil'];
}   

// N
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag!='AUTO'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $N=$res['hasil'];
}   

// O
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag!='AUTO'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $O=$res['hasil'];
}   

// P
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag!='AUTO'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $P=$res['hasil'];
}   

// Q
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahunlalu."-".$bulan."-01' and kodeorg like '%pn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Q=$res['hasil'];
}   

// R
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahunlalu."-01-01' and kodeorg like '%pn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $R=$res['hasil'];
}   

// S
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahun."-".$bulan."-01' and kodeorg like '%pn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $S=$res['hasil'];
}   

// T
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahun."-01-01' and kodeorg like '%pn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $T=$res['hasil'];
}   

// Y
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Y=$res['hasil'];
}   

// Z
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Z=$res['hasil'];
}   

// AA
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AA=$res['hasil'];
}   

// AB
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'TMB' and kodeorg like '%pn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AB=$res['hasil'];
}   

// AC
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'DBT' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AC=$res['hasil'];
}   

// AD
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'DBT' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AD=$res['hasil'];
}   

// AE
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'DBT' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AE=$res['hasil'];
}   

// AF
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'DBT' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AF=$res['hasil'];
}   

// AG
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'AFB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AG=$res['hasil'];
}   

// AH
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'AFB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AH=$res['hasil'];
}   

// AI
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'AFB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AI=$res['hasil'];
}   

// AJ
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'AFB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AJ=$res['hasil'];
}   

// AK
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AK=$res['hasil'];
}   

// AL
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AL=$res['hasil'];
}   

// AM
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AM=$res['hasil'];
}   

// AN
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AN=$res['hasil'];
}   

// AO
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'TPB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AO=$res['hasil'];
}   

// AP
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'TPB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AP=$res['hasil'];
}   

// AQ
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'TPB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AQ=$res['hasil'];
}   

// AR
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'TPB' and kodeorg like '%pn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AR=$res['hasil'];
}   

// AS
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' 
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AS=$res['hasil'];
}   

// AT
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' 
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AT=$res['hasil'];
}   

// AU
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' 
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AU=$res['hasil'];
}   

// AV
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'PNB' and kodeorg like '%pn%' 
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $AV=$res['hasil'];
}   

// M1
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahunlalu."-".$bulan."-01' and kodeorg like '%mn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $M1=$res['hasil'];
}   

// M2
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahunlalu."-01-01' and kodeorg like '%mn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $M2=$res['hasil'];
}   

// M3
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahun."-".$bulan."-01' and kodeorg like '%mn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $M3=$res['hasil'];
}   

// M4
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE tanggal < '".$tahun."-01-01' and kodeorg like '%mn%' and kodeorg like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $M4=$res['hasil'];
}   

// N1
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $N1=$res['hasil'];
}   

// N2
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $N2=$res['hasil'];
}   

// N3
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $N3=$res['hasil'];
}   

// N4
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%' and flag='auto'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $N4=$res['hasil'];
}   

// O1
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $O1=$res['hasil'];
}   

// O2
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $O2=$res['hasil'];
}   

// O3
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $O3=$res['hasil'];
}   

// O4
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'TMB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $O4=$res['hasil'];
}   

// P1
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'DBT' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $P1=$res['hasil'];
}   

// P2
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'DBT' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $P2=$res['hasil'];
}   

// P3
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'DBT' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $P3=$res['hasil'];
}   

// P4
$str="SELECT sum(".$jumlah.") as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'DBT' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $P4=$res['hasil'];
}   

// Q1
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'AFB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Q1=$res['hasil'];
}   

// Q2
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'AFB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Q2=$res['hasil'];
}   

// Q3
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'AFB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Q3=$res['hasil'];
}   

// Q4
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'AFB' and kodeorg like '%mn%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $Q4=$res['hasil'];
}   

// R1
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $R1=$res['hasil'];
}   

// R2
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $R2=$res['hasil'];
}   

// R3
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $R3=$res['hasil'];
}   

// R4
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman not like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $R4=$res['hasil'];
}   

// S1
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $S1=$res['hasil'];
}   

// S2
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$talasabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $S2=$res['hasil'];
}   

// S3
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanibuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $S3=$res['hasil'];
}   

// S4
$str="SELECT sum(".$jumlah.")*-1 as hasil FROM ".$dbname.".bibitan_mutasi
    WHERE ".$tanisabuni." and kodetransaksi = 'PNB' and kodeorg like '%mn%' and lokasipengiriman like '".$unit."%'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $S4=$res['hasil'];
}   

$U=$M; $V=$N; $W=$O; $X=$P;
$N1=$AO; $N2=$AP; $N3=$AQ; $N4=$AR;

$QAS=$Q+$U+$Y+$AC-$AG-$AK-$AO-$AS;
$RAT=$R+$V+$Z+$AD-$AH-$AL-$AP-$AT;
$SAU=$S+$W+$AA+$AE-$AI-$AM-$AQ-$AU;
$TAV=$T+$X+$AB+$AF-$AJ-$AN-$AR-$AV;

$M1S1=$M1+$N1+$O1+$P1-$Q1-$R1-$S1;
$M2S2=$M2+$N2+$O2+$P2-$Q2-$R2-$S2;
$M3S3=$M3+$N3+$O3+$P3-$Q3-$R3-$S3;
$M4S4=$M4+$N4+$O4+$P4-$Q4-$R4-$S4;

$tobi1=$QAS+$M1S1;
$tobi2=$RAT+$M2S2;
$tobi3=$SAU+$M3S3;
$tobi4=$TAV+$M4S4;

// umur pn
$str="SELECT a.batch, a.kodeorg, sum(a.jumlah) as hasil, b.tanggaltanam, ROUND(DATEDIFF(LAST_DAY('".$periode."-15'), b.tanggaltanam)/30,2) as umurbibit FROM ".$dbname.".bibitan_mutasi a
    LEFT JOIN ".$dbname.".bibitan_batch b ON a.batch=b.batch
    WHERE a.kodeorg like '".$unit."%' and b.tanggaltanam < '".$periode."-01' and a.kodeorg LIKE '%pn%'
    GROUP BY a.batch, a.kodeorg, b.tanggaltanam
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if($res['umurbibit']>3)$empat2+=$res['hasil'];else
    $empat1+=$res['hasil'];
}  

// umur mn
$str="SELECT a.batch, a.kodeorg, sum(a.jumlah) as hasil, b.tanggaltanam, ROUND(DATEDIFF(LAST_DAY('".$periode."-15'), b.tanggaltanam)/30,2) as umurbibit FROM ".$dbname.".bibitan_mutasi a
    LEFT JOIN ".$dbname.".bibitan_batch b ON a.batch=b.batch
    WHERE a.kodeorg like '".$unit."%' and b.tanggaltanam < '".$periode."-01' and a.kodeorg LIKE '%mn%'
    GROUP BY a.batch, a.kodeorg, b.tanggaltanam
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if($res['umurbibit']>14)$empat7+=$res['hasil'];else
    if($res['umurbibit']>12)$empat6+=$res['hasil'];else
    if($res['umurbibit']>9)$empat5+=$res['hasil'];else
    if($res['umurbibit']>6)$empat4+=$res['hasil'];else
    $empat3+=$res['hasil'];
}  

$empattotal=$empat1+$empat2+$empat3+$empat4+$empat5+$empat6+$empat7;

function numberformat($qwe,$asd)
{
    if($qwe==0)$zxc='0'; 
    else{
        $zxc=number_format($qwe,$asd);
    }
    return $zxc;
}        

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=2 align=left><font size=3>16. AREAL STATEMENT ".strtoupper($_SESSION['lang']['pembibitan'])."</font></td>
        <td colspan=4 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=6 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>   
</table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=3 ".$bg.">No.</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['pekerjaan']."</td>
    <td align=center colspan=4 ".$bg.">".$_SESSION['lang']['tahun']."</td>
    </tr>
    <tr>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['tahun']." ".$_SESSION['lang']['lalu']."</td>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['tahun']." ".$_SESSION['lang']['ini']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    </thead>
    <tbody>
";
    
$tab.= "<tr class=rowcontent>";
    $tab.= "<td rowspan=5 valign=top align=right>1.</td>";
    $tab.= "<td align=left>Penerimaan Kecambah (Butir)</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>a. Dikirim (sesuai DO)</td>";
    $tab.= "<td align=right>".numberformat($A,0)."</td>";
    $tab.= "<td align=right>".numberformat($B,0)."</td>";
    $tab.= "<td align=right>".numberformat($C,0)."</td>";
    $tab.= "<td align=right>".numberformat($D,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>b. Diterima di Kebun</td>";
    $tab.= "<td align=right>".numberformat($E,0)."</td>";
    $tab.= "<td align=right>".numberformat($F,0)."</td>";
    $tab.= "<td align=right>".numberformat($G,0)."</td>";
    $tab.= "<td align=right>".numberformat($H,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>c. Afkir (seleksi)</td>";
    $tab.= "<td align=right>".numberformat($I,0)."</td>";
    $tab.= "<td align=right>".numberformat($J,0)."</td>";
    $tab.= "<td align=right>".numberformat($K,0)."</td>";
    $tab.= "<td align=right>".numberformat($L,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>d. Tanam di PN</td>";
    $tab.= "<td align=right>".numberformat($M,0)."</td>";
    $tab.= "<td align=right>".numberformat($N,0)."</td>";
    $tab.= "<td align=right>".numberformat($O,0)."</td>";
    $tab.= "<td align=right>".numberformat($P,0)."</td>";
$tab.= "</tr>";

$tab.= "<tr class=rowcontent>";
    $tab.= "<td rowspan=10 valign=top align=right>2.</td>";
    $tab.= "<td align=left>Pre Nursery (Bibit/Butir)</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>a. Saldo Awal</td>";
    $tab.= "<td align=right>".numberformat($Q,0)."</td>";
    $tab.= "<td align=right>".numberformat($R,0)."</td>";
    $tab.= "<td align=right>".numberformat($S,0)."</td>";
    $tab.= "<td align=right>".numberformat($T,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>b. Biji kecambah yang ditanam</td>";
    $tab.= "<td align=right>".numberformat($U,0)."</td>";
    $tab.= "<td align=right>".numberformat($V,0)."</td>";
    $tab.= "<td align=right>".numberformat($W,0)."</td>";
    $tab.= "<td align=right>".numberformat($X,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>c. Penanaman kecambah bibit sendiri / penerimaan dari PN lain</td>";
    $tab.= "<td align=right>".numberformat($Y,0)."</td>";
    $tab.= "<td align=right>".numberformat($Z,0)."</td>";
    $tab.= "<td align=right>".numberformat($AA,0)."</td>";
    $tab.= "<td align=right>".numberformat($AB,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>d. Bibit double tone</td>";
    $tab.= "<td align=right>".numberformat($AC,0)."</td>";
    $tab.= "<td align=right>".numberformat($AD,0)."</td>";
    $tab.= "<td align=right>".numberformat($AE,0)."</td>";
    $tab.= "<td align=right>".numberformat($AF,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>e. Afkir (seleksi)</td>";
    $tab.= "<td align=right>".numberformat($AG,0)."</td>";
    $tab.= "<td align=right>".numberformat($AH,0)."</td>";
    $tab.= "<td align=right>".numberformat($AI,0)."</td>";
    $tab.= "<td align=right>".numberformat($AJ,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>f. Dipindahkan ke kebun lain</td>";
    $tab.= "<td align=right>".numberformat($AK,0)."</td>";
    $tab.= "<td align=right>".numberformat($AL,0)."</td>";
    $tab.= "<td align=right>".numberformat($AM,0)."</td>";
    $tab.= "<td align=right>".numberformat($AN,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>g. Dipindahkan ke Main Nursery</td>";
    $tab.= "<td align=right>".numberformat($AO,0)."</td>";
    $tab.= "<td align=right>".numberformat($AP,0)."</td>";
    $tab.= "<td align=right>".numberformat($AQ,0)."</td>";
    $tab.= "<td align=right>".numberformat($AR,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>h. Dipindahkan ke lapangan (kebun sendiri)</td>";
    $tab.= "<td align=right>".numberformat($AS,0)."</td>";
    $tab.= "<td align=right>".numberformat($AT,0)."</td>";
    $tab.= "<td align=right>".numberformat($AU,0)."</td>";
    $tab.= "<td align=right>".numberformat($AV,0)."</td>";
$tab.= "</tr>";

$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>i. Sisa bibit</td>";
    $tab.= "<td align=right>".numberformat($QAS,0)."</td>";
    $tab.= "<td align=right>".numberformat($RAT,0)."</td>";
    $tab.= "<td align=right>".numberformat($SAU,0)."</td>";
    $tab.= "<td align=right>".numberformat($TAV,0)."</td>";
$tab.= "</tr>";

$tab.= "<tr class=rowcontent>";
    $tab.= "<td rowspan=9 valign=top align=right>3.</td>";
    $tab.= "<td align=left>Main Nursery (Pokok)</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
    $tab.= "<td align=right>&nbsp;</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>a. Saldo Awal</td>";
    $tab.= "<td align=right>".numberformat($M1,0)."</td>";
    $tab.= "<td align=right>".numberformat($M2,0)."</td>";
    $tab.= "<td align=right>".numberformat($M3,0)."</td>";
    $tab.= "<td align=right>".numberformat($M4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>b. Pindahan dari Pre Nursery</td>";
    $tab.= "<td align=right>".numberformat($N1,0)."</td>";
    $tab.= "<td align=right>".numberformat($N2,0)."</td>";
    $tab.= "<td align=right>".numberformat($N3,0)."</td>";
    $tab.= "<td align=right>".numberformat($N4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>c. Penanaman kecambah bibit sendiri / penerimaan dari PN lain</td>";
    $tab.= "<td align=right>".numberformat($O1,0)."</td>";
    $tab.= "<td align=right>".numberformat($O2,0)."</td>";
    $tab.= "<td align=right>".numberformat($O3,0)."</td>";
    $tab.= "<td align=right>".numberformat($O4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>d. Bibit double tone</td>";
    $tab.= "<td align=right>".numberformat($P1,0)."</td>";
    $tab.= "<td align=right>".numberformat($P2,0)."</td>";
    $tab.= "<td align=right>".numberformat($P3,0)."</td>";
    $tab.= "<td align=right>".numberformat($P4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>e. Afkir (seleksi)</td>";
    $tab.= "<td align=right>".numberformat($Q1,0)."</td>";
    $tab.= "<td align=right>".numberformat($Q2,0)."</td>";
    $tab.= "<td align=right>".numberformat($Q3,0)."</td>";
    $tab.= "<td align=right>".numberformat($Q4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>f. Pengiriman ke kebun lain</td>";
    $tab.= "<td align=right>".numberformat($R1,0)."</td>";
    $tab.= "<td align=right>".numberformat($R2,0)."</td>";
    $tab.= "<td align=right>".numberformat($R3,0)."</td>";
    $tab.= "<td align=right>".numberformat($R4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>g. Dipindahkan ke divisi (kebun sendiri)</td>";
    $tab.= "<td align=right>".numberformat($S1,0)."</td>";
    $tab.= "<td align=right>".numberformat($S2,0)."</td>";
    $tab.= "<td align=right>".numberformat($S3,0)."</td>";
    $tab.= "<td align=right>".numberformat($S4,0)."</td>";
$tab.= "</tr>";

$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>h. Sisa bibit</td>";
    $tab.= "<td align=right>".numberformat($M1S1,0)."</td>";
    $tab.= "<td align=right>".numberformat($M2S2,0)."</td>";
    $tab.= "<td align=right>".numberformat($M3S3,0)."</td>";
    $tab.= "<td align=right>".numberformat($M4S4,0)."</td>";
$tab.= "</tr>";

$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=center>Total Bibit</td>";
    $tab.= "<td align=right>".numberformat($tobi1,0)."</td>";
    $tab.= "<td align=right>".numberformat($tobi2,0)."</td>";
    $tab.= "<td align=right>".numberformat($tobi3,0)."</td>";
    $tab.= "<td align=right>".numberformat($tobi4,0)."</td>";
$tab.= "</tr>";
$tab.="</tbody></table>";

$tab.="Klasifikasi Umur<br>";
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable>
    <thead class=rowheader>
    <tr>
    <td align=right ".$bg.">4.</td>
    <td align=center".$bg.">".$_SESSION['lang']['umur']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['jumlah']."</td>
    </tr>
    </thead>
    <tbody>
";
    
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>1. PN 0-3 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat1,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>2. PN > 3 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat2,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>3. MN 3-6 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat3,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>4. MN 6-9 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat4,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>5. MN 9-12 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat5,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>6. MN 12-14 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat6,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=left>7. MN > 14 bulan</td>";
    $tab.= "<td align=right>".numberformat($empat7,0)."</td>";
$tab.= "</tr>";
$tab.= "<tr class=rowcontent>";
    $tab.= "<td align=left>&nbsp;</td>";
    $tab.= "<td align=center>Total</td>";
    $tab.= "<td align=right>".numberformat($empattotal,0)."</td>";
$tab.= "</tr>";

$tab.="</tbody></table>";
			
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Fields required");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field required");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="lbm_aresta_bibit_".$unit.$periode;
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

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;
            $wkiri=50;
            $wlain=11;

    class PDF extends FPDF {
        function Header() {
            global $periode;
            global $unit;
            global $optNm;
            global $optBulan;
            global $tahun;
            global $bulan;
            global $dbname;
            global $luas;
            global $wkiri, $wlain;
                $width = $this->w - $this->lMargin - $this->rMargin;

            $height = 18;
            $this->SetFillColor(220,220,220);
            $this->SetFont('Arial','B',12);

            $this->Cell($width/2,$height,'16. AREAL STATEMENT '.strtoupper($_SESSION['lang']['pembibitan']),NULL,0,'L',1);
            $this->Cell($width/2,$height,$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun,NULL,0,'R',1);
            $this->Ln();
            $this->Cell($width,$height,$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")",NULL,0,'L',1);
            $this->Ln();
            $this->Ln();

            $height = 12;
            $this->SetFont('Arial','B',10);
            $this->Cell(5/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell($wkiri/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell($wlain*4/100*$width,$height,$_SESSION['lang']['tahun'],1,0,'C',1);	
            $this->Ln();
            $this->Cell(5/100*$width,$height,'No.',RL,0,'C',1);	
            $this->Cell($wkiri/100*$width,$height,$_SESSION['lang']['pekerjaan'],RL,0,'C',1);	
            $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['tahun'].' '.$_SESSION['lang']['lalu'],1,0,'C',1);	
            $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['tahun'].' '.$_SESSION['lang']['ini'],1,0,'C',1);	
            $this->Ln();
            $this->Cell(5/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
            $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
            $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
            $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
            $this->Ln();
        }
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    //================================

    $pdf=new PDF('L','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 12;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',10);
    
    $pdf->Cell(5/100*$width,$height,'1.',TRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'Penerimaan Kecambah (Butir)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'a. Dikirim (Sesuai DO)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($A,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($B,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($C,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($D,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'b. Diterima di kebun',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($E,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($F,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($G,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($H,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'c. Afkir (Seleksi)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($I,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($J,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($K,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($L,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',BRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'d. Ditanam di PN',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($N,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($O,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($P,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'2.',TRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'Pre Nursery (Bibit/Butir)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'a. Saldo Awal',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Q,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($R,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($S,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($T,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'b. Biji kecambah yang ditanam',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($U,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($V,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($W,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($X,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'c. Penanaman kecambah bibit sendiri / penerimaan dari PN bibitan lain',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Y,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Z,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AA,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AB,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'d. Bibit double tone',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AC,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AD,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AE,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AF,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'e. Afkir (Seleksi)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AG,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AH,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AI,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AJ,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'f. Dipindahkan ke kebun lain',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AK,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AL,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AM,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AN,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'g. Dipindahkan ke Main Nursery',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AO,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AP,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AQ,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AR,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'h. Dipindahkan ke lapangan (Kebun sendiri)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AS,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AT,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AU,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($AV,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',BRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'i. Sisa bibit',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($QAS,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($RAT,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($SAU,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($TAV,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'3.',TRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'Main Nursery (Pokok)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'a. Saldo Awal',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'b. Pindahan dari Pre Nursery',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($N1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($N2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($N3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($N4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'c. Saldo Awal',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($O1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($O2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($O3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($O4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'d. Bibit double tone',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($P1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($P2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($P3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($P4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'e. Afkir (Seleksi)',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Q1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Q2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Q3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($Q4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'f. Pengiriman ke kebun lain',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($R1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($R2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($R3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($R4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',RL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'g. Pengiriman ke divisi kebun sendiri',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($S1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($S2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($S3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($S4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',BRL,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'h. Sisa Bibit',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M1S1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M2S2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M3S3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($M4S4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wkiri/100*$width,$height,'Total Bibit',1,0,'C',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($tobi1,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($tobi2,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($tobi3,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($tobi4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell($wlain*2/100*$width+5,$height,'Klasifikasi Umur',TB,0,'L',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'4.',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,$_SESSION['lang']['umur'],1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'1. PN 0-3 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat1,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'2. PN > 3 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat2,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'3. MN 3-6 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat3,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'4. PN 6-9 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat4,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'5. MN 9-12 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat5,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'6. MN 12-14 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat6,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'7. PN > 14 bulan',1,0,'L',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empat7,0),1,0,'R',1);	
    $pdf->Ln();
    $pdf->Cell(5/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'Total',1,0,'C',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($empattotal,0),1,0,'R',1);	
    $pdf->Ln();
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>

<?php 
// file creator: dhyaz aug 3, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$unit=$_POST['unit'];
$periode=$_POST['periode'];
$jurnaldari=$_POST['jurnaldari'];
$jurnalsampai=$_POST['jurnalsampai'];

//check, one-two
if($unit==''){
    echo "WARNING: Unitcode is obligatory."; exit;
}
if($periode==''){
    echo "WARNING: Period is obligatory."; exit;
}
if($jurnaldari==''){
    echo "WARNING: Please choose (Journal from)."; exit;
}
if($jurnalsampai==''){
    echo "WARNING: Please choose (Journal until)."; exit;
}
if($jurnalsampai<$jurnaldari){
    echo "WARNING: Journal from must smaller than Journal until."; exit;
}

if($_SESSION['language']=='EN'){
    $str="select noakun,namaakun1 as namaakun from ".$dbname.".keu_5akun
    where level = '5'
    order by noakun";
}else{
    $str="select noakun,namaakun from ".$dbname.".keu_5akun
    where level = '5'
    order by noakun";
}

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namaakun[$bar->noakun]=$bar->namaakun;
}

$whereunit='';
// ambil periode
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$periode."' and kodeorg='".$unit."'";
$per=fetchData($str);

// ambil data
$isidata=array();
$str="select * from ".$dbname.".keu_jurnaldt_vw where nojurnal not like '%CLSM%' and kodeorg = '".$unit."'
      and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'
      and nojurnal >= '".$jurnaldari."' and nojurnal <= '".$jurnalsampai."' ".$whereunit." order by nojurnal";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $qwe=$bar->nojurnal.$bar->noakun.$bar->nourut;
    $isidata[$qwe][nojur]=$bar->nojurnal;
    $isidata[$qwe][nouru]=$bar->nourut;
    $isidata[$qwe][noaku]=$bar->noakun;
    $isidata[$qwe][keter]=$bar->keterangan;
    $isidata[$qwe][jumla]=$bar->jumlah;
}

    if(!empty($isidata)) foreach($isidata as $c=>$key) {
        $sort_nojur[] = $key['nojur'];
        $sort_nouru[] = $key['nouru'];
    }else{
        echo "No data found.";
        exit;
    }

    array_multisort($sort_nojur, SORT_ASC, $sort_nouru, SORT_ASC, $isidata);

//tampil data
$no=0;
$totaldebet=0;
$totalkredit=0;
$grandtotaldebet=0;
$grandtotalkredit=0;
$grandtotalselisih=0;
foreach($isidata as $baris)
{
    if(($jurnalaktif<>$baris[nojur])and($jurnalaktif<>'')){ // akun sekarang beda sama akun sebelumnya?
        echo"<tr class=rowtitle>";
        echo"<td align=right colspan=4>Total</td>";
        echo"<td align=right>".number_format($totaldebet)."</td>";
        echo"<td align=right>".number_format(-1*$totalkredit)."</td>";
        echo"<td align=right>".number_format($selisih)."</td>";
        echo"</tr>";
        $grandtotaldebet+=$totaldebet;
        $grandtotalkredit+=$totalkredit;
        $grandtotalselisih+=$selisih;
        $totaldebet=0;
        $totalkredit=0;
        $selisih=0;
    }
    echo"<tr class=rowcontent>";
    echo"<td>".$baris[nojur]."</td>";
    echo"<td>".$baris[noaku]."</td>";
    echo"<td>".$namaakun[$baris[noaku]]."</td>";
    echo"<td>".$baris[keter]."</td>";
    if($baris[jumla]>0){
        echo"<td align=right>".number_format($baris[jumla])."</td>";
        echo"<td align=right></td>";
        $totaldebet+=$baris[jumla];
    }else{
        echo"<td align=right></td>";
        echo"<td align=right>".number_format(-1*$baris[jumla])."</td>";
        $totalkredit-=$baris[jumla];
    }
    $selisih+=$baris[jumla];    
    echo"<td align=right>".number_format($selisih)."</td>";
    echo"</tr>";
    $jurnalaktif=$baris[nojur];
}
if(($jurnalaktif<>'')){ // akun sekarang beda sama akun sebelumnya?
    echo"<tr class=rowtitle>";
    echo"<td align=right colspan=4>Total</td>";
    echo"<td align=right>".number_format($totaldebet)."</td>";
    echo"<td align=right>".number_format(-1*$totalkredit)."</td>";
    echo"<td align=right>".number_format($selisih)."</td>";
    echo"</tr>";
    $grandtotaldebet+=$totaldebet;
    $grandtotalkredit+=$totalkredit;
    $grandtotalselisih+=$selisih;
}
echo"<tr class=rowtitle>";
echo"<td align=right colspan=4>Grand Total</td>";
echo"<td align=right>".number_format($grandtotaldebet)."</td>";
echo"<td align=right>".number_format(-1*$grandtotalkredit)."</td>";
echo"<td align=right>".number_format($grandtotalselisih)."</td>";
echo"</tr>";

?>

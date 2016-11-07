<?php 
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$unit=$_GET['unit'];
$periode=$_GET['periode'];
$jurnaldari=$_GET['jurnaldari'];
$jurnalsampai=$_GET['jurnalsampai'];
        
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

$stream=$_SESSION['lang']['laporanperiksajurnal']."<br />".$unit." ".$periode;    
    
$stream.="
<table border=1>
<thead>
<tr bgcolor='#dedede'>
    <td align=center>".$_SESSION['lang']['nojurnal']."</td>
    <td align=center>".$_SESSION['lang']['noakun']."</td>
    <td align=center>".$_SESSION['lang']['namaakun']."</td>
    <td align=center>".$_SESSION['lang']['keterangan']."</td>
    <td align=center>".$_SESSION['lang']['debet']."</td>
    <td align=center>".$_SESSION['lang']['kredit']."</td>
    <td align=center>".$_SESSION['lang']['selisih']."</td>
</tr>  
</thead>
<tbody id=container>";
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
        $stream.="<tr bgcolor='#dedede'>";
        $stream.="<td align=right colspan=4>Total</td>";
        $stream.="<td align=right>".number_format($totaldebet)."</td>";
        $stream.="<td align=right>".number_format(-1*$totalkredit)."</td>";
        $stream.="<td align=right>".number_format($selisih)."</td>";
        $stream.="</tr>";
        $grandtotaldebet+=$totaldebet;
        $grandtotalkredit+=$totalkredit;
        $grandtotalselisih+=$selisih;
        $totaldebet=0;
        $totalkredit=0;
        $selisih=0;
    }
    $stream.="<tr class=rowcontent>";
    $stream.="<td>".$baris[nojur]."</td>";
    $stream.="<td>".$baris[noaku]."</td>";
    $stream.="<td>".$namaakun[$baris[noaku]]."</td>";
    $stream.="<td>".$baris[keter]."</td>";
    if($baris[jumla]>0){
        $stream.="<td align=right>".number_format($baris[jumla])."</td>";
        $stream.="<td align=right></td>";
        $totaldebet+=$baris[jumla];
    }else{
        $stream.="<td align=right></td>";
        $stream.="<td align=right>".number_format(-1*$baris[jumla])."</td>";
        $totalkredit-=$baris[jumla];
    }
    $selisih+=$baris[jumla];    
    $stream.="<td align=right>".number_format($selisih)."</td>";
    $stream.="</tr>";
    $jurnalaktif=$baris[nojur];
}
if(($jurnalaktif<>'')){ // akun sekarang beda sama akun sebelumnya?
    $stream.="<tr bgcolor='#dedede'>";
    $stream.="<td align=right colspan=4>Total</td>";
    $stream.="<td align=right>".number_format($totaldebet)."</td>";
    $stream.="<td align=right>".number_format(-1*$totalkredit)."</td>";
    $stream.="<td align=right>".number_format($selisih)."</td>";
    $stream.="</tr>";
    $grandtotaldebet+=$totaldebet;
    $grandtotalkredit+=$totalkredit;
    $grandtotalselisih+=$selisih;
}
$stream.="<tr bgcolor='#dedede'>";
$stream.="<td align=right colspan=4>Grand Total</td>";
$stream.="<td align=right>".number_format($grandtotaldebet)."</td>";
$stream.="<td align=right>".number_format(-1*$grandtotalkredit)."</td>";
$stream.="<td align=right>".number_format($grandtotalselisih)."</td>";
$stream.="</tr>";

$stream.="</tbody>
<tfoot>
</tfoot>		 
</table>";
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");
$nop_="PeriksaJurnal_".$unit.$periode." ".$qwe;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>
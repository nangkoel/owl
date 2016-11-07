<?php 
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$pam=$_POST['pam'];
$unit=$_POST['unit'];
$periode=$_POST['periode'];
$hasil='';

//ambil no jurnal
if($pam==1){
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode."' and kodeorg='".$unit."'";
    $per=fetchData($str);
    $str="select distinct nojurnal from ".$dbname.".keu_jurnaldt_vw
        where nojurnal not like '%CLSM%' and kodeorg = '".$unit."' and tanggal between '".$per[0]['tanggalmulai']."' and '".$per[0]['tanggalsampai']."'
        order by nojurnal";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $hasil.="<option value='".$bar->nojurnal."'>".$bar->nojurnal."</option>";
    }
    $hasil.='<option value=""></option>';
echo $hasil;
}

//cek tanggal mulai harus 1
if($pam==2){
	$tanggal1=$_POST['tanggal1'];
	$tanggal2=$_POST['tanggal2'];
    $qwe=explode("-",$tanggal1);
    $tanggal1=$qwe[0];
    if($tanggal1!='01')
    echo "WARNING: Tanggal Mulai harus 01."; 
}

//cek tanggal sampai harus lebih
if($pam==3){
	$tanggal1=$_POST['tanggal1'];
	$tanggal2=$_POST['tanggal2'];
    if($tanggal1==''){
        echo "WARNING: Silakan memilih Tanggal Mulai."; exit;
    }
    $qwe=explode("-",$tanggal1);
    $tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
    $qwe=explode("-",$tanggal2);
    $tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];
    if($tanggal2<$tanggal1)
        echo "WARNING: Sampai Tanggal tidak bisa lebih kecil dari Tanggal Mulai."; 
}

?>
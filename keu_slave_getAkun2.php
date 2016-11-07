<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pam=$_POST['pam'];
        $hasil='';

//ambil akun2
if($pam==1){
	$akun=$_POST['akun'];
        $str="select noakun,namaakun from ".$dbname.".keu_5akun
                        where level = '5' and noakun>= '".$akun."'
                        order by noakun
                        ";
        $res=mysql_query($str);
        $hasil="";
        while($bar=mysql_fetch_object($res))
        {
                $hasil.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
        }
echo $hasil;
}

//cek tanggal mulai harus 1
if($pam==2){
	$tanggal1=$_POST['tanggal1'];
	$tanggal2=$_POST['tanggal2'];
    $qwe=explode("-",$tanggal1);
    $tanggal1=$qwe[0];
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
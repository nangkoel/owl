<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pam=$_POST['pam'];
        $hasil='';

//ambil akun2
if($pam==1){
        $str="select tahunbudget from ".$dbname.".bgt_upah
            where kodeorg = '".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                  group by tahunbudget order by tahunbudget";
        $res=mysql_query($str);
        $opttahun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        while($bar=mysql_fetch_object($res))
        {
                $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";

        }
echo $opttahun;
}

//cek tanggal mulai harus 1
if($pam==2){
	$tanggal1=$_POST['tanggal1'];
	$tanggal2=$_POST['tanggal2'];
    $qwe=explode("-",$tanggal1);
    $tanggal1=$qwe[0];
    if($tanggal1!='01')
    echo "WARNING: Date must begin from 01."; 
}

//cek tanggal sampai harus lebih
if($pam==3){
	$tanggal1=$_POST['tanggal1'];
	$tanggal2=$_POST['tanggal2'];
    if($tanggal1==''){
        echo "WARNING: Starting date required."; exit;
    }
    $qwe=explode("-",$tanggal1);
    $tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];
    $qwe=explode("-",$tanggal2);
    $tanggal2=$qwe[2]."-".$qwe[1]."-".$qwe[0];
    if($tanggal2<$tanggal1)
        echo "WARNING: Ending date must greater than starting date."; 
}

?>
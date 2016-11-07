<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

$organisasi=$_POST['organisasi'];
$regional=$_POST['regional'];
//$arrEnum=getEnum($dbname,'bgt_tipe','tipe,nama');

    $str="select * from ".$dbname.".bgt_regional_assignment 
    where kodeunit='".$organisasi."' 
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $sudahtutup="1";
        $pesan=$bar->kodeunit." - ".$bar->regional;
    }
    if($sudahtutup=="1"){
        echo "data sudah ada: ".$pesan; exit;
    }

?>

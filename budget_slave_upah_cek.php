<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=trim($_POST['kodeorg']);
$what=$_POST['what'];

if($what=='adadata'){
    $str="select * from ".$dbname.".bgt_upah 
    where tahunbudget='".$tahunbudget."' and kodeorg = '".$kodeorg."' 
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $adadata="1";	
    }
    if($adadata=="1"){
        echo "Data already exist on the same perio, replace..?"; exit;
    }
}

if($what=='closing'){
    $str="select * from ".$dbname.".bgt_upah 
    where tahunbudget='".$tahunbudget."' and kodeorg = '".$kodeorg."' 
        and closed = 1
            limit 0,1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $sudahtutup="1";	
    }
    if($sudahtutup=="1"){
        echo "Budget for this period has been closed"; exit;
    }
}

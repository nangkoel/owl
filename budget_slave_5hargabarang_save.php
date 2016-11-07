<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$what=$_POST['what'];
$tahunbudget=$_POST['tahunbudget'];
$regional=$_POST['regional'];
$sumberharga=$_POST['sumberharga'];
$kodebarang=$_POST['kodebarang'];
$hargasatuan=$_POST['hargasatuan'];
$variant=$_POST['variant'];
$hargalalu=$_POST['hargalalu'];

if($what=='update')
{
    $str="UPDATE ".$dbname.".bgt_masterbarang SET `hargasatuan` = '".$hargasatuan."', `sumberharga` = '".$sumberharga."',
        `hargalalu` = '".$hargalalu."', `variant` = '".$variant."', `updateby` = '".$_SESSION['standard']['userid']."', 
        `lastupdate` = CURRENT_TIMESTAMP 
        WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."' AND `kodebarang` = '".$kodebarang."'";
    if(mysql_query($str))
    {} else
    {
        echo " Gagal9,".addslashes(mysql_error($conn));
        exit;
    }	
}else
if($what=='edit')
{
$str="select * from ".$dbname.".bgt_masterbarang where "
     . "tahunbudget='".$tahunbudget."' and regional='".$regional."' "
     . "and kodebarang='".$kodebarang."' and hargasatuan=0 and closed=1";
$adadata=false;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $adadata=true;	
}
//$str="select closed from ".$dbname.".bgt_masterbarang where tahunbudget='".$tahunbudget."' and regional='".$regional."'
//      ";
//$tutupdata=0;
//$res=mysql_query($str);
//while($bar=mysql_fetch_object($res)){
//    if($bar->closed=='1')$tutupdata=1;	
//}
//    
    
    if($adadata==true){
        $str="UPDATE ".$dbname.".bgt_masterbarang SET `hargasatuan` = '".$hargasatuan."',
        `updateby` = '".$_SESSION['standard']['userid']."', 
        `lastupdate` = CURRENT_TIMESTAMP 
        WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."' AND `kodebarang` = '".$kodebarang."'";
    }else if($adadata==false){
        $str="INSERT INTO ".$dbname.".`bgt_masterbarang` (
        `regional` ,
        `tahunbudget` ,
        `kodebarang` ,
        `hargasatuan` ,
        `updateby` ,
        `closed` ,
        `lastupdate`
        )
        VALUES (
        '".$regional."', '".$tahunbudget."', '".$kodebarang."', '".$hargasatuan."', '".$_SESSION['standard']['userid']."', '".$tutupdata."',
        CURRENT_TIMESTAMP 
        )";
    }
    if(mysql_query($str))
    {} else
    {
        echo " Gagal9,".addslashes(mysql_error($conn));
        exit;
    }	
}else{
    // hanya tambahkan yang harganya diinput (hargasatuan > 0)
    if ($hargasatuan>0){
    $str="DELETE FROM ".$dbname.".bgt_masterbarang WHERE tahunbudget='".$tahunbudget."' AND regional='".$regional."' AND kodebarang='".$kodebarang."'";
    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
        exit;
    }	

        $str="INSERT INTO ".$dbname.".`bgt_masterbarang` (
        `regional` ,
        `tahunbudget` ,
        `kodebarang` ,
        `hargasatuan` ,
        `sumberharga` ,
        `variant` ,
        `updateby` ,
        `lastupdate` ,
        `hargalalu`
        )
        VALUES (
        '".$regional."', '".$tahunbudget."', '".$kodebarang."', '".$hargasatuan."', '".$sumberharga."' , '".$variant."', '".$_SESSION['standard']['userid']."',
        CURRENT_TIMESTAMP , '".$hargalalu."'
        )";

        if(mysql_query($str))
        {} else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
    }

}
?>

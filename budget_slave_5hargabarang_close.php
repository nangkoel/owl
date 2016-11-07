<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$regional=$_POST['regional'];
$kodebarang=$_POST['kodebarang'];

$str="UPDATE ".$dbname.".`bgt_masterbarang` 
SET `closed` = '1'
WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."'";
//WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."' AND `kodebarang` = '".$kodebarang."'";
if(mysql_query($str))
{} else {
    echo " Gagal,".addslashes(mysql_error($conn));
}	

?>

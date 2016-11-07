<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
$kodegolongan=$_POST['kodegolongan'];
$kodeupah=$_POST['kodeupah'];

$str="UPDATE ".$dbname.".`bgt_upah` 
SET `closed` = '1'
WHERE `kodeorg` = '".$kodeorg."' AND `tahunbudget` = '".$tahunbudget."' AND `golongan` = '".$kodegolongan."'";
if(mysql_query($str))
{} else {
    echo " Gagal,".addslashes(mysql_error($conn));
}	

?>

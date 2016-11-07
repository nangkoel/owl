<?php
// file creator: dhyaz aug 10, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahunbudget=$_POST['tahunbudget'];
$kodeorg=$_POST['kodeorg'];
$kodegolongan=$_POST['kodegolongan'];
$upah=$_POST['upah'];

$str="DELETE FROM ".$dbname.".bgt_upah WHERE tahunbudget='".$tahunbudget."' AND kodeorg='".$kodeorg."' AND golongan='".$kodegolongan."'";
if(mysql_query($str))
{} else
{
    echo " Gagal,".addslashes(mysql_error($conn));
}	

$str="INSERT INTO ".$dbname.".`bgt_upah` (
`tahunbudget` ,
`kodeorg` ,
`golongan` ,
`jumlah` ,
`updateby` ,
`lastupdate`
)
VALUES (
'".$tahunbudget."', '".$kodeorg."', '".$kodegolongan."', '".$upah."' , '".$_SESSION['standard']['userid']."',
CURRENT_TIMESTAMP 
)";


if(mysql_query($str))
{} else
{
    echo " Gagal,".addslashes(mysql_error($conn));
}	
?>

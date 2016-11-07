<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$karyawanid=$_POST['karid'];

$str="select * from ".$dbname.".datakaryawan where karyawanid=".$karyawanid ." limit 1";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    echo"<?xml version='1.0' ?>
     <karyawan>
         <tipekaryawan>".($bar->tipekaryawan!=""?$bar->tipekaryawan:"*")."</tipekaryawan>
         <kodejabatan>".($bar->kodejabatan!=""?$bar->kodejabatan:"*")."</kodejabatan>
         <kodegolongan>".($bar->kodegolongan!=""?$bar->kodegolongan:"*")."</kodegolongan>
         <lokasitugas>".($bar->lokasitugas!=""?$bar->lokasitugas:"*")."</lokasitugas>
         <bagian>".($bar->bagian!=""?$bar->bagian:"*")."</bagian>    
     </karyawan>";	
}
?>
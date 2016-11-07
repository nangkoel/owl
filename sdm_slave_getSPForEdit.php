<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$nosp=$_POST['nosp'];
$karyawanid=$_POST['karyawanid'];

$str="select * from ".$dbname.".sdm_suratperingatan
      where karyawanid=".$karyawanid ." and nomor='".$nosp."'";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        echo"<?xml version='1.0' ?>
             <sp>
                         <jenissp>".($bar->jenissp!=""?$bar->jenissp:"*")."</jenissp>
                         <karyawanid>".($bar->karyawanid!=""?$bar->karyawanid:"*")."</karyawanid>
                         <tanggal>".($bar->tanggal!=""?tanggalnormal($bar->tanggal):"*")."</tanggal>
                         <masaberlaku>".($bar->masaberlaku!=""?$bar->masaberlaku:"*")."</masaberlaku>
                         <paragraf1>".($bar->paragraf1!=""?$bar->paragraf1:"*")."</paragraf1>
                         <pelanggaran>".($bar->pelanggaran!=""?$bar->pelanggaran:"*")."</pelanggaran>
                     <paragraf3>".($bar->paragraf3!=""?$bar->paragraf3:"*")."</paragraf3>
                         <paragraf4>".($bar->paragraf4!=""?$bar->paragraf4:"*")."</paragraf4>
                         <penandatangan>".($bar->penandatangan!=""?$bar->penandatangan:"*")."</penandatangan>
                         <jabatan>".($bar->jabatan!=""?$bar->jabatan:"*")."</jabatan>
                         <tembusan1>".($bar->tembusan1!=""?$bar->tembusan1:"*")."</tembusan1>
                         <tembusan2>".($bar->tembusan2!=""?$bar->tembusan2:"*")."</tembusan2>
                         <tembusan3>".($bar->tembusan3!=""?$bar->tembusan3:"*")."</tembusan3>
                         <tembusan4>".($bar->tembusan4!=""?$bar->tembusan4:"*")."</tembusan4>
                         <nomor>".($bar->nomor!=""?$bar->nomor:"*")."</nomor>
                         <verifikasi>".($bar->verifikasi!=""?$bar->verifikasi:"*")."</verifikasi>
                         <dibuat>".($bar->dibuat!=""?$bar->dibuat:"*")."</dibuat>
                         <jabatandibuat>".($bar->jabatandibuat!=""?$bar->jabatandibuat:"*")."</jabatandibuat>
                         <jabatanverifikasi>".($bar->jabatanverifikasi!=""?$bar->jabatanverifikasi:"*")."</jabatanverifikasi>    
                 </sp>";	
}
?>
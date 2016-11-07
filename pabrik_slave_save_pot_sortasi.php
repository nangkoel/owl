<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$str="delete from ".$dbname.".pabrik_5pot_fraksi where 
      kodeorg='".$_POST['kodeorg']."' and  kodefraksi='".$_POST['kode']."'";
mysql_query($str);
$str="insert into ".$dbname.".pabrik_5pot_fraksi (kodeorg,kodefraksi,potongan)
      values('".$_POST['kodeorg']."','".$_POST['kode']."',".$_POST['potongan'].");";
if(mysql_query($str))
{
    $sFraksi="select distinct kode,keterangan,keterangan1 from ".$dbname.".pabrik_5fraksi order by keterangan asc";
    $qFraksi=mysql_query($sFraksi) or die(mysql_error($conn));
    while($rFraksi=  mysql_fetch_assoc($qFraksi)){
        if($_SESSION['language']=='EN'){
           
            $kodeNama[$rFraksi['kode']]=$rFraksi['keterangan1'];
        }else{
           
            $kodeNama[$rFraksi['kode']]=$rFraksi['keterangan'];
        }
    }
    	$str1="select * from ".$dbname.".pabrik_5pot_fraksi where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by kodefraksi";
        $res1=mysql_query($str1);
        while($bar1=mysql_fetch_object($res1))
	{
           echo"<tr class=rowcontent><td>".$kodeNama[$bar1->kodefraksi]."</td><td align=right>".$bar1->potongan."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodefraksi."','".$bar1->potongan."');\"></td></tr>";
	}
}
 else {
    exit("Error:".mysql_error($conn));
}

?>
<?php
//session_start();
$tanggal=$_GET['tanggal'];
$product=$_GET['product'];
$tgl2=substr($tanggal,6,4).'-'.substr($tanggal,3,2).'-'.substr($tanggal,0,2);
   $query="select unitname from ".$dbname.".msunit where unitcode='".$unit."'";
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
?>

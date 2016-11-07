<?php
//session_start();
$tanggal=$_GET['tanggal'];
$unit=$_GET['unit'];
$tgl2=substr($tanggal,6,4).'-'.substr($tanggal,3,2).'-'.substr($tanggal,0,2);
$query="select unitname from ".$dbname.".msunit where unitcode='".$unit."'";
//   echo $query;
   $res=mysql_query($query);
   while($bar=mysql_fetch_array($res)){
   $un=$bar[0];
   }
?>

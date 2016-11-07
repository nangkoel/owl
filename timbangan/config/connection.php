<?php
//$dbserver='192.168.1.5';
$dbserver='localhost';
$dbport  ='3306';
$dbname  ='wbhip26';
$uname   ='root';
$passwd  ='1234';
$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Unable to Connect to database ".$dbserver);
?>

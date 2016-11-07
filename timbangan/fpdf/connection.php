<?php
//$dbserver='192.168.1.5';
$dbserver='localhost';
$dbport  ='3306';
$dbname  ='wbhip25';
$uname   ='root';
$passwd  ='123';
$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Unable to Connect to database ".$dbserver);
?>

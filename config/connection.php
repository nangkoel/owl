<?php
$dbserver='localhost';
$dbport  ='3306';
$dbname  ='owl';
$uname   ='cosa';
$passwd  ='123456';

$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or die("Error/Gagal :Unable to Connect to database ".$dbserver);
?>

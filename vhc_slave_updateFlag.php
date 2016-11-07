<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

$param = $_POST;
$periode=$param['periode'];
$kodetraksi=$_SESSION['empl']['lokasitugas'];
$str="insert into ".$dbname.".vhc_flag_alokasi(kodetraksi,periode) values('".$kodetraksi."','".$periode."')";
if(mysql_query($str))
{
    
}
 else {
     //let error pass 
     // echo " Error ".mysql_error($conn);    
}
?>
<?php
//server jakarta
$dbserver='1.1.1.7';
$dbport  ='3306';
$dbname  ='owl';
$uname   ='production';
$passwd  ='p@ssw0rd123';
$conn3=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or exit(mysql_error($conn3)."Error production connection:".$dbname);

//PKS
$str="select ip,username,password,port,dbname from ".$dbname.".setup_remotetimbangan
      where lokasi='L01M1'";
//	  echo $str;	
$res=mysql_query($str,$conn3);
$idAdd='';
while($bar=mysql_fetch_object($res))
{
    $idAdd=$bar->ip;//ip pks ada di dalam database owl
    $prt=$bar->port;
    $usrName=$bar->username;
    $pswrd=$bar->password;
    $dbnm=$bar->dbname;
}
if($idAdd=='')
{ echo "Error: Koneksi PKS SIL gagal";}
else
  $corn3=mysql_connect($idAdd.":".$prt,$usrName,$pswrd) or exit(mysql_error($corn3).":Could not Connect to remote Computer".$dbnm);
?>

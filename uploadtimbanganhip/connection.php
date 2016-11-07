<?php
//server jakarta
$dbserver='1.1.1.7';
$dbport  ='3306';
$dbname  ='owl';
$uname   ='production';
$passwd  ='p@ssw0rd123';
$conn=mysql_connect($dbserver.":".$dbport,$uname,$passwd) or exit(mysql_error($conn)."Error production connection:".$dbname);

//PKS
$str="select ip,username,password,port,dbname from ".$dbname.".setup_remotetimbangan
      where lokasi='H01M1'";
//	  echo $str;	
$res=mysql_query($str,$conn);
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
{ echo "Error: Koneksi PKS gagal";}
else
  $corn=mysql_connect($idAdd.":".$prt,$usrName,$pswrd) or exit(mysql_error($corn).":Could not Connect to remote Computer".$dbnm);
?>

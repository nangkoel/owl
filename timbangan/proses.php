<?php
// membaca file koneksi.php
//include "koneksi.php";
include('config/connection.php');
// membaca tabel-tabel yang dipilih dari form
$tabel = $_POST['tabel'];

// proses untuk menggabung nama-nama tabel yang dipilih
// sehingga menjadi sebuah string berbentuk 'tabel1 tabel2 tabel3 ...'

/*$listTabel = "";
foreach($tabel as $namatabel)
{
  $listTabel .= $namatabel." ";
}*/

// membentuk string command menjalankan mysqldump
// diasumsikan file mysqldump terletak di dalam folder C:\AppServ\MySQL\bin

//$command = "C:\MySQL\bin\mysqldump -u".$uname." -p".$passwd." ".$dbname." ".$listTabel." > ".$dbname.".sql";
//$command = "C:\MySQL\bin\mysqldump -u".$uname." -p".$passwd." ".$dbname."> ".$dbname.".sql";
$nop_=date('d-m-Y_').$dbname;
//$namafile = $a.$dbname;
$command = "D:\datatimbangan\bin\mysqldump -u".$uname." -p".$passwd." ".$dbname."> ".$nop_.".sql";

// perintah untuk menjalankan perintah mysqldump dalam shell melalui PHP
exec($command);

// bagian perintah untuk proses download file hasil backup.

header("Content-Disposition: attachment; filename=".$nop_.".sql");
header("Content-type: application/download");
$fp  = fopen($nop_.".sql", 'r');
$content = fread($fp, filesize($nop_.".sql"));
fclose($fp);

echo $content;

exit;
?>

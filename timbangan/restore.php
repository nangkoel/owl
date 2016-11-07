<?php
// membaca file koneksi.php
//include "koneksi.php";
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('config/connection.php');
echo open_body();

include('master_mainMenu.php');
echo "<div align=center>";
echo "<h1>Restore DataBase</h1>";
echo "<h3>Nama Database: ".$dbname."</h3>";

// form upload file dumo
echo "<form enctype='multipart/form-data' method='post' action='".$_SERVER['PHP_SELF']."?op=restore'>";
echo "<input type='hidden' name='MAX_FILE_SIZE' value='20000000'>
      <input name='datafile' type='file'>
      <input name='submit' type='submit' value='Restore'>";
echo "</form>";
echo "</div>";
// proses restore data
if ($_GET['op'] == "restore")
{
  // baca nama file
  $fileName = $_FILES['datafile']['name'];

  // proses upload file
  move_uploaded_file($_FILES['datafile']['tmp_name'], $fileName);

  // membentuk string command untuk restore
  // di sini diasumsikan letak file mysql.exe terletak di direktori C:\MySQL\bin
  $string = "D:\datatimbangan\bin\mysql -u".$uname." -p".$passwd." ".$dbname." < ".$fileName;

  // menjalankan command restore di shell via PHP
  exec($string);

  // hapus file dump yang diupload
  unlink($fileName);
}

echo close_body();
?>

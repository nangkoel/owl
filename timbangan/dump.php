<?php
// membaca file koneksi.php
//include "koneksi.php";
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('config/connection.php');
echo open_body();

include('master_mainMenu.php');

echo "<table align=center>
	<tr>
		<td><h1>Backup Database</h1> </td>
	</tr>
	<tr>
		<td><h3>Nama Database: ".$dbname."</h3></td>
	</tr>
</table>";
//echo "<h3>Daftar Tabel</h3>";

// query untuk menampilkan semua tabel dalam database
$stx = "SHOW TABLES from ".$dbname."";
$hasil = mysql_query($stx);
echo $query;
// menampilkan semua tabel dalam form
echo "<form method='post' action='proses.php'>";
/*echo "<table>";
while ($data = mysql_fetch_row($hasil))
{
   echo "<tr><td><input type='checkbox' name='tabel[]' value='".$data[0]."'></td><td>".$data[0]."</td></tr>";
}
echo "</table><br>";*/
echo "
	<table align=center>
	<tr>
		<td><input type='submit' name='submit' value='Backup Data' ></td>
	</tr>
	</table>";
//echo "</form>";
echo close_body();
?>

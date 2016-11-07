<?php
// menggunakan class phpExcelReader
include('config/connection.php');
include('excel_reader2.php');

// koneksi ke mysql
mysql_connect("dbHost", "dbUser", "dbPass");
mysql_select_db("dbname");

// membaca file excel yang diupload
$data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);

// membaca jumlah baris dari data excel
$baris = $data->rowcount($sheet_index=0);

// nilai awal counter untuk jumlah data yang sukses dan yang gagal diimport
$sukses = 0;
$gagal = 0;

// import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom)
for ($i=2; $i<=$baris; $i++)
{
  // membaca data nim (kolom ke-1)
  $nim = $data->val($i, 1);
  // membaca data nama (kolom ke-2)
  $nama = $data->val($i, 2);
  // membaca data alamat (kolom ke-3)
  $alamat = $data->val($i, 4);
  $alamat = $data->val($i, 5);
  $alamat = $data->val($i, 6);
  $alamat = $data->val($i, 7);
  $alamat = $data->val($i, 8);

  // setelah data dibaca, sisipkan ke dalam tabel mhs
  $query = "INSERT INTO newwbridge.msproduct VALUES ('$nim', '$nama', '$alamat')";
  $hasil = mysql_query($query);

  // jika proses insert data sukses, maka counter $sukses bertambah
  // jika gagal, maka counter $gagal yang bertambah
  if ($hasil) $sukses++;
  else $gagal++;
}

// tampilan status sukses dan gagal
echo "<h3>Proses import data selesai.</h3>";
echo "<p>Jumlah data yang sukses diimport : ".$sukses."<br>";
echo "Jumlah data yang gagal diimport : ".$gagal."</p>";

?>

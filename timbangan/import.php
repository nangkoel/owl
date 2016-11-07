<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('config/connection.php');
echo open_body();

include('master_mainMenu.php');
?>
<h1>Import Data Product</h1>

<form method="post" enctype="multipart/form-data" action="prosesimportexcel.php">
Silakan Pilih File Excel: <input name="userfile" type="file">
<input name="upload" type="submit" value="Import">
</form>


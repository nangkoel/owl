<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

$afdeling = $_POST['afdeling'];
$where1 = "(tipe='BLOK' or tipe='BIBITAN') and induk='".$afdeling."'";
$blok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where1);

foreach($blok as $key=>$row) {
    echo "kodeorg.options[kodeorg.options.length] = new Option('".$row."','".$key."');";
}
?>
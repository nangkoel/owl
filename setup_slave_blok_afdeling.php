<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once ('lib/zLib.php');

# Make Query
$opt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe in ('AFDELING','BIBITAN') and induk='".$_POST['kebun']."'");
#echo "error";
#print_r($opt);

# Isi Options
echo "var afdeling = document.getElementById('".$_POST['afdelingId']."');";
foreach($opt as $key=>$row) {
    echo "afdeling.options[afdeling.options.length] = new Option('".$row."','".$key."');";
}
?>
<?php
include_once('lib/zLib.php');

# Extract Param
$sNIK = $_POST['nik'];
$sNama = $_POST['nama'];

#=============Get Data===============
# Condition
$where = "";
if($sNIK!='') {
    $where .= "nik like '".$sNIK."'";
}
if($sNama!='') {
    if($where != ''){
        $where .= " AND ";
    }
    $where .= "namakaryawan like '".$sNama."'";
}

# Data
$query = selectQuery($dbname,'datakaryawan',"nik,namakaryawan,kodeorganisasi,bagian,karyawanid",$where);
$data = fetchData($query);

# Header
$header = array();
if($data!=array()) {
    foreach($data[0] as $key=>$row) {
        if($key!='karyawanid') {
            $header[] = $key;
        }
    }
}

#==============Generate Table===============
$table = "<table id='mainTable' name='mainTable' class='sortable' cellspacing='1' border='0'>";

# THEAD
$table .= "<thead><tr class='rowheader'>";
if($data==array()) {
    $table .= "<td>Data Tidak ada</td>";
} else {
    foreach($header as $head) {
        $table .= "<td>".$head."</td>";
    }
}
$table .= "</tr></thead>";

# TBODY
$table .= "<tbody id='mainBody'>";
foreach($data as $key=>$row) {
    $table .= "<tr id='tr_".$key."' class='rowcontent'
        onclick=\"showManage('edit','".$key."',event)\" style='cursor:pointer'>";
    foreach($row as $head=>$content) {
        if($head!='karyawanid') {
            $table .= "<td id='".$head."_".$key."'>".$content."</td>";
        }
    }
    $table .= "<input id='karyawanid_".$key."' type='hidden' value='".$row['karyawanid']."'>";
    $table .= "</tr>";
}
$table .= "</tbody>";

# TFOOT
$table .= "<tfoot>";
$table .= "</tfoot>";

$table .= "</table>";

echo "<a onclick=\"showManage('add','0',event)\" style='cursor:pointer'>Tambah Data</a>";
echo $table;
?>
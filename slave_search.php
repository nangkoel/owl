<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$mode = $_GET['mode'];
# Get POST
$keyword = $_POST['keyword'];
$target = $_POST['target'];
$targetSatuan = $_POST['targetSatuan'];
$targetHarga = $_POST['targetHarga'];

switch($mode) {
    case 'barang':
	# Get Data
	$where = "namabarang like '%".$keyword."%'";// and kodeorg like '".$_SESSION['empl']['kodeorganisasi']."%'";
	$query = "SELECT DISTINCT a.kodebarang,a.namabarang,a.satuan ";//,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ";
	$query .= "FROM ".$dbname.".`log_5masterbarang` a ";
	//$query .= "LEFT OUTER JOIN (".$dbname.".log_5masterbarangdt b) ";
	//$query .= "ON a.kodebarang=b.kodebarang ";
	$query .= "WHERE ".$where." and inactive='0' ";
	$data = fetchData($query);
	
	# Make Table
	$headers = array('Kode','Nama','Satuan','Harga');
	
	$table = "<table>";
	$table .= "<thead><tr class='rowheader'>";
	foreach($headers as $head) {
	    $table .= "<td>".$head."</td>";
	}
	$table .= "</tr></thead>";
	$table .= "<tbody>";
	foreach($data as $key=>$row) {
	    $table .= "<tr id='inv_tr_".$key."' class='rowcontent' ";
	    $table .= "onclick=\"passValue('".$row['kodebarang']."','".$target."');";
	    $table .= "passValue('".$row['namabarang']."','".$target."_name');";
	    $table .= "passValue('".$row['satuan']."','".$targetSatuan."');";
	    $table .= "passValue('".$row['harga']."','".$targetHarga."');";
	    $table .= "\">";
	    foreach($row as $head=>$con) {
		$table .= "<td id='".$head."_".$key."'>".$con."</td>";
	    }
	    $table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "<tfoot></tfoot></table>";
	
	echo $table;
	break;
    case 'kegiatan':
	# Get Data
	$where = "namakegiatan like '%".$keyword."%'";
	$query = selectQuery($dbname,'setup_kegiatan','kelompok,kodekegiatan,namakegiatan',
	    $where);
	$data = fetchData($query);
	
	# Make Table
	$headers = array('Kelompok','Kode Kegiatan','Nama Kegiatan');
	
	$table = "<table>";
	$table .= "<thead><tr class='rowheader'>";
	foreach($headers as $head) {
	    $table .= "<td>".$head."</td>";
	}
	$table .= "</tr></thead>";
	$table .= "<tbody>";
	foreach($data as $key=>$row) {
	    $table .= "<tr id='inv_tr_".$key."' class='rowcontent' ";
	    $table .= "onclick=\"passValue('".$row['kodekegiatan']."','".$target."');";
	    $table .= "passValue('".$row['namakegiatan']."','".$target."_name');\">";
	    foreach($row as $head=>$con) {
		$table .= "<td id='".$head."_".$key."'>".$con."</td>";
	    }
	    $table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "<tfoot></tfoot></table>";
	
	echo $table;
	break;
    case 'asset':
	# Get Data
	$where = "namabarang like '%".$keyword."%'";
	$query = "SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ";
	$query .= "FROM ".$dbname.".`log_5masterbarang` a ";
	$query .= "LEFT OUTER JOIN (".$dbname.".log_5masterbarangdt b) ";
	$query .= "ON a.kodebarang=b.kodebarang ";
	$query .= "WHERE ".$where;
	$data = fetchData($query);
	
	# Make Table
	$headers = array('Kode','Nama','Satuan','Harga');
	
	$table = "<table>";
	$table .= "<thead><tr class='rowheader'>";
	foreach($headers as $head) {
	    $table .= "<td>".$head."</td>";
	}
	$table .= "</tr></thead>";
	$table .= "<tbody>";
	foreach($data as $key=>$row) {
	    $table .= "<tr id='inv_tr_".$key."' class='rowcontent' ";
	    $table .= "onclick=\"passValue('".$row['kodebarang']."','".$target."');";
	    $table .= "passValue('".$row['namabarang']."','".$target."_name');\">";
	    foreach($row as $head=>$con) {
		$table .= "<td id='".$head."_".$key."'>".$con."</td>";
	    }
	    $table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "<tfoot></tfoot></table>";
	
	echo $table;
	break;
    case 'customer':
	# Get Data
	$where = "namabarang like '%".$keyword."%'";
	$query = "SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ";
	$query .= "FROM ".$dbname.".`log_5masterbarang` a ";
	$query .= "LEFT OUTER JOIN (".$dbname.".log_5masterbarangdt b) ";
	$query .= "ON a.kodebarang=b.kodebarang ";
	$query .= "WHERE ".$where;
	$data = fetchData($query);
	
	# Make Table
	$headers = array('Kode','Nama','Satuan','Harga');
	
	$table = "<table>";
	$table .= "<thead><tr class='rowheader'>";
	foreach($headers as $head) {
	    $table .= "<td>".$head."</td>";
	}
	$table .= "</tr></thead>";
	$table .= "<tbody>";
	foreach($data as $key=>$row) {
	    $table .= "<tr id='inv_tr_".$key."' class='rowcontent' ";
	    $table .= "onclick=\"passValue('".$row['kodebarang']."','".$target."');";
	    $table .= "passValue('".$row['namabarang']."','".$target."_name');\">";
	    foreach($row as $head=>$con) {
		$table .= "<td id='".$head."_".$key."'>".$con."</td>";
	    }
	    $table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "<tfoot></tfoot></table>";
	
	echo $table;
	break;
    case 'supplier':
	# Get Data
	$where = "namabarang like '%".$keyword."%'";
	$query = "SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ";
	$query .= "FROM ".$dbname.".`log_5masterbarang` a ";
	$query .= "LEFT OUTER JOIN (".$dbname.".log_5masterbarangdt b) ";
	$query .= "ON a.kodebarang=b.kodebarang ";
	$query .= "WHERE ".$where;
	$data = fetchData($query);
	
	# Make Table
	$headers = array('Kode','Nama','Satuan','Harga');
	
	$table = "<table>";
	$table .= "<thead><tr class='rowheader'>";
	foreach($headers as $head) {
	    $table .= "<td>".$head."</td>";
	}
	$table .= "</tr></thead>";
	$table .= "<tbody>";
	foreach($data as $key=>$row) {
	    $table .= "<tr id='inv_tr_".$key."' class='rowcontent' ";
	    $table .= "onclick=\"passValue('".$row['kodebarang']."','".$target."');";
	    $table .= "passValue('".$row['namabarang']."','".$target."_name');\">";
	    foreach($row as $head=>$con) {
		$table .= "<td id='".$head."_".$key."'>".$con."</td>";
	    }
	    $table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "<tfoot></tfoot></table>";
	
	echo $table;
	break;
    default:
	break;
}
?>
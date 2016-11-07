<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zMysql.php');

# Get Parameter
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
unset($data['tableName']);
unset($data['numRow']);
unset($data['idField']);
unset($data['idVal']);

# Custom Constraint
if($data['kodeorg']===$data['parent']) {
    echo "alert('Error Constraint : Kode Organisasi dan Parent tidak boleh sama');";
    exit;
}

# Query
$query = "insert into `".$dbname."`.`".$tableName."`(";
$i=0;
foreach($data as $key=>$row) {
    if($i==0) {
        $query .= "`".$key."`";
    } else {
        $query .= ",`".$key."`";
    }
    $i++;
}
$query .=") values (";
$i=0;
foreach($data as $row) {
    if($i==0) {
        if(is_string($row)) {
            $query .= "'".$row."'";
        } else {
            $query .= $row;
        }
    } else {
        if(is_string($row)) {
            $query .= ",'".$row."'";
        } else {
            $query .= ",".$row;
        }
    }
    $i++;
}
$query .= ");";

#echo "ERROR".$query;

# Execute Query
try {
    # Insert Data
    mysql_query($query);
    
    # Append row to Tables in HTML
    echo "mTable = document.getElementById('mTabBody');";
    echo "mTable.innerHTML += ";
    echo "<tr id='tr_".$numRow."' class='rowcontent'>";
    $tmpField = "";
    $tmpVal = "";
    foreach($data as $key=>$row) {
	echo "<td id='".$key."_".$numRow."'>".$row."</td>";
	$tmpField .= "##".$key;
	$tmpVal .= "##".$row;
    }
    echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."')\"
	class='zImgBtn' src='images/application/application_edit.png' /></td>";
    echo "<td><img id='delRow".$numRow."' title='Hapus' onclick=\"delRow(".$numRow.",'".$idField."','".$idVal."',null,'".$tableName."')\"
	class='zImgBtn' src='images/application/application_delete.png' /></td>";
    echo "</tr>;";
    
    # Update Select
    /*$qSelect = "select `setup_org`.`kodeorg`,`setup_org`.`namaorganisasi` from `setup_org`";
    $dataOpt = fetchData($qSelect);
    echo $qSelect;
    echo "var parField = document.getElementById('parent');";
    echo "parField.options.length = 0;";
    foreach($dataOpt as $key=>$row) {
        echo "parField.options[parField.options.length] = new Options('".$row['namaorganisasi']."','".$row['kodeorg']."');";
    }*/
} catch(Exception $e) {
    echo "ERROR Query";
    echo $e->getMessage();
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


# Get Parameter
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
//$data['tgl_pengajuan']=date('Ymd');

unset($data['tableName']);
unset($data['numRow']);
unset($data['idField']);
unset($data['idVal']);

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
    # Transform Tanggal
    $tmpStr = explode("-",$row);
    if(count($tmpStr)==3) {
	$row = tanggalsystem($row);
    }
    
    $int = (int)$row;
    if($i==0) {
	if((string)$int==$row and strlen((string)$int)==strlen($row)) {
	    $query .= $row;
	} elseif(is_string($row)) {
            $query .= "'".$row."'";
        } else {
            $query .= $row;
        }
    } else {
	if((string)$int==$row and strlen((string)$int)==strlen($row)) {
	    $query .= ",".$row;
	} elseif(is_string($row)) {
            $query .= ",'".$row."'";
        } else {
            $query .= ",".$row;
        }
    }
    $i++;
}
$query .= ");";

//echo "ERROR".$query; exit();
#echo "error";

# Execute Query
try {
    # Insert Data
    if(!mysql_query($query)) {
	echo "DB Error : ".mysql_error($conn);
	exit;
    }
    
    # Append row to Tables in HTML
    #echo "mTable = document.getElementById('mTabBody');";
    #echo "mTable.innerHTML += ";
    echo "<tr id='tr_".$numRow."' class='rowcontent'>";
    $tmpField = "";
    $tmpVal = "";
    unset($data['tgl_pengajuan']);
    foreach($data as $key=>$row) {
	echo "<td id='".$key."_".$numRow."'>".$row."</td>";
	$tmpField .= "##".$key;
	$tmpVal .= "##".$row;
    }
    echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."')\"
	class='zImgBtn' src='images/001_45.png' /></td>";
    echo "<td><img id='delRow".$numRow."' title='Hapus' onclick=\"delRow(".$numRow.",'".$idField."','".$idVal."',null,'".$tableName."')\"
	class='zImgBtn' src='images/delete_32.png' /></td>";
    echo "</tr>";
} catch(Exception $e) {
    echo "ERROR Query";
    echo $e->getMessage();
}
?>
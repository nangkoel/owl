<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

# Get Parameter
$empty = $_GET['empty'];
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
$opt = json_decode(str_replace('##','"',$_POST['opt']),true);
unset($data['tableName']);
unset($data['numRow']);
unset($data['opt']);
unset($data['idField']);
unset($data['idVal']);
unset($data['freeze']);

if($empty==false) {
    foreach($data as $dt=>$isi)
    {
	if($isi=='')
	{
	    echo"warning:Please Insert The Form";
	    exit();
	}
    }
}
$sCek="select * from `".$dbname."`.`".$tableName."` ";

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

#echo "ERROR".$query;
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
    foreach($data as $key=>$row) {
	if(isset($opt[$key])) {
	    $tmpCont = $opt[$key][$row];
	} else {
	    $tmpCont = $row;
	}
	echo "<td id='".$key."_".$numRow."' value='".$row."'>".$tmpCont."</td>";
	$tmpField .= "##".$key;
	$tmpVal .= "##".$row;
    }
    if(isset($_POST['freeze'])) {
	echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."','".$_POST['freeze']."')\"
	    class='zImgBtn' src='images/001_45.png' /></td>";
    } else {
	echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."')\"
	    class='zImgBtn' src='images/001_45.png' /></td>";
    }
    echo "<td><img id='delRow".$numRow."' title='Hapus' onclick=\"delRow(".$numRow.",'".$idField."','".$idVal."',null,'".$tableName."')\"
	class='zImgBtn' src='images/delete_32.png' /></td>";
	echo"<td><img id='detail".$i."' title='Edit Detail' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift');".
    "showDetail(".$i.",'".$primaryStr."##kodeorg##shift',event)\"
    class='zImgBtn' src='images/application/application_view_xp.png' /></td>";
    echo "</tr>";
} catch(Exception $e) {
    echo "ERROR Query";
    echo $e->getMessage();
}
?>
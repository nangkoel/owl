<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

# Get Parameter
$tableName = $_POST['tableName'];
$IDs = $_POST['IDs'];
$id = explode("##",$IDs);
$data = $_POST;
$opt = json_decode(str_replace('##','"',$_POST['opt']),true);
unset($data['tableName']);
unset($data['IDs']);
unset($data['opt']);

# Create Condition
$where = "";

for($i=1;$i<count($id);$i++) {
    $tmpId = explode(",",$id[$i]);
    # Transform Tanggal
    $tmpStr = explode("-",$tmpId[1]);
    if(count($tmpStr)==3) {
	$tmpId[1] = tanggalsystem($tmpId[1]);
    }
    $int = (int)$tmpId[1];
    if($i==1) {
        if((string)$int==$tmpId[1] and strlen((string)$int)==strlen($tmpId[1])) {
	    $where .= "`".$tableName."`.`".$tmpId[0]."`=".$tmpId[1];
	} elseif(is_string($tmpId[1])) {
            $where .= "`".$tableName."`.`".$tmpId[0]."`='".$tmpId[1]."'";
        } else {
            $where .= "`".$tableName."`.`".$tmpId[0]."`=".$tmpId[1];
        }
    } else {
        if((string)$int==$tmpId[1] and strlen((string)$int)==strlen($tmpId[1])) {
	    $where .= " AND `".$tableName."`.`".$tmpId[0]."`=".$tmpId[1];
	} elseif(is_string($tmpId[1])) {
            $where .= " AND `".$tableName."`.`".$tmpId[0]."`='".$tmpId[1]."'";
        } else {
            $where .= " AND `".$tableName."`.`".$tmpId[0]."`=".$tmpId[1];
        }
    }
}

# Update Query
$query = "update `".$dbname."`.`".$tableName."` set ";

$i=0;
foreach($data as $key=>$row) {
    # Transform Tanggal
    $tmpStr = explode("-",$row);
    if(count($tmpStr)==3) {
	$row = tanggalsystem($row);
    }
    
    $int = (int)$row;
    if($i==0) {
        if((string)$int==$row and strlen((string)$int)==strlen($row)) {
	    $query .= "`".$tableName."`.`".$key."`=".$row;
	} elseif(is_string($row)) {
            $query .= "`".$tableName."`.`".$key."`='".$row."'";
        } else {
            $query .= "`".$tableName."`.`".$key."`=".$row;
        }
    } else {
        if((string)$int==$row and strlen((string)$int)==strlen($row)) {
	   $query .= ","."`".$tableName."`.`".$key."`=".$row;
	} elseif(is_string($row)) {
            $query .= ","."`".$tableName."`.`".$key."`='".$row."'";
        } else {
            $query .= ","."`".$tableName."`.`".$key."`=".$row;
        }
    }
    $i++;
}

# Appy Condition
$query .= " where ".$where;

try {
    # Update to DB
    if(!mysql_query($query)) {
	echo "DB Error : ".mysql_error($conn);
        exit;
    }
    
    # Update to Table
    echo "var currRow = document.getElementById('currRow').value;";
    foreach($data as $key=>$row) {
	if(isset($opt[$key])) {
	    $tmpCont = $opt[$key][$row];
	} else {
	    $tmpCont = $row;
	}
        echo "document.getElementById('".$key."_'+currRow).innerHTML = '".$tmpCont."';";
	echo "document.getElementById('".$key."_'+currRow).setAttribute('value','".$row."');";
    }
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
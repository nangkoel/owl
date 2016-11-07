<?php
include_once('lib/zMysql.php');

# Extract Data
$data = $_POST;
unset($data['proses']);

# Add Required Data
$data['kurs'] = 0;
$data['kurspajak'] = 0;

if($_POST['proses']=='main_add') {
    # Check Valid Data
    if($data['kode']=='' || $data['matauang']=='' || $data['simbol']=='' || $data['kodeiso']=='') {
        echo "Error : Data tidak boleh ada yang kosong";
        exit;
    }
    
    # Make Query
    $query = insertQuery($dbname,'setup_matauang',$data);
    
    # Insert Data
    if(!mysql_query($query)) {
        echo "DB Error : ".mysql_error($conn);
    }
} elseif ($_POST['proses']=='main_edit') {
    # Check Valid Data
    if($data['kode']=='' || $data['matauang']=='' || $data['simbol']=='' || $data['kodeiso']=='') {
        echo "Error : Data tidak boleh ada yang kosong";
        exit;
    }
    
    # Extract Primary Key
    unset($data['primField']);
    unset($data['primVal']);
    $prim = array(
        'field'=>$_POST['primField'],
        'value'=>$_POST['primVal']
    );
    
    # Create Condition
    $where = "`".$prim['field']."`='".$prim['value']."'";
    
    # Make Query
    $query = updateQuery($dbname,'setup_matauang',$data,$where);
    
    # Update Data
    if(!mysql_query($query)) {
        echo "DB Error : ".mysql_error($conn);
    }
} elseif ($_POST['proses']=='main_delete') {
    # Create Query
    $query = "delete from `".$dbname."`.`setup_matauang` where `kode`='".$_POST['primVal']."'";
    
    # Delete
    if(!mysql_query($query)) {
        echo "DB Error : ".mysql_error($conn);
    }
}
?>
<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$data = $_POST;

unset($data['proses']);
switch($_POST['proses']) {
    case 'add' :
        # Check Valid Data
        foreach($data as $key=>$row) {
            if($row=='') {
                echo "Error : Data ".$key." tidak boleh kosong";
                exit;
            }
        }
        
        # Unset Data
        unset($data['namabarang']);
        unset($data['satuan']);
        
        # Make Query
        $query = insertQuery($dbname,'setup_kegiatannorma',$data);
        
        # Insert Data
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error($conn);
        } else {
            echo $_SESSION['theme'];
        }
        break;
    case 'edit' :
        $data = $_POST;
        unset($data['proses']);
        unset($data['primary']);
        unset($data['primVal']);
        $primary = explode('##',$_POST['primary']);
        $primVal = explode('##',$_POST['primVal']);
        unset($primary['namabarang']);
        unset($primary['satuan']);
        unset($data['namabarang']);
        unset($data['satuan']);
        
        # Create Condition
        $where = "";
        for($i=1;$i<count($primary);$i++) {
            if($i==1) {
                $where .= "`".$primary[$i]."`='".$primVal[$i]."'";
            } else {
                $where .= " AND `".$primary[$i]."`='".$primVal[$i]."'";
            }
        }
        
        # Create Query
        $query = updateQuery($dbname,'setup_kegiatannorma',$data,$where);
        
        # Update Data
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error($conn);
        }
        break;
    case 'delete' :
        $data = $_POST;
        unset($data['proses']);
        unset($data['primary']);
        unset($data['primVal']);
        $primary = explode('##',$_POST['primary']);
        $primVal = explode('##',$_POST['primVal']);
        unset($primary['namabarang']);
        unset($primary['satuan']);
        unset($data['namabarang']);
        unset($data['satuan']);
        
        # Create Condition
        $where = "";
        for($i=1;$i<count($primary);$i++) {
            if($i==1) {
                $where .= $primary[$i]."='".$primVal[$i]."'";
            } else {
                $where .= " AND ".$primary[$i]."='".$primVal[$i]."'";
            }
        }
        
        # Create Query
        $query = "delete from `".$dbname."`.`setup_kegiatannorma` where ".$where;
        
        # Delete
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error($conn);
        }
        break;
        
    case 'addRow':
        # Get Field
        $tmpField = explode('##',$_POST['field']);
        $j = $_POST['numRow'];
        $primaryStr = $_POST['primary'];
        $fieldStr = $_POST['field'];
        foreach($tmpField as $key=>$row) {
            if($key!=0) {
                $field[] = $row;
            }
        }
        
        # Setting drop down options
        $optTopografi = makeOption($dbname,'setup_topografi','topografi,keterangan');
        $optTipeAng = getEnum($dbname,'setup_kegiatannorma','tipeanggaran');
        #$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
        
        $content = "";
        foreach($field as $row) {
            if($row=='topografi') {
                $content .= "<td>".makeElement($row."_".$j,'select','',
                    array('style'=>'width:100px'),$optTopografi)."</td>";
            } elseif($row=='tipeanggaran') {
                $content .= "<td>".makeElement($row."_".$j,'select','',
                    array('style'=>'width:100px'),$optTipeAng)."</td>";
            } elseif($row=='kodebarang') {
                $content .= "<td>".makeElement($row."_".$j,'text','',
                    array('style'=>'width:70px','readonly'=>'readonly')).
                    makeElement('getInvBtn_'.$j,'btn','Cari',array('onclick'=>'getInv(event,\''.$j.'\')'))."</td>";
            } elseif($row=='namabarang') {
                $content .= "<td>".makeElement($row."_".$j,'txt','',
                    array('style'=>'width:120px','readonly'=>'readonly'))."</td>";
            } elseif($row=='kuantitas1') {
                $content .= "<td>".makeElement($row."_".$j,'textnum','0',
                    array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)'))."&nbsp;<span id='uom1_".$j."'></span></td>";
            } elseif($row=='kuantitas2') {
                $content .= "<td>".makeElement($row."_".$j,'textnum','0',
                    array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)'))."&nbsp;<span id='uom2_".$j."'></span></td>";
            } else {
                $content .= "<td>".makeElement($row."_".$j,'textnum','0',
                    array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
            }
        }
        $content .= "<td><img id='addNorma_".$j."' title='Tambah' class=zImgBtn onclick=\"addNorma('".$j."','".$primaryStr."','".$fieldStr."')\" src='images/plus.png'/>";
        $content .= "&nbsp;<img id='deleteNorma_".$j."' /></td>";
        echo $content;
        break;
    default :
        break;
}
?>
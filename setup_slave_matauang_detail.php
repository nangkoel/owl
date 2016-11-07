<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

if($_POST['proses']=='createTable') {
    # Get Data
    $query = selectQuery($dbname,'setup_matauangrate',"*","`kode`='".$_POST['id']."'");
    $data = fetchData($query);
    
    # Create Detail Table
    createTabDetail($_POST['id'],$data);
} else {
    $data = $_POST;
    unset($data['proses']);
    switch($_POST['proses']) {
        case 'detail_add' :
            # Check Valid Data
            if($data['kode']=='' or $data['daritanggal']=='' or $data['kurs']=='') {
                echo "Error : Data tidak boleh ada yang kosong";
                exit;
            }
            
            # Make Query
            $tmpTgl = tanggalsystem($data['daritanggal']);
            $data['daritanggal'] = $tmpTgl;
            $data['jam'] = $data['jam'].":".$data['menit'].":00";
            unset($data['menit']);
            $query = insertQuery($dbname,'setup_matauangrate',$data);
            
            # Insert Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            break;
        case 'detail_edit' :
            # Check Valid Data
            if($data['kode']=='' or $data['daritanggal']=='' or $data['jam']=='' or $data['kurs']=='') {
                echo "Error : Data tidak boleh ada yang kosong";
                exit;
            }
            
            # Rearrange Data
            $tmpTgl = tanggalsystem($data['daritanggal']);
            $data['daritanggal'] = $tmpTgl;
            $data['jam'] = $data['jam'].":".$data['menit'].":00";
            unset($data['menit']);
            
            # Create Condition
            $where = "`kode`='".$data['kode']."'";
            $where .= " and `daritanggal`='".$data['daritanggal']."'";
            $where .= " and `jam`='".$data['jam']."'";
            
            # Make Query
            unset($data['kode']);
            unset($data['daritanggal']);
            unset($data['jam']);
            $query = updateQuery($dbname,'setup_matauangrate',$data,$where);
            
            # Update Data
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            break;
        case 'detail_delete' :
            $data = $_POST;
            
            # Rearrange Data
            $tmpTgl = tanggalsystem($data['daritanggal']);
            $data['daritanggal'] = $tmpTgl;
            $data['jam'] = $data['jam'].":".$data['menit'].":00";
            unset($data['menit']);
            
            # Create Condition
            $where = "`kode`='".$data['kode']."'";
            $where .= " and `daritanggal`='".$data['daritanggal']."'";
            $where .= " and `jam`='".$data['jam']."'";
            
            # Create Query
            $query = "delete from `".$dbname."`.`setup_matauangrate` where ".$where;
            
            # Delete
            if(!mysql_query($query)) {
                echo "DB Error : ".mysql_error($conn);
            }
            break;
        default :
            break;
    }
}

function createTabDetail($id,$data) {
    $table = "<b>".$_SESSION['lang']['kode']."</b> : ".makeElement("detail_kode",'text',$id,array('disabled'=>'disabled','style'=>'width:40px'));
    $table .= "<table id='matauangDetailTable'>";
    # Header
    $table .= "<thead>";
    $table .= "<tr>";
    $table .= "<td>Tanggal</td>";
    $table .= "<td>Jam</td>";
    $table .= "<td>Kurs</td>";
    $table .= "<td>Z</td>";
    $table .= "</tr>";
    $table .= "</thead>";
    
    # Data
    $table .= "<tbody id='detailBody'>";
    
    $i=0;
    
    #======= Display Data =======
    if($data!=array()) {
        foreach($data as $key=>$row) {
            $tmpTgl = tanggalnormal($row['daritanggal']);
            $row['daritanggal'] = $tmpTgl;
            $table .= "<tr id='detail_tr_".$key."' class='rowcontent'>";
            $table .= "<td>".makeElement("daritanggal_".$key."",'txt',$row['daritanggal'],
                array('style'=>'width:70px','disabled'=>'disabled'))."</td>";
            $table .= "<td>".makeElement("jam_".$key."",'select',substr($row['jam'],0,2),array('style'=>'width:40px','disabled'=>'disabled'),optionNum(24))." : ".
                makeElement("menit_".$key."",'select',substr($row['jam'],3,2),array('style'=>'width:40px','disabled'=>'disabled'),optionNum(60))."</td>";
            $table .= "<td>".makeElement("kurs_".$key."",'txt',$row['kurs'],array('style'=>'width:70px','onkeypress'=>'return angka_doang(event)'))."</td>";
            
            $table .= "<td><img id='detail_edit_".$key."' title='Edit' class=zImgBtn onclick=\"editDetail('".$key."')\" src='images/001_45.png'/>";
            $table .= "&nbsp;<img id='detail_delete_".$key."' title='Hapus' class=zImgBtn onclick=\"deleteDetail('".$key."')\" src='images/delete_32.png'/></td>";
            $i = $key;
        }
        $i++;
    }
    
    #======= New Row ===========
    $table .= "<tr id='detail_tr_".$i."' class='rowcontent'>";
    $table .= "<td>".makeElement("daritanggal_".$i."",'txt','',
        array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)','onmousemove'=>'setCalendar(this.id)','readonly'=>'readonly'))."</td>";
    $table .= "<td>".makeElement("jam_".$i."",'select','00',array('style'=>'width:40px'),optionNum(24))." : ".
        makeElement("menit_".$i."",'select','00',array('style'=>'width:40px'),optionNum(60))."</td>";
    $table .= "<td>".makeElement("kurs_".$i."",'txt','',array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'));
    $table .= makeElement("kode_".$i."",'hidden',$id,array('style'=>'width:70px','onkeypress'=>'return tanpa_kutip(event)'))."</td>";
    
    # Add, Container Delete
    $table .= "<td><img id='detail_add_".$i."' title='Tambah' class=zImgBtn onclick=\"addDetail('".$i."')\" src='images/plus.png'/>";
    $table .= "&nbsp;<img id='detail_delete_".$i."' /></td>";
    $table .= "</tr>";
    
    $table .= "</tbody>";
    $table .= "</table>";
    echo $table;
}
?>
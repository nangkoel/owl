<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

# Get POST
$IDs = $_POST;
$namaKeg = $_POST['namakegiatan'];
$uom = $_POST['satuan'];
unset($IDs['namakegiatan']);
unset($IDs['satuan']);

# Get Header
$tmpField = getFieldName('setup_kegiatannorma','array');
$tmpPrim = getPrimary($dbname,'setup_kegiatannorma');

$fieldNew = $field = array();
$fieldStr = "";
foreach($tmpField as $row) {
    if($row!='kodeorg' and $row!='kodekegiatan' and $row!='kelompok') {
        $fieldNew[] = $field[] = $row;
        $fieldStr .= "##".$row;
        if($row=='kodebarang') {
            $fieldNew[] = 'namabarang';
            $fieldStr .= "##namabarang";
        }
    }
}

$primaryStr = "";
foreach($tmpPrim as $row) {
    $primaryStr .= "##".$row;
}

#======== Get Data
# Prep Condition
$i=0;
foreach($IDs as $key=>$row) {
    if($i==0) {
        $where = $key."='".$row."'";
    } else {
        $where .= " AND ".$key."='".$row."'";
    }
    $i++;
}

# Fetch Data
$query = selectQuery($dbname,'setup_kegiatannorma',$field,$where);
$tmpData = fetchData($query);
$data = array();
$listInv = array();

# Insert namabarang column
foreach($tmpData as $key=>$row) {
    foreach($row as $head=>$cont) {
        $data[$key][$head] = $cont;
        if($head=='kodebarang') {
            $data[$key]['namabarang'] = '';
            $listInv[] = $cont;
        }
    }
}

# Create Header
$header = array();
foreach($fieldNew as $row) {
  $header[] = $_SESSION['lang'][$row];
}
$header[] = "Z";

#=========== Reform Content from Data =================
$primary = "<table>";
$primary .= "<tr><td>".makeElement('kodeorg_norma','label',$_SESSION['lang']['kodeorg'])."</td><td>: ".
    makeElement('kodeorg_norma','text',$IDs['kodeorg'],array('disabled'=>'disabled','style'=>'width:100px'))."</td></tr><tr><td>";
$primary .= makeElement('kodekegiatan_norma','label',$_SESSION['lang']['kodekegiatan'])."</td><td>: ".
    makeElement('kodekegiatan_norma','text',$IDs['kodekegiatan'],array('disabled'=>'disabled','style'=>'width:50px'))."&nbsp;".
    makeElement('namakegiatan_norma','text',$namaKeg,array('disabled'=>'disabled','style'=>'width:150px')).
    "</td></tr><tr><td>";
$primary .= makeElement('kelompok_norma','label',$_SESSION['lang']['kelompok'])."</td><td>: ".
    makeElement('kelompok_norma','text',$IDs['kelompok'],array('disabled'=>'disabled','style'=>'width:100px'))."</td></tr>";
$primary .= "</table>";

$content = array();

# Setting drop down options
$optTopografi = makeOption($dbname,'setup_topografi','topografi,keterangan');
$optTipeAng = getEnum($dbname,'setup_kegiatannorma','tipeanggaran');

# Get Nama Barang
$whereNB = "";
foreach($listInv as $key=>$row) {
    if($key==0) {
        $whereNB .= "kodebarang=".$row;
    } else {
        $whereNB .= " or kodebarang=".$row;
    }
}
if($whereNB!="") {
    $query = selectQuery($dbname,'log_5masterbarang','kodebarang,namabarang,satuan',$whereNB);
    $resBar = fetchData($query);
    $namaBarang = array();
    $satuanBarang = array();
    foreach($resBar as $row) {
        $namaBarang[$row['kodebarang']] = $row['namabarang'];
        $satuanBarang[$row['kodebarang']] = $row['satuan'];
    }
}

# Masking Nama Barang
foreach($data as $key=>$row) {
    $data[$key]['namabarang'] = $namaBarang[$row['kodebarang']];
}

# Editable Row
$j=0;
if($data!=array()) {
  foreach($data as $i=>$row) {
    foreach($row as $key=>$data) {
        if($key=='topografi') {
            $content[$i][$key] = makeElement($key."_".$i,'select',$data,
                array('style'=>'width:100px','disabled'=>'disabled'),array($data=>$optTopografi[$data]));
        } elseif($key=='tipeanggaran') {
            $content[$i][$key] = makeElement($key."_".$i,'select',$data,
                array('style'=>'width:100px','disabled'=>'disabled'),array($data=>$optTipeAng[$data]));
        } elseif($key=='kodebarang') {
            $content[$i][$key] = makeElement($key."_".$i,'text',$data,
                array('style'=>'width:70px','readonly'=>'readonly','disabled'=>'disabled')).
                makeElement('getInvBtn_'.$i,'btn','Cari',
                array('onclick'=>'getInv(event,\''.$i.'\')','disabled'=>'disabled'));
        } elseif($key=='namabarang') {
            $content[$i][$key] = makeElement($key."_".$i,'txt',$data,
                array('style'=>'width:120px','disabled'=>'disabled'));
        } elseif($key=='kuantitas1') {
            $content[$i][$key] = makeElement($key."_".$i,'textnum',$data,
                array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)')).
                "&nbsp;<span id='uom1_".$i."'>".$satuanBarang[$row['kodebarang']]."</span>";
        } elseif($key=='kuantitas2') {
            $content[$i][$key] = makeElement($key."_".$i,'textnum',$data,
                array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)')).
                "&nbsp;<span id='uom2_".$i."'>".$uom."</span>";
        } else {
            $content[$i][$key] = makeElement($key."_".$i,'textnum',$data,
                array('style'=>'width:40px','onkeypress'=>'return tanpa_kutip(event)'));
        }
    }
    $content[$i]['Z'] = "<img id='editNorma_".$i."' title='Edit' class=zImgBtn onclick=\"editNorma('".$i."','".$primaryStr."','".$fieldStr."')\" src='images/".$_SESSION['theme']."/save.png'/>";
    $content[$i]['Z'] .= "&nbsp;<img id='deleteNorma_".$i."' title='Hapus' class=zImgBtn onclick=\"deleteNorma('".$i."','".$primaryStr."','".$fieldStr."')\" src='images/".$_SESSION['theme']."/delete.png'/>";
    $j = $i+1;
  }
}

# New Row
foreach($fieldNew as $row) {
    if($row=='topografi') {
        $content[$j][$row] = makeElement($row."_".$j,'select','',
            array('style'=>'width:100px'),$optTopografi);
    } elseif($row=='tipeanggaran') {
        $content[$j][$row] = makeElement($row."_".$j,'select','',
            array('style'=>'width:100px'),$optTipeAng);
    } elseif($row=='kodebarang') {
        $content[$j][$row] = makeElement($row."_".$j,'text','',
            array('style'=>'width:70px','readonly'=>'readonly')).
            makeElement('getInvBtn_'.$j,'btn','Cari',array('onclick'=>'getInv(event,\''.$j.'\')'));
    } elseif($row=='namabarang') {
        $content[$j][$row] = makeElement($row."_".$j,'txt','',
            array('style'=>'width:120px','readonly'=>'readonly'));
    } elseif($row=='kuantitas1') {
        $content[$j][$row] = makeElement($row."_".$j,'textnum','0',
            array('style'=>'width:40px','onkeypress'=>'return angka_doang(event)'))."&nbsp;<span id='uom1_".$j."'></span>";
    } elseif($row=='kuantitas2') {
        $content[$j][$row] = makeElement($row."_".$j,'textnum','0',
            array('style'=>'width:40px','onkeypress'=>'return angka_doang(event)'))."&nbsp;<span id='uom2_".$j."'>".$uom."</span>";
    } else {
        $content[$j][$row] = makeElement($row."_".$j,'textnum','0',
            array('style'=>'width:40px','onkeypress'=>'return angka_doang(event)'));
    }
}
$content[$j]['Z'] = "<img id='addNorma_".$j."' title='Tambah' class=zImgBtn onclick=\"addNorma('".$j."','".$primaryStr."','".$fieldStr."')\" src='images/plus.png'/>";
$content[$j]['Z'] .= "&nbsp;<img id='deleteNorma_".$j."' />";

#============= Generate Main Table =======================
$mainTable = makeTable('normaTable','normaBody',$header,$content,array(),true,'detail_tr');
echo "<div id='mainTable' style='float:left;'>";
echo "<fieldset><legend><b>Norma</b></legend>";
echo "<div style='overflow:auto;width:770px;max-height:270px'>";
echo $primary;
echo $mainTable;
echo "</div></fieldset></div>";
?>
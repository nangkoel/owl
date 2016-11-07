<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
switch($proses) {
    #==== Show Detail Form =====
    case 'showDetail':
        # Extract Data
        $closed = $_POST['closed'];
        $where = "kodeorg='".$_POST['kodeorg']."' and kodeanggaran='".
	    $_POST['kodeanggaran']."' and tahun=".$_POST['tahun'];
        $query = selectQuery($dbname,'keu_anggarandt',
            'kodebagian,kodekegiatan,kelompok,revisi,kodebarang',
            $where,'kodebagian');
        $tmpData = fetchData($query);
        
        # Prepare Data
        $data = array();
        foreach($tmpData as $key=>$row) {
            $data[$key] = $row;
            $fieldStr = "";
            $fieldVal = "";
            foreach($row as $h=>$r) {
                $fieldStr .= "##".$h;
                $fieldVal .= "##".$r;
            }
	    
	    if($closed=='0') {
		$data[$key]['manage'] =
		    "<img id='editDetail_".$key."' title='Edit' onclick=\"editDetail(".$key.",event,'".$fieldStr."','".$fieldVal."')\"
			class='zImgBtn' src='images/001_45.png' />&nbsp;".
		    "<img id='deleteDetail_".$key."' title='Hapus' onclick=\"deleteDetail(".$key.",'".$fieldStr."','".$fieldVal."')\"
			class='zImgBtn' src='images/delete_32.png' />";
	    } else {
		$data[$key]['manage'] = '';
	    }
        }
        
        # Prepare Header
        $header = array(
            $_SESSION['lang']['kodebagian'],
            $_SESSION['lang']['kodekegiatan'],
            $_SESSION['lang']['kelompok'],
            $_SESSION['lang']['revisi'],
            $_SESSION['lang']['kodebarang'],
            'Z'
        );
        
        # Create Table
        $tables = makeTable('listDetail','bodyDetail',$header,$data,array(),true,'detail_tr');
        
        #Show Table
        if($closed=='0') {
#	    echo "<a onclick='addDetail(event)' style='cursor:pointer'>Tambah Detail</a>";
	    echo "<img id='addDetailId' title='Tambah Detail' src='images/plus.png'".
		"style='width:20px;height:20px;cursor:pointer' onclick='addDetail(event)' />&nbsp;";
	}
        echo $tables;
        break;
#$headControl = "<img id='addHeaderId' title='Tambah Header' src='images/plus.png'".
#  "style='width:20px;height:20px;cursor:pointer' onclick='addHeader(event)' />&nbsp;";
    #==== Form Tambah Detail =====
    case "addDetail":
	$data = array(
	    'kodeorg'=>$_POST['kodeorg'],
	    'kodebagian'=>'','kodekegiatan'=>'',
	    'noaruskas'=>'',
	    'kelompok'=>'','kodebarang'=>'',
	    'revisi'=>0,'hargasatuan'=>0,
	    'jumlah'=>0,
	    'jan'=>0,'peb'=>0,'mar'=>0,
	    'apr'=>0,'mei'=>0,'jun'=>0,
	    'jul'=>0,'agt'=>0,'sep'=>0,
	    'okt'=>0,'nov'=>0,'dec'=>0
	);
	$form = renderFormDetail($data);
        echo $form;
	break;
    #==== Form Edit Detail =====
    case 'editDetail':
	#echo "error";
	#print_r($_POST);
	$where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ".
	"kodebagian='".$_POST['kodebagian']."' AND ".
	"tahun='".$_POST['tahun']."' AND ".
	"kodeorg='".$_POST['kodeorg']."' AND ".
	"kodekegiatan='".$_POST['kodekegiatan']."' AND ".
	"kelompok='".$_POST['kelompok']."' AND ".
	"revisi=".$_POST['revisi']." AND ".
	"kodebarang='".$_POST['kodebarang']."'";
	$query = selectQuery($dbname,'keu_anggarandt','*',$where);
	$res = fetchData($query);
	
	$data = $res[0];
	$form = renderFormDetail($data,'edit',$_POST['numRow']);
        echo $form;
	break;
    #==== Proses Tambah Detail =====
    case 'add':
	$data = $_POST;
	$numRow = $_POST['numRow'];
	unset($data['numRow']);
	$data['kelompok']='-';
	#echo "error\n";
	#print_r($data);
	
	# Insert Data
	$column = array('kodeorg','kodeanggaran','tahun','kodebagian','kodekegiatan',
	    'noaruskas','kodebarang','revisi','hargasatuan','jumlah','jan','peb',
	    'mar','apr','mei','jun','jul','agt','sep','okt','nov','dec','kelompok');
	$query = insertQuery($dbname,'keu_anggarandt',$data,$column);
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    # Prep FieldStr & Val
	    $field = $data;
	    unset($field['kodeorg']);
	    unset($field['kodeanggaran']);
	    $fieldStr = "";
	    $fieldVal = "";
	    foreach($field as $key=>$row) {
		$fieldStr .= "##".$key;
		$fieldVal .= "##".$row;
	    }
	    
	    $tRow = "<tr id='detail_tr_".$numRow."'>";
	    $tRow .= "<td class='rowcontent'>".$data['kodebagian']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kodekegiatan']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kelompok']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['revisi']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kodebarang']."</td>";
	    $tRow .= "<td class='rowcontent'><img id='editDetail_".$numRow."' title='Edit' onclick=\"editDetail(".$numRow.",event,'".$fieldStr."','".$fieldVal."')\"
		class='zImgBtn' src='images/001_45.png' />&nbsp;";
	    $tRow .= "<img id='deleteDetail_".$numRow."' title='Hapus' onclick=\"deleteDetail(".$numRow.",'".$fieldStr."','".$fieldVal."')\"
		class='zImgBtn' src='images/delete_32.png' /></td>";
	    $tRow .= "</tr>";
	    
	    echo $tRow;
	}
	break;
    #==== Proses Edit Detail =====
    case 'edit':
	$data = $_POST;
	$numRow = $_POST['numRow'];
	unset($data['numRow']);
	if($data['kelompok']=='') {
	    $data['kelompok'] = '-';
	}
	#echo "error\n";
	#print_r($data);
	
	# Update Data
	$where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ";
	$where .= "kodebagian='".$_POST['kodebagian']."' AND ";
	$where .= "kodeorg='".$_POST['kodeorg']."' AND ";
	$where .= "tahun='".$_POST['tahun']."' AND ";
	$where .= "kodekegiatan='".$_POST['kodekegiatan']."' AND ";
	$where .= "kelompok='".$data['kelompok']."' AND ";
	$where .= "revisi=".$_POST['revisi']." AND ";
	$where .= "kodebarang='".$_POST['kodebarang']."'";
	$query = updateQuery($dbname,'keu_anggarandt',$data,$where);
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	} else {
	    # Prep FieldStr & Val
	    $field = $data;
	    unset($field['kodeorg']);
	    unset($field['kodeanggaran']);
	    unset($field['tahun']);
	    $fieldStr = "";
	    $fieldVal = "";
	    foreach($field as $key=>$row) {
		$fieldStr .= "##".$key;
		$fieldVal .= "##".$row;
	    }
	    
	    $tRow = "";
	    $tRow .= "<td class='rowcontent'>".$data['kodebagian']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kodekegiatan']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kelompok']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['revisi']."</td>";
	    $tRow .= "<td class='rowcontent'>".$data['kodebarang']."</td>";
	    $tRow .= "<td class='rowcontent'><img id='editDetail_".$numRow."' title='Edit' onclick=\"editDetail(".$numRow.",event,'".$fieldStr."','".$fieldVal."')\"
		class='zImgBtn' src='images/001_45.png' />&nbsp;";
	    $tRow .= "<img id='deleteDetail_".$numRow."' title='Hapus' onclick=\"deleteDetail(".$numRow.",'".$fieldStr."','".$fieldVal."')\"
		class='zImgBtn' src='images/delete_32.png' /></td>";
	    
	    echo $tRow;
	}
	break;
    #==== Proses Hapus Detail =====
    case 'delete':
	$where = "kodeanggaran='".$_POST['kodeanggaran']."' AND ";
	$where .= "kodebagian='".$_POST['kodebagian']."' AND ";
	$where .= "kodeorg='".$_POST['kodeorg']."' AND ";
	$where .= "tahun='".$_POST['tahun']."' AND ";
	$where .= "kodekegiatan='".$_POST['kodekegiatan']."' AND ";
	$where .= "kelompok='".$_POST['kelompok']."' AND ";
	$where .= "revisi=".$_POST['revisi']." AND ";
	$where .= "kodebarang='".$_POST['kodebarang']."'";
	$query = "delete from `".$dbname."`.`keu_anggarandt` where ".$where;
	
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	}
	break;
    default:
        break;
}

function renderFormDetail($data,$mode='add',$num=0) {
    global $dbname;
    
    # Prepare Options
    $holding = getHolding($dbname,$data['kodeorg']);
    if($holding!=false) {
	$kelompok = array(''=>'');
	$tmpKel = makeOption($dbname,'setup_klpkegiatan','kodeklp,namakelompok',"kodeorg='".$holding['kode']."'");
	foreach($tmpKel as $key=>$row) {
	    $kelompok[$key] = $row;
	}
    } else {
	$kelompok = array();
    }
    $optCashFlow = makeOption($dbname,'keu_5mesinlaporandt','nourut,keterangandisplay',
	"tipe='Detail' and namalaporan='CASH FLOW DIRECT'");
    $whereKeg = "(substr(noakun,1,2)='52' or substr(noakun,1,2)='64') and detail=1";
    $kegiatan = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereKeg,1);
    
    if(!isset($_SESSION['org']['below'])) {
	$_SESSION['org']['below'] = getOrgBelow($dbname,$data['kodeorg']);
    }
    $orgBelow = $_SESSION['org']['below'];
    
    # Prepare Field
    if($mode=='add') {
	$disabled = '';
    } else {
	$disabled = 'disabled';
    }
    $els = array();
    $els[] = array(
	makeElement('kodebagian','label',$_SESSION['lang']['kodebagian']),
	makeElement('kodebagian','select',$data['kodebagian'],array('style'=>'width:250px',
	    $disabled=>$disabled),$orgBelow)
    );
    $els[] = array(
	makeElement('kelompok','label',$_SESSION['lang']['kelompok']),
	makeElement('kelompok','select',$data['kelompok'],array('style'=>'width:250px'
	    ,'onchange'=>"getKegiatan(this,'kodekegiatan')",'disabled'=>'disabled'),$kelompok)
    );
    $els[] = array(
	makeElement('kodekegiatan','label',$_SESSION['lang']['posbiaya']),
	makeElement('kodekegiatan','select',$data['kodekegiatan'],array('style'=>'width:250px'
	    ,$disabled=>$disabled),$kegiatan)
    );
    $els[] = array(
	makeElement('noaruskas','label',$_SESSION['lang']['noaruskas']),
	makeElement('noaruskas','select',$data['noaruskas'],array('style'=>'width:250px'),$optCashFlow)
    );
    $els[] = array(
	makeElement('kodebarang','label',$_SESSION['lang']['kodebarang']),
	makeElement('kodebarang','searchBarang',$data['kodebarang'],array('style'=>'width:70px','maxlength'=>'10',
	    'onkeypress'=>'return tanpa_kutip(event)','readonly'=>'readonly',$disabled=>$disabled))
	#makeElement('getInvBtn','btn','Cari',array('onclick'=>'getInv(event,\'kodebarang\')',$disabled=>$disabled))
    );
    /*$els[] = array(
	makeElement('revisi','label',$_SESSION['lang']['revisi']),
	makeElement('revisi','textnum',$data['revisi'],array('style'=>'width:70px','maxlength'=>'2',
	    'onkeypress'=>'return angka_doang(event)',$disabled=>$disabled))
    );*/
    $els[] = array(
	makeElement('hargasatuan','label',$_SESSION['lang']['hargasatuan']),
	makeElement('hargasatuan','textnum',$data['hargasatuan'],array('style'=>'width:70px',
	    'readonly'=>'readonly'))
    );
    $els[] = array(
	makeElement('jumlah','label',$_SESSION['lang']['jumlah']),
	makeElement('jumlah','textnum',$data['jumlah'],array('style'=>'width:70px',
	    'readonly'=>'readonly'))
    );
    
    $els2 = array();
    $els2[] = array(
	makeElement('jan','label',$_SESSION['lang']['jan']),
	makeElement('jan','textnum',$data['jan'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('peb','label',$_SESSION['lang']['peb']),
	makeElement('peb','textnum',$data['peb'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('mar','label',$_SESSION['lang']['mar']),
	makeElement('mar','textnum',$data['mar'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('apr','label',$_SESSION['lang']['apr']),
	makeElement('apr','textnum',$data['apr'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('mei','label',$_SESSION['lang']['mei']),
	makeElement('mei','textnum',$data['mei'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('jun','label',$_SESSION['lang']['jun']),
	makeElement('jun','textnum',$data['jun'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('jul','label',$_SESSION['lang']['jul']),
	makeElement('jul','textnum',$data['jul'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('agt','label',$_SESSION['lang']['agt']),
	makeElement('agt','textnum',$data['agt'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('sep','label',$_SESSION['lang']['sep']),
	makeElement('sep','textnum',$data['sep'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('okt','label',$_SESSION['lang']['okt']),
	makeElement('okt','textnum',$data['okt'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('nov','label',$_SESSION['lang']['nov']),
	makeElement('nov','textnum',$data['nov'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    $els2[] = array(
	makeElement('dec','label',$_SESSION['lang']['dec']),
	makeElement('dec','textnum',$data['dec'],array('style'=>'width:90px','maxlength'=>'13',
	    'onkeypress'=>'return angka_doang(event)','onkeyup'=>'updateQty()'))
    );
    
    $fieldStr = "##kodebagian##kodekegiatan##kelompok##revisi##kodebarang##hargasatuan".
	"##jumlah##jan##peb##mar##apr##mei##jun##jul##agt##sep##okt##nov##dec";
	
    if($mode=='add') {
	$btn = makeElement('addDataDetailB','button',$_SESSION['lang']['save'],
	    array('onclick'=>'addDataDetail()','style'=>'float:left;clear:both;'));
    } else {
	$btn = makeElement('editDataDetailB','button',$_SESSION['lang']['save'],
	    array('onclick'=>'editDataDetail('.$num.')','style'=>'float:left;clear:both;'));
    }
    
    # Make Layout
    $form = genElTitle('Form Detail',$els);
    $form .= genElementMultiDim('Rincian Sebaran',$els2,3);
    $form .= $btn;
    
    return $form;
}
?>
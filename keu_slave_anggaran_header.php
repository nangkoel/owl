<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
switch($proses) {
    #======= Form Tambah Header =============
    case 'addHeader':
        # Prepare Options
        if(!isset($_SESSION['org']['below'])) {
            $_SESSION['org']['below'] = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas']);
        }
        $optOrg = $_SESSION['org']['below'];
        $optCurr = makeOption($dbname,'setup_matauang','kode,matauang');
        $optType = getEnum($dbname,'keu_anggaran','tipeanggaran');
        
        # Prepare Field
        $els = array();
        $els[] = array(
            makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
            makeElement('kodeorg','select','',array('style'=>'width:200px'),$optOrg)
        );
        $els[] = array(
            makeElement('kodeanggaran','label',$_SESSION['lang']['kodeanggaran']),
            makeElement('kodeanggaran','text','',array('style'=>'width:70px','maxlength'=>'10',
                'onkeypress'=>'return tanpa_kutip(event)'))
        );
        $els[] = array(
            makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
            makeElement('keterangan','text','',array('style'=>'width:250px','maxlength'=>'50',
                'onkeypress'=>'return tanpa_kutip(event)'))
        );
        $els[] = array(
            makeElement('tipeanggaran','label',$_SESSION['lang']['tipeanggaran']),
            makeElement('tipeanggaran','select','',array('style'=>'width:70px'),$optType)
        );
        $els[] = array(
            makeElement('tahun','label',$_SESSION['lang']['tahun']),
            makeElement('tahun','text',date('Y'),array('style'=>'width:70px','maxlength'=>'4',
                'onkeypress'=>'return angka_doang(event)'))
        );
        $els[] = array(
            makeElement('matauang','label',$_SESSION['lang']['matauang']),
            makeElement('matauang','select','',array('style'=>'width:70px'),$optCurr)
        );
        $els[] = array(
            makeElement('revisi','label',$_SESSION['lang']['revisi']),
            makeElement('revisi','textnum','0',array('style'=>'width:70px','maxlength'=>'2',
                'onkeypress'=>'return angka_doang(event)'))
        );
        $els[] = array(
            makeElement('tutup','label',$_SESSION['lang']['tutup']),
            makeElement('tutup','check')
        );
        $fieldStr = "##kodeorg##kodeanggaran##keterangan##tipeanggaran##tahun##matauang".
            "##tutup##revisi";
        $els['button'] = array(
            makeElement('addDataHead','button',$_SESSION['lang']['save'],
                array('onclick'=>'addDataHeader(\''.$fieldStr.'\')'))
        );
        
        echo genElementMultiDim('Tambah Header Anggaran',$els,1);
        break;
    #========== Tampilkan List Header ==========
    case 'showList':
        # Extract Data
        $lokTugas = getOrgBelow($dbname,$_SESSION['empl']['lokasitugas']);
	$whereLok = "";
	$i=0;
	foreach($lokTugas as $key=>$row) {
	    if($i==0) {
		$whereLok .= "kodeorg='".$key."'";
	    } else {
		$whereLok .= " or kodeorg='".$key."'";
	    }
	    $i++;
	}
        $query = selectQuery($dbname,'keu_anggaran',
            'kodeanggaran,kodeorg,tahun,keterangan,tipeanggaran,tutup,revisi',
	    $whereLok,'kodeanggaran');
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
            $data[$key]['manage'] =
                "<img id='showDetail_".$key."' title='Lihat Detail' onclick=\"showingDetail('".$fieldStr."','".$fieldVal."','".$row['tutup']."')\"
		    class='zImgBtn' src='images/edit.png' />&nbsp;".
                "<img id='editHeader_".$key."' title='Edit Header' onclick=\"editHeader(event,'".$fieldStr."','".$fieldVal."')\"
		    class='zImgBtn' src='images/001_45.png' />&nbsp;".
                "<img id='deleteHeader_".$key."' title='Hapus Header' onclick=\"deleteHeader(".$key.",'".$fieldStr."','".$fieldVal."')\"
		    class='zImgBtn' src='images/delete_32.png' />";
        }
        
        # Prepare Header
        $header = array(
            'kodeanggaran'=>$_SESSION['lang']['kodeanggaran'],
            'kodeorg'=>$_SESSION['lang']['kodeorg'],
            'tahun'=>$_SESSION['lang']['tahun'],
            'keterangan'=>$_SESSION['lang']['keterangan'],
            'tipeanggaran'=>$_SESSION['lang']['tipeanggaran'],
            'tutup'=>$_SESSION['lang']['tutup'],
	    'revisi'=>$_SESSION['lang']['revisi'],
            'manip'=>'Z'
        );
        
        # Create Table
        $tables = makeCompleteTable('listHeader','bodyList',$header,$data,array(),true,'edit_tr');
        
        #Show Table
        echo $tables;
        break;
    #========== Form Edit Header ================
    case 'editHeader':
        # Get Data
        $query = selectQuery($dbname,'keu_anggaran',"*","kodeanggaran='".$_POST['kodeanggaran']."'");
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        
        # Prepare Options
        $optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi'
            ,"kodeorganisasi='".$data['kodeorg']."'");
        $optCurr = makeOption($dbname,'setup_matauang','kode,matauang');
        $optType = getEnum($dbname,'keu_anggaran','tipeanggaran');
        
        # Prepare Field
        $els = array();
        $els[] = array(
            makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
            makeElement('kodeorg','select',$data['kodeorg'],array('style'=>'width:200px',
                'disabled'=>'disabled'),$optOrg)
        );
        $els[] = array(
            makeElement('kodeanggaran','label',$_SESSION['lang']['kodeanggaran']),
            makeElement('kodeanggaran','text',$data['kodeanggaran'],array('style'=>'width:70px','maxlength'=>'10',
                'disabled'=>'disabled'))
        );
        $els[] = array(
            makeElement('keterangan','label',$_SESSION['lang']['keterangan']),
            makeElement('keterangan','text',$data['keterangan'],array('style'=>'width:250px','maxlength'=>'50',
                'onkeypress'=>'return tanpa_kutip(event)'))
        );
        $els[] = array(
            makeElement('tipeanggaran','label',$_SESSION['lang']['tipeanggaran']),
            makeElement('tipeanggaran','select',$data['tipeanggaran'],array('style'=>'width:70px'),$optType)
        );
        $els[] = array(
            makeElement('tahun','label',$_SESSION['lang']['tahun']),
            makeElement('tahun','text',$data['tahun'],array('style'=>'width:70px','maxlength'=>'4',
                'onkeypress'=>'return angka_doang(event)','disabled'=>'disabled'))
        );
        $els[] = array(
            makeElement('matauang','label',$_SESSION['lang']['matauang']),
            makeElement('matauang','select',$data['matauang'],array('style'=>'width:70px'),$optCurr)
        );
        $els[] = array(
            makeElement('revisi','label',$_SESSION['lang']['revisi']),
            makeElement('revisi','textnum',$data['revisi'],array('style'=>'width:70px',
                'disabled'=>'disabled'))
        );
        if($data['tutup']==1) {
            $els[] = array(
                makeElement('tutup','label',$_SESSION['lang']['tutup']),
                makeElement('tutup','check','',array('checked'=>'checked'))
            );
        } else {
            $els[] = array(
                makeElement('tutup','label',$_SESSION['lang']['tutup']),
                makeElement('tutup','check')
            );
        }
        
        $fieldStr = "##kodeorg##kodeanggaran##keterangan##tipeanggaran##tahun##matauang".
            "##tutup##revisi";
        $els['button'] = array(
            makeElement('editDataHead','button',$_SESSION['lang']['save'],
                array('onclick'=>'editDataHeader(\''.$fieldStr.'\')')).
            makeElement('cancelEdit','button',$_SESSION['lang']['cancel'],
                array('onclick'=>'showHeadList(event)'))
        );
        
        echo genElementMultiDim('Edit Header Anggaran',$els,1);
        break;
    #========== Tampilkan form Header ================
    case 'showHead':
        # Get Data
        $data = $_POST;
        $nameOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
            "kodeorganisasi='".$data['kodeorg']."'");
        
        $where = "kodeanggaran='".$data['kodeanggaran']."'";
        $query = selectQuery($dbname,'keu_anggaran','*',$where);
        $dataX = fetchData($query);
        $dataX[0]['nameorg'] = $nameOrg[$data['kodeorg']];
        
        echo showMainHead($dbname,$dataX[0]);
        
        break;
    #========== Proses Hapus Header ================
    case 'deleteHeader':
        break;
    #========== Proses Tambah Header ================
    case 'add':
        # Get Data
        $data = $_POST;
        
        # Clean up data
        unset($data['nameOrg']);
        $data['jumlahkoreksi'] = 0;
        $data['jumlah'] = 0;
        
        $column = array();
        foreach($data as $key=>$row) {
            $column[] = $key;
        }
        $query = insertQuery($dbname,'keu_anggaran',$data,$column);
        
        # Insert
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error();
            exit;
        }
        
        $data['nameorg'] = $_POST['nameOrg'];
        echo showMainHead($dbname,$data);
        break;
    #========== Proses Edit Header ================
    case 'edit':
        # Get Data
        $data = $_POST;
        
        # Clean up data
        unset($data['nameOrg']);
        
        $column = array();
        foreach($data as $key=>$row) {
            $column[] = $key;
        }
        $where = "kodeanggaran='".$data['kodeanggaran']."' AND ".
            "kodeorg='".$data['kodeorg']."' AND ".
            "tahun='".$data['tahun']."'";
        $query = updateQuery($dbname,'keu_anggaran',$data,$where);
        
        # Insert
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error();
            exit;
        }
        
        # Get Jumlah Total
        $query2 = selectQuery($dbname,'keu_anggaran','jumlah',$where);
        $jml = fetchData($query2);
        $data['nameorg'] = $_POST['nameOrg'];
        $data['jumlah'] = $jml[0]['jumlah'];
        showMainHead($dbname,$data);
        break;
    #========== Proses Hapus Header ================
    case 'delete':
        # Get Data
        $data = $_POST;
        
        $where = "kodeorg='".$data['kodeorg']."' AND ".
            "kodeanggaran='".$data['kodeanggaran']."' AND ".
            "tahun='".$data['tahun']."'";
        $query = "delete from `".$dbname."`.`keu_anggarandt` where ".$where;
        $query2 = "delete from `".$dbname."`.`keu_anggaran` where ".$where;
        #echo "error";
        #echo $query2;
        
        # Delete
        if(!mysql_query($query)) {
            echo "DB Error : ".mysql_error();
        }
        if(!mysql_query($query2)) {
            echo "DB Error : ".mysql_error();
            exit;
        }
        break;
    default:
        break;
}

function showMainHead($dbname,$data) {
    # Assign Element
    foreach($data as $key=>$row) {
        echo "var ".$key." = document.getElementById('main_".$key."');";
        echo "if(".$key.") {";
            echo "if(".$key.".getAttribute('type')=='checkbox') {";
            if($row=='1') {
                echo $key.".checked=true;";
            } else {
                echo $key.".checked=false;";
            }
            echo "} else {".$key.".value='".$row."';}";
        echo "}";
    }
}
?>
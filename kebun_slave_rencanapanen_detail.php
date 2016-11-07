<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');
?>

<?php

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
	# Options
	$optBlok = getOrgBelow($dbname,$param['afdeling'],false,'blok');
	
	# Get Data
	$where = "kodeorg='".$param['afdeling']."' and bulan=".$param['bulan'].
            " and tahun=".$param['tahun'];
	//$cols = "kodeblok,tanggal,jumlah,jumlahha,jumlahpremi,jumlahpokok";
	$cols = "kodeblok,tanggal,jumlah,jumlahbungabetina,jumlahpokok";
	$query = selectQuery($dbname,'kebun_rencanapanen',$cols,$where);
	$data = fetchData($query);
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	}
	
	# Form
	$theForm2 = new uForm('formRencana','Form Sensus Produksi',2);
	$theForm2->addEls('kodeblok',$_SESSION['lang']['kodeblok'],'','select','L',25,$optBlok);
	$theForm2->addEls('tanggal',$_SESSION['lang']['tanggal'],'','text','L',25);
        $theForm2->_elements[1]->_attr['readonly'] = 'readonly';
        $theForm2->_elements[1]->_attr['onmousemove'] = 'setCalendar(this.id)';
        $theForm2->addEls('jumlah',$_SESSION['lang']['jumlah']."(JJG)",'0','textnum','R',25);
	$theForm2->addEls('jumlahbungabetina',$_SESSION['lang']['jumlahbungabetina']."(JJG)",'0','textnum','R',25);	
		
	//$theForm2->addEls('jumlahha',$_SESSION['lang']['jumlahha'],'0','textnum','R',25);
	//$theForm2->addEls('jumlahpremi',$_SESSION['lang']['jumlahpremi'],'0','textnum','R',25);
      //  $theForm2->_elements[4]->_attr['disabled'] = 'disabled';
	  
	$theForm2->addEls('jumlahpokok',$_SESSION['lang']['jumlahpokok']." ".$_SESSION['lang']['contoh'],'0','textnum','R',25);
	
	# Table
	$theTable2 = new uTable('tableRencana','Daftar Rencana Panen',$cols,$data,$dataShow);
	
	# FormTable
	$formTab2 = new uFormTable('ftRencanaPanen',$theForm2,$theTable2,null,array('kodeorg','bulan','tahun'));
        $formTab2->_target = "kebun_slave_rencanapanen_detail";
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	$formTab2->render();
	echo "</fieldset>";
	break;
    case 'add':
	$cols = array(
	    'kodeblok','tanggal','jumlah','jumlahbungabetina',
	    'jumlahpokok','kodeorg','bulan','tahun'
	);
	/*$cols = array(
	    'kodeblok','tanggal','jumlah','jumlahha','jumlahpremi',
	    'jumlahpokok','kodeorg','bulan','tahun'
	);*/
//        echo"<pre>";
//        print_r($param);
//        echo"</pre>";
//        exit("Error");
	$data = $param;
	unset($data['numRow']);
        $tgl=intval(substr($data['tanggal'],3,2));
        if($tgl!=$data['bulan'])
        {
            exit("Error:Tanggal tidak sesuai dengan bulan");
        }
        $data['tanggal'] = tanggalsystem($data['tanggal']);
	
	$query = insertQuery($dbname,'kebun_rencanapanen',$data,$cols);
        if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
	unset($data['kodeorg']);unset($data['bulan']);unset($data['tahun']);
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
	
	$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
    case 'edit':
	$data = $param;
	unset($data['notransaksi']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
        $tgl=intval(substr($data['tanggal'],3,2));
        if($tgl!=$data['bulan'])
        {
            exit("Error:Tanggal tidak sesuai dengan bulan");
        }
        $data['tanggal'] = tanggalsystem($data['tanggal']);
        
	$where = "kodeorg='".$param['kodeorg']."' and tahun=".$param['tahun'].
	    " and bulan=".$param['bulan']." and kodeblok='".$param['cond_kodeblok']."'";
	$query = updateQuery($dbname,'kebun_rencanapanen',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
	
    case 'delete':
	$where = "kodeorg='".$param['kodeorg']."' and tahun=".$param['tahun'].
	    " and bulan=".$param['bulan']." and kodeblok='".$param['kodeblok']."'";
	$query = "delete from `".$dbname."`.`kebun_rencanapanen` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}
?>
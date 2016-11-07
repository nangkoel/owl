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
	# Get Data
	$where = "nopengolahan='".$param['nopengolahan']."'";
	$cols = "kodeorg as station,tahuntanam,jammulai,jamselesai,jamstagnasi,downstatus,".
	    "keterangan";
	$query = selectQuery($dbname,'pabrik_pengolahanmesin',$cols,$where);
	$data = fetchData($query);
	
	# Options
	/*if(!empty($whereBarang)) {
	    $whereBarang = "kodebarang in (";
	    foreach($data as $key=>$row) {
		if($key==0) {
		    $whereBarang .= "'".$row['kodebarang']."'";
		} else {
		    $whereBarang .= ",'".$row['kodebarang']."'";
		}
	    }
	    $whereBarang .= ")";
	    $optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
		$whereBarang);
	} else {
	    $optBarang = array();
	}*/
	$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "induk='".$param['kodeorg']."'");
	$optMesin = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE' and induk='".end(array_reverse(array_keys($optOrg)))."'");
	$optMesinAll = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE'",'0',true);
		
     //   $optDwnStat = makeOption($dbname,'pabrik_pengolahanmesin','downstatus','','7');
	
	$optDwnStat=array('EDT'=>'EDT : Emergency Downtime','SDT'=>'SDT : Sequential Downtime','CDT'=>'CDT : Commercial Downtime');
	
	
	# Data Show
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['station'] = $optOrg[$row['station']];
	    $dataShow[$key]['tahuntanam'] = $optMesinAll[$row['tahuntanam']];
	}
	
	# Form
	$theForm1 = new uForm('mesinForm',$_SESSION['lang']['form']." ".$_SESSION['lang']['mesin'],2);
	$theForm1->addEls('station',$_SESSION['lang']['station'],'','select','L',25,$optOrg);
	$theForm1->_elements[0]->_attr['onchange']='updMesin()';
	$theForm1->addEls('tahuntanam',$_SESSION['lang']['mesin'],'0','select','L',25,$optMesin);
	
    


    
    
    $theForm1->addEls('jammulai',$_SESSION['lang']['jammulaistagnasi'],'0','jammenit','R',10);
	$theForm1->addEls('jamselesai',$_SESSION['lang']['jamselesaistagnasi'],'0','jammenit','R',10);
    
    
	$theForm1->addEls('jamstagnasi',$_SESSION['lang']['jamstagnasi'],'0','textnum','R',10);
        $theForm1->addEls('downstatus',$_SESSION['lang']['downstatus'],'0','select','L',25,$optDwnStat);
	$theForm1->addEls('keterangan',$_SESSION['lang']['keterangan'],'','text','L',50);
	#$theForm1->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20,null,null,'jumlahbarang_satuan');
	#$theForm1->addEls('jumlahbarang',$_SESSION['lang']['jumlahbarang'],'0','textnumwsatuan','L',10);
	
	# Table
	$theTable1 = new uTable('mesinTable',$_SESSION['lang']['tabel']." ".$_SESSION['lang']['mesin'],$cols,$data,$dataShow);
	
	# FormTable
	$formTab1 = new uFormTable('ftMesin',$theForm1,$theTable1,null,array('nopengolahan'));
	$formTab1->_target = "pabrik_slave_pengolahan_mesin";
	$formTab1->_addActions = array(
	    'material'=>array(
		'img'=>'detail1.png',
		'onclick'=>'showMaterial'
	    )
	);
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	$formTab1->render();
	echo "</fieldset>";
	break;
    case 'updMesin':
	$opt = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	    "tipe='STENGINE' and induk='".$param['station']."'");
	echo json_encode($opt);
	break;
    default:
	break;
}
?>
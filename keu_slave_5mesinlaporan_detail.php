<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
#include_once('lib/zGrid.php');
#include_once('lib/rGrid.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
	# Options
	$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',"detail=1",'2',true);
	$optTipe = getEnum($dbname,'keu_5mesinlaporandt','tipe');
	
	# Get Data
	$where = "kodeorg='".$param['kodeorg']."' and namalaporan='".$param['namalaporan']."'";
	$cols = "nourut,tipe,noakundari,noakunsampai,noakundisplay,keterangandisplay,
	    inputbit,rubahoperatr,variableoutput,operator,variablejadi,resetvariableoutput";
	$query = selectQuery($dbname,'keu_5mesinlaporandt',$cols,$where);
	$data = fetchData($query);
	$dataShow = $data;
	foreach($dataShow as $key=>$row) {
	    $dataShow[$key]['tipe'] = $optTipe[$row['tipe']];
	    #$dataShow[$key]['noakundari'] = $optAkun[$row['noakundari']];
	    #$dataShow[$key]['noakunsampai'] = $optAkun[$row['noakunsampai']];
	    #$dataShow[$key]['noakundisplay'] = $optAkun[$row['noakundisplay']];
	}
	$maxNo = 1;
	foreach($data as $row) {
	    if($row['nourut']>$maxNo) {
		$maxNo = $row['nourut'];
	    }
	}
	$maxNo++;
	
	# Form
	$theForm1 = new uForm('mesinForm','Form Detail',2);
	$theForm1->addEls('nourut',$_SESSION['lang']['nourut'],$maxNo,'textnum','R',10);
	$theForm1->addEls('tipe',$_SESSION['lang']['tipe'],'','select','L',25,$optTipe);
	$theForm1->addEls('noakundari',$_SESSION['lang']['noakundari'],' ','select','L',25,$optAkun);
	$theForm1->addEls('noakunsampai',$_SESSION['lang']['noakunsampai'],' ','select','L',25,$optAkun);
	$theForm1->addEls('noakundisplay',$_SESSION['lang']['noakundisplay'],' ','text','L',25);
	$theForm1->addEls('keterangandisplay',$_SESSION['lang']['keterangandisplay'],'','text','L',45);
	$theForm1->addEls('inputbit',$_SESSION['lang']['inputbit'],'','text','C',2);
	$theForm1->addEls('rubahoperatr',$_SESSION['lang']['ubahoperator'],'0','textnum','C',2);
	$theForm1->addEls('variableoutput',$_SESSION['lang']['variableoutput'],'0','textnum','C',10);
	$theForm1->addEls('operator',$_SESSION['lang']['operator'],'+','text','C',2);
	$theForm1->addEls('variablejadi',$_SESSION['lang']['variablejadi'],'0','textnum','C',10);
	$theForm1->addEls('resetvariableoutput',$_SESSION['lang']['resetvariableoutput'],'0','textnum','C',2);
	
	# Table
	$theTable1 = new uTable('mesinTable','Tabel Detail',$cols,$data,$dataShow);
	
	# FormTable
	$formTab1 = new uFormTable('ftMesin',$theForm1,$theTable1,null,array('kodeorg','namalaporan'));
	$formTab1->_target = "keu_slave_5mesinlaporan_detail";
	$formTab1->_nourutJs = true;
	
	#== Display View
	# Draw Tab
	echo "<fieldset><legend><b>Detail</b></legend>";
	$formTab1->render();
	echo "</fieldset>";
	break;
    case 'add':
	$tmpCol = "nourut,tipe,noakundari,noakunsampai,noakundisplay,keterangandisplay,".
	    "inputbit,rubahoperatr,variableoutput,operator,variablejadi,resetvariableoutput,kodeorg,namalaporan";
	$cols = explode(',',$tmpCol);
	$data = $param;
	unset($data['numRow']);
	
	# Additional Default Data
	#$data['kodekegiatan'] = '20420';$data['tahuntanam'] = 0;
	#$data['upahkerja'] = 0;$data['upahpremi'] = 0;
	#$data['statusblok'] = 0;$data['pekerjaanpremi'] = 0;
	
	$query = insertQuery($dbname,'keu_5mesinlaporandt',$data,$cols);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	
	unset($data['kodeorg']);
	unset($data['namalaporan']);
	
	$res = "";
	foreach($data as $cont) {
	    $res .= "##".$cont;
	}
	
	$result = "{res:\"".$res."\",theme:\"".$_SESSION['theme']."\"}";
	echo $result;
	break;
    case 'edit':
	$data = $param;
	unset($data['kodeorg']);
	unset($data['namalaporan']);
	foreach($data as $key=>$cont) {
	    if(substr($key,0,5)=='cond_') {
		unset($data[$key]);
	    }
	}
	$where = "kodeorg='".$param['kodeorg']."' and namalaporan='".
	    $param['namalaporan']."' and nourut='".$param['cond_nourut']."'";
	$query = updateQuery($dbname,'keu_5mesinlaporandt',$data,$where);
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	echo json_encode($param);
	break;
    case 'delete':
	$where = "kodeorg='".$param['kodeorg']."' and namalaporan='".
	    $param['namalaporan']."' and nourut='".$param['nourut']."'";
	$query = "delete from `".$dbname."`.`keu_5mesinlaporandt` where ".$where;
	if(!mysql_query($query)) {
	    echo "DB Error : ".mysql_error();
	    exit;
	}
	break;
    default:
	break;
}
?>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	case 'list':
		$where = "karyawanid=".$param['karyawanid']." and tipe='uang ganti'";
		$queryD = selectQuery($dbname,'sdm_pesangondt','*',$where,"no asc");
		$dataD = fetchData($queryD);
		
		$content = '';
		foreach($dataD as $row) {
			if($row['tipe']=='uang ganti') {
				$rp = explode('.',$row['rp']);
				$decLen = 0;
				if(count($rp)>2 and $rp[1]>0) {
					if(strlen($rp[1])>2) {
						$decLen = 2;
					} else {
						$decLen = strlen($rp[1]);
					}
				}
				$content .= "<tr id='ganti_".$row['no']."' no='".$row['no']."'>";
				$content .= "<td>".makeElement('ganti_narasi_'.$row['no'],'text',$row['narasi'],
					array('onkeyup'=>"changeBg(getById('ganti_".$row['no']."'),'#F4FF74')",'style'=>'width:276px'))."</td>";
				$content .= "<td>".makeElement('ganti_pengali_'.$row['no'],'textnum',$row['pengali'],
					array('style'=>'width:150px','placeholder'=>'Pengali',
					      'onkeyup'=>"changeBg(getById('ganti_".$row['no']."'),'#F4FF74');calcGanti('".$row['no']."')"))."</td><td>X</td>";
				$content .= "<td>".makeElement('ganti_rp_'.$row['no'],'textnum',number_format($row['rp'],$decLen),
					array('style'=>'width:150px','placeholder'=>'Rp',
						  'onkeyup'=>"changeBg(getById('ganti_".$row['no']."'),'#F4FF74');calcGanti('".$row['no']."')"))."</td><td>=</td>";
				$content .= "<td>".makeElement('ganti_total_'.$row['no'],'textnum',number_format($row['total']),
					array('dototal'=>'total-plus','style'=>'width:150px','disabled'=>'disabled'))."</td>";
				$content .= "<td style='text-align:center'><img src='images/".$_SESSION['theme']."/save.png' class=zImgBtn onclick='saveGanti(".$row['no'].")'></td>
					<td><img src='images/".$_SESSION['theme']."/delete.png' class=zImgBtn onclick='deleteGanti(".$row['no'].")'></td>";
				$content .= "</tr>";
			}
		}
		$res = array(
			'content' => $content,
		);
		echo json_encode($res);
		break;
	
    case 'add':
		if(empty($param['narasi']))
			exit("Warning: Description must not empty");
		if(empty($param['pengali']))
			exit("Warning: Multiplier must not zero");
		if(empty($param['rp']))
			exit("Warning: Rupiah must not zero");
		
		// Get Max No
		$queryNo = "select max(no) as no from ".$dbname.".sdm_pesangondt where karyawanid=".$param['karyawanid'];
		$resNo = fetchData($queryNo);
		if(empty($resNo)) {
			$no = 1;
		} else {
			$no = $resNo[0]['no']+1;
		}
		
		$data = array(
			'karyawanid' => $param['karyawanid'],
			'no' => $no,
			'tipe' => 'uang ganti',
			'narasi' => $param['narasi'],
			'pengali' => $param['pengali'],
			'rp' => $param['rp'],
			'total' => $param['pengali']*$param['rp'],
		);
		$query = insertQuery($dbname,'sdm_pesangondt',$data);
		mysql_query($query) or die("Error DB: ".mysql_error());
		updateTotal($dbname,$param);
		break;
	
	case 'save':
		$data = $param;
		$data['total'] = $data['pengali']*$data['rp'];
		unset($data['karyawanid']);
		unset($data['no']);
		$where = "karyawanid=".$param['karyawanid']." and no=".$param['no'];
		$query = updateQuery($dbname,'sdm_pesangondt',$data,$where);
		mysql_query($query) or die("Error DB: ".mysql_error());
		updateTotal($dbname,$param);
		break;
		
	case 'delete':
		$where = "karyawanid=".$param['karyawanid']." and no=".$param['no'];
		$query = deleteQuery($dbname,'sdm_pesangondt',$where);
		mysql_query($query) or die("Error DB: ".mysql_error());
		updateTotal($dbname,$param);
		break;
    default:
	break;
}

function updateTotal($dbname,$param) {
	# Get Data
	$where = "karyawanid=".$param['karyawanid'];
	$cols = "*";
	
	# Get Data
	$where = "karyawanid=".$param['karyawanid'];
	$cols = "*";
	$query = selectQuery($dbname,'sdm_pesangonht',$cols,$where);
	$data = fetchData($query);
	$dataH = $data[0];
	
	$queryD = selectQuery($dbname,'sdm_pesangondt',$cols,$where,"no asc");
	$dataD = fetchData($queryD);
	
	$subTotal = $dataH['perusahaan']+$dataH['kesalahanbiasa']+$dataH['kesalahanberat']+$dataH['uangganti'];
	$pph = 0;
	$totalPot = 0;
	foreach($dataD as $row) {
		if($row['tipe']=='uang ganti') {
			$subTotal += $row['total'];
		} elseif($row['tipe']=='potongan') {
			$totalPot += $row['total'];
		}
	}
	
	// Pph
	if($subTotal>50000000) {
		$sisa = $subTotal - 50000000;
		if($sisa>50000000) {
			$pph += 50000000*5/100;
			$sisa -= 50000000;
			if($sisa>400000000) {
				$pph += 400000000*15/100;
				$sisa -= 400000000;
				$pph += $sisa*25/100;
			} else {
				$pph += $sisa*15/100;
			}
		} else {
			$pph += $sisa*5/100;
		}
	}
	
	$totalH = $subTotal - $pph - $totalPot;
	
	$data = array(
		'total' => $totalH,
		'pph' => $pph,
		'updateby' => $_SESSION['standard']['userid']
	);
	$where = "karyawanid=".$param['karyawanid'];
	$query = updateQuery($dbname,'sdm_pesangonht',$data,$where);
	mysql_query($query) or die("Error DB: ".mysql_error());
}
?>
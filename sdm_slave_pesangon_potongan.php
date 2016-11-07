<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
	case 'list':
		$where = "karyawanid=".$param['karyawanid']." and tipe='potongan'";
		$queryD = selectQuery($dbname,'sdm_pesangondt','*',$where,"no asc");
		$dataD = fetchData($queryD);
		
		$content = '';
		foreach($dataD as $row) {
			if($row['tipe']=='potongan') {
				$rp = explode('.',$row['rp']);
				$decLen = 0;
				if(count($rp)>2 and $rp[1]>0) {
					if(strlen($rp[1])>2) {
						$decLen = 2;
					} else {
						$decLen = strlen($rp[1]);
					}
				}
				$content .= "<tr id='potongan_".$row['no']."'>";
				$content .= "<td>".makeElement('potongan_narasi_'.$row['no'],'text',$row['narasi'],
					array('onkeyup'=>"changeBg(getById('potongan_".$row['no']."'),'#F4FF74')",'style'=>'width:612px'))."</td>";
				$content .= "<td>".makeElement('potongan_total_'.$row['no'],'textnum',number_format($row['total']),
					array('dototal'=>'total-min','style'=>'width:150px',
						  'onkeyup'=>"changeBg(getById('potongan_".$row['no']."'),'#F4FF74');calcPotongan('".$row['no']."')"))."</td>";
				$content .= "<td style='text-align:center'><img src='images/".$_SESSION['theme']."/save.png' class=zImgBtn onclick='savePotongan(".$row['no'].")'></td>
					<td><img src='images/".$_SESSION['theme']."/delete.png' class=zImgBtn onclick='deletePotongan(".$row['no'].")'></td>";
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
		if(empty($param['total']))
			exit("Warning: Rp must not zero");
		
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
			'tipe' => 'potongan',
			'narasi' => $param['narasi'],
			'pengali' => 1,
			'rp' => $param['total'],
			'total' => $param['total'],
		);
		$query = insertQuery($dbname,'sdm_pesangondt',$data);
		mysql_query($query) or die("Error DB: ".mysql_error());
		updateTotal($dbname,$param);
		break;
	
	case 'save':
		$data = $param;
		$data['pengali'] = $data['total'];
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
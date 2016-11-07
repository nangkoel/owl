<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');

$proses = $_GET['proses'];
$param = $_POST;

switch($proses) {
    case 'showDetail':
		# Get Data
		$where = "karyawanid=".$param['karyawanid'];
		$cols = "*";
		$query = selectQuery($dbname,'sdm_pesangonht',$cols,$where);
		$data = fetchData($query);
		$dataH = $data[0];
		
		$queryD = selectQuery($dbname,'sdm_pesangondt',$cols,$where,"no asc");
		$dataD = fetchData($queryD);
		
		$content = "<fieldset><legend><b>Data Detail</b></legend>";
		$content .= "<div id='detailCont'><ol>";
		
		$subTotal = 0;
		// Penggantian Hak
		$content .= "<li><div style='font-weight:bold'>Uang Penggantian Hak</div>";
		$content .= "<table id='tableGanti'><tbody id=tBodyGanti>";
		foreach($dataD as $row) {
			if($row['tipe']=='uang ganti') {
				$rp = explode('.',$row['rp']);
				$decLen = 0;
				if((count($rp)>2) and $rp[1]>0) {
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
				$subTotal += $row['total'];
			}
		}
		$content .= "</tbody><tbody><tr id='ganti_add'>";
		$content .= "<td>".makeElement('ganti_narasi_add','text','',array('style'=>'width:276px'))."</td>";
		$content .= "<td>".makeElement('ganti_pengali_add','textnum','0',
			array('style'=>'width:150px','placeholder'=>'Pengali','onkeyup'=>"calcGanti('add')"))."</td><td>X</td>";
		$content .= "<td>".makeElement('ganti_rp_add','textnum','0',
			array('style'=>'width:150px','placeholder'=>'Rp','onkeyup'=>"calcGanti('add')"))."</td><td>=</td>";
		$content .= "<td>".makeElement('ganti_total_add','textnum','0',
			array('dototal'=>'total-plus','style'=>'width:150px','disabled'=>'disabled'))."</td>";
		$content .= "<td colspan=2>".makeElement('ganti_addBtn','btn',$_SESSION['lang']['tambah'],array('onclick'=>'addGanti()'))."</td>";
		$content .= "</tr>";
		$content .= "</tbody></table>";
		$content .= "</li>";
		
		// Pesangon
		$pesangon = $dataH['perusahaan'] + $dataH['kesalahanbiasa'] + $dataH['kesalahanberat'];
		$content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Pesangon</div>";
		$content .= "<div>".makeElement('detailPesangon','textnum',number_format($pesangon),
			array('dototal'=>'total-plus','style'=>'width:150px','disabled'=>true))."</div></div>";
		$subTotal += $pesangon;
		
		// Uang Pisah
		$content .= "<li><div><div style='font-weight:bold;width:621px;float:left'>Uang Pisah</div>";
		$content .= "<div>".makeElement('detailPesangon','textnum',number_format($dataH['uangpisah']),
			array('dototal'=>'total-plus','style'=>'width:150px','disabled'=>true))."</div></div>";
		$content .= "</li>";
		$subTotal += $dataH['uangpisah'];
		
		// Hitung PPh
		$pph = 0;
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
		
		// Total & PPh
		$content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Total&nbsp;&nbsp;</div>";
		$content .= "<div>".makeElement('subTotal','textnum',number_format($subTotal),
			array('style'=>'width:150px','disabled'=>true))."</div></div>";
		$content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Pph&nbsp;&nbsp;</div>";
		$content .= "<div>".makeElement('pph','textnum',number_format($pph),
			array('style'=>'width:150px','disabled'=>true))."</div></div>";
		$content .= "</li>";
		
		// Potongan
		$totalPot = 0;
		$content .= "<li><div style='font-weight:bold'>Potongan</div>";
		$content .= "<table id='tablePotongan'><tbody id=tBodyPotongan>";
		foreach($dataD as $row) {
			if($row['tipe']=='potongan') {
				$content .= "<tr id='potongan_".$row['no']."'>";
				$content .= "<td>".makeElement('potongan_narasi_'.$row['no'],'text',$row['narasi'],
					array('onkeyup'=>"changeBg(getById('potongan_".$row['no']."'),'#F4FF74')",'style'=>'width:612px'))."</td>";
				$content .= "<td>".makeElement('potongan_total_'.$row['no'],'textnum',number_format($row['total']),
					array('dototal'=>'total-min','style'=>'width:150px',
						  'onkeyup'=>"changeBg(getById('potongan_".$row['no']."'),'#F4FF74');calcPotongan('".$row['no']."')"))."</td>";
				$content .= "<td style='text-align:center'><img src='images/".$_SESSION['theme']."/save.png' class=zImgBtn onclick='savePotongan(".$row['no'].")'></td>
					<td><img src='images/".$_SESSION['theme']."/delete.png' class=zImgBtn onclick='deletePotongan(".$row['no'].")'></td>";
				$content .= "</tr>";
				$totalPot += $row['total'];
			}
		}
		$content .= "</tbody><tbody><tr id='potongan_add'>";
		$content .= "<td>".makeElement('potongan_narasi_add','text','',array('style'=>'width:612px'))."</td>";
		$content .= "<td>".makeElement('potongan_total_add','textnum','0',
			array('dototal'=>'total-min','style'=>'width:150px','onkeyup'=>"calcPotongan('add')"))."</td>";
		$content .= "<td colspan=2>".makeElement('potongan_addBtn','btn',$_SESSION['lang']['tambah'],array('onclick'=>'addPotongan()'))."</td>";
		$content .= "</tr>";
		$content .= "</tbody></table>";
		
		$total = $subTotal - $pph - $totalPot;
		$content .= "<div><div style='font-weight:bold;width:621px;float:left;text-align:right'>Diterima&nbsp;&nbsp;</div>";
		$content .= "<div>".makeElement('detailDiterima','textnum',number_format($total),
			array('style'=>'width:150px','disabled'=>true))."</div></div>";
		$content .= "</li>";
		
		$content .= "";
		$content .= "</ol></div></fieldset>";
		echo $content;
		break;
    
    default:
	break;
}
?>
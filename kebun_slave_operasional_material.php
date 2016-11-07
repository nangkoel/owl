<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$proses = $_GET['proses'];
$param = $_POST;
switch($proses) {
    case 'add':
        
                
                
        
		# Kegiatan harus ada
		$qKeg = selectQuery($dbname,'kebun_prestasi','*',"notransaksi='".$param['notransaksi']."'");
		$resKeg = fetchData($qKeg);
		if(empty($resKeg)) {
			echo 'Warning : Kegiatan harus diisi lebih dahulu';
			exit;
		}
		
		# Set Kolom dan Extract Data
		$cols = array(
			'kodeorg','kwantitasha','kodegudang','kodebarang','kwantitas',
			'notransaksi','hargasatuan'
		);
		$data = $param;  
		unset($data['numRow']);
		$data['hargasatuan'] = 0;
		
		# Barang harus ada
		if($data['kodebarang']=='' or $data['kodebarang']=='0') {
			echo 'Warning : Barang harus diisi';
			exit;
		}
                
                #validasi jumlah barang (kuantitas barang) <=0
                if($data['kwantitas']<=0) {
			echo 'Warning : Kuantitas barang tidak boleh 0';
			exit;
		}
		
		// Cek Ha
		cekHa($dbname,$param,$resKeg[0]['kodekegiatan']);
		
		# Insert
		$query = insertQuery($dbname,'kebun_pakaimaterial',$data,$cols);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		
		unset($data['notransaksi']);
		unset($data['hargasatuan']);
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
		
		# Cek Ha
		# Kegiatan harus ada
		$qKeg = selectQuery($dbname,'kebun_prestasi','*',"notransaksi='".$param['notransaksi']."'");
		$resKeg = fetchData($qKeg);
		
		// Cek Ha
		cekHa($dbname,$param,$resKeg[0]['kodekegiatan']);
		
		$where = "notransaksi='".$param['notransaksi']."' and kodeorg='".
			$param['cond_kodeorg']."' and kodebarang='".$param['cond_kodebarang']."'";
		$query = updateQuery($dbname,'kebun_pakaimaterial',$data,$where);
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		echo json_encode($param);
		break;
    case 'delete':
		$where = "notransaksi='".$param['notransaksi']."' and kodeorg='".
			$param['kodeorg']."' and kodebarang='".$param['kodebarang']."'";
		$query = "delete from `".$dbname."`.`kebun_pakaimaterial` where ".$where;
		if(!mysql_query($query)) {
			echo "DB Error : ".mysql_error();
			exit;
		}
		break;
    default:
    break;
}

function cekHa($dbname,$param, $kegiatan) {
	$qKeg = selectQuery($dbname,'setup_kegiatan','kodekegiatan,satuan',"kodekegiatan='".$kegiatan."'");
	$resKeg = fetchData($qKeg);
	
	if(trim($resKeg[0]['satuan'])=='HA') {
		# Cek Ha
		$tipe='BLOK';//default
		$str="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$param['kodeorg']."'";
		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$tipe=$bar->tipe;
		}
		if($tipe!='BLOK')
		{}
		else
		{
			$theHa = makeOption($dbname,'setup_blok','kodeorg,luasareaproduktif',
				"kodeorg='".$param['kodeorg']."'");
			if(strlen(trim($param['kodeorg']))==6)
			{
			
			}
			else if($param['kwantitasha']>$theHa[$param['kodeorg']]) {
				echo "Validation Error : Ha harus lebih kecil dari Luas produktif Blok:".$param['kodeorg'];
				exit;
			}
		}
	}
}
?>
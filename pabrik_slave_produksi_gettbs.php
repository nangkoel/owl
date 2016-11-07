<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
$proses = $_POST['proses'];
$tanggal=tanggalsystem($_POST['tanggal']);

switch($proses) {
	case 'gettbs':
	

		$i="select sum(beratbersih)- sum(kgpotsortasi) as tbsmasuk from ".$dbname.".pabrik_timbangan where tanggal='".$tanggal."' ";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error($conn));
		$d=mysql_fetch_assoc($n);
			$tbs=$d['tbsmasuk'];
		if($tbs=='')
		{
			$tbs=0;
		}
		else
		{
			$tbs;
		}
		
		echo $tbs;
		
		break;
		default;
}
?>
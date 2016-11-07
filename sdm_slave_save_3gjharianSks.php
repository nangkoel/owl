<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_POST['proses'];
$karyawanid=$_POST['karyawanid'];
$kdorg=$_POST['kdorg'];
$tgl=tanggalsystem($_POST['tgl']);
$upah=$_POST['upah'];


	

switch($proses)
{	
	case'savedata':
//	exit("Error:MASUK");
	//update kebun_prestasi set jumlahhk='3',hasilkerja='1.65' where notransaksi='20140301/H05E/TBM/001';
		$str="update ".$dbname.".sdm_absensidt set insentif='".$upah."' where karyawanid='".$karyawanid."' and kodeorg='".$kdorg."' and tanggal='".$tgl."'";
		//exit("Error:$str");
		if(mysql_query($str))
		{//case berhasil kosongin aja
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		
		
	
	break;
	default:
}

?>
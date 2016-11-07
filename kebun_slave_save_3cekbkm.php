<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_POST['proses'];
$not=$_POST['not'];
$hk=$_POST['hk'];
$hs=str_replace(",","",$_POST['hs']);
$jjg=str_replace(",","",$_POST['jjg']);

$notx=$_POST['notx'];

switch($proses)
{	
	case'savedata':
//	exit("Error:MASUK");
	//update kebun_prestasi set jumlahhk='3',hasilkerja='1.65' where notransaksi='20140301/H05E/TBM/001';
		$str="update ".$dbname.".kebun_prestasi set jjg='".$jjg."',jumlahhk='".$hk."',hasilkerja='".$hs."' where notransaksi='".$not."'";
		//exit("Error:$str");
		if(mysql_query($str))
		{//case berhasil kosongin aja
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		
		
	
	break;
        
        case'savedatax':
	//exit("Error:MASUK");
	//update kebun_prestasi set jumlahhk='3',hasilkerja='1.65' where notransaksi='20140301/H05E/TBM/001';
		$str="delete from ".$dbname.".kebun_aktifitas where notransaksi='".$notx."' ";
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
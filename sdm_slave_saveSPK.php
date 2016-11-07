<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$karyawanid 	= $_POST['karyawanid'];
$penandatangan	= $_POST['penandatangan'];
$tanggal 		= tanggalsystem($_POST['tanggal']);
$method 		= $_POST['method'];
$notr 			= $_POST['notr'];

if ($method == 'insert') {
	// ..validasi jika SPK sudah pernah dibuat
	$svSPK = "select karyawanid from ".$dbname.".sdm_pengalamankerja where karyawanid=".$karyawanid."";
	$qvSPK = mysql_query($svSPK) or die(mysql_error());
	if (mysql_num_rows($qvSPK)>0) {
		echo 'Surat Pengalaman Kerja ini sudah pernah dibuat';
		exit();
	}

	// ..get number and formating number
		// ..get namaorganisasi
		$nOrg = $_SESSION['org']['kodeorganisasi'];
		// ..get bulan dan tahun
			// ..convert bulan to romawi format number
			$blnSPK = substr($tanggal, 4,2);
			$aBln = array(1=>"I" ,2=>"II",3=>"III",4=>"IV",5=>"V",6=>"VI",7=>"VII",8=>"VIII",9=>"IX",10=>"X",11=>"XI",12=>"XII");
			$romawow = $aBln[$blnSPK];
			// ..get tahun
			$tahunSPK = substr($tanggal, 0,4); 

	$numSPK = "/SKK/".$nOrg."/".$romawow."/".$tahunSPK;
	$cerk="/SKK/".$nOrg."/";
	$sNot = "select notransaksi from ".$dbname.".sdm_pengalamankerja where notransaksi like '%".$cerk."%' and notransaksi like '%".$tahunSPK."'
	    order by notransaksi desc";
		//$numtrs = 0;
		$qNot = mysql_query($sNot) or die(mysql_error());
		$rNot=mysql_fetch_object($qNot);
		$numtrs = explode("/",$rNot->notransaksi);
		$thnskrg=date("Y");
		if($thnskrg!=$tahunSPK){
			$numtrs=0;
		}else{
			$numtrs=intval($numtrs[0])+1;
		}
		$numtrs = str_pad($numtrs, 3,"0",STR_PAD_LEFT);
		$numtrs = $numtrs.$numSPK;
		//exit("error:".$numtrs);
		// ..create automatic number format for notransaksi
		/*
		
		$numtrs = $numtrs+1;
		$numtrs = str_pad($numtrs, 3,"0",STR_PAD_LEFT);*/

	// ..INSERT into sdm_pengalamankerja
	$svSPK = "insert into ".$dbname.".sdm_pengalamankerja (`notransaksi`,`karyawanid`,`penandatangan`,`tanggal`,`updateby`) values ('".$numtrs."','".$karyawanid."','".$penandatangan."','".$tanggal."',".$_SESSION['standard']['userid'].")";
} else if ($method == 'delete') {
	$svSPK 	= "delete from ".$dbname.".sdm_pengalamankerja where notransaksi='".$notr."'";
} else if ($method == 'update') {
	$svSPK 	= "update ".$dbname.".sdm_pengalamankerja set `karyawanid`=".$karyawanid.",`penandatangan`=".$penandatangan.",`tanggal`=".$tanggal.",`updateby`=".$_SESSION['standard']['userid']." where  notransaksi='".$notr."'";
}

if (mysql_query($svSPK)) {
	
} else
	echo " Gagal:".addslashes(mysql_error());
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');



$kodeorg		=isset($_POST['kodeorg'])?		$_POST['kodeorg']:	'';
if(isset($_POST['kodekegiatan'])) {
	$kodekegiatan = $_POST['kodekegiatan'];
} else {
	$kodekegiatan	=isset($_POST['kegiatan'])?		$_POST['kegiatan']:	'';
}
$proses			=isset($_POST['proses'])?		$_POST['proses']:		'';
$notransaksi	=isset($_POST['notransaksi'])?	$_POST['notransaksi']:	'';
$tanggal		=isset($_POST['tanggal'])?		tanggalsystem($_POST['tanggal']):	'';
$jjg			=isset($_POST['jjg'])?			$_POST['jjg']:			'';
$jjgDis			=isset($_POST['jjgDisable'])?	$_POST['jjgDisable']:	'';
$hasilkerja		=isset($_POST['hasilkerja'])?	$_POST['hasilkerja']:	'';
$nik			=isset($_POST['nik'])?			$_POST['nik']:			'';
$jhk			=isset($_POST['jhk'])?			$_POST['jhk']:			'';
$umr			=isset($_POST['umr'])?			$_POST['umr']:			'';
$insentif		=isset($_POST['insentif'])?		$_POST['insentif']:		'';
$tahun = substr($tanggal,0,4);

switch($proses)//proses2= untuk proses post
{
	case 'cekAll':
		// Update Hasil Kerja
		if($jjgDis=='false') {
			#ambil kodeblok dari notransaksi
			$a="select kodeorg from ".$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
			$b=mysql_query($a);
			$c=mysql_fetch_assoc($b);
				$kdAfd=substr($c['kodeorg'],0,6);//exit("Error:$kdAfd");
			
			#ambil bjr
			//$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
			//	where blok like '%".substr($kodeblok,0,6)."%' ";	
			$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
				where blok like '%".$kdAfd."%' and tanggal<='".$tanggal."' and tanggal='".$tanggal."'";
			$y=mysql_query($x) or die (mysql_error($conn));
			$z=mysql_fetch_assoc($y);
			$bjr=$z['bjr'];
				
			$hasilkerja=$bjr*$jjg;
		}
		
		// Cari umr dari kebun_5psatuan
		$i="select rupiah,insentif from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);
		$rupiah=$d['rupiah'];
		$insentif=$d['insentif'];
		$umr=$rupiah*$hasilkerja;
		
		#cari umr dari sdm_5gajipokok untuk perbandingan umr dari kegiatan
		$tahun=substr($tanggal,0,4);//exit("Error:$tahun");
		$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
				"karyawanid='".$nik."' and tahun='".$tahun."' and idkomponen in (1,31)");
		$Umr = fetchData($qUMR);
		$zUmr=$Umr[0]['nilai']/25;	
		
		if(empty($rupiah)) {
			$umr = $zUmr;
		}
		
		#buat perbandingan HK
		if($umr>=$zUmr) {
			$jhk=1;
		} else {
			$jhk=$umr/@$zUmr;
		}
		
		$res = array(
			'hasilkerja' => number_format($hasilkerja,2),
			'jhk' => number_format($jhk,2),
			'umr' => number_format($umr,2),
			'insentif' => number_format($insentif,2)
		);
		echo json_encode($res);
		break;
		
	case'cekKonversi':
	 
			$i="select konversi from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
			//exit("Error:$i");
			$n=mysql_query($i) or die (mysql_error($conn));
			$d=mysql_fetch_assoc($n);
				$konversi=$d['konversi'];//exit("Error:$konversi");
				
				
			
			echo $konversi;
	break;
	
	case'cekBJR':
		$w="select konversi from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
		$i=mysql_query($w) or die (mysql_error($conn));
		$b=mysql_fetch_assoc($i);
			$konversi=$b['konversi'];
	break;
	
	
	case'getHasilKerja':
			$kdAfd=substr($kodeorg,0,6);//exit("Error:$kdAfd");
			
			#ambil bjr
			//$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
			//	where blok like '%".substr($kodeblok,0,6)."%' ";	
			$x="select sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb 
				where blok like '%".$kdAfd."%' and tanggal<='".$tanggal."' and tanggal='".$tanggal."'";
			$y=mysql_query($x) or die (mysql_error($conn));
			$z=mysql_fetch_assoc($y);
			$bjr=$z['bjr'];
			
			$hasilKerja=number_format($bjr*$jjg,2);
	 
			echo $hasilKerja; 
	break;
	//621010304
	
	case'getUMR1':
	
		$i="select * from ".$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
		
		//exit("Error:$i");
		$n=mysql_query($i);
		$d=mysql_fetch_assoc($n);
			$kodekegiatan=$d['kodekegiatan'];
		//exit("Error:$kodekegiatan");
		// Get Konversi
		$w="select kodekegiatan from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
		
		$i=mysql_query($w) or die (mysql_error($conn));
		$b=mysql_fetch_assoc($i);
		$konversi=$b['konversi'];
		$kdKeg=$b['kodekegiatan'];
		//exit("Error:$kdKeg");

		$a="select jumlah from ".$dbname.".sdm_5gajipokok where idkomponen=1 and tahun='".$tahun."' and karyawanid='".$nik."' ";
		$b=mysql_query($a) or die (mysql_error($conn));
		$c=mysql_fetch_assoc($b);
			$gajihar=$c['jumlah']/25;
		$gaji=$jhk*$gajihar;	

	echo $gaji;
	break;
	
	
	case'getUMR':
			
			#cari umr dari kebun_5psatuan
			$a="select kodekegiatan from ".$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
			$b=mysql_query($a);
			$c=mysql_fetch_assoc($b);
				$kodekegiatan=$c['kodekegiatan'];
				//exit("Error:$kodekegiatan");
			
			$i="select rupiah,insentif from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
			$n=mysql_query($i) or die (mysql_error($conn));
			$d=mysql_fetch_assoc($n);
				$rupiah=$d['rupiah'];
				$insentif=$d['insentif'];
			
			
				$umr=$rupiah*$hasilkerja;
				
				
			#cari umr dari sdm_5gajipokok untuk perbandingan umr dari kegiatan
			$tahun=substr($tanggal,0,4);//exit("Error:$tahun");
			
			$qUMR = selectQuery($dbname,'sdm_5gajipokok','sum(jumlah) as nilai',
					"karyawanid='".$nik."' and tahun='".$tahun."' and idkomponen in (1,31)");
			$Umr = fetchData($qUMR);
					$zUmr=$Umr[0]['nilai']/25;	
					//exit("Error:$zUmr.___$umr");
			#buat perbandingan HK
			if($umr>$zUmr)
			{	$jhk=1;
				//exit("Error:MASUK1.$umr.___.$zUmr");
			}
			else
			{
			//exit("Error:MASUK2.$umr.___.$zUmr");
				$jhk=$umr/@$zUmr;
			}
			
			//exit("Error:$umr.__.$zUmr.___.$insentif");
			//exit("Error:$insentif");
			echo number_format($umr,2)."###".number_format($jhk,2)."###".number_format($insentif,2); 
	break;
	
	
	case'getPremi':
	
		//exit("Error:masuk");
		#cari umr dari kebun_5psatuan
		$a="select kodekegiatan from ".$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
			$kodekegiatan=$c['kodekegiatan'];
			
		#cari insentif
		$i="select insentif from ".$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
		$n=mysql_query($i) or die (mysql_error($conn));
		$d=mysql_fetch_assoc($n);
			$rupiah=$d['rupiah'];
			$insentif=$d['insentif'];
			
		echo $insentif;//exit("Error:$insentif");

	break;
	
	
	case'getAbsen':
		//$whereAbsen="kodeabsen in ('H','L','MG')";
		//$optAbs = makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan',$whereAbsen);
		//exit("Error:masuk");
		#cari umr dari kebun_5psatuan
		$a="select tanggal from ".$dbname.".sdm_5harilibur where tanggal='".$tanggal."'";
		$b=mysql_query($a);
		$c=mysql_fetch_assoc($b);
			$tanggal=$c['tanggal'];//exit("Error:$tanggal");
			
		if($tanggal!='')
		{
			$cekMG=date('D', strtotime($tanggal));
			if($cekMG=='Sun')
			$optAbs.="<option value='MG'>Hari Minggu</option>";
			else
			$optAbs.="<option value='L'>Hari libur(diluar hari minggu)</option>";
		}
		else
		{
		}
		
		echo $optAbs;
	default;
    
}
?>
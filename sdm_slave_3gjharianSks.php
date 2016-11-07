<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$kdorg=$_POST['kdorgx'];
$per=$_POST['perx'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorgx'];
	$per=$_GET['perx'];
	$border="border=1";
}


$stream.="Data yang tampil adalah data yang salah";
$stream.="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center rowspan=2>No</td>
					<td align=center rowspan=2>Nama Karyawan</td>
					<td align=center rowspan=2>Nik</td>
					<td align=center rowspan=2>Karyawan ID</td>
					<td align=center rowspan=2>Kode Organisasi</td>
					<td align=center rowspan=2>tanggal</td>
					<td  align=center rowspan=2>Absensi</td>
					<td align=center colspan=2>Jam</td>
					<td align=center colspan=2>Upah</td>
				 </tr>
				  <tr>
					<td align=center>Masuk</td>
					<td align=center>Keluar</td>
					<td align=center>Sebelum</td>
					<td align=center>Sesudah</td>
				</tr>
			</thead>
			<tbody>";
		#gajipokok
		$iGapok="select jumlah,karyawanid from ".$dbname.".sdm_5gajipokok where tahun='".substr($per,0,4)."'";
		$nGapok=mysql_query($iGapok) or die (mysql_error($conn));
		while($dGapok=mysql_fetch_assoc($nGapok))
		{
			$listGapok[$dGapok['karyawanid']]=$dGapok['jumlah']/25;
		}

		/*$iKar="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan";
		$nKar=mysql_query($iKar) or die (mysql_error($conn));
		while($dKar=mysql_fetch_assoc($nKar))
		{
			$listNm[$dKar['karyawanid']]=$dKar['namakaryawan'];
			$listNik[$dKar['karyawanid']]=$dKar['nik'];	
		}*/		
		$iAbsen="SELECT a.*,b.tipekaryawan,b.nik,b.namakaryawan
				FROM ".$dbname.".`sdm_absensidt` a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				WHERE  kodeorg like '%".$kdorg."%' AND `tanggal` LIKE '%".$per."%' and tipekaryawan=4
				and absensi='s1' and insentif=0";
		$nAbsen=mysql_query($iAbsen) or die (mysql_error($conn));
		while($dAbsen=mysql_fetch_assoc($nAbsen))
		{
			$no+=1;
			$stream.= "
			<tr class=rowcontent id=rowx".$no.">
				<td  ".$bg." align=center>".$no."</td>
				<td  ".$bg." align=left>".$dAbsen['namakaryawan']."</td>
				<td  ".$bg." align=left>".$dAbsen['nik']."</td>
				<td  ".$bg." align=left id=karyawanidx".$no.">".$dAbsen['karyawanid']."</td>
				<td  ".$bg." align=left id=kdorgx".$no.">".$dAbsen['kodeorg']."</td>
				<td  ".$bg." align=left id=tglx".$no.">".tanggalnormal($dAbsen['tanggal'])."</td>
				<td  ".$bg." align=left>".$dAbsen['absensi']."</td>
				<td  ".$bg." align=left>".$dAbsen['jam']."</td>
				<td  ".$bg." align=left>".$dAbsen['jamPlg']."</td>
				<td  ".$bg." align=right>".$dAbsen['insentif']."</td>
				<td  ".$bg." align=left id=upahx".$no.">".$listGapok[$dAbsen['karyawanid']]."</td>
			</tr>";	
			
		}
		
		$stream.="</table>";
	
$stream.="<button class=mybutton onclick=saveAllx(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_cek_prestasi_kehadiran".$tglSkrg;
		if(strlen($stream)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}     
		break;
	
	default;
}


?>
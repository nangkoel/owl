<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');



$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$per=$_POST['per'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorg'];
	$per=$_GET['per'];
	$border="border=1";
}

$keNmTpKar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');

$stream="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center>No</td>
					<td align=center>Karyawan Id</td>
					<td align=center>Nik</td>
					<td align=center>Nama Karyawan</td>
					<td align=center>Tipe Karyawan</td>
					<td align=center>Kode Kendaraan</td>
					<td align=center>Jenis Kendaraan</td>
					
					<td align=center>Tahun Perolehan</td>
					<td align=center>Jumlah Hari Kerja</td>
					<td  align=center>Selisih Tahun</td>
					<td  align=center>Target</td>
					<td  align=center>Hari</td>
					<td  align=center>Pengali</td>
					<td  align=center>Premi</td>
				 </tr>
				  
			</thead>
			<tbody>";
			
			
		#gajipokok
		$iKar="select *,b.nik,b.namakaryawan,c.jenisvhc,b.tipekaryawan from ".$dbname.".vhc_5operator a
				left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				left join ".$dbname.".vhc_5master c on a.vhc=c.kodevhc
				  where aktif=1 and jenisvhc in ('DUMPTRUCK','TRUCKBUS','TRAKCTOR','TRUKTANKI')";
		$nKar=mysql_query($iKar) or die (mysql_error($conn));
		while($dKar=mysql_fetch_assoc($nKar))
		{
			$listKarVhc[$dKar['vhc']]=$dKar['vhc'];
			$listKarId[$dKar['vhc']]=$dKar['karyawanid'];
			$listKarNm[$dKar['vhc']]=$dKar['namakaryawan'];
			$listKarNik[$dKar['vhc']]=$dKar['nik'];
			$listKarTpKar[$dKar['vhc']]=$dKar['tipekaryawan'];
		}
		

		$iHari="select count(distinct(tanggal)) as jumlahhari,kodevhc from ".$dbname.".vhc_runht where jenisvhc in ('DUMPTRUCK','TRUCKBUS','TRAKCTOR','TRUKTANKI') and tanggal like '%".$per."%' group by kodevhc";			
		$nHari=mysql_query($iHari) or die (mysql_error($conn));
		while($dHari=mysql_fetch_assoc($nHari))
		{
			$listHari[$dHari['kodevhc']]=$dHari['jumlahhari'];
		}//count(distinct(tanggal))
		
		$iMaster="select * from ".$dbname.".vhc_5master";
		$nMaster=mysql_query($iMaster) or die (mysql_error($conn));
		while($dMaster=mysql_fetch_assoc($nMaster))
		{
			$jenisVhc[$dMaster['kodevhc']]=$dMaster['jenisvhc'];
			$tahunPerVhc[$dMaster['kodevhc']]=$dMaster['tahunperolehan'];
		}
		
		
		/*$iCuci="select count(*) as haricuci from ".$dbname.".vhc_runhk where tanggal like '%2014-03%' and premicuci!=0";
		$nCuci=mysql_query($iCuci) or die (mysql_error($conn));
		while($dCuci=mysql_fetch_assoc($nCuci))
		{
			$jenisVhc[$dMaster['kodevhc']]=$dCuci['haricuci'];
			
		}*/
		
		
		//$jenisVhc=makeOption($dbname,'vhc_5master','kodevhc,jenisvhc');
		//$detailVhc=makeOption($dbname,'vhc_5master','kodevhc,detailvhc');
		
		foreach($listKarVhc as $kdVhc)
		{
			$no+=1;
			
			$umur=substr($per,0,4)-$tahunPerVhc[$kdVhc];
			
			$stream.= "<tr class=rowcontent id=row".$no.">
				<td  ".$bg." align=center>".$no."</td>
				<td  ".$bg." align=left>".$listKarId[$kdVhc]."</td>
				<td  ".$bg." align=left>".$listKarNik[$kdVhc]."</td>
				<td  ".$bg." align=left>".$listKarNm[$kdVhc]."</td>
				
				<td  ".$bg." align=left>".$keNmTpKar[$listKarTpKar[$kdVhc]]."</td>
				
				<td  ".$bg." align=left>".$kdVhc."</td>
				<td  ".$bg." align=left>".$jenisVhc[$kdVhc]."</td>
				<td  ".$bg." align=left>".$tahunPerVhc[$kdVhc]."</td>
				<td  ".$bg." align=left>".$listHari[$kdVhc]."</td>
				<td  ".$bg." align=left>".$umur."</td>";
				if($jenisVhc[$kdVhc]=='TRAKCTOR')
				{
					$pengali=11000;
					if($umur<=5)
						$target=25;
					else
						$target=22;
				}
				else
				{
					$target=22;
					$pengali=20000;
				}
				$dapatHari=$listHari[$kdVhc]-$target;
				if($dapatHari<0)
					$dapatHari=0;
				else
					$dapatHari=$dapatHari;
				
				$stream.="<td  ".$bg." align=left>".$target."</td>";
				$stream.="<td  ".$bg." align=left>".$dapatHari."</td>";
				$stream.="<td  ".$bg." align=left>".$pengali."</td>";
				$stream.="<td  ".$bg." align=left>".$dapatHari*$pengali."</td>";
			$stream.="</tr>";	
			
		}
		
		
				
		
		
		
		$stream.="</table>";/*if($proses=='excel')
				$nik="'".$dAbsen['nik'];
			else
				$nik=$dAbsen['nik'];*/
	
$stream.="<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="laporan_upah_salah".$tglSkrg;
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
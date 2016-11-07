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


$stream.="Data yang tampil adalah data yang salah";
$stream.="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center rowspan=2>No</td>
					<td align=center colspan=4>Prestasi</td>
					<td align=center colspan=4>Absensi</td>
					
				  </tr>
				  <tr>
					<td align=center>Notransaksi</td>
					<td align=center>HK</td>
                                        <td align=center>JJG</td>
					<td align=center>Hasil Kerja</td>
					<td align=center>Notransaksi</td>
					<td align=center>HK</td>
                                        <td align=center>JJG</td>
					<td align=center>Hasil Kerja</td>
					
				</tr>
			</thead>
			<tbody>";
			
		/*$i="SELECT notransaksi,sum(jumlahhk) as jumlahhk,sum(hasilkerja) as hasilkerja
			FROM ".$dbname.".`kebun_prestasi` where notransaksi in (select notransaksi from ".$dbname.".kebun_aktifitas 
			where kodeorg='".$kdorg."' and tanggal like '%".$per."%' and tipetransaksi!='PNN') group by notransaksi";//echo $i;//where jhk>'20'
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$no+=1;
				$stream.= "<tr class=rowcontent id=row".$no.">";
				$stream.= "<td align=center>".$no."</td>";
				$stream.= "<td align=left id=notransaksi".$no.">".$d['notransaksi']."</td>";
				$stream.= "<td align=right>".$d['jumlahhk']."</td>";
				$stream.= "<td align=right>".$d['hasilkerja']."</td>";
			$stream.= "</tr>";
		}*/
		
		#pres
                $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
                      periode='".$per2."' and kodeorg='".$kdorg2."'";
                $res=fetchData($str);
		$iPres="SELECT notransaksi,sum(jumlahhk) as jumlahhk,sum(hasilkerja) as hasilkerja,sum(jjg) as jjg
				FROM ".$dbname.".`kebun_prestasi` where notransaksi in 
				(select notransaksi from ".$dbname.".kebun_aktifitas where kodeorg='".$kdorg."'
                                and tanggal between '".$res[0]['tanggalmulai']."' and '".$res[0]['tanggalsampai']."'
                                and tipetransaksi!='PNN' and jurnal=0) group by notransaksi";//echo $i;//where jhk>'20'
		$nPres=mysql_query($iPres) or die (mysql_error($conn));
		while($dPres=mysql_fetch_assoc($nPres))
		{
			$listTran[$dPres['notransaksi']]=$dPres['notransaksi'];
			$listHkPres[$dPres['notransaksi']]=$dPres['jumlahhk'];
			$listHslPres[$dPres['notransaksi']]=$dPres['hasilkerja'];
                        $listJjgPres[$dPres['notransaksi']]=$dPres['jjg'];
		}
		
		/*SELECT notransaksi,sum(jhk) as jhk,sum(hasilkerja) as hasilkerja
FROM `kebun_kehadiran` where notransaksi in (select notransaksi from kebun_aktifitas where kodeorg='h01e' and tanggal like '%2014-03%' and tipetransaksi!='PNN') group by notransaksi
*/
		#absensi
		$iAbs="SELECT notransaksi,sum(jhk) as jumlahhk,sum(hasilkerja) as hasilkerja,sum(jjg) as jjg
				FROM ".$dbname.".`kebun_kehadiran` where notransaksi in 
				(select notransaksi from ".$dbname.".kebun_aktifitas where kodeorg='".$kdorg."'
                                and tanggal between '".$res[0]['tanggalmulai']."' and '".$res[0]['tanggalsampai']."'
                                and tipetransaksi!='PNN'  and jurnal=0) group by notransaksi";
		$nAbs=mysql_query($iAbs) or die (mysql_error($conn));
		while($dAbs=mysql_fetch_assoc($nAbs))
		{
			$listTran[$dAbs['notransaksi']]=$dAbs['notransaksi'];
			$listHkAbs[$dAbs['notransaksi']]=$dAbs['jumlahhk'];
			$listHslAbs[$dAbs['notransaksi']]=$dAbs['hasilkerja'];
                         $listJjgAbs[$dAbs['notransaksi']]=$dAbs['jjg'];
			
		}
		
		
		
		foreach($listTran as $notran)
		{
			//if($listHkPres[$notran]==$listHkAbs[$notran])$cekHk="";else$cekHk="SALAH";
			//if($listHslPres[$notran]==$listHslAbs[$notran])$cekHs="";else$cekHs="SALAH";
			
			//if($listHkPres[$notran]!=$listHkAbs[$notran] || $listHslPres[$notran]!=$listHslAbs[$notran])$bg="bgcolor=#FF0000";else$bg="";
			if(number_format($listJjgPres[$notran],2) != number_format($listJjgAbs[$notran],2) || number_format($listHkPres[$notran],2)!=number_format($listHkAbs[$notran],2) || number_format($listHslPres[$notran],2)!=number_format($listHslAbs[$notran],2))
			{
				$no+=1;
				$stream.= "
				<tr class=rowcontent id=row".$no.">
					<td  ".$bg." align=center>".$no."</td>
					<td  ".$bg." align=left id=not".$no.">".$notran."</td>
					<td ".$bg." align=right >".number_format($listHkPres[$notran],2)."</td>
                                        <td ".$bg." align=right >".number_format($listJjgPres[$notran],2)."</td>
					<td  ".$bg." align=right >".number_format($listHslPres[$notran],2)."</td>
					<td ".$bg." align=left>".$notran."</td>
					<td ".$bg." align=right id=hk".$no.">".number_format($listHkAbs[$notran],2)."</td>
                                         <td ".$bg." align=right id=jjg".$no.">".number_format($listJjgAbs[$notran],2)."</td>   
					<td ".$bg." align=right id=hs".$no.">".number_format($listHslAbs[$notran],2)."</td>
					
				</tr>";
			}
		
			
			
		}
		$stream.="</table>";
	
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
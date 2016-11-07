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


$stream.="<i><b>* Data yang tampil adalah data yang salah</b></i>";
$stream.="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center rowspan=2>No</td>
					<td align=center rowspan=2>Nama Karyawan</td>
					<td align=center rowspan=2>Nik</td>
					<td align=center rowspan=2>Karyawan ID</td>
					<td align=center rowspan=2>Kode Organisasi</td>
					<td align=center rowspan=2>Hari</td>
					<td align=center rowspan=2>Tanggal</td>
					<td align=center rowspan=2>Absensi</td>
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
		#periodegaji
		$iPer="select * from ".$dbname.".sdm_5periodegaji where kodeorg ='".$kdorg."'  and periode='".$per."'";
		$nPer=mysql_query($iPer) or die (mysql_error($conn));
		while($dPer=mysql_fetch_assoc($nPer))
		{
			$mulai=$dPer['tanggalmulai'];
			$sampai=$dPer['tanggalsampai'];
		}

		#gajipokok
		$iGapok="select jumlah,karyawanid from ".$dbname.".sdm_5gajipokok where idkomponen=1 and tahun='".substr($per,0,4)."'";
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
		//$iAbsen="select * from ".$dbname.".sdm_absensidt where absensi='H' and insentif=0 and tanggal like '%".$per."%' 
			//	and kodeorg like '%".$kdorg."%' and karyawanid in (select karyawanid from ".$dbname.".datakaryawan where tipekaryawan='4')";
				
		
		$iAbsen="SELECT a.*,b.tipekaryawan,b.nik,b.namakaryawan
				FROM ".$dbname.".`sdm_absensidt` a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				WHERE  kodeorg like '%".$kdorg."%' AND `tanggal` BETWEEN '".$mulai."' AND '".$sampai."' and tipekaryawan=4 and absensi in (select kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen not in ('MG'))";		
								
		$nAbsen=mysql_query($iAbsen) or die (mysql_error($conn));
		while($dAbsen=mysql_fetch_assoc($nAbsen))
		{
			/*$listKar[$dAbsen['karyawanid']]=$dAbsen['karyawanid'];
			$listKdOrg[$dAbsen['karyawanid']]=$dAbsen['kodeorg'];
			$listTgl[$dAbsen['karyawanid']]=$dAbsen['tanggal'];
			$listAbs[$dAbsen['karyawanid']]=$dAbsen['absensi'];
			$listJamMsk[$dAbsen['karyawanid']]=$dAbsen['jam'];
			$listJamPlg[$dAbsen['karyawanid']]=$dAbsen['jamPlg'];
			$listUpah[$dAbsen['karyawanid']]=$dAbsen['insentif'];
			$listPremi[$dAbsen['karyawanid']]=$dAbsen['premi'];
			$listDenda[$dAbsen['karyawanid']]=$dAbsen['penaltykehadiran'];*/
			
			$_POST['tglDt']=tanggalnormal($dAbsen['tanggal']);
			$umr=$listGapok[$dAbsen['karyawanid']];
			
			$_POST['jamPlg']=$dAbsen['jamPlg'];
			$_POST['jmMulai']=$dAbsen['jam'];
			
			//echo $_POST['jmMulai'].___;
			
			
			if($_POST['jamPlg']=='00:00'){
				$_POST['jmMulai']="00:00";
			}
			$jm1=explode(":",$_POST['jmMulai']);
			$jm2=explode(":",$_POST['jamPlg']);

			$dtTmbh=0;
			if($jm2<$jm1){
				$dtTmbh=1;
			}
			$qwe=date('D', strtotime($dAbsen['tanggal']));
			//exit("error: ".$qwe);
			$wktmsk=mktime(intval($jm1[0]),intval($jm1[1]),intval($jm1[2]),intval(substr($_POST['tglDt'],3,2)),intval(substr($_POST['tglDt'],0,2)),substr($_POST['tglDt'],6,4));
			$wktplg=mktime(intval($jm2[0]),intval($jm2[1]),intval($jm2[2]),intval(substr($_POST['tglDt'],3,2)),intval(substr($_POST['tglDt'],0,2)+$dtTmbh),substr($_POST['tglDt'],6,4));
			$slsihwaktu=$wktplg-$wktmsk;
			$sisa = $slsihwaktu % 86400;
			$jumlah_jam = floor($sisa/3600);  
			//exit("error:".$jumlah_jam);
			/*if($jumlah_jam>=7){
				$upahHarian=$umr;
			}else{
				$upahHarian=($jumlah_jam/7)*$umr;
				
			}*/
                        
                        
                        if($qwe=='Sat'){
                                        if($jumlah_jam>=5){
                                            $upahHarian=$umr;
                                        }else{
                                            $upahHarian=($jumlah_jam/5)*$umr;    
                                        }    
                        }else{
                                        if($jumlah_jam>=7){
                                            $upahHarian=$umr;
                                        }else{
                                            $upahHarian=($jumlah_jam/7)*$umr;
                                            
                                            //exit("Error:$upahHarian._.$jumlah_jam");
                                        }
                        }
                        if ($dAbsen['absensi']=='C' and $dAbsen['insentif']>0)
                            $upahHarian=$umr;
			if (round($dAbsen['insentif'],4)!=round($upahHarian,4)){
			
                            $no+=1;
                            $hari=date('D', strtotime($dAbsen['tanggal']));
                            if ($_SESSION['language']=='ID'){
                                switch ($hari):
                                   case "Mon": $hari="Sen"; break;
                                   case "Tue": $hari="Sel"; break;
                                   case "Wed": $hari="Rab"; break;
                                   case "Thu": $hari="Kam"; break;
                                   case "Fri": $hari="Jum"; break;
                                   case "Sat": $hari="Sab"; break;
                                   case "Sun": $hari="Mggu"; break;
                                endswitch;
                            }
                            $stream.= "
                            <tr class=rowcontent id=row".$no.">
                                    <td  ".$bg." align=center>".$no."</td>
                                    <td  ".$bg." align=left>".$dAbsen['namakaryawan']."</td>";
                            if($proses=='excel')
                                    $nik="'".$dAbsen['nik'];
                            else
                                    $nik=$dAbsen['nik'];
                            $stream.= "	<td  ".$bg." align=left>".$nik."</td>
                                    <td  ".$bg." align=left id=karyawanid".$no.">".$dAbsen['karyawanid']."</td>
                                    <td  ".$bg." align=left id=kdorg".$no.">".$dAbsen['kodeorg']."</td>
                                    <td  ".$bg." align=left id=hari".$no.">".$hari."</td>
                                    <td  ".$bg." align=left id=tgl".$no.">".tanggalnormal($dAbsen['tanggal'])."</td>
                                    <td  ".$bg." align=left>".$dAbsen['absensi']."</td>
                                    <td  ".$bg." align=left>".$dAbsen['jam']."</td>
                                    <td  ".$bg." align=left>".$dAbsen['jamPlg']."</td>
                                    <td  ".$bg." align=right>".$dAbsen['insentif']."</td>
                                    <td  ".$bg." align=right id=upah".$no.">".$upahHarian."</td>
                            </tr>";	
                        }
		}
		$stream.="<br><button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		$stream.="</table>";
	

		
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
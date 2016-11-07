<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$kdorg=$_POST['kdorgCu'];
$per=$_POST['perCu'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorgCu'];
	$per=$_GET['perCu'];
	$border="border=1";
}


if($kdorg=='' || $per=='')
{
	exit("Error:Field Empty");
}

#nyari jumlah hari dalam 1 bulan buat koreksi jumlah cuci
$bulan=substr($per,5,2);
$tahun=substr($per,0,4);
$jumHari= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

//$iHr="select distinct periode  from ".$dbname.".setup_periodeakuntansi where periode='".$per."' ";
//echo $iHr;

$stream="Warna Merah adalah salah input. harap perhatikan jumlah banyak cucian mobilnya";

$stream.="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center>No</td>
					<td align=center>".$_SESSION['lang']['nik']."</td>
                                        <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td align=center>".$_SESSION['lang']['tipekaryawan']."</td>
                                        <td align=center>".$_SESSION['lang']['kodevhc']."</td>
                                        <td align=center>".$_SESSION['lang']['jenisvch']."</td>
					<td align=center>Jumlah Cuci</td>
					<td align=center>Premi Cuci</td>
					<td  align=center>Premi Lain</td>
					<td  align=center>".$_SESSION['lang']['total']."</td>
				 </tr>
			</thead>
			<tbody>";
			
		$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');	
		$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');	
		$tpKar=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan');
		$keNmTpKar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
		$vhc=makeOption($dbname,'vhc_5operator','karyawanid,vhc');
		$vhcJenis=makeOption($dbname,'vhc_5master','kodevhc,jenisvhc');
                $ptKar=  makeOption($dbname, 'datakaryawan', 'karyawanid,kodeorganisasi');
                
                $arrPer=explode("-",$per);
                if (($arrPer[1]-1)==0) {
                    $prdlalu=($arrPer[0]-1)."-12";
                } else {
                    $prdlalu=$arrPer[0]."-".($arrPer[1]-1);
                    if (strlen($prdlalu)==6)
                        $prdlalu=$arrPer[0]."-0".($arrPer[1]-1);
                }
//		 $prd=explode("-",$per);
//                if($prd[0]!=(date("Y"))){
//                    $prdlalu=($prd[0]-1)."-12";
//                }else{
//                    $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
//                    $prdlalu=$prd[0]."-".$bln;
//                }
		#ambil cut off bulan lalu
                $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$prdlalu."' and kodeorg='".$kdorg."'";
                $qDt=  mysql_query($sDt) or die(mysql_error($conn));
                $rDtLalu=  mysql_fetch_assoc($qDt);
                $tglCutblnlalu=$rDtLalu['tglcutoff'];
                if($tglCutblnlalu==''){
                    exit("error: Cut off date can't empty");
                }
                $tglcutblnlalu=nambahHari(tanggalnormal($tglCutblnlalu),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
                
                #ambil cut off bulan ini
                $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$per."' and kodeorg='".$kdorg."'";
                $qDt=  mysql_query($sDt) or die(mysql_error($conn));
                $rDt=  mysql_fetch_assoc($qDt);
                $tglCutblnini=$rDt['tglcutoff'];
                //$tglcutblnIni=nambahHari(tanggalnormal($tglCutblnini),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
                if($prdlalu=='2014-02'){
                    $tglCutblnlalu='2014-02-28';
                }
                #cek transaksi di antara tanggal cut off bln lalu smp dengan tanggal cut off bln ini sudah terposting atau belum
                $scek="select * from ".$dbname.".vhc_runht "
                    . "where tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' and kodeorg='".$kdorg."' and posting=0 "
                    . "and notransaksi not in (select notransaksi from ".$dbname.".vhc_rundt) order by tanggal asc";
                //exit("error:".$scek);
                
                
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek=  mysql_num_rows($qcek);
                if($rcek!=0){
                   $sdel="delete from ".$dbname.".vhc_runht where tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' and kodeorg='".$kdorg."' and posting=0 "
                    . "and notransaksi not in (select notransaksi from ".$dbname.".vhc_rundt) ";
                   if(!mysql_query($sdel)){
                       exit("error:".mysql_error($conn)."___".$sdel);
                   }else{
                       $scek="select * from ".$dbname.".vhc_runht "
                    . "where tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' and kodeorg='".$kdorg."' and posting=0  order by tanggal asc";
                     $qcek=  mysql_query($scek) or die(mysql_error($conn));
                     $rcek=  mysql_num_rows($qcek);
                     if($rcek!=0){
                        while($rTgl=  mysql_fetch_assoc($qcek)){
                           $notrans[$rTgl['tanggal']]=$rTgl['tanggal'];
                        }
                       echo"Masih ada transaksi traksi yang belum terposting di tanggal :<pre>";
                       print_r($notrans);
                       echo"</pre>";
                       exit("error:");
                     }
                   }
                }else{
                    $scek="select * from ".$dbname.".vhc_runht "
                    . "where tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' and kodeorg='".$kdorg."' and posting=0  order by tanggal asc";
                     
                    $qcek=  mysql_query($scek) or die(mysql_error($conn));
                     $rcek=  mysql_num_rows($qcek);
                     if($rcek!=0){
                        while($rTgl=  mysql_fetch_assoc($qcek)){
                           $notrans[$rTgl['tanggal']]=$rTgl['tanggal'];
                        }
                       echo"Masih ada transaksi traksi yang belum terposting di tanggal :<pre>";
                       print_r($notrans);
                       echo"</pre>";
                       exit("error:");
                     }
                }
                #ambil cut off bulan ini
                $sDt="select distinct tglcutoff from ".$dbname.".sdm_5periodegaji where periode='".$per."' and kodeorg='".$kdorg."'";
                $qDt=  mysql_query($sDt) or die(mysql_error($conn));
                $rDt=  mysql_fetch_assoc($qDt);
                $tglCutblnini=$rDt['tglcutoff'];
                //$tglcutblnIni=nambahHari(tanggalnormal($tglCutblnini),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
		$iMaster="select sum(premicuci) as premicuci,sum(premiluarjam) as premiluarjam,idkaryawan,tanggal from ".$dbname.".vhc_runhk
				  where tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' group by idkaryawan";
                #jumlah hari
                $jumHari=0;
                // memecah string tanggal awal untuk mendapatkan
                // tanggal, bulan, tahun
                $pecah1 = explode("-", $tglcutblnlalu);
                $date1 = $pecah1[2];
                $month1 = $pecah1[1];
                $year1 = $pecah1[0];

                // memecah string tanggal akhir untuk mendapatkan
                // tanggal, bulan, tahun
                $pecah2 = explode("-", $tglCutblnini);
                $date2 = $pecah2[2];
                $month2 = $pecah2[1];
                $year2 =  $pecah2[0];

                // mencari total selisih hari dari tanggal awal dan akhir
                $jd1 = GregorianToJD($month1, $date1, $year1);
                $jd2 = GregorianToJD($month2, $date2, $year2);

                $jumHari = $jd2 - $jd1;
		
		$nMaster=mysql_query($iMaster) or die (mysql_error($conn));
		while($dMaster=mysql_fetch_assoc($nMaster))
                {
                    
                    
                    
			if($vhcJenis[$vhc[$dMaster['idkaryawan']]]=='JEEPPICKUP' && $ptKar[$dMaster['idkaryawan']]=='HIP')
                        {
				$premicuci=$dMaster['premicuci']*4500;
				$no+=1;
				if($dMaster['premicuci']>$jumHari)
					$bgErr="bgcolor=#FF0000";
				else
					$bgErr="";
				
				$premi=$premicuci+$dMaster['premiluarjam'];
				$stream.= "<tr class=rowcontent id=row".$no.">
					<td  ".$bgErr." align=center>".$no."</td>
					<td  ".$bgErr." align=left><input type=hidden value='".$dMaster['idkaryawan']."'  id=karyawanid".$no." />".$nikKar[$dMaster['idkaryawan']]."</td>
					<td  ".$bgErr." align=left>".$nmKar[$dMaster['idkaryawan']]."</td>
					<td  ".$bgErr." align=left>".$keNmTpKar[$tpKar[$dMaster['idkaryawan']]]."</td>
					<td  ".$bgErr." align=left>".$vhc[$dMaster['idkaryawan']]."</td>
					<td  ".$bgErr." align=left>".$vhcJenis[$vhc[$dMaster['idkaryawan']]]."</td>
					<td  ".$bgErr." align=right>".$dMaster['premicuci']."</td>
					<td  ".$bgErr." align=right>".number_format($premicuci,0)."</td>
					<td  ".$bgErr." align=right>".number_format($dMaster['premiluarjam'],0)."</td>
					<td  ".$bgErr." align=right><input type=hidden  id=premi".$no." value='".$premi."' />".number_format($premi,0)."</td>";
				$stream.="</tr>";	
			}
		}
		
		$stream.="</table>";/*if($proses=='excel')
				$nik="'".$dAbsen['nik'];
			else
				$nik=$dAbsen['nik'];*/
	
$stream.="<button class=mybutton onclick=saveAllCu(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="premi_cuci_".$tglSkrg;
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
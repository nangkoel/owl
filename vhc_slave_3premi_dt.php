<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');



$proses=$_GET['proses'];
$kdorg=$_POST['kdorgDt'];
$per=$_POST['perDt'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorgDt'];
	$per=$_GET['perDt'];
	$border="border=1";
}


if($kdorg=='' || $per=='')
{
	exit("Error:Field Empty");
}

$keNmTpKar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');

$stream="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center>No</td>
					<td align=center>".$_SESSION['lang']['nik']."</td>
                                        <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                        <td align=center>".$_SESSION['lang']['tipekaryawan']."</td>
                                        <td align=center>".$_SESSION['lang']['kodevhc']."</td>
                                        <td align=center>".$_SESSION['lang']['jenisvch']."</td>
                                        <td align=center>".$_SESSION['lang']['tahun']."</td>
                                        <td align=center>".$_SESSION['lang']['jhk']."</td>
					<td  align=center>Selisih Tahun</td>
					<td  align=center>Target</td>
					<td  align=center>Hari</td>
					<td  align=center>Pengali</td>
					<td  align=center>Premi</td>
				 </tr>
				  
			</thead>
			<tbody>";
			
			
		#gajipokok
		$iKar="select a.*,b.nik,b.namakaryawan,c.jenisvhc,b.tipekaryawan,b.lokasitugas from ".$dbname.".vhc_5operator a
				left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
				left join ".$dbname.".vhc_5master c on a.vhc=c.kodevhc
				  where aktif=1 and jenisvhc in ('DUMPTRUCK','TRUCKBUS','TRAKCTOR','TRUKTANKI') and kodeorg like '%".$kdorg."%' and lokasitugas like '%".$kdorg."%'";
				  
		$nKar=mysql_query($iKar) or die (mysql_error($conn));
		while($dKar=mysql_fetch_assoc($nKar))
		{
			$listKarVhc[$dKar['vhc']]=$dKar['vhc'];
			$listKarId[$dKar['vhc']]=$dKar['karyawanid'];
			$listKarNm[$dKar['vhc']]=$dKar['namakaryawan'];
			$listKarNik[$dKar['vhc']]=$dKar['nik'];
			$listKarTpKar[$dKar['vhc']]=$dKar['tipekaryawan'];
		}
                $arrPer=explode("-",$per);
                if (($arrPer[1]-1)==0) {
                    $prdlalu=($arrPer[0]-1)."-12";
                } else {
                    $prdlalu=$arrPer[0]."-".($arrPer[1]-1);
                    if (strlen($prdlalu)==6)
                        $prdlalu=$arrPer[0]."-0".($arrPer[1]-1);
                }
//                $prd=explode("-",$per);
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
                if($prdlalu=='2014-02'){
                    $tglCutblnlalu='2014-02-28';
                }
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
		$iHari="select count(distinct(tanggal)) as jumlahhari,kodevhc from ".$dbname.".vhc_runht where jenisvhc in ('DUMPTRUCK','TRUCKBUS','TRAKCTOR','TRUKTANKI') "
                        . "and tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."'  and kodeorg='".$kdorg."' group by kodevhc";
                //echo $iHari;
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
				<td  ".$bg." align=left><input type=hidden id=karyawanid".$no." value='".$listKarId[$kdVhc]."' />".$listKarNik[$kdVhc]."</td>
				<td  ".$bg." align=left>".$listKarNm[$kdVhc]."</td>
				
				<td  ".$bg." align=left>".$keNmTpKar[$listKarTpKar[$kdVhc]]."</td>
				
				<td  ".$bg." align=left>".$kdVhc."</td>
				<td  ".$bg." align=left>".$jenisVhc[$kdVhc]."</td>
				<td  ".$bg." align=left>".$tahunPerVhc[$kdVhc]."</td>
				<td  ".$bg." align=right>".$listHari[$kdVhc]."</td>
				<td  ".$bg." align=center>".$umur."</td>";
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
				$hslPremi=$dapatHari*$pengali;
				$stream.="<td  ".$bg." align=right>".$target."</td>";
				$stream.="<td  ".$bg." align=right>".$dapatHari."</td>";
				$stream.="<td  ".$bg." align=right>".number_format($pengali,0)."</td>";
				$stream.="<td  ".$bg." align=right><input type=hidden  id=premi".$no." value='".$hslPremi."' />".number_format($hslPremi,0)."</td>";
			$stream.="</tr>";	
		}
		$stream.="</table>";
$stream.="<button class=mybutton onclick=saveAllDt(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="premirawat_dt_".$tglSkrg;
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
<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$kdorg=$_POST['kdorgAb'];
$per=$_POST['perAb'];
if($proses=='excel')
{
	$kdorg=$_GET['kdorgAb'];
	$per=$_GET['perAb'];
	$border="border=1";
}

if($kdorg=='' || $per=='')
{
	exit("Error:Field Empty");
}


$thn=substr($per,0,4);


$nmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');	
$nikKar=makeOption($dbname,'datakaryawan','karyawanid,nik');	
$tpKar=makeOption($dbname,'datakaryawan','karyawanid,tipekaryawan');
$keNmTpKar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');


$stream="<table cellspacing='1' ".$border." class='sortable'>
			<thead class=rowheader>
				<tr class=rowheader>
					<td align=center>No</td>
					<td align=center>".$_SESSION['lang']['nik']."</td>
					<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
					<td align=center>".$_SESSION['lang']['tipekaryawan']."</td>
                                        <td  align=center>".$_SESSION['lang']['kodevhc']."</td>
                                        <td  align=center>".$_SESSION['lang']['tahun']."</td>
                                        <td  align=center>".$_SESSION['lang']['umur']."</td>
                                        <td align=center>HM per Karyawan</td>
					<td  align=center>".$_SESSION['lang']['total']." HM per ".$_SESSION['lang']['kodevhc']."</td>
                                        <td  align=center>".$_SESSION['lang']['basis']."</td>
                                        <td  align=center>".$_SESSION['lang']['selisih']."</td>
                                        <td  align=center>".$_SESSION['lang']['premi']." per ".$_SESSION['lang']['kodevhc']."</td>
                                        <td  align=center>".$_SESSION['lang']['premi']." per ".$_SESSION['lang']['namakaryawan']."</td>
				 </tr>
				  
			</thead>
			<tbody>";
			
		##thn perolehan
		$iThn="select tahunperolehan,kodevhc from ".$dbname.".vhc_5master where  jenisvhc in 
			   (select jenisvhc from ".$dbname.".vhc_5jenisvhc where kelompokvhc='AB') ";
		$nThn=mysql_query($iThn) or die (mysql_error($conn));
		while($dThn=mysql_fetch_assoc($nThn))
		{
			$listThnPer[$dThn['kodevhc']]=$dThn['tahunperolehan'];
		}
                $arrPer=explode("-",$per);
                if (($arrPer[1]-1)==0) {
                    $prdlalu=($arrPer[0]-1)."-12";
                } else {
                    $prdlalu=$arrPer[0]."-".($arrPer[1]-1);
                    if (strlen($prdlalu)==6)
                        $prdlalu=$arrPer[0]."-0".($arrPer[1]-1);
                }
//	        $prd=explode("-",$per);
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
                $sKend="select distinct sum(jumlah) as jmlh,kodevhc,jenisvhc,b.tanggal,c.idkaryawan from ".$dbname.".vhc_rundt a 
                left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi 
                left join ".$dbname.".vhc_runhk c on a.notransaksi=c.notransaksi 
                where b.tanggal between '".$tglcutblnlalu."' and '".$tglCutblnini."' and b.kodeorg='".$kdorg."' 
                and kodevhc!='' and b.jenisvhc in (select jenisvhc from ".$dbname.".vhc_5jenisvhc where kelompokvhc='AB' and jenisvhc!='TRAKCTOR') and (idkaryawan!='' or idkaryawan<>NULL)
                group by kodevhc,idkaryawan 
                order by kodevhc asc";
                //echo $sKend;
                $qKend=mysql_query($sKend) or die(mysql_error($conn));
                while($rKend=mysql_fetch_assoc($qKend)){
                    if(!is_null($rKend['idkaryawan'])&&($rKend['idkaryawan']!='')&&($rKend['kodevhc']!='')){
                    $dtVhc[$rKend['kodevhc']]=$rKend['kodevhc'];
                    $dtPrest[$rKend['kodevhc']]+=$rKend['jmlh'];
                    $dtPresPerKar[$rKend['kodevhc'].$rKend['idkaryawan']]+=$rKend['jmlh'];
                    $jmlHar[$rKend['kodevhc']]+=1;
                    $jnsVhc[$rKend['kodevhc']]=$rKend['jenisvhc'];
                    $jmlHar[$rKend['kodevhc'].$rKend['idkaryawan']]+=1;
                    $listKar[$rKend['idkaryawan']]=$rKend['idkaryawan'];
                    }
                }
        
		
		$iMaster="select * from ".$dbname.".vhc_5master where jenisvhc in (select jenisvhc from ".$dbname.".vhc_5jenisvhc where kelompokvhc='AB' and jenisvhc!='TRAKCTOR')";
		$nMaster=mysql_query($iMaster) or die (mysql_error($conn));
		while($dMaster=mysql_fetch_assoc($nMaster))
		{
			$jenisVhc[$dMaster['kodevhc']]=$dMaster['jenisvhc'];
			$tahunPerVhc[$dMaster['kodevhc']]=$dMaster['tahunperolehan'];
		}
                foreach($listKar as $kar)
		{
		foreach($dtVhc as $lstVhcDt){
		        if($dtPresPerKar[$lstVhcDt.$kar]!=''){
                            $no+=1;
			$stream.= "<tr class=rowcontent id=row".$no.">
				<td  ".$bg." align=center>".$no."</td>				
				<td  ".$bg." align=left><input type=hidden id=karyawanid".$no." value='".$kar."' />'".$nikKar[$kar]."</td>
				<td  ".$bg." align=left>".$nmKar[$kar]."</td>
				<td  ".$bg." align=left>".$keNmTpKar[$tpKar[$kar]]."</td>";
                                        $umur=$listThnPer[$lstVhcDt];   
                                        if($listThnPer[$lstVhcDt]>5){
                                            $umur=$thn-$listThnPer[$lstVhcDt];
                                        }
					if($umur<=5)
						$basis=175;
					else if ($umur>5 and $umur<=10)
						$basis=150;
					else
						$basis=125;		
					
                                        //$lstJam=$dtPresPerKar[$kar.$lstVhcDt]/;
					$lebihBasis=$dtPrest[$lstVhcDt]-$basis;
					
					if($lebihBasis<0)
						$lebihBasis=0;
					else
						$lebihBasis=$lebihBasis;
					
					$totalPremiVhc=$lebihBasis*5000;
					$premi[$lstVhcDt.$kar]=($dtPresPerKar[$lstVhcDt.$kar]/$dtPrest[$lstVhcDt])*$totalPremiVhc;
                                $stream.="<td  ".$bg." >".$lstVhcDt."</td>";
                                $stream.="<td  ".$bg." align=center >".$listThnPer[$lstVhcDt]."</td>";
                                $stream.="<td  ".$bg." align=center >".$umur."</td>";
                                $stream.="<td  ".$bg." align=right >".$dtPresPerKar[$lstVhcDt.$kar]."</td>";
                                $stream.="<td  ".$bg." align=right >".$dtPrest[$lstVhcDt]."</td>";
                                $stream.="<td  ".$bg." align=right >".$basis."</td>";
                                $stream.="<td  ".$bg." align=right >".$lebihBasis."</td>";
                                $stream.="<td  ".$bg." align=right>".number_format($totalPremiVhc,0)."</td>";
                                $stream.="<td  ".$bg." align=right ><input type=hidden id=premi".$no." value=".$premi[$lstVhcDt.$kar]." />".number_format($premi[$lstVhcDt.$kar],0)."</td>";
                                $stream.="</tr>";
                        }
                        }
		}
		$stream.="</table>";
	
$stream.="<button class=mybutton onclick=saveAllAb(".$no.");>".$_SESSION['lang']['proses']."</button>";	
		
switch($proses)
{
	case'preview':
	//exit("Error:MASUK");
		echo $stream;
	break;
	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="premirawat_alatberat_".$tglSkrg;
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
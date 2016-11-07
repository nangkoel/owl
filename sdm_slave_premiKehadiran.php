<?php
//ind
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$per=$_POST['per'];
$unit=$_POST['unit'];
$proses2=$_POST['proses'];
$periode=$_POST['periode'];
$karyawanid=$_POST['karyawanid'];
$premi=$_POST['premi'];

$arrXV=array('0'=>'X','1'=>'√');
$tahunGaji=substr($per,0,4);

if($_SESSION['empl']['bagian']!='IT'){
    $unit=$_SESSION['empl']['lokasitugas'];
}
$atgl="select * from ".$dbname.".sdm_5periodegaji where periode='".$per."' and kodeorg='".$unit."'";
//echo $atgl;
$btgl=mysql_query($atgl) or die(mysql_error($conn));
$ctgl=mysql_fetch_assoc($btgl);

	$tgl1=$ctgl['tanggalmulai'];
	$tgl2=$ctgl['tanggalsampai'];


$golkar=makeOption($dbname,'datakaryawan','karyawanid','kodegolongan');
$namagol=makeOption($dbname,'sdm_5golongan','kodegolongan','namagolongan');
$namatipe=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');

function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}

       
	   
	   
	   

$sGetKary="select sum(c.jumlah) as jumlah,a.kodegolongan,a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,a.tipekaryawan,subbagian from ".$dbname.".datakaryawan a 
           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan and lokasitugas='".$unit."'
		   left join ".$dbname.".sdm_5gajipokok c on a.karyawanid=c.karyawanid
		    where  a.tipekaryawan='4' and c.tahun='".$tahunGaji."' group by a.karyawanid order by namakaryawan asc";    
 
 //echo $sGetKary;
 //$sGetKar="";
 // echo $sGetKary; exit;
$rGetkary=fetchData($sGetKary);
foreach($rGetkary as $row => $kar)
{
   // $resData[$kar['karyawanid']][]=$kar['karyawanid'];
   	
	//$karyawanid[$kar['karyawanid']]=$kar['karyawanid'];
	$jumlahUmr[$kar['karyawanid']]=$kar['jumlah'];
    $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    $nikkar[$kar['karyawanid']]=$kar['nik'];
    $nmJabatan[$kar['karyawanid']]=$kar['namajabatan'];
    $sbgnb[$kar['karyawanid']]=$kar['subbagian'];
	$tipekaryawan[$kar['karyawanid']]=$kar['tipekaryawan'];
	$golongankar[$kar['karyawanid']]=$kar['kodegolongan'];
}  




		
		
switch($proses)
{
	case'preview':
	
	
	$xi="select distinct * from ".$dbname.".sdm_5periodegaji where periode='".$per."' 
              and kodeorg='".$unit."' and sudahproses='1'";
$xu=mysql_query($xi) or die(mysql_error($conn));
if(mysql_num_rows($xu)>0)
    $aktif2=false;
       else
     $aktif2=true;
  if(!$aktif2)
  {
      exit("Error:Periode gaji untuk ".$unit." sudah ditutup");
  }
  
  
 #periksa apakah sudah tutup buku

       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$per."' and 
             kodeorg='".$unit."' and tutupbuku=1";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
           $aktif=false;
       else
           $aktif=true;
  if(!$aktif)
  {
      exit("Error:Periode akuntansi untuk ".$unit." sudah tutup buku");
  } 
  
  if($per=='')
  {
	  exit("Error:Periode masih kosong");
  }
	
	
	
	
###########	
	
	
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		$tgl1=$tgl_1;
		$tgl2=$tgl_2;
	}
	
	$test = dates_inbetween($tgl1, $tgl2);
	if(($tgl2=="")&&($tgl1==""))
	{
		echo"warning: Periode Penggajian Belum Terinput";
		exit();
	}

	$jmlHari=count($test);
	//cek max hari inputan
	if($jmlHari>40)
	{
		echo"warning:Range tanggal tidak valid";
		exit();
	}

	$sAbsen="select kodeabsen from ".$dbname.".sdm_5absensi order by kodeabsen";
	$qAbsen=mysql_query($sAbsen) or die(mysql_error());
	$jmAbsen=mysql_num_rows($qAbsen);
	$colSpan=intval($jmAbsen)+2;
	echo"<table cellspacing='1' border='0' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td align=center>No</td>
	<td align=center>".$_SESSION['lang']['nama']."</td>
	<td align=center>".$_SESSION['lang']['nik']."</td>
	<td align=center>".$_SESSION['lang']['jabatan']."</td>
	<td align=center>".$_SESSION['lang']['subbagian']."</td>
	<td align=center>".$_SESSION['lang']['karyawanid']."</td>
	<td align=center>".$_SESSION['lang']['periode']."</td>
	";/*<td>UMP Bulan</td>
	<td>UMP Harian</td>*/
	foreach($test as $ar => $isi)
	{
		$qwe=date('D', strtotime($isi));
		echo"<td width=5px align=center>";
		if($qwe=='Sun')
			echo"<font color=red>".substr($isi,8,2)."</font>"; 
		else echo(substr($isi,8,2)); 
		echo"</td>";
	//	echo"<td>Std</td>";
	//	echo"<td>Upah Dapat</td>";
		
	}
	
	
	echo"
	<td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['absensi']."</td>
	<td align=center>".$_SESSION['lang']['upahpremi']."</td>";//<td>Jumlah Hari Hadir</td>
	$klmpkAbsn=array();
	foreach($test as $ar => $isi)
	{
		$qwe=date('D', strtotime($isi));
	//	echo"<td width=5px align=center>";
	//	if($qwe=='Sun')echo"<font color=red>".substr($isi,8,2)."</font>"; else echo(substr($isi,8,2)); 
		//echo"</td>";
	}
	while($rKet=mysql_fetch_assoc($qAbsen))
	{
		$klmpkAbsn[]=$rKet;
	//	echo"<td width=10px>".$rKet['kodeabsen']."</td>";
	}
	echo"
	</tr></thead>
	<tbody>";//<td>Jumlah</td>
	
	$resData[]=array();
	$hasilAbsn[]=array();
	$umrList[]=array();


			$sAbsn="select absensi,tanggal,karyawanid,kodeorg from ".$dbname.".sdm_absensidt 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '%".$unit."%'";
			 // echo $sAbsn;
			  
			  //exit("Error".$sAbsn);
			$rAbsn=fetchData($sAbsn);
			foreach ($rAbsn as $absnBrs =>$resAbsn)
			{
				if(!is_null($resAbsn['absensi']))
				{
					$umrList[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array('umr'=>'ind');
					$hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array('absensi'=>$resAbsn['absensi']);
              		$notran[$resAbsn['karyawanid']][$resAbsn['tanggal']].='ABSENSI:'.$resAbsn['kodeorg'].'__';
					$resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
				}

			}
                        
			$sKehadiran="select absensi,tanggal,karyawanid,notransaksi,umr from ".$dbname.".kebun_kehadiran_vw 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '%".$unit."%'";
			  //exit("Error".$sKehadiran);
			$rkehadiran=fetchData($sKehadiran);
			foreach ($rkehadiran as $khdrnBrs =>$resKhdrn)
			{	
				if($resKhdrn['absensi']!='')
				{
					$umrList[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array('umr'=>$resKhdrn['umr']);
					$hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array('absensi'=>$resKhdrn['absensi']);
					$notran[$resKhdrn['karyawanid']][$resKhdrn['tanggal']].='BKM:'.$resKhdrn['notransaksi'].'__';
			  		$resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
				}
			
			}
			
			
			
			
			$sPrestasi="select a.upahkerja,b.tanggal,a.jumlahhk,a.nik,a.notransaksi from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                            where b.notransaksi like '%PNN%' and b.kodeorg like '%".$unit."%' and b.tanggal between '".$tgl1."' and '".$tgl2."'";
                         //exit("Error".$sPrestasi);
			$rPrestasi=fetchData($sPrestasi);
			foreach ($rPrestasi as $presBrs =>$resPres)
			{
					//$umrList[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array('umr'=>$resKhdrn['upahkerja']);
					$umrList[$resPres['nik']][$resPres['tanggal']][]=array('umr'=>$resPres['upahkerja']);
					$hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array('absensi'=>'H');
                  	$notran[$resPres['nik']][$resPres['tanggal']].='BKM:'.$resPres['notransaksi'].'__';
					$resData[$resPres['nik']][]=$resPres['nik'];

			} 
			
			//print_r($umrList);
			

// ambil pengawas                        
$dzstr="SELECT tanggal,nikmandor,a.notransaksi,b.upahpremi FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$unit."%' and c.namakaryawan is not NULL
    union select tanggal,nikmandor1,a.notransaksi FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$unit."%' and c.namakaryawan is not NULL";

//exit("Error".$dzstr);   upahpremi
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $umrList[$dzbar->nikmandor][$dzbar->tanggal][]=array('umr'=>'ind');
	$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
    $notran[$dzbar->nikmandor][$dzbar->tanggal].='BKM:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil administrasi                       
$dzstr="SELECT tanggal,nikmandor,a.notransaksi FROM ".$dbname.".kebun_aktifitas a
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$unit."%' and c.namakaryawan is not NULL
    union select tanggal,keranimuat,a.notransaksi FROM ".$dbname.".kebun_aktifitas a 
    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$unit."%' and c.namakaryawan is not NULL";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
    $umrList[$dzbar->nikmandor][$dzbar->tanggal][]=array('umr'=>'ind');
	$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
    $notran[$dzbar->nikmandor][$dzbar->tanggal].='BKM:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
}

// ambil traksi                       
$dzstr="SELECT a.upah,a.tanggal,idkaryawan, a.notransaksi FROM ".$dbname.".vhc_runhk a
        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
        where a.tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".substr($unit,0,4)."%'";
 //exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
	$umrList[$dzbar->idkaryawan][$dzbar->tanggal][]=array('umr'=>$dzbar->upah);
    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array('absensi'=>'H');    
    $notran[$dzbar->idkaryawan][$dzbar->tanggal].='TRAKSI:'.$dzbar->notransaksi.'__';
    $resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
}

function kirimnama($nama) // buat ngirim nama lewat javascript. spasi diganti __
{
    $qwe=explode(' ',$nama);
    foreach($qwe as $kyu){
        $balikin.=$kyu.'__';
    }    
    return $balikin;
}

function removeduplicate($notransaksi) // buat ngilangin nomor transaksi yang dobel
{
    $notransaksi=substr($notransaksi,0,-2);    
    $qwe=explode('__',$notransaksi);
    foreach($qwe as $kyu){
        $tumpuk[$kyu]=$kyu;
    }    
    foreach($tumpuk as $tumpz){
        $balikin.=$tumpz.'__';
    }    

    return $balikin;
}




//exit("Error:$tahunGaji");

  // $umrBulan=array();

   
/*$iGapok="select * from ".$dbname.".sdm_5gajipokok where tahun='".$tahunGaji."' ";
$nGapok=mysql_query($iGapok) or die (mysql_error($conn));
while($dGapok=mysql_fetch_assoc($nGapok))
{
	
	$umrBulan[$kar['karyawanid']]=$dGapok['jumlah'];
}

echo"<pre>";
print_r($umrBulan);
echo"</pre>";*/

//$karyawanid[$kar['karyawanid']]=$kar['karyawanid'];




	
	//$resData[]=array();
	//$hasilAbsn[]=array();
/*echo"<pre>";
print_r($umrList);
echo"</pre>";	*/	
        $brt=array();
	
	$lmit=count($klmpkAbsn);
	$a=0;
	foreach($resData as $hslBrs => $hslAkhir)
	{	
			
		if($hslAkhir[0]!='' and $namakar[$hslAkhir[0]]!='')
		{
			$umpHari=$jumlahUmr[$hslAkhir[0]]/25;
			$no+=1;
			echo"<tr class=rowcontent id=row".$no."><td>".$no."</td>";
			echo"
			<td>".$namakar[$hslAkhir[0]]."</td>
			<td>".$nikkar[$hslAkhir[0]]."</td>
			<td>".$nmJabatan[$hslAkhir[0]]."</td>
			<td>".$sbgnb[$hslAkhir[0]]."</td>
			<td id=karyawanid".$no.">".$hslAkhir[0]."</td>
			<td id=periode".$no.">".$per."</td>
			";/*<td>".$jumlahUmr[$hslAkhir[0]]."</td>
			<td>".$umpHari."</td>*/
			
			foreach($test as $barisTgl =>$isiTgl)
			{
				if($hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']!='H')
				{
					//echo"<td><font color=red>X</font></td>";
					echo"<td>-</td>";
					//echo"<td><font color=red></font></td>";
					//echo"<td><font color=red></font></td>";
				}
				else
				{
					
					if($umrList[$hslAkhir[0]][$isiTgl][0]['umr']=='ind')
					{
						
						$umrData=$umpHari;
					}
					else
					{
						$umrData=0;
						
						//$umrData=$umrList[$hslAkhir[0]][$isiTgl][0]['umr']+$umrList[$hslAkhir[0]][$isiTgl][1]['umr'];
						for($i=0;$i<=10;$i++)
						{
							$umrData+=$umrList[$hslAkhir[0]][$isiTgl][$i]['umr'];
						}
					}
					
					//ind koreksi
					//if($nmJabatan[$hslAkhir[0]]=='PEMANEN')
					//{
					//}
					if($umrData>=$umpHari)
					{
						$cekList=1;//$cekList='V';
						$totCekList[$hslAkhir[0]]+=1;
					}
					else
					{
						$cekList=0;
					}
					
					 echo"<td>".$arrXV[$cekList]."</td> ";
				  // echo"<td>".$umrData."_".$umpHari."_".$arrXV[$cekList]."</td> ";
				   // echo"<td>".$cekList."</td> ";
				   
				// echo"<td>".$umpHari."</td>";// echo"<td>".$isiTgl."</td>";
				//  echo"<td>".$umrData."</td>";
				   
					//$totTgl[$isiTgl]+=1;
					
					
					//$totCekList+=$hslAkhir[0][$isiTgl][$cekList];
					
				
					
				}                    
				 
			}
				echo"<td width=5px  align=right>".$totCekList[$hslAkhir[0]]."</td>";	
				if($totCekList[$hslAkhir[0]]>='23')
					$premi=$totCekList[$hslAkhir[0]]*1000;
				else
					$premi='0';
				
				/*foreach($test as $barisTgl =>$isiTgl)
				{
									if($hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']!='H')
									{
					echo"<td title='Click untuk melihat notransaksi.' style=\"cursor: pointer\" onclick=showpopup('".$hslAkhir[0]."','".kirimnama($namakar[$hslAkhir[0]])."','".tanggalnormal($isiTgl)."','".removeduplicate($notran[$hslAkhir[0]][$isiTgl])."',event)><font color=red>".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</font></td>";
									}
									else
									{
										echo"<td title='Click untuk melihat notransaksi.' style=\"cursor: pointer\" onclick=showpopup('".$hslAkhir[0]."','".kirimnama($namakar[$hslAkhir[0]])."','".tanggalnormal($isiTgl)."','".removeduplicate($notran[$hslAkhir[0]][$isiTgl])."',event)>".$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']."</td>";
										$totTgl[$isiTgl]+=1;
									}
					$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']]+=1;
										
				}*/
				
				/*foreach($klmpkAbsn as $brsKet =>$hslKet)
				{
					
										
									if($hslKet['kodeabsen']!='H')
									{
					//echo"<td width=5px align=right><font color=red>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</font></td>";	
									}
									else
									{
										echo"<td width=5px  align=right>".$brt[$hslAkhir[0]][$hslKet['kodeabsen']]."</td>";	
										
										
									}
				}	*/
				
				echo"<td width=5px  align=right id=premi".$no.">".$premi."</td>";	
				echo"</tr>";
			}	
	}
	echo"<button class=mybutton onclick=saveAll(".$no.");>".$_SESSION['lang']['proses']."</button>";
	echo"</tbody></table>";
	break;

	default:
}


switch($proses2)
{
	case'savedata':
	
		if($premi=='0' or $premi=='')
		{
		}
		else
		{
			$str="insert into ".$dbname.".kebun_premikemandoran (`kodeorg`,`periode`,`karyawanid`,`jabatan`,`pembagi`,`premiinput`,`updateby`,`posting`)
			values ('".$unit."','".$periode."','".$karyawanid."','PREMIHADIR','1','".$premi."','".$_SESSION['standard']['userid']."',1)";
	
			if(mysql_query($str))
			{
			}
			else
			{
				$str="update ".$dbname.".kebun_premikemandoran set posting=1,premiinput='".$premi."',updateby='".$_SESSION['standard']['userid']."' "
                                  . " where kodeorg='".$unit."' and periode='".$periode."' and karyawanid='".$karyawanid."' and jabatan='PREMIHADIR'";
                                //exit("error:".$str);
				if(mysql_query($str))
				{
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
			
			}
		}
	break;
	
	break;
	default;	
	
	
}

?>
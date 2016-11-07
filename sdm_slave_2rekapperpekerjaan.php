<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['regionId']==''?$ptId=$_GET['regionId']:$ptId=$_POST['regionId'];
$_POST['tpKary']==''?$tpKary=$_GET['tpKary']:$tpKary=$_POST['tpKary'];
if(substr($periode,0,4)=='2014'){
	if($_SESSION['empl']['regional']=='SULAWESI'){
		if($tpKary==3){
			#tanggal gaji satu periode
			$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$tglKmrn=nambahHari($optTglMulai[$periode],1,0);
			$wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
			$tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
			$tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
		}else{
			#tanggal gaji satu periode
			$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
			$tglKmrn=$optTglMulai[$periode];
		}
	}else{
			#tanggal gaji satu periode
			$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
			$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
			$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
			$tglKmrn=$optTglMulai[$periode];
	}
}else{
	#tanggal gaji satu periode
	$wrt="periode='".$periode."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
	$optTglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
	$tglKmrn=nambahHari($optTglMulai[$periode],1,0);
	$wrt2="periode='".substr($tglKmrn,0,7)."' and kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."')";
	$tglCutoffLalu=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt2);
	if($tglCutoffLalu[$periode]!='0000-00-00'){
		$tglKmrn=nambahHari($tglCutoffLalu[substr($tglKmrn,0,7)],1,1);
		$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tglcutoff', $wrt);
	}else{
		$optTglCutoff=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
		$tglKmrn=$optTglMulai[$periode];
	}
	
	
}

$optRegional=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
/* $optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optSatKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan'); */

$prd=explode("-",$periode);
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");
$arrPlusId=array("0"=>"JMLH_TENAGA KERJA","1"=>"HARI_H","2"=>"FAKTOR LEMBUR","3"=>"H_KERJA","4"=>"UPAH POKOK","5"=>"LEMBUR","6"=>"PREMI LAINNYA","7"=>"UPAH SATUAN","8"=>"JOB INSENTIF","9"=>"PREMI HADIR","10"=>"TMASA","11"=>"LAIN2");

$garis=0;
if($proses=='excel'){
    $garis=1;
   $bgcolordt=" bgcolor=#DEDEDE";
}
if(($proses=='excel')||($proses=='preview')){
     if($periode==''){
		exit("warning: Periode Tidak Boleh Kosong");
	}
	$tglGaji="tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";
	$inquery="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$ptId."'";
	$optTipekar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
	
	##tambahan absen permintaan dari pak ujang#
						$optTipekar=makeOption($dbname,'sdm_5tipekaryawan','id,tipe');
                        $sKehadiran="select count(absensi) as jmlabsn,left(kodeorg,4) as kodeorg,kodekegiatan from 
						             ".$dbname.".kebun_kehadiran_vw 
									 where ".$tglGaji." and left(kodeorg,4) in  (".$inquery.") and tipekaryawan='".$optTipekar[$tpKary]."' group by kodeorg,kodekegiatan";
                        //exit("Error".$sKehadiran);
                        $rkehadiran=fetchData($sKehadiran);
                        foreach ($rkehadiran as $khdrnBrs =>$resKhdrn){	
							if($resKhdrn['absensi']!=''){
						//$hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array('absensi'=>$resKhdrn['absensi']);
								//$resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];
								$komHdr=1;//HARI_H
								$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$komHdr]+=1;
								$dtKdorg[$resKhdrn['kodeorg']]=$resKhdrn['kodeorg'];
								$dtKegId[$resKhdrn['kodekegiatan']]=$resKhdrn['kodekegiatan'];
								$jmlhRow[$resKhdrn['kodeorg']][$resKhdrn['kodekegiatan']]=$resKhdrn['kodekegiatan'];
							}
                        }
						$tglGaji2="b.tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";
                       /*  $sPrestasi="select a.nik,b.tanggal,left(b.kodeorg,4) as kodeorg
						            from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
									left join ".$dbname.".datakaryawan c on a.nik=c.karyawanid
                                    where ".$tglGaji2." and b.notransaksi like '%PNN%' and left(b.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."'";
                        //exit("Error".$sPrestasi);
                        $rPrestasi=fetchData($sPrestasi);
                        foreach ($rPrestasi as $presBrs =>$resPres){
                            //$hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array('absensi'=>'H');
                            //$resData[$resPres['nik']][]=$resPres['nik'];
							$komHdr=1;//HARI_H
							$keg=611010101;
							$rupKeg[$resPres['kodeorg'].$keg.$komHdr]+=1;
							$dtKdorg[$resPres['kodeorg']]=$resPres['kodeorg'];
							$dtKegId[$keg]=$keg;
							$jmlhRow[$resPres['kodeorg']][$keg]=$keg;
                        }  */

        // ambil pengawas 
		$tglGaji3="a.tanggal between '".$tglKmrn."' and '".$optTglCutoff[$periode]."'";		
        $dzstr="SELECT tanggal,count(nikmandor) as jmlabsn,left(b.kodeorg,4) as kodeorg,c.kodejabatan FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL group by left(b.kodeorg,4)
            union select tanggal,count(nikmandor1) as jmlabsn,left(b.kodeorg,4) as kodeorg,c.kodejabatan FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL group by left(b.kodeorg,4)";
			//echo $dzstr;
		//exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres)){
            //$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
            //$resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
			$komHdr=1;//HARI_H
			$kegH='H';
			$rupKeg[$dzbar->kodeorg.$kegH.$komHdr]=$dzbar->jmlabsn;
			$dtKdorg[$dzbar->kodeorg]=$dzbar->kodeorg;
			$dtKegId[$kegH]=$dzbar->kodejabatan;
			$jmlhRow[$dzbar->kodeorg][$kegH]=$kegH;
        }
        // ambil administrasi                       
        $dzstr="SELECT tanggal,count(nikmandor) as jmlabsn,left(b.kodeorg,4) as kodeorg,c.kodejabatan FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL group by left(b.kodeorg,4)
            union select tanggal,count(keranimuat) as jmlabsn,left(b.kodeorg,4) as kodeorg,c.kodejabatan FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where ".$tglGaji3." and left(b.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' and c.namakaryawan is not NULL group by left(b.kodeorg,4)";
         //exit("Error".$dzstr);
        $dzres=mysql_query($dzstr);
        while($dzbar=mysql_fetch_object($dzres)){
            //$hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][]=array('absensi'=>'H');
            //$resData[$dzbar->nikmandor][]=$dzbar->nikmandor;
			$komHdr=1;//HARI_H
			$kegH='H';
			$rupKeg[$dzbar->kodeorg.$kegH.$komHdr]=$dzbar->jmlabsn;
			$dtKdorg[$dzbar->kodeorg]=$dzbar->kodeorg;
			$dtKegId[$kegH]=$kegH;
			$jmlhRow[$dzbar->kodeorg][$kegH]=$kegH;
        }
		#pembentukan array untuk karyawan di kantor sumber data dari absen
		/*$sKantor="select karyawanid,left(kodeorg,4) as kodeorg,upah as insentif,absensi 
				  from ".$dbname.".sdm_absensidt_vw where ".$tglGaji." and upah!=0  and 
				  left(kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."'";*/
				  if($tpKary==3){
					$sKantor="select count(absensi) as jmlOrg,left(kodeorg,4) as kodeorg,sum(upah) as insentif,absensi 
				  from ".$dbname.".sdm_absensidt_vw where ".$tglGaji." and 
				  left(kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' group by left(kodeorg,4),absensi";
		
				  }else{
					$sKantor="select left(kodeorg,4) as kodeorg,sum(upah) as insentif,absensi 
					from ".$dbname.".sdm_absensidt_vw where ".$tglGaji." and upah!=0  and 
				  left(kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."' group by left(kodeorg,4),absensi";
		
				  }
		//echo $sKantor;
		//exit("error:".$sKantor);
		$qKantor=mysql_query($sKantor) or die(mysql_error($conn));
		while($rKantor=mysql_fetch_assoc($qKantor)){
			$scek="select * from ".$dbname.".sdm_5absensi where kelompok=1 and kodeabsen='".$rKantor['absensi']."'";
			$qcek=  mysql_query($scek) or die(mysql_error($con));
			$rcek=  mysql_num_rows($qcek);
			$komHdr=1;//HARI_H
			$komp=4;//UPAH POKOK
			$dtKdorg[$rKantor['kodeorg']]=$rKantor['kodeorg'];
			//if($rcek!=0){
				if($rKantor['absensi']!='L'){
					if($rKantor['absensi']!='H'){
						$dtKegId[$rKantor['absensi']]=$rKantor['absensi'];
						$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komHdr]+=$rKantor['jmlOrg'];
						$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komp]=$rKantor['insentif'];
						$jmlhRow[$rKantor['kodeorg']][$rKantor['absensi']]=$rKantor['absensi'];
					}else{
						//$whrjbtn="karyawanid='".$rKantor['karyawanid']."'";
						//$optJbtn=makeOption($dbname,'datakaryawan','karyawanid,kodejabatan',$whrjbtn);
						$dtKegId[$rKantor['absensi']]=$rKantor['absensi'];
						$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komHdr]+=$rKantor['jmlOrg'];
						$jmlhRow[$rKantor['kodeorg']][$rKantor['absensi']]=$rKantor['absensi'];
						if($tpKary==3){
							$sGapok="select sum(jumlah) as jumlah from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where idkomponen in (1,42) and periodegaji='".$periode."' and kodeorg='".$rKantor['kodeorg']."' and tipekaryawan='".$tpKary."'";
							$qGapok=mysql_query($sGapok) or die(mysql_error($conn));
							$rGapok=mysql_fetch_assoc($qGapok);
							$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komp]=$rGapok['jumlah'];
						}else{
							$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komp]=$rKantor['insentif'];
						}
						
					}
			}elseif(($rKantor['absensi']=='L')&&($rKantor['insentif']!='0')){
					$keg='L';
					$dtKegId[$keg]=$keg;
					$rupKeg[$rKantor['kodeorg'].$rKantor['absensi'].$komHdr]+=$rKantor['jmlOrg'];
					$rupKeg[$rKantor['kodeorg'].$keg.$komp]=$rKantor['insentif'];
					$jmlhRow[$rKantor['kodeorg']][$keg]=$keg;
				}
			//}
		}
        #tambahan absen permintaan abis disini#
	
		#pendapatan lain
		$sDptLaen="select sum(jumlah) as jumlah,left(a.kodeorg,4) as kodeorg,idkomponen from ".$dbname.".sdm_pendapatanlaindt 
				   a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
				   . " where periodegaji='".$periode."' and left(a.kodeorg,4) in (".$inquery.") 
				   and tipekaryawan='".$tpKary."' 
				   group by idkomponen,a.kodeorg";
		//exit("error:".$sDptLaen);
		$qDptLaen=  mysql_query($sDptLaen) or die(mysql_error($conn));
		while($rDptLaen=  mysql_fetch_assoc($qDptLaen)){
			   $komp=11;//LAIN2
			   $rupKeg[$rDptLaen['kodeorg'].$rDptLaen['idkomponen'].$komp]+=$rDptLaen['jumlah'];
			   $dtKegId[$rDptLaen['idkomponen']]=$rDptLaen['idkomponen'];
			   $dtKdorg[$rDptLaen['kodeorg']]=$rDptLaen['kodeorg'];
			   $jmlhRow[$rDptLaen['kodeorg']][$rDptLaen['idkomponen']]=$rDptLaen['idkomponen'];
		}
		#premi kehadiran
		$sKehadiran="select a.`karyawanid`,`jabatan`,left(a.kodeorg,4) as kodeorg,sum(premiinput) as premihadir 
					 from ".$dbname.".kebun_premikemandoran a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
				.   " where jabatan='PREMIHADIR' and periode='".$periode."' and kodeorg in (".$inquery.") 
					 and tipekaryawan='".$tpKary."'  group by left(a.kodeorg,4)";
		//exit("error:".$sKehadiran);
		$qKehadiran=  mysql_query($sKehadiran) or die(mysql_error($conn));
		while($rKehadiran=  mysql_fetch_assoc($qKehadiran)){
			$kegId="PREMIHADIR";
			$komp=9;//PREMI HADIR
			$rupKeg[$rKehadiran['kodeorg'].$kegId.$komp]+=$rKehadiran['premihadir'];
			$dtKegId[$kegId]=$kegId;
			$dtKdorg[$rKehadiran['kodeorg']]=$rKehadiran['kodeorg'];
			$jmlhRow[$rKehadiran['kodeorg']][$kegId]=$kegId;
		}
		#premi kehadiran
		$sKehadiran="select a.`karyawanid`,`jabatan`,left(a.kodeorg,4) as kodeorg,sum(premiinput) as premihadir 
					 from ".$dbname.".kebun_premikemandoran a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
				.   " where jabatan like 'RAWAT%' and periode='".$periode."' and kodeorg in (".$inquery.") 
					 and tipekaryawan='".$tpKary."'  group by left(a.kodeorg,4)";
		//exit("error:".$sKehadiran);
		$qKehadiran=  mysql_query($sKehadiran) or die(mysql_error($conn));
		while($rKehadiran=  mysql_fetch_assoc($qKehadiran)){
			$kegId=$rKehadiran['jabatan'];
			$komp=8;//PREMI HADIR
			$rupKeg[$rKehadiran['kodeorg'].$kegId.$komp]+=$rKehadiran['premihadir'];
			$dtKegId[$kegId]=$kegId;
			$dtKdorg[$rKehadiran['kodeorg']]=$rKehadiran['kodeorg'];
			$jmlhRow[$rKehadiran['kodeorg']][$kegId]=$kegId;
		}	
		if($optTipekar[$tpKary]=='KHT'){
			#ambil data perawatan
			/*$sKehadiran2="select count(tanggal) as jmlhHadir,sum(umr) as upah,kodekegiatan,sum(hasilkerja) as hslkrj,
						 sum(insentif) as premikrj,unit as kodeorg,karyawanid  from ".$dbname.".kebun_kehadiran_vw 
						 where  ".$tglGaji."  and jurnal=1 and unit in (".$inquery.") and tipekaryawan='".$optTipekar[$tpKary]."' group by karyawanid,kodekegiatan";*/
			$sKehadiran2="select count(tanggal) as jmlhHadir,sum(umr) as upah,kodekegiatan,sum(hasilkerja) as hslkrj,
						 sum(insentif) as premikrj,a.karyawanid,lokasitugas  from ".$dbname.".kebun_kehadiran_vw a 
						 left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
						 where  ".$tglGaji."  and jurnal=1 and (unit in (".$inquery.") or lokasitugas in (".$inquery.")) 
						 and a.tipekaryawan='".$optTipekar[$tpKary]."' group by karyawanid,kodekegiatan order by unit asc";
			//echo $sKehadiran2;
			//exit("Error".$sKehadiran);
			$qKehadiran2=mysql_query($sKehadiran2) or die(mysql_error($conn));
			while ($resKhdrn=mysql_fetch_assoc($qKehadiran2)){	
				$komharih=1;//HARI_H
				$komphkerja=3;//H_KERJA
				$whrkeg="kodekegiatan='".$resKhdrn['kodekegiatan']."'";
				$optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan',$whrkeg);
				
				$lokGji="karyawanid='".$resKhdrn['karyawanid']."' and periodegaji='".$periode."'";
				$optLokgaj=makeOption($dbname,'sdm_gaji','karyawanid,kodeorg',$lokGji);
				$resKhdrn['kodeorg']=$optLokgaj[$resKhdrn['karyawanid']];
				 
				if(substr($optNmKeg[$resKhdrn['kodekegiatan']],-3,3)=="[S]"){
					$kompupsatuan=7;//UPAH SATUAN
					$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$kompupsatuan]+=$resKhdrn['upah'];
				}else{
					$komp=4;//UPAH POKOK
					$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$komp]+=$resKhdrn['upah'];
				}
				$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$komphkerja]+=$resKhdrn['hslkrj'];
				$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$komharih]+=$resKhdrn['jmlhHadir'];
				$komp=8;//JOB INSENTIF
				$dtKdorg[$resKhdrn['kodeorg']]=$resKhdrn['kodeorg'];
				$rupKeg[$resKhdrn['kodeorg'].$resKhdrn['kodekegiatan'].$komp]+=$resKhdrn['premikrj'];
				$dtKegId[$resKhdrn['kodekegiatan']]=$resKhdrn['kodekegiatan'];
				$jmlhRow[$resKhdrn['kodeorg']][$resKhdrn['kodekegiatan']]=$resKhdrn['kodekegiatan'];
			}
		}
	 
		/* $sPrestasi="select  unit as kodeorg,sum(hasilkerja) as hasilkerja,sum(upahkerja) as upahkerja,
					sum(upahpremi) as upahpremi,tarif,count(`tanggal`) as jmlhHadir,a.karyawanid from ".$dbname.".kebun_prestasi_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
					where ".$tglGaji." and unit in (".$inquery.") and jurnal=1 and b.tipekaryawan='".$tpKary."'
					group by a.karyawanid,tarif"; */
		$sPrestasi="select  unit as kodeorg,sum(hasilkerja) as hasilkerja,sum(upahkerja) as upahkerja,
					sum(upahpremi) as upahpremi,tarif,count(`tanggal`) as jmlhHadir,a.karyawanid,lokasitugas from ".$dbname.".kebun_prestasi_vw a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
					where ".$tglGaji." and (unit in (".$inquery.") or lokasitugas in (".$inquery.")) and jurnal=1 and b.tipekaryawan='".$tpKary."'
					group by a.karyawanid,tarif";
		 //echo $sPrestasi;
		//exit("Error".$sPrestasi);
		$rPrestasi=fetchData($sPrestasi);
		foreach ($rPrestasi as $presBrs =>$resPres){
				 
				$komHdr=1;//HARI_H
				$komphkerja=3;//H_KERJA
				$lokGji="karyawanid='".$resPres['karyawanid']."' and periodegaji='".$periode."'";
				$optLokgaj=makeOption($dbname,'sdm_gaji','karyawanid,kodeorg',$lokGji);
				$resPres['kodeorg']=$optLokgaj[$resPres['karyawanid']];
			if($resPres['tarif']=='harian'){
				$komp=4;//UPAH POKOK
				$keg="pnnhar";
				$rupKeg[$resPres['kodeorg'].$keg.$komp]+=$resPres['upahkerja'];
				$dtKegId[$keg]=$keg;
				$dtKdorg[$resPres['kodeorg']]=$resPres['kodeorg'];
				$rupKeg[$resPres['kodeorg'].$keg.$komHdr]+=$resPres['jmlhHadir'];
				$rupKeg[$resPres['kodeorg'].$keg.$komphkerja]+=$resPres['hasilkerja'];
				$jmlhRow[$resPres['kodeorg']][$keg]=$keg;
			}else{
				$komp=7;//UPAH SATUAN
				if($resPres['upahpremi']!=''){
					$keg="pnnbukitsat";
					$rupKeg[$resPres['kodeorg'].$keg.$komp]+=$resPres['upahkerja'];
					$rupKeg[$resPres['kodeorg'].$keg.$komphkerja]+=$resPres['hasilkerja'];
					$dtKegId[$keg]=$keg;
					$dtKdorg[$resPres['kodeorg']]=$resPres['kodeorg'];
					$rupKeg[$resPres['kodeorg'].$keg.$komHdr]+=$resPres['jmlhHadir'];
					
					$komp=8;//PREMI LAINNYA
					$rupKeg[$resPres['kodeorg'].$keg.$komp]+=$resPres['upahpremi'];
					$jmlhRow[$resPres['kodeorg']][$keg]=$keg;
				}else{
					$keg="pnnsat";
					$rupKeg[$resPres['kodeorg'].$keg.$komp]+=$resPres['upahkerja'];
					$rupKeg[$resPres['kodeorg'].$keg.$komphkerja]+=$resPres['hasilkerja'];
					$dtKegId[$keg]=$keg;
					$dtKdorg[$resPres['kodeorg']]=$resPres['kodeorg'];
					$rupKeg[$resPres['kodeorg'].$keg.$komHdr]+=$resPres['jmlhHadir'];
					$jmlhRow[$resPres['kodeorg']][$keg]=$keg;
				}
			}
			
		}
	
	#ambil data lembur
	$sLem="select a.karyawanid,a.tanggal,tipelembur,jamaktual,uangkelebihanjam,b.kodejabatan,left(a.kodeorg,4) as kodeorg
		   from ".$dbname.".sdm_lemburdt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
		 . " where ".$tglGaji." and left(a.kodeorg,4) in (".$inquery.") and tipekaryawan='".$tpKary."'  order by tanggal asc";
	//exit("error:".$sLem);
	$qLem=mysql_query($sLem) or die(mysql_error($conn));
	while($rLem=mysql_fetch_assoc($qLem)){
		$whrFak="kodeorg='".$rLem['kodeorg']."' and tipelembur='".$rLem['tipelembur']."' and jamaktual='".$rLem['jamaktual']."'";
		$optJamFak=makeOption($dbname,'sdm_5lembur','jamaktual,jamlembur',$whrFak);
		$komHdr=2;//FAKTOR LEMBUR
		$komp=5;//LEMBUR
		$keg="lembur";
		$rupKeg[$rLem['kodeorg'].$keg.$komHdr]+=$optJamFak[$rLem['jamaktual']];
		$rupKeg[$rLem['kodeorg'].$keg.$komp]+=$rLem['uangkelebihanjam'];
		$dtKegId[$keg]=$keg;
		$dtKdorg[$rLem['kodeorg']]=$rLem['kodeorg'];
		$jmlhRow[$rLem['kodeorg']][$keg]=$keg;
	}
	
	#premi panen
		$urut2=0;
		$urut=0;
		$sSetupPrem="select kodeorg,hasilkg,rupiah,premirajin from ".$dbname.".kebun_5premipanen where kodeorg in ('SULAWESI','H12E02','H12E01')";
		$qSetupPrem=  mysql_query($sSetupPrem) or die(mysql_error($conn));
		while($rSetupPrem=  mysql_fetch_assoc($qSetupPrem)){
			if($rSetupPrem['kodeorg']=='SULAWESI'){
				$urut+=1;
				$kgPrem[$rSetupPrem['kodeorg'].$urut]=$rSetupPrem['hasilkg'];
				$rpPrem[$rSetupPrem['kodeorg'].$urut]=$rSetupPrem['rupiah'];
				$rajinPrem[$rSetupPrem['kodeorg']]=$rSetupPrem['premirajin'];
				$kdOrg[$rSetupPrem['kodeorg']]=$rSetupPrem['kodeorg'];
			}else{
				if($rSetupPrem['rupiah']!=$tmpRph){
					$tmpRph=$rSetupPrem['rupiah'];
					$urut2+=1;
				}
				$kgPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['hasilkg'];
				$rpPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['rupiah'];
				$kdOrg[$rSetupPrem['kodeorg']]=$rSetupPrem['kodeorg'];
			}
		}
		 
		$sJjg="select karyawanid,sum(hasilkerja) as jmlhjjg,count(distinct tanggal) as hk from ".$dbname.".kebun_prestasi_vw "
				. " where  ".$tglGaji." and unit in (".$inquery.") and tipekaryawan='".$optTipekar[$tpKary]."'  group by karyawanid";
		//exit("error:".$sJjg);
		$qJjg=  mysql_query($sJjg) or die(mysql_error($conn));
		while($rJjg=  mysql_fetch_assoc($qJjg)){
			if($rJjg['jmlhjjg']!=''){
				$hrEfektif[$rJjg['karyawanid']]=$rJjg['hk'];
				$totJjg[$rJjg['karyawanid']]=$rJjg['jmlhjjg'];
			}
		}
		$sSum="select a.karyawanid,totalkg,rupiahpremi,left(kodeorg,4) as kodeorg 
					  from ".$dbname.".kebun_premipanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
					. " where kodeorg in (".$inquery.") and periode='".$periode."' and rupiahpremi!=0 and tipekaryawan='".$tpKary."' order by kodeorg asc";
		$qSum=mysql_query($sSum) or die(mysql_error($conn));
		while($rSum=mysql_fetch_assoc($qSum)){
			$totPremi[$rSum['kodeorg']]+=$rSum['rupiahpremi'];
			$rowPremidt[$rSum['kodeorg']]+=1;
		}
		//exit("error:".$totPremi[H01E]);
		$sPremiPanen="select a.karyawanid,totalkg,rupiahpremi,left(kodeorg,4) as kodeorg,kodepremi 
					  from ".$dbname.".kebun_premipanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
					. " where kodeorg in (".$inquery.") and periode='".$periode."' and rupiahpremi!=0 and tipekaryawan='".$tpKary."' order by kodeorg asc";
	 
		 $qPremiPanen=  mysql_query($sPremiPanen) or die(mysql_error());
		 while($rPremiPanen=  mysql_fetch_assoc($qPremiPanen)){
			 if($rPremiPanen['rupiahpremi']!='0'){
					if($tempKbn!=$rPremiPanen['kodeorg']){
						$tempKbn=$rPremiPanen['kodeorg'];
						$totRup=0;
						$rowdtPremi=1;
					}else{
						$rowdtPremi+=1;
					}
					 $angk1=1;
					 $angk2=2;
					 $angk3=3;
					 $ws="karyawanid='".$rPremiPanen['karyawanid']."'";
					 $optSubbgain=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian', $ws);
					
					 $komphkerja=3;//H_KERJA
					 $kdData=$optRegional[$rPremiPanen['kodeorg']];
					 $dtKdorg[$rPremiPanen['kodeorg']]=$rPremiPanen['kodeorg'];
					 
					 if($kdData=='SULAWESI'){
						if($kdOrg[$optSubbgain[$rPremiPanen['karyawanid']]]!=''){
						 
								if(round($rPremiPanen['rupiahpremi'])==$rpPrem[$optSubbgain[$rPremiPanen['karyawanid'].$angk1]]){
									 $komp=8;//satuan sumber dari premi panen
									 $keg="prpanen11";
									 $rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=$rPremiPanen['rupiahpremi'];
									 $rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
									 $dtKegId[$keg]=$keg;
									 $totRup+=$rPremiPanen['rupiahpremi'];
									 $jmlhRow[$rPremiPanen['kodeorg']][$keg]=$keg;
									 
								}elseif(round($rPremiPanen['rupiahpremi'])==$rpPrem[$optSubbgain[$rPremiPanen['karyawanid'].$angk2]]){
									$komp=8;//satuan sumber dari premi panen
									$keg="prpanen11";
									$rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=$rPremiPanen['rupiahpremi'];
									$rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
									$dtKegId[$keg]=$keg;
									$totRup+=$rPremiPanen['rupiahpremi'];
									$jmlhRow[$rPremiPanen['kodeorg']][$keg]=$keg;
								}else{
									 $keg="prpanen5";
									 $komp=8;//satuan sumber dari premi panen
									 $rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=$rPremiPanen['rupiahpremi'];
									 $rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
									 $dtKegId[$keg]=$keg;
									 $totRup+=$rPremiPanen['rupiahpremi'];
									 $jmlhRow[$rPremiPanen['kodeorg']][$keg]=$keg;
								}
							 
						 }else{
						 $bandingJjg=0;
						 @$bandingJjg=$totJjg[$rPremiPanen['karyawanid']]/70;
						 //if($hariAktif[$karyId]>=16){
						 if($bandingJjg>$hrEfektif[$rPremiPanen['karyawanid']]){
							 $rupy=($hrEfektif[$rPremiPanen['karyawanid']])*$rajinPrem[$kdData];
						 }else{
							 $rupy=($bandingJjg)*$rajinPrem[$kdData];
						 }
						 //exit("error".$kdData."____".$idAfd."___".$hrEfektif[$rPremiPanen['karyawanid']]."___".$bandingJjg);
						 if($rupy!=0){
							 if($rpPrem[$kdData.$angk3]==(round($rPremiPanen['rupiahpremi']-$rupy))){
								$keg="prpanen25";
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
								$dtKegId[$keg]=$keg;
								$kompa=8;//JOB INSENTIF
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$kompa]+=$rupy;
								$totRup+=$rupy;
								$komp=8;//satuan sumber dari premi panen
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=($rPremiPanen['rupiahpremi']-$rupy);
								$totRup+=($rPremiPanen['rupiahpremi']-$rupy);
						   }elseif($rpPrem[$kdData.$angk2]==(round($rPremiPanen['rupiahpremi']-$rupy))){
								$keg="prpanen30";
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
								$dtKegId[$keg]=$keg;
								$kompa=8;//JOB INSENTIF
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$kompa]+=$rupy;
								$totRup+=$rupy;
								$komp=8;//satuan sumber dari premi panen
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=($rPremiPanen['rupiahpremi']-$rupy);
								$totRup+=($rPremiPanen['rupiahpremi']-$rupy);
							}elseif($rpPrem[$kdData.$angk1]==(round($rPremiPanen['rupiahpremi']-$rupy))){
								$keg="prpanen35";
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
								$dtKegId[$keg]=$keg;
								$kompa=8;//JOB INSENTIF
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$kompa]+=$rupy;
								$totRup+=$rupy;
								$komp=8;//satuan sumber dari premi panen
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komp]+=($rPremiPanen['rupiahpremi']-$rupy);
								$totRup+=($rPremiPanen['rupiahpremi']-$rupy);
							}elseif((round($rPremiPanen['rupiahpremi'])-round($rupy))==0){ 	
								$keg="prrajin";
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
								$dtKegId[$keg]=$keg;
								$kompa=8;//tambahan tam insentif dari premi rajin
								$rupKeg[$rPremiPanen['kodeorg'].$keg.$kompa]+=$rupy;
								$totRup+=$rupy;
							 }
							 $jmlhRow[$rPremiPanen['kodeorg']][$keg]=$keg;
						 }
								if($rowdtPremi==$rowPremidt[$rPremiPanen['kodeorg']]){
								if((($totPremi[$rPremiPanen['kodeorg']]-$totRup)!=0)||(($totPremi[$rPremiPanen['kodeorg']]-$totRup)>0)){
										$keg="prrajin";
									$dtKegId[$keg]=$keg;
									$kompa=8;//tambahan tam insentif dari premi rajin
									$rupKeg[$rPremiPanen['kodeorg'].$keg.$kompa]+=($totPremi[$rPremiPanen['kodeorg']]-$totRup);
								}
								
								}
						 }

						}
			 
					  }
		}
		 
		#ambil rupiah kht dari traksi
		$sRunHk="select sum(upah) as upah,sum(premi+premiluarjam) as premi,b.kodejabatan,a.kodeorg from ".$dbname.".vhc_runhk_vw a 
				 left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid where ".$tglGaji."  and kodeorg in (".$inquery.") and tipekaryawan='".$tpKary."' 
				 group by a.kodeorg";
		//echo $sRunHk;
		//exit("error:".$sRunHk);
		$qRunHk=mysql_query($sRunHk) or die(mysql_error($conn));
		while($rRunHk=mysql_fetch_assoc($qRunHk)){
			$komHdr=6;//PREMI LAINNYA
			$komp=4;//UPAH POKOK
			$keg="premitraksi";
			$dtKegId[$keg]=$keg;
			$rupKeg[$rRunHk['kodeorg'].$keg.$komp]+=$rRunHk['upah'];
			$rupKeg[$rRunHk['kodeorg'].$keg.$komHdr]+=$rRunHk['premi'];
			#$jmlhHadir[$rRunHk['kodeorg'].$rRunHk['kodejabatan'].$komHdr]=$rKantor['jmlhdr'];
			$dtKdorg[$rRunHk['kodeorg']]=$rRunHk['kodeorg'];
			$jmlhRow[$rRunHk['kodeorg']][$keg]=$keg;
		}
	
	#loading ford dan dt
		$sFord="select jabatan,sum(premiinput) as premiinput ,left(a.kodeorg,4) as kodeorg from 
				".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
			  . " where periode='".$periode."' and left(a.kodeorg,4) in (".$inquery.") and jabatan like 'LOADING%' and tipekaryawan='".$tpKary."'
				group by left(a.kodeorg,4),jabatan";
		 //echo $sFord;
		$qForm=  mysql_query($sFord) or die(mysql_error($conn));
		while($rForm=  mysql_fetch_assoc($qForm)){
			 $komphkerja=3;//H_KERJA
			 if($rForm['premiinput']!=0){
				$keg=$rForm['jabatan'];
				if($rForm['jabatan']=='LOADINGFORD'){
					$sKg="select sum(hasilkerja) as totalkg from ".$dbname.".kebun_kehadiran_vw "
						 . " where ".$tglGaji." and unit='".$rForm['kodeorg']."' and kodekegiatan in('611020228','611020224')";
				}else{
					$sKg="select sum(hasilkerja) as totalkg  from ".$dbname.".kebun_kehadiran_vw "
						. " where ".$tglGaji." and  unit='".$rForm['kodeorg']."' and kodekegiatan in('611020221')";
				}
				//exit("error".$sKg);
				$qKg=  mysql_query($sKg) or die(mysql_error($conn));
				$rKg=  mysql_fetch_assoc($qKg);
				$hslKerja[$rForm['kodeorg'].$keg.$komphkerja]+=$rPremiPanen['totalkg'];
				$dtKegId[$keg]=$keg;
				$komp=8;//satuan sumber premi loading
				$rupKeg[$rForm['kodeorg'].$keg.$komp]+=$rForm['premiinput'];
				$jmlhRow[$rForm['kodeorg']][$keg]=$keg;
				$dtKdorg[$rForm['kodeorg']]=$rForm['kodeorg'];
			}
			
		}
	   
		#loading PREMI RAWAT dan dt
		$sFord="select a.karyawanid,jabatan,premiinput,left(a.kodeorg,4) as kodeorg from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
			 . " where periode='".$periode."' and left(a.kodeorg,4) in (".$inquery.") and jabatan like 'PREMIRAWAT%' and tipekaryawan='".$tpKary."'";
		 //echo $sFord;
		$qForm=  mysql_query($sFord) or die(mysql_error($conn));
		while($rForm=  mysql_fetch_assoc($qForm)){
			$keg=$rForm['jabatan'];
			$komp=8;//tam insentif dari traksi
			$dtKegId[$keg]=$keg;
			$rupKeg[$rForm['kodeorg'].$keg.$komp]+=$rForm['premiinput'];
			$jmlhRow[$rForm['kodeorg']][$keg]=$keg;
			$dtKdorg[$rForm['kodeorg']]=$rForm['kodeorg'];
		}
		$TglMulai=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
		$periodeKmrn=nambahHari($TglMulai[$periode],1,0);
			
		$sSimpanan="select sum(jumlah) as simpanan,kodeorg from ".$dbname.".sdm_gaji a 
					left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
		            where idkomponen ='60' and periodegaji='".$periode."' and b.tipekaryawan='".$tpKary."'
					and kodeorg in (".$inquery.")
					group by kodeorg";
		$qSimpanan=mysql_query($sSimpanan) or die(mysql_error($conn));
		while($rSimpanan=mysql_fetch_assoc($qSimpanan)){
			$komp=11;//UPAH POKOK
			$keg="simpanan";
			$rupKeg[$rSimpanan['kodeorg'].$keg.$komp]+=$rSimpanan['simpanan'];
			$jmlhRow[$rSimpanan['kodeorg']][$keg]=$keg;
			$dtKdorg[$rSimpanan['kodeorg']]=$rSimpanan['kodeorg'];
			$dtKegId[$keg]=$keg;
		}
		
		
	 
			//exit("error:".count($rupKeg));
            $tab.="<table cellpadding=1 cellspacing=1 border='".$garis."' class=sortable><thead>";
            $tab.="<tr ".$bgcolordt." align=center>";
            $tab.="<td>No.</td>";
            $tab.="<td>".strtoupper($_SESSION['lang']['namakegiatan'])."</td>";
            foreach($arrPlusId as $rwDt=>$lsData){
				$tab.="<td>".strtoupper($lsData)."</td>";
			}
			$tab.="<td>TBRUTO</td>";
			/* $sKdro="select kodeorganisasi from ".$dbname.".organisasi where tipe='KANWIL' and kodeorganisasi in (".$inquery.")";
			$qKdro=mysql_query($sKdro) or die(mysql_error($conn));
			$rKdro=mysql_fetch_assoc($qKdro);
			unset($dtKdorg[$rKdro['kodeorganisasi']]); */
			array_multisort($dtKdorg,SORT_ASC);
            $tab.="</tr></thead><tbody>";
			foreach($dtKdorg as  $lstKdOrg){
				if($lstKdOrg!=''){
					foreach($dtKegId as $lstKeg=>$dtKegIs){
						if($jmlhRow[$lstKdOrg][$dtKegIs]!=''){
							if($lstKdOrg!=$tempKdOrg){
								$rowData=1;
								$tempKdOrg=$lstKdOrg;
							}else{
								$rowData+=1;
							}
							$no+=1;
							switch(trim($dtKegIs)){
                                          
                                            case'prpanen25':
                                                 $optNmKeg[$dtKegIs]="INSENTIF PANEN 25 TON";
                                                 $optSatKeg[$dtKegIs]="KG";
                                            break;
                                            case'prpanen30':
                                                 $optNmKeg[$dtKegIs]="INSENTIF PANEN 30 TON";
                                                 $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen35':
                                                $optNmKeg[$dtKegIs]="INSENTIF PANEN 35 TON";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen5':
                                                $optNmKeg[$dtKegIs]="INSENTIF PANEN 5 TON";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'prpanen11':
                                                 $optNmKeg[$dtKegIs]="INSENTIF PANEN 11 TON";
                                                 $optSatKeg[$keg]="KG";
                                            break;
                                            case'LOADINGFORD':
                                                $optNmKeg[$dtKegIs]="LOADINGFORD";
                                                $optSatKeg[$keg]="KG";
                                            break;
                                            case'LOADINGDT':
                                                  $optNmKeg[$dtKegIs]="LOADINGDT";
                                                  $optSatKeg[$keg]="KG";
                                            break;
                                            case'L':
                                            $optNmKeg[$dtKegIs]="LIBUR NASIONAL";
                                            $optSatKeg[$keg]="HARI";    
                                            break;
                                            case'PREMIHADIR':
												$optNmKeg[$dtKegIs]="PREMI HADIR";
											break;
                                            case'pnnbukithar':
                                            $optNmKeg[$dtKegIs]="PANEN BUKIT HARIAN";
                                            $optSatKeg[$keg]="JJG";    
                                            break;
                                            case'pnnbukitsat':
                                            $optNmKeg[$dtKegIs]="PANEN BUKIT SATUAN";
                                            $optSatKeg[$keg]="JJG";    
                                            break;
                                            case'pnnhar':
                                                    $optNmKeg[$dtKegIs]="PANEN HARIAN";
                                                    $optSatKeg[$keg]="JJG";
                                            break;//
                                            case'pnnsat':
                                                    $optNmKeg[$dtKegIs]="PANEN SATUAN";
                                                    $optSatKeg[$keg]="JJG";
                                            break;
											case'prrajin':
												    $optNmKeg[$dtKegIs]="PREMI RAJIN";
											break;
											case'simpanan':
												 $optNmKeg[$dtKegIs]="SIMPANAN";
											break;
											case'lembur':
												 $optNmKeg[$dtKegIs]="LEMBUR";
											break;
											case'premitraksi':
												$optNmKeg[$dtKegIs]="PREMI TRAKSI";
											break;
                                            case'RAWATDT':
												$optNmKeg[$dtKegIs]="RAWAT DT";
											break;
											case'RAWATCU':
												$optNmKeg[$dtKegIs]="CUCI MOBIL";
											break;
											case'RAWATAB':
												$optNmKeg[$dtKegIs]="RAWAT ALAT BERAT";
											break;
                                            default:
												$wher="kodekegiatan='".$dtKegIs."'";
												$optNmKeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',$wher);
												if($optNmKeg[$dtKegIs]==''){
													$wher="id='".$dtKegIs."'";
													$optNmKeg=makeOption($dbname,'sdm_ho_component','id,name',$wher);
												}
												/* if($optNmKeg[$dtKegIs]==''){
														$wher="kodejabatan='".$dtKegIs."'";
														$optNmKeg=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan',$wher);
												} */
												if($optNmKeg[$dtKegIs]==''){
													$wher="kodeabsen='".$dtKegIs."'";
													$optNmKeg=makeOption($dbname,'sdm_5absensi','kodeabsen,keterangan',$wher);
												}
                                                
                                            break;
                            }
							$tab.="<tr class=rowcontent>";
							$tab.="<td>".$no."</td>";
							$tab.="<td>".strtoupper($optNmKeg[$dtKegIs])."</td>";
							foreach($arrPlusId as $rwDt=>$lsData){
									$tab.="<td align=right>".number_format($rupKeg[$lstKdOrg.$dtKegIs.$rwDt],0)."</td>";
									if($rwDt>3){
										$totBruto[$lstKdOrg.$dtKegIs]+=$rupKeg[$lstKdOrg.$dtKegIs.$rwDt];
										$totSemua[$lstKdOrg]+=$rupKeg[$lstKdOrg.$dtKegIs.$rwDt];
										//$totSmaPerBruto+=$rupKeg[$lstKdOrg.$dtKegIs.$rwDt];
										$totPerId[$lstKdOrg.$rwDt]+=$rupKeg[$lstKdOrg.$dtKegIs.$rwDt];
									}
							}
							$tab.="<td align=right>".number_format($totBruto[$lstKdOrg.$dtKegIs],0)."</td>";
							$tab.="</tr>";
						}
					}
					if(count($jmlhRow[$lstKdOrg])==$rowData){
							$whrNm="kodeorganisasi='".$lstKdOrg."'";
							$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whrNm);
							$tab.="<tr>";
							$tab.="<td colspan=2>".strtoupper($_SESSION['lang']['subtotal']." ".$lstKdOrg."-".$nmOrg[$lstKdOrg])."</td>";
							foreach($arrPlusId as $rwDt=>$lsData){
								$tab.="<td align=right>".number_format($totPerId[$lstKdOrg.$rwDt],0)."</td>";
								$totSmaPeriD[$rwDt]+=$totPerId[$lstKdOrg.$rwDt];
								$totSmaPerBruto+=$totPerId[$lstKdOrg.$rwDt];
							}
							$tab.="<td align=right>".number_format($totSemua[$lstKdOrg],0)."</td>";
							$tab.="</tr>";
					}
				}
			}
			$tab.="<tr>";
			$tab.="<td colspan=2>".strtoupper($_SESSION['lang']['grnd_total'])."</td>";
			foreach($arrPlusId as $rwDt=>$lsData){
				$tab.="<td align=right>".number_format($totSmaPeriD[$rwDt],0)."</td>";
			}
			$tab.="<td align=right>".number_format($totSmaPerBruto,0)."</td>";
			$tab.="</tr>";			
			
			$tab.="</tbody></table>";
	
}
switch($proses){
    case'preview':
        echo $tab;
    break;
case'getKary':
    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    if(strlen($kdUnit)>4){
		$dtisi=0;
        $whr=" subbagian='".$kdUnit."' ";
    }else{
        $whr=" lokasitugas='".$kdUnit."' ";
		$dtisi=1;
		$optAfd=$optKary;
		$safd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdUnit."'";
		$qafd=mysql_query($safd) or die(mysql_error($conn));
		while($rafd=mysql_fetch_assoc($qafd)){
			$optAfd.="<option value='".$rafd['kodeorganisasi']."'>".$rafd['namaorganisasi']."</option>";
		}
    }
    
    $sData="select distinct nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan "
         . " where ".$whr." and tipekaryawan=4 order by namakaryawan asc";
    $qData=  mysql_query($sData) or die(mysql_error($conn));
    while($rData=  mysql_fetch_assoc($qData)){
        $optKary.="<option value='".$rData['karyawanid']."'>".$rData['nik']."-".$rData['namakaryawan']."</option>";
    }
	if($dtisi==1){
		echo $optAfd."####".$optKary;
	}else{
		echo $optKary;
	}
break;
case'excel':
 $tab.="Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];
 $tmz=date('dmYHis');
 $nop_="rekapGajiPerKeg_".$periode."__".$tmz;
    if(strlen($tab)>0){
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $tab);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
	}
break;
}
?>
<?php
session_start();
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('config/connection.php');
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
$jnlibur=$_POST['jnlibur'];
$tgllibur=tanggalsystem($_POST['tgllibur']);
$t=substr($tgllibur,0,4)."-".substr($tgllibur,4,2)."-".substr($tgllibur,6,2);
$hari=date('D',  strtotime($t));
#periksa jl libur, jika Minggu (M) maka periksa tgllibur
$sLbr="select distinct * from ".$dbname.".sdm_5harilibur 
       where regional='".$_SESSION['empl']['regional']."' and tanggal='".$t."'";
 //exit("error:".$sLbr);
$qLbr=mysql_query($sLbr) or die(mysql_error($conn));
$rLbr=mysql_num_rows($qLbr);
if(($rLbr==0)&&($jnlibur!='MG')){
    exit("error: The date is not in holiday list");
}else{
    if($jnlibur=='MG' and $hari!='Sun'){
    exit('Error: Date '.$_POST['tgllibur']." is not Sunday, absence code incorrect");
    }
}

#ambil periode gaji
$str="select periode,tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where '".$t."'<=tanggalsampai and   '".$t."'>=tanggalmulai and jenisgaji='H' 
          and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $periode=$bar->periode;
    $rTgl['tanggalmulai']=$bar->tanggalmulai;
    $rTgl['tanggalsampai']=$bar->tanggalsampai;
    $tgl1=$rTgl['tanggalmulai'];
    $tgl2=$rTgl['tanggalsampai'];
}
if($periode==''){
    exit("Error: Payroll period required");
}
//$sgaji="select * from ".$dbname.".sdm_gaji where periodegaji='".$periode."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
//$qgaji=  mysql_query($sgaji) or die(mysql_error($conn));
//$rgaji=  mysql_num_rows($qgaji);
//if($rgaji>0){
//    exit("Error: Payroll period already closed");
//}
#ambil semua karyawan KBL dan KHT  yang masih aktif serta subbagiannya:
#subbagian:

$str="select distinct subbagian,lokasitugas,karyawanid from ".$dbname.".datakaryawan where tipekaryawan in(1,2,3,4) and 
    lokasitugas='".$_SESSION['empl']['lokasitugas']."' and 
    (tanggalkeluar>='".$t."' or tanggalkeluar='0000-00-00') and alokasi=0
    and ( tanggalmasuk<='".$t."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null) order by subbagian,karyawanid asc";
//exit("error".$str);
$res=mysql_query($str);
$trig=1;
while($bar=mysql_fetch_object($res)){
     
    if(($bar->subbagian=='')||is_null($bar->subbagian=='')){
        $sub[$bar->lokasitugas]=$_SESSION['empl']['lokasitugas'];
        $subbagian[$bar->karyawanid]=$_SESSION['empl']['lokasitugas'];
        $karyawanid[$bar->karyawanid]=$bar->karyawanid;
    }
    else{
        $sub[$bar->subbagian]=$bar->subbagian;
        $subbagian[$bar->karyawanid]=$bar->subbagian;
        $karyawanid[$bar->karyawanid]=$bar->karyawanid;
    }
}
$sget="select * from ".$dbname.".setup_temp_lokasitugas where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$qget=  mysql_query($sget) or die(mysql_error());
$rwget=  mysql_num_rows($qget);
if($rwget!=0){
    while($rget=  mysql_fetch_assoc($qget)){
    $sub[$rget['karyawanid']]=$rget['kodeorg'];
    $subbagian[$rget['karyawanid']]=$rget['kodeorg'];
    $karyawanid[$rget['karyawanid']]=$rget['karyawanid'];
    }
}
$test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
##tambahan absen permintaan dari paa ujang#
$sAbsn="select absensi,tanggal,karyawanid from ".$dbname.".sdm_absensidt 
                where tanggal between '".$tgl1."' and '".$tgl2."' and left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."' 
				and absensi in (select kodeabsen from ".$dbname.".sdm_5absensi where kelompok=1) 
				and absensi not in ('MG','L','C') ";
                         //exit("Error".$sAbsn);
$rAbsn=fetchData($sAbsn);
foreach ($rAbsn as $absnBrs =>$resAbsn){
        if(!is_null($resAbsn['absensi'])){
                $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][]=array('absensi'=>$resAbsn['absensi']);
                $resData[$resAbsn['karyawanid']][]=$resAbsn['karyawanid'];
        }
}
$sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                        where tanggal between '".$tgl1."' and '".$tgl2."' and unit='".$_SESSION['empl']['lokasitugas']."'";
//exit("Error".$sKehadiran);
$rkehadiran=fetchData($sKehadiran);
foreach ($rkehadiran as $khdrnBrs =>$resKhdrn){	
                if($resKhdrn['absensi']!=''){
                        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][]=array(
                        'absensi'=>$resKhdrn['absensi']);
                        $resData[$resKhdrn['karyawanid']][]=$resKhdrn['karyawanid'];

                }

}
$sPrestasi="select a.nik,b.tanggal from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b      
                        on  a.notransaksi=b.notransaksi 
                        where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and 
                        b.tanggal between '".$tgl1."' and '".$tgl2."' ";
                        //exit("Error".$sPrestasi);
$rPrestasi=fetchData($sPrestasi);
foreach ($rPrestasi as $presBrs =>$resPres){
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][]=array(
                'absensi'=>'H');
                $resData[$resPres['nik']][]=$resPres['nik'];
} 

// ambil traksi                       
$dzstr="SELECT a.tanggal,idkaryawan FROM ".$dbname.".vhc_runhk a
left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
where a.tanggal between '".$tgl1."' and '".$tgl2."'  and notransaksi like '%".$_SESSION['empl']['lokasitugas']."%'";
//exit("Error".$dzstr);
$dzres=mysql_query($dzstr);
while($dzbar=mysql_fetch_object($dzres))
{
$hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][]=array(
'absensi'=>'H');
$resData[$dzbar->idkaryawan][]=$dzbar->idkaryawan;
}      

foreach($resData as $hslBrs => $hslAkhir)
{	
        if($hslAkhir[0]!='')
        {
                foreach($test as $barisTgl =>$isiTgl){	
                    if($hasilAbsn[$hslAkhir[0]][$isiTgl]!=''){
                                $brt[$hslAkhir[0]]+=1;
                    }
                }
        }	
}
 
//exit("error:masuk sini si bang bang".$kehadiran);
#tambahan absen permintaan abis disini#
 
foreach($sub as $key => $bagian){
    $strim="('".$t."','".$bagian."','".$periode."')";
    if(substr($bagian,0,4)!=$_SESSION['empl']['lokasitugas']){
        continue;
    }
    $sData="select distinct * from ".$dbname.".sdm_absensiht where tanggal='".$t."' and kodeorg='".$bagian."'";
    $qData=mysql_query($sData) or die(mysql_error($conn));
    $rData=mysql_num_rows($qData);
    if($rData==0){
            $strim2="insert into ".$dbname.".sdm_absensiht(tanggal,kodeorg,periode) values ".$strim."";
            if(!mysql_query($strim2)){
               exit("error:".mysql_error($conn)."___".$strim2);
            }
    }
    foreach($karyawanid as $id){
        $dptUph=0;
        $kehadiran=0;
        $where="karyawanid='".$id."'";
        $tpKary=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan',$where);
        $sUmr="select sum(jumlah) as jumlah from ".$dbname.".sdm_5gajipokok 
                where karyawanid='".$id."' and tahun='".substr($t,0,4)."'  and idkomponen ='1' ";
        $qUmr=mysql_query($sUmr) or die(mysql_error());
        $rUmr=mysql_fetch_assoc($qUmr);
        $umr=$rUmr['jumlah']/25;
        
        
        if($subbagian[$id]==$bagian){
        $scek="select distinct absensi from ".$dbname.".sdm_absensidt where tanggal='".$t."' and karyawanid='".$id."'";
        $qcek=mysql_query($scek) or die(mysql_error($conn));
        $rcek=mysql_num_rows($qcek);
            if($rcek>0){
					$rDat=mysql_fetch_assoc($qcek);
					if(($rDat['absensi']=='L')||($rDat['absensi']=='MG')||($rDat['absensi']=='C')){
						$sdel="delete from ".$dbname.".sdm_absensidt where karyawanid='".$id."' and tanggal='".$t."' and absensi='".$rDat['absensi']."'";
        						if(!mysql_query($sdel)){
								echo "Error:". mysql_error($conn)."____".$sdel."___page 2 bawah";
						}else{
							if($_SESSION['empl']['regional']=='SULAWESI'){
								if(($tpKary[$id]==4)&&($jnlibur!='MG')){
									if($brt[$id]>=1){ // sebelumnya 16
											$dptUph=$umr;
									}else{
											$dptUph=0;
									}
								}
							}
							#akhir premi libnas
								$strix=" ('". $subbagian[$id]."','".$t."','".$id."','".$jnlibur."','00:00:00','00:00:00',0,'".$dptUph."')";
								$strix2="insert into ".$dbname.".sdm_absensidt(kodeorg,tanggal,karyawanid,absensi,jam,jamPlg,catu,insentif) values ".$strix.";";
								if(!mysql_query($strix2)){
									echo "Error:". mysql_error($conn)."____".$strix2."___page 2 atas";
								}
						}
					}else{
						continue;
					}
            }else{
					if($_SESSION['empl']['regional']=='SULAWESI'){
						if(($tpKary[$id]==4)&&($jnlibur!='MG')){
							if($brt[$id]>=1){ // sebelumnya 16
									$dptUph=$umr;
							}else{
									$dptUph=0;
							}
						}
					}
					#akhir premi libnas
                    $strix=" ('". $subbagian[$id]."','".$t."','".$id."','".$jnlibur."','00:00:00','00:00:00',0,'".$dptUph."')";
                    $strix2="insert into ".$dbname.".sdm_absensidt(kodeorg,tanggal,karyawanid,absensi,jam,jamPlg,catu,insentif) values ".$strix.";";
                    if(!mysql_query($strix2)){
                        echo "Error:". mysql_error($conn)."____".$strix2."___page 2 bawah";
                    }
            }
        }
    }
}
?>
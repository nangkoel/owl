<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];//
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
$_POST['karyId']==''?$karyId=$_GET['karyId']:$karyId=$_POST['karyId'];
$arrPer=explode("-",$periode);
if (($arrPer[1]-1)==0) {
        $periode2=($arrPer[0]-1)."-12";
} else {
        $periode2=$arrPer[0]."-".($arrPer[1]-1);
        if (strlen($periode2)==6)
                $periode2=$arrPer[0]."-0".($arrPer[1]-1);
}
#tanggal gaji satu periode
$wrt="periode='".$periode."' and kodeorg='".$kdUnit."'";
$wrt2="periode='".$periode2."' and kodeorg='".$kdUnit."'";
$optTglGj2=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt2);
$optTglGj=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalmulai', $wrt);
$optTglGjS=  makeOption($dbname, 'sdm_5periodegaji', 'periode,tanggalsampai', $wrt);
#array tanggal satu periode
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
$arrTgl=dates_inbetween($optTglGj[$periode],$optTglGjS[$periode]);
$optRegional=  makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optSatKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$prd=explode("-",$periode);
$arrBln=array(1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember");


$garis=0;
if($proses=='excel'){
    $garis=1;
   $bgcolordt=" bgcolor=#DEDEDE";
}
if(($proses=='excel')||($proses=='preview')){
     if($periode==''){
        exit("error:Period can't empty");
    } 
    if($kdUnit==''){
           exit("error:Unit can't empty");
    }else{
		$add=" and lokasitugas='".$kdUnit."' and (subbagian='' or subbagian is null)";
		$addHdr=" and lokasitugas='".$kdUnit."' and (subbagian='' or subbagian is null)";
		
	}
	
    if($afdId!=''){
        $add.=" and subbagian='".$afdId."'";
        //$addKhdrn.=" and left(kodeorg,6)='".$afdId."'";
		$addHdr=" and subbagian='".$afdId."'";
    }
    if($karyId!=''){
        $add=" and a.karyawanid='".$karyId."'";
        //$addKhdrn.=" and karyawanid='".$karyId."'";
		$addHdr.=" and karyawanid='".$karyId."'";
		 
    }
	$sKary="select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas!='' ".$addHdr."";
    /*$sRekap="select distinct a.karyawanid,nik,subbagian,namakaryawan from ".$dbname.".sdm_gaji a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
     . " where periodegaji='".$periode."' and tipekaryawan=4 and lokasitugas='".$kdUnit."' ".$add." order by namakaryawan asc";*/
	 $sRekap="select distinct karyawanid,nik,subbagian,namakaryawan from ".$dbname.".datakaryawan where tipekaryawan=4 
	          and lokasitugas='".$kdUnit."' ".$addHdr." order by namakaryawan asc";
    //echo $sRekap;
    $qRekap=  mysql_query($sRekap) or die(mysql_error($conn));
    while($rRekap=  mysql_fetch_assoc($qRekap)){
        $dtKary[$rRekap['karyawanid']]=$rRekap['karyawanid'];
        $dtNik[$rRekap['karyawanid']]=$rRekap['nik'];
        $dtSubdivisi[$rRekap['karyawanid']]=$rRekap['subbagian'];
        $dtNmkar[$rRekap['karyawanid']]=$rRekap['namakaryawan'];
    }
    #ambil data perawatan
    /* $sKehadiran="select karyawanid,tanggal,kodekegiatan,hasilkerja,umr,insentif,notransaksi,kodeorg from ".$dbname.".kebun_kehadiran_vw 
                 where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and unit='".$kdUnit."' ".$addKhdrn." and tipekaryawan='KHT' and jurnal in ('0','1')
                order by tanggal asc"; */
	
    //exit("Error".$sKehadiran);
    $rkehadiran=fetchData($sKehadiran);
    foreach ($dtKary as $lstKaryId){	
        $sKehadiran="select karyawanid,tanggal,kodekegiatan,hasilkerja,umr,insentif,notransaksi,kodeorg from ".$dbname.".kebun_kehadiran_vw 
                 where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and  karyawanid='".$lstKaryId."' and tipekaryawan='KHT' and jurnal=1
                order by tanggal asc";
        //exit("error:".$sKehadiran);
        $qKhdr= mysql_query($sKehadiran) or die(mysql_error($conn));
        while($resKhdrn=  mysql_fetch_assoc($qKhdr)){
				  $dtKary[$resKhdrn['karyawanid']]=$resKhdrn['karyawanid'];
                  $whrt="kodeorg='".$resKhdrn['kodeorg']."'";
                  $optBloklm=  makeOption($dbname, 'setup_blok', 'kodeorg,bloklama',$whrt);
                  $hslKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['hasilkerja'];
                  $notrKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['notransaksi'];
				  if($_SESSION['empl']['regional']=='SULAWESI'){
						$blkKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$optBloklm[$resKhdrn['kodeorg']];
				  }else{
						$blkKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['kodeorg'];
				  }
                  //$blkKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$optBloklm[$resKhdrn['kodeorg']];
                  //exit("error".$optNmKeg[$resKhdrn['kodekegiatan']]."____".substr($optNmKeg[$resKhdrn['kodekegiatan']],-3,3));
                  if(substr($optNmKeg[$resKhdrn['kodekegiatan']],-3,3)=="[S]"){
                      $pkkRpSat[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['umr'];
                  }else{
                      $pkkRp[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['umr'];
                  }
                  
                  $premiKerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['insentif'];
                  if($resKhdrn['insentif']!='0'){
                      $hdrPremi[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]=1;
                  }
                  if($resKhdrn['hasilkerja']!=''){
                      $hdrAbsn[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]=1;
                  }
                  $dafkerja[$resKhdrn['karyawanid'].$resKhdrn['tanggal'].$resKhdrn['notransaksi']]=$resKhdrn['kodekegiatan'];
                  $lstKerja[$resKhdrn['notransaksi']]=$resKhdrn['notransaksi'];
                  $jmlhRowTgl[$resKhdrn['karyawanid'].$resKhdrn['tanggal']]+=1;
        }
    }
    
    #ambil data panen
    $sPrestasi="select  kodeorg,tanggal,karyawanid,notransaksi,hasilkerja,upahkerja,upahpremi from ".$dbname.".kebun_prestasi_vw 
                where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and unit='".$kdUnit."' and karyawanid  in (".$sKary.") and tipekaryawan='KHT' ".$addKhdrn."  and jurnal in ('0','1')
                order by tanggal asc";
    //exit("Error".$sPrestasi);
    $rPrestasi=fetchData($sPrestasi);
    foreach ($rPrestasi as $presBrs =>$resPres){
		$dtKary[$resPres['karyawanid']]=$resPres['karyawanid'];
        $whrt="kodeorg='".$resPres['kodeorg']."'";
        $optBloklm=  makeOption($dbname, 'setup_blok', 'kodeorg,bloklama',$whrt);
        $whtrns="notransaksi='".$resPres['notransaksi']."'";
        $optTrf=  makeOption($dbname, 'kebun_prestasi', 'notransaksi,tarif',$whtrns);
        $sTarif="select distinct tarif from ".$dbname.".kebun_prestasi where notransaksi='".$resPres['notransaksi']."' and nik='".$resPres['karyawanid']."'";
        $qTarif=  mysql_query($sTarif) or die(mysql_error($conn));
        $rTarif=  mysql_fetch_assoc($qTarif);
        //$resPres['kodekegiatan']="611010101";
        $hslKerja[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['hasilkerja'];
        $notrKerja[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['notransaksi'];
		if($_SESSION['empl']['regional']=='SULAWESI'){
			$blkKerja[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$optBloklm[$resPres['kodeorg']];
		}else{
			$blkKerja[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['kodeorg'];
		}
        if($rTarif['tarif']=='harian'){
            $pkkRp[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['upahkerja'];
        }else{
            $pkkRpSat[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['upahkerja'];
        }
        $premiPanen[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$resPres['upahpremi'];
        $tarif[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]=$rTarif['tarif'];
        if($resPres['upahpremi']!='0'){
            $hdrPremi[$resPres['karyawanid'].$resPres['tanggal']]=1;
        }
        if($resPres['hasilkerja']!=''){
            $hdrAbsn[$resPres['karyawanid'].$resPres['tanggal']]=1;
        }
        $dafkerja[$resPres['karyawanid'].$resPres['tanggal'].$resPres['notransaksi']]="611010101";
        $lstKerja[$resPres['notransaksi']]=$resPres['notransaksi'];
        $jmlhRowTgl[$resPres['karyawanid'].$resPres['tanggal']]+=1;
    }
    
    #data absen
    $sAbsn="select a.karyawanid,a.tanggal,absensi,insentif from ".$dbname.".sdm_absensidt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
         . "where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and left(kodeorg,4)='".$kdUnit."' and tipekaryawan=4 and  a.karyawanid in (".$sKary.") order by tanggal asc";
    $qAbsn=  mysql_query($sAbsn) or die(mysql_error($conn));
    while($rAbsn=  mysql_fetch_assoc($qAbsn)){
		$dtKary[$rAbsn['karyawanid']]=$rAbsn['karyawanid'];
        $keg=tanggalsystem(tanggalnormal($rAbsn['tanggal']))."admin";
        if(($rAbsn['absensi']=='H')||(substr($rAbsn['absensi'],0,1)=='H')||(substr($rAbsn['absensi'],0,1)=='C')){
            $dafkerja[$rAbsn['karyawanid'].$rAbsn['tanggal'].$keg]="admin";
            $pkkRp[$rAbsn['karyawanid'].$rAbsn['tanggal'].$keg]=$rAbsn['insentif'];
            $jmlhRowTgl[$rAbsn['karyawanid'].$rAbsn['tanggal']]+=1;
        }
        
        $hdrAbsn[$rAbsn['karyawanid'].$rAbsn['tanggal']]=1;
        $lstKerja[$keg]=$keg;
        if(($rAbsn['absensi']=='L')&&($rAbsn['insentif']!='0')){
            #libnas
            $sLibnas="select tanggal from ".$dbname.".sdm_5harilibur where regional='".$optRegional[$kdUnit]."' and tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."'";
            $qLibnas=  mysql_query($sLibnas) or die(mysql_error($conn));
            $rLibnas=  mysql_fetch_assoc($qLibnas);
            $dafkerja[$rAbsn['karyawanid'].$rAbsn['tanggal'].$keg]="Libnas";
            $pkkRp[$rAbsn['karyawanid'].$rAbsn['tanggal'].$keg]=$rAbsn['insentif'];
            $jmlhRowTgl[$rAbsn['karyawanid'].$rAbsn['tanggal']]+=1;
        }
    }
    #data lembur
    $sLem="select a.karyawanid,a.tanggal,tipelembur,jamaktual,uangkelebihanjam from ".$dbname.".sdm_lemburdt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
          . " where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and a.kodeorg like '%".$kdUnit."%' and b.tipekaryawan=4 and a.karyawanid in (".$sKary.") order by tanggal asc";
    
    /*$sLem="select a.karyawanid,a.tanggal,tipelembur,jamaktual,uangkelebihanjam from ".$dbname.".sdm_lemburdt a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
          . " where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and a.kodeorg='".$kdUnit."' and b.tipekaryawan=4 and a.karyawanid in (".$sKary.") order by tanggal asc";*/
    

//exit("Error:$sLem");
    
    $qLem=  mysql_query($sLem) or die(mysql_error($conn));
    while($rLem=  mysql_fetch_assoc($qLem)){
        $keg=tanggalsystem(tanggalnormal($rLem['tanggal']))."lembur";
        $dafkerja[$rLem['karyawanid'].$rLem['tanggal'].$keg]="Lembur";
        $wrk="kodeorg='".$kdUnit."' and tipelembur='".$rLem['tipelembur']."'";
        $lemJamAk[$rLem['karyawanid'].$rLem['tanggal']]=$rLem['jamaktual'];
        $lemRp[$rLem['karyawanid'].$rLem['tanggal']]=$rLem['uangkelebihanjam'];
        $whr="kodeorg='".$kdUnit."' and jamaktual='".$rLem['jamaktual']."' and tipelembur='".$rLem['tipelembur']."'";
        $optMasLembur=  makeOption($dbname, 'sdm_5lembur', 'jamaktual,jamlembur',$whr );
        $lemJamLem[$rLem['karyawanid'].$rLem['tanggal']]=$optMasLembur[$rLem['jamaktual']];
        $jmlhRowTgl[$rLem['karyawanid'].$rLem['tanggal']]+=1;
        $lstKerja[$keg]=$keg;
		$dtKary[$rLem['karyawanid']]=$rLem['karyawanid'];
    }
    #premi kehadrian
    $sKehadiran="select a.`karyawanid`,`jabatan`,`pembagi`,`premiinput` from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
            .   " where jabatan='PREMIHADIR' and periode='".$periode."' and kodeorg='".$kdUnit."' and tipekaryawan=4 and a.`karyawanid` in (".$sKary.")";
    $qKehadiran=  mysql_query($sKehadiran) or die(mysql_error($conn));
    while($rKehadiran=  mysql_fetch_assoc($qKehadiran)){
        $premiKehadiran[$rKehadiran['karyawanid']]=$rKehadiran['premiinput'];
		$dtKary[$rKehadiran['karyawanid']]=$rKehadiran['karyawanid'];
    }
    $t1=$periode."-01 00:00:01";
    $startd = strtotime($t1);
    $dt=date('t',$startd);        
    $tglTerakhir=$periode."-".$dt;
    #premi panen
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
            $urut2+=1;
            $kgPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['hasilkg'];
            $rpPrem[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['rupiah'];
            $kdOrg[$rSetupPrem['kodeorg'].$urut2]=$rSetupPrem['kodeorg'];
        }
    }// a.`karyawanid`
        /* $sJjg="select karyawanid,sum(hasilkerja) as jmlhjjg from ".$dbname.".kebun_prestasi_vw "
                . " where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and unit='".$kdUnit."' and tipekaryawan='KHT' ".$addKhdrn." group by karyawanid,tanggal"; */
				$sJjg="select karyawanid,sum(hasilkerja) as jmlhjjg from ".$dbname.".kebun_prestasi_vw "
                . " where tanggal between '".$optTglGj[$periode]."' and '".$optTglGjS[$periode]."' and unit='".$kdUnit."' and tipekaryawan='KHT' and karyawanid in (".$sKary.") group by karyawanid,tanggal";
        //exit("error:".$sJjg);
        $qJjg=  mysql_query($sJjg) or die(mysql_error($conn));
        while($rJjg=  mysql_fetch_assoc($qJjg)){
            if($rJjg['jmlhjjg']!=''){
				$dtKary[$rJjg['karyawanid']]=$rJjg['karyawanid'];
                $hrEfektif[$rJjg['karyawanid']]+=1;
                $totJjg[$rJjg['karyawanid']]+=$rJjg['jmlhjjg'];
            }
        }
    $sPremiPanen="select a.karyawanid,totalkg,rupiahpremi from ".$dbname.".kebun_premipanen a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
               . " where a.kodeorg='".$kdUnit."' and periode='".$periode."' and tipekaryawan=4 and a.karyawanid in (".$sKary.")";
    //exit("error:".$sPremiPanen);
    $qPremiPanen=  mysql_query($sPremiPanen) or die(mysql_error());
    while($rPremiPanen=  mysql_fetch_assoc($qPremiPanen)){
        if($rPremiPanen['rupiahpremi']!='0'){
                $angk1=1;
                $angk2=2;
                $angk3=3;
                $ws="karyawanid='".$rPremiPanen['karyawanid']."'";
				$dtKary[$rPremiPanen['karyawanid']]=$rPremiPanen['karyawanid'];
                $optSubbgain=  makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian', $ws);
                //exit("error:".$optSubbgain[$rPremiPanen['karyawanid']]."___".$kdOrg[$optSubbgain[$rPremiPanen['karyawanid']]]);
                if($kdOrg[$optSubbgain[$rPremiPanen['karyawanid']]]!=''){
                    if($rPremiPanen['totalkg']==($rpPrem[$optSubbgain[$rPremiPanen['karyawanid'].$angk1]]*$rPremiPanen['totalkg'])){
                         $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanen11";
                         $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanen11";
                         $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                         $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rPremiPanen['rupiahpremi'];
                    }else{
                         $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanen5";
                         $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanen5";
                         $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                         $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rPremiPanen['rupiahpremi'];
                    }
                }else{
                    $bandingJjg=0;
                    $kdData=$optRegional[$kdUnit];
                    @$bandingJjg=$totJjg[$rPremiPanen['karyawanid']]/70;
                    //
                    //if($hariAktif[$karyId]>=16){
                    if($bandingJjg>$hrEfektif[$rPremiPanen['karyawanid']]){
                        $rupy=($hrEfektif[$rPremiPanen['karyawanid']])*$rajinPrem[$kdData];
                    }else{
                        $rupy=($bandingJjg)*$rajinPrem[$kdData];
                    }
                    if(($rPremiPanen['rupiahpremi']-$rupy)==0){
                        $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanenrjn";
                        $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanenrjn";
                        $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rPremiPanen['rupiahpremi'];
                        $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                    }elseif($rpPrem[$kdData.$angk1]==($rPremiPanen['rupiahpremi']-$rupy)){
                        $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanen35";
                        $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanen35";
                        $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=($rPremiPanen['rupiahpremi']-$rupy);
                        $lstKerja[$keg]=$keg;
                        $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                        if($rupy!=0){
                            $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanenrjn";
                            $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanenrjn";
                            $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rupy;
                            $lstKerja[$keg]=$keg;
                            $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                        }
                    }elseif($rpPrem[$kdData.$angk2]==($rPremiPanen['rupiahpremi']-$rupy)){
					
                        $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanen30";
                        $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanen30";
                        $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=($rPremiPanen['rupiahpremi']-$rupy);
                        $lstKerja[$keg]=$keg;
                        $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                        if($rupy!=0){
                            $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanenrjn";
                            $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanenrjn";
                            $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rupy;
                            $lstKerja[$keg]=$keg;
                            $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                        }
                    }elseif($rpPrem[$kdData.$angk3]==($rPremiPanen['rupiahpremi']-$rupy)){
                        $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanen25";
                        $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanen25";
                        $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=($rPremiPanen['rupiahpremi']-$rupy);
                        $lstKerja[$keg]=$keg;
                        $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
                        if($rupy!=0){
                            $keg=tanggalsystem(tanggalnormal($tglTerakhir))."prpanenrjn";
                            $dafkerja[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]="prpanenrjn";
                            $pkkRpSat[$rPremiPanen['karyawanid'].$tglTerakhir.$keg]=$rupy;
                            $lstKerja[$keg]=$keg;
                            $jmlhRowTgl[$rPremiPanen['karyawanid'].$tglTerakhir]+=1;
							
                        }
                    }
                }
                 
                //exit("error".$keg.$kdData);
                
        }
    }
    #loading ford dan dt
    $sFord="select a.karyawanid,jabatan,premiinput from ".$dbname.".kebun_premikemandoran a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
         . " where periode='".$periode."' and a.kodeorg='".$kdUnit."' and tipekaryawan=4 and jabatan like 'LOADING%' and a.karyawanid in (".$sKary.")";
    //echo $sFord;
    $qForm=  mysql_query($sFord) or die(mysql_error($conn));
    while($rForm=  mysql_fetch_assoc($qForm)){
        $keg=tanggalsystem(tanggalnormal($tglTerakhir)).$rForm['jabatan'];
        $dafkerja[$rForm['karyawanid'].$tglTerakhir.$keg]=$rForm['jabatan'];
        $lstKerja[$keg]=$keg;
        $pkkRpSat[$rForm['karyawanid'].$tglTerakhir.$keg]=$rForm['premiinput'];
        $jmlhRowTgl[$rForm['karyawanid'].$tglTerakhir]+=1;
		$dtKary[$rForm['karyawanid']]=$rForm['karyawanid'];
    }
    
    
    array_multisort($arrTgl,SORT_ASC);
    foreach($dtKary as $lstKary){
	
        $no+=1;
            $tab.="<table>";
            $tab.="<tr><td></td>";
            $tab.="<tr><td>No.</td>";
            $tab.="<td>&nbsp;</td>";
            $tab.="<td>: ".$no."</td></tr>";
            $tab.="<tr><td>".strtoupper($_SESSION['lang']['bulan'])."</td>";
            $tab.="<td>&nbsp;</td>";
//            $tab.="<td>: ".strtoupper($arrBln[intval($prd[1])])." (".substr($optTglGj[$periode],-2,2)."-".substr($optTglGjS[$periode],-2,2).") ".$prd[0]."</td></tr>";
            $tab.="<td>:  (".tanggalnormal(nambahHari($optTglGj2[$periode2],1,1))." s.d ".tanggalnormal($optTglGjS[$periode]).")  </td></tr>";
            $tab.="<tr><td colspan=2>".strtoupper($_SESSION['lang']['nik'])." / ".strtoupper($_SESSION['lang']['namakaryawan'])."</td>";
            $tab.="<td>: ".$dtNik[$lstKary]." / ".strtoupper($dtNmkar[$lstKary])."</td></tr>";
            $tab.="<tr><td>".strtoupper($_SESSION['lang']['unit']." kerja")."</td>";
            $tab.="<td>&nbsp;</td>";
            $sbdiv="kodeorganisasi='".$dtSubdivisi[$lstKary]."'";
            $optDivisi=  makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi',$sbdiv);
            $tab.="<td>: ".$dtSubdivisi[$lstKary]." / ".$optDivisi[$dtSubdivisi[$lstKary]]."</td></tr>";
            $tab.="</table>";
            $tab.="<table cellpadding=1 cellspacing=1 border='".$garis."' class=sortable><thead>";
            $tab.="<tr ".$bgcolordt." align=center>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['tanggal'])."</td>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['kodekegiatan'])."</td>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['namakegiatan'])."</td>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['satuan'])."</td>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['notransaksi'])."</td>";
            $tab.="<td rowspan=2>".strtoupper($_SESSION['lang']['kodeblok'])."</td>";
            $tab.="<td rowspan=2>HASIL KERJA</td>";
            $tab.="<td colspan=2>H A R I</td>";
            $tab.="<td colspan=2>L E M B U R</td>";
            $tab.="<td colspan=3>U P A H</td>";
            $tab.="<td rowspan=2>JOB INSENTIF</td>";
            $tab.="<td rowspan=2>TAM. INSE SATUAN</td>";
            $tab.="<td rowspan=2>PREMI HADIR</td>";
            $tab.="<td rowspan=2>TOTAL</td></tr>";
            $tab.="<tr  ".$bgcolordt."><td>HADIR</td>";
            $tab.="<td>PREMI</td>";
            $tab.="<td nowrap>J A M</td>";
            $tab.="<td>FAKTOR</td>";
            $tab.="<td>POKOK</td>";
            $tab.="<td>SATUAN</td>";
            $tab.="<td>LEMBUR</td>";
            $tab.="</tr></thead><tbody>";
            foreach($arrTgl as $dtTgl){
                foreach($lstKerja as $lstKegiatan){
                     if($dafkerja[$lstKary.$dtTgl.$lstKegiatan]!=''){
                          $tab.="<tr class=rowcontent>";
                           if($dtTgl!=$tglTem){
                                $tglTem=$dtTgl;
                                $aret=0;
                                if ($proses=='excel')
                                    $tab.="<td nowrap>".$dtTgl."</td>";
                                else
                                    $tab.="<td nowrap>".tanggalnormal($dtTgl)."</td>";
                            }else{
                               if($aret==0){
                                    $tab.="<td rowspan=".($jmlhRowTgl[$lstKary.$dtTgl]-1).">&nbsp;</td>";
                                    $aret=1;
                                }
                            }
                            if($optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]=='POTONG BUAH'){
								$optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="PANEN";
							}
                             
                            switch($dafkerja[$lstKary.$dtTgl.$lstKegiatan]){
                                case'Lembur':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="LEMBUR";
                                break;
                                case'admin':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="ADMIN";
                                break;
                                case'Libnas':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="LIBUR NASIONAL";
                                break;
                                case'prpanenrjn':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="PREMI RAJIN";
                                break;
                                case'prpanen25':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="INSENTIF PANEN 25 TON";
                                break;
                                case'prpanen30':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="INSENTIF PANEN 30 TON";
                                break;
                                case'prpanen35':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="INSENTIF PANEN 35 TON";
                                break;
                                case'prpanen5':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="INSENTIF PANEN 5 TON";
                                break;
                                case'prpanen11':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="INSENTIF PANEN 11 TON";
                                break;
                                case'LOADINGFORD':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="LOADINGFORD";
                                break;
                                case'LOADINGDT':
                                    $optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]="LOADINGDT";
                                break;
                            }
                            $tab.="<td>".$dafkerja[$lstKary.$dtTgl.$lstKegiatan]."</td>";
                            $tab.="<td>".$optNmKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]." ".strtoupper($tarif[$lstKary.$dtTgl.$lstKegiatan])."</td>";
                            $tab.="<td>".$optSatKeg[$dafkerja[$lstKary.$dtTgl.$lstKegiatan]]."</td>";
                            //$tab.="<td>".substr($notrKerja[$lstKary.$dtTgl.$lstKegiatan],14,7)."</td>";
                            $tab.="<td>".$notrKerja[$lstKary.$dtTgl.$lstKegiatan]."</td>";
                            $tab.="<td>".$blkKerja[$lstKary.$dtTgl.$lstKegiatan]."</td>";
                            $tab.="<td align=right>".$hslKerja[$lstKary.$dtTgl.$lstKegiatan]."</td>";
                            if($dtTgl!=$hrTgl){
                                $hrTgl=$dtTgl;
                                $tab.="<td align=right>".$hdrAbsn[$lstKary.$dtTgl]."</td>";
                                $tab.="<td align=right>".$hdrPremi[$lstKary.$dtTgl]."</td>";
                                $totHadir[$lstKary]+=$hdrAbsn[$lstKary.$dtTgl];
                                $totHrPrem[$lstKary]+=$hdrPremi[$lstKary.$dtTgl];
                            }else{
                                $tab.="<td align=right>&nbsp;</td>";
                                $tab.="<td align=right>&nbsp;</td>";
                            }
                            $tab.="<td align=right>".$lemJamAk[$lstKary.$dtTgl]."</td>";
                            $tab.="<td align=right>".$lemJamLem[$lstKary.$dtTgl]."</td>";
                            
                            if($pkkRp[$lstKary.$dtTgl.$lstKegiatan]==''){#upah kegiatan harian
                                $tab.="<td align=right>&nbsp;</td>";
                            }else{
                                $tab.="<td align=right>".number_format($pkkRp[$lstKary.$dtTgl.$lstKegiatan],0)."</td>";
                                $totuphPokok[$lstKary]+=$pkkRp[$lstKary.$dtTgl.$lstKegiatan];
                            }
                            if($pkkRpSat[$lstKary.$dtTgl.$lstKegiatan]==''){#upah kegiatan satuan
                                $tab.="<td align=right>&nbsp;</td>";
                            }else{
                                $tab.="<td align=right>".number_format($pkkRpSat[$lstKary.$dtTgl.$lstKegiatan],0)."</td>";
                                $totuphSatuan[$lstKary]+=$pkkRpSat[$lstKary.$dtTgl.$lstKegiatan];
                            }
                            if($lemRp[$lstKary.$dtTgl]==''){#upah lembur
                                $tab.="<td align=right>&nbsp;</td>";
                            }else{
                                $tab.="<td align=right>".number_format($lemRp[$lstKary.$dtTgl],0)."</td>";
                                $totUphlembur[$lstKary]+=$lemRp[$lstKary.$dtTgl];
                            }
                            if($premiKerja[$lstKary.$dtTgl.$lstKegiatan]==''){#premi kegiatan
                                $tab.="<td align=right>&nbsp;</td>";
                            }else{
                                $tab.="<td align=right>".number_format($premiKerja[$lstKary.$dtTgl.$lstKegiatan],0)."</td>";
                                $totPremiKrj[$lstKary]+=$premiKerja[$lstKary.$dtTgl.$lstKegiatan];
                            }
                            if($premiPanen[$lstKary.$dtTgl.$lstKegiatan]==''){#premi panen
                                $tab.="<td align=right>&nbsp;</td>";
                            }else{
                                $tab.="<td align=right>".number_format($premiPanen[$lstKary.$dtTgl.$lstKegiatan],0)."</td>";
                                $totPremiPnn[$lstKary]+=$premiPanen[$lstKary.$dtTgl.$lstKegiatan];
                            }
                            if($premiKehadiran[$lstKary]!=0){ //premi kehadiran
                                @$premihdr[$lstKary]=$premiKehadiran[$lstKary]/($premiKehadiran[$lstKary]/1000);
                                if($premihdr[$lstKary]!=$premiKehadiran[$lstKary]){
                                    $tab.="<td align=right>".number_format($premihdr[$lstKary],0)."</td>";
                                }else{
                                    $tab.="<td align=right>&nbsp;</td>";
                                }
                            }else{
                                $tab.="<td align=right>&nbsp;</td>";
                            }
                            
                            $rpPertgl[$lstKary.$dtTgl]=$premihdr[$lstKary]+$premiPanen[$lstKary.$dtTgl.$lstKegiatan]+$premiKerja[$lstKary.$dtTgl.$lstKegiatan]+$lemRp[$lstKary.$dtTgl]+$pkkRpSat[$lstKary.$dtTgl.$lstKegiatan]+$pkkRp[$lstKary.$dtTgl.$lstKegiatan];
                            $totSmua[$lstKary]+=$rpPertgl[$lstKary.$dtTgl];
                            $totKrj[$lstKary]+=$hslKerja[$lstKary.$dtTgl.$lstKegiatan];
                            $totJamLemAk[$lstKary]+=$lemJamAk[$lstKary.$dtTgl];
                            $totJamLemLem[$lstKary]+=$lemJamLem[$lstKary.$dtTgl];
                            $tab.="<td align=right>".number_format($rpPertgl[$lstKary.$dtTgl],0)."</td>";
                            $tab.="</tr>";
                            
                    }
                }
            }
            $tab.="<tr><td colspan=6>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right>".$totKrj[$lstKary]."</td>";
            $tab.="<td align=right>".$totHadir[$lstKary]."</td>";
            $tab.="<td align=right>".$totHrPrem[$lstKary]."</td>";
            $tab.="<td align=right>".$totJamLemAk[$lstKary]."</td>";
            $tab.="<td align=right>".$totJamLemLem[$lstKary]."</td>";
            $tab.="<td align=right>".number_format($totuphPokok[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($totuphSatuan[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($totUphlembur[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($totPremiKrj[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($totPremiPnn[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($premiKehadiran[$lstKary],0)."</td>";
            $tab.="<td align=right>".number_format($totSmua[$lstKary],0)."</td>";
            $tab.="</tbody></table>";
            //exit("error".$tab);
    }
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
         . " where ".$whr." and tipekaryawan=4 and (tanggalkeluar='0000-00-00') order by namakaryawan asc";
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
 $nop_="kartuKerjaPerKary__".$periode."__".$kdUnit;
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

<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$proses = $_GET['proses'];
$param = $_POST;
$namakar=Array();
#cek tutup atau belum periode gaji
$sCekPeriode="select distinct * from ".$dbname.".sdm_5periodegaji where periode='".$param['periodegaji']."' 
              and kodeorg='".$param['kodeorg']."' and sudahproses=1 and jenisgaji='H'";
$qCekPeriode=mysql_query($sCekPeriode) or die(mysql_error($conn));
if(mysql_num_rows($qCekPeriode)>0)
    $aktif2=false;
       else
     $aktif2=true;
  if(!$aktif2)
  {
      exit(" Payroll period has been closed");
  }
  #periksa tutupbuku
       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and 
             kodeorg='".$param['kodeorg']."' and tutupbuku=1";
       $res=mysql_query($str);
       if(mysql_num_rows($res)>0)
           $aktif=false;
       else
           $aktif=true;
  if(!$aktif)
  {
      exit("Accounting period has been closed");
  }
  
# Get Period Range
$qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai,tglcutoff',
    "periode='".$param['periodegaji']."' and kodeorg='".
    $param['kodeorg']."' and jenisgaji='H'");

$resPeriod = fetchData($qPeriod);
$tanggal1 = $resPeriod[0]['tanggalmulai'];
$tanggal2   =$resPeriod[0]['tanggalsampai'];
if($tanggal2=='0000-00-00') {
    echo "Error : Tanggal cut off pada periode gaji belum ditentukan.";
    exit();
}
if($param['tipe']==3){
    $tanggal2 = $resPeriod[0]['tglcutoff'];
    $tglsmp   =$resPeriod[0]['tanggalsampai'];
    #mengambil tgl cutoff di bulan lalu
    $prd=explode("-",$param['periodegaji']);
    if($prd[0]!=(date("Y"))){
        $prdlalu=($prd[0]-1)."-12";
    }else{
        $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
        $prdlalu=$prd[0]."-".$bln;
    }
    $qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai,tglcutoff',
    "periode='".$prdlalu."' and kodeorg='".$param['kodeorg']."' and jenisgaji='H'");
    $resPeriod2 = fetchData($qPeriod);
    $tglcutblnlalu=$resPeriod2[0]['tglcutoff'];
    $tglsmpblnlalu=$resPeriod2[0]['tanggalsampai'];
    if($param['periodegaji']=='2014-03'){
        $tglcutblnlalu='2014-03-01';
    }
    if(($tglcutblnlalu=='')||($tglcutblnlalu=='0000-00-00')){
        exit("error: Cut off date last month empty");
    }
    $tglcutblnlalu=nambahHari(tanggalnormal($tglcutblnlalu),1,1);//ditambahkan satu hari dari hari cut off untuk perhitungan lembur dan premi
}
//exit("error:".$tanggal2);
# Hapus transaksi yang nomor BKM-nya salah
$str="delete from ".$dbname.".kebun_aktifitas where notransaksi like '%//%'";
mysql_query($str);

#2. Get Karyawan harian yang penggajian=Harian dan alokasi=0
$query1 = selectQuery($dbname,'datakaryawan','karyawanid,tipekaryawan,namakaryawan,jms,statuspajak,npwp,nik,lokasitugas',"tipekaryawan='".$param['tipe']."' and 
     lokasitugas='".$param['kodeorg']."'   and 
     (tanggalkeluar>='".$tanggal1."' or tanggalkeluar='0000-00-00') and alokasi=0 
      and (tanggalmasuk<='".$tanggal2."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null)");
//echo $query1;
$absRes = fetchData($query1);
# Error empty karyawan
if(empty($absRes)) {
    echo "Error : ".$_SESSION['lang']['noempltobeprocess'];
    exit();
}
else
{
    $trig=1;
    $id=Array();
    foreach($absRes as $row => $kar)
    {
        if($trig==1){
            $trig=2;
            #ambil karyawan yang pindah lokasi tugas
            $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
         
                while($rcek=  mysql_fetch_assoc($qcek)){
                    $sGj="select karyawanid,tipekaryawan,namakaryawan,jms,statuspajak,npwp,nik from ".$dbname.".datakaryawan where karyawanid='".$rcek['karyawanid']."'";
                    $qDt= mysql_query($sGj) or die(mysql_error($conn));
                    $kar2=  mysql_fetch_assoc($qDt);
                    $id[$kar2['karyawanid']][]=$kar2['karyawanid'];

                    $namakar[$kar2['karyawanid']]=$kar2['namakaryawan'];
                    $nikKar[$kar2['karyawanid']]=$kar2['nik'];
                    $lokasiTugas[$kar2['karyawanid']]=$kar2['lokasitugas'];
                    $gajiperhari[$kar2['karyawanid']]=0;   #default gaji KHT=0
                    #mengambil no Jamsostek
                    $nojms[$kar2['karyawanid']]=trim($kar2['jms']);
                    $statuspajak[$kar2['karyawanid']]=trim($kar2['statuspajak']);
                    $npwp[$kar2['karyawanid']]=trim($kar2['npwp']);
                    if($kar2['tipekaryawan']==2)
                    $tipekaryawan[$kar2['karyawanid']]='Kontrak';
                    else if($kar2['tipekaryawan']==3)
                    $tipekaryawan[$kar2['karyawanid']]='KBL';
                    else if($kar2['tipekaryawan']==4)
                    $tipekaryawan[$kar2['karyawanid']]='KHT'; 
                    else if($kar2['tipekaryawan']==6)
                    $tipekaryawan[$kar2['karyawanid']]='Kontrak Karya';      
                    else
                    $tipekaryawan[$kar2['karyawanid']]='Magang'; 
                }
            

        }
      #filter yang bukan di lokasitugasnya
      $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$kar['karyawanid']."'";
      //exit("error:".$scek);
      $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
      $rcek=  mysql_fetch_assoc($qcek);
      if($rcek['kodeorg']!=''){
        if($rcek['kodeorg']!=$param['kodeorg']){
            continue;
        }
      } 
      $id[$kar['karyawanid']][]=$kar['karyawanid'];
      $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
      $nikKar[$kar['karyawanid']]=$kar['nik'];
      $gajiperhari[$kar['karyawanid']]=0;   #default gaji KHT=0
      #mengambil no Jamsostek
      $nojms[$kar['karyawanid']]=trim($kar['jms']);
      $statuspajak[$kar['karyawanid']]=  strtoupper(trim($kar['statuspajak']));
      $npwp[$kar['karyawanid']]=trim($kar['npwp']);
      
      if($kar['tipekaryawan']==2)
         $tipekaryawan[$kar['karyawanid']]='Kontrak';
      else if($kar['tipekaryawan']==3)
         $tipekaryawan[$kar['karyawanid']]='KBL';
      else if($kar['tipekaryawan']==4)
         $tipekaryawan[$kar['karyawanid']]='KHT'; 
       else if($kar['tipekaryawan']==6)
         $tipekaryawan[$kar['karyawanid']]='Kontrak Karya';      
      else
         $tipekaryawan[$kar['karyawanid']]='Magang'; 
    }  
    
}
# ambil gaji pokok per hari untuk KHT=>KBL jika di hardaya
if($param['tipe']==3){
    $trig=1;
    $strgjh = "select a.karyawanid,sum(jumlah)/25 as gjperhari from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.idkomponen in (1,2)
               group by a.karyawanid";
    //echo $strgjh;
    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val)
    {
        if($trig==1){
            $trig=2;
            #ambil karyawan yang pindah lokasi tugas
            $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
            
                while($rcek=  mysql_fetch_assoc($qcek)){
                    $sGj="select karyawanid,sum(jumlah)/25 as gjperhari from ".$dbname.".sdm_5gajipokok where karyawanid='".$rcek['karyawanid']."'";
                    $qDt= mysql_query($sGj) or die(mysql_error($conn));
                    $val2=  mysql_fetch_assoc($qDt);
                    $gajiperhari[$val2['karyawanid']]=$val2['gjperhari'];
                    //exit("error".$gajiperhari[$val2['karyawanid']]);
                }
            
        }
        $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
        $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
        $rcek=  mysql_fetch_assoc($qcek);
        if($rcek['kodeorg']!=''){
            if($rcek['kodeorg']!=$param['kodeorg']){
                continue;
            }
        }
          $gajiperhari[$val['karyawanid']]=$val['gjperhari'];
    }
}
     //Jika periode gaji lebih dari sebulan, maka kelebihannya ditambah sesuai dengan gaji  harian            
     //==========hitung selisih hari
        $t1=$tanggal1." 00:00:01";//awal
        $t2=$tanggal2." 23:59:59";//sampai
        $endd = strtotime($t2);
        $startd = strtotime($t1);
        $jumlahh= round(abs($endd-$startd)/60/60/24);
        //ambil jumlah hari periode gaji ( jumlah hari satu bulan)
        $pengurang=date('t',$startd);        
        if($param['tipe']==3){
            #jumlah hari
            $pengurang=$jumlahh;
        }
        
     //=======================================================================  
 if($param['tipe']==3){//khusus kht aka kbl jika di hardaya
 #ambil jumlah hk tidak dibayar untuk KHT dan total tidak dibayar
     $trig=1;
     $strgjh = "select  count(*) as jlh,b.karyawanid from ".$dbname.".sdm_hktdkdibayar_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."'     
               group by a.karyawanid";
     //echo $strgjh;
      //exit("error:".$strgjh);
    $tdkdibayar=Array();
    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val){
        if($trig==1){
            $trig=2;
            #ambil karyawan yang pindah lokasi tugas
            $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
            $qcek=  mysql_query($scek) or die(mysql_error($conn));
                        #ambil karyawan yang pindah lokasi tugas
                        while($rcek=  mysql_fetch_assoc($qcek)){
                        // Cek promosi
                        $qPromosi = selectQuery($dbname,'sdm_riwayatjabatan','*',"karyawanid='".$rcek['karyawanid']."' and mulaiberlaku='".$tanggal1."'");
                        $resPromosi=fetchData($qPromosi);
                        if ($resPromosi[0]['daritipe']==4 and $resPromosi[0]['ketipekaryawan']==3){
                            $sGj="select  count(*) as jlh,karyawanid from ".$dbname.".sdm_hktdkdibayar_vw "
                               . "where karyawanid='".$rcek['karyawanid']."' and tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."' ";
                        } else {
                            $sGj="select  count(*) as jlh,karyawanid from ".$dbname.".sdm_hktdkdibayar_vw "
                               . "where karyawanid='".$rcek['karyawanid']."' and tanggal>='".$tglcutblnlalu."' and tanggal<='".$tanggal2."' ";
                        }
                        $qDt= mysql_query($sGj) or die(mysql_error($conn));
                        $val2=  mysql_fetch_assoc($qDt);
                        //exit("error".$val2['jlh']."___".$gajiperhari[$rcek['karyawanid']]."__".$val2['karyawanid']);
                        $tdkdibayar[$rcek['karyawanid']]=$gajiperhari[$rcek['karyawanid']]*$val2['jlh'];#jumlah tidak dibayar
                         $readyData[] = array(
                            'kodeorg'=>$param['kodeorg'],
                            'periodegaji'=>$param['periodegaji'],
                            'karyawanid'=>$rcek['karyawanid'],
                            'idkomponen'=>37,//potongan hk
                            'jumlah'=>$tdkdibayar[$rcek['karyawanid']],
                            'pengali'=>1);
                        }
        }
        $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
        $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
        $rcek=  mysql_fetch_assoc($qcek);
        if($rcek['kodeorg']!=''){
            if($rcek['kodeorg']!=$param['kodeorg']){
                continue;
            }
        }
        // Cek promosi
        $qPromosi = selectQuery($dbname,'sdm_riwayatjabatan','*',"karyawanid='".$val['karyawanid']."' and mulaiberlaku='".$tanggal1."'");
        $resPromosi=fetchData($qPromosi);
        if ($resPromosi[0]['daritipe']==4 and $resPromosi[0]['ketipekaryawan']==3){
            $sGj="select  count(*) as jlh,karyawanid from ".$dbname.".sdm_hktdkdibayar_vw "
               . "where karyawanid='".$val['karyawanid']."' and tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."' ";
            $qDt= mysql_query($sGj) or die(mysql_error($conn));
            $val2=  mysql_fetch_assoc($qDt);
                $tdkdibayar[$rcek['karyawanid']]=$gajiperhari[$rcek['karyawanid']]*$val2['jlh'];#jumlah tidak dibayar
                 $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$rcek['karyawanid'],
                    'idkomponen'=>37,//potongan hk
                    'jumlah'=>$tdkdibayar[$rcek['karyawanid']],
                    'pengali'=>1);
            continue;
        }
        
        $tdkdibayar[$val['karyawanid']]=$gajiperhari[$val['karyawanid']]*$val['jlh'];#jumlah tidak dibayar
        //koreksi untuk memindahkan potongan hk dari gaji pokok ke komponen potongan hk
        //seperti yang diterangkan pada escape dibawah  
        $readyData[] = array(
        'kodeorg'=>$param['kodeorg'],
        'periodegaji'=>$param['periodegaji'],
        'karyawanid'=>$val['karyawanid'],
        'idkomponen'=>37,//potongan hk
        'jumlah'=>$tdkdibayar[$val['karyawanid']],
        'pengali'=>1);      
     }
     
 
    #tambahan absensi yang tidak dicatat sebagai penambah hk tidak dibayar==========added by ginting
    #ambil hari-hari dapam periode berjalan  
   if($proses=='list'){     
       
              #ambil absensi dari bkm=================
             $strux="select a.karyawanid,tanggal from ".$dbname.".kebun_kehadiran_vw a left join 
                     ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                      where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."'
                      and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                      and a.unit like '".$param['kodeorg']."%' 
                      and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                       order by tanggal";
           //exit("error:".$strux);
           $resux = mysql_query($strux); 
           while($baux=mysql_fetch_object($resux)){
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$baux->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
               $periksa[$baux->karyawanid][$baux->tanggal]=$baux->tanggal;
           }
           #ambil absensi dari panen=================
           $strux="select a.karyawanid,tanggal from ".$dbname.".kebun_prestasi_vw a left join 
                     ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                      where b.tipekaryawan in(1,2,3,6) and b.lokasitugas='".$param['kodeorg']."' 
                      and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                      and a.unit like '".$param['kodeorg']."%' 
                      and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                      order by tanggal";
           //exit("error:".$strux);
           $resux = mysql_query($strux); 
           while($baux=mysql_fetch_object($resux)){
               $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$baux->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
               $periksa[$baux->karyawanid][$baux->tanggal]=$baux->tanggal;
           }
       }
        #ambil absensi dari traksi=================
          $strux="select a.idkaryawan as karyawanid,tanggal
                  from ".$dbname.".vhc_runhk_vw a left join 
                 ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                  where b.tipekaryawan in(1,2,3,6) and b.lokasitugas='".$param['kodeorg']."' 
                  and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                  order by tanggal";
          //exit("error:".$strux);
           $resux = mysql_query($strux); 
           while($baux=mysql_fetch_object($resux)){
               $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$baux->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
               $periksa[$baux->karyawanid][$baux->tanggal]=$baux->tanggal;
           }
           #absensi dari absen
            $strux="select a.karyawanid,tanggal
                  from ".$dbname.".sdm_absensidt a left join 
                 ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                  where b.tipekaryawan in(1,2,3,6) and b.lokasitugas='".$param['kodeorg']."' 
                  and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                  and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
                  order by tanggal";  //exit("Error:$strux");
            
            $resux = mysql_query($strux); 
           while($baux=mysql_fetch_object($resux)){
                    #ambil karyawan yang pindah lokasi tugas
                    $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                         . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
                    $qcek=  mysql_query($scek) or die(mysql_error($conn));
                    while($rcek=  mysql_fetch_object($qcek)){
                        $sGj="select karyawanid,tanggal from ".$dbname.".sdm_absensidt where karyawanid='".$rcek->karyawanid."' and tanggal='".$baux->tanggal."'";
                        $qDt= mysql_query($sGj) or die(mysql_error($conn));
                        $val2=  mysql_fetch_object($qDt);
                        $periksa[$val2->karyawanid][$val2->tanggal]=$val2->tanggal;
                    }
               
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$baux->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
               $periksa[$baux->karyawanid][$baux->tanggal]=$baux->tanggal;
           }    
          #ambil absen krani,mandor dari header BKM dan Panen
            $strux="select tanggal,nikmandor,nikmandor1,nikasisten,keranimuat
                  from ".$dbname.".kebun_aktifitas where kodeorg='".$param['kodeorg']."'
                  and  tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."'     
                  order by tanggal";  //exit("error:".$strux);
            $resux = mysql_query($strux) or die(mysql_error())."____".$scek; 
           while($baux=mysql_fetch_object($resux)){
			   $periksa[$baux->nikmandor][$baux->tanggal]=$baux->tanggal;
               $periksa[$baux->nikmandor1][$baux->tanggal]=$baux->tanggal;
               $periksa[$baux->nikasisten][$baux->tanggal]=$baux->tanggal;
               #ambil karyawan yang pindah lokasi tugas
                $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                    . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                while($rcek=  mysql_fetch_object($qcek)){
                    if($rcek->karyawanid==$baux->keranimuat){
                        $sGj="select keranimuat,tanggal from ".$dbname.".kebun_aktifitas where keranimuat='".$rcek->karyawanid."' and tanggal='".$baux->tanggal."'";
                        $qDt= mysql_query($sGj) or die(mysql_error($conn));
                        $val2=  mysql_fetch_object($qDt);
                        $periksa[$val2->keranimuat][$val2->tanggal]=$val2->tanggal;
                    }
                }
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$baux->keranimuat."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
//               if ($baux->nikmandor=='0000001304' || $baux->nikmandor1=='0000001304' || $baux->nikasisten=='0000001304' || $baux->keranimuat=='0000001304'){
//                   echo $baux->tanggal."\n\r";
//               }
               
               $periksa[$baux->keranimuat][$baux->tanggal]=$baux->tanggal;
           }
   
           #query sblm di ubah jamhari
           /*$queryq= selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',"tipekaryawan in(1,2,3,6) and 
           lokasitugas='".$param['kodeorg']."' and 
           (tanggalkeluar>='".$tanggal2."' or tanggalkeluar='0000-00-00') and alokasi=0  
           and ( tanggalmasuk<='".$tanggal1."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null)");
            * 
            */
           #ambil data karyawan KBL,KHT dan Kontrak yang masih aktif
           $queryq= selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,subbagian',"tipekaryawan='".$param['tipe']."' and 
           lokasitugas='".$param['kodeorg']."' and 
           (tanggalkeluar>='".$tanggal2."' or tanggalkeluar='0000-00-00') and alokasi=0  
           and (tanggalmasuk<='".$tanggal1."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null)");
           
            $absResq = fetchData($queryq);
         #periksa apakah masih ada absensi yang belum diinput
            $kotak="";
            $nx=0;
            $trig=1;
            foreach($absResq as $rowq => $karq){
                
                if($trig==1){
                    $trig=2;
                    $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                         . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
                    $qcek=  mysql_query($scek) or die(mysql_error($conn));
                    while($rcek=  mysql_fetch_assoc($qcek)){
                        $sGj="select karyawanid,namakaryawan,subbagian from ".$dbname.".datakaryawan where karyawanid='".$rcek['karyawanid']."'";
                        $qDt= mysql_query($sGj) or die(mysql_error($conn));
                        $val2= mysql_fetch_assoc($qDt);
                        if(count($periksa[$rcek['karyawanid']])<$jumlahh){
                            $nx++;
                            $kotak.=$nx.".".$val2['namakaryawan']."-".$val2['subbagian']."\n";
                        }
                    }
                }
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$karq['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                $drAtas="";
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                
                if(count($periksa[$karq['karyawanid']])<$jumlahh){
//echo"<pre>";
//print_r($periksa[$rcek['karyawanid']]);
//echo"</pre>";
                            //exit("error:".count($periksa[$rcek['karyawanid']]));
                       $nx++;
                       $kotak.=$nx.".".$karq['namakaryawan']."-".$karq['subbagian']."\n";
                }
             }     
			 //indra
             if($kotak!=''){
                exit("Error: There are absences that have not been recorded in the name of the following employees:\n".$kotak);
             }
        }
      #end periksa absensi===========================================================================  
    
    
        
    ##indra tambahan periksa SPB sudah posting semua apa belum 
    ## berlaku hanya untuk tipe organisasi kebun 
        
    $whtpOrg="kodeorganisasi='".$param['kodeorg']."'";
    $tipeOrg=  makeOption($dbname,'organisasi', 'kodeorganisasi,tipe',$whtpOrg);
    $tpOrg=$tipeOrg[$param['kodeorg']];
    
    
    if($tpOrg=='KEBUN')
    {
     //exit("Error:MASUK");  
        $iCek="select nospb,tanggal from ".$dbname.".kebun_spbht where posting=0 and tanggal between '".$tanggal1."' and"
                . " '".$tanggal2."' and kodeorg='".$param['kodeorg']."'";
        //exit("Error:MASUK");  
        $bCek=mysql_query($iCek) or die (mysql_error($conn));
        if(mysql_num_rows($bCek)>0)
        {
            echo " There are SPB transaction that has not been posted:\n";
            $no=0;
            while($cCek=mysql_fetch_assoc($bCek))
            {
                $no+=1;
               echo $no.". ".$cCek['nospb'].", tanggal : ".tanggalnormal($cCek['tanggal'])."\n";
            }
            exit('Error');
        }
    }

  
#2 Get Jamsostek porsi==========================
#ambil semua komponen dari gajipokok khusus KHT dan Kontrak Harian===================== pembentukan penadapatan dan pengurang dari KBL
    $str1 = "select a.*,b.namakaryawan from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0";
    //echo $str1;
    $res1 = fetchData($str1);
    //$query6 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawan'");
    //$jmsRes = fetchData($query6);
    //$persenJms = $jmsRes[0]['value']/100;
    $tjms=Array(); //kembali di aktifkan
        foreach($res1 as $idx => $val)
        {
                #filter karyawan yang lokasitugas aslinya bukan kodeorg proses gaji
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
          #KHT dan Kontrak Harian
          if($id[$val['karyawanid']][0]==$val['karyawanid'] and ($tipekaryawan[$val['karyawanid']]=='KBL' or $tipekaryawan[$val['karyawanid']]=='Kontrak')){
            
            // #kurangkan pemotongan HK tidak dibayar
            // if (array_key_exists($val['karyawanid'], $tdkdibayar)){ 
            //     if($val['idkomponen']=='1')#kurangkan hanya pada gaji pokok
            //     $val['jumlah']=$val['jumlah']-$tdkdibayar[$val['karyawanid']];#pengurangan HK tidak dibayar
            // }
            //Khusus KHT dan Kontrak harian tidak lagi dipotong pada gaji pokok, tetapi ditambah pada potongan HK diatas 
             //==============================================================================================================================
                //filter jumlah hari                
                if($val['idkomponen']==1 and $jumlahh>$pengurang)
                   { 
                    $selisih=$jumlahh-$pengurang;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    $bersih=$selisih-$pengurangkelebihanminggu;
                    //exit("error:".$bersih."___2");
                     $val['jumlah']+=$gajiperhari[$val['karyawanid']]*$bersih;//nilai gajipokok diubah ditambah kelebihan hari
                   } 
                if($val['idkomponen']==1 and $jumlahh<$pengurang)
                   { 
                    $selisih=$pengurang-$jumlahh;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    
                    $bersih=$selisih-$pengurangkelebihanminggu;
                     //exit("error:".$bersih);
                     $val['jumlah']-=$gajiperhari[$val['karyawanid']]*$bersih;//nilai gajipokok diubah dikurang kekurangan hari                   
                   }
                //============================== 
            
             #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$val['jumlah'],
                'pengali'=>1);
              if($val['idkomponen']==1)
            // if($val['idkomponen']==1 or $val['idkomponen']==2 or $val['idkomponen']==31)
             { #ambil,
               #tunjangan jabatan
               #tunjangan masakerja
               #tunjangan Provesi
               #gaji pokok
                  #REVISI SIL SIP HANYA GAPOK SAJA
                 if($nojms[$val['karyawanid']]!=''){#jika No. JMS diisi maka ada potongan jamsostek
                    $tjms[$val['karyawanid']]+=$val['jumlah'];
                 }
             }
          }
         else { #BHL
            //diabaikan yang dari gaji pokok 
             
             #khusus sil sip ambil juga dari gapoknya #update ind
             if($_SESSION['empl']['regional']=='KALIMANTAN'){
                 if($nojms[$val['karyawanid']]!=''){#jika No. JMS diisi maka ada potongan jamsostek
                    $tjms[$val['karyawanid']]+=$val['jumlah'];
                 }
                 
             }
        
           }  
      }
      #ambil karyawan yang pindah lokasi tugas
      $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
          . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
      $qcek=  mysql_query($scek) or die(mysql_error($conn));
      while($rcek=  mysql_fetch_assoc($qcek)){
            $str1 = "select a.*,b.namakaryawan from ".$dbname.".sdm_5gajipokok a left join 
                    ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                    where a.tahun=".substr($tanggal1,0,4)." and a.karyawanid='".$rcek['karyawanid']."' 
                    and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0";
            $qstr=  mysql_query($str1) or die(mysql_error($conn));
            $val=  mysql_fetch_assoc($qstr);
    
            //exit("error".$val['karyawanid']."____".$id[$val['karyawanid']][0]."___".$tipekaryawan[$val['karyawanid']]);
            if($id[$val['karyawanid']][0]==$val['karyawanid'] and ($tipekaryawan[$val['karyawanid']]=='KBL' or $tipekaryawan[$val['karyawanid']]=='Kontrak')){
                if($val['idkomponen']==1 and $jumlahh>$pengurang){ 
                    $selisih=$jumlahh-$pengurang;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    $bersih=$selisih-$pengurangkelebihanminggu;
                     $val['jumlah']+=$gajiperhari[$val['karyawanid']]*$bersih;//nilai gajipokok diubah ditambah kelebihan hari
                } 
                if($val['idkomponen']==1 and $jumlahh<$pengurang){ 
                    $selisih=$pengurang-$jumlahh;
                    $pengurangkelebihanminggu=floor($selisih/7);
                    $bersih=$selisih-$pengurangkelebihanminggu;                     
                    $val['jumlah']-=$gajiperhari[$val['karyawanid']]*$bersih;//nilai gajipokok diubah dikurang kekurangan hari                   
                }
              
                             
              #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$val['jumlah'],
                'pengali'=>1);
            }
    
        }

       $i="select * from ".$dbname.".sdm_pendapatanlaindt where kodeorg='".$param['kodeorg']."' and periodegaji='".$param['periodegaji']."'  ";
        
     /*    $i="select a.periodegaji,a.karyawanid,a.idkomponen,a.jumlah,b.lokasitugas,b.tipekaryawan from ".$dbname.".sdm_pendapatanlaindt a left join"
                . " ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where  b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."'"
                . " and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0"; */
        //exit("error:".$i);
        
       
        
        /* a left join 
        ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
         where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
         and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0                 
         and ".$where2." group by a.karyawanid*/
        
        
        $n = fetchData($i);
        foreach($n as $idx => $val){
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$val['jumlah'],
                'pengali'=>1);
	}
	  
	 // exit("Error:$i");
      
	  
	 #ditaro di bawah 
    /*foreach($tjms as $key=>$nilai){
             #add jamsostek to ready data====================================
           if($tipekaryawan[$key]=='KHT' or $tipekaryawan[$key]=='Kontrak'){ 
             $readyData[] = array(
            'kodeorg'=>$param['kodeorg'],
            'periodegaji'=>$param['periodegaji'],
            'karyawanid'=>$key,
            'idkomponen'=>3,   
            'jumlah'=>($nilai* $persenJms),//'jumlah'=>(($nilai+$tdkdibayar[$key])* $persenJms),
            'pengali'=>1);  
           }
    }  */    
#query sblm di ubah jamhari
/*
 * $query2="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0                 
               and ".$where2." group by a.karyawanid";
 */

#3. Get Lembur Data
if($param['tipe']==3){
    $where2 = " a.kodeorg like '".$param['kodeorg']."%' and (tanggal>='".
        $tglcutblnlalu."' and tanggal<='".$tanggal2."')";
}else{
    $where2 = " a.kodeorg like '".$param['kodeorg']."%' and (tanggal>='".
        $tanggal1."' and tanggal<='".$tanggal2."')";
}
$trig=1;
$query2="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
        ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
         where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
         and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0                 
         and ".$where2." group by a.karyawanid";
     
    $lbrRes = fetchData($query2); 
    foreach($lbrRes as $idx=>$row) {  
            if($trig==1){
                $trig=2;
                #ambil karyawan yang pindah lokasi tugas
                $scek="select a.* from ".$dbname.".setup_temp_lokasitugas a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid "
                    . "where a.kodeorg='".$param['kodeorg']."' and b.tipekaryawan='".$param['tipe']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn));
                while($rcek=  mysql_fetch_assoc($qcek)){
                    $queryL="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
                            ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                             where a.karyawanid='".$rcek['karyawanid']."' 
                             and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0                 
                             and ".$where2." group by a.karyawanid";                    
                    $resQuer=  mysql_query($queryL) or die(mysql_error($conn));
                    $red=  mysql_fetch_assoc($resQuer);
                    if($id[$red['karyawanid']]){
                        $readyData[] = array(
                        'kodeorg'=>$param['kodeorg'],
                        'periodegaji'=>$param['periodegaji'],
                        'karyawanid'=>$red['karyawanid'],
                        'idkomponen'=>33,   
                        'jumlah'=>($red['lembur']),
                        'pengali'=>1); 
                    }
                }
            }
        
          if(isset ($id[$row['karyawanid']]))
          {
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$row['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
             
                $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>33,   
                'jumlah'=>($row['lembur']),
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }
if($param['tipe']==3){
    
}

//                        // Cek promosi
//                        $qPromosi = selectQuery($dbname,'sdm_riwayatjabatan','*',"karyawanid='".$rcek['karyawanid']."' and mulaiberlaku='".$tanggal1."'");
//                        $resPromosi=fetchData($qPromosi);
//                        if ($resPromosi[0]['daritipe']==4 and $resPromosi[0]['ketipekaryawan']==3){
//                            $sGj="select  count(*) as jlh,karyawanid from ".$dbname.".sdm_hktdkdibayar_vw "
//                               . "where karyawanid='".$rcek['karyawanid']."' and tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."' ";
//                        } else {
//                            $sGj="select  count(*) as jlh,karyawanid from ".$dbname.".sdm_hktdkdibayar_vw "
//                               . "where karyawanid='".$rcek['karyawanid']."' and tanggal>='".$tglcutblnlalu."' and tanggal<='".$tanggal2."' ";
//                        }
    
#query sebelum di ubah jamhari
/*$query3="select a.nik as karyawanid,sum(jumlahpotongan) as potongan,tipepotongan from ".$dbname.".sdm_potongandt a left join 
              ".$dbname.".datakaryawan b on a.nik=b.karyawanid
               where b.tipekaryawan in(2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0              
               and ".$where3." group by a.nik,a.tipepotongan";
 * 
 */
     
#4. Get Potongan Data============================================================
    $where3 = " kodeorg='".$param['kodeorg']."' and periodegaji='".
        $param['periodegaji']."'";
    //$query3 = selectQuery($dbname,'sdm_potongandt','nik,sum(jumlahpotongan) as potongan',$where3)." group by nik";
    $query3="select a.nik as karyawanid,sum(jumlahpotongan) as potongan,tipepotongan from ".$dbname.".sdm_potongandt a left join 
              ".$dbname.".datakaryawan b on a.nik=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0              
               and ".$where3." group by a.nik,a.tipepotongan";
    //exit("Error:".$query3);
    $potRes = fetchData($query3);
    foreach($potRes as $idx=>$row) {  
        
          if(isset ($id[$row['karyawanid']]))
          { //'idkomponen'=>18,  
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$row['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>$row['tipepotongan'],   
                'jumlah'=>$row['potongan'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }   
#query sblm di ubah jamhari
/*$query4="select a.karyawanid,a.bulanan,a.jenis from ".$dbname.".sdm_angsuran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.active=1                 
               and ".$where4;
 * 
 */
    
    
#5. Get Angsuran Data==========================================================
    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    //$query4 = selectQuery($dbname,'sdm_angsuran','karyawanid,bulanan,jenis',$where4)." group by karyawanid";
    $query4="select a.karyawanid,a.bulanan,a.jenis from ".$dbname.".sdm_angsuran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.active=1                 
               and ".$where4;
    //exit("Error:".$query4);
    $angRes = fetchData($query4);
    foreach($angRes as $idx=>$row) { 
          if($id[$row['karyawanid']][0]==$row['karyawanid'])
          {
              $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$row['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
           #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>$row['jenis'],
                'jumlah'=>$row['bulanan'],
                'pengali'=>1);
          }
    }

    if($param['tipe']!=3){
#6 Premi dan penalty =======================================================================
    #6.0 periksa posting transaksi
    #posting perawatan
    $stru1="select distinct(tanggal) from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               order by tanggal";
    
    $resu1 = mysql_query($stru1); 
    #posting panen
    $stru2="select distinct(tanggal) from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               order by tanggal";
   $resu2 = mysql_query($stru2); 
    }
   #posting traksi
   $stru3="select distinct(tanggal)
           from ".$dbname.".vhc_runhk_vw a left join 
          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
           where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
           and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
           and posting=0 order by tanggal";
   $resu3 = mysql_query($stru3) or die(mysql_error($conn))."____".$stru3;
   if(mysql_num_rows($resu1)>0 or mysql_num_rows($resu2)>0 or mysql_num_rows($resu3)>0)
   {
       echo"Masih ada data yang belum di posting/There still unconfirmed transaction:";
       echo"<table class=sortable border=0 cellspacing=1>
            <thead><tr class=rowheader>
            <td>".$_SESSION['lang']['jenis']."</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            </tr></thead><tbody>";
       while($bar=mysql_fetch_object($resu1))
       {
           echo"<tr class=rowcontent><td>Perawatan Kebun</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       while($bar=mysql_fetch_object($resu2))
       {
           echo"<tr class=rowcontent><td>Panen</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       while($bar=mysql_fetch_object($resu3))
       {
           echo"<tr class=rowcontent><td>Traksi Pekerjaan</td><td>".tanggalnormal($bar->tanggal)."</td></tr>";
       }
       echo "</tbody><tfoot></tfoot></table>";
      exit();//keluar dari proses
   }
   
    if($param['tipe']==4){
    #6.3.1 Get Premi Kegiatan Perawatan dan gaji pokok BHL
        $premi=Array();
        $penalty=Array();
        $gapokbhl=Array();
        $penaltykehadiran=Array();
        /* $query5="select sum(a.umr) as gaji,a.karyawanid,sum(a.insentif) as premi from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%'
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                group by a.karyawanid"; //update untuk mengakomodasi assitensi melalui inputan perawatan */
		$query5="select distinct karyawanid from ".$dbname.".datakaryawan where (tanggalkeluar>='".$tanggal1."' or tanggalkeluar='0000-00-00') and lokasitugas='".$param['kodeorg']."' and alokasi=0";
        $premRes = fetchData($query5);  
        foreach($premRes as $idx => $val)
        {
			$sgaji="select sum(umr) as gaji,karyawanid,sum(insentif) as premi from ".$dbname.".kebun_kehadiran_vw 
			        where karyawanid='".$val['karyawanid']."' and tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."' ";
			$qGaji=mysql_query($sgaji) or die(mysql_error($conn));
			$rGaji=mysql_fetch_assoc($qGaji);
            if($rGaji['premi']>0)
            $premi[$val['karyawanid']]=$rGaji['premi'];
			#gapok KHL...//BHL->KHT
			  if($tipekaryawan[$val['karyawanid']]=='KHT')//BHL->KHT
			  {   
				  if(empty ($gapokbhl[$val['karyawanid']]))
					  $gapokbhl[$val['karyawanid']]=$rGaji['gaji'];
				  else
					  $gapokbhl[$val['karyawanid']]+=$rGaji['gaji'];  
			  }
        }  
    }
    /*sblm diganti query
     * $query6="select sum(a.upahkerja) as upahkerja,a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,b.tipekaryawan 
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                group by a.karyawanid";
        $premRes1 = fetchData($query6); 
     */
//echo"</pre>";         
    #6.3.2 Get Premi Kegiatan Panen    dan gaji pokok BHL
    
    if($param['tipe']==3){
         $query6="select sum(a.upahkerja) as upahkerja,a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,b.tipekaryawan 
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%' 
               and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."'     
                group by a.karyawanid";
    }else{
         $query6="select sum(a.upahkerja) as upahkerja,a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,b.tipekaryawan 
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$param['kodeorg']."%' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                group by a.karyawanid";
    }
        $premRes1 = fetchData($query6); 
         foreach($premRes1 as $idx => $val)
        {
             $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
             if($val['premi']>0)
             { 
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi']; 
             }
             if($val['penalty']>0)    
                 $penalty[$val['karyawanid']]=$val['penalty'];
       
          #gapok KHL//BHL->KHT
          if($tipekaryawan[$val['karyawanid']]=='KHT')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upahkerja'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upahkerja'];  
          }  
        }         
      /*query sblmnya
       * $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty,b.tipekaryawan 
               from ".$dbname.".vhc_runhk_vw a left join 
              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
               where b.tipekaryawan in(2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and substr(a.notransaksi,1,4)='".$param['kodeorg']."' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                group by a.idkaryawan";
       */  
     #6.3.3 Get Premi Transport dan gaji pokok BHL
        if($param['tipe']==3){
            /*$query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty,b.tipekaryawan 
                   from ".$dbname.".vhc_runhk_vw a left join 
                  ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                   where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and substr(a.notransaksi,1,4)='".$param['kodeorg']."' 
                   and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."'     
                    group by a.idkaryawan";
            */
            if ($_SESSION['empl']['regional']=='SULAWESI'){
                $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty,b.tipekaryawan,b.lokasitugas 
                   from ".$dbname.".vhc_runhk_vw a left join 
                  ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                   where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas in (select kodeunit from 
                    ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."'     
                    group by a.idkaryawan";
            } else {
                $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty,b.tipekaryawan,b.lokasitugas 
                   from ".$dbname.".vhc_runhk_vw a left join 
                  ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                   where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas in (select kodeunit from 
                    ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."'     
                    group by a.idkaryawan";
            }
            
            
        }else{
            /*$query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty,b.tipekaryawan 
               from ".$dbname.".vhc_runhk_vw a left join 
              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and substr(a.notransaksi,1,4)='".$param['kodeorg']."' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                group by a.idkaryawan";*/
            if ($_SESSION['empl']['regional']=='SULAWESI'){
                $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty,b.tipekaryawan,b.lokasitugas
                    from ".$dbname.".vhc_runhk_vw a left join 
                    ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                    where b.tipekaryawan='".$param['tipe']."' and  b.lokasitugas in (select kodeunit from 
                    ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                    and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                    and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                    group by a.idkaryawan";
            } else {
                $query7="select sum(a.upah) as upah,a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty,b.tipekaryawan,b.lokasitugas
                    from ".$dbname.".vhc_runhk_vw a left join 
                    ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
                    where b.tipekaryawan='".$param['tipe']."' and  b.lokasitugas in (select kodeunit from 
                    ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
                    and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                    and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
                    group by a.idkaryawan";
            }            
        }
       // exit("Error:$query7");
        //exit("error:")
        $premRes2 = fetchData($query7); 
        foreach($premRes2 as $idx => $val)
        {
            $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                
              $lok2[$val['karyawanid']]=$val['lokasitugas'];  
                
             if($val['premi']>0){   
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi'];
             }
              if($val['penalty']>0){              
                 if(isset ($penalty[$val['karyawanid']]))
                     $penalty[$val['karyawanid']]+=$val['penalty'];
                 else
                     $penalty[$val['karyawanid']]=$val['penalty'];   
             }
             
           #gapok KHL
          /*   BHL sudah dapat gaji pokok dari absensi, karena vhc_runhk sudah otomatis masuk ke sdm_absensi
          if($tipekaryawan[$val['karyawanid']]=='BHL')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upah'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upah'];  
          } 
           * 
           */
		  
		  if($tipekaryawan[$val['karyawanid']]=='KHT')
          {   
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upah'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upah'];  
          }  
        }  
        /*query sblmnya
         * $query8="select sum(a.premiinput) as premi,a.karyawanid
               from ".$dbname.".kebun_premikemandoran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in (2,3,4,6) and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg='".$param['kodeorg']."'  
               and a.periode like  '%".$param['periodegaji']."%'
               and a.posting=1                   
               group by a.karyawanid";
         */
#6.3.4 Get Premi Kemandoran
        $query8="select sum(a.premiinput) as premi,a.karyawanid
               from ".$dbname.".kebun_premikemandoran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan='".$param['tipe']."' and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg='".$param['kodeorg']."'  
               and a.periode like  '%".$param['periodegaji']."%'
               and a.posting=1                   
               group by a.karyawanid";
		//exit("Error:$query8");   
		//indra
		//	    and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'  
        $premRes2 = fetchData($query8); 
        foreach($premRes2 as $idx => $val){
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$val['karyawanid']."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
             if($val['premi']>0){   
                 if(isset ($premi[$val['karyawanid']]))
                     $premirawat[$val['karyawanid']]+=$val['premi'];
                 else
                     $premirawat[$val['karyawanid']]=$val['premi'];
             }
        }          
        
        
        #gapok BHL dari absensi===================================
        $strup="select a.karyawanid,sum(upah) as upahabsen FROM ".$dbname.".sdm_absensidt_vw a 
               where substr(a.kodeorg,1,4)='".$param['kodeorg']."' 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
               and a.tipekaryawan=4 group by a.karyawanid";
//        $strup="select a.karyawanid,(b.jumlah/25) as upahabsen FROM ".$dbname.".sdm_absensidt_vw a 
//                left join ".$dbname.".sdm_5gajipokok b on a.karyawanid=b.karyawanid and nilaihk=1
//                and b.idkomponen=1 
//               where b.tahun=".substr($tanggal1,0,4)." and substr(a.kodeorg,1,4)='".$param['kodeorg']."' 
//               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' 
//               and a.tipekaryawan=4";
        $resup = fetchData($strup);    
        foreach($resup as $idx => $val)
        {        
              if(empty ($gapokbhl[$val['karyawanid']]))
                  $gapokbhl[$val['karyawanid']]=$val['upahabsen'];
              else
                  $gapokbhl[$val['karyawanid']]+=$val['upahabsen']; 
        }
       #add gapok BHL to ready data
       foreach($gapokbhl as $key=>$val){
             if($val>0) {
                 $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$key,
                    'idkomponen'=>1,#kode komponen gapok
                    'jumlah'=>$val,
                    'pengali'=>1);
               }           
       }
       
               /*query sblmnya
                * $stkh="select a.karyawanid,sum(a.premi) as premi,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                   left join  ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                   where b.tipekaryawan in (2,3,4,6)  and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.kodeorg like '".$param['kodeorg']."%'   
                   and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
                * 
                */
        #premi tetap dari absensi==========================================//insentif di gunakan untuk upah harian yg absen via administrasi personalia>absensi
       if($param['tipe']==3){
            $stkh="select a.karyawanid,sum(a.premi) as premi,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                   left join  ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                   where b.tipekaryawan='".$param['tipe']."'  and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.kodeorg like '".$param['kodeorg']."%'   
                   and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
       }else{
           $stkh="select a.karyawanid,sum(a.premi) as premi,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                   left join  ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                   where b.tipekaryawan='".$param['tipe']."'  and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.kodeorg like '".$param['kodeorg']."%'   
                   and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
       }
            $reskh=mysql_query($stkh);
            while($barky=mysql_fetch_object($reskh)){
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$barky->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                 if(isset ($premi[$barky->karyawanid]))
                     $premi[$barky->karyawanid]+=$barky->premi;
                 else
                     $premi[$barky->karyawanid]=$barky->premi;
            } 
	#end premi tetap dari absensi==========================================
            /*query sbllmnya
             * $stkh1="select a.karyawanid,a.rupiahpremi  from ".$dbname.".kebun_premipanen a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan in(2,3,4,6)  and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$param['kodeorg']."%'   
               and a.periode like  '%".$param['periodegaji']."%' group by a.karyawanid";
             */
            
        #premi pemanen yang dihitung bulanan==========================================
            $stkh1="select a.karyawanid,a.rupiahpremi  from ".$dbname.".kebun_premipanen a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan='".$param['tipe']."'  and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$param['kodeorg']."%'   
               and a.periode like  '%".$param['periodegaji']."%' group by a.karyawanid";
			  //indra
			 //and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";  
	    $reskh1=mysql_query($stkh1);
            while($barky=mysql_fetch_object($reskh1)){
                $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$barky->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                }
                 if(isset ($premi[$barky->karyawanid]))
                     $premi[$barky->karyawanid]+=$barky->rupiahpremi;
                 else
                     $premi[$barky->karyawanid]=$barky->rupiahpremi;
            } 
	#end premi pemanen yang dihitung bulanan==========================================
            
       
            
            
           
            
        #insert    
       foreach($premi as $idx=>$row) { //indra
           #add to ready data================================================
             if($row>0) {//'kodeorg'=>$param['kodeorg'],
                    if($lok2[$idx]!='')
                    {
                        $readyData[] = array(
                           'kodeorg'=>$lok2[$idx],
                           'periodegaji'=>$param['periodegaji'],
                           'karyawanid'=>$idx,
                           'idkomponen'=>32,
                           'jumlah'=>$row,
                           'pengali'=>1); 
                    }
                    else
                    {
                         $readyData[] = array(
                       'kodeorg'=>$param['kodeorg'],
                       'periodegaji'=>$param['periodegaji'],
                       'karyawanid'=>$idx,
                       'idkomponen'=>32,
                       'jumlah'=>$row,
                       'pengali'=>1);
                    }
                
                 }
             } 
             
             
             
             
          foreach($premirawat as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {
                 $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$idx,
                    'idkomponen'=>62,
                    'jumlah'=>$row,
                    'pengali'=>1);
                 }
             }  
         foreach($penalty as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$idx,
                'idkomponen'=>34,
                'jumlah'=>$row,
                'pengali'=>1);
             }
             } 
             /*query sblmnya
              * $stkh="select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan in(2,3,4,6)  and b.lokasitugas='".$param['kodeorg']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$param['kodeorg']."%'   
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
              */
           #penalty kehadiran dari absensi
             if($param['tipe']==3){
                $stkh="select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                    left join 
                  ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                              where b.tipekaryawan='".$param['tipe']."'  and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.kodeorg like '".$param['kodeorg']."%'   
                   and a.tanggal>='".$tglcutblnlalu."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
             }else{
                $stkh="select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran,b.tipekaryawan from ".$dbname.".sdm_absensidt a 
                    left join 
                  ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                              where b.tipekaryawan='".$param['tipe']."'  and b.lokasitugas='".$param['kodeorg']."' 
                   and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
                   and a.kodeorg like '".$param['kodeorg']."%'   
                   and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
             }
            $reskh=mysql_query($stkh);
            while($barkh=mysql_fetch_object($reskh)){
                 $scek="select kodeorg from ".$dbname.".setup_temp_lokasitugas where karyawanid='".$barkh->karyawanid."'";
                $qcek=  mysql_query($scek) or die(mysql_error($conn))."____".$scek;
                $rcek=  mysql_fetch_assoc($qcek);
                if($rcek['kodeorg']!=''){
                    if($rcek['kodeorg']!=$param['kodeorg']){
                        continue;
                    }
                } 
                  if($barkh->penaltykehadiran>0)
                     $penaltykehadiran[$barkh->karyawanid]+=$barkh->penaltykehadiran;
            }
            
         foreach($penaltykehadiran as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$param['kodeorg'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$idx,
                'idkomponen'=>41,
                'jumlah'=>$row,
                'pengali'=>1);
             }
             }  
             
 //calculate to component
       $strx="select id as komponen, case plus when 0 then -1 else plus end as pengali,name as nakomp 
              FROM ".$dbname.".sdm_ho_component";
       $comRes = fetchData($strx); 
       $comp=Array();
       $nakomp=Array();
       foreach($comRes as $idx=>$row){
          $comp[$row['komponen']]=$row['pengali'];
          $nakomp[$row['komponen']]=$row['nakomp'];
       }
       
##======PPh21=================================================================
 //ambil biaya jabatan    
    $jabPersen=0;
    $jabMax=0;
    $str="select persen,max from ".$dbname.".sdm_ho_pph21jabatan";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $jabPersen=$bar->persen/100;
        $jabMax=$bar->max*12;
    }    
    
//Ambil PTKP:
    $ptkp=Array();
    $str="select id,value from ".$dbname.".sdm_ho_pph21_ptkp";
    $res=mysql_query($str);        
    while($bar=mysql_fetch_object($res))
    {
        $ptkp[$bar->id]=$bar->value;
    } 
    
//ambil tarif pph21
  $pphtarif=Array();  
  $pphpercent=Array();  
  $str="select level,percent,upto from ".$dbname.".sdm_ho_pph21_kontribusi order by level";
  $res=mysql_query($str);    
  $urut=0;
  while($bar=mysql_fetch_object($res))
    {
        $pphtarif[$urut]    =$bar->upto;
        $pphpercent[$urut]  =$bar->percent/100;      
        $urut+=1;  
    }       
    
    
  #ambil penghasilan yang kena PPH per karyawan dan masuk ke variable penghasilan=================dan jamsostek juga
       foreach($id as $key=>$val){
           $penghasilan[$val[0]]=0;
           foreach($readyData as $dat=>$bar){
              if($val[0]==$bar['karyawanid'])
              {
                  if($comp[$bar['idkomponen']]==1 or $bar['idkomponen']==37)
				  {
				  #komponen gaji yang plus dikurang potongan hk
				  // if($comp[$bar['idkomponen']]==1 or $bar['idkomponen']==37)#komponen gaji yang plus dikurang potongan hk
						  if($bar['idkomponen']==37){
							  if($bar['jumlah']>0)
							  {
								$bar['jumlah']=$bar['jumlah']*-1;
							  }
							  else
							  {
								$bar['jumlah']=0;
							  }
						  }
					$penghasilan[$val[0]]+=$bar['jumlah']; 
					}
				
				  }
                  
              }  
           }
           
	  
           
           
           
            #########################################################################
            #############################    PPH      ###############################
            #########################################################################
           
           
            $iPph="select * from ".$dbname.".sdm_5komponenpph where status=1 and regional='".$_SESSION['empl']['regional']."' ";
            $nPph=mysql_query($iPph) or die (mysql_error($conn));
            while($dPph=mysql_fetch_assoc($nPph))
            {
                $pphaktif[$dPph['id']]=$dPph['id'];
            }
            
            
            #tarik THR
            
            
             if($param['tipe']=='3')
            {
                $komthr='28';
            }
            else
            {
                $komthr='71';
            }
            
            $iThr="select * from ".$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."' and"
                    . " idkomponen='".$komthr."' ";
            $nThr=mysql_query($iThr) or die (mysql_error($conn));
            while($dThr=mysql_fetch_assoc($nThr))
            {
                 $thr[$dThr['karyawanid']]=$dThr['jumlah'];
            }
            
            
            
           
            
            foreach($thr as $idx=>$row) { 
            #add to ready data================================================
             if($row>0) {
                 $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$idx,
                    'idkomponen'=>$komthr,
                    'jumlah'=>$row,
                    'pengali'=>1);
                 }
             } 
            
            
            //exit("Error:A");
            #bentuk komponen PPH
            foreach($id as $key=>$val)
            {
                $brutopph[$val[0]]=0;
                foreach($readyData as $dat=>$bar)
                {
                    if($val[0]==$bar['karyawanid'])
                    {
                        if($bar['idkomponen']==$pphaktif[$bar['idkomponen']])
                        {
                            //exit("Error:MASUK");
                            $brutopph[$val[0]]+=$bar['jumlah'];    
                        }
                    }
                }
           }
           
         
 
	   
	   $ijkk="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jkk' ";
	   $njkk=mysql_query($ijkk) or die (mysql_error($conn))."____".$ijkk;
	   $djkk=mysql_fetch_assoc($njkk);
	   		$jkk=$djkk['persen'];
		
		$ijht="select persen from ".$dbname.".sdm_ho_pph21jaminan where regional='".$_SESSION['empl']['regional']."' and tipe='jht' ";
		$njht=mysql_query($ijht) or die (mysql_error($conn))."____".$ijht;
		$djht=mysql_fetch_assoc($njht);
	   		$jht=$djht['persen'];	
	   
	
	
    
       
                        
        #$penghasilan di ganti dengan $brutopph 
                        
       if($_SESSION['empl']['regional']=='SULAWESI')
       {    
        #########HIP                                 
        #penghasilah disetahunkan 
        foreach($brutopph as $xid =>$jlh){
            #penghasilan 1thn + jhk 1thn
            $penghasilanSetahun[$xid]=($jlh*12)+($jkk/100*$jlh*12);
            #periksa biaya jabatan=========================== 
            $biayaJab[$xid]=$penghasilanSetahun[$xid]*$jabPersen;
            if($biayaJab[$xid]>$jabMax){#jika lebih dari max maka dibatasi sebesar max
                $biayaJab[$xid]=$jabMax;
            }
            $penghasilanKurangJab[$xid]=$penghasilanSetahun[$xid]-$biayaJab[$xid]-($jht/100*$jlh*12);
            #kurangkan dengan PTKP===============bug done by ind 
            $pkp[$xid]=$penghasilanKurangJab[$xid]-$ptkp[str_replace("K","",$statuspajak[$xid])]; //$satatuspajak ambil dr array pertama di atas	
            $zz=0;
             $sisazz=0;
             if($pkp[$xid]>0){         
             #tahap 1: 
                 if($pkp[$xid]<$pphtarif[0])
                 {//exit("Error:Y");
                     $zz+=$pphpercent[0]*$pkp[$xid];
                     $sisazz=0;
                 }
                 else if($pkp[$xid]>=$pphtarif[0])
                 {
                     $zz+=$pphpercent[0]*$pphtarif[0];
                     $sisazz=$pkp[$xid]-$pphtarif[0];
                     #level 2
                         if($sisazz<($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*$sisazz;
                             $sisazz=0;        
                         }    
                         else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                             $sisazz=$pkp[$xid]-$pphtarif[1]; 
                             #level 3   
                                 if($sisazz<($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*$sisazz;
                                     $sisazz=0;        
                                 }    
                                 else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                                     $sisazz=$pkp[$xid]-$pphtarif[2];
                                      // print_r($sisazz);exit();
                                         if($sisazz>0){
                                         #level 4  sisanya kali 30% 
                                             $zz+=$pphpercent[3]*$sisazz;  
                                         }                          
                                 } 
                         }   

                 }
             }
                #zz adalah PPh Setahun per karyawan
                 $pphSetahun[$xid]=$zz/12;
                 //jika tidak memiliki NPWP maka tambahkan 20% dari PPh yang ada
                 if($npwp[$xid]==''){
                     $pphSetahun[$xid]=$pphSetahun[$xid]+($pphSetahun[$xid]*20/100);
                 }
            }
       }
       else
       {//
        #########SIL SIPPPPPPP                           
        #penghasilah disetahunkan
        foreach($brutopph as $xid =>$jlh){
            
            $brutoPenghasilan[$xid]=$jlh+($jkk/100*$tjms[$xid]);
            
            #nanti disini untuk excep jika kht=biaya jab 0; kbl ada persen jabatan
            if($param['tipe']=='3')
            {   
                $biayaJab[$xid]=$brutoPenghasilan[$xid]*$jabPersen;
            }
            else
            {
                $biayaJab[$xid]=0;
            }
            
            if($biayaJab[$xid]>$jabMax){#jika lebih dari max maka dibatasi sebesar max
                $biayaJab[$xid]=$jabMax;
            }
            
            #netto setelah di setahunkan
            $netto[$xid]=($brutoPenghasilan[$xid]-$biayaJab[$xid]-($jht/100*$tjms[$xid]))*12;
            
            
            #kurangkan dengan PTKP===============bug done by ind 
            $pkp[$xid]=$netto[$xid]-$ptkp[str_replace("K","",$statuspajak[$xid])]; //$satatuspajak ambil dr array pertama di atas	
           
            $pkp[$xid]=1000*(floor($pkp[$xid]/1000));
           
            //print_r($pkp);
            $zz=0;
             $sisazz=0;
             if($pkp[$xid]>0){         
             #tahap 1: 
                 if($pkp[$xid]<$pphtarif[0])
                 {//exit("Error:Y");
                     $zz+=$pphpercent[0]*$pkp[$xid];
                     $sisazz=0;
                 }
                 else if($pkp[$xid]>=$pphtarif[0])
                 {
                     $zz+=$pphpercent[0]*$pphtarif[0];
                     $sisazz=$pkp[$xid]-$pphtarif[0];
                     #level 2
                         if($sisazz<($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*$sisazz;
                             $sisazz=0;        
                         }    
                         else if($sisazz>=($pphtarif[1]-$pphtarif[0]))
                         {
                             $zz+=$pphpercent[1]*($pphtarif[1]-$pphtarif[0]);
                             $sisazz=$pkp[$xid]-$pphtarif[1]; 
                             #level 3   
                                 if($sisazz<($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*$sisazz;
                                     $sisazz=0;        
                                 }    
                                 else if($sisazz>=($pphtarif[2]-$pphtarif[1]))
                                 {
                                     $zz+=$pphpercent[2]*($pphtarif[2]-$pphtarif[1]);
                                     $sisazz=$pkp[$xid]-$pphtarif[2];
                                      // print_r($sisazz);exit();
                                         if($sisazz>0){
                                         #level 4  sisanya kali 30% 
                                             $zz+=$pphpercent[3]*$sisazz;  
                                         }                          
                                 } 
                         }   

                 }
             }
                #zz adalah PPh Setahun per karyawan
                 $pphSetahun[$xid]=$zz/12;
                 //jika tidak memiliki NPWP maka tambahkan 20% dari PPh yang ada
                 /*if($npwp[$xid]==''){ //note : di silsip bulan 6 dianggap punya npwp semua, bln 7 dilepas pak cosa
                     $pphSetahun[$xid]=$pphSetahun[$xid]+($pphSetahun[$xid]*20/100);
                 }*/
            }    
       }
	   
	
        #masukkan pph21 ke array utama
        
       #update indra PPH di pindah ke pembulatan gaji
       #update lagi pph di kembalikan ke proses gaji, karena mencegah gaji 0
       foreach($pphSetahun as $idx=>$row) { 
         #add to ready data================================================
           if($row>0) {             
            $readyData[] = array(
              'kodeorg'=>$param['kodeorg'],
              'periodegaji'=>$param['periodegaji'],
              'karyawanid'=>$idx,
              'idkomponen'=>44,
              'jumlah'=>$row,
              'pengali'=>1);
           }
           }
           
           
		   //print_r($readyData);
		          
##======END PPh21==============================================================  

           /*echo"<pre>";
           print_r($tjms);
	   echo"</pre>";*/
###============================================================BUAT JMS BARU INDRA
    $query6 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawan'");
    $jmsRes = fetchData($query6);
    $persenJms = $jmsRes[0]['value']/100;
	#penghasilan  penambah total sudah dideclarasi dari pph diatas
	if($_SESSION['empl']['regional']=='SULAWESI'){
		foreach($penghasilan as $key=>$nilai){
                    if($nojms[$key]!=''){
                        //if($nilai>0) { 
                     $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$key,
                    'idkomponen'=>3,   
                    'jumlah'=>($nilai*$persenJms),//'jumlah'=>(($nilai+$tdkdibayar[$key])* $persenJms),
                    'pengali'=>1);  
                    }
		}
	}else{
            foreach($tjms as $key=>$nilai){
                if($nojms[$key]!=''){
                 $readyData[] = array(
                    'kodeorg'=>$param['kodeorg'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$key,
                    'idkomponen'=>3,   
                    'jumlah'=>($nilai*$persenJms),//'jumlah'=>(($nilai+$tdkdibayar[$key])* $persenJms),
                    'pengali'=>1);  
                }
            }
	}
        
        
        
	/* foreach($penghasilan as $key=>$nilai){
            if($nojms[$key]!=''){
		//if($nilai>0) { 
             $readyData[] = array(
            'kodeorg'=>$param['kodeorg'],
            'periodegaji'=>$param['periodegaji'],
            'karyawanid'=>$key,
            'idkomponen'=>3,   
            'jumlah'=>($nilai*$persenJms),//'jumlah'=>(($nilai+$tdkdibayar[$key])* $persenJms),
            'pengali'=>1);  
            }
    } 	 */
/*			echo"<pre>";
print_r($nilai*$persenJms);		
echo"</pre>";*/
          // }
##============================================================#END JMS BARU IND
       
        
        
        
        
        
        
        
    
 //sampai sini
   //=tampilan  ============================
           $listbutton="<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>"; 
           $list0 ="<table class=sortable border=0 cellspacing=1>
                     <thead>
                     <tr class=rowheader>";
            $list0 .= "<td>".$_SESSION['lang']['nomor']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['periodegaji']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['nik']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['namakaryawan']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['karyawanid']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['tipe']."</td>";
            $list0.= "<td>".$_SESSION['lang']['jumlah']."</td></tr></thead><tbody>";
            
//periksa gaji minus
    $negatif=false; 
    $list1='';
    $listx = "Masih ada gaji dibawah 0:";
    $list2='';
    $list3='';
    $no=0;
    //ambil premi pengawas di sdm_gaji hanya untuk pemeriksaan minus id komponen 16 untuk tranasport cuti
//    $strsl="select karyawanid,jumlah from ".$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'
//         and kodeorg like '".$param['kodeorg']."%' and idkomponen=16"; 
//    $slRes = fetchData($strsl); 
//   foreach($slRes as $key=>$val)
//   {
//       $premPengawas[$val['karyawanid']]=$val['jumlah'];
//   }     

	
##ready dat

    #ambil lokasitugas masing2 karyawan, karyawanid,lokasitugas
    



        foreach($id as $key=>$val){
           $sisa[$val[0]]=0;
           foreach($readyData as $dat=>$bar){
              if($val[0]==$bar['karyawanid'])
              {
                    if($bar['idkomponen']=='28' or $bar['idkomponen']=='71')
                    {
                    }
                    else
                    {
                        $sisa[$val[0]]+=intval($bar['jumlah']*$comp[$bar['idkomponen']]); 
                    }
              } 
              else
               continue;
           }
           //$sisa[$val[0]]+=$premPengawas[$val[0]];   //if(abs(intval($resVp[0]['total']))>0.01
           if($sisa[$val[0]]<0)
           {$nox+=1;
                $list1 .="<tr class=rowcontent>";
                $list1 .= "<td>".$nox."</td>";
                $list1 .= "<td>".$param['periodegaji']."</td>";
                $list1 .= "<td>".$nikKar[$val[0]]."</td>";
                $list1 .= "<td>".$namakar[$val[0]]."</td>";
                $list1 .= "<td>".$val[0]."</td>";
                $list1 .= "<td>".$tipekaryawan[$val[0]]."</td>";
                $list1 .= "<td>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";                
                $negatif=true;
           } 
           else
           {
               $no+=1; 
                $list2 .="<tr class=rowcontent>";
                $list2 .= "<td>".$no."</td>";
                $list2 .= "<td>".$param['periodegaji']."</td>";
                $list2 .= "<td>".$nikKar[$val[0]]."</td>";
                $list2 .= "<td>".$namakar[$val[0]]."</td>";
                $list2 .= "<td>".$val[0]."</td>";
                $list2 .= "<td>".$tipekaryawan[$val[0]]."</td>";
                $list2 .= "<td align=right>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";  
           }    
       }
     $list3="</tbody><table>";     
     
switch($proses) {
    case 'list':
         if($negatif)
             echo $listx.$list0.$list1.$list3;
         else
             echo $listbutton.$list0.$list2.$list3;
         break;
    case 'post':
        #delete first
        # Insert All ready data
        $insError = "";
        
        $sdel="delete from ".$dbname.".sdm_gaji "
           . " where periodegaji='".$param['periodegaji']."' and kodeorg='".$param['kodeorg']."' and idkomponen not in ('28','71') "
           . " and karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where tipekaryawan='".$param['tipe']."' and lokasitugas='".$param['kodeorg']."')";
       
        //exit("error".$sdel);
        mysql_query($sdel);// or die(mysql_error());
        foreach($readyData as $row) {

            if($row['jumlah']==0 or $row['jumlah']=='' or $row['idkomponen']=='28' or $row['idkomponen']=='71')
            {
                continue;
            }
            else{
                $rt="karyawanid='".$row['karyawanid']."'";
                $optTipe=  makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan', $rt);
           if($optTipe[$row['karyawanid']]==$param['tipe']){
               
                     $queryIns = insertQuery($dbname,'sdm_gaji',$row);
                     if(!mysql_query($queryIns)) {
                         $queryUpd = updateQuery($dbname,'sdm_gaji',$row,
                             "kodeorg='".$row['kodeorg'].
                             "' and periodegaji='".$row['periodegaji'].
                             "' and karyawanid='".$row['karyawanid'].
                             "' and idkomponen=".$row['idkomponen']);
                         $tmpErr = mysql_error();
                         if(!mysql_query($queryUpd)) {
                             echo "DB Insert Error :".$tmpErr."\n";
                             print_r($row);
                             echo "DB Update Error :".mysql_error()."\n";
                         }
                     }
                
                  } 
            }
        }
        break;
    default:
        break;
}
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
              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='B'";
$qCekPeriode=mysql_query($sCekPeriode) or die(mysql_error($conn));
if(mysql_num_rows($qCekPeriode)>0)
    $aktif2=false;
       else
     $aktif2=true;
  if(!$aktif2)
  {
      exit(" Payroll period has been closed");
  }
#periksa apakah sudah tutup buku

       $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and 
             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
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
$qPeriod = selectQuery($dbname,'sdm_5periodegaji','tanggalmulai,tanggalsampai',
    "periode='".$param['periodegaji']."' and kodeorg='".
    $_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");
$resPeriod = fetchData($qPeriod);
$tanggal1 = $resPeriod[0]['tanggalmulai'];
$tanggal2 = $resPeriod[0]['tanggalsampai'];

#2. Get Karyawan bulanan yang penggajian=bulanan dan alokasi=0
$query1 = selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan,jms,statuspajak,npwp',"tipekaryawan in(1,2,6) and ".
    "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and ".
    "(tanggalkeluar>='".$tanggal1."' or tanggalkeluar='0000-00-00') and alokasi=0 and sistemgaji='Bulanan'".
     " and ( tanggalmasuk<='".$tanggal2."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null)");
$absRes = fetchData($query1);
# Error empty karyawan
if(empty($absRes)) {
    echo "Error : There is no prsence(kehadiran) on this period";
    exit();
}
else
{
    $id=Array();
    foreach($absRes as $row => $kar)
    {
      $id[$kar['karyawanid']][]=$kar['karyawanid'];
      $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
      $nojms[$kar['karyawanid']]=trim($kar['jms']);
      $statuspajak[$kar['karyawanid']]=trim($kar['statuspajak']);
      $npwp[$kar['karyawanid']]=trim($kar['npwp']);
    }  
}

#===============================KBL Boleh potong gaji=======================================
    $strgjh = "select a.karyawanid,sum(jumlah)/25 as gjperhari from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.idkomponen in(1,2) and sistemgaji='Bulanan'
               group by a.karyawanid";

    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val)
        {
          $gajiperhari[$val['karyawanid']]=$val['gjperhari'];
        }

 #ambil jumlah hk tidak dibayar untuk KBLdan total tidak dibayar
     $strgjh = "select  count(*) as jlh,b.karyawanid from ".$dbname.".sdm_hktdkdibayar_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'
               group by a.karyawanid";
    $tdkdibayar=Array();
    $resgjh = fetchData($strgjh);
    foreach($resgjh as $idx => $val)
        {
          $tdkdibayar[$val['karyawanid']]=$gajiperhari[$val['karyawanid']]*$val['jlh'];#jumlah tidak dibayar
        //koreksi untuk memindahkan potongan hk dari gaji pokok ke komponen potongan hk
        //seperti yang diterangkan pada escape dibawah  
        $readyData[] = array(
        'kodeorg'=>$_SESSION['empl']['lokasitugas'],
        'periodegaji'=>$param['periodegaji'],
        'karyawanid'=>$val['karyawanid'],
        'idkomponen'=>37,//potongan hk
        'jumlah'=>$tdkdibayar[$val['karyawanid']],
        'pengali'=>1);      
        }
#==================END potongan hk KBL====================================================================

#1ambil semua komponen dari gajipokok=====================
    $str1 = "select a.*,b.namakaryawan,b.tipekaryawan from ".$dbname.".sdm_5gajipokok a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where a.tahun=".substr($tanggal1,0,4)." and b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'";
    $res1 = fetchData($str1);
#2 Get Jamsostek porsi==========================
    $query6 = selectQuery($dbname,'sdm_ho_hr_jms_porsi','value',"id='karyawan'");
    $jmsRes = fetchData($query6);
    $persenJms = $jmsRes[0]['value']/100;
        $tjms=Array();   
        $tipekaryawan=Array();
        foreach($res1 as $idx => $val)
        {
          if($id[$val['karyawanid']][0]==$val['karyawanid'])
          {
              if($val['tipekaryawan']=='2')
                 $tipekaryawan[$val['karyawanid']]='Kontrak';
               else  if($val['tipekaryawan']=='1')
                 $tipekaryawan[$val['karyawanid']]='KBL';
                else 
                 $tipekaryawan[$val['karyawanid']]='Kontrak Karya';
                
             #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$val['karyawanid'],
                'idkomponen'=>$val['idkomponen'],
                'jumlah'=>$val['jumlah'],
                'pengali'=>1);
             if($val['idkomponen']==1 or $val['idkomponen']==2 or $val['idkomponen']==31)
             { #ambil,
               #tunjangan jabatan
               #tunjangan masakerja
               #tunjangan Provesi
               #gaji pokok
                  if($nojms[$val['karyawanid']]!=''){#jika No. JMS diisi maka ada potongan jamsostek
                      $tjms[$val['karyawanid']]+=$val['jumlah']; 
                  }
             }
          }
        }
        
        foreach($tjms as $key=>$nilai){
                 #add jamsostek to ready data====================================
            if($tipekaryawan[$key]=='KBL' or $tipekaryawan[$key]=='Kontrak'){
                 $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$key,
                'idkomponen'=>3,   
                'jumlah'=>($nilai* $persenJms),
                'pengali'=>1);  
            }
        }
        
#3. Get Lembur Data
    $where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".
        $tanggal1."' and tanggal<='".$tanggal2."')";
      $query2="select a.karyawanid,sum(a.uangkelebihanjam) as lembur from ".$dbname.".sdm_lemburdt a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'                  
               and ".$where2." group by a.karyawanid";
    $lbrRes = fetchData($query2); 
    foreach($lbrRes as $idx=>$row) {  
          if(isset ($id[$row['karyawanid']]))
          {
                $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>33,   
                'jumlah'=>$row['lembur'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }

#4. Get Potongan Data============================================================
    $where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".
        $param['periodegaji']."'";
    //$query3 = selectQuery($dbname,'sdm_potongandt','nik,sum(jumlahpotongan) as potongan',$where3)." group by nik";
    $query3="select a.nik as karyawanid,sum(jumlahpotongan) as potongan from ".$dbname.".sdm_potongandt a left join 
              ".$dbname.".datakaryawan b on a.nik=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and sistemgaji='Bulanan'                   
               and ".$where3." group by a.nik";

    $potRes = fetchData($query3);
    foreach($potRes as $idx=>$row) {  
          if(isset ($id[$row['karyawanid']]))
          {
                $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>18,   
                'jumlah'=>$row['potongan'],
                'pengali'=>1); 
          }
          else
          {
            //abaikan jika tidak terdaftar pada karyawanid  
          }   
    }   

#5. Get Angsuran Data==========================================================
    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    //$query4 = selectQuery($dbname,'sdm_angsuran','karyawanid,bulanan,jenis',$where4)." group by karyawanid";
    $query4="select a.karyawanid,a.bulanan,a.jenis from ".$dbname.".sdm_angsuran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.active=1                      
               and sistemgaji='Bulanan'                    
               and ".$where4;

    
    $angRes = fetchData($query4);
    foreach($angRes as $idx=>$row) { 
         if($id[$row['karyawanid']][0]==$row['karyawanid'])
          {

             #add to ready data================================================
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$row['karyawanid'],
                'idkomponen'=>$row['jenis'],
                'jumlah'=>$row['bulanan'],
                'pengali'=>1);
          }
    }
#6 Premi dan penalty =======================================================================
    #6.0 periksa posting transaksi
    #posting perawatan
    $stru1="select distinct(tanggal) from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0 
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'    
               and sistemgaji='Bulanan' order by tanggal";
    $resu1 = mysql_query($stru1); 
    #posting panen
    $stru2="select distinct(tanggal) from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan' order by tanggal";
   $resu2 = mysql_query($stru2); 
   #posting traksi
   $stru3="select distinct(tanggal)
           from ".$dbname.".vhc_runhk_vw a left join 
          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
           where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
           and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
           and posting=0 and sistemgaji='Bulanan' order by tanggal";
   $resu3 = mysql_query($stru3);
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
   

    #6.3.1 Get Premi Kegiatan Perawatan
        $premi=Array();
        $penalty=Array();
        $penaltykehadiran=Array();
        $query5="select a.karyawanid,sum(a.insentif) as premi from ".$dbname.".kebun_kehadiran_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.karyawanid";
        $premRes = fetchData($query5);  
        foreach($premRes as $idx => $val)
        {
          if($val['premi']>0)
            $premi[$val['karyawanid']]=$val['premi'];
        }  
    #6.3.2 Get Premi Kegiatan Panen    
         $query6="select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty 
               from ".$dbname.".kebun_prestasi_vw a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.karyawanid";
        $premRes1 = fetchData($query6); 
         foreach($premRes1 as $idx => $val)
        {
             if($val['premi']>0)
             { 
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi']; 
             }
             if($val['penalty']>0)    
                 $penalty[$val['karyawanid']]=$val['penalty'];
        }         
     #6.3.3 Get Premi Transport
        $query7="select a.idkaryawan as karyawanid,sum(a.premi) as premi,sum(a.penalty) as penalty 
               from ".$dbname.".vhc_runhk_vw a left join 
              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and sistemgaji='Bulanan'                    
               group by a.idkaryawan";
        $premRes2 = fetchData($query7); 
         foreach($premRes2 as $idx => $val)
        {
             if($val['premi']>0)
             {   
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi'];
             }
              if($val['penalty']>0)
             {              
                 if(isset ($penalty[$val['karyawanid']]))
                     $penalty[$val['karyawanid']]+=$val['penalty'];
                 else
                     $penalty[$val['karyawanid']]=$val['penalty'];   
             }
        }  
#6.3.4 Get Premi Kemandoran
        $query8="select sum(a.premiinput) as premi,a.karyawanid
               from ".$dbname.".kebun_premikemandoran a left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'     
               and b.sistemgaji='Bulanan'  and a.posting=1                   
               group by a.karyawanid";
        $premRes2 = fetchData($query8); 
         foreach($premRes2 as $idx => $val)
        {
             if($val['premi']>0)
             {   
                 if(isset ($premi[$val['karyawanid']]))
                     $premi[$val['karyawanid']]+=$val['premi'];
                 else
                     $premi[$val['karyawanid']]=$val['premi'];
             }
        }  
          #premi tetap dari absensi==========================================
            $stkh="select a.karyawanid,sum(a.premi+a.insentif) as premi from ".$dbname.".sdm_absensidt a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan in(1,2,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Bulanan'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
            $reskh=mysql_query($stkh);
            while($barky=mysql_fetch_object($reskh)){
                 if(isset ($premi[$barky->karyawanid]))
                     $premi[$barky->karyawanid]+=$barky->premi;
                 else
                     $premi[$barky->karyawanid]=$barky->premi;
            }
       #end premi tetap dari absensi========================================== 
             
        #premi pemanen yang dihitung bulanan==========================================
            $stkh1="select a.karyawanid,b.rupiahpremi  from ".$dbname.".kebun_premipanen a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan in(1,2,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%'   
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
            $reskh1=mysql_query($stkh1);
            while($barky=mysql_fetch_object($reskh1)){
                 if(isset ($premi[$barky->karyawanid]))
                     $premi[$barky->karyawanid]+=$barky->rupiahpremi;
                 else
                     $premi[$barky->karyawanid]=$barky->rupiahpremi;
            } 
	#end premi pemanen yang dihitung bulanan==========================================
            
                  
        
         foreach($premi as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {
                 $readyData[] = array(
                    'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                    'periodegaji'=>$param['periodegaji'],
                    'karyawanid'=>$idx,
                    'idkomponen'=>32,
                    'jumlah'=>$row,
                    'pengali'=>1);
                 }
             }    
         foreach($penalty as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
                'periodegaji'=>$param['periodegaji'],
                'karyawanid'=>$idx,
                'idkomponen'=>34,
                'jumlah'=>$row,
                'pengali'=>1);
             }
             } 
           #penalty kehadiran dari absensi
            $stkh="select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran from ".$dbname.".sdm_absensidt a 
                left join 
              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
                          where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar='0000-00-00') and b.alokasi=0
               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Bulanan'  
               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
            $reskh=mysql_query($stkh);
            while($barkh=mysql_fetch_object($reskh)){
                  if($barkh->penaltykehadiran>0)
                     $penaltykehadiran[$barkh->karyawanid]=$barkh->penaltykehadiran;
            }
         foreach($penaltykehadiran as $idx=>$row) { 
           #add to ready data================================================
             if($row>0) {             
              $readyData[] = array(
                'kodeorg'=>$_SESSION['empl']['lokasitugas'],
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
  #ambil penghasilan yang kena PPH per karyawan dan masuk ke variable penghasilan=================
       foreach($id as $key=>$val){
           $penghasilan[$val[0]]=0;
           foreach($readyData as $dat=>$bar){
              if($val[0]==$bar['karyawanid'])
              {
                  if($comp[$bar['idkomponen']]==1 or $bar['idkomponen']==37)#komponen gaji yang plus dikurang potongan hk
                   $penghasilan[$val[0]]+=$bar['jumlah']; 
              }  
           }
       }
 #penghasilah disetahunkan
       foreach($penghasilan as $xid =>$jlh){
           $penghasilanSetahun[$xid]=$jlh*12;
           #periksa biaya jabatan=========================== 
           $biayaJab[$xid]=$penghasilanSetahun[$xid]*$jabPersen;
           if($biayaJab[$xid]>$jabMax){#jika lebih dari max maka dibatasi sebesar max
               $biayaJab[$xid]=$jabMax;
           }
           $penghasilanKurangJab[$xid]=$penghasilanSetahun[$xid]-$biayaJab[$xid];
           #kurangkan dengan PTKP===============
           $pkp[$xid]=$penghasilanKurangJab[$xid]-$ptkp[str_replace("K","",$kamusKar[$bar->karyawanid]['status'])];       
    
           
           $zz=0;
            $sisazz=0;
            if($pkp[$xid]>0){         
            #tahap 1: 
                if($pkp[$xid]<$pphtarif[0])
                {
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
 #masukkan pph21 ke array utama
        foreach($pphSetahun as $idx=>$row) { 
         #add to ready data================================================
           if($row>0) {             
            $readyData[] = array(
              'kodeorg'=>$_SESSION['empl']['lokasitugas'],
              'periodegaji'=>$param['periodegaji'],
              'karyawanid'=>$idx,
              'idkomponen'=>44,
              'jumlah'=>$row,
              'pengali'=>1);
           }
           }      
##======END PPh21==============================================================  
       
   //=tampilan  ============================
           $listbutton="<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>"; 
           $list0 ="<table class=sortable border=0 cellspacing=1>
                     <thead>
                     <tr class=rowheader>";
            $list0 .= "<td>".$_SESSION['lang']['nomor']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['periodegaji']."</td>";
            $list0 .= "<td>".$_SESSION['lang']['karyawanid']."</td>";
            $list0.= "<td>".$_SESSION['lang']['jumlah']."</td></tr></thead><tbody>";
            
//periksa gaji minus
    $negatif=false; 
    $list1='';
     $listx = "Masih ada gaji dibawah 0:";    
    $list2='';
    $list3='';
    $no=0;
    //ambil premi pengawas di sdm_gaji
    $strsl="select karyawanid,jumlah from ".$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'
         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=16"; 
    $slRes = fetchData($strsl); 
   foreach($slRes as $key=>$val)
   {
       $premPengawas[$val['karyawanid']]=$val['jumlah'];
   } 
  
       foreach($id as $key=>$val){
           $sisa[$val[0]]=0;
           foreach($readyData as $dat=>$bar){
              if($val[0]==$bar['karyawanid'])
              {
                  $sisa[$val[0]]+=$bar['jumlah']*$comp[$bar['idkomponen']]; 
              }  
              continue;
           }
           $sisa[$val[0]]+=$premPengawas[$val[0]]; //ditambahkan pada tampilan hanya sekali
           
           if($sisa[$val[0]]<0)
           {
                $list1 .="<tr class=rowcontent>";
                $list1 .= "<td>-</td>";
                $list1 .= "<td>".$param['periodegaji']."</td>";
                $list1 .= "<td>".$namakar[$val[0]]."</td>";
                $list1 .= "<td>".number_format($sisa[$val[0]],0,',','.')."</td></tr>";                
                $negatif=true;
           
                
           } 
           else
           {
               $no+=1; 
                $list2 .="<tr class=rowcontent>";
                $list2 .= "<td>".$no."</td>";
                $list2 .= "<td>".$param['periodegaji']."</td>";
                $list2 .= "<td>".$namakar[$val[0]]."</td>";
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
        # Insert All ready data
        $insError = "";
        foreach($readyData as $row) {
            if($row['jumlah']==0 or $row['jumlah']=='')
            {
                continue;
            }
            else{
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
                    echo "DB Update Error :".mysql_error()."\n";
                }
            }
            }  
        }
        break;
    default:
        break;
}
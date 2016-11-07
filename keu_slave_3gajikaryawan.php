<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');
#==========================================konfigurasi database
# KBNB0	Gaji BTL Kebun/Pabrik
# KBNB1	Premi/Lebur BTL Kebun/Pabrik
# KBNB2	Tunjangan Lain
# KBNB3	THR BTL
# KBNB4	Bonus BTL
# KBNB5	Pengobatan BTL
# VHCG0	Gaji Kendaraan/A.Berat
# VHCG1	Biaya Lebur Kendaraan/A.Berat
# VHCG2	Biaya Tunjangan Lain Kend./A.Berat
# VHCG3	THR Kend./A.Berat
# VHCG4	Bonus Kend. A.Berat
# VHCG5	Pengobatan Kend./A.Berat
# WSG0	Biaya Gaji Bengkel
# WSG1	Biaya Premi/Lembur Bengkel
# WSG2	Tunjangan Lain Bengkel
# WSG3	THR Traksi
# WSG4	Bonus Traksi
# WSG5	Pengobatan Traksi
# KBNL0	Biaya pengawasan BBT
# KBNL1	Biaya pengawasan TBM
# KBNL2	Biaya pengawasan TM
# KBNL3	Biaya Pengawasan Panen
#============================================konfigurasi database

#==Komfigurasi komponen gaji
# 1	Gaji Pokok
# 2	Tunjangan Jabatan
# 14	Rapel
# 16	Premi Pengawasan
# 21	Klaim Pengobatan
# 26	Bonus
# 27	Tunjangan Fasilitas
# 28	THR
# 30	Tunjangan Profesi
# 31	Tunjangan Masa Kerja
# 32	Premi
# 33	Lembur
# 34	Penalti
#

$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#ambil periode akuntansi
$str="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
    where kodeorg='".$_SESSION['empl']['lokasitugas']."'
    and periode='".$param['periode']."'";
$tgmulai='';
$tgsampai='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $tgsampai   = $bar->tanggalsampai;
    $tgmulai    = $bar->tanggalmulai;
}
if($tgmulai=='' || $tgsampai=='')
    exit("Error: Accounting period is not registered");

#---------------------------------------------------------------
#ambil potongan HK
#---------------------------------------------------------------
 $str="select sum(jumlah) as jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and idkomponen in(37,41) and periodegaji='".$param['periode']."' group by idkomponen,karyawanid";
 $resx=  mysql_query($str);
 $potx=Array();
 while($barx=mysql_fetch_object($resx))
 {
     $potx[$barx->karyawanid]=$barx->jumlah;
 }
#---------------------------------------------------------------
#ambil semua gaji per karyawan
#---------------------------------------------------------------
#1. Ambil gaji total per karyawan pada unit bersangkutan
 $str="select jumlah,idkomponen,karyawanid from ".$dbname.".sdm_gajidetail_vw 
       where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and plus=1 and periodegaji='".$param['periode']."'";
 $res=  mysql_query($str);
 $gaji=Array();
 while($bar=mysql_fetch_object($res))
 {
     if($bar->idkomponen==1) 
        $gaji[$bar->karyawanid][$bar->idkomponen]=$bar->jumlah-$potx[$bar->karyawanid];//dikurangkan dengan potongan HK
     else
        $gaji[$bar->karyawanid][$bar->idkomponen]=$bar->jumlah;
 }
 #2 Ambil subunit setiap karyawan
 $str="select subbagian,karyawanid,namakaryawan from ".$dbname.".datakaryawan 
       where lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
 $res=mysql_query($str);
 $subunit=Array();
 while($bar=mysql_fetch_object($res))
 {
     $subunit[$bar->karyawanid]=$bar->subbagian;
     $namakaryawan[$bar->karyawanid]=$bar->namakaryawan;
     
 }
 #3 ambil semua organisasi yang traksi atau workshop
 $str="select distinct kodeorganisasi,tipe from ".$dbname.".organisasi 
       where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
 $res=mysql_query($str);
 $tipe=Array();
 while($bar=mysql_fetch_object($res))
 {
     $tipe[$bar->kodeorganisasi]=$bar->tipe;
     
 } 

  #==========================================================================================  
   $GJ=$gaji;
   #buang karyawan yang gajinya sudah teralokasi
 
    $str="select karyawanid from ".$dbname.".kebun_kehadiran_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
      unset($gaji[$bar->karyawanid]);
    }
    #buang karyawan yang tanggalmasuknya > dari tanggal akhir periode
    $str1="select karyawanid from ".$dbname.".datakaryawan where 
           lokasitugas='".$_SESSION['empl']['lokasitugas']."'
           and tanggalmasuk>'".$tgsampai."'";
    $res1=mysql_query($str1);
    while($bar1=mysql_fetch_object($res1))
    {
        unset($gaji[$bar1->karyawanid]);
    }    
  #b. ambil prestasi kebun
    $str="select karyawanid from ".$dbname.".kebun_prestasi_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
       unset($gaji[$bar->karyawanid]);
    }

 #==========================================================================================
    #ambil kendaraan atau mesin yang menempel pada orang
    $str="select vhc,karyawanid from ".$dbname.".vhc_5operator";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $ken[$bar->karyawanid]=$bar->vhc;
    }
 #ambil komponen gaji
     $str="select id,name from ".$dbname.".sdm_ho_component";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $komponen[$bar->id]=$bar->id;
        $namakomponen[$bar->id]=$bar->name;
    }   
    

 #ambil gaji yang sudah teralokasi per karyawan
 #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  #a.kehadiran kebun
    $str="select sum(umr) as umr, sum(insentif) as insentif,karyawanid from ".$dbname.".kebun_kehadiran_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $potongan[$bar->karyawanid][1]+=$bar->umr;//potongan gaji pokok
        $potongan[$bar->karyawanid][32]+=$bar->insentif; //potongan premi    
    }
  #b. ambil prestasi kebun
    $str="select sum(upahkerja) as umr, sum(upahpremi) as insentif,sum(rupiahpenalty) as penalty,
          karyawanid from ".$dbname.".kebun_prestasi_vw
          where tanggal>='".$tgmulai."' and tanggal <='".$tgsampai."' 
          and unit='".$_SESSION['empl']['lokasitugas']."' and jurnal=1 group by karyawanid";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $potongan[$bar->karyawanid][1]+=$bar->umr-$bar->penalty;//potongan gaji pokok
        $potongan[$bar->karyawanid][32]+=$bar->insentif; //potongan premi 
    }    
 #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 #kurangkan gaji yang ada dengan yang sudah dialokasi
    $gajiblmalokasi=$GJ;
    foreach($GJ as $key=>$row)
    {
       $gajiblmalokasi[$key][1]-= $potongan[$key][1];
       $gajiblmalokasi[$key][32]-= $potongan[$key][32];
    }
 #ambil selisih kekurangan 
    $kekurangan=0;
    foreach($gajiblmalokasi as $key)
    { 
      foreach($key as $row=>$cell)
      {
        if($cell<0)
            $kekurangan+=$cell;
      }
    }

  #=======================================================================================================  
    
   #echo $kekurangan; Buang escape ini untuk mengetahui selisih gaji yang belum teralokasi 
 if(empty($gaji))
     exit('Error: No Salary data found');
 else {

     
       
             echo"<button class=mybutton onclick=prosesGaji(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>".$_SESSION['lang']['periode']."</td>
                    <td>".$_SESSION['lang']['employeename']."</td>
                    <td>".$_SESSION['lang']['karyawanid']."</td>
                    <td>".$_SESSION['lang']['idkomponen']."</td>
                    <td>".$_SESSION['lang']['nama']."</td>
                    <td>".$_SESSION['lang']['subbagian']."</td>
                    <td>".$_SESSION['lang']['tipe']."</td>
                    <td>".$_SESSION['lang']['kendaraan']."</td>
                    <td>".$_SESSION['lang']['jumlah']."</td>
                    </tr>
                  </thead>
                  <tbody>";

             $no=0;
            foreach($gaji as $key =>$baris)
             { 
                foreach ($baris as $val=>$jlh)
                { 
                $no+=1;
                 echo"<tr class=rowcontent id='row".$no."'>
                    <td>".$no."</td>
                    <td id='periode".$no."'>".$_POST['periode']."</td>
                    <td id='namakaryawan".$no."'>".$namakaryawan[$key]."</td>
                    <td id='karyawanid".$no."'>".$key."</td>    
                    <td id='komponen".$no."'>".$val."</td>
                    <td id='namakomponen".$no."'>".$namakomponen[$val]."</td>
                    <td id='subbagian".$no."'>".$subunit[$key]."</td>
                    <td id='tipeorganisasi".$no."'>".$tipe[$subunit[$key]]."</td>                        
                    <td id='mesin".$no."'>".$ken[$key]."</td>
                    <td align=right id='jumlah".$no."'>".$jlh."</td>
                    </tr>";
                 $ttl+=$jlh;
                }
             }
            echo"<tr class=rowcontent id='row".$no."'>
                    <td colspan=9>Total</td>
                    <td align=right>".number_format($ttl)."</td>
                    </tr>";
             echo"</tbody><tfoot></tfoot></table>";

}
#----------------------------------------------------------------
?>
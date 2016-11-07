<?php
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

#default Message:
$mess="<html>
               <head>
               </head>
               <body>
               Hardaya Plantations Group Preventive Maintenance, remind you for the folowing task:<br>";

## HEAD OFFICE ##

##  email ulang tahun:============================================================
$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='H0HO'";
$res1=mysql_query($str);
$to1="";
while($bar=mysql_fetch_object($res1)){
    $to1.=$bar->nilai;
}
             
$str1="select karyawanid,namakaryawan,lokasitugas,email from ".$dbname.".datakaryawan
	 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".date('Ymd').")  and tanggallahir like '%".date('m-d')."'
         and lokasitugas IN ('H0HO','L0HO','P0HO') and tipekaryawan =0";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
        $mess2="<html>
               <head>
               </head>
               <body>";
        $subject2="Selamat Ulang Tahun !";
        $mess2.="<br>Dear ".$bar1->namakaryawan.",<br><br>
                        Waktu berjalan tiada henti<br> 
                        mengiringi rembulan dan mentari yang terbit nan tenggelam setiap hari<br>
                        mengiringi usiamu yang terus bertambah dari hari ke hari<br>
                        hingga saat ini..<br><br>

                        Selamat ulang tahun ".$bar1->namakaryawan."
                        Sungguh masa depan itu memang ada<br> 
                        karena kau telah berhasil melewati satu 1 tahun lagi masa usiamu.<br><br>

                        Semoga dengan bertambahnya usia menjadikan ".$bar1->namakaryawan." insan yang mulia,<br>
                        semakin bijak dan menjadi berkah bagi lingkungan kehidupan saudara dan 
                        semakin berprestasi dan berkarya di Hardaya Plantations Group.<br><br>

                        Kami segenap Direksi dan Karyawan Hardaya Plantations Group mengucapkan SELAMAT ULANG TAHUN<br>
                        Panjang Umur dan Bahagia selalu dalam hidupmu.<br><br><br><br>
                        
                        Hardaya Plantations Group</body></html>";
       if($bar1->email!==''){
           $to2=$to1.",".$bar1->email;
       } else {
           $to2=$to1;
       }
      if($to2!=''){
          $kirim=kirimEmail($to2,$subject2,$mess2);#this has return but disobeying;            
      }      
      $mess1="<html>
               <head>
               </head>
               <body>";
}    


##  email  reminder probation:============================================================
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')-76),date('Y'));//105 dianggap 3 bulan kurang 2 minggu
$tanggalmasuk=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where  tanggalmasuk='".$tanggalmasuk."' and lokasitugas IN ('H0HO','L0HO','P0HO','H0RO','L0RO','P0RO') and tipekaryawan =0";
//echo $str1;
$res=mysql_query($str1);
$mess3="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject3=" Reminder Akhir Masa Percobaan Karyawan Baru (On: ".date('d-m-Y H:i:s').")";
                    $mess3.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa percobannya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess3.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td><td>".$bar->subbagian."</td>
                                    </tr>";   
               } 
             $mess3.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to1!=''){ 
               $kirim=kirimEmail($to1,$subject3,$mess3);#this has return but disobeying;     
        }    
}    


##  email  reminder akhir kontrak karyawan:============================================================
             $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='KONTRAK' and kodeorg='H0HO'";
             $res2=mysql_query($str);
             while($bar=mysql_fetch_object($res2)){
                 $to2=$bar->nilai;
             }
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')+14),date('Y'));
$tanggalkeluar=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where  tanggalkeluar='".$tanggalkeluar."' and lokasitugas IN ('H0HO','L0HO','P0HO') and tipekaryawan in(6,2)";
$res=mysql_query($str1);
$mess4="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject4=" Reminder Akhir Masa Kontrak Karyawan (On: ".date('d-m-Y H:i:s').")";
                    $mess4.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa kontraknya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         <td>Akhir.Kontrak</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess4.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td>
                                  <td>".$bar->subbagian."</td>
                                  <td>".tanggalnormal($bar->tanggalkeluar)."</td>    
                                    </tr>";   
               } 
             $mess4.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to2!=''){ 
                $kirim=kirimEmail($to2,$subject4,$mess4);#this has return but disobeying;     
        }    
}    


##  email  masa akhir cuti tahunan:============================================================
$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='H0HO'";
$res2=mysql_query($str);
$toCuti="";
while($bar=mysql_fetch_object($res2)){
    $toCuti.=$bar->nilai;
}
$mess4='';
//penentuan tanggal 60 hari lagi
$t=mktime(0,0,0,date('m'),(date('d')+60),date('Y'));
$tanggalakhir=date('Y-m-d',$t);
$tglll=date('m-d',$t);
$tmasuk=date('Y',$t);
$tmasuk=$tmasuk-1;# tahun masuk harus lebih kecil dari atau sama dengan 2 tahun sebelumnya 
$tnorm=date('d-m-Y',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where  tanggalmasuk like '%".$tglll."'  and tipekaryawan in(1,2,3) and left(tanggalmasuk,4)<=".$tmasuk
            ." and lokasitugas IN ('H0HO','L0HO','P0HO') and (tanggalkeluar='0000-00-00' or tanggalkeluar>'".$tanggalakhir."')"; 
$res=mysql_query($str1);
$mess4="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject4=" Reminder akhir masa cuti tahunan karyawan [".date('d-m-Y H:i:s')."]";
                    $mess4.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa cuti tahunan:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         <td>Akhir.Cuti</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess4.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td>
                                  <td>".$bar->subbagian."</td>
                                  <td>".$tnorm."</td>    
                                    </tr>";   
               } 
               
             $mess4.="</tbody><tfoot></tfoot></table>";
             $mess4.="<br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($toCuti!=''){ 
               $kirim=kirimEmail($toCuti,$subject4,$mess4);#this has return but disobeying;     
        }    
}    

##  email  masa akhir cuti besar:============================================================
$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='H0HO'";
$res2=mysql_query($str);
$toCuti="";
while($bar=mysql_fetch_object($res2)){
    $toCuti.=$bar->nilai;
}
$mess4='';
//penentuan tanggal 60 hari lagi
$t=mktime(0,0,0,date('m'),(date('d')+60),date('Y'));
$tanggalakhir=date('Y-m-d',$t);
$tnorm=date('d-m-Y',$t);

$t=mktime(0,0,0,date('m'),(date('d')-2130),date('Y'));#tanggal masuk adalah 6 tahun lalu
$masuknya1=date('Y-m-d',$t);
$t=mktime(0,0,0,date('m'),(date('d')-4320),date('Y'));#tanggal masuk adalah 12 tahun lalu
$masuknya2=date('Y-m-d',$t);
$t=mktime(0,0,0,date('m'),(date('d')-6510),date('Y'));#tanggal masuk adalah 18 tahun lalu
$masuknya3=date('Y-m-d',$t);
$t=mktime(0,0,0,date('m'),(date('d')-8700),date('Y'));#tanggal masuk adalah 24 tahun lalu
$masuknya4=date('Y-m-d',$t);
$t=mktime(0,0,0,date('m'),(date('d')-10890),date('Y'));#tanggal masuk adalah 30tahun lalu
$masuknya5=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,tanggalkeluar,lokasitugas,subbagian from ".$dbname.".datakaryawan
             where  tanggalmasuk in('".$masuknya1."','".$masuknya2."','".$masuknya3."','".$masuknya4."','".$masuknya5."')  
             and lokasitugas IN ('H0HO','L0HO','P0HO') and tipekaryawan in(1,2,3)  and (tanggalkeluar='0000-00-00' or tanggalkeluar>'".$tanggalakhir."')"; 
$res=mysql_query($str1);
$mess4="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject4=" Reminder akhir masa cuti panjang karyawan [".date('d-m-Y H:i:s')."]";
                    $mess4.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa cuti panjang:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         <td>Akhir.Cuti</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess4.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td>
                                  <td>".$bar->subbagian."</td>
                                  <td>".$tnorm."</td>    
                                    </tr>";   
               } 

             $mess4.="</tbody><tfoot></tfoot></table>";
             $mess4.="<br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($toCuti!=''){ 
                $kirim=kirimEmail($toCuti,$subject4,$mess4);#this has return but disobeying;     
        }    
}   

##  email  keluarga 21th yg masih tanggungan:============================================================
$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='H0HO'";
$res2=mysql_query($str);
$toCuti="";
while($bar=mysql_fetch_object($res2)){
    $toCuti.=$bar->nilai;
}
$mess4='';
$str1="select a.namakaryawan, a.lokasitugas,b.nama,b.hubungankeluarga from ".$dbname.".datakaryawan a left join
           ".$dbname.".sdm_karyawankeluarga b on a.karyawanid=b.karyawanid where 
             COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',b.tanggallahir)/365.25,1),0)>20.9
             and lokasitugas in (select kodeunit from bgt_regional_assignment where kodeunit NOT LIKE '%HO' 
             and regional in (select regional from bgt_regional_assignment where kodeunit='H0RO')) and b.tanggungan=1 and b.hubungankeluarga='Anak'"; 
$res=mysql_query($str1);

$mess4="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject4=" Reminder karyawan 21th [".date('d-m-Y H:i:s')."]";
                    $mess4.="Dear Hrd,<br>Berikut data karyawan yang harus diupdate berkaitan dengan umur tanggungan sudah 21 Th:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>Lokasi Tugas</td>
                         <td>Nama Tanggungan</td>
                         <td>Hubungan Keluarga</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess4.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".$bar->lokasitugas."</td>
                                  <td>".$bar->nama."</td>
                                  <td>".$bar->hubungankeluarga."</td>
                                    </tr>";   
               } 

             $mess4.="</tbody><tfoot></tfoot></table>";
             $mess4.="<br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($toCuti!=''){ 
               $kirim=kirimEmail($toCuti,$subject4,$mess4);#this has return but disobeying;     
        }    
}


## CENTRAL MODO (HIP) ##

$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='H0RO'";
$res1=mysql_query($str);
$to1="";
while($bar=mysql_fetch_object($res1)){
    $to1.=$bar->nilai;
}
$str1="select karyawanid,namakaryawan,lokasitugas,email from ".$dbname.".datakaryawan
	 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".date('Ymd').")  and tanggallahir like '%".date('m-d')."'
         AND lokasitugas NOT LIKE '%HO' AND lokasitugas IN (SELECT kodeunit FROM ".$dbname.".bgt_regional_assignment WHERE regional='SULAWESI')
         and tipekaryawan =0";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
        $mess2="<html>
               <head>
               </head>
               <body>";
        $subject2="Selamat Ulang Tahun !";
        $mess2.="<br>Dear ".$bar1->namakaryawan.",<br><br>
                        Waktu berjalan tiada henti<br> 
                        mengiringi rembulan dan mentari yang terbit nan tenggelam setiap hari<br>
                        mengiringi usiamu yang terus bertambah dari hari ke hari<br>
                        hingga saat ini..<br><br>

                        Selamat ulang tahun ".$bar1->namakaryawan."
                        Sungguh masa depan itu memang ada<br> 
                        karena kau telah berhasil melewati satu 1 tahun lagi masa usiamu.<br><br>

                        Semoga dengan bertambahnya usia menjadikan ".$bar1->namakaryawan." insan yang mulia,<br>
                        semakin bijak dan menjadi berkah bagi lingkungan kehidupan saudara dan 
                        semakin berprestasi dan berkarya di Hardaya Plantations Group.<br><br>

                        Kami segenap Direksi dan Karyawan Hardaya Plantations Group mengucapkan SELAMAT ULANG TAHUN<br>
                        Panjang Umur dan Bahagia selalu dalam hidupmu.<br><br><br><br>
                        
                        Hardaya Plantations Group</body></html>";
       if($bar1->email!==''){
           $to2=$to1.",".$bar1->email;
       } else {
           $to2=$to1;
       }
      if($to2!=''){
          $kirim=kirimEmail($to2,$subject2,$mess2);#this has return but disobeying;            
      }      
      $mess1="<html>
               <head>
               </head>
               <body>";
}    


##  email  reminder probation:============================================================
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')-76),date('Y'));//105 dianggap 3 bulan kurang 2 minggu
$tanggalmasuk=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where tanggalmasuk='".$tanggalmasuk."' AND lokasitugas NOT LIKE '%HO'
               AND lokasitugas IN (SELECT kodeunit FROM ".$dbname.".bgt_regional_assignment WHERE regional='SULAWESI') 
               and tipekaryawan =0";
//echo $str1;
$res=mysql_query($str1);
$mess3="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject3=" Reminder Akhir Masa Percobaan Karyawan Baru Kebun Modo (On: ".date('d-m-Y H:i:s').")";
                    $mess3.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa percobannya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess3.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td><td>".$bar->subbagian."</td>
                                    </tr>";   
               } 
             $mess3.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to1!=''){ 
               $kirim=kirimEmail($to1,$subject3,$mess3);#this has return but disobeying;     
        }    
}    

## CENTRAL SEBAKIS (SIL DAN SIP) ##

$str="select nilai from ".$dbname.".setup_parameterappl where kodeaplikasi='X3' and kodeparameter='RCUTI' and kodeorg='L0RO'";
$res1=mysql_query($str);
$to1="";
while($bar=mysql_fetch_object($res1)){
    $to1.=$bar->nilai;
}
$str1="select karyawanid,namakaryawan,lokasitugas,email from ".$dbname.".datakaryawan
	 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".date('Ymd').")  and tanggallahir like '%".date('m-d')."'
         AND lokasitugas NOT LIKE '%HO' AND lokasitugas IN (SELECT kodeunit FROM ".$dbname.".bgt_regional_assignment WHERE regional='KALIMANTAN')
         and tipekaryawan =0";
$res1=mysql_query($str1);
while($bar1=mysql_fetch_object($res1))
{
        $mess2="<html>
               <head>
               </head>
               <body>";
        $subject2="Selamat Ulang Tahun !";
        $mess2.="<br>Dear ".$bar1->namakaryawan.",<br><br>
                        Waktu berjalan tiada henti<br> 
                        mengiringi rembulan dan mentari yang terbit nan tenggelam setiap hari<br>
                        mengiringi usiamu yang terus bertambah dari hari ke hari<br>
                        hingga saat ini..<br><br>

                        Selamat ulang tahun ".$bar1->namakaryawan."
                        Sungguh masa depan itu memang ada<br> 
                        karena kau telah berhasil melewati satu 1 tahun lagi masa usiamu.<br><br>

                        Semoga dengan bertambahnya usia menjadikan ".$bar1->namakaryawan." insan yang mulia,<br>
                        semakin bijak dan menjadi berkah bagi lingkungan kehidupan saudara dan 
                        semakin berprestasi dan berkarya di Hardaya Plantations Group.<br><br>

                        Kami segenap Direksi dan Karyawan Hardaya Plantations Group mengucapkan SELAMAT ULANG TAHUN<br>
                        Panjang Umur dan Bahagia selalu dalam hidupmu.<br><br><br><br>
                        
                        Hardaya Plantations Group</body></html>";
       if($bar1->email!==''){
           $to2=$to1.",".$bar1->email;
       } else {
           $to2=$to1;
       }
      if($to2!=''){
          $kirim=kirimEmail($to2,$subject2,$mess2);#this has return but disobeying;            
      }      
      $mess1="<html>
               <head>
               </head>
               <body>";
}    


##  email  reminder probation:============================================================
//penentuan tanggal 2 minggu lagi
$t=mktime(0,0,0,date('m'),(date('d')-76),date('Y'));//105 dianggap 3 bulan kurang 2 minggu
$tanggalmasuk=date('Y-m-d',$t);

$str1="select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,subbagian from ".$dbname.".datakaryawan
	       where tanggalmasuk='".$tanggalmasuk."' AND lokasitugas NOT LIKE '%HO'
               AND lokasitugas IN (SELECT kodeunit FROM ".$dbname.".bgt_regional_assignment WHERE regional='KALIMANTAN') 
               and tipekaryawan =0";
//echo $str1;
$res=mysql_query($str1);
$mess3="<html>
               <head>
               </head>
               <body>";
if(mysql_num_rows($res)>0)
{
    $subject3=" Reminder Akhir Masa Percobaan Karyawan Baru Kebun Sebakis (On: ".date('d-m-Y H:i:s').")";
                    $mess3.="Dear Hrd,<br>Berikut ini adalah karyawan yang akan berakhir masa percobannya:
                        <table border=1 cellspacing=0>
                        <thead>
                         <tr><td>No.</td>
                         <td>Nama Naryawan</td>
                         <td>TMK</td>
                         <td>Lokasi Tugas</td>
                         <td>Sub.Bagian</td>
                         </tr>   
                        </thead>  
                    <tbody>";
              $no=0;      
              while($bar=mysql_fetch_object($res))
              {
                  
                 $no+=1;
                  $mess3.="<tr><td>".$no."</td>
                                  <td>".$bar->namakaryawan."</td>
                                  <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                                  <td>".$bar->lokasitugas."</td><td>".$bar->subbagian."</td>
                                    </tr>";   
               } 
             $mess3.="</tbody><tfoot></tfoot></table><br>
                               Silahkan diproses sesuai dengan tahapan yang berlaku.<br>
                             <br>Regards, <br>OWL-Plantation System<br>The Best and Proven ERP For Palm Oil Plantation Solutions</body></html>";
          #kirim email   
        if($to1!=''){ 
               $kirim=kirimEmail($to1,$subject3,$mess3);#this has return but disobeying;     
        }    
}    


?>
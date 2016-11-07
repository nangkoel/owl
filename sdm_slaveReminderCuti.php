<?php
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$periode=date('m');

//Khusus staff HIP non HO
$strKary="select namakaryawan,lokasitugas,tanggalmasuk from ".$dbname.".datakaryawan
      where MONTH(tanggalmasuk)=".$periode." and tanggalmasuk not like '".date('Y-m')."%'
      and tipekaryawan=0 and (tanggalkeluar='0000-00-00' or tanggalkeluar>'".date('Y-m-d')."')
      and lokasitugas like 'H0%' and lokasitugas not like '%HO' order by lokasitugas,namakaryawan,tanggalmasuk";//0=staff 	
	  
$res=mysql_query($strKary);
if(mysql_num_rows($res)>0)
{
     $stream="<table>
              <thead>
              <tr>
              <td>No.</td>
              <td>Nama</td>
              <td>Tanggal Masuk</td>
              <td>Lokasi Tugas</td>
              </tr>
              </thead>
              <tbody>
              ";   
     while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                $stream.="<tr><td>".$no."</td>
                        <td>".$bar->namakaryawan."</td>
                        <td>".tanggalnormal($bar->tanggalmasuk)."</td>	
                        <td>".$bar->lokasitugas."</td>  
                      </tr>";
        }
     $stream.="</tbody>
               <tfoot>
               </tfoot>
               </table>"; 
     
  //ambilemail
     $to='';
     $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='RCUTI-H0RO'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $to=trim($bar->nilai);
     }
         
        $subject="[Notifikasi] Hak Cuti karyawan Central HIP Modo periode ".date('Y-m');
        $body="<html>
                 <head>
                 <body>
                   <dd>Dengan Hormat,</dd><br>
                   <br>
                   Berikut ini adalah karyawan yang akan memperoleh cuti baru bulan ini:
                   <br>
                    ".$stream."
                   <br>
                   Regards,<br>
                   Owl-Plantation System.
                 </body>
                 </head>
               </html>
               ";
       if($to!=''){ 
        $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;     
       }
}	

//Khusus staff SILSIP non HO
$strKary="select namakaryawan,lokasitugas,tanggalmasuk from ".$dbname.".datakaryawan
      where MONTH(tanggalmasuk)=".$periode." and tanggalmasuk not like '".date('Y-m')."%'
      and tipekaryawan=0 and (tanggalkeluar='0000-00-00' or tanggalkeluar>'".date('Y-m-d')."')
      and lokasitugas like 'L0%' and lokasitugas like 'P0%' and lokasitugas not like '%HO' 
      order by lokasitugas,namakaryawan,tanggalmasuk";//0=staff 	
	  
$res=mysql_query($strKary);
if(mysql_num_rows($res)>0)
{
     $stream="<table>
              <thead>
              <tr>
              <td>No.</td>
              <td>Nama</td>
              <td>Tanggal Masuk</td>
              <td>Lokasi Tugas</td>
              </tr>
              </thead>
              <tbody>
              ";   
     while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                $stream.="<tr><td>".$no."</td>
                        <td>".$bar->namakaryawan."</td>
                        <td>".tanggalnormal($bar->tanggalmasuk)."</td>	
                        <td>".$bar->lokasitugas."</td>  
                      </tr>";
        }
     $stream.="</tbody>
               <tfoot>
               </tfoot>
               </table>"; 
     
  //ambilemail
     $to='';
     $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='RCUTI-L0RO'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $to=trim($bar->nilai);
     }
         
        $subject="[Notifikasi] Hak Cuti karyawan Central SIL dan SIP periode ".date('Y-m');
        $body="<html>
                 <head>
                 <body>
                   <dd>Dengan Hormat,</dd><br>
                   <br>
                   Berikut ini adalah karyawan yang akan memperoleh cuti baru bulan ini:
                   <br>
                    ".$stream."
                   <br>
                   Regards,<br>
                   Owl-Plantation System.
                 </body>
                 </head>
               </html>
               ";
       if($to!=''){ 
        $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;     
       }
}	


//Khusus staff HIP HO
$strKary="select namakaryawan,lokasitugas,tanggalmasuk from ".$dbname.".datakaryawan
      where MONTH(tanggalmasuk)=".$periode." and tanggalmasuk not like '".date('Y-m')."%'
      and tipekaryawan=0 and (tanggalkeluar='0000-00-00' or tanggalkeluar>'".date('Y-m-d')."')
      and lokasitugas like '%HO' order by lokasitugas,namakaryawan,tanggalmasuk";//0=staff 	
	  
$res=mysql_query($strKary);
if(mysql_num_rows($res)>0)
{
     $stream="<table>
              <thead>
              <tr>
              <td>No.</td>
              <td>Nama</td>
              <td>Tanggal Masuk</td>
              <td>Lokasi Tugas</td>
              </tr>
              </thead>
              <tbody>
              ";   
     $no=0;
     while($bar=mysql_fetch_object($res))
        {
                $no+=1;
                $stream.="<tr><td>".$no."</td>
                        <td>".$bar->namakaryawan."</td>
                        <td>".tanggalnormal($bar->tanggalmasuk)."</td>	
                        <td>".$bar->lokasitugas."</td>  
                      </tr>";
        }
     $stream.="</tbody>
               <tfoot>
               </tfoot>
               </table>"; 
     
  //ambilemail
     $to='';
     $str="select nilai from ".$dbname.".setup_parameterappl where kodeparameter='RCUTI-H0HO'";
     $res=mysql_query($str);
     while($bar=mysql_fetch_object($res))
     {
         $to=trim($bar->nilai);
     }
         
        $subject="[Notifikasi] Hak Cuti karyawan HO periode ".date('Y-m');
        $body="<html>
                 <head>
                 <body>
                   <dd>Dengan Hormat,</dd><br>
                   <br>
                   Berikut ini adalah karyawan HO yang akan memperoleh cuti baru bulan ini:
                   <br>
                    ".$stream."
                   <br>
                   Regards,<br>
                   Owl-Plantation System.
                 </body>
                 </head>
               </html>
               ";
       if($to!=''){ 
        $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;     
       }
}	

?>

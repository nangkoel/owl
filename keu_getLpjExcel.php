<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

    $param=$_GET;
#header
$stream="
        
        Periode:".$param['dari']." S/d ".$param['sampai']." 
        <table class=sortable cellspacing=0 border=1>
        <thead>
        <tr class=rowheader>
        <td>".$_SESSION['lang']['biaya']."</td>
        <td>".$_SESSION['lang']['afdeling']."</td>
        <td>".$_SESSION['lang']['jumlah']."</td>
        </tr>
        </thead>
        <tbody>";

#Biaya Umum
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='6410100' and noakun<='6440100'    
     group  by afdeling";

$res=mysql_query($str);

$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>BIAYA UMUM</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }
  
#Biaya Panen
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='6520101' and noakun<='6520204'    
     group  by afdeling";
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>BIAYA PANEN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }  
  
#PemeliharaanTM
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and ((noakun >='6510100' and noakun<'6510301') or    
     (noakun >'6510311' and noakun<='6511003'))
     group  by afdeling";
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>PEMELIHARAAN TM</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }    
  
  
#Biaya Pemupukan
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='6510301' and noakun<='6510311'    
     group  by afdeling";
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>PEMUPUKAN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }   
 #Biaya LC
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='1320101' and noakun<='1320501'    
     group  by afdeling";
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>LC</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  } 
  
 #Biaya BIBITAN
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='1310101' and noakun<='1310401'    
     group  by afdeling";	

$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>BIBITAN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }

 #TBM
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='1322001' and noakun<='1330200'    
     group  by afdeling";		
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>PEMELIHARAAN TBM</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }  

 #Biaya KAPITAL
$str="select sum(jumlah) as jumlah,left(kodeblok,6) as afdeling,noakun  from ".$dbname.".keu_jurnaldt_vw
     where kodeorg='".$param['unit']."'
     and tanggal between ".tanggalsystem($param['dari'])." and ".tanggalsystem($param['sampai'])."
     and noakun >='1410100' and noakun<='1411000'    
     group  by afdeling";		
	
$res=mysql_query($str);
$no=0;
 while($bar=mysql_fetch_object($res))
  {
     $no+=1; 
     $stream.="<tr>
                <td>KAPITAL NON TANAMAN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }    
  
$stream.="</tbody>
          <tfoot></tfoot> 
          </table> 
        ";
if(isset($_GET['excel']))
   {
$nop_="LPJ_".$param['unit']."_".$param['dari']."_".$_GET['sampai'];
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
   }
   else
   {
       echo $stream;
   } 
?>
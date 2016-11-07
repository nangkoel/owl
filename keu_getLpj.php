<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

    $param=$_POST;
#header
$stream="
        <img onclick=\"fisikKeExcel(event)\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\"> 
        Periode:".$param['dari']."S/d".$param['sampai']." 
        <table class=sortable cellspacing=1 border=0>
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
     $stream.="<tr class=rowcontent onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','6410100','6440100','',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent  onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','6520101','6520204','',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent   onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','6510100','6511003','PTM',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent  onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','6510301','6510311','',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent  onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','1320101','1320501','',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','1310101','1310401','',event,'".$param['unit']."');\">
                <td>BIBITAN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }

 #PEMELIHARAAN TBM
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
     $stream.="<tr class=rowcontent  onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','1322001','1330200','',event,'".$param['unit']."');\">
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
     $stream.="<tr class=rowcontent   onclick=\"showByDetail('".$bar->afdeling."','".tanggalsystem($param['dari'])."','".tanggalsystem($param['sampai'])."','1410100','1411000','',event,'".$param['unit']."');\">
                <td>KAPITAL NON TANAMAN</td>
                <td>".$bar->afdeling."</td>
                <td align=right>".number_format($bar->jumlah)."</td>
               </tr>";
  }    
  
$stream.="</tbody>
          <tfoot></tfoot> 
          </table> 
        ";

       echo $stream;
?>
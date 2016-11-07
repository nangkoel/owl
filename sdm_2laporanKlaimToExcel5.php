<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$periode=$_GET['periode'];
$karyawanid=$_GET['karyawanid'];
if($periode=='')$periode=date('Y');    

 if($_GET['karyawanid']==''){
    $str3="select  sum(jasars) as rs, 
               sum(jasadr) as dr, sum(jasalab) as lab, 
               sum(byobat) as obat, 
               sum(bypendaftaran) administrasi, 
               a.periode, sum(a.totalklaim) as klaim, sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a 
               left join ".$dbname.".datakaryawan c
               on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
             group by periode order by periode";
}
else
        {
    $str3="select  sum(jasars) as rs, 
               sum(jasadr) as dr, sum(jasalab) as lab, 
               sum(byobat) as obat, 
               sum(bypendaftaran) administrasi, 
               a.periode, sum(a.totalklaim) as klaim, sum(a.jlhbayar) as bayar ".$dbname.".sdm_pengobatanht a 
               left join ".$dbname.".datakaryawan c
               on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
               and c.karyawanid=".$_GET['karyawanid']."
             group by periode order by periode";    
        }
    $x=$_GET['karyawanid']==''?'ALL':$_GET['nama'];    
  $stream.=" Trend Biaya Pengobatan per Jenis Biaya periode:".$periode."<br>
                      Nama Karyawan:".$x."<br>
      <table border=1>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td>Period</td>
        <td>".$_SESSION['lang']['biayars']."</td>
        <td>".$_SESSION['lang']['biayadr']."</td>
        <td>".$_SESSION['lang']['biayalab']."</td>
        <td>".$_SESSION['lang']['biayaobat']."</td>
        <td>".$_SESSION['lang']['biayapendaftaran']."</td>
        <td>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['dibayar']."</td>
    </tr>
    </thead><tbody>";      
    $res3=mysql_query($str3);    
    $no=0;
   $trs=0;
   $tdr=0;
   $tlb=0;
   $tob=0;
   $tad=0;
   $ttl=0;
    while($bar3=mysql_fetch_object($res3))
    {
        $no+=1;
        $stream.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->periode."</td>
            <td align=right>".number_format($bar3->rs)."</td>
            <td align=right>".number_format($bar3->dr)."</td>
            <td align=right>".number_format($bar3->lab)."</td>
            <td align=right>".number_format($bar3->obat)."</td>
            <td align=right>".number_format($bar3->administrasi)."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
            <td align=right>".number_format($bar3->bayar)."</td>    
        </tr>";	  
         $trs+=$bar3->rs;
         $tdr+=$bar3->dr;
         $tlb+=$bar3->lab;
         $tob+=$bar3->obat;
         $tad+=$bar3->administrasi;
         $ttl+=$bar3->klaim;        
         $byr+=$bar3->bayar;     
    }
   $stream.="<tr class=rowcontent>
            <td></td>
            <td>".$_SESSION['lang']['total']."</td>
            <td align=right>".number_format($trs)."</td>
            <td align=right>".number_format($tdr)."</td>
            <td align=right>".number_format($tlb)."</td>
            <td align=right>".number_format($tob)."</td>
            <td align=right>".number_format($tad)."</td>
            <td align=right>".number_format($ttl)."</td>
            <td align=right>".number_format($byr)."</td>    
        </tr>";    
$stream.="</tbody>
    <tfoot>
    </tfoot>
    </table>";	 
//write exel   
$nop_="TrendBiayaperDiagnosa-".$periode.$kodeorg;
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
        parent.window.alert('Cant convert to excel format');
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
   
	 
?>

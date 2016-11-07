<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$periode=$_GET['periode'];
$kodeorg=$_GET['kodeorg'];
if($periode=='')$periode=date('Y');    

$str3="select  sum(a.jlhbayar) as klaim,a.periode from ".$dbname.".sdm_pengobatanht a 
        left join ".$dbname.".datakaryawan c
        on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
              and c.lokasitugas like '".$kodeorg."%'
        group by periode order by periode
    ";


$stream="Trend Biaya Pengobatan ".$periode." ".$kodeorg."
<table border=1>
<thead>
<tr>
    <td bgcolor=#dedede>No</td>
    <td bgcolor=#dedede>Period</td>
    <td bgcolor=#dedede>".$_SESSION['lang']['jumlah']."</td>
</tr>
</thead>
<tbody>";  
$res3=mysql_query($str3);    
$no=0;
while($bar3=mysql_fetch_object($res3))
{
    $no+=1;
    $stream.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->periode."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
    </tr>";	  	
}   
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

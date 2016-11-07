<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$periode=$_GET['periode'];
$kodeorg=$_GET['kodeorg'];
if($periode=='')$periode=date('Y');    
$str3="select  sum(a.jlhbayar) as klaim,a.periode,a.kodebiaya,c.nama from ".$dbname.".sdm_pengobatanht a 
        left join ".$dbname.".sdm_5jenisbiayapengobatan c
        on a.kodebiaya=c.kode
        left join ".$dbname.".datakaryawan b 
        on a.karyawanid=b.karyawanid
              where a.periode like '".$periode."%'
              and b.lokasitugas like '".$kodeorg."%'
        group by kodebiaya,periode order by periode
    ";
    $res3=mysql_query($str3);    
    $no=0;
    while($bar3=mysql_fetch_object($res3))
    {
        $kode[$bar3->kodebiaya][$bar3->periode]=$bar3->klaim;
        $kodex[$bar3->kodebiaya]['nama']=$bar3->nama;
    }
 $stream.="Biaya Pengobatan per jenis perawatan
     <table border=1>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td>".$_SESSION['lang']['kodeorg']."</td>
        <td>".$_SESSION['lang']['tahun']."</td>            
        <td>Treatment Type</td>
        <td  align=center>Jan</td>
        <td  align=center>Feb</td>
        <td  align=center>Mar</td>
        <td  align=center>Apr</td>
        <td  align=center>Mei</td>
        <td  align=center>Jun</td>
        <td  align=center>Jul</td>
        <td  align=center>Aug</td>
        <td  align=center>Sep</td>
        <td  align=center>Oct</td>
        <td  align=center>Nov</td>
        <td  align=center>Dec</td>
        <td>".$_SESSION['lang']['total']."</td>
    </tr>
    </thead>
    <tbody>";   
    
    foreach($kodex as $key=>$val){
        $no+=1;
        $total=$kode[$key][$periode."-12"]+$kode[$key][$periode."-11"]+$kode[$key][$periode."-10"]+$kode[$key][$periode."-09"]+$kode[$key][$periode."-08"]+$kode[$key][$periode."-07"]+$kode[$key][$periode."-06"]+$kode[$key][$periode."-05"]+$kode[$key][$periode."-04"]+$kode[$key][$periode."-03"]+$kode[$key][$periode."-02"]+$kode[$key][$periode."-01"];
        $gt+=$total;        
 $stream.="<tr>
            <td>".$no."</td>
            <td>".$kodeorg."</td>
            <td>".$periode."</td>    
            <td>".$kodex[$key]['nama']."</td>                
            <td align=right>".number_format($kode[$key][$periode."-01"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-02"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-03"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-04"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-05"])."</td> 
            <td align=right>".number_format($kode[$key][$periode."-06"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-07"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-08"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-09"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-10"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-11"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-12"])."</td>
            <td align=right>".number_format($total)."</td>    
        </tr>";
        $t01+=$kode[$key][$periode."-01"];
        $t02+=$kode[$key][$periode."-02"];
        $t03+=$kode[$key][$periode."-03"];
        $t04+=$kode[$key][$periode."-04"];
        $t05+=$kode[$key][$periode."-05"];
        $t06+=$kode[$key][$periode."-06"];
        $t07+=$kode[$key][$periode."-07"];
        $t08+=$kode[$key][$periode."-08"];
        $t09+=$kode[$key][$periode."-09"];
        $t10+=$kode[$key][$periode."-10"];
        $t11+=$kode[$key][$periode."-11"];
        $t12+=$kode[$key][$periode."-12"]; 
    }
 $stream.="<tr class=rowcontent>
            <td colspan=4>Total</td>                
            <td align=right>".number_format($t01)."</td>
            <td align=right>".number_format($t02)."</td>
            <td align=right>".number_format($t03)."</td>
             <td align=right>".number_format($t04)."</td>
             <td align=right>".number_format($t05)."</td>
             <td align=right>".number_format($t06)."</td>
             <td align=right>".number_format($t07)."</td>
             <td align=right>".number_format($t08)."</td>
             <td align=right>".number_format($t09)."</td>
             <td align=right>".number_format($t10)."</td>
             <td align=right>".number_format($t11)."</td>
             <td align=right>".number_format($t12)."</td>     
            <td align=right>".number_format($gt)."</td>    
        </tr>";  
 $stream.="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset>";    
    
$nop_="Biaya pengobatan Per jenis Pengobatan-".$periode.$kodeorg;
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

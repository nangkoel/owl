<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahun=$_GET['tahun'];
$departemen=$_GET['departemen'];
        	
//check, one-two
if($tahun==''){
    echo "WARNING: silakan mengisi tahun."; exit;
}
if($departemen==''){
    echo "WARNING: silakan mengisi departemen."; exit;
}

//echo $tahun.$kebun;
$stream='';
$warnalatar="#77ff77";
//header
$stream.="<table class=sortable cellspacing=1 border=1 width=100%>
     <thead>
        <tr class=rowtitle>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>No.</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['namaakun']."</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['keterangan']."</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['alokasibiaya']."</td>
            <td bgcolor=\"".$warnalatar."\" rowspan=2 align=center>".$_SESSION['lang']['jumlah']."</td>
            <td bgcolor=\"".$warnalatar."\" colspan=12 align=center>Distribusi</td>
        </tr>";
       $stream.="<tr>
           <td bgcolor=\"".$warnalatar."\" align=center>Jan</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Feb</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Mar</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Apr</td>
           <td bgcolor=\"".$warnalatar."\" align=center>May</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Jun</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Jul</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Aug</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Sep</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Oct</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Nov</td>
           <td bgcolor=\"".$warnalatar."\" align=center>Dec</td>
       </tr>";
     
$stream.="</thead>
    <tbody>";

//pilihan kodeakun    
    $str="select noakun,namaakun from ".$dbname.".keu_5akun
                    where detail=1  order by noakun
                    ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $noakun[$bar->noakun]=$bar->namaakun;
    }

// ambil data
$str="select * from ".$dbname.".bgt_dept where departemen = '".$departemen."' and tahunbudget = '".$tahun."' order by noakun, alokasibiaya";
//            echo $str;
$no=0;
$jumlahan=$d01an=$d02an=$d03an=$d04an=$d05an=$d06an=$d07an=$d08an=$d09an=$d10an=$d11an=$d12an=0;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
    $no+=1;
    $stream.="<tr class=rowcontent>
       <td align=center>$no</td>
       <td align=left>".$bar->noakun." - ".$noakun[$bar->noakun]."</td>
       <td align=left>".$bar->keterangan."</td>
       <td align=center>".$bar->alokasibiaya."</td>
       <td align=right>".number_format($bar->jumlah)."</td>
       <td align=right>".number_format($bar->d01)."</td>
       <td align=right>".number_format($bar->d02)."</td>
       <td align=right>".number_format($bar->d03)."</td>
       <td align=right>".number_format($bar->d04)."</td>
       <td align=right>".number_format($bar->d05)."</td>
       <td align=right>".number_format($bar->d06)."</td>
       <td align=right>".number_format($bar->d07)."</td>
       <td align=right>".number_format($bar->d08)."</td>
       <td align=right>".number_format($bar->d09)."</td>
       <td align=right>".number_format($bar->d10)."</td>
       <td align=right>".number_format($bar->d11)."</td>
       <td align=right>".number_format($bar->d12)."</td>
    </tr>";
        $jumlahan+=$bar->jumlah;
        $d01an+=$bar->d01;
        $d02an+=$bar->d02;
        $d03an+=$bar->d03;
        $d04an+=$bar->d04;
        $d05an+=$bar->d05;
        $d06an+=$bar->d06;
        $d07an+=$bar->d07;
        $d08an+=$bar->d08;
        $d09an+=$bar->d09;
        $d10an+=$bar->d10;
        $d11an+=$bar->d11;
        $d12an+=$bar->d12;
}
   $stream.="<tr>
       <td colspan=4 align=center>Total</td>
       <td align=right>".number_format($jumlahan)."</td>
       <td align=right>".number_format($d01an)."</td>
       <td align=right>".number_format($d02an)."</td>
       <td align=right>".number_format($d03an)."</td>
       <td align=right>".number_format($d04an)."</td>
       <td align=right>".number_format($d05an)."</td>
       <td align=right>".number_format($d06an)."</td>
       <td align=right>".number_format($d07an)."</td>
       <td align=right>".number_format($d08an)."</td>
       <td align=right>".number_format($d09an)."</td>
       <td align=right>".number_format($d10an)."</td>
       <td align=right>".number_format($d11an)."</td>
       <td align=right>".number_format($d12an)."</td>
    </tr>";
$stream.="    </tbody>
         <tfoot>
         </tfoot>		 
   </table>";       
$stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];

$qwe=date("YmdHms");

$nop_="bgt_departemen_".$tahun." ".$departemen;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}    
?>
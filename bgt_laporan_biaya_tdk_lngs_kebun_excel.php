<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_GET['kodeorg'];
$thnbudget=$_GET['thnbudget'];
#ambil luas kebun
@$luas=0;
//$str="select sum(hathnini) as luas from ".$dbname.".bgt_areal_per_afd_vw 
//      where tahunbudget=".$thnbudget." and afdeling like '".$kodeorg."%'";
$str="select sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok where 
      kodeblok like '".$kodeorg."%' and tahunbudget='".$thnbudget."' and statusblok in ('TBM','TM') group by tahunbudget,kodeblok";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $luas+=$bar->luas;
}

$str="select a.*,b.namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where a.kodebudget='UMUM' and tahunbudget=".$thnbudget." and a.kodeorg='".$kodeorg."'";
$res=mysql_query($str);
$no=0;
$rpperha=0;
$stream="Budget Biaya tidak langsung Kebun ".$kodeorg." tahun budget: ".$thnbudget."
<table border=1>
 <thead>
     <tr>
       <td align=center>".$_SESSION['lang']['nourut']."</td>
       <td align=center>".$_SESSION['lang']['noakun']."</td>
       <td align=center>".$_SESSION['lang']['namaakun']."</td>
       <td align=center>".$_SESSION['lang']['luas']."</td>
       <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
       <td align=center>".$_SESSION['lang']['rpperha']."</td>  
       <td align=center>01(Rp)</td>
       <td align=center>02(Rp)</td>
       <td align=center>03(Rp)</td>
       <td align=center>04(Rp)</td>
       <td align=center>05(Rp)</td>
       <td align=center>06(Rp)</td>
       <td align=center>07(Rp)</td>
       <td align=center>08(Rp)</td>
       <td align=center>09(Rp)</td>
       <td align=center>10(Rp)</td>
       <td align=center>11(Rp)</td>
       <td align=center>12(Rp)</td>
     </tr>
     </thead>
     <tbody>";
while($bar=mysql_fetch_object($res))
{
    @$rpperha=$bar->rupiah/$luas;
    $no+=1;
    $stream.="<tr>
           <td>".$no."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($luas,0,'.',',')."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperha,0,'.',',')."</td>    
           <td align=right>".number_format($bar->rp01,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp02,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp03,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp04,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp05,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp06,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp07,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp08,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp09,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp10,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp11,0,'.',',')."</td>
           <td align=right>".number_format($bar->rp12,0,'.',',')."</td>
         </tr>";
    $tt+=$bar->rupiah;
    $t01+=$bar->rp01;
    $t02+=$bar->rp02;
    $t03+=$bar->rp03;
    $t04+=$bar->rp04;
    $t05+=$bar->rp05;
    $t06+=$bar->rp06;
    $t07+=$bar->rp07;
    $t08+=$bar->rp08;
    $t09+=$bar->rp09;
    $t10+=$bar->rp10;
    $t11+=$bar->rp11;
    $t12+=$bar->rp12;   
}
    @$ttperluas=$tt/$luas;
    $stream.="<tr>
           <td colspan=4>TOTAL</td>
           <td align=right>".number_format($tt,0,'.',',')."</td>
           <td align=right>".number_format($ttperluas,0,'.',',')."</td>    
           <td align=right>".number_format($t01,0,'.',',')."</td>
           <td align=right>".number_format($t02,0,'.',',')."</td>
           <td align=right>".number_format($t03,0,'.',',')."</td>
           <td align=right>".number_format($t04,0,'.',',')."</td>
           <td align=right>".number_format($t05,0,'.',',')."</td>
           <td align=right>".number_format($t06,0,'.',',')."</td>
           <td align=right>".number_format($t07,0,'.',',')."</td>
           <td align=right>".number_format($t08,0,'.',',')."</td>
           <td align=right>".number_format($t09,0,'.',',')."</td>
           <td align=right>".number_format($t10,0,'.',',')."</td>
           <td align=right>".number_format($t11,0,'.',',')."</td>
           <td align=right>".number_format($t12,0,'.',',')."</td>
         </tr>
         <tr>
           <td colspan=18>Luas : ".number_format($luas,0,'.',',')." Ha (Total Planted)</td>
         </tr>";
    
$stream.="</tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
$dte=date("Hms");
$nop_="Budget_".$kodeorg."_BTL_".$thnbudget."__".$dte;
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
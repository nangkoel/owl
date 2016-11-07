<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg=$_GET['kodeorg'];
$thnbudget=$_GET['thnbudget'];
#ambil prd pks
$prd=0;
$str="select sum(kgcpo) as cpo,sum(kgkernel) as kernel,sum(kgolah)  as tbs from ".$dbname.".bgt_produksi_pks_vw 
      where tahunbudget=".$thnbudget." and millcode = '".$kodeorg."'";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{
    $prd=$bar->cpo+$bar->kernel;
    $totTbs=$bar->tbs;
    $totCpo=$bar->cpo;
    $totKer=$bar->kernel;
}

$str="select a.*,b.namaakun from ".$dbname.".bgt_budget_detail a left join
      ".$dbname.".keu_5akun b on a.noakun=b.noakun
      where a.kodebudget='UMUM' and tahunbudget=".$thnbudget." and a.kodeorg='".$kodeorg."'";
$res=mysql_query($str);
$no=0;

$stream="<fieldset><legend>".$_SESSION['lang']['produksipabrik']." </legend>
<table class=sortable cellspacing=1 border=1 width=300px>
     <thead>
         <tr class=rowheader>
           <td align=center>".$_SESSION['lang']['tbsdiolah']."</td>
           <td align=center>Palm Product</td>
           <td align=center>".$_SESSION['lang']['cpo']."</td>                  
           <td align=center>".$_SESSION['lang']['kernel']."</td> 
         </tr>
         </thead>
         <tbody>
         <tr class=rowcontent>
           <td align=right>".number_format($totTbs/1000,0,".",",")."</td>
           <td align=right>".number_format($prd/1000,0,".",",")."</td>
           <td align=right>".number_format($totCpo/1000,0,".",",")."</td>
           <td align=right>".number_format($totKer/1000,0,".",",")."</td>    
         </tr>   
     </tbody>
     <tfoot>
     </tfoot>
     </table>
     </fieldset>"; 
$stream.="Budget Biaya tidak langsung PKS ".$kodeorg." tahun budget: ".$thnbudget."
<table border=1>
 <thead>
     <tr>
       <td align=center>".$_SESSION['lang']['nourut']."</td>
       <td align=center>".$_SESSION['lang']['noakun']."</td>
       <td align=center>".$_SESSION['lang']['namaakun']."</td>
       <td align=center>".$_SESSION['lang']['jumlahrp']."</td>
       <td align=center>".$_SESSION['lang']['rpperkg']."-PP</td> 
       <td align=center>".$_SESSION['lang']['rpperkg']."-TBS</td> 
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
    @$rpperha=$bar->rupiah/$prd;
    @$rptbs=$bar->rupiah/$totTbs;
    $no+=1;
    $stream.="<tr>
           <td>".$no."</td>
           <td>".$bar->noakun."</td>
           <td>".$bar->namaakun."</td>
           <td align=right>".number_format($bar->rupiah,0,'.',',')."</td>
           <td align=right>".number_format($rpperha,7,'.',',')."</td>  
           <td align=right>".number_format($rptbs,7,'.',',')."</td>     
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
    $totRup+=$bar->rupiah;
    $grTotRp+=$rpperha;
    $grTotTbs+=$rptbs;
    $tot[1]+=$bar->rp02;$tot[2]+=$bar->rp02;$tot[3]+=$bar->rp03;
    $tot[4]+=$bar->rp04;$tot[5]+=$bar->rp05;$tot[6]+=$bar->rp06;
    $tot[7]+=$bar->rp07;$tot[8]+=$bar->rp08;$tot[9]+=$bar->rp09;
    $tot[10]+=$bar->rp10;$tot[11]+=$bar->rp11;$tot[12]+=$bar->rp12;
}
$stream.="<tr><td colspan=3>".$_SESSION['lang']['total']."</td>";
$stream.="<td>".number_format($totRup,0)."</td><td>".number_format($grTotRp,7)."</td><td>".number_format($grTotTbs,7)."</td>";
for($rd=1;$rd<=12;$rd++)
{
    $stream.="<td>".number_format($tot[$rd],0)."</td>";
}
$stream.="</tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
$nop_="Budget_".$kodeorg."_BTL_".$thnbudget;
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
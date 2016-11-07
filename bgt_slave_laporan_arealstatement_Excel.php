<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$tahun=$_GET['tahun'];
$kebun=$_GET['kebun'];
        
	
//check, one-two
if($tahun==''){
    echo "WARNING: silakan mengisi tahun."; exit;
}
if($kebun==''){
    echo "WARNING: silakan mengisi kebun."; exit;
}

//echo $tahun.$kebun;

// ambil data
    $isidata=array();
//$str="select * from ".$dbname.".bgt_areal_per_afd_vw where tahunbudget = '".$tahun."' and afdeling like '".$kebun."%' order by afdeling, thntnm";
$str="select sum(hathnini) as hathnini,sum(hanonproduktif) as hanonproduktif,sum(pokokproduksi) as pokokproduksi,
      thntnm,substr(kodeblok,1,6) as afdeling,statusblok,sum(pokokthnini) as pokokthnini from ".$dbname.".bgt_blok where
      substr(kodeblok,1,4)='".$kebun."' and tahunbudget = '".$tahun."' and statusblok != 'BBT' group by substr(kodeblok,1,6),thntnm,statusblok
      order by substr(kodeblok,1,6),thntnm";
//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
 if(($bar->thntnm+3)<$tahun){
     if($bar->statusblok!='CADANGAN')
      {
        $isidata[$bar->thntnm.$bar->statusblok][$bar->afdeling]+=$bar->hathnini;
        $totalrowdata[$bar->thntnm.$bar->statusblok][total]+=$bar->hathnini;
        $totalcolumndata[$bar->afdeling.$bar->statusblok][total]+=$bar->hathnini;
        $total[$bar->statusblok]+=$bar->hathnini;
        $rowdata0[$bar->thntnm.$bar->statusblok]=$bar->thntnm;
      }
    }
    else
    {
      if($bar->statusblok!='CADANGAN')
      {
         if($bar->statusblok=='TB')
            {
                $bar->statusblok='TBM';
            }
            $isidata1[$bar->thntnm.$bar->statusblok][$bar->afdeling]+=$bar->hathnini;
            $totalrowdata1[$bar->thntnm.$bar->statusblok][total]+=$bar->hathnini;
            $totalcolumndata1[$bar->afdeling.$bar->statusblok][total]+=$bar->hathnini;
            $total1[$bar->statusblok]+=$bar->hathnini;
            $rowdata1[$bar->thntnm.$bar->statusblok]=$bar->thntnm;
      }
    }
    if($bar->statusblok=='CADANGAN')
    {
        $bar->hanonproduktif=$bar->hathnini;
    }
    $unplanted[$bar->afdeling]+=$bar->hanonproduktif;
    $totalunplanted+=$bar->hanonproduktif;
    
    $kadaster[$bar->afdeling]+=$bar->hathnini+$bar->hanonproduktif;
    $totalkadaster+=$bar->hathnini+$bar->hanonproduktif;
    
    $isidata2[$bar->thntnm][$bar->afdeling]+=$bar->pokokthnini;
    $totalrowdata2[$bar->thntnm][total]+=$bar->pokokthnini;
    $totalcolumndata2[$bar->afdeling][total]+=$bar->pokokthnini;
    $total2+=$bar->pokokthnini;
    $pkkProduktif[$bar->thntnm][$bar->afdeling]+=$bar->pokokproduksi;
    $totPkkProduktif+=$bar->pokokproduksi;
    $totPerthnPkk[$bar->thntnm][total]+=$bar->pokokproduksi;
    $totAfdPkkProduktif[$bar->afdeling][total]+=$bar->pokokproduksi;
    
    $headerdata[$bar->afdeling]=$bar->afdeling;
    $rowdata[$bar->thntnm]=$bar->thntnm;
}
count($headerdata)>0?sort($headerdata):false;
count($rowdata)>0?sort($rowdata):false;
count($rowdata0)>0?sort($rowdata0):false;
count($rowdata1)>0?sort($rowdata1):false;
//        echo "<pre>";
//        print_r($rowdata);
//        print_r($isidata);
//        echo "</pre>";

$jumlahafdeling=0;
if(!empty($headerdata))foreach($headerdata as $baris1)
{
    $jumlahafdeling+=1;
} 
$jumlahrow=0;
if(!empty($rowdata))foreach($rowdata as $baris2)
{
    $jumlahrow+=1;
}else{
    echo"Data tidak tersedia."; exit;
} 
$jumlahafdeling=$jumlahafdeling*2;
$stream='Data Areal Statement<br>Tahun Budget : '.$tahun.'<br>Unit : '.$kebun;
//header
$stream.="<table class=sortable cellspacing=1 border=1 width=100%>
     <thead>
        <tr class=rowtitle>
            <td rowspan=2 align=center>".$_SESSION['lang']['uraian']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['tahuntanam']."</td>
            <td colspan=".$jumlahafdeling." align=center>Data per Afdeling</td>";
       $stream.="<td rowspan=2 align=center colspan=2>".$_SESSION['lang']['total']."</td>
        </tr>";
    if(!empty($headerdata))foreach($headerdata as $baris)
    {
       $stream.="<td align=center colspan=2>".$baris."</td>";
    } 
$stream.="</thead>
    <tbody>";
//TM
//body1
$statTm="TM";
$countdown=$jumlahrow;
if(!empty($rowdata0))foreach($rowdata0 as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    $stream.="<tr class=rowcontent>";
    if($countdown==$jumlahrow)$stream.="<td align=left>A. Luas Areal TM (ha)</td>"; else $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>".$tt."</td>";
//    $tahuntanam=$baris2;
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
        $stream.="<td align=right colspan=2>".number_format($isidata[$tt.$statTm][$af],2)."</td>";
        $totalplanted_tm[$af]+=$isidata[$tt.$statTm][$af];
    } 
    $stream.="<td align=right colspan=2>".number_format($totalrowdata[$tt.$statTm][total],2)."</td>";
    $stream.="</tr>";
    $countdown-=1;
    }
} 
if(!empty($rowdata0)){
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>Subtotal</td>";
    if(!empty($headerdata))foreach($headerdata as $af)
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right colspan=2>".number_format($totalcolumndata[$af.$statTm][total],2)."</td>";
    } 
        $stream.="<td align=right colspan=2>".number_format($total[$statTm],2)."</td>";
    $stream.="</tr>";
}
 //TBM
    $statTbm="TBM";
$countdown=$jumlahrow;
if(!empty($rowdata1))foreach($rowdata1 as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    $stream.="<tr style=\"cursor:pointer;\" title=\"Click untuk melihat detail\" onclick=\"detail('A',".$tahun.",'".$kebun."',".$tt.",event)\"; class=rowcontent>";
    if($countdown==$jumlahrow)$stream.="<td align=left>B. Luas Areal TBM(ha)</td>"; else $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>".$tt."</td>";
//    $tahuntanam=$baris2;
    foreach($headerdata as $af) // afdeling / column
    {
        $stream.="<td align=right colspan=2>".number_format($isidata1[$tt.$statTbm][$af],2)."</td>";
        $totalplanted_tbm[$af]+=$isidata1[$tt.$statTbm][$af];
    } 
    $stream.="<td align=right colspan=2>".number_format($totalrowdata1[$tt.$statTbm][total],2)."</td>";
    $stream.="</tr>";
    $countdown-=1;
    }
} 
if(!empty($rowdata1)){
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>Subtotal TBM</td>";
   foreach($headerdata as $af)
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right colspan=2>".number_format($totalcolumndata1[$af.$statTbm][total],2)."</td>";
    } 
        $stream.="<td align=right colspan=2>".number_format($total1[$statTbm],2)."</td>";
    $stream.="</tr>";
}      
    
//Total Planted

    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>TOTAL PLANTED</td>";
   if(!empty($headerdata))foreach($headerdata as $af)
    {
        $tp=$totalplanted_tbm[$af]+$totalplanted_tm[$af];
        $stream.="<td align=right colspan=2>".number_format($tp,2)."</td>";
    } 
    $ttp=$total1[$statTbm]+$total[$statTm];
        $stream.="<td align=right  colspan=2>".number_format($ttp,2)."</td>";
    $stream.="</tr>";
  
    
//data unplanted
$stream.="<tr class=rowcontent><td></td><td align=center>Unplanted</td>";
if(!empty($unplanted))foreach($unplanted as $dat)
{
    $stream.="<td align=right colspan=2>".number_format($dat,2)."</td>";
}
$stream.="<td align=right colspan=2>".number_format($totalunplanted,2)."</td></tr>";

//Grand Total

    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>GRAND TOTAL</td>";
if(!empty($headerdata)){   
   foreach($headerdata as $af)
    { 
       $gt=$totalplanted_tbm[$af]+$totalplanted_tm[$af]+$unplanted[$af];
      $stream.="<td align=right colspan=2>".number_format($gt,2)."</td>";
    } 
    $tgt=$ttp+$totalunplanted;
        $stream.="<td align=right  colspan=2>".number_format($tgt,2)."</td>";
    $stream.="</tr>";
 }


/*
//total planted and kadaster
$stream.="<tr class=rowcontent><td></td><td align=center>TOTAL AREAL</td>";

foreach($kadaster as $dat)
{
    $stream.="<td align=right>".number_format($dat,2)."</td>";
}
$stream.="<td align=right>".number_format($totalkadaster,2)."</td></tr>";
*/
    
//body2
//$countdown=$jumlahrow;
//if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
//{
//    if($tt!=0)
//    {
//    $stream.="<tr class=rowcontent>";
//    if($countdown==$jumlahrow)$stream.="<td align=left>B. Populasi Tanaman (pkk)</td>"; else $stream.="<td align=center>&nbsp;</td>";
//    $stream.="<td align=center>".$tt."</td>";
////    $tahuntanam=$baris2;
//    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
//    {
////        $tahuntanam=$baris1;
//        $stream.="<td align=right colspan=2>".number_format($isidata2[$tt][$af])."</td>";
//    } 
//    $stream.="<td align=right colspan=2>".number_format($totalrowdata2[$tt][total])."</td>";
//    $stream.="</tr>";
//    $countdown-=1;
//    }
//} 

$stream.="<tr  class=rowcontent>";
$stream.="<td align=left>C. Populasi Tanaman (pkk)</td><td align=left>&nbsp;</td>";
if(!empty($headerdata))foreach($headerdata as $af)
{
    $stream.="<td align=center>Jumlah Pokok</td><td align=center>Pokok Produktif</td>"; 
}
$stream.="<td align=center>Jumlah Pokok</td><td align=center>Pokok Produktif</td></tr>"; 
//body2
$countdown=$jumlahrow;
if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    $stream.="<tr style=\"cursor:pointer;\" title=\"Click untuk melihat detail\" onclick=\"detail('B',".$tahun.",'".$kebun."',".$tt.",event)\"; class=rowcontent>";
    if($countdown==$jumlahrow)$stream.="<td align=left>&nbsp;</td>"; else $stream.="<td align=center>&nbsp;</td>";
    
    $stream.="<td align=center>".$tt."</td>";
//    $tahuntanam=$baris2;
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right>".number_format($isidata2[$tt][$af])."</td>";
        $stream.="<td align=right>".number_format($pkkProduktif[$tt][$af])."</td>";
    } 
    $stream.="<td align=right>".number_format($totalrowdata2[$tt][total])."</td>";
    $stream.="<td align=right>".number_format($totPerthnPkk[$tt][total])."</td>";
    $stream.="</tr>";
    $countdown-=1;
    }
} 
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>Total Pokok</td>";
    if(!empty($headerdata))foreach($headerdata as $af) 
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right>".number_format($totalcolumndata2[$af][total])."</td>";
        $stream.="<td align=right>".number_format($totAfdPkkProduktif[$af][total])."</td>";
    } 
        $stream.="<td align=right>".number_format($total2)."</td>";
        $stream.="<td align=right>".number_format($totPkkProduktif)."</td>";
    $stream.="</tr>";


$stream.="    </tbody>
         <tfoot>
         </tfoot>		 
   </table>";    
$stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
$qwe=date("YmdHms");

$nop_="bgt_arealstatement".$tahun." ".$kebun;
//if(strlen($stream)>0)
//{
//     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
//     gzwrite($gztralala, $stream);
//     gzclose($gztralala);
//     echo "<script language=javascript1.2>
//        window.location='tempExcel/".$nop_.".xls.gz';
//        </script>";
//}  
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
?>
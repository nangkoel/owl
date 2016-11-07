<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses']; 
$_POST['traksiId']==''?$traksiId=$_GET['traksiId']:$traksiId=$_POST['traksiId']; 
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];

if($proses=='preview'||$proses=='excel'){

    if($traksiId!='')
    {
        $whr=" and  b.kodeorg='".$traksiId."'";
        $whrpab=" and kodeorg='".$traksiId."'";
    }
    if($afdId!='')
    {
        $whr=" and a.nospb like '%".$afdId."%'";
        $whrpab=" and nospb like '%".$afdId."%'";
    }

    if($periode=='')
    {
         exit("Error:Field Tidak Boleh Kosong");
    }
    $brd=0;
    if($proses=='excel')
    {
        $brd=1;
         $bgcoloraja="bgcolor=#DEDEDE align=center";
    }
#ambil spb kebun
$str="SELECT a.nospb,sum(a.jjg) as jjg,b.tanggal,substr(a.nospb,9,6) as afdeling FROM ".$dbname.".kebun_spbdt a
           left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb where b.tanggal like '".$periode."%' ".$whr." group by a.nospb
           order by tanggal,nospb";    
$reskebun=mysql_query($str);
#ambil  spb timbangan
$sPabrik="select nospb,(jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,beratbersih as berat,(beratbersih-kgpotsortasi) as beratbersih,
                notransaksi,left(tanggal,10) as tanggal from ".$dbname.".pabrik_timbangan where 
          left(tanggal,10)!='' ".$whrpab." and nospb!='' 
          and left(tanggal,10) like '".$periode."%' order by tanggal,nospb";
$respabrik=mysql_query($sPabrik);
#ambil semua no spb di pks maupun kebun
while($bar=mysql_fetch_object($reskebun)){
    $nospb[$bar->nospb].=$bar->nospb.' ';
    $afd[$bar->nospb].=$bar->afdeling.' ';
    $tglkebun[$bar->nospb].=$bar->tanggal.' ';
    $jjgkebun[$bar->nospb]+=$bar->jjg.' ';
    $nospbkebun[$bar->nospb].=$bar->nospb.' ';
}
while($bar1=mysql_fetch_object($respabrik)){
    $nospb[$bar1->nospb].=$bar1->nospb.' ';
    $tglpabrik[$bar1->nospb].=$bar1->tanggal.' ';
    $tiket[$bar1->nospb].=$bar1->notransaksi.' ';   
    $berat[$bar1->nospb]+=$bar1->berat.' ';
    $beratbersih[$bar1->nospb]+=$bar1->beratbersih.' ';  
    $jjgpabrik[$bar1->nospb]+=$bar1->jjgpabrik.' ';    
    $nosppabrik[$bar1->nospb].=$bar1->nospb.' ';   
}

$tab.="
<table cellspacing=1 border=".$brd." >
<thead>
<tr><td align=center colspan=5>".$_SESSION['lang']['kebun']."</td>
<td align=center colspan=6>".$_SESSION['lang']['pabrik']."</td></tr>
            <tr class=rowheader>
            <td ".$bgcoloraja.">No</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['kodeorg']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['nospb']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['tglNospb']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['jjg']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['tanggal']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['nospb']."</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['notransaksi']."</td>
                <td ".$bgcoloraja.">".$_SESSION['lang']['berat']." Bersih</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['berat']." Normal</td>
            <td ".$bgcoloraja.">".$_SESSION['lang']['jjg']."</td>
            </tr>
</thead><tbody>";
if(isset($nospb)){
    $no=0;
    foreach($nospb as $spb=>$val){
        $no++;
        if(!isset($nospbkebun[$spb])){
            $colorkebun='red';
        }
       else { 
           $colorkebun='#D1E3BA';
       }
         if(!isset($nosppabrik[$spb])){
            $colorpabrik='red';
        }       
       else { 
           $colorpabrik='#CEDCDE';
       }        
          $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td bgcolor=".$colorkebun.">".$afd[$spb]."</td>
            <td bgcolor=".$colorkebun.">".$nospbkebun[$spb]."</td>
            <td bgcolor=".$colorkebun.">".tanggalnormal($tglkebun[$spb])."</td>
            <td bgcolor=".$colorkebun." align=right>".number_format($jjgkebun[$spb])."</td> 
            <td bgcolor=".$colorpabrik.">".$tglpabrik[$spb]."</td>
            <td bgcolor=".$colorpabrik.">".$nosppabrik[$spb]."</td>
            <td bgcolor=".$colorpabrik.">".$tiket[$spb]."</td>
                <td align=right bgcolor=".$colorpabrik.">".number_format($berat[$spb])."</td>
            <td align=right bgcolor=".$colorpabrik.">".number_format($beratbersih[$spb])."</td>
            <td align=right bgcolor=".$colorpabrik.">".number_format($jjgpabrik[$spb])."</td>
            </tr>";        
          $totaljjgkebun+=$jjgkebun[$spb];
          $totalberatnormal+=$berat[$spb];
          $totalberatbersih+=$beratbersih[$spb];
          $totaljjgpabrik+=$jjgpabrik[$spb];     
    }
          $tab.="<tr class=rowcontent>
            <td></td>
            <td bgcolor=".$colorkebun."></td>
            <td bgcolor=".$colorkebun."></td>
            <td bgcolor=".$colorkebun."><b>Total</b></td>
            <td bgcolor=".$colorkebun." align=right><b>".number_format($totaljjgkebun)."</b></td> 
            <td bgcolor=".$colorpabrik."></td>
            <td bgcolor=".$colorpabrik."></td>
            <td bgcolor=".$colorpabrik."><b>Total</b></td>
            <td align=right bgcolor=".$colorpabrik."><b>".number_format($totalberatnormal)."</b></td>
            <td align=right bgcolor=".$colorpabrik."><b>".number_format($totalberatbersih)."</b></td>
            <td align=right bgcolor=".$colorpabrik."><b>".number_format($totaljjgpabrik)."</b></td>
            </tr>";                
    
}
 $tab.="</tbody></table></td></tr></tbody><table>";

}	
switch($proses)
{
        case'preview':
        echo $tab;
        break;

        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $nop_="spbvstimbangan__".$traksiId."__".$periode;
        if(strlen($tab)>0)
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
        if(!fwrite($handle,$tab))
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
        break;

        case'getPrd':
            //$traksiId
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $sPrd="select distinct left(tanggal,7) as periode from ".$dbname.".kebun_spbht 
               where kodeorg = '".$traksiId."' order by left(tanggal,7) desc";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optPeriode.="<option value=".$rPrd['periode'].">".$rPrd['periode']."</option>";
        }
        $optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sPrd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
               where induk = '".$traksiId."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd=mysql_query($sPrd) or die(mysql_error($conn));
        while($rPrd=  mysql_fetch_assoc($qPrd)){
            $optAfd.="<option value=".$rPrd['kodeorganisasi'].">".$rPrd['namaorganisasi']."</option>";
        }
        echo $optPeriode."####".$optAfd;
        break;
        default:
        break;
}
?>
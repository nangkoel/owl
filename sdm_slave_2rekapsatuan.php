<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$proses=$_GET['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$optRegional=  makeOption($dbname, 'bgt_regional', 'regional,regional');

if($periode==''){
    exit("error:Period can't empty");
}else{
    $whr.=" and left(tanggal,7)='".$periode."'";
}
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    if($kdUnit==''){
       exit("error:Unit can't empty");
    }else{
        if(isset($optRegional[$kdUnit])){
            $whr.="  and left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$kdUnit."')";
        }else{
            $whr.=" and  left(kodeorg,4)='".$kdUnit."'";
        }
    }   
}else{
    if($kdUnit==''){
         exit("error:Unit can't empty");
//         if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
//            $whr.=" and left(kodeorg,4) in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
//         }
    }
    else{
        $whr.="  and left(kodeorg,4)='".$kdUnit."'";
    }
}
$sRekap="select sum(hasilkerja) as hasil,sum(jhk) as jhk,sum(umr) as umr,sum(insentif) as insentif,kodekegiatan,kodeorg from ".$dbname.".kebun_kehadiran_vw "
       ."where kodekegiatan in (select distinct kodekegiatan from ".$dbname.".setup_kegiatan where right(namakegiatan,3)='[S]') and jurnal=1 ".$whr." "
      . " group by kodeorg,kodekegiatan order by kodeorg,kodekegiatan asc";
//echo $sRekap;
$qRekap=  mysql_query($sRekap) or die(mysql_error($conn));
while($rRekap=  mysql_fetch_assoc($qRekap)){
        $lstKeg[$rRekap['kodekegiatan']]=$rRekap['kodekegiatan'];
        $lstBlk[$rRekap['kodeorg']]=$rRekap['kodeorg'];
        $lstHslKrj[$rRekap['kodeorg'].$rRekap['kodekegiatan']]=$rRekap['hasil'];
        $lstJmlHk[$rRekap['kodeorg'].$rRekap['kodekegiatan']]=$rRekap['jhk'];
        $lstUpah[$rRekap['kodeorg'].$rRekap['kodekegiatan']]=$rRekap['umr'];
        $lstPremi[$rRekap['kodeorg'].$rRekap['kodekegiatan']]=$rRekap['insentif'];
}
$garis=0;
if($proses=='excel'){
    $garis=1;
   $bgcolordt=" bgcolor=#DEDEDE";
}
array_multisort($lstKeg, 'SORT_ASC');
array_multisort($lstBlk, 'SORT_ASC');
$tab.="<table cellpadding=1 cellspacing=1 border='".$garis."' class=sortable><thead>";
$tab.="<tr ".$bgcolordt." align=center>";
$tab.="<td>".$_SESSION['lang']['nomor']."</td>";
$tab.="<td>".$_SESSION['lang']['kodekegiatan']."</td>";
$tab.="<td>".$_SESSION['lang']['namakegiatan']."</td>";
$tab.="<td>".$_SESSION['lang']['hasilkerjad']."</td>";
$tab.="<td>".$_SESSION['lang']['kodeblok']."</td>";
$tab.="<td>".$_SESSION['lang']['bloklama']."</td>";
$tab.="<td>".$_SESSION['lang']['tahuntanam']."</td>";
$tab.="<td>".$_SESSION['lang']['jhk']."</td>";
$tab.="<td>".$_SESSION['lang']['upah']."</td>";
$tab.="<td>".$_SESSION['lang']['insentif']."</td>";
$tab.="<td>Bruto</td></thead><tbody>";
foreach($lstKeg as $dtKeg){
foreach($lstBlk as $dtBlok){
            $whrkeg="kodekegiatan='".$dtKeg."'";
            $whrblk=" kodeorg='".$dtBlok."'";
            $optNmKeg=  makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $whrkeg);
            $optThn=  makeOption($dbname, 'setup_blok', 'kodeorg,tahuntanam', $whrblk);
            $optBlk=  makeOption($dbname, 'setup_blok', 'kodeorg,bloklama', $whrblk);
            if($lstHslKrj[$dtBlok.$dtKeg]!=''){
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$dtKeg."</td>";
                $tab.="<td>".$optNmKeg[$dtKeg]."</td>";
                $tab.="<td align=right>".$lstHslKrj[$dtBlok.$dtKeg]."</td>";
                $tab.="<td>".$dtBlok."</td>";
                $tab.="<td>".$optBlk[$dtBlok]."</td>";
                $tab.="<td>".$optThn[$dtBlok]."</td>";
                $tab.="<td align=right>".number_format($lstJmlHk[$dtBlok.$dtKeg],2)."</td>";
                $tab.="<td align=right>".number_format($lstUpah[$dtBlok.$dtKeg],2)."</td>";
                $tab.="<td align=right>".number_format($lstPremi[$dtBlok.$dtKeg],2)."</td>";
                $bruto=$lstUpah[$dtBlok.$dtKeg]+$lstPremi[$dtBlok.$dtKeg];
                $tab.="<td align=right>".number_format($bruto,2)."</td>";
                $tab.="</tr>";
                $bruto=0;
            }
        }
}
$tab.="</tbody></table>";
switch($proses){
    case'preview':
        echo $tab;
    break;
case'excel':
 $tab.="Print Time:".date('d-m-Y H:i:s')."<br>By:".$_SESSION['empl']['name'];
 $nop_="RekapUpahsatuan".$periode."__".$kdUnit;
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
}
?>
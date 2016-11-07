<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];

$pt=$_POST['pt'];
$per=$_POST['per'];
$tp=$_POST['tp'];
if($proses=='excel')
{
    $pt=$_GET['pt'];
    $per=$_GET['per'];
    $tp=$_GET['tp'];
}

$optNmKomponen=  makeOption($dbname, 'sdm_ho_component', 'id,name');

if($proses=='getkebun'){
    $optkebun="<option value=''>".$_SESSION['lang']['all']."</option>";
//    if ($inti=='inti'){
//        $whr=" and namaorganisasi not like 'PLASMA%'";
//    } else if ($inti=='plasma'){
//        $whr=" and namaorganisasi like 'PLASMA%'";
//    } else {
//        $whr="";
//    }
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
        where induk='".$pt."'".$whr." and tipe in ('KANWIL','KEBUN','PABRIK')";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=  mysql_fetch_assoc($query))
    {
        $optkebun.="<option value=".$res['kodeorganisasi'].">".$res['namaorganisasi']."</option>";
    }
    if($pt=='')$optkebun="<option value=''></option>";
    echo $optkebun;
}


#ambil komponen penambah
#include potongan HK
$iPlus="select distinct idkomponen from ".$dbname.".sdm_gaji where kodeorg in (select kodeorganisasi from "
        . " ".$dbname.".organisasi where induk='".$pt."') and idkomponen in (select id from ".$dbname.".sdm_ho_component"
        . " where plus=1) or idkomponen='37' ";
$nPlus=mysql_query($iPlus) or die (mysql_error($conn));
while($dPlus=mysql_fetch_assoc($nPlus))
{
    $noPlus+=1;
    $idKomponenPlus[$dPlus['idkomponen']]=$dPlus['idkomponen'];
}
//echo $iPlus;


#ambil komponen gaji pengurang
# potongan HK dimove ke penambah, dan simpanan di keluarkan sesuai format
$iMin="select distinct idkomponen from ".$dbname.".sdm_gaji where kodeorg in (select kodeorganisasi from "
        . " ".$dbname.".organisasi where induk='".$pt."') and idkomponen in (select id from ".$dbname.".sdm_ho_component"
        . " where plus=0) and idkomponen not in ('37','61') ";
$nMin=mysql_query($iMin) or die (mysql_error($conn));
while($dMin=mysql_fetch_assoc($nMin))
{
    $noMin+=1;
    $idKomponenMin[$dMin['idkomponen']]=$dMin['idkomponen'];
}


#bentuk list organisasi
$iOrg="select kodeorganisasi,namaorganisasi,namaalias from ".$dbname.".organisasi ";
if ($unit!=''){
    $iOrg.="where kodeorganisasi='".$unit."' or (induk='".$unit."' and tipe in ('AFDELING','TRAKSI')) order by namaorganisasi asc";
} else {
    $iOrg.="where induk='".$pt."' order by namaorganisasi asc";
}
$nOrg=mysql_query($iOrg) or die (mysql_error($conn));
while($dOrg=mysql_fetch_assoc($nOrg))
{
    $kodeOrg[$dOrg['kodeorganisasi']]=$dOrg['kodeorganisasi'];
    $namaOrg[$dOrg['kodeorganisasi']]=$dOrg['namaalias'];
}
if ($tp!=''){
    $whrtp=" and b.tipekaryawan=".$tp;
}
#ambil jumlah tenaga kerja
if ($unit!=''){
    $iTk="select count(distinct(a.karyawanid)) as karyawan,a.periodegaji,b.subbagian as kodeorg from ".$dbname.".sdm_gaji a"
            . " left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
            . " where a.kodeorg='".$unit."' and a.periodegaji='".$per."'".$whrtp." group by periodegaji,b.subbagian";
} else {
    $iTk="select count(distinct(a.karyawanid)) as karyawan,a.periodegaji,a.kodeorg from ".$dbname.".sdm_gaji a"
            . " left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
            . " where a.kodeorg in (select kodeorganisasi from "
            . " ".$dbname.".organisasi where induk='".$pt."') and a.periodegaji='".$per."'".$whrtp." group by kodeorg,periodegaji";
}
$nTk=mysql_query($iTk) or die (mysql_error($conn));
while($dTk=mysql_fetch_assoc($nTk))
{
    if ($dTk['kodeorg']=='')$dTk['kodeorg']=$unit;
    $tk[$dTk['kodeorg']][$dTk['periodegaji']]=$dTk['karyawan'];
}


#ambil upah semuanya
if ($unit!=''){
    $iUpah="select sum(jumlah) as jumlah,periodegaji,b.subbagian as kodeorg,idkomponen from ".$dbname.".sdm_gaji a"
            . " left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
            . " where kodeorg='".$unit."' and periodegaji='".$per."'".$whrtp.$whrsub." group by idkomponen,periodegaji,b.subbagian";
} else {
    $iUpah="select sum(jumlah) as jumlah,periodegaji,kodeorg,idkomponen from ".$dbname.".sdm_gaji a"
            . " left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid"
            . " where kodeorg in (select kodeorganisasi from "
            . " ".$dbname.".organisasi where induk='".$pt."') and periodegaji='".$per."'".$whrtp." group by idkomponen,kodeorg,periodegaji";
}
$nUpah=mysql_query($iUpah) or die (mysql_error($conn));
while($dUpah=mysql_fetch_assoc($nUpah))
{
    if ($dUpah['kodeorg']=='')$dUpah['kodeorg']=$unit;
        if($dUpah['idkomponen']=='37') {
             $upah[$dUpah['kodeorg']][$dUpah['periodegaji']][$dUpah['idkomponen']]=$dUpah['jumlah']*-1;
        } else {
            $upah[$dUpah['kodeorg']][$dUpah['periodegaji']][$dUpah['idkomponen']]=$dUpah['jumlah'];
        }
//    if ($unit!=''){
//        if($dUpah['idkomponen']=='37') {
//             $upah[$dUpah['kodeorg']][$dUpah['periodegaji']][$dUpah['idkomponen']][$dUpah['subbagian']]=$dUpah['jumlah']*-1;
//        } else {
//            $upah[$dUpah['kodeorg']][$dUpah['periodegaji']][$dUpah['idkomponen']][$dUpah['subbagian']]=$dUpah['jumlah'];
//        }
//    } else {
//    }
}

if ($proses == 'excel') 
{
    $stream = "<table class=sortable cellspacing=1 border=1>";
} else 
{
    $stream = "<table class=sortable cellspacing=1>";
}

$stream.="<thead class=rowheader>
    <tr class=rowheader>
       <td rowspan=2 bgcolor=#CCCCCC align=center>No</td>
       <td rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['kodeorganisasi']."</td>
       <td rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namaorganisasi']."</td>
       <td rowspan=2 bgcolor=#CCCCCC align=center>Tenaga Kerja</td>
       <td  bgcolor=#CCCCCC colspan=$noPlus align=center>".$_SESSION['lang']['penambah']."</td>
       <td  bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['penambah']."</td>
       <td  bgcolor=#CCCCCC colspan=$noMin align=center>".$_SESSION['lang']['pengurang']."</td>
       <td  bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['pengurang']."</td> 
       <td  bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['gajiBersih']."</td> 
       <td  bgcolor=#CCCCCC rowspan=2 align=center>Simpanan</td> 
       <td  bgcolor=#CCCCCC rowspan=2 align=center>".$_SESSION['lang']['gaji']." ".$_SESSION['lang']['diterima']."</td> 
    </tr>";
$stream.="<tr>";
foreach($idKomponenPlus as $idKomPlus)
{
     $stream.="<td  bgcolor=#CCCCCC align=center>".$optNmKomponen[$idKomPlus]."</td>";
}

foreach($idKomponenMin as $idKomMin)
{
     $stream.="<td  bgcolor=#CCCCCC align=center>".$optNmKomponen[$idKomMin]."</td>";
}
$stream.="</tr>";
$stream.="</thead>";

if ($unit!=''){
    foreach($kodeOrg as $kdOrg)
    {
        $subtotPlus[$kdOrg]=0;
        $subtotMin[$kdOrg]=0;
        $no+=1;
        $stream.="<tr class=rowcontent>
        <td>".$no."</td>
        <td>".$kdOrg."</td>
        <td>".$namaOrg[$kdOrg]."</td>
        <td align=right>".number_format($tk[$kdOrg][$per])."</td>";
        foreach($idKomponenPlus as $idKomPlus)
        {
            $stream.="<td align=right>".number_format($upah[$kdOrg][$per][$idKomPlus])."</td>";
            $tot[$idKomPlus]+=$upah[$kdOrg][$per][$idKomPlus];
            $subtotPlus[$kdOrg]+=$upah[$kdOrg][$per][$idKomPlus];
        }
        $stream.="<td align=right>".number_format($subtotPlus[$kdOrg])."</td>";
        foreach($idKomponenMin as $idKomMin)
        {
             $stream.="<td align=right>".number_format($upah[$kdOrg][$per][$idKomMin])."</td>";
             $tot[$idKomMin]+=$upah[$kdOrg][$per][$idKomMin];
             $subtotMin[$kdOrg]+=$upah[$kdOrg][$per][$idKomMin];
        }
        $nettoGaji=$subtotPlus[$kdOrg]-$subtotMin[$kdOrg];
        $pendapatan=$nettoGaji-$upah[$kdOrg][$per]['61'];
        $stream.="<td align=right>".number_format($subtotMin[$kdOrg])."</td>";
        $stream.="<td align=right>".number_format($nettoGaji)."</td>";
        $stream.="<td align=right>".number_format($upah[$kdOrg][$per]['61'])."</td>";
        $stream.="<td align=right>".number_format($pendapatan)."</td>";
        $stream.="</tr>";  

        $totTk+=$tk[$kdOrg][$per];
        $totPlus+=$subtotPlus[$kdOrg];
        $totMin+=$subtotMin[$kdOrg];
        $totSimpanan+=$upah[$kdOrg][$per]['61'];
        $totNettoGaji+=$nettoGaji;
        $totPendapatan+=$pendapatan;   
    }
} else {
    foreach($kodeOrg as $kdOrg)
    {
        $subtotPlus[$kdOrg]=0;
        $subtotMin[$kdOrg]=0;
        $no+=1;
        $stream.="<tr class=rowcontent>
        <td>".$no."</td>
        <td>".$kdOrg."</td>
        <td>".$namaOrg[$kdOrg]."</td>
        <td align=right>".number_format($tk[$kdOrg][$per])."</td>";
        foreach($idKomponenPlus as $idKomPlus)
        {
            $stream.="<td align=right>".number_format($upah[$kdOrg][$per][$idKomPlus])."</td>";
            $tot[$idKomPlus]+=$upah[$kdOrg][$per][$idKomPlus];
            $subtotPlus[$kdOrg]+=$upah[$kdOrg][$per][$idKomPlus];
        }
        $stream.="<td align=right>".number_format($subtotPlus[$kdOrg])."</td>";
        foreach($idKomponenMin as $idKomMin)
        {
             $stream.="<td align=right>".number_format($upah[$kdOrg][$per][$idKomMin])."</td>";
             $tot[$idKomMin]+=$upah[$kdOrg][$per][$idKomMin];
             $subtotMin[$kdOrg]+=$upah[$kdOrg][$per][$idKomMin];
        }
        $nettoGaji=$subtotPlus[$kdOrg]-$subtotMin[$kdOrg];
        $pendapatan=$nettoGaji-$upah[$kdOrg][$per]['61'];
        $stream.="<td align=right>".number_format($subtotMin[$kdOrg])."</td>";
        $stream.="<td align=right>".number_format($nettoGaji)."</td>";
        $stream.="<td align=right>".number_format($upah[$kdOrg][$per]['61'])."</td>";
        $stream.="<td align=right>".number_format($pendapatan)."</td>";
        $stream.="</tr>";  

        $totTk+=$tk[$kdOrg][$per];
        $totPlus+=$subtotPlus[$kdOrg];
        $totMin+=$subtotMin[$kdOrg];
        $totSimpanan+=$upah[$kdOrg][$per]['61'];
        $totNettoGaji+=$nettoGaji;
        $totPendapatan+=$pendapatan;   
    }
}
$stream.="<thead><tr class=rowcontent>";
$stream.="<td colspan=3>".$_SESSION['lang']['total']."</td>
          <td align=right>".number_format($totTk)."</td>";
foreach($idKomponenPlus as $idKomPlus)
{
     $stream.="<td align=right>".number_format($tot[$idKomPlus])."</td>";
}
 $stream.="<td align=right>".number_format($totPlus)."</td>";
foreach($idKomponenMin as $idKomMin)
{
     $stream.="<td align=right>".number_format($tot[$idKomMin])."</td>";
}
$stream.="<td align=right>".number_format($totMin)."</td>";
$stream.="<td align=right>".number_format($totNettoGaji)."</td>";
$stream.="<td align=right>".number_format($totSimpanan)."</td>";
$stream.="<td align=right>".number_format($totPendapatan)."</td>";
$stream.="</tr></thead>";
$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="lapora_Rekap_Gaji_".$pt."_".$per;
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
		break;	
}
?>
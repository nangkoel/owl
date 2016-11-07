<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['kdPt']==''?$kdPt=$_GET['kdPt']:$kdPt=$_POST['kdPt'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['regDt']==''?$regDt=$_GET['regDt']:$regDt=$_POST['regDt'];
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if($proses=='excel'||$proses=='preview')
{
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
}
if($regDt!='')
{
    $whrtd="regional='".$regDt."'";
    if($regDt=='SUMSEL')
    {
        $whrtd=" regional in ('SUMSEL','LAMPUNG')";
    }
    $sUnit="select distinct kodeunit from ".$dbname.".bgt_regional_assignment where ".$whrtd."";    
}
else
{
    $sUnit="select distinct kodeunit from ".$dbname.".bgt_regional_assignment order by kodeunit";    
}
    $arte="";
    $ader=0;
    $qUnit=mysql_query($sUnit) or die(mysql_error($conn));
    while($rUnit=mysql_fetch_assoc($qUnit))
    {
        $ader+=1;
        if($ader==1)
        {
            $arte.="'".$rUnit['kodeunit']."'";
        }
        else
        {
            $arte.=",'".$rUnit['kodeunit']."'";
        }
    }
    $whrbgt=" and substr(kodeorg,1,4) in (".$arte.")";
    $whrKapt=" and substr(kodeunit,1,4) in (".$arte.")";
    $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi in (".$arte.")";
    //exit("Error:".$sPt);
    $qPt=mysql_query($sPt) or die(mysql_error($conn));
    while($rPt=  mysql_fetch_assoc($qPt))
    {
        $ert+=1;
        if($ert==1)
        {
            $dtPete.="'".$rPt['induk']."'";
        }
        else
        {
            $dtPete.=",'".$rPt['induk']."'";
        }
    }
    $whr=" and kodeorg in (".$dtPete.")";
    
if($kdPt!='')
{
    $whr=" and kodeorg='".$kdPt."'";
    $sBgt="select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdPt."'";
    $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
    while($rBgt=  mysql_fetch_assoc($qBgt))
    {
        $ater+=1;
        if($ater==1)
        {
            $aretd="'".$rBgt['kodeorganisasi']."'";
        }
        else
        {
            $aretd.=",'".$rBgt['kodeorganisasi']."'";
        }
    }
    $whrbgt=" and substr(kodeorg,1,4) in (".$aretd.")";
    $whrKapt=" and substr(kodeunit,1,4) in (".$aretd.")";
}

$arr="##periode##judul##kdPt##regDt";
$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];
if($proses=='excel'||$proses=='preview')
{

##bulan ini##
#total annual pembelian kapital dan non kapital relaisasi#
$sTot="select distinct sum(jumlahpesan) as total  from 
       ".$dbname.".log_po_vw where substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset order by kodetipe) 
       and tanggal like '".$periode."%' ".$whr."";
//exit("Error:".$sTot);
$qTot=mysql_query($sTot) or die(mysql_error($sTot));
while($rTot=mysql_fetch_assoc($qTot))
{
    $totKapi+=$rTot['total'];
}

$sTot="select distinct sum(jumlahpesan) as total,matauang from 
       ".$dbname.".log_po_vw where  substr(kodebarang,1,1) not in ('8','9') 
       and tanggal like '".$periode."%'  ".$whr."";
$qTot=mysql_query($sTot) or die(mysql_error($sTot));
while($rTot=mysql_fetch_assoc($qTot))
{
    $totNonKapi+=$rTot['total'];
}
#end total annual pembelian kapital dan non kapital relaisasi#

#pembelian kapital dan non kapital realisasi s.d bulan ini mulai#
$sTot="select distinct sum(jumlahpesan) as total from 
       ".$dbname.".log_po_vw where substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset order by kodetipe)
       ".$whr." and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'";
 //echo $sTot;
$qTot=mysql_query($sTot) or die(mysql_error($sTot));
while($rTot=mysql_fetch_assoc($qTot))
{
    $totKapiSmp+=$rTot['total'];
}

$sTot="select distinct sum(jumlahpesan) as total from 
       ".$dbname.".log_po_vw where substr(kodebarang,1,1) not in ('8','9') ".$whr."
       and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'";
$qTot=mysql_query($sTot) or die(mysql_error($sTot));
while($rTot=mysql_fetch_assoc($qTot))
{
    $totNonKapiSmp+=$rTot['total'];
}
#pembelian kapital dan non kapital realisasi s.d bulan ini sudah disini#


strlen($bulan)<1?$bln="0".$bulan:$bln=$bulan;

#anggaran kapital bulan ini mulai#
$sBgt="select distinct sum(k".$bln.") as total from 
      ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' ".$whrKapt."";
$qBgt=mysql_query($sBgt) or die(mysql_error($conn));
$rBgt=mysql_fetch_assoc($qBgt);
$bgtKapital=0;

$sBgt="select distinct sum(fis".$bln.") as total from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
      and substr(kodebudget,1,1)='M' ".$whrbgt."";
$qBgt=mysql_query($sBgt) or die(mysql_error($conn));
$rBgt=mysql_fetch_assoc($qBgt);
$bgtNonKapital=$rBgt['total'];
#anggaran kapital bulan ini end#

#anggaran s.d bulan ini mulai#
$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="k0".$W;
    else $jack="k".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";


$aresta="SELECT sum(".$addstr.") as total FROM ".$dbname.".bgt_kapital_vw
        WHERE tahunbudget = '".$tahun."' ".$whrKapt."";
$query=mysql_query($aresta) or die(mysql_error($conn));
$res=mysql_fetch_assoc($query);
$bgtKapSmp=0;


$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="fis0".$W;
    else $jack="fis".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";


$aresta="SELECT sum(".$addstr.") as total FROM ".$dbname.".bgt_budget_detail
         WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgt."";
$query=mysql_query($aresta) or die(mysql_error($conn));
$res=mysql_fetch_assoc($query);
$bgtNonKap=$res['total'];
#anggaran s.d bulan ini end#

#annual non kapital dan kapital mulai#
$aresta="SELECT sum(harga) as total FROM ".$dbname.".bgt_kapital_vw
        WHERE tahunbudget = '".$tahun."' ".$whrKapt."";
$query=mysql_query($aresta) or die(mysql_error($conn));
$res=mysql_fetch_assoc($query);
$annualKap=0;

$aresta="SELECT sum(jumlah) as total FROM ".$dbname.".bgt_budget_detail
         WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."' ".$whrbgt."";
$query=mysql_query($aresta) or die(mysql_error($conn));
$res=mysql_fetch_assoc($query);
$annualNonKap=$res['total'];
#annual non kapital dan kapital end#


$lnkKapital="style='cursor:pointer' onclick=getDetailKap2('".$arr."')";
$lnkNonKap="style='cursor:pointer' onclick=getDetailNonKap2('".$arr."')";
$bg="";
$brdr=0;
if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=4 align=left><font size=3>".$judul."</font></td>
        <td colspan=3 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr>    
</table>";
}

if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">Kelompok</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center rowspan=2 ".$bg.">ANNUAL BUDGET</td>
    <td align=center rowspan=2 ".$bg.">%</td>
    </tr>
    <tr>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">%</td>
    <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center ".$bg.">%</td>
    </tr>
    </thead>
    <tbody>
";
    $tab.="<tr class=rowcontent ".$lnkKapital.">";
    $tab.="<td>KAPITAL</td>";
    $tab.="<td align=right>".number_format($totKapi,0)."</td>";
    $tab.="<td align=right>".number_format($bgtKapital,0)."</td>";
    @$persenBlnini=($totKapi/$bgtKapital)*100;
    $tab.="<td align=right>".number_format($persenBlnini,0)."</td>";
    $tab.="<td align=right>".number_format($totKapiSmp,0)."</td>";
    $tab.="<td align=right>".number_format($bgtKapSmp,0)."</td>";
    @$persenSmpBlnini=($totKapiSmp/$bgtKapSmp)*100;
    $tab.="<td align=right>".number_format($persenSmpBlnini,0)."</td>";
    @$persenAnnual=($totKapiSmp/$annualKap)*100;
    $tab.="<td align=right>".number_format($annualKap,0)."</td>";
    $tab.="<td align=right>".number_format($persenAnnual,0)."</td>";
    $tab.="</tr>";
    $tab.="<tr class=rowcontent ".$lnkNonKap.">";
    $tab.="<td>NON KAPITAL</td>";
    $tab.="<td align=right>".number_format($totNonKapi,0)."</td>";
    $tab.="<td align=right>".number_format($bgtNonKapital,0)."</td>";
    @$prsnBlnini=($totNonKapi/$bgtNonKapital)*100;
    $tab.="<td align=right>".number_format($prsnBlnini,0)."</td>";
    $tab.="<td align=right>".number_format($totNonKapiSmp,0)."</td>";
    $tab.="<td align=right>".number_format($bgtNonKap,0)."</td>";
    @$prsnSmpBlnini=($totNonKapiSmp/$bgtNonKap)*100;
    $tab.="<td align=right>".number_format($prsnSmpBlnini,0)."</td>";
    @$prsnAnnual=($totNonKapiSmp/$annualNonKap)*100;
    $tab.="<td align=right>".number_format($annualNonKap,0)."</td>";
    $tab.="<td align=right>".number_format($prsnAnnual,0)."</td>";
    $tab.="</tr>";
    $grReal=$totKapi+$totNonKapi;
    $grBudget=$bgtKapital+$bgtNonKapital;
    $grPersen=$grReal/$grBudget*100;
    $grSmpBgt=$bgtKapSmp+$bgtNonKap;
    $grSmp=$totKapiSmp+$totNonKapiSmp;
    $grPersenSmp=$grSmp/$grSmpBgt*100;
    $grAnnual=$annualKap+$annualNonKap;
    $grPersenAnn=$grSmp/$grAnnual*100;
    $tab.="<tr class=rowcontent>";
    $tab.="<td>GRAND TOTAL</td>";
    $tab.="<td align=right>".number_format($grReal,0)."</td>";
    $tab.="<td align=right>".number_format($grBudget,0)."</td>";
    $tab.="<td align=right>".number_format($grPersen,0)."</td>";
    $tab.="<td align=right>".number_format($grSmp,0)."</td>";
    $tab.="<td align=right>".number_format($grSmpBgt,0)."</td>";
    $tab.="<td align=right>".number_format($grPersenSmp,0)."</td>";
    $tab.="<td align=right>".number_format($grAnnual,0)."</td>";
    $tab.="<td align=right>".number_format($grPersenAnn,0)."</td>";
    $tab.="</tr>";
    $tab.="</tbody></table>";
}		
switch($proses)
{
    case'preview':
    //    exit("error:masuk");
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("His");
    $nop_="totalPembelianFis_".$dte;
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
    case'getDetPt':
    $arte="";
    $optPt="";
    $ader=0;
    $sUnit="select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_POST['regional']."'";
    //exit("Error:".$sUnit);
    $qUnit=mysql_query($sUnit) or die(mysql_error($conn));
    while($rUnit=mysql_fetch_assoc($qUnit))
    {
        $ader+=1;
        if($ader==1)
        {
            $arte.="'".$rUnit['kodeunit']."'";
        }
        else
        {
            $arte.=",'".$rUnit['kodeunit']."'";
        }
    }
    $optPt="<option value=''>".$_SESSION['lang']['all']."</option>";
    $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi in (".$arte.")";
    //exit("Error:".$sPt);
    $qPt=mysql_query($sPt) or die(mysql_error($conn));
    while($rPt=  mysql_fetch_assoc($qPt))
    {
        $optPt.="<option value='".$rPt['induk']."'>".$optNm[$rPt['induk']]."</option>";
    }
    echo $optPt;
    break;
    default:
    break;
}
	
?>

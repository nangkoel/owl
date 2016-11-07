<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['tipe']==''?$tipe=$_GET['tipe']:$tipe=$_POST['tipe'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdPt']==''?$kdPt=$_GET['kdPt']:$kdPt=$_POST['kdPt'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['klmpkbrg']==''?$klmpkbrg=$_GET['klmpkbrg']:$klmpkbrg=$_POST['klmpkbrg'];
$_POST['regDt']==''?$regDt=$_GET['regDt']:$regDt=$_POST['regDt'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];
strlen($bulan)<1?$bln="0".$bulan:$bln=$bulan;
//exit("Error:".$periode);
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNamaBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKlmpbrg=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
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



if($periode=='')
{
    exit("Error:Field Tidak Boleh Kosong adasd");
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
    $sBgt="select distinct kodeorganisasi from ".$dbname.".organisasi where  induk='".$kdPt."'";
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
#realiasasi  non kapital bulan ini mulai#
$sData="SELECT SUBSTR( kodebarang, 1, 2 ) AS klmpkBrg, SUM(jumlahpesan) AS totalharga 
        FROM  ".$dbname.".`log_po_vw` 
        WHERE SUBSTR( tanggal, 1, 7 ) =  '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90') ".$whr."
        GROUP BY SUBSTR( kodebarang, 1, 2 ) ";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData))
{
    $dtBarang[$rData['klmpkBrg']]=$rData['klmpkBrg'];
    $rData['totalharga']=$rData['totalharga'];
    $toHrg[$rData['klmpkBrg']]+=$rData['totalharga'];
}
#realiasasi non kapital bulan ini selesai#

#anggaran non kapital mulai#
$sBgt="select distinct sum(fis".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
      and substr(kodebudget,1,1)='M'  ".$whrbgt." group by substr(kodebudget,3,2)";
$qBgt=mysql_query($sBgt) or die(mysql_error($conn));
while($rBgt=mysql_fetch_assoc($qBgt))
{
    $dtBarang[$rBgt['klmpkBrg']]=$rBgt['klmpkBrg'];
    $toHrgBgt[$rBgt['klmpkBrg']]+=$rBgt['total'];
}
#anggaran non kapital ini selesai#

#realiasasi  non kapital smp dgn bulan ini mula#
$sData="SELECT SUBSTR( kodebarang, 1, 2 ) AS klmpkBrg, SUM(jumlahpesan) AS totalharga 
        FROM  ".$dbname.".`log_po_vw` 
        WHERE SUBSTR( tanggal, 1, 7 ) between '".$tahun."-01' and '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90')  ".$whr."
        GROUP BY SUBSTR( kodebarang, 1, 2 ) ";
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData))
{
    $dtBarang[$rData['klmpkBrg']]=$rData['klmpkBrg'];
    $rData['totalharga']=$rData['totalharga'];
    $toHrgBln[$rData['klmpkBrg']]+=$rData['totalharga'];
}
#realiasasi non kapital smp dgn bulan ini selesai#

#anggaran non kapital mulai#
$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="fis0".$W;
    else $jack="fis".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";
$sBgt="select distinct sum(".$addstr.") as total,substr(kodebudget,3,2) as klmpkBrg  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
      and substr(kodebudget,1,1)='M'  ".$whrbgt." group by substr(kodebudget,3,2)";
$qBgt=mysql_query($sBgt) or die(mysql_error($conn));
while($rBgt=mysql_fetch_assoc($qBgt))
{
    $dtBarang[$rBgt['klmpkBrg']]=$rBgt['klmpkBrg'];
    $toHrgBgtBln[$rBgt['klmpkBrg']]+=$rBgt['total'];
}
#anggaran non kapital ini selesai#

#annual budget#
$aresta="SELECT sum(rupiah) as total,substr(kodebudget,3,2) as klmpkBrg FROM ".$dbname.".bgt_budget_detail
         WHERE substr(kodebudget,1,1)='M' and tahunbudget = '".$tahun."'  ".$whrbgt." group by substr(kodebudget,3,2)";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dtAnn[$res['klmpkBrg']]=$res['total'];
}
#annual budget selesai#

#ambil nama kelompok barang#
$sKd="select distinct SUBSTR( kodebarang, 1, 2) as klmpk  FROM  ".$dbname.".`log_po_vw` 
           WHERE SUBSTR( tanggal, 1, 7 ) between '".$tahun."-01'
           and '".$periode."' AND SUBSTR( kodebarang, 1, 2 ) NOT IN ('80',  '90')  ".$whr."";
$qKd=mysql_query($sKd) or die(mysql_error($conn));
while($rKd=mysql_fetch_assoc($qKd))
{
    $der+=1;
    if($der==1)
    {
        $kodeklmpk=$rKd['klmpk'];
    }
    else
    {
        $kodeklmpk.=",".$rKd['klmpk'];
    }
}
$sNamSup="select distinct kelompok,substr(kode,1,2) as kdKlmpk from ".$dbname.".log_5klbarang where  substr(kode,1,2) in 
          (".$kodeklmpk.")";
//echo $sNamSup;
$qNamSup=mysql_query($sNamSup) or die(mysql_error($conn));
while($rNamSup=  mysql_fetch_assoc($qNamSup))
{
    if($rNamSup['kdKlmpk']!=$kepup)
    {
        $dtNama[$rNamSup['kdKlmpk']]=$rNamSup['kelompok'];
        $kepup=$rNamSup['kdKlmpk'];
    }
    else
    {
        $dtNama[$rNamSup['kdKlmpk']].=",".$rNamSup['kelompok'];
    }
}
#ambil kelompok barang end#
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

    
			
switch($proses)
{
    case'getDetailNonKap':
    //    exit("error:masuk");
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    $tab.=$judul;
    $tab.="<input type=hidden id=periodeDet value='".$periode."' /><table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['kelompokbarang']."</td>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['namakelompok']."</td>
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

    foreach($dtBarang as $kdKlmpkBarang)
    {
        $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetBrg2('".$kdKlmpkBarang."','".$arr."')>";
        $tab.="<td>".$kdKlmpkBarang."0</td>";
        $tab.="<td>".$dtNama[$kdKlmpkBarang]."</td>";
        $tab.="<td align=right>".number_format($toHrg[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgt[$kdKlmpkBarang],0)."</td>";
        @$prsen[$kdKlmpkBarang]=$toHrg[$kdKlmpkBarang]/$toHrgBgt[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsen[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgtBln[$kdKlmpkBarang],0)."</td>";
        @$prsenBln[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$toHrgBgtBln[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($dtAnn[$kdKlmpkBarang],0)."</td>";
        @$prsenAnn[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$dtAnn[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdKlmpkBarang],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$toHrg[$kdKlmpkBarang];
        $totBudget+=$toHrgBgt[$kdKlmpkBarang];
        $totBlnReal+=$toHrgBln[$kdKlmpkBarang];
        $totBlnBgt+=$toHrgBgtBln[$kdKlmpkBarang];
        $totAnn+=$dtAnn[$kdKlmpkBarang];
    }
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".number_format($totRealisasi,0)."</td>";
        $tab.="<td align=right>".number_format($totBudget,0)."</td>";
        @$prsenDt=$totRealisasi/$totBudget*100;
        $tab.="<td align=right>".number_format($prsenDt,0)."</td>";
        $tab.="<td align=right>".number_format($totBlnReal,0)."</td>";
        $tab.="<td align=right>".number_format($totBlnBgt,0)."</td>";
        @$prsenBlnDt=$totBlnReal/$totBlnBgt*100;
        $tab.="<td align=right>".number_format($prsenBlnDt,0)."</td>";
        $tab.="<td align=right>".number_format($totAnn,0)."</td>";
        @$prsenAnnDt=$totBlnReal/$totAnn*100;
        $tab.="<td align=right>".number_format($prsenAnnDt,0)."</td>";
        $tab.="</tr>";
    $tab.="<tr><td colspan=10>
           <button onclick=\"zBack()\" class=\"mybutton\">Back</button>
           <button onclick=\"zExcel(event,'log_slave_proc_brg_detail_kap.php','".$arr."','reportcontainer1')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>
           </td></tr>";
    $tab.="</tbody></table>";
    echo $tab;
    break;
    case'excel':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosongv ads");
    }
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['kelompokbarang']."</td>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['namakelompok']."</td>
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
    $arr="##periodeDet";
    foreach($dtBarang as $kdKlmpkBarang)
    {
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$kdKlmpkBarang."0</td>";
        $tab.="<td>".$dtNama[$kdKlmpkBarang]."</td>";
        $tab.="<td align=right>".number_format($toHrg[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgt[$kdKlmpkBarang],0)."</td>";
        @$prsen[$kdKlmpkBarang]=$toHrg[$kdKlmpkBarang]/$toHrgBgt[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsen[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgtBln[$kdKlmpkBarang],0)."</td>";
        @$prsenBln[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$toHrgBgtBln[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($dtAnn[$kdKlmpkBarang],0)."</td>";
        @$prsenAnn[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$dtAnn[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdKlmpkBarang],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$toHrg[$kdKlmpkBarang];
        $totBudget+=$toHrgBgt[$kdKlmpkBarang];
        $totBlnReal+=$toHrgBln[$kdKlmpkBarang];
        $totBlnBgt+=$toHrgBgtBln[$kdKlmpkBarang];
        $totAnn+=$dtAnn[$kdKlmpkBarang];
    }
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=2>".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".number_format($totRealisasi,0)."</td>";
        $tab.="<td align=right>".number_format($totBudget,0)."</td>";
        @$prsenDt=$totRealisasi/$totBudget*100;
        $tab.="<td align=right>".number_format($prsenDt,0)."</td>";
        $tab.="<td align=right>".number_format($totBlnReal,0)."</td>";
        $tab.="<td align=right>".number_format($totBlnBgt,0)."</td>";
        @$prsenBlnDt=$totBlnReal/$totBlnBgt*100;
        $tab.="<td align=right>".number_format($prsenBlnDt,0)."</td>";
        $tab.="<td align=right>".number_format($totAnn,0)."</td>";
        @$prsenAnnDt=$totBlnReal/$totAnn*100;
        $tab.="<td align=right>".number_format($prsenAnnDt,0)."</td>";
        $tab.="</tr>";
   
    $tab.="</tbody></table>";
    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    
    $nop_="detailNkapitalFis";
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
    
    case'getDetBarang':
        #get detail per kelompok barang realisasi mulai#
        $sData="select sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,2)='".$klmpkbrg."' and tanggal like '".$periode."%'
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtNmSat[$rData['kodebarang']]=$rData['satuan'];
             $dtHarga[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,2)='".$klmpkbrg."' and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtNmSat[$rData['kodebarang']]=$rData['satuan'];
             $dtHargaSmp[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(fis".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
        and substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgt[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan*/
        $sBgt="select distinct sum(".$addstr.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
              ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
              and substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgtSmp[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="SELECT sum(rupiah) as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang FROM ".$dbname.".bgt_budget_detail
                WHERE substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$klmpkbrg."' and tahunbudget = '".$tahun."' 
                group by kodebarang order by kodebarang asc";
        $qaresta=mysql_query($aresta) or die(mysql_error($conn));
        while($raresta=mysql_fetch_assoc($qaresta))
        {
            $dtHrgBgtThn[$raresta['kodebarang']]=$raresta['total'];
            $dtKdBrng[$raresta['kodebarang']]=$raresta['kodebarang'];
        }
        
        /*data budget tahunan abis disini aja*/
        
        #budget data selesai#
        ksort($dtKdBrng);
        $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
        $tab.="<thead><tr class=rowheader>";
        $tab.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
        $tab.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['namabarang']."</td>
        <td align=center colspan=4 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
        <td align=center colspan=4 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
        <td align=center rowspan=2 ".$bg.">ANNUAL BUDGET</td>
        <td align=center rowspan=2 ".$bg.">%</td>
        </tr>
        <tr>
        <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['satuan']."</td>
        <td align=center ".$bg.">%</td>
        <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['satuan']."</td>
        <td align=center ".$bg.">%</td>
        </tr></thead><tbody>";
        foreach($dtKdBrng as $dtrBrg)
        {
            if($drt!=substr($dtrBrg,0,3))
            {
                $drt=substr($dtrBrg,0,3);
                $tab.="<tr class=rowcontent>";
                $tab.="<td colspan=2>".$optKlmpbrg[$drt]."</td>";
                $tab.="<td colspan=10>&nbsp;</td>";
                $tab.="</tr>";
                $klmpBrg=substr($dtrBrg,0,2);
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td title='".$_SESSION['lang']['kodebarang']."'>".$dtrBrg."</td>";
            $tab.="<td title='".$_SESSION['lang']['namabarang']."'>".$optNamaBrg[$dtrBrg]."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHarga[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHrgBgt[$dtrBrg],0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtNmSat[$dtrBrg]."</td>";
            @$prsen[$dtrBrg]=$dtHarga[$dtrBrg]/$dtHrgBgt[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHargaSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHrgBgtSmp[$dtrBrg],0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtNmSat[$dtrBrg]."</td>";
            @$prsenSmp[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtSmp[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='ANNUAL BUDGET'>".number_format($dtHrgBgtThn[$dtrBrg],0)."</td>";
             @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtThn[$dtrBrg]*100;
             $tab.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab.="</tr>";
        }
        
        $tab.="<tr><td colspan=10>
           <button onclick=\"zBack2()\" class=\"mybutton\">Back</button>
           <button onclick=\"zExcelDet(event,'log_slave_proc_brg_detail_kap.php','".$arr."','".$klmpBrg."','reportcontainer1')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
         $tab.="</tr></tbody></table>";
        echo $tab;
    break;
   
   case'exceLgetDetBarang':
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
        #get detail per kelompok barang realisasi mulai#
        $sData="select sum(jumlahpesan*hargasatuan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,2)='".$_GET['klmpkbrg']."' and tanggal like '".$periode."%'
                group by kodebarang
                order by kodebarang asc";
       
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtNmSat[$rData['kodebarang']]=$rData['satuan'];
             $dtHarga[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,2)='".$_GET['klmpkbrg']."' and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtNmSat[$rData['kodebarang']]=$rData['satuan'];
             $dtHargaSmp[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(fis".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
        and substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$_GET['klmpkbrg']."' group by kodebarang";
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgt[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan*/
        $sBgt="select distinct sum(".$addstr.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
              ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
              and substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$_GET['klmpkbrg']."' group by kodebarang";
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgtSmp[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="SELECT sum(rupiah) as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang FROM ".$dbname.".bgt_budget_detail
                WHERE substr(kodebudget,1,1)='M' and substr(kodebudget,3,2)='".$_GET['klmpkbrg']."' and tahunbudget = '".$tahun."' 
                group by kodebarang";
        $qaresta=mysql_query($aresta) or die(mysql_error($conn));
        while($raresta=mysql_fetch_assoc($qaresta))
        {
            $dtHrgBgtThn[$raresta['kodebarang']]=$raresta['total'];
            $dtKdBrng[$raresta['kodebarang']]=$raresta['kodebarang'];
        }
        
        /*data budget tahunan abis disini aja*/
        
        #budget data selesai#
        $tab2.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
        $tab2.="<thead><tr class=rowheader>";
        $tab2.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
        $tab2.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['namabarang']."</td>
        <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
        <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
        <td align=center rowspan=2 ".$bg.">ANNUAL BUDGET</td>
        <td align=center rowspan=2 ".$bg.">%</td>
        </tr>
        <tr>
        <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['satuan']."</td>
        <td align=center ".$bg.">%</td>
        <td align=center ".$bg.">".$_SESSION['lang']['realisasi']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['anggaran']."</td>
        <td align=center ".$bg.">".$_SESSION['lang']['satuan']."</td>
        <td align=center ".$bg.">%</td>
        </tr></thead><tbody>";
        foreach($dtKdBrng as $dtrBrg)
        {
            if($drt!=substr($dtrBrg,0,3))
            {
                $drt=substr($dtrBrg,0,3);
                $tab2.="<tr class=rowcontent>";
                $tab2.="<td colspan=5>".$optKlmpbrg[$drt]."</td>";
                $tab2.="<td colspan=5>&nbsp;</td>";
                $tab2.="</tr>";
            }
            $tab2.="<tr class=rowcontent>";
            $tab2.="<td title='".$_SESSION['lang']['kodebarang']."'>".$dtrBrg."</td>";
            $tab2.="<td title='".$_SESSION['lang']['namabarang']."'>".$optNamaBrg[$dtrBrg]."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHarga[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHrgBgt[$dtrBrg],0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtNmSat[$dtrBrg]."</td>";
            @$prsen[$dtrBrg]=$dtHarga[$dtrBrg]/$dtHrgBgt[$dtrBrg]*100;
            $tab2.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHargaSmp[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHrgBgtSmp[$dtrBrg],0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtNmSat[$dtrBrg]."</td>";
            @$prsenSmp[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtSmp[$dtrBrg]*100;
            $tab2.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='ANNUAL BUDGET'>".number_format($dtHrgBgtThn[$dtrBrg],0)."</td>";
             @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtThn[$dtrBrg]*100;
             $tab2.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab2.="</tr>";
        }
        $tab2.="</tr></tbody></table>";
        $tab2.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    
    $nop_="detailBrgFis";
    if(strlen($tab2)>0)
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
        if(!fwrite($handle,$tab2))
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

    default:
    break;
}
	
?>

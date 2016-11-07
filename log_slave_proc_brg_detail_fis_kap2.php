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
$arr="##periode##judul##kdPt";
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
$sKap="select distinct kodetipe,namatipe,kelompokbarang from ".$dbname.".sdm_5tipeasset order by kodetipe";
$qKap=mysql_query($sKap) or die(mysql_error($conn));
while($rKap=mysql_fetch_assoc($qKap))
{
    $dtTipe[$rKap['kodetipe']]=$rKap['kodetipe'];
    $dtBrg[$rKap['kodetipe']]=$rKap['kelompokbarang'];
    $dtNmTipe[$rKap['kodetipe']]=$rKap['namatipe'];
}
#realisasi bulan ini mulai#
$sReal="select distinct sum(jumlahpesan) as total,matauang,substr(kodebarang,1,3) as klmpokBrg
        from ".$dbname.".log_po_vw where 
        substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset order by kodetipe)
        and tanggal like '".$periode."%' ".$whr." group by substr(kodebarang,1,3)";
$qReal=mysql_query($sReal) or die(mysql_error($conn));
while($rReal=mysql_fetch_assoc($qReal))
{
   $totKapi[$rReal['klmpokBrg']]+=$rReal['total'];
    $mtUang[$rReal['matauang']]=$rReal['matauang'];
}
#realisasi bulan ini selesai#

#realisasi s.d bulan ini mulai#
$sReal="select distinct sum(jumlahpesan) as total,matauang,substr(kodebarang,1,3) as klmpokBrg
        from ".$dbname.".log_po_vw where 
        substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset order by kodetipe)
        and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'  ".$whr." group by substr(kodebarang,1,3)";
$qReal=mysql_query($sReal) or die(mysql_error($conn));
while($rReal=mysql_fetch_assoc($qReal))
{
    $totKapiSmp[$rReal['klmpokBrg']]+=$rReal['total'];
    $mtUang[$rReal['matauang']]=$rReal['matauang'];
}
#realisasi s.d bulan ini selesai#

#budget kapital mulai#
$sKap="select distinct sum(k".$bln.") as rup,jeniskapital from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' ".$whrKapt." group by jeniskapital";
$qKap=mysql_query($sKap) or die(mysql_error($conn));
while($rKap=mysql_fetch_assoc($qKap))
{
    $dtKapBln[$rKap['jeniskapital']]=0;
}
$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="k0".$W;
    else $jack="k".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";
$sKap="select distinct sum(".$addstr.") as rup,jeniskapital from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' ".$whrKapt." group by jeniskapital";
$qKap=mysql_query($sKap) or die(mysql_error($conn));
while($rKap=mysql_fetch_assoc($qKap))
{
    $dtKapSmpBln[$rKap['jeniskapital']]=0;
}
$sYkap="select distinct sum(harga) as rup,jeniskapital from 
        ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' ".$whrKapt." group by jeniskapital";
$qYkap=mysql_query($sYkap) or die(mysql_error($conn));
while($rYkap=mysql_fetch_assoc($qYkap))
{
    $dtKapThn[$rYkap['jeniskapital']]=0;
}

#budget kapital selesai#

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
    case'getDetailKap':
    
    $tab.=$judul;
    $tab.="<input type=hidden id=periodeDet value='".$periode."' /><table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['jnsKapital']."</td>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['kapital']."</td>
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

    foreach($dtTipe as $kdTipe)
    {
        $klmpBrg=$dtBrg[$kdTipe];
        $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetBrgKap2('".$kdTipe."','".$arr."##regDt')>";
        $tab.="<td>".$kdTipe."</td>";
        $tab.="<td>".$dtNmTipe[$kdTipe]."</td>";
        $tab.="<td align=right>".number_format($totKapi[$klmpBrg],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapBln[$kdTipe],0)."</td>";
        @$prsen[$kdTipe]=$totKapi[$klmpBrg]/$dtKapBln[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsen[$kdTipe],0)."</td>";
        $tab.="<td align=right>".number_format($totKapiSmp[$klmpBrg],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapSmpBln[$kdTipe],0)."</td>";
        @$prsenBln[$kdTipe]=$totKapiSmp[$klmpBrg]/$dtKapSmpBln[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdTipe],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapThn[$kdTipe],0)."</td>";
        @$prsenAnn[$kdTipe]=$totKapiSmp[$klmpBrg]/$dtKapThn[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdTipe],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$totKapi[$klmpBrg];
        $totBudget+=$dtKapBln[$kdTipe];
        $totBlnReal+=$totKapiSmp[$klmpBrg];
        $totBlnBgt+=$dtKapSmpBln[$kdTipe];
        $totAnn+=$dtKapThn[$kdTipe];
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
           <button onclick=\"zExcel(event,'log_slave_proc_brg_detail_kap2.php','".$arr."','reportcontainer1')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>
           </td></tr>";
    $tab.="</tbody></table>";
    echo $tab."###".$judul;
    break;
    case'excel':
    if($periode=='')
    {
        exit("Error:Field Tidak Boleh Kosongv ads");
    }
   $tab.="<input type=hidden id=periodeDet value='".$periode."' /><table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['jnsKapital']."</td>
    <td align=center rowspan=2 ".$bg.">".$_SESSION['lang']['kapital']."</td>
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

    foreach($dtTipe as $kdTipe)
    {
        $klmpBrg=$dtBrg[$kdTipe];
        $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetBrg('".$kdTipe."','".$arr."')>";
        $tab.="<td>".$kdTipe."</td>";
        $tab.="<td>".$dtNmTipe[$kdTipe]."</td>";
        $tab.="<td align=right>".number_format($totKapi[$klmpBrg],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapBln[$kdTipe],0)."</td>";
        @$prsen[$kdTipe]=$totKapi[$klmpBrg]/$dtKapBln[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsen[$kdTipe],0)."</td>";
        $tab.="<td align=right>".number_format($totKapiSmp[$klmpBrg],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapSmpBln[$kdTipe],0)."</td>";
        @$prsenBln[$kdTipe]=$totKapiSmp[$klmpBrg]/$dtKapSmpBln[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdTipe],0)."</td>";
        $tab.="<td align=right>".number_format($dtKapThn[$kdTipe],0)."</td>";
        @$prsenAnn[$kdTipe]=$totKapiSmp[$klmpBrg]/$dtKapThn[$kdTipe]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdTipe],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$totKapi[$klmpBrg];
        $totBudget+=$dtKapBln[$kdTipe];
        $totBlnReal+=$totKapiSmp[$klmpBrg];
        $totBlnBgt+=$dtKapSmpBln[$kdTipe];
        $totAnn+=$dtKapThn[$kdTipe];
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
    
    $nop_="detailKapitalFis";
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
    
    case'getDetBrgKap':
        #get detail per kelompok barang realisasi mulai#
        $sData="select sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset  where kodetipe='".$klmpkbrg."') 
                and tanggal like '".$periode."%' ".$whr."
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtSatuan[$rData['kodebarang']]=$rData['satuan'];
             $dtHarga[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset  where kodetipe='".$klmpkbrg."') 
                and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."' ".$whr."
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtSatuan[$rData['kodebarang']]=$rData['satuan'];
             $dtHargaSmp[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(k".$bln.") as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
       
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
           $bgtBlni=0;
        }
        /* data budget s.d bulan*/
        $sBgt="select distinct sum(".$addstr.") as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $bgtSmpBln=0;
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="select distinct sum(harga) as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
        $qaresta=mysql_query($aresta) or die(mysql_error($conn));
        while($raresta=mysql_fetch_assoc($qaresta))
        {
            $bgtThnan=0;
        }
        
        /*data budget tahunan abis disini aja*/
        
        #budget data selesai#
        
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
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($bgtBlni,0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtSatuan[$dtrBrg]."</td>";
            @$prsen[$dtrBrg]=$dtHarga[$dtrBrg]/$bgtBlni*100;
            $tab.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHargaSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($bgtSmpBln,0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtSatuan[$dtrBrg]."</td>";
            @$prsenSmp[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$bgtSmpBln*100;
            $tab.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='ANNUAL BUDGET'>".number_format($bgtThnan,0)."</td>";
            @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$bgtThnan*100;
            $tab.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab.="</tr>";
        }
        
        $tab.="<tr><td colspan=10>
           <button onclick=\"zBack2()\" class=\"mybutton\">Back</button>
           <button onclick=\"zExcelDet2(event,'log_slave_proc_brg_detail_kap2.php','".$arr."','".$klmpkbrg."','reportcontainer1')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
         $tab.="</tr></tbody></table>";
        echo $tab."###".$judul;
    break;
   
   case'exceLgetDetBarang':
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
        $sData="select sum(jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset  where kodetipe='".$klmpkbrg."') 
                and tanggal like '".$periode."%'
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtSatuan[$rData['kodebarang']]=$rData['satuan'];
             $dtHarga[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum(hargasatuan*jumlahpesan) as hargasatuan,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                substr(kodebarang,1,3) in (select distinct kelompokbarang from ".$dbname.".sdm_5tipeasset  where kodetipe='".$klmpkbrg."') 
                and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtSatuan[$rData['kodebarang']]=$rData['satuan'];
             $dtHargaSmp[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(k".$bln.") as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
       
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
           $bgtBlni=0;
        }
        /* data budget s.d bulan*/
        $sBgt="select distinct sum(".$addstr.") as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $bgtSmpBln=0;
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="select distinct sum(harga) as total from 
       ".$dbname.".bgt_kapital_vw where tahunbudget='".$tahun."' and jeniskapital='".$klmpkbrg."'";
        $qaresta=mysql_query($aresta) or die(mysql_error($conn));
        while($raresta=mysql_fetch_assoc($qaresta))
        {
            $bgtThnan=0;
        }
        
        /*data budget tahunan abis disini aja*/
        
        #budget data selesai#
        
        $tab2.="<table cellpadding=1 cellspacing=1 border=1 class=sortable>";
        $tab2.="<thead><tr class=rowheader>";
        $tab2.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
        $tab2.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['namabarang']."</td>
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
                $tab2.="<tr class=rowcontent>";
                $tab2.="<td colspan=5>".$optKlmpbrg[$drt]."</td>";
                $tab2.="<td colspan=5>&nbsp;</td>";
                $tab2.="</tr>";
                $klmpBrg=substr($dtrBrg,0,2);
            }
            $tab2.="<tr class=rowcontent>";
            $tab2.="<td title='".$_SESSION['lang']['kodebarang']."'>".$dtrBrg."</td>";
            $tab2.="<td title='".$_SESSION['lang']['namabarang']."'>".$optNamaBrg[$dtrBrg]."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHarga[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($bgtBlni,0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtSatuan[$dtrBrg]."</td>";
            @$prsen[$dtrBrg]=$dtHarga[$dtrBrg]/$bgtBlni*100;
            $tab2.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHargaSmp[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($bgtSmpBln,0)."</td>";
            $tab.="<td  title='".$_SESSION['lang']['satuan']." ".$_SESSION['lang']['bulanini']."'>".$dtSatuan[$dtrBrg]."</td>";
            @$prsenSmp[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$bgtSmpBln*100;
            $tab2.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab2.="<td align=right title='ANNUAL BUDGET'>".number_format($bgtThnan,0)."</td>";
            @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$bgtThnan*100;
            $tab2.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab2.="</tr>";
        }
        
        $tab2.="</tr></tbody></table>";
        $tab2.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    
    $nop_="detailBrgKapFis";
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

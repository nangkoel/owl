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
$_POST['smbrData']==''?$smbrData=$_GET['smbrData']:$smbrData=$_POST['smbrData'];

$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];
strlen($bulan)<1?$bln="0".$bulan:$bln=$bulan;
//exit("Error:".$periode);
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNamaBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optKlmpbrg=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$arr="##periode##judul##kdPt##regDt##smbrData";

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
$ard=0;


if($periode==''){
    exit("Error:Field Tidak Boleh Kosong adasd");
}
if($regDt!=''){
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
    $whr.=" and kodeorg in (".$dtPete.")";
    
if($kdPt!='')
{
    $whr.=" and kodeorg='".$kdPt."'";
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
$dft="statuspo=3";
if($smbrData!='3'){
    $dft="statuspo in('2','3')";
}
#realiasasi  non kapital bulan ini mulai#
$sData="SELECT SUBSTR( kodebarang, 1, 2 ) AS klmpkBrg, SUM(jumlahpesan*(hargasatuan*kurs)) AS totalharga,matauang 
        FROM  ".$dbname.".`log_po_vw` 
        WHERE ".$dft." and hargasatuan!=1 and SUBSTR( tanggal, 1, 7 ) =  '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90') ".$whr."
        GROUP BY SUBSTR( kodebarang, 1, 2 ) ";
//echo $sData;
 //exit("Error:".$sData);
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData)){
    $dtBarang[$rData['klmpkBrg']]=$rData['klmpkBrg'];
    $toHrg[$rData['klmpkBrg']]+=$rData['totalharga'];
}
$sData="SELECT substr(kodebarang,1,2) as kodebarang, SUM(ppn*kurs) AS totalharga,a.nopo
        FROM  ".$dbname.".`log_poht` a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
        WHERE ".$dft." and hargasatuan!=1 and SUBSTR(tanggal, 1, 7 ) =  '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90') ".$whr." group by a.nopo,substr(kodebarang,1,2) order by nopo asc";
//exit("Error:".$sData);
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData)){
    if($nopor!=$rData['nopo']){
        $srow="select * from ".$dbname.".log_podt where nopo='".$rData['nopo']."'";
        $qrow=  mysql_query($srow) or die(mysql_error($conn));
        $rrow=  mysql_num_rows($qrow);
        $pembagi=$rrow;
        $nopor=$rData['nopo'];
    }
    $klmpkBrg=substr($rData['kodebarang'],0,2);
    @$totPpnNonKapi[$klmpkBrg]+=$rData['totalharga']/$pembagi;
}
#realiasasi non kapital bulan ini selesai#

#anggaran non kapital mulai#
$sBgt="select distinct sum(rp".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg  from 
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
$sData="SELECT SUBSTR( kodebarang, 1, 2 ) AS klmpkBrg, SUM(jumlahpesan*(kurs*hargasatuan)) AS totalharga,matauang 
        FROM  ".$dbname.".`log_po_vw` 
        WHERE ".$dft." and hargasatuan!=1 and SUBSTR( tanggal, 1, 7 ) between '".$tahun."-01' and '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90')  ".$whr."
        GROUP BY SUBSTR( kodebarang, 1, 2 ) ";
//echo $sData;
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData)){
    $dtBarang[$rData['klmpkBrg']]=$rData['klmpkBrg'];
    $toHrgBln[$rData['klmpkBrg']]+=$rData['totalharga'];
}
$sData="SELECT substr(kodebarang,1,2) as kodebarang, SUM(ppn*kurs) AS totalharga,a.nopo
        FROM  ".$dbname.".`log_poht` a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
        WHERE ".$dft." and hargasatuan!=1 and SUBSTR( tanggal, 1, 7 ) between '".$tahun."-01' and '".$periode."'
        AND SUBSTR( kodebarang, 1, 2 ) NOT 
        IN ('80',  '90') and ppn!=0  ".$whr."
        group by a.nopo,substr(kodebarang,1,2) order by nopo asc,substr(kodebarang,1,2) asc";
//echo $sData;
$qData=mysql_query($sData) or die(mysql_error($conn));
while($rData=mysql_fetch_assoc($qData)){
    if($nopor!=$rData['nopo']){
        $srow="select * from ".$dbname.".log_podt where nopo='".$rData['nopo']."'";
        $qrow=  mysql_query($srow) or die(mysql_error($conn));
        $rrow=  mysql_num_rows($qrow);
        $pembagi=$rrow;
        $nopor=$rData['nopo'];
    }
    //$klmpkBrg=substr($rData['kodebarang'],0,2);
    @$drt=$rData['totalharga']/$pembagi;
    $totPpnNonKapiBi[$rData['kodebarang']]=$totPpnNonKapiBi[$rData['kodebarang']]+$drt;
    $drt=0;
}
#realiasasi non kapital smp dgn bulan ini selesai#

#anggaran non kapital mulai#
$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="rp0".$W;
    else $jack="rp".$W;
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
           WHERE  ".$dft." and hargasatuan!=1 and  SUBSTR( tanggal, 1, 7 ) between '".$tahun."-01'
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
        $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetBrg('".$kdKlmpkBarang."','".$arr."')>";
        $tab.="<td>".$kdKlmpkBarang."0</td>";
        $tab.="<td>".$dtNama[$kdKlmpkBarang]."</td>";
        $totHrgBlnIni[$kdKlmpkBarang]=$toHrg[$kdKlmpkBarang]+$totPpnNonKapi[$kdKlmpkBarang];
        $tab.="<td align=right>".number_format($totHrgBlnIni[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgt[$kdKlmpkBarang],0)."</td>";
        @$prsen[$kdKlmpkBarang]=$totHrgBlnIni[$kdKlmpkBarang]/$toHrgBgt[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsen[$kdKlmpkBarang],0)."</td>";
        
        $totHrgSmpBlnIni[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]+$totPpnNonKapiBi[$kdKlmpkBarang];
        $tab.="<td align=right>".number_format($totHrgSmpBlnIni[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgtBln[$kdKlmpkBarang],0)."</td>";
        @$prsenBln[$kdKlmpkBarang]=$totHrgSmpBlnIni[$kdKlmpkBarang]/$toHrgBgtBln[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($dtAnn[$kdKlmpkBarang],0)."</td>";
        @$prsenAnn[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$dtAnn[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdKlmpkBarang],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$totHrgBlnIni[$kdKlmpkBarang];
        $totBudget+=$toHrgBgt[$kdKlmpkBarang];
        $totBlnReal+=$totHrgSmpBlnIni[$kdKlmpkBarang];
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
        $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetBrg('".$kdKlmpkBarang."','".$arr."')>";
        $tab.="<td>".$kdKlmpkBarang."0</td>";
        $tab.="<td>".$dtNama[$kdKlmpkBarang]."</td>";
        $totHrgBlnIni[$kdKlmpkBarang]=$toHrg[$kdKlmpkBarang]+$totPpnNonKapi[$kdKlmpkBarang];
        $tab.="<td align=right>".number_format($totHrgBlnIni[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgt[$kdKlmpkBarang],0)."</td>";
        @$prsen[$kdKlmpkBarang]=$totHrgBlnIni[$kdKlmpkBarang]/$toHrgBgt[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsen[$kdKlmpkBarang],0)."</td>";
        
        $totHrgSmpBlnIni[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]+$totPpnNonKapiBi[$kdKlmpkBarang];
        $tab.="<td align=right>".number_format($totHrgSmpBlnIni[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($toHrgBgtBln[$kdKlmpkBarang],0)."</td>";
        @$prsenBln[$kdKlmpkBarang]=$totHrgSmpBlnIni[$kdKlmpkBarang]/$toHrgBgtBln[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenBln[$kdKlmpkBarang],0)."</td>";
        $tab.="<td align=right>".number_format($dtAnn[$kdKlmpkBarang],0)."</td>";
        @$prsenAnn[$kdKlmpkBarang]=$toHrgBln[$kdKlmpkBarang]/$dtAnn[$kdKlmpkBarang]*100;
        $tab.="<td align=right>".number_format($prsenAnn[$kdKlmpkBarang],0)."</td>";
        $tab.="</tr>";
        $totRealisasi+=$totHrgBlnIni[$kdKlmpkBarang];
        $totBudget+=$toHrgBgt[$kdKlmpkBarang];
        $totBlnReal+=$totHrgSmpBlnIni[$kdKlmpkBarang];
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
    
    $nop_="detailNkapital";
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
        $dtHarga=array();
        $dtHargaSmp=array();
        #get detail per kelompok barang realisasi mulai#
        $sData="select sum(jumlahpesan*(kurs*hargasatuan)) as hargasatuan,kodebarang,matauang,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and left(kodebarang,2)='".$klmpkbrg."' 
                and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') and tanggal like '".$periode."%' ".$whr."
                group by kodebarang
                order by kodebarang asc";
        //echo $sData;
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData)){
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtHarga[$rData['kodebarang']]=$rData['hargasatuan'];
        }
        $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
            ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
            where ".$dft." and hargasatuan!=1 and  left(kodebarang,2)='".$klmpkbrg."'
            and left(tanggal,7) = '".$periode."' and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr."
             group by a.nopo,kodebarang order by nopo asc";
        //echo $sTot;
         //exit("Error:".$sTot);
        $qTot=mysql_query($sTot) or die(mysql_error($sTot));
        while($rTot=mysql_fetch_assoc($qTot)){
            if($nopor!=$rTot['nopo']){
                $srow="select * from ".$dbname.".log_podt where nopo='".$rTot['nopo']."'";
                $qrow=  mysql_query($srow) or die(mysql_error($conn));
                $rrow=  mysql_num_rows($qrow);
                $pembagi=$rrow;
                $nopor=$rTot['nopo'];
            }
            @$ppnBrg[$rTot['kodebarang']]+=$rTot['total']/$pembagi;
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum((hargasatuan*kurs)*jumlahpesan) as hargasatuan,kodebarang,matauang,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and left(kodebarang,2)='".$klmpkbrg."' and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'  
                and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr." group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData)){
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtHargaSmp[$rData['kodebarang']]+=$rData['hargasatuan'];
        }
        $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
            ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
            where ".$dft." and hargasatuan!=1 and  left(kodebarang,2)='".$klmpkbrg."'
            and left(tanggal,7) between '".$tahun."-01' and '".$periode."'  and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr." 
            group by a.nopo,kodebarang order by nopo asc";
        // exit("Error:".$sTot);
        $qTot=mysql_query($sTot) or die(mysql_error($sTot));
        while($rTot=mysql_fetch_assoc($qTot)){
            if($nopor!=$rTot['nopo']){
                $srow="select * from ".$dbname.".log_podt where nopo='".$rTot['nopo']."'";
                $qrow=  mysql_query($srow) or die(mysql_error($conn));
                $rrow=  mysql_num_rows($qrow);
                $pembagi=$rrow;
                $nopor=$rTot['nopo'];
            }
            @$ppnBrgSbi[$rTot['kodebarang']]+=$rTot['total']/$pembagi;
        }
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(rp".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
        and substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
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
              and substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgtSmp[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="SELECT sum(rupiah) as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang FROM ".$dbname.".bgt_budget_detail
                WHERE substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' and tahunbudget = '".$tahun."' 
                group by kodebarang order by kodebarang asc";
        //exit("error:".$aresta);
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
        </tr></thead><tbody>";
        foreach($dtKdBrng as $dtrBrg)
        {
            if($drt!=substr($dtrBrg,0,3))
            {
                $drt=substr($dtrBrg,0,3);
                $tab.="<tr class=rowcontent>";
                $tab.="<td colspan=5>".$optKlmpbrg[$drt]."</td>";
                $tab.="<td colspan=5>&nbsp;</td>";
                $tab.="</tr>";
                $klmpBrg=substr($dtrBrg,0,2);
            }
            if(intval($dtHarga[$dtrBrg])!=0){
                $lnkData="style='cursor:pointer' title='Detail ".$optNamaBrg[$dtrBrg]."' onclick=detData(event,'log_slave_proc_brg_detail_kap.php','".$arr."','".$dtrBrg."','1')";
            }
            if(intval($dtHargaSmp[$dtrBrg])!=0){
                $lnkData2="style='cursor:pointer' title='Detail ".$optNamaBrg[$dtrBrg]."' onclick=detData(event,'log_slave_proc_brg_detail_kap.php','".$arr."','".$dtrBrg."','2')";
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td title='".$_SESSION['lang']['kodebarang']."'>".$dtrBrg."</td>";
            $tab.="<td title='".$_SESSION['lang']['namabarang']."'>".$optNamaBrg[$dtrBrg]."</td>";
            $totHrgBi[$dtrBrg]=$dtHarga[$dtrBrg]+$ppnBrg[$dtrBrg];
            $tab.="<td align=right  ".$lnkData.">".number_format($totHrgBi[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHrgBgt[$dtrBrg],0)."</td>";
            @$prsen[$dtrBrg]=$totHrgBi[$dtrBrg]/$dtHrgBgt[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $totHrgsBi[$dtrBrg]=$dtHargaSmp[$dtrBrg]+$ppnBrgSbi[$dtrBrg];
            $tab.="<td align=right  ".$lnkData2.">".number_format($totHrgsBi[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHrgBgtSmp[$dtrBrg],0)."</td>";
            @$prsenSmp[$dtrBrg]=$totHrgsBi[$dtrBrg]/$dtHrgBgtSmp[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='ANNUAL BUDGET'>".number_format($dtHrgBgtThn[$dtrBrg],0)."</td>";
             @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtThn[$dtrBrg]*100;
             $tab.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab.="</tr>";
        }
        
        $tab.="<tr><td colspan=10>
           <button onclick=\"zBack2()\" class=\"mybutton\">Back</button>
           <button onclick=\"zExcelDet(event,'log_slave_proc_brg_detail_kap.php','".$arr."','".$klmpkbrg."','reportcontainer1')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>";
         $tab.="</tr></tbody></table>";
        echo $tab;
    break;
   
   case'exceLgetDetBarang':
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
      #get detail per kelompok barang realisasi mulai#
        $sData="select sum(jumlahpesan*(kurs*hargasatuan)) as hargasatuan,kodebarang,matauang,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and left(kodebarang,2)='".$klmpkbrg."' and tanggal like '".$periode."%'  
                and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr." 
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData)){
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtHarga[$rData['kodebarang']]+=$rData['hargasatuan'];
        }
        $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
            ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
            where ".$dft." and hargasatuan!=1 and  left(kodebarang,2)='".$klmpkbrg."'
            and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr." 
            and left(tanggal,7) = '".$periode."' "
            . "group by a.nopo,kodebarang order by nopo asc";
         //exit("Error:".$sTot);
        $qTot=mysql_query($sTot) or die(mysql_error($sTot));
        while($rTot=mysql_fetch_assoc($qTot)){
            if($nopor!=$rTot['nopo']){
                $srow="select * from ".$dbname.".log_podt where nopo='".$rTot['nopo']."'";
                $qrow=  mysql_query($srow) or die(mysql_error($conn));
                $rrow=  mysql_num_rows($qrow);
                $pembagi=$rrow;
                $nopor=$rTot['nopo'];
            }
            @$ppnBrg[$rTot['kodebarang']]+=$rTot['total']/$pembagi;
        }
        #get detail per kelompok barang realisasi selesai#
        #get detail per kelompok barang realisasi smp bulan mulai#
        $sData="select distinct sum((hargasatuan*kurs)*jumlahpesan) as hargasatuan,kodebarang,matauang,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and left(kodebarang,2)='".$klmpkbrg."' and substr(tanggal,1,7) between '".$tahun."-01' and '".$periode."'
                 and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90') ".$whr." 
                group by kodebarang
                order by kodebarang asc";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData)){
             $dtKdBrng[$rData['kodebarang']]=$rData['kodebarang'];
             $dtNmBrng[$rData['kodebarang']]=$rData['namabarang'];
             $dtHargaSmp[$rData['kodebarang']]+=$rData['hargasatuan'];
        }
        $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
            ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
            where ".$dft." and hargasatuan!=1 and  left(kodebarang,2)='".$klmpkbrg."'
            and left(tanggal,7) between '".$tahun."-01' and '".$periode."' "
            . "group by a.nopo,kodebarang order by nopo asc";
        // exit("Error:".$sTot);
        $qTot=mysql_query($sTot) or die(mysql_error($sTot));
        while($rTot=mysql_fetch_assoc($qTot)){
            if($nopor!=$rTot['nopo']){
                $srow="select * from ".$dbname.".log_podt where nopo='".$rTot['nopo']."'";
                $qrow=  mysql_query($srow) or die(mysql_error($conn));
                $rrow=  mysql_num_rows($qrow);
                $pembagi=$rrow;
                $nopor=$rTot['nopo'];
            }
            @$ppnBrgSbi[$rTot['kodebarang']]+=$rTot['total']/$pembagi;
        }
        #get detail per kelompok barang realisasi smp bulan selesai#
        
        #budget data mulai#
         /* data budget*/
        $sBgt="select distinct sum(rp".$bln.") as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang  from 
      ".$dbname.".bgt_budget_detail where tahunbudget='".$tahun."'
        and substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
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
              and substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' group by kodebarang order by kodebarang asc";
        //exit("error:".$sBgt);
        $qBgt=mysql_query($sBgt) or die(mysql_error($conn));
        while($rBgt=mysql_fetch_assoc($qBgt))
        {
            $dtHrgBgtSmp[$rBgt['kodebarang']]=$rBgt['total'];
            $dtKdBrng[$rBgt['kodebarang']]=$rBgt['kodebarang'];
        }
        /* data budget s.d bulan abis disini*/
        
        /*data budget tahunan*/
        $aresta="SELECT sum(rupiah) as total,substr(kodebudget,3,2) as klmpkBrg,kodebarang FROM ".$dbname.".bgt_budget_detail
                WHERE substr(kodebudget,1,1)='M' and left(kodebarang,2)='".$klmpkbrg."' and tahunbudget = '".$tahun."' 
                group by kodebarang order by kodebarang asc";
        //exit("error:".$aresta);
        $qaresta=mysql_query($aresta) or die(mysql_error($conn));
        while($raresta=mysql_fetch_assoc($qaresta))
        {
            $dtHrgBgtThn[$raresta['kodebarang']]=$raresta['total'];
            $dtKdBrng[$raresta['kodebarang']]=$raresta['kodebarang'];
        }
        
        /*data budget tahunan abis disini aja*/
        
        #budget data selesai#
        ksort($dtKdBrng);
        $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable>";
        $tab.="<thead><tr class=rowheader>";
        $tab.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['kodebarang']."</td>";
        $tab.="<td  rowspan=2 ".$bg.">".$_SESSION['lang']['namabarang']."</td>
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
        </tr></thead><tbody>";
        foreach($dtKdBrng as $dtrBrg)
        {
            if($drt!=substr($dtrBrg,0,3))
            {
                $drt=substr($dtrBrg,0,3);
                $tab.="<tr class=rowcontent>";
                $tab.="<td colspan=5>".$optKlmpbrg[$drt]."</td>";
                $tab.="<td colspan=5>&nbsp;</td>";
                $tab.="</tr>";
                $klmpBrg=substr($dtrBrg,0,2);
            }
            $tab.="<tr class=rowcontent>";
            $tab.="<td title='".$_SESSION['lang']['kodebarang']."'>".$dtrBrg."</td>";
            $tab.="<td title='".$_SESSION['lang']['namabarang']."'>".$optNamaBrg[$dtrBrg]."</td>";
            $totHrgBi[$dtrBrg]=$dtHarga[$dtrBrg]+$ppnBrg[$dtrBrg];
            $tab.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['bulanini']."'>".number_format($totHrgBi[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['bulanini']."'>".number_format($dtHrgBgt[$dtrBrg],0)."</td>";
            @$prsen[$dtrBrg]=$totHrgBi[$dtrBrg]/$dtHrgBgt[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsen[$dtrBrg],0)."</td>";
            $totHrgsBi[$dtrBrg]=$dtHargaSmp[$dtrBrg]+$ppnBrgSbi[$dtrBrg];
            $tab.="<td align=right title='".$_SESSION['lang']['realisasi']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHargaSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['sdbulanini']."'>".number_format($dtHrgBgtSmp[$dtrBrg],0)."</td>";
            @$prsenSmp[$dtrBrg]=$totHrgsBi[$dtrBrg]/$dtHrgBgtSmp[$dtrBrg]*100;
            $tab.="<td align=right title='%'>".number_format($prsenSmp[$dtrBrg],0)."</td>";
            $tab.="<td align=right title='ANNUAL BUDGET'>".number_format($dtHrgBgtThn[$dtrBrg],0)."</td>";
             @$prsenThn[$dtrBrg]=$dtHargaSmp[$dtrBrg]/$dtHrgBgtThn[$dtrBrg]*100;
             $tab.="<td align=right title='%'>".number_format($prsenThn[$dtrBrg],0)."</td>";
            $tab.="</tr>";
        }
        
        $tab.="</tbody></table>";
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    
    $nop_="detailBrg";
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
    case'excelDetailBrg':
    
    $brdr=0;
      #get detail per kelompok barang realisasi mulai#
        if(substr($_POST['kodeBarang'],0,1)=='8'||substr($_POST['kodeBarang'],0,1)=='9'){
            $whr.="and SUBSTR( kodebarang, 1, 2 ) IN ('80', '90')";
        }else{
            $whr.="and SUBSTR( kodebarang, 1, 2 ) NOT IN ('80', '90')";
        }
    if($_POST['pilihan']=='1'){
        $sData="select jumlahpesan,(kurs*hargasatuan) as hargasatuan,nopo,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and kodebarang='".$_POST['kodeBarang']."' and tanggal like '".$periode."%'  
                 ".$whr."  group by nopo order by nopo asc";
        $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
              ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
              where ".$dft." and hargasatuan!=1 and  kodebarang='".$_POST['kodeBarang']."' 
              ".$whr." and left(tanggal,7) = '".$periode."' "
              ."group by a.nopo,kodebarang order by nopo asc";
    }else{
         $sData="select jumlahpesan,(kurs*hargasatuan) as hargasatuan,nopo,kodebarang,satuan,namabarang from ".$dbname.".log_po_vw where 
                ".$dft." and hargasatuan!=1 and kodebarang='".$_POST['kodeBarang']."' and left(tanggal,7) between '".$tahun."-01' and '".$periode."'  
                ".$whr."  group by nopo order by nopo asc";
         $sTot="select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from 
               ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo 
               where ".$dft." and hargasatuan!=1 and  kodebarang='".$_POST['kodeBarang']."' 
               ".$whr."  and left(tanggal,7) between '".$tahun."-01' and '".$periode."' "
               ."group by a.nopo,kodebarang order by nopo asc";
    }
    //echo $sTot;
        //exit("error:".$sData);
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData)){
             $dtNopo[$rData['nopo']]=$rData['nopo'];
             $dtHarga[$rData['nopo']]=$rData['hargasatuan'];
             $dtJmlh[$rData['nopo']]=$rData['jumlahpesan'];
        }
        
         //exit("Error:".$sTot);
        $qTot=mysql_query($sTot) or die(mysql_error($sTot));
        while($rTot=mysql_fetch_assoc($qTot)){
            if($nopor!=$rTot['nopo']){
                $srow="select * from ".$dbname.".log_podt where nopo='".$rTot['nopo']."'";
                $qrow=  mysql_query($srow) or die(mysql_error($conn));
                $rrow=  mysql_num_rows($qrow);
                $pembagi=$rrow;
                $nopor=$rTot['nopo'];
            }
            @$ppnBrg[$rTot['nopo']]+=$rTot['total']/$pembagi;
        }
        #get detail per kelompok barang realisasi selesai#        
        $tab.="<table cellpadding=1 cellspacing=1 border=0>";
        $tab.="<tr><td colspan=3>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td>".$_POST['kodeBarang']."</td></tr>";
        $tab.="<tr><td colspan=3>".$_SESSION['lang']['namabarang']."</td><td>:</td><td>".$optNamaBrg[$_POST['kodeBarang']]."</td></tr>";
        $tab.="<tr><td colspan=3>".$_SESSION['lang']['satuan']."</td><td>:</td><td>".$optSatBrg[$_POST['kodeBarang']]."</td></tr></table>";
        
        $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable>";
        $tab.="<thead><tr class=rowheader>";
        $tab.="<td ".$bg.">".$_SESSION['lang']['nopo']."</td>";
        $tab.="<td ".$bg.">".$_SESSION['lang']['jumlah']."</td>";
        $tab.="<td ".$bg.">".$_SESSION['lang']['hargasatuan']."</td>";
        $tab.="<td ".$bg.">".$_SESSION['lang']['ppn']."</td>";
        $tab.="<td ".$bg.">".$_SESSION['lang']['subtotal']."</td>";
        $tab.="</tr></thead><tbody>";
        foreach($dtNopo as $lstNopo){
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$lstNopo."</td>";
            $tab.="<td align=right>".number_format($dtJmlh[$lstNopo],0)."</td>";
            $tab.="<td align=right>".number_format($dtHarga[$lstNopo],0)."</td>";
            $tab.="<td align=right>".number_format($ppnBrg[$lstNopo],0)."</td>";
            $sbTotalDt[$lstNopo]=($dtHarga[$lstNopo]*$dtJmlh[$lstNopo])+$ppnBrg[$lstNopo];
            $tab.="<td align=right>".number_format($sbTotalDt[$lstNopo],0)."</td>";
            $tab.="</tr>";
            $granTot+=$sbTotalDt[$lstNopo];
        }
        $tab.="<tr class=rowcontent><td colspan=4>".$_SESSION['lang']['grandtotal']."</td>";
        $tab.="<td>".number_format($granTot,0)."</td></tr>";
        $tab.="</tbody></table>";
       echo $tab;
    break;

}
	
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_POST['proses'];

if($proses=='getUnit'){
    $kodePt=$_POST['kodePt'];
    switch($proses)
    {
        case'getUnit':
        if($kodePt=='')echo"<option value=''>".$_SESSION['lang']['pilihdata']."</option>"; else
        {
            $sOrg="select kodeorganisasi from ".$dbname.".organisasi where induk = '".$kodePt."' order by kodeorganisasi";
            $qOrg=mysql_query($sOrg) or die(mysql_error());
            echo"<option value=''>".$_SESSION['lang']['all']."</option>";
            while($rData=mysql_fetch_assoc($qOrg))
            {
                    echo"<option value=".$rData['kodeorganisasi'].">".$rData['kodeorganisasi']."</option>";
            }
        }
        break;
        default:
        break;
    }        
    exit;
}

$proses=$_GET['proses'];
$_POST['kdPt2']==''?$kodePt=$_GET['kdPt2']:$kodePt=$_POST['kdPt2'];
$_POST['kdUnit2']==''?$kodeOrg=$_GET['kdUnit2']:$kodeOrg=$_POST['kdUnit2'];
$_POST['thnBudget2']==''?$thnBudget=$_GET['thnBudget2']:$thnBudget=$_POST['thnBudget2'];

$dates = explode("-", $thnBudget);
$lastyear=$dates[0]-1;

$sOrg="select kodeorganisasi from ".$dbname.".organisasi where induk = '".$kodePt."' order by kodeorganisasi";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$where3="(";
while($rData=mysql_fetch_assoc($qOrg))
{
    $where3.="'".$rData['kodeorganisasi']."',";
}
$where3=substr($where3,0,-1);
$where3.=")";

$where=" unit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
if($kodeOrg=='')$where=" unit in ".$where3." and tahunbudget = '".$thnBudget."'";
$where2=" kodeorg='".$kodeOrg."' and periode = '".$lastyear."12' group by noakun";
if($kodeOrg=='')$where2=" kodeorg in ".$where3." and periode = '".$lastyear."12' group by noakun";
//query sum buat total setahun
//$sSum="select (awal12+debet12-kredit) as lastsum, kodeorg from ".$dbname.".keu_saldobulanan where ".$where."";
////echo $sSum;
//$qSum=mysql_query($sSum) or die(mysql_error($conn));
//$rSum=mysql_fetch_assoc($qSum);

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optAkun=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

//get lastyear
$sLastsum="select noakun,sum(awal12+debet12-kredit12) as lastsum, kodeorg from ".$dbname.".keu_saldobulanan where  ".$where2."";
$qLastsum=mysql_query($sLastsum) or die(mysql_error($conn));
while($hasil=mysql_fetch_assoc($qLastsum))
{
//    $noakun=$hasil['noakun'];
//    $isidata[$noakun]['noakun']=$noakun;
//    $isidata[$noakun]['lastsum']=$hasil['lastsum'];
    $lastsum[$hasil['noakun']]=$hasil['lastsum'];
    
}

//echo $sLastsum;
//echo "<pre>";
//print_r($lastsum);
//echo "</pre>";

//get budgetyear
$sLastsum="select * from ".$dbname.".bgt_summary_biaya_vw where ".$where." order by noakun";
//echo $sLastsum;
$isidata=array();
$qLastsum=mysql_query($sLastsum) or die(mysql_error($conn));
while($hasil=mysql_fetch_assoc($qLastsum))
{
//    $a+=1; 
    //$dtKdunit[$a]=$rKode['afdeling']; 
    $noakun=$hasil['noakun'];
    $isidata[$noakun]['noakun']=$noakun;
   // $isidata[$noakun]['setahun']+=$hasil['setahun'];
    $isidata[$noakun]['01']+=$hasil['rp01'];
    $isidata[$noakun]['02']+=$hasil['rp02'];
    $isidata[$noakun]['03']+=$hasil['rp03'];
    $isidata[$noakun]['04']+=$hasil['rp04'];
    $isidata[$noakun]['05']+=$hasil['rp05'];
    $isidata[$noakun]['06']+=$hasil['rp06'];
    $isidata[$noakun]['07']+=$hasil['rp07'];
    $isidata[$noakun]['08']+=$hasil['rp08'];
    $isidata[$noakun]['09']+=$hasil['rp09'];
    $isidata[$noakun]['10']+=$hasil['rp10'];
    $isidata[$noakun]['11']+=$hasil['rp11'];
    $isidata[$noakun]['12']+=$hasil['rp12'];    
}

if($kodePt==''||$thnBudget=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}

if($_GET['proses']=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab="<table>
 <tr><td colspan=5 align=left><font size=5>".strtoupper($_SESSION['lang']['aruskas'])." ".strtoupper($_SESSION['lang']['labarugi'])."</font></td></tr>"; 
 if($kodePt!='')$tab.="<tr><td colspan=5 align=left>".$optNm[$kodePt]."</td></tr>";   
 if($kodeOrg!='')$tab.="<tr><td colspan=5 align=left>".$optNm[$kodeOrg]."</td></tr>";   
 $tab.="<tr><td>".$_SESSION['lang']['budgetyear']."</td><td colspan=2 align=left>".$thnBudget."</td></tr>   
 </table>";
}
else
{
   $bg=" ";
   $brdr=0; 
}

$tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable width=100%><thead>";
$tab.="<tr class=rowheader>";
$tab.="<td rowspan=2 align=center ".$bg." >".$_SESSION['lang']['nourut']."</td>";
$tab.="<td rowspan=2 align=center ".$bg." >".$_SESSION['lang']['uraian']."</td>";
$tab.="<td rowspan=2 align=center ".$bg." >".$_SESSION['lang']['catatan']."</td>";
//$tab.="<td rowspan=2 align=center ".$bg." >".$_SESSION['lang']['dec']." ".$lastyear."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['jan']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['peb']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['mar']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['apr']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['mei']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['jun']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['jul']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['agt']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['sep']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['okt']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['nov']."</td>";
$tab.="<td colspan=2 align=center ".$bg." >".$_SESSION['lang']['dec']."</td>";
$tab.="<td rowspan=2 align=center ".$bg." >".$_SESSION['lang']['total']."</td>";
$tab.="</tr>";
$tab.="<tr class=rowheader>";
for ($i = 1; $i <= 12; $i++) {
    $tab.="<td align=center ".$bg." >Bulan Ini</td>";
    $tab.="<td align=center ".$bg." >Akumulasi</td>";
}
$tab.="</tr>";
$tab.="</thead><tbody>";

function artinya($nomer){
    if($nomer=='1')$tabz="Aktiva";
    if($nomer=='11')$tabz="Aktiva Lancar";
    if($nomer=='12')$tabz="Aktiva Tidak Lancar";
    if($nomer=='2')$tabz="Kewajiban";
    if($nomer=='21')$tabz="Kewajiban Lancar";
    if($nomer=='22')$tabz="Kewajiban Tidak Lancar";
    if($nomer=='3')$tabz="Ekuitas";
    if($nomer=='5')$tabz="Pendapatan Usaha";
    if($nomer=='6')$tabz="Beban Produksi Langsung";
    if($nomer=='7')$tabz="Beban Produksi Tidak Langsung";
    if($nomer=='8')$tabz="Beban Usaha";
    if($nomer=='9')$tabz="Pendapatan dan Beban Lain-lain";
    return $tabz;
}

function printline($depannya){
    $tabz="";
    $tabz.="<tr class=rowcontent>";
    $tabz.="<td align=left ".$bg." >".$depannya."</td>";
    $tabz.="<td align=left ".$bg." >".strtoupper(artinya($depannya))."</td>";
    for ($i = 1; $i <= 13; $i++) {
        $tabz.="<td align=left ".$bg." ></td>";
        $tabz.="<td align=left ".$bg." ></td>";
    }
    $tabz.="</tr>";
    return $tabz;
}
function printline2($depannya){
    global $isidata;
    global $optAkun;
    global $lastsum;
    
    $tabz=""; 
    $berapa=strlen($depannya);
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if($dimana==$depannya){
            $tabz.="<tr class=rowcontent>"; // CETAK ISI DATA YANG DEPANNYA SESUAI
            $tabz.="<td align=left ".$bg." >".$isi['noakun']."</td>";
            $tabz.="<td align=left ".$bg." >".$optAkun[$isi['noakun']]."</td>";
            $tabz.="<td align=left ".$bg." ></td>";
            //$tabz.="<td align=right ".$bg." >".number_format($lastsum[$isi['noakun']])."</td>";
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $tabz.="<td align=right ".$bg." >".number_format($isi[$ii])."</td>";
                $akumulasi+=$isi[$ii];
                $tabz.="<td align=right ".$bg." >".number_format($akumulasi)."</td>";
                $total[$isi['noakun']]+=$isi[$ii];

            }
            $akumulasi=0;
            $tabz.="<td align=right ".$bg." >".number_format($total[$isi['noakun']])."</td>";
            $tabz.="</tr>";
        }
    }
    return $tabz;
}

function printline3($depannya){
    global $isidata;
    global $lastsum;
    
    $tabz=""; 
    $jumlah=array();
    $jumlah2=array();
    $berapa=strlen($depannya);
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if($dimana==$depannya){
            $jumlah[0]+=$lastsum[$isi['noakun']];
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $akumulasi+=$isi[$ii];
                $jumlah[$ii]+=$isi[$ii];
                $jumlah2[$ii]+=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    $tabz.="<tr class=rowcontent>";
    $tabz.="<td align=left ".$bg." ></td>";
    $tabz.="<td align=left ".$bg." >Jumlah ".artinya($depannya)."</td>";
    $tabz.="<td align=left ".$bg." ></td>";
    //$tabz.="<td align=right ".$bg." >".number_format($jumlah[0])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $tabz.="<td align=right ".$bg." >".number_format($jumlah[$ii])."</td>";
        $tabz.="<td align=right ".$bg." >".number_format($jumlah2[$ii])."</td>";
        $totalDt+=$jumlah[$ii];
    }
    $tabz.="<td align=right ".$bg." >".number_format($totalDt)."</td>";
    $tabz.="</tr>";
    return $tabz;
}

function printline4(){
    global $isidata;
    global $lastsum;
    $tabz=""; 
    $jumlah=array();
    $jumlah2=array();
    $berapa=1;
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if(($dimana==5)or($dimana==6)or($dimana==7)){
            $jumlah[0]+=$lastsum[$isi['noakun']];
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $jumlah[$ii]+=$isi[$ii];
                $akumulasi+=$isi[$ii];
                $jumlah2[$ii]+=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    $tabz.="<tr class=rowcontent>";
    $tabz.="<td align=left ".$bg." ></td>";
    $tabz.="<td align=left ".$bg." >Jumlah Laba Rugi Kotor</td>";
    $tabz.="<td align=left ".$bg." ></td>";
    //$tabz.="<td align=right ".$bg." >".number_format($jumlah[0])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $tabz.="<td align=right ".$bg." >".number_format($jumlah[$ii])."</td>";
        $tabz.="<td align=right ".$bg." >".number_format($jumlah2[$ii])."</td>";
        $totalDt+=$jumlah[$ii];
    }
    $tabz.="<td align=right ".$bg." >".number_format($totalDt)."</td>";
    $tabz.="</tr>";
    return $tabz;
}

function printline5(){
    global $isidata;
    global $lastsum;

    $tabz=""; 
    $jumlah=array();
    $jumlah2=array();
    $berapa=1;
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if(($dimana==5)or($dimana==6)or($dimana==7)){
            $jumlah[0]+=$lastsum[$isi['noakun']]; 
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $jumlah[$ii]+=$isi[$ii];
                $akumulasi+=$isi[$ii];
                $jumlah2[$ii]+=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if(($dimana==8)){
            $jumlah[0]-=$$lastsum[$isi['noakun']];
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $jumlah[$ii]-=$isi[$ii];
                $akumulasi+=$isi[$ii];
                $jumlah2[$ii]-=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    $tabz.="<tr class=rowcontent>";
    $tabz.="<td align=left ".$bg." ></td>";
    $tabz.="<td align=left ".$bg." >Jumlah Laba Rugi Usaha</td>";
    $tabz.="<td align=left ".$bg." ></td>";
    //$tabz.="<td align=right ".$bg." >".number_format($jumlah[0])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $tabz.="<td align=right ".$bg." >".number_format($jumlah[$ii])."</td>";
        $tabz.="<td align=right ".$bg." >".number_format($jumlah2[$ii])."</td>";
     $totalDt+=$jumlah[$ii];
    }
    $tabz.="<td align=right ".$bg." >".number_format($totalDt)."</td>";
    $tabz.="</tr>";
    return $tabz;
}

function printline6(){
    global $isidata;
    global $lastsum;

    $tabz=""; 
    $jumlah=array();
    $jumlah2=array();
    $berapa=1;
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if(($dimana==5)or($dimana==6)or($dimana==7)){
            $jumlah[0]+=$lastsum[$isi['noakun']]; 
           // $akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $jumlah[$ii]+=$isi[$ii];
                $akumulasi+=$isi[$ii];
                $jumlah2[$ii]+=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    if(!empty($isidata))
    foreach($isidata as $isi){
        $dimana=substr($isi['noakun'],0,$berapa);
        if(($dimana==8)or(($dimana==9))){
            $jumlah[0]-=$lastsum[$isi['noakun']];
            //$akumulasi=$lastsum[$isi['noakun']];
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $jumlah[$ii]-=$isi[$ii];
                $akumulasi+=$isi[$ii];
                $jumlah2[$ii]-=$akumulasi;
            }
            $akumulasi=0;
        }
    }
    $tabz.="<tr class=rowcontent>";
    $tabz.="<td align=left ".$bg." ></td>";
    $tabz.="<td align=left ".$bg." >Jumlah Laba Rugi Usaha</td>";
    $tabz.="<td align=left ".$bg." ></td>";
    //$tabz.="<td align=right ".$bg." >".number_format($jumlah[0])."</td>";
    for ($i = 1; $i <= 12; $i++) {
        if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
        $tabz.="<td align=right ".$bg." >".number_format($jumlah[$ii])."</td>";
        $tabz.="<td align=right ".$bg." >".number_format($jumlah2[$ii])."</td>";
        $totalDt+=$jumlah[$ii];
    }
    $tabz.="<td align=right ".$bg." >".number_format($totalDt)."</td>";
    $tabz.="</tr>";
    return $tabz;
}

// printline = header
// printline2 = content
// printline3 = sum

$tab.=printline('5');
$tab.=printline2('5');
$tab.=printline3('5');

$tab.=printline('6');
$tab.=printline2('6');
$tab.=printline3('6');

$tab.=printline('7');
$tab.=printline2('7');
$tab.=printline3('7');
$tab.=printline4();

$tab.=printline('8');
$tab.=printline2('8');
$tab.=printline3('8');
$tab.=printline5();

$tab.=printline('9');
$tab.=printline2('9');
$tab.=printline3('9');
$tab.=printline6();

$tab.="</tbody></table>";

	switch($proses)
        {
            case'preview':
            echo $tab;
            break;
            case'excel':
             
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="laparuskasbudget_labarugi_".$kodeOrg.$thnBudget."_".$dte;
            $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                     gzwrite($gztralala, $tab);
                     gzclose($gztralala);
                     echo "<script language=javascript1.2>
                        window.location='tempExcel/".$nop_.".xls.gz';
                        </script>";

            break;
            default:
            break;
        }
	
?>

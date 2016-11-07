<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];
 

$qwe=explode("-",$periode); $tahun=$qwe[0]; $bulan=$qwe[1];
//exit("Error:".$periode."___".$tahun."___".$bulan);
$optNm=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKeg=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optKegSat=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
if($unit==''||$periode=='')
{
    exit("Error:Field Tidak Boleh Kosong");
}
$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="jjg0".$W;
    else $jack="jjg".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

$addstr3="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="rp0".$W;
    else $jack="rp".$W;
    if($W<intval($bulan))$addstr3.=$jack."+";
    else $addstr3.=$jack;
}
$addstr3.=")";

$addstr2="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="kg0".$W;
    else $jack="kg".$W;
    if($W<intval($bulan))$addstr2.=$jack."+";
    else $addstr2.=$jack;
}
$addstr2.=")";
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
$bg="";
$brdr=0;

#luas#
$sLuas="select distinct sum(luasareaproduktif) as luaskrja,left(kodeorg,6) as afd,tahuntanam from 
        ".$dbname.".`setup_blok`  
        where kodeorg like '".$unit."%' and tahuntanam!='' and statusblok='TM'
        group by left(kodeorg,6),tahuntanam order by left(kodeorg,6) asc,tahuntanam asc";
if($afdId!='')
{
    $sLuas="select distinct sum(luasareaproduktif) as luaskrja,left(kodeorg,6) as afd,tahuntanam from 
        ".$dbname.".`setup_blok`  
        where kodeorg like '".$afdId."%' and tahuntanam!='' and statusblok='TM'
        group by left(kodeorg,6),tahuntanam order by left(kodeorg,6) asc,tahuntanam asc";
}
//echo $sLuas;
$qLuas=mysql_query($sLuas) or die(mysql_error($conn));
while($rLuas=  mysql_fetch_assoc($qLuas))
{
        $dtAfd[$rLuas['afd']]=$rLuas['afd'];
        $dtThnTnm[$rLuas['tahuntanam']]=$rLuas['tahuntanam'];
        $dtLuas[$rLuas['afd'].$rLuas['tahuntanam']]=$rLuas['luaskrja'];
}
#luas end#

#produksi#
$sProd="select distinct sum(hasilkerjakg) as kg,sum(hasilkerja) as jjg,left(kodeorg,6) as afd,
        tahuntanam,sum(luaspanen) as ha,count(karyawanid) as jmlhk,substr(tanggal,6,2) as bln from ".$dbname.".kebun_prestasi_vw where
        kodeorg like '".$unit."%' and left(tanggal,7) between '".$tahun."-01' and '".$periode."' group by left(kodeorg,6),tahuntanam,substr(tanggal,6,2)";
if($afdId!='')
{
   $sProd="select distinct sum(hasilkerjakg) as kg,sum(hasilkerja) as jjg,left(kodeorg,6) as afd,
        tahuntanam,sum(luaspanen) as ha,count(karyawanid) as jmlhk,substr(tanggal,6,2) as bln from ".$dbname.".kebun_prestasi_vw where
        kodeorg like '".$afdId."%' and left(tanggal,7) between '".$tahun."-01' and '".$periode."' group by left(kodeorg,6),tahuntanam,substr(tanggal,6,2)"; 
}
//exit("Error:".$sProd);
$qProd=mysql_query($sProd) or die(mysql_error($conn));
while($rProd=  mysql_fetch_assoc($qProd))
{
    $dThntnm[$rProd['tahuntanam']]=$rProd['tahuntanam'];
    $dAfd[$rProd['afd']]=$rProd['afd'];
    $dtProdKg[$rProd['afd'].$rProd['tahuntanam']][$rProd['bln']]=$rProd['kg'];
    $dtProdJjg[$rProd['afd'].$rProd['tahuntanam']][$rProd['bln']]=$rProd['jjg'];
    $dtProdJmhk[$rProd['afd'].$rProd['tahuntanam']][$rProd['bln']]=$rProd['jmlhk'];
    $dtProdKgSi[$rProd['afd'].$rProd['tahuntanam']]+=$rProd['kg'];
    $dtProdJjgSi[$rProd['afd'].$rProd['tahuntanam']]+=$rProd['jjg'];
    $dtProdJmhkSi[$rProd['afd'].$rProd['tahuntanam']]+=$rProd['jmlhk'];
}

$sbgt="select distinct sum(".$addstr2.") as jmlkg,left(kodeblok,6) as afd,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw
       where kodeblok like '".$unit."%' and tahunbudget='".$tahun."' group by left(kodeblok,6),thntnm order by left(kodeblok,6) asc,thntnm asc";
if($afdId!='')
{
    $sbgt="select distinct sum(".$addstr2.") as jmlkg,left(kodeblok,6) as afd,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw
       where kodeblok like '".$afdId."%' and tahunbudget='".$tahun."' group by left(kodeblok,6),thntnm order by left(kodeblok,6) asc,thntnm asc";
}
$qbgt=mysql_query($sbgt) or die(mysql_error($conn));
while($rbgt=  mysql_fetch_assoc($qbgt))
{
    $dtbgtkg[$rbgt['afd'].$rbgt['thntnm']]=$rbgt['jmlkg'];
    $dThntnm[$rbgt['thntnm']]=$rbgt['thntnm'];
    $dAfd[$rbgt['afd']]=$rbgt['afd'];
}
$sbgt="select distinct sum(".$addstr.") as jjg,left(kodeblok,6) as afd,thntnm from ".$dbname.".bgt_produksi_kbn_vw
       where kodeblok like '".$unit."%' and tahunbudget='".$tahun."' group by left(kodeblok,6),thntnm order by left(kodeblok,6) asc,thntnm asc";
if($afdId!='')
{
   $sbgt="select distinct sum(".$addstr.") as jjg,left(kodeblok,6) as afd,thntnm from ".$dbname.".bgt_produksi_kbn_vw
       where kodeblok like '".$afdId."%' and tahunbudget='".$tahun."' group by left(kodeblok,6),thntnm order by left(kodeblok,6) asc,thntnm asc"; 
}
$qbgt=mysql_query($sbgt) or die(mysql_error($conn));
while($rbgt=  mysql_fetch_assoc($qbgt))
{
    $dtbgtjjg[$rbgt['afd'].$rbgt['thntnm']]=$rbgt['jjg'];
    $dThntnm[$rbgt['thntnm']]=$rbgt['thntnm'];
    $dAfd[$rbgt['afd']]=$rbgt['afd'];
}
$sRotasiBudget="select distinct sum".$addstr3." as rpblnan,sum(rupiah) as rupiah,left(a.kodeorg,6) as afd,b.thntnm,sum(jumlah) as jmlh
                from ".$dbname.".bgt_budget_detail a left join 
                ".$dbname.".bgt_blok b on a.kodeorg=b.kodeblok where a.tahunbudget='".$tahun."' 
                and a.kodeorg like '".$unit."%' and kegiatan='611010101' and kodebudget like 'SDM%' group by left(a.kodeorg,6),b.thntnm";
if($afdId!='')
{
    $sRotasiBudget="select distinct sum".$addstr3." as rpblnan,sum(rupiah) as rupiah,left(a.kodeorg,6) as afd,b.thntnm,sum(jumlah) as jmlh
                    from ".$dbname.".bgt_budget_detail a left join 
                   ".$dbname.".bgt_blok b on a.kodeorg=b.kodeblok where a.tahunbudget='".$tahun."' 
                   and a.kodeorg like '".$afdId."%' and kegiatan='611010101' and kodebudget like 'SDM%'
                   group by left(a.kodeorg,6),b.thntnm";
}
//exit("Error:".$sRotasiBudget);
$qbgt=mysql_query($sRotasiBudget) or die(mysql_error($conn));
while($rbgt=  mysql_fetch_assoc($qbgt))
{
    @$dtbgthk[$rbgt['afd'].$rbgt['thntnm']]=($rbgt['rpblnan']/$rbgt['rupiah'])*$rbgt['jmlh'];
    $dThntnm[$rbgt['thntnm']]=$rbgt['thntnm'];
    $dAfd[$rbgt['afd']]=$rbgt['afd'];
}
#produksi end#

#rotasi#
// data panen bulan ini
$panen="select kodeorg,tanggal,tahuntanam from ".$dbname.".kebun_interval_panen_vw
    where  tanggal like '".$periode."%' and kodeorg like '".$unit."%'  order by kodeorg asc,tanggal asc";
if($afdId!='')
{
   $panen="select kodeorg,tanggal,tahuntanam from ".$dbname.".kebun_interval_panen_vw
    where  tanggal like '".$periode."%' and kodeorg like '".$afdId."%'  order by kodeorg asc,tanggal asc"; 
}
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $tanggalArr[$res['tanggal']]=$res['tanggal'];
}   

// data panen sd bulan ini
$panen="select kodeorg,tanggal,tahuntanam,luasareaproduktif as luas from ".$dbname.".kebun_interval_panen_vw
    where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$unit."%'  order by kodeorg asc,tanggal asc";
if($afdId!='')
{
    $panen="select kodeorg,tanggal,tahuntanam,luasareaproduktif as luas from ".$dbname.".kebun_interval_panen_vw
    where (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%' order by kodeorg asc,tanggal asc";
}
$query=mysql_query($panen) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $adt=substr($res['kodeorg'],0,6);
    $kodeorgArr[$res['kodeorg']]=$res['kodeorg'];
    $tanggalsdArr[$res['tanggal']]=$res['tanggal'];
    $dzArr[$res['kodeorg']][$res['tanggal']]='P';
    $dThntnm2[$res['kodeorg']]=$res['tahuntanam'];
    //$dtLuas[$adt.$res['tahuntanam']]+=$res['luas'];
}   
 
// susun rotasi
if(!empty($kodeorgArr))foreach($kodeorgArr as $koko){
    // bulan ini

    $afd=substr($koko,0,6);
    if(!empty($tanggalArr))foreach($tanggalArr as $tata){
        $bln=substr($tata,5,2);
        $kemarin = strtotime('-1 day',strtotime($tata));
        $kemarin = date('Y-m-d',$kemarin);
        
        //exit("Error:masup".$bln);
        if(($dzArr[$koko][$tata]=='P')and($dzArr[$koko][$kemarin]!='P')){  
            $dzRot[$afd.$dThntnm2[$koko]][$bln]+=1;
            $dThntnm[$dThntnm2[$koko]]=$dThntnm2[$koko];
            $dAfd[$afd]=$afd;
            $jmlhRowBlok[$bln][$koko]=$koko;
         }
        
           
    }    
    // sd bulan ini
    if(!empty($tanggalsdArr))foreach($tanggalsdArr as $tata){
        $kemarin = strtotime('-1 day',strtotime($tata));
        $kemarin = date('Y-m-d',$kemarin);
         $bln=substr($tata,5,2);
        if(($dzArr[$koko][$tata]=='P')and($dzArr[$koko][$kemarin]!='P')){
            if($bln!='11'){
                $dzRot[$afd.$dThntnm2[$koko]][$bln]+=1;
                $dThntnm[$dThntnm2[$koko]]=$dThntnm2[$koko];
                $dAfd[$afd]=$afd;
                $jmlhRowBlok[$bln][$koko]=$koko;
            }
           // $dzRotSi[$afd.$dThntnm2[$koko]]+=1;
        }
    }    
    
}
//array_multisort($jmlhRowBlok2);
//foreach($jmlhRowBlok2 as $lstBlokData)
//{
//   if(($dtAfdS!=substr($lstBlokData,0,6))&&($ertThn!=$dThntnm2[$lstBlokData]))
//    {
//        $dtAfdS=substr($lstBlokData,0,6);
//        $ertThn=$dThntnm2[$lstBlokData];
//        $dtRowSi[$dtAfdS.$ertThn]=1;
//    }
//    else
//    {
//        if($ertThn!=$dThntnm2[$lstBlokData])
//        {
//             $ertThn=$dThntnm2[$lstBlokData];
//             $dtRowSi[$dtAfdS.$ertThn]+=1;
//        }
//        else if($dtAfdS!=substr($lstBlokData,0,6))
//        {
//            $dtAfdS=substr($lstBlokData,0,6);
//            $dtRowSi[$dtAfdS.$ertThn]+=1;
//        }
//        else {
//            $dtRowSi[$dtAfdS.$ertThn]+=1;
//        }
//    }
//}
//echo"<pre>";
//print_r($dtRowSi);
//echo"</pre>";
//exit("Error:masup");
foreach($jmlhRowBlok as $blnBlok=>$lstBlok)
{
   array_multisort($lstBlok);
   foreach($lstBlok as $dtKodeBlok)
   {
        if(($dtAfdS!=substr($dtKodeBlok,0,6))&&($ertThn!=$dThntnm2[$dtKodeBlok]))
        {
            $dtAfdS=substr($dtKodeBlok,0,6);
            $ertThn=$dThntnm2[$dtKodeBlok];
            $dtRow[$dtAfdS.$ertThn][$blnBlok]=1;
        }
        else
        {
            if($ertThn!=$dThntnm2[$dtKodeBlok])
            {
                 $ertThn=$dThntnm2[$dtKodeBlok];
                 $dtRow[$dtAfdS.$ertThn][$blnBlok]+=1;
            }
            else if($dtAfdS!=substr($dtKodeBlok,0,6))
            {
                $dtAfdS=substr($dtKodeBlok,0,6);
                $dtRow[$dtAfdS.$ertThn][$blnBlok]+=1;
            }
            else {
                $dtRow[$dtAfdS.$ertThn][$blnBlok]+=1;
            }
        }
   }
}
 


   
$sRotasiBudget="select distinct rotasi,a.kodeorg,b.thntnm from ".$dbname.".bgt_budget a left join 
                ".$dbname.".bgt_blok b on a.kodeorg=b.kodeblok
                where a.tahunbudget='".$tahun."' and a.kodeorg like '".$unit."%' and kegiatan=611010101";
if($afdId!='')
{
    $sRotasiBudget="select distinct rotasi,a.kodeorg,b.thntnm from ".$dbname.".bgt_budget a left join 
                   ".$dbname.".bgt_blok b on a.kodeorg=b.kodeblok
                   where a.tahunbudget='".$tahun."' and a.kodeorg like '".$afdId."%' and kegiatan=611010101";
}
//exit("Error:".$sRotasiBudget);
$qRotasiBudget=mysql_query($sRotasiBudget) or die(mysql_error());
while($rRotasiBudget=mysql_fetch_assoc($qRotasiBudget))
{
    $adf=substr($rRotasiBudget['kodeorg'],0,6);
    @$dzRot[$rRotasiBudget['kodeorg']]['bib']+=$rRotasiBudget['rotasi']/12;
    $dzRotBgt[$adf.$rRotasiBudget['thntnm']]+=$dzRot[$rRotasiBudget['kodeorg']]['bib']*(intval($bulan));
}
#rotasi end#

$jafd=count($dtAfd);
if($jafd==0)
{
    exit("Error:Data TM Kosong");
}
if($proses=='excel')
{
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
    $tab.="<table border=0>
         <tr>
            <td colspan=8 align=left><font size=3>DATA PANEN TAHUN ".$tahun."</font></td>
            <td colspan=6 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
         </tr> 
         <tr><td colspan=14 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>";
        if($afdId!='')
        {
            $tab.="<tr><td colspan=14 align=left>".$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")</td></tr>";
        }
    $tab.="</table>";
}



$brdr0;
$bgcoloraja="";
if($preview=='excel')
{
     $bgcoloraja="bgcolor=#DEDEDE"; 
     $brdr=1;
}
    $tab.=$judul;

    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>";
    $tab.="<td rowspan=3  align=center ".$bgcoloraja.">".$_SESSION['lang']['afdeling']."</td>";
    $tab.="<td rowspan=3  align=center ".$bgcoloraja.">".$_SESSION['lang']['tahuntanam']."</td>";
    $tab.="<td rowspan=3  align=center ".$bgcoloraja.">".$_SESSION['lang']['luas']."</td>";
    $tab.="<td rowspan=3  align=center ".$bgcoloraja.">UMUR TANAMAN</td>";
    $tab.="<td align=center colspan=".(4*intval($bulan)).">".$_SESSION['lang']['realisasi']."</td>";
    $tab.="<td rowspan=2  colspan=4 align=center ".$bgcoloraja.">".$_SESSION['lang']['sdbulanini']."</td>";
    $tab.="<td rowspan=2  colspan=4 align=center ".$bgcoloraja.">BUDGET  S.D B INI</td>";
    $tab.="<td rowspan=2  colspan=4 align=center ".$bgcoloraja.">VARIAN (%)</td></tr>";
    $tab.="<tr>";
    //exit("Error:".$tab);
    for($ard=1;$ard<=intval($bulan);$ard++){
       
        if($ard<10)
        {
            $ert="0".$ard;
            $tab.="<td align=center colspan=4 ".$bgcoloraja.">".$optBulan[$ert]."</td>";
        }
        else
        {
             $tab.="<td align=center colspan=4 ".$bgcoloraja.">".$optBulan[$ard]."</td>";
        }
        
    }
    $tab.="</tr><tr>";
   for($ard=1;$ard<=(intval($bulan)+3);$ard++){
        $tab.="<td align=center  ".$bgcoloraja.">PROD. (TON)</td>";
        $tab.="<td align=center  ".$bgcoloraja.">OUT  PUT (TON/HK) </td>";
        $tab.="<td align=center  ".$bgcoloraja.">BJR (KG)</td>";
        $tab.="<td align=center  ".$bgcoloraja.">ROTASI </td>";
   }
    $tab.="</tr></thead><tbody>";
    foreach($dAfd as $sltAfd){
        foreach($dThntnm as $lstThnTanam){
            if($dtLuas[$sltAfd.$lstThnTanam]!='')
            {
            $umr=$tahun-$lstThnTanam;
            if($umr!=0)
            {
               
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$sltAfd."</td>";
                $tab.="<td>".$lstThnTanam."</td>";
                $tab.="<td align=right>".number_format($dtLuas[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".$umr."</td>";
                 for($ard=1;$ard<=intval($bulan);$ard++){
                    if($ard<10)
                    {
                        //
                        $ert="0".$ard;
                        @$prod[$sltAfd.$lstThnTanam][$ert]=$dtProdKg[$sltAfd.$lstThnTanam][$ert]/1000;
                        @$drtHk[$sltAfd.$lstThnTanam][$ert]=$prod[$sltAfd.$lstThnTanam][$ert]/$dtProdJmhk[$sltAfd.$lstThnTanam][$ert];
                        @$dtBjr[$sltAfd.$lstThnTanam][$ert]=$dtProdKg[$sltAfd.$lstThnTanam][$ert]/$dtProdJjg[$sltAfd.$lstThnTanam][$ert];
                        @$dtRotasi[$sltAfd.$lstThnTanam][$ert]=$dzRot[$sltAfd.$lstThnTanam][$ert]/$dtRow[$sltAfd.$lstThnTanam][$ert];
                        $tab.="<td align=right>".number_format($prod[$sltAfd.$lstThnTanam][$ert],2)."</td>";
                        $tab.="<td align=right>".number_format($drtHk[$sltAfd.$lstThnTanam][$ert],2)."</td>";
                        $tab.="<td align=right>".number_format($dtBjr[$sltAfd.$lstThnTanam][$ert],2)."</td>";
                        $tab.="<td align=right>".number_format($dtRotasi[$sltAfd.$lstThnTanam][$ert],0)."</td>";
                        $totKg[$ert]+=$dtProdKg[$sltAfd.$lstThnTanam][$ert];
                        $totJjg[$ert]+=$dtProdJjg[$sltAfd.$lstThnTanam][$ert];
                        $totHk[$ert]+=$dtProdJmhk[$sltAfd.$lstThnTanam][$ert];
                        $totRot[$ert]+=$dtRotasi[$sltAfd.$lstThnTanam][$ert];
                        $dzRotSi[$sltAfd.$lstThnTanam]+=$dtRotasi[$sltAfd.$lstThnTanam][$ert];
                    }
                    else
                    {
                        @$prod[$sltAfd.$lstThnTanam][$ard]=$dtProdKg[$sltAfd.$lstThnTanam][$ard]/1000;
                        @$drtHk[$sltAfd.$lstThnTanam][$ard]=$prod[$sltAfd.$lstThnTanam][$ard]/$dtProdJmhk[$sltAfd.$lstThnTanam][$ard];
                        @$dtBjr[$sltAfd.$lstThnTanam][$ard]=$dtProdKg[$sltAfd.$lstThnTanam][$ard]/$dtProdJjg[$sltAfd.$lstThnTanam][$ard];
                        @$dtRotasi[$sltAfd.$lstThnTanam][$ard]=$dzRot[$sltAfd.$lstThnTanam][$ard]/$dtRow[$sltAfd.$lstThnTanam][$ard];
                        $tab.="<td align=right>".number_format($prod[$sltAfd.$lstThnTanam][$ard],2)."</td>";
                        $tab.="<td align=right>".number_format($drtHk[$sltAfd.$lstThnTanam][$ert],2)."</td>";
                        $tab.="<td align=right>".number_format($dtBjr[$sltAfd.$lstThnTanam][$ert],2)."</td>";
                        $tab.="<td align=right>".number_format($dtRotasi[$sltAfd.$lstThnTanam][$ard],0)."</td>";
                        $totKg[$ard]+=$dtProdKg[$sltAfd.$lstThnTanam][$ard];
                        $totJjg[$ard]+=$dtProdJjg[$sltAfd.$lstThnTanam][$ard];
                        $totHk[$ard]+=$dtProdJmhk[$sltAfd.$lstThnTanam][$ard];
                        $totRot[$ard]+=$dtRotasi[$sltAfd.$lstThnTanam][$ard];
                        $dzRotSi[$sltAfd.$lstThnTanam]+=$dtRotasi[$sltAfd.$lstThnTanam][$ard];
                    }
                    
                }
                @$prodSi[$sltAfd.$lstThnTanam]=$dtProdKgSi[$sltAfd.$lstThnTanam]/1000;
                @$drtHkSi[$sltAfd.$lstThnTanam]=$prodSi[$sltAfd.$lstThnTanam]/$dtProdJmhkSi[$sltAfd.$lstThnTanam];
                @$dtBjrSi[$sltAfd.$lstThnTanam]=$dtProdKgSi[$sltAfd.$lstThnTanam]/$dtProdJjgSi[$sltAfd.$lstThnTanam];
                
                $totKgSi+=$dtProdKgSi[$sltAfd.$lstThnTanam];
                $totJjgSi+=$dtProdJjgSi[$sltAfd.$lstThnTanam];
                $totHkSi+=$dtProdJmhkSi[$sltAfd.$lstThnTanam];
                $totRotSi+=$dzRotSi[$sltAfd.$lstThnTanam];
                $tab.="<td align=right>".number_format($prodSi[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($drtHkSi[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($dtBjrSi[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($dzRotSi[$sltAfd.$lstThnTanam],0)."</td>";
                
                @$prodBgt[$sltAfd.$lstThnTanam]=$dtbgtkg[$sltAfd.$lstThnTanam]/1000;
                @$drtHkBgt[$sltAfd.$lstThnTanam]=$prodBgt[$sltAfd.$lstThnTanam]/$dtbgthk[$sltAfd.$lstThnTanam];
                @$dtBjrBgt[$sltAfd.$lstThnTanam]=$dtbgtkg[$sltAfd.$lstThnTanam]/$dtbgtjjg[$sltAfd.$lstThnTanam];
                $totKgBgt+=$dtbgtkg[$sltAfd.$lstThnTanam];
                $totJjgBgt+=$dtProdJjgSi[$sltAfd.$lstThnTanam];
                $totHkBgt+=$dtbgthk[$sltAfd.$lstThnTanam];
                $totRotBgt+=$dzRotBgt[$sltAfd.$lstThnTanam];
                $tab.="<td align=right>".number_format($prodBgt[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($drtHkBgt[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($dtBjrBgt[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($dzRotBgt[$sltAfd.$lstThnTanam],0)."</td>";
                @$totVarKg[$sltAfd.$lstThnTanam]=(($prodBgt[$sltAfd.$lstThnTanam]-$prodSi[$sltAfd.$lstThnTanam])/$prodBgt[$sltAfd.$lstThnTanam])*100;
                @$totVarHk[$sltAfd.$lstThnTanam]=(($drtHkBgt[$sltAfd.$lstThnTanam]-$drtHkSi[$sltAfd.$lstThnTanam])/$drtHkBgt[$sltAfd.$lstThnTanam])*100;
                @$totVarBjr[$sltAfd.$lstThnTanam]=(($dtBjrBgt[$sltAfd.$lstThnTanam]-$dtBjrSi[$sltAfd.$lstThnTanam])/$dtBjrBgt[$sltAfd.$lstThnTanam])*100;
                @$totVarRot[$sltAfd.$lstThnTanam]=(($dzRotBgt[$sltAfd.$lstThnTanam]-$dzRotSi[$sltAfd.$lstThnTanam])/$dzRotBgt[$sltAfd.$lstThnTanam])*100;
                $tab.="<td align=right>".number_format($totVarKg[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($totVarHk[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($totVarBjr[$sltAfd.$lstThnTanam],2)."</td>";
                $tab.="<td align=right>".number_format($totVarRot[$sltAfd.$lstThnTanam],0)."</td>";
            }
            }
        }
    }
    $tab.="<tr class=rowcontent>";
    $tab.="<td colspan=4>".$_SESSION['lang']['total']."</td>";
     for($ard=1;$ard<=intval($bulan);$ard++){
        if($ard<10)
        {
            $ert="0".$ard;
            @$totProd[$ert]=$totKg[$ert]/1000;
            @$totHk[$ert]=$totProd[$ert]/$totHk[$ert];
            @$totBjr[$ert]=$totKg[$ert]/$totJjg[$ert];
            $tab.="<td align=right>".number_format($totProd[$ert],2)."</td>";
            $tab.="<td align=right>".number_format($totHk[$ert],2)."</td>";
            $tab.="<td align=right>".number_format($totBjr[$ert],2)."</td>";
            $tab.="<td align=right>".number_format($totRot[$ert],0)."</td>";
           
        }
        else
        {
            @$totProd[$ard]=$totKg[$ard]/1000;
            @$totHk[$ard]=$totProd[$ard]/$totHk[$ard];
            @$totBjr[$ard]=$totKg[$ard]/$totJjg[$ard];
            $tab.="<td align=right>".number_format($totProd[$ard],2)."</td>";
            $tab.="<td align=right>".number_format($totHk[$ard],2)."</td>";
            $tab.="<td align=right>".number_format($totBjr[$ard],2)."</td>";
            $tab.="<td align=right>".number_format($totRot[$ard],0)."</td>";
        }
     }
          @$totProdSi=$totKgSi/1000;
          @$totHkSidr=$totProdSi/$totHkSi;
          @$totBjrSi=$totKgSi/$totJjgSi;
        $tab.="<td align=right>".number_format($totProdSi,2)."</td>";
        $tab.="<td align=right>".number_format($totHkSidr,2)."</td>";
        $tab.="<td align=right>".number_format($totBjrSi,2)."</td>";
        $tab.="<td align=right>".number_format($totRotSi,0)."</td>";
        @$totProdBgt=$totKgBgt/1000;
        @$totHkBgt=$totProdBgt/$totHkBgt;
        @$totBjrBgt=$totKgBgt/$totJjgBgt;
        $tab.="<td align=right>".number_format($totProdBgt,2)."</td>";
        $tab.="<td align=right>".number_format($totHkBgt,2)."</td>";
        $tab.="<td align=right>".number_format($totBjrBgt,2)."</td>";
        $tab.="<td align=right>".number_format($totRotBgt,0)."</td>";
        @$totVarKgSi=(($totProdBgt-$totProdSi)/$totProdBgt)*100;
        @$totVarHkSi=(($totHkBgt-$totHkSidr)/$totHkBgt)*100;
        @$totVarBjrSi=(($totBjrBgt-$totBjrSi)/$totBjrBgt)*100;
        @$totVarRotSi=(($totRotBgt-$totRotSi)/$totRotBgt)*100;
        $tab.="<td align=right>".number_format($totVarKgSi,2)."</td>";
        $tab.="<td align=right>".number_format($totVarHkSi,2)."</td>";
        $tab.="<td align=right>".number_format($totVarBjrSi,2)."</td>";
        $tab.="<td align=right>".number_format($totVarRotSi,0)."</td>";
        $tab.="</tr>";
	$tab.="</tbody></table>";	
        //exit("Error:".$tab);
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="lbm_produksiblnan_".$unit.$periode;
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

    default:
    break;
}
	
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];

$optNmorg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$_POST['kdPt']==''?$kdPt=$_GET['kdPt']:$kdPt=$_POST['kdPt'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periodeDt']==''?$periodeDt=$_GET['periodeDt']:$periodeDt=$_POST['periodeDt'];
$thnbgt=explode("-",$periodeDt);


if($periodeDt=='')
{
    exit("Error:Tahun Tidak Boleh Kosong");
}
$nmpt=$unit=$_SESSION['lang']['all'];

if($kdUnit!='')
{
    $whreblok="kodeorg like '".$kdUnit."%'";
    $whre="kodeblok like '".$kdUnit."%'";
    $whrakt="and blok like '".$kdUnit."%'";
    $unit=$optNmorg[$kdUnit];
}
elseif($kdPt!='')
{
    $nmpt=$optNmorg[$kdPt];
    $whreblok=" substr(kodeorg,1,4) in (";
    $whre=" substr(kodeblok,1,4) in (";
    $whrakt=" and substr(blok,1,4) in (";
   
    $sKod="select distinct kodeorganisasi from ".$dbname.".organisasi where induk ='".$kdPt."' and tipe='KEBUN'";
    $qKod=mysql_query($sKod) or die(mysql_error($conn));
    $rTot=mysql_num_rows($qKod);
    while($rKod=mysql_fetch_assoc($qKod))
    {
        $nord+=1;
        $whreblok.="'".$rKod['kodeorganisasi']."'";
        $whre.="'".$rKod['kodeorganisasi']."'";
        $whrakt.="'".$rKod['kodeorganisasi']."'";
        if($nord<$rTot){
            $whreblok.=",";
            $whre.=",";
            $whrakt.=",";
        }
    }
    $whreblok.=")";
    $whre.=")";
    $whrakt.=")";
}
elseif($kdPt=='')
{
   $whreblok=" substr(kodeorg,1,4) in (";
    $whre=" substr(kodeblok,1,4) in (";
    $whrakt=" and substr(blok,1,4) in (";
   
    $sKod="select distinct kodeorganisasi from ".$dbname.".organisasi  where tipe='KEBUN'";
    $qKod=mysql_query($sKod) or die(mysql_error($conn));
    $rTot=mysql_num_rows($qKod);
    while($rKod=mysql_fetch_assoc($qKod))
    {
        $nord+=1;
        $whreblok.="'".$rKod['kodeorganisasi']."'";
        $whre.="'".$rKod['kodeorganisasi']."'";
        $whrakt.="'".$rKod['kodeorganisasi']."'";
        if($nord<$rTot){
            $whreblok.=",";
            $whre.=",";
            $whrakt.=",";
        }
    }
    $whreblok.=")";
    $whre.=")";
    $whrakt.=")";
}
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
if($proses!='getData')
        {
        
        ////merah luas
        $sLuasMrh="select sum(luasareaproduktif) as luas,substr(kodeorg,1,6) as afd,tahuntanam 
                   from ".$dbname.".setup_blok where ".$whreblok." and statusblok='TM' group by substr(kodeorg,1,6),tahuntanam 
                   order by substr(kodeorg,1,6),tahuntanam asc";
        //exit("Error".$sLuasMrh);
        $qLuasMrh=mysql_query($sLuasMrh) or die(mysql_error($conn));
        while($rLuasMrh=mysql_fetch_assoc($qLuasMrh))
        {
            $sPt="select distinct induk from ".$dbname.".organisasi where kodeorganisasi='".substr($rLuasMrh['afd'],0,4)."'";
           // exit("error:".$sPt);
            $qPt=mysql_query($sPt) or die(mysql_error($conn));
            $rPt=mysql_fetch_assoc($qPt);
            $dtPt[$rLuasMrh['afd']]=$rPt['induk'];
            $dtLuas[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rLuasMrh['luas'];
            $dtAfdeling[$rLuasMrh['afd']]=$rLuasMrh['afd'];
            $dtThnTnm[$rLuasMrh['afd'].$rLuasMrh['tahuntanam']]=$rLuasMrh['tahuntanam'];
            $amThnTnm[$rLuasMrh['tahuntanam']]=$rLuasMrh['tahuntanam'];
            $dtThnAfd[$rLuasMrh['tahuntanam'].$rLuasMrh['afd']]=$rLuasMrh['afd'];
        }
        ////biru telur asin (budget)Luas Ambil dari bgt_blok ambil sum(hathnini+nonproduktif)
        $sLuasBgt="select sum(hathnini) as luasbgt,thntnm,substr(kodeblok,1,6) as afd from ".$dbname.".bgt_blok where 
                   ".$whre." and tahunbudget='".$thnbgt[0]."' and statusblok='TM' group by substr(kodeblok,1,6),thntnm order by substr(kodeblok,1,6),thntnm asc";
        // exit("Error".$sLuasBgt);
        $qLuasBgt=mysql_query($sLuasBgt) or die(mysql_error($conn));
        while($rLuasBgt=mysql_fetch_assoc($qLuasBgt))
        {
            if($rLuasBgt['luasbgt']!=0)
            {
                $dtLuasBgt[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['luasbgt'];
                $dtAfdeling[$rLuasBgt['afd']]=$rLuasBgt['afd'];
                $dtThnTnm[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['thntnm'];
                $dtThnAfd[$rLuasBgt['thntnm'].$rLuasBgt['afd']]=$rLuasBgt['afd'];
                $amThnTnm[$rLuasBgt['thntnm']]=$rLuasBgt['thntnm'];
               
            }
        }
        for($asr5=1;$asr5<=12;$asr5++)
        {
            if($asr5<10)
            {
                if($asr5==1)
                {
                    $field5="sum(kg0".$asr5.") as kg01";
                }
                else
                {
                    $field5.=",sum(kg0".$asr5.") as kg0".$asr5."";
                }
            }
            else
            {
                $field5.=",sum(kg".$asr5.") as kg".$asr5."";
            }


        }
        ////berat budget bln ini
        $sBrtBgt="select distinct ".$field5.",substr(kodeblok,1,6) as afd,thntnm,sum(kgsetahun) as kgsetahun 
                  from ".$dbname.".bgt_produksi_kbn_kg_vw 
                  where ".$whre." and tahunbudget='".$periodeDt."'  group by substr(kodeblok,1,6),thntnm";
         //exit("Error".$sBrtBgt);
        $qBrtBgt=mysql_query($sBrtBgt) or die(mysql_error($conn));
        while($rBrtBgt=mysql_fetch_assoc($qBrtBgt))
        {
            
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['01']=$rBrtBgt['kg01'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['02']=$rBrtBgt['kg02'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['03']=$rBrtBgt['kg03'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['04']=$rBrtBgt['kg04'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['05']=$rBrtBgt['kg05'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['06']=$rBrtBgt['kg06'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['07']=$rBrtBgt['kg07'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['08']=$rBrtBgt['kg08'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['09']=$rBrtBgt['kg09'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['10']=$rBrtBgt['kg10'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['11']=$rBrtBgt['kg11'];
                $brtBgt[$rBrtBgt['thntnm'].$rBrtBgt['afd']]['12']=$rBrtBgt['kg12'];
                $bgtThnan[$rBrtBgt['thntnm'].$rBrtBgt['afd']]=$rBrtBgt['kgsetahun'];
        }
        
        ////berat aktual bln ini
        $sAktual="select sum(kgwbtanpabrondolan) as brtaktual,substr(blok,1,6) as afd,tahuntanam,substr(periode,6,2) as bln
                  from ".$dbname.".kebun_spb_vs_rencana_blok_vw where
                  substr(periode,1,4)='".$periodeDt."' ".$whrakt." group by substr(blok,1,6),tahuntanam,periode";
        //exit("Error".$sAktual);
        $qAktual=mysql_query($sAktual) or die(mysql_error($conn));
        while($rAktual=mysql_fetch_assoc($qAktual))
        {
            $brtAktual[$rAktual['tahuntanam'].$rAktual['afd']][$rAktual['bln']]=$rAktual['brtaktual'];
        }
        
       
$brd=0;
$agdDt='';
$lrt=0;
if(($proses=='excel')||($proses=='pdf'))
{
    $brd=1;
    $bgcoloraja="bgcolor=#DEDEDE ";
    $tab.="<table border=0>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['pt']." :[ ".$nmpt." ] ".$_SESSION['lang']['unit']." : [ ".$unit." ]</td></tr>";
    $tab.="<tr><td colspan=13 align=center>LAPORAN PRODUKSI s.d. ".$thnbgt[1]."-".$thnbgt[0]." </td></tr>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['satuan']." : Kg TBS  </td></tr>";
    $tab.="<tr><td colspan=13>".$_SESSION['lang']['periode']." : ".$thnbgt[1]."-".$thnbgt[0]."  </td></tr>";
    $tab.="</table>";

////tampilannya nich
//

$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>";
$tab.="<thead><tr class=rowheader>";
$tab.="<td rowspan=2 align=center ".$bgcoloraja.">".$_SESSION['lang']['tahuntanam']."</td>";
$tab.="<td rowspan=2 align=center ".$bgcoloraja.">".$_SESSION['lang']['afdeling']."</td>";
$tab.="<td colspan=2  align=center ".$bgcoloraja.">LUAS TM (Ha)</td>";
for($der=1;$der<=12;$der++){
    $red=$der;
    if($der<10)
     {
        $red="0".$der;
     }
     
    $tab.="<td colspan=2  align=center ".$bgcoloraja.">".$optBulan[$red]."</td>";
}
$tab.="<td colspan=2  align=center ".$bgcoloraja.">".$_SESSION['lang']['total']."</td></tr>";
$tab.="<tr>";
   
for($der2=1;$der2<=14;$der2++){
    $tab.="<td align=center ".$bgcoloraja.">Aktual</td>";
    $tab.="<td align=center ".$bgcoloraja.">Budget</td>";
}
$tab.="</tr></thead><tbody>";

foreach($amThnTnm as $lstAfdeling){
    foreach($dtAfdeling as $rthntnm){
        if($dtThnAfd[$lstAfdeling.$rthntnm]!=''){
            $subTotLuas[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
            $subTotLuasBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
            $sbTotLs[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
            $sbTotLsBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
            $tab.="<tr class=rowcontent>";
            $tab.="<td>".$lstAfdeling."</td>";
            $tab.="<td align=center>".$dtThnAfd[$lstAfdeling.$rthntnm]."</td>";
            $tab.="<td align=right>".number_format($dtLuas[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
            $tab.="<td align=right>".number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling budget
            foreach($optBulan as $ltBLn =>$dtBln){
                $tab.="<td align=right>".number_format($brtAktual[$lstAfdeling.$rthntnm][$ltBLn],2)."</td>";
                $tab.="<td align=right>".number_format($brtBgt[$lstAfdeling.$rthntnm][$ltBLn],2)."</td>";
                
                //total aktual per afd dan tahun tanam
                $totAktual[$lstAfdeling.$rthntnm]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                $totBudget[$lstAfdeling.$rthntnm]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];
                
                //subtotal per bulan
                $subTotAkt[$lstAfdeling][$ltBLn]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                $subTotBgt[$lstAfdeling][$ltBLn]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];
                
                //total per tahun tanaam per bulan
                $totThnAkt[$lstAfdeling][$ltBLn]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                $totThnBgt[$lstAfdeling][$ltBLn]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];
            }
            $tab.="<td align=right>".number_format($totAktual[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
            $tab.="<td align=right>".number_format($totBudget[$lstAfdeling.$rthntnm],2)."</td>";
            $subTotAktual[$lstAfdeling]+=$totAktual[$lstAfdeling.$rthntnm];
            $subTotBudget[$lstAfdeling]+=$totBudget[$lstAfdeling.$rthntnm];
            $TotAktualThn[$lstAfdeling]+=$totAktual[$lstAfdeling.$rthntnm];
            $TotBudgetThn[$lstAfdeling]+=$totBudget[$lstAfdeling.$rthntnm];
        }

    }
    if($lstAfdeling!=$agdDt)
    {
        $agdDt=$lstAfdeling;
      
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=2><b>Sub ".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right><b>".number_format($subTotLuas[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($subTotLuasBgt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling budget
       foreach($optBulan as $ltBLn =>$dtBln){
        $tab.="<td align=right><b>".number_format($totThnAkt[$lstAfdeling][$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($totThnBgt[$lstAfdeling][$ltBLn],2)."</b></td>";
       }
        $tab.="<td align=right><b>".number_format($subTotAktual[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right><b>".number_format($subTotBudget[$lstAfdeling],2)."</b></td>";
        $tab.="</tr>";
        
       
    }

}
   asort($amThnTnm);
 foreach($amThnTnm as $rthntnm){
    $tab.="<tr  class=rowcontent>";
    $tab.="<td><b>".$_SESSION['lang']['total']."</b></td>";
    $tab.="<td><b>".$rthntnm."</b></td>";
    $tab.="<td align=right><b>".number_format($sbTotLs[$rthntnm],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
    $tab.="<td align=right><b>".number_format($sbTotLsBgt[$rthntnm],2)."</b></td>";//luas per tahun tanam per afdeling budget
       foreach($optBulan as $ltBLn =>$dtBln){
        $tab.="<td align=right><b>".number_format($totThnAkt[$rthntnm][$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($totThnBgt[$rthntnm][$ltBLn],2)."</b></td>";
        $grndTotThnAkt[$ltBLn]+=$totThnAkt[$rthntnm][$ltBLn];
        $grndTotThnBgt[$ltBLn]+=$totThnBgt[$rthntnm][$ltBLn];
       }
    $tab.="<td align=right><b>".number_format($TotAktualThn[$rthntnm],2)."</b></td>";
    $tab.="<td align=right><b>".number_format($TotBudgetThn[$rthntnm],2)."</b></td>";
    $tab.="</tr>";
    $grndLuas+=$sbTotLs[$rthntnm];
    $grndLuasBgt+=$sbTotLsBgt[$rthntnm];
    $grandTotAkt+=$TotAktualThn[$rthntnm];
    $grandTotBgt+=$TotBudgetThn[$rthntnm];
    
 }
 $tab.="<tr  class=rowcontent>";
 $tab.="<td colspan=2><b>".$_SESSION['lang']['total']."</b></td>";
 $tab.="<td align=right><b>".number_format($grndLuas,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
 $tab.="<td align=right><b>".number_format($grndLuasBgt,2)."</b></td>";//luas per tahun tanam per afdeling budget
 foreach($optBulan as $ltBLn =>$dtBln){
    $tab.="<td align=right><b>".number_format($grndTotThnAkt[$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
    $tab.="<td align=right><b>".number_format($grndTotThnBgt[$ltBLn],2)."</b></td>";
 }
$tab.="<td align=right><b>".number_format($grandTotAkt,2)."</b></td>";
$tab.="<td align=right><b>".number_format($grandTotBgt,2)."</b></td>";
$tab.="</tr>";
 $tab.="</tbody></table>";
}
        }
switch($proses)
{
    
	case'getData':
        //exit("Error:masuk donks");	
            if($kdPt!='')
            {
                $sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                       where induk='".$kdPt."' and tipe='KEBUN' order by namaorganisasi asc";
            }
            else
            {
                 $sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                       where tipe='KEBUN' order by namaorganisasi asc";
            }
         //   exit("Error".$sOrg);
	$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	}
	echo $optorg;
	break;
	case'preview':
            
        foreach($amThnTnm as $lstAfdeling){
            foreach($dtAfdeling as $rthntnm){
            if($dtThnAfd[$lstAfdeling.$rthntnm]!=''){
                $subTotLuas[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
                $subTotLuasBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
                $sbTotLs[$lstAfdeling]+=$dtLuas[$lstAfdeling.$rthntnm];
                $sbTotLsBgt[$lstAfdeling]+=$dtLuasBgt[$lstAfdeling.$rthntnm];
                $tab.="<tr class=rowcontent>";
                $tab.="<td style=width:'30px'>".$lstAfdeling."</td>";
                $tab.="<td style=width:'30px' align=center>".$dtThnAfd[$lstAfdeling.$rthntnm]."</td>";
                $tab.="<td style=width:'30px' align=right>".number_format($dtLuas[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                $tab.="<td style=width:'30px' align=right>".number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling budget
                foreach($optBulan as $ltBLn =>$dtBln){
                    $tab.="<td align=right style=width:'30px'>".number_format($brtAktual[$lstAfdeling.$rthntnm][$ltBLn],2)."</td>";
                    $tab.="<td align=right style=width:'30px'>".number_format($brtBgt[$lstAfdeling.$rthntnm][$ltBLn],2)."</td>";

                    //total aktual per afd dan tahun tanam
                    $totAktual[$lstAfdeling.$rthntnm]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                    $totBudget[$lstAfdeling.$rthntnm]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];

                    //subtotal per bulan 
                    $subTotAkt[$lstAfdeling][$ltBLn]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                    $subTotBgt[$lstAfdeling][$ltBLn]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];

                    //total per tahun tanaam per bulan
                    $totThnAkt[$lstAfdeling][$ltBLn]+=$brtAktual[$lstAfdeling.$rthntnm][$ltBLn];
                    $totThnBgt[$lstAfdeling][$ltBLn]+=$brtBgt[$lstAfdeling.$rthntnm][$ltBLn];
                }
                $tab.="<td align=right style=width:'30px'>".number_format($totAktual[$lstAfdeling.$rthntnm],2)."</td>";//luas per tahun tanam per afdeling realisasi
                $tab.="<td align=right style=width:'30px'>".number_format($totBudget[$lstAfdeling.$rthntnm],2)."</td>";
                $subTotAktual[$lstAfdeling]+=$totAktual[$lstAfdeling.$rthntnm];
                $subTotBudget[$lstAfdeling]+=$totBudget[$lstAfdeling.$rthntnm];
                $TotAktualThn[$lstAfdeling]+=$totAktual[$lstAfdeling.$rthntnm];
                $TotBudgetThn[$lstAfdeling]+=$totBudget[$lstAfdeling.$rthntnm];
            }

        }
        if($lstAfdeling!=$agdDt)
        {
        $agdDt=$lstAfdeling;

        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=2><b>Sub ".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right><b>".number_format($subTotLuas[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($subTotLuasBgt[$lstAfdeling],2)."</b></td>";//luas per tahun tanam per afdeling budget
        foreach($optBulan as $ltBLn =>$dtBln){
        $tab.="<td align=right><b>".number_format($subTotAkt[$lstAfdeling][$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($subTotBgt[$lstAfdeling][$ltBLn],2)."</b></td>";
        }
        $tab.="<td align=right><b>".number_format($subTotAktual[$lstAfdeling],2)."</b></td>";
        $tab.="<td align=right><b>".number_format($subTotBudget[$lstAfdeling],2)."</b></td>";
        $tab.="</tr>";


        }

        }
        asort($amThnTnm);
        $barisdtr=count($amThnTnm);
        $afd=true;
        $brc=true;
        foreach($amThnTnm as $rthntnm){
        $tab.="<tr  class=rowcontent>";
        if($afd==true)
        {
        $tab.="<td><b>".$_SESSION['lang']['total']."</b></td>";
       
        $afd=false;
        }
        else
        {
            if($brc==true)
            {
                $tab.="<td  rowspan=".($barisdtr-1).">&nbsp;</td>";
                $brc=false; 
            }
        }
        $tab.="<td><b>".$rthntnm."</b></td>";
        $tab.="<td align=right><b>".number_format($sbTotLs[$rthntnm],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($sbTotLsBgt[$rthntnm],2)."</b></td>";//luas per tahun tanam per afdeling budget
        foreach($optBulan as $ltBLn =>$dtBln){
        $tab.="<td align=right><b>".number_format($totThnAkt[$rthntnm][$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($totThnBgt[$rthntnm][$ltBLn],2)."</b></td>";
        $grndTotThnAkt[$ltBLn]+=$totThnAkt[$rthntnm][$ltBLn];
        $grndTotThnBgt[$ltBLn]+=$totThnBgt[$rthntnm][$ltBLn];
        }
        $tab.="<td align=right><b>".number_format($TotAktualThn[$rthntnm],2)."</b></td>";
        $tab.="<td align=right><b>".number_format($TotBudgetThn[$rthntnm],2)."</b></td>";
        $tab.="</tr>";
        $grndLuas+=$sbTotLs[$rthntnm];
        $grndLuasBgt+=$sbTotLsBgt[$rthntnm];
        $grandTotAkt+=$TotAktualThn[$rthntnm];
        $grandTotBgt+=$TotBudgetThn[$rthntnm];

        }
        $tab.="<tr  class=rowcontent>";
        $tab.="<td colspan=2><b>Grand ".$_SESSION['lang']['total']."</b></td>";
        $tab.="<td align=right><b>".number_format($grndLuas,2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($grndLuasBgt,2)."</b></td>";//luas per tahun tanam per afdeling budget
        foreach($optBulan as $ltBLn =>$dtBln){
        $tab.="<td align=right><b>".number_format($grndTotThnAkt[$ltBLn],2)."</b></td>";//luas per tahun tanam per afdeling realisasi
        $tab.="<td align=right><b>".number_format($grndTotThnBgt[$ltBLn],2)."</b></td>";
        }
        $tab.="<td align=right><b>".number_format($grandTotAkt,2)."</b></td>";
        $tab.="<td align=right><b>".number_format($grandTotBgt,2)."</b></td>";
        $tab.="</tr>";
        echo $tab;
	break;
        case'excel':
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("Hms");
            $nop_="produksiTbsBulanan__".$kdUnit."__".$dte;
            $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
            gzwrite($gztralala, $tab);
            gzclose($gztralala);
            echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";	
	break;
	case'pdf':
	
           class PDF extends FPDF {
           function Header() {
            global $periodeDt;
            global $kdUnit;
            global $unit;  
            global $dbname;
            global $nmpt;
            global $kdPt;
            global $optBulan;
   
                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper("LAPORAN PRODUKSI BULANAN TAHUN ".$periodeDt),0,1,'L');
                //$this->Cell($width,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeDt),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['pt'].' : '.$nmpt,0,1,'L');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$unit,0,1,'L');
                $this->Cell(790,$height,' ',0,1,'R');
                
                $height = 12;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',4);
                
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
               
                $this->Cell(30,$height,$_SESSION['lang']['afdeling'],TLR,0,'C',1);
                $this->Cell(30,$height,$_SESSION['lang']['tahuntanam'],TLR,0,'C',1);
                $this->SetFont('Arial','B',5);
                $this->Cell(60,$height,"LUAS TM (Ha)",TLR,0,'C',1);
                for($der3=1;$der3<=12;$der3++){
                    $red=$der3;
                    if($der3<10)
                     {
                        $red="0".$der3;
                     }
                    $this->Cell(50,$height,$optBulan[$red],TBLR,0,'C',1);
                }
                $this->Cell(60,$height,$_SESSION['lang']['total'],TBLR,1,'C',1);
                
                $this->Cell(30,$height," ",BLR,0,'C',1);
                $this->Cell(30,$height," ",BLR,0,'C',1);
                $this->Cell(30,$height,"Aktual",TBLR,0,'C',1);
                $this->Cell(30,$height,"Budget",TBLR,0,'C',1);
                for($der3=1;$der3<=12;$der3++){
                        $this->Cell(25,$height,"Aktual",TBLR,0,'C',1);
                        $this->Cell(25,$height,"Budget",TBLR,0,'C',1);
                }
                $this->Cell(30,$height,"Aktual",TBLR,0,'C',1);
                $this->Cell(30,$height,"Budget",TBLR,1,'C',1);
          
                
          }
              function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 12;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',4);
            $i=0;
            
            foreach($amThnTnm as $lstAfdeling){
                foreach($dtAfdeling as $rthntnm){
                    if($dtThnAfd[$lstAfdeling.$rthntnm]!=''){
                                $pdf->Cell(30,$height,$lstAfdeling,TBLR,0,'C',1);
                                $pdf->Cell(30,$height,$dtThnAfd[$lstAfdeling.$rthntnm],TBLR,0,'C',1);
                                $pdf->Cell(30,$height,number_format($dtLuas[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(30,$height,number_format($dtLuasBgt[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                 foreach($optBulan as $ltBLn =>$dtBln){
                                    $pdf->Cell(25,$height,number_format($brtAktual[$lstAfdeling.$rthntnm][$ltBLn],2),TBLR,0,'R',1);
                                    $pdf->Cell(25,$height,number_format($brtAktual[$lstAfdeling.$rthntnm][$ltBLn],2),TBLR,0,'R',1);
                                 }
                                $pdf->Cell(30,$height,number_format($totAktual[$lstAfdeling.$rthntnm],2),TBLR,0,'R',1);
                                $pdf->Cell(30,$height,number_format($totBudget[$lstAfdeling.$rthntnm],2),TBLR,1,'R',1);
                    }
                }
                if($lstAfdeling!=$agdDt)
                {
                    $agdDt=$lstAfdeling;
                    $pdf->Cell(60,$height,"Sub ".$_SESSION['lang']['total'],TBLR,0,'C',1);
                    $pdf->Cell(30,$height,number_format($subTotLuas[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(30,$height,number_format($subTotLuasBgt[$lstAfdeling],2),TBLR,0,'R',1);
                    foreach($optBulan as $ltBLn =>$dtBln){
                        $pdf->Cell(25,$height,number_format($subTotAkt[$lstAfdeling][$ltBLn],2),TBLR,0,'R',1);
                        $pdf->Cell(25,$height,number_format($subTotBgt[$lstAfdeling][$ltBLn],2),TBLR,0,'R',1);
                    }
                    $pdf->Cell(30,$height,number_format($subTotAktual[$lstAfdeling],2),TBLR,0,'R',1);
                    $pdf->Cell(30,$height,number_format($subTotBudget[$lstAfdeling],2),TBLR,1,'R',1);
                }

            }
            $rwDt=count($amThnTnm);
            $erer=true;
            $dterr=true;
            foreach($amThnTnm as $rthntnm){
            if($erer==true)
            {
            $pdf->Cell(30,$height,$_SESSION['lang']['total'],TBLR,0,'C',1);
            $erer=false;
            }
            else
            {
              $pdf->Cell(30,$height," ",TBLR,0,'C',1);
            }
            $pdf->Cell(30,$height,$rthntnm,TBLR,0,'C',1);
            $pdf->Cell(30,$height,number_format($sbTotLs[$rthntnm],2),TBLR,0,'R',1);
            $pdf->Cell(30,$height,number_format($sbTotLsBgt[$rthntnm],2),TBLR,0,'R',1);
            $pdf->SetFont('Arial','',3);
            foreach($optBulan as $ltBLn =>$dtBln){
                $pdf->Cell(25,$height,number_format($totThnAkt[$rthntnm][$ltBLn],2),TBLR,0,'R',1);
                $pdf->Cell(25,$height,number_format($totThnBgt[$rthntnm][$ltBLn],2),TBLR,0,'R',1);
             }
            $pdf->SetFont('Arial','',4);
            $pdf->Cell(30,$height,number_format($TotAktualThn[$rthntnm],2),TBLR,0,'R',1);
            $pdf->Cell(30,$height,number_format($TotBudgetThn[$rthntnm],2),TBLR,1,'R',1);
             }

            $pdf->Cell(60,$height,"Grand ".$_SESSION['lang']['total'],TBLR,0,'C',1);
            $pdf->Cell(30,$height,number_format($grndLuas,2),TBLR,0,'R',1);
            $pdf->Cell(30,$height,number_format($grndLuasBgt,2),TBLR,0,'R',1);
            $pdf->SetFont('Arial','',3);
            foreach($optBulan as $ltBLn =>$dtBln){
                $pdf->Cell(25,$height,number_format($grndTotThnAkt[$ltBLn],2),TBLR,0,'R',1);
                $pdf->Cell(25,$height,number_format($grndTotThnBgt[$ltBLn],2),TBLR,0,'R',1);
             }
            
            $pdf->Cell(30,$height,number_format($grandTotAkt,2),TBLR,0,'R',1);
            $pdf->Cell(30,$height,number_format($grandTotBgt,2),TBLR,1,'R',1);
            $pdf->Output();
	break;
	
	
	default:
	break;
}
?>
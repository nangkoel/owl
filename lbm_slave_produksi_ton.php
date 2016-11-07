<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
//$arr="##klmpkBrg##kdUnit##periode##lokasi##statId##purId";
$sKlmpk="select kode,kelompok from ".$dbname.".log_5klbarang order by kode";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $rKelompok[$rKlmpk['kode']]=$rKlmpk['kelompok'];
}
$optNmOrang=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optInduk=makeOption($dbname, 'organisasi','kodeorganisasi,induk');
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
$_POST['afdId']==''?$afdId=$_GET['afdId']:$afdId=$_POST['afdId'];


$unitId=$_SESSION['lang']['all'];
$nmPrshn="Holding";
$purchaser=$_SESSION['lang']['all'];
if($periode=='')
{
    exit("Error: ".$_SESSION['lang']['periode']." required");
}
if($kdUnit!='')
{
    $unitId=$optNmOrg[$kdUnit];
}
else
{
    exit("Error:".$_SESSION['lang']['unit']." required");
}
$thn=explode("-",$periode);

//buat bi dan sbi
    if(strlen($thn[1])<2)
    {
        $field="kg0".$thn[1];
    }
    else
    {
        $field="kg".$thn[1];
    }
  
for($asr5=1;$asr5<=$thn[1];$asr5++)
{
    
        if(strlen($asr5)<2)
        {
            if($asr5==1)
            {
                $field5="kg0".$asr5;
            }
            else
            {
             $field5.="+kg0".$asr5;
            }
        }
        else
        {
            $field5.="+kg".$asr5;
        }
   
}
//array tahun tanam
$sThnTnm="select distinct thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw where 	
          kodeunit='".$kdUnit."' and tahunbudget='".$thn[0]."'  order by thntnm asc";
if($afdId!='')
{
    $sThnTnm="select distinct thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw where 	
          kodeblok like '".$afdId."%' and tahunbudget='".$thn[0]."'  order by thntnm asc";
}
$qThnTnm=mysql_query($sThnTnm) or die(mysql_error());
while($rThnTnm=mysql_fetch_assoc($qThnTnm))
{
    if(strlen($rThnTnm['thntnm'])=='4')
    {
        $dtThnTnm[]=$rThnTnm['thntnm'];
    }
}

//potensi produksi

//ambil luas dan thn taman dari budget
$sLuas="select distinct sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok 
        where substr(kodeblok,1,4)='".$kdUnit."' and tahunbudget='".$thn[0]."' group by thntnm";
if($afdId!='')
{
    $sLuas="select distinct sum(hathnini) as luas,thntnm from ".$dbname.".bgt_blok 
        where substr(kodeblok,1,6)='".$afdId."' and tahunbudget='".$thn[0]."' group by thntnm";
}
$qLuas=mysql_query($sLuas) or die(mysql_error());
while($rLuas=mysql_fetch_assoc($qLuas))
{
  $lsAnggran[$rLuas['thntnm']]+=$rLuas['luas']; 
  
  //,
}
//ambil luas dan thn taman dari setup_blok
$sLuasRealisasi="select distinct sum(luasareaproduktif) as luas,tahuntanam from ".$dbname.".setup_blok 
                 where substr(kodeorg,1,4)='".$kdUnit."' group by tahuntanam";
if($afdId!='')
{
    $sLuasRealisasi="select distinct sum(luasareaproduktif) as luas,tahuntanam from ".$dbname.".setup_blok 
                 where substr(kodeorg,1,6)='".$afdId."' group by tahuntanam";
}
$qLuasRealisasi=mysql_query($sLuasRealisasi) or die(mysql_error());
while($rLuasRealisasi=mysql_fetch_assoc($qLuasRealisasi))
{
    $lsRealisasi[$rLuasRealisasi['tahuntanam']]+=$rLuasRealisasi['luas'];
}
//ambil data kilogram dari budget
$sKgTaon="select distinct sum(kgsetahun) as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeunit='".$kdUnit."' group by thntnm";
if($afdId!='')
{
    $sKgTaon="select distinct sum(kgsetahun) as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeblok like '".$afdId."%' group by thntnm";
}
$qKgTaon=mysql_query($sKgTaon) or die(mysql_error());
while($rKgTaon=mysql_fetch_assoc($qKgTaon))
{
    @$kgSthn[$rKgTaon['thntnm']]+=$rKgTaon['kgstaun']/1000;
}
//budget kg bi->bulan ini
$sKgTaonbi="select distinct sum(".$field.") as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeunit='".$kdUnit."' group by thntnm";
if($afdId!='')
{
    $sKgTaonbi="select distinct sum(".$field.") as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeblok like '".$afdId."%' group by thntnm";
}
//exit("Error:".$sKgTaonbi);
$qKgTaonbi=mysql_query($sKgTaonbi) or die(mysql_error());
while($rKgTaonbi=mysql_fetch_assoc($qKgTaonbi))
{
    @$kgSthnBi[$rKgTaonbi['thntnm']]+=$rKgTaonbi['kgstaun']/1000;
}
//budget kg sbi->sampai bulan ini
$sKgTaonsbi="select distinct sum(".$field5.") as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeunit='".$kdUnit."' group by thntnm";
if($afdId!='')
{
  $sKgTaonsbi="select distinct sum(".$field5.") as kgstaun,thntnm from ".$dbname.".bgt_produksi_kbn_kg_vw 
          where tahunbudget='".$thn[0]."' and kodeblok like '".$afdId."%' group by thntnm";  
}
$qKgTaonsbi=mysql_query($sKgTaonsbi) or die(mysql_error());
while($rKgTaonsbi=mysql_fetch_assoc($qKgTaonsbi))
{
    @$kgSthnsBi[$rKgTaonsbi['thntnm']]+=$rKgTaonsbi['kgstaun']/1000;
}

//sensus kg
$sSensus="select distinct sum(kgsensus) as kgsensus, tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
          where periode='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
if($afdId!='')
{
   $sSensus="select distinct sum(kgsensus) as kgsensus, tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
          where periode='".$periode."' and substr(blok,1,6)='".$afdId."' group by tahuntanam"; 
}
//exit("Error:".$sSensus);
$qSensus=mysql_query($sSensus) or die(mysql_error());
while($rSensus=mysql_fetch_assoc($qSensus))
{
      $biSensus[$rSensus['tahuntanam']]+=$rSensus['kgsensus']/1000;
}

//echo "<pre>";
//print_r($biSensus);
//echo "</pre>";

$sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
          where periode<='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
if($afdId!='')
{ 
$sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
          where periode<='".$periode."' and substr(blok,1,6)='".$afdId."' group by tahuntanam"; 
}
//echo $sSensus;
$qSensus=mysql_query($sSensus) or die(mysql_error());
while($rSensus=mysql_fetch_assoc($qSensus))
{
    $sbiSensus[$rSensus['tahuntanam']]+=$rSensus['kgsensus']/1000;
}
if($thn[1]<7)
{
    $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
      where periode<'".$thn[0]."-07' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
    if($afdId!='')
    {
      $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
      where periode<'".$thn[0]."-07' and substr(blok,1,6)='".$afdId."' group by tahuntanam";  
    }
//    $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
//      where periode<='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
    $qSensus=mysql_query($sSensus) or die(mysql_error());
    while($rSensus=mysql_fetch_assoc($qSensus))
    {
      $senSmstr[$rSensus['tahuntanam']]+=$rSensus['kgsensus']/1000;
    } 
}
else if($thn[1]<13&&$thn[1]>6)
{
    $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
      where periode>'".$thn[0]."-06' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
      if($afdId!='')
      {
          $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
                    where periode>'".$thn[0]."-06' and substr(blok,1,6)='".$afdId."' group by tahuntanam";
      }
//    $sSensus="select distinct sum(kgsensus) as kgsensus,tahuntanam from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
//      where periode<='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
    $qSensus=mysql_query($sSensus) or die(mysql_error());
    while($rSensus=mysql_fetch_assoc($qSensus))
    {
      $senSmstr[$rSensus['tahuntanam']]+=$rSensus['kgsensus']/1000;
    } 
}

//REALISASI
$sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where periode='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
 if($afdId!='')
 {
    $sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where periode='".$periode."' and substr(blok,1,6)='".$afdId."' group by tahuntanam"; 
 }

$qRealisasi=mysql_query($sRealaisasi) or die(mysql_error());
while($rRealisasi=mysql_fetch_assoc($qRealisasi))
{
    @$biRealisasi[$rRealisasi['tahuntanam']]+=$rRealisasi['realisasi']/1000;
}
$sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  periode<='".$periode."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
 if($afdId!='')
 {
     $sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  periode<='".$periode."' and substr(blok,1,6)='".$afdId."' group by tahuntanam";
 }
$qRealisasi=mysql_query($sRealaisasi) or die(mysql_error());
while($rRealisasi=mysql_fetch_assoc($qRealisasi))
{
    @$sbiRealisasi[$rRealisasi['tahuntanam']]+=$rRealisasi['realisasi']/1000;
}

//produksi tahun lalu
$thnLalu=$thn[0]-1;
$period=$thnLalu."-".$thn[1];
$sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  periode<='".$period."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
 if($afdId!='')
 {
    $sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  periode<='".$period."' and substr(blok,1,6)='".$afdId."' group by tahuntanam"; 
 }
$qRealisasi=mysql_query($sRealaisasi) or die(mysql_error());
while($rRealisasi=mysql_fetch_assoc($qRealisasi))
{
    @$prodThnLalusbi[$rRealisasi['tahuntanam']]+=$rRealisasi['realisasi']/1000;
}
$sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  substr(periode,1,4)='".$thnLalu."' and substr(blok,1,4)='".$kdUnit."' group by tahuntanam";
 if($afdId!='')
 {
     $sRealaisasi="select distinct sum(nettotimbangan) as realisasi,tahuntanam from  ".$dbname.".kebun_spb_vs_rencana_blok_vw 
              where  substr(periode,1,4)='".$thnLalu."' and substr(blok,1,6)='".$afdId."' group by tahuntanam";
 }
$qRealisasi=mysql_query($sRealaisasi) or die(mysql_error());
while($rRealisasi=mysql_fetch_assoc($qRealisasi))
{
    @$prodThnLalu[$rRealisasi['tahuntanam']]+=$rRealisasi['realisasi']/1000;
}
//potensi produksi
$sPotensi="select distinct tahuntanam,klasifikasitanah,jenisbibit from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
           where  periode='".$periode."' and substr(blok,1,4)='".$kdUnit."' ";
 if($afdId!='')
 {
   $sPotensi="select distinct tahuntanam,klasifikasitanah,jenisbibit from ".$dbname.".kebun_spb_vs_rencana_blok_vw 
           where  periode='".$periode."' and substr(blok,1,6)='".$afdId."' ";  
 }
 //exit("error:".$sPotensi);
$qPotensi=mysql_query($sPotensi) or die(mysql_error());
while($rSensus=mysql_fetch_assoc($qPotensi))
{
      $umur=$thn[0]-$rSensus['tahuntanam'];
      $sPot="select distinct kgproduksi from ".$dbname.".kebun_5stproduksi where jenisbibit='".$rSensus['jenisbibit']."' and klasifikasitanah='".$rSensus['klasifikasitanah']."' and umur='".$umur."'";
      // exit("error:".$sPot);
      $qPot=mysql_query($sPot) or die(mysql_error());
      $rPot=mysql_fetch_assoc($qPot);
      $potProd[$rSensus['tahuntanam']]=$lsRealisasi[$rSensus['tahuntanam']]*$rPot['kgproduksi']/1000;
}
$varCek=count($dtThnTnm);
if($varCek<1)
{
    //array tahun tanam, kalo di budget kosong, ambil dari setup_blok
    $sThnTnm="select distinct tahuntanam as thntnm from ".$dbname.".setup_blok where 	kodeorg like '".$kdUnit."%'  order by tahuntanam asc";
    if($afdId!='')
    {
        $sThnTnm="select distinct tahuntanam as thntnm from ".$dbname.".setup_blok where 	
                  kodeorg like '".$afdId."%' order by tahuntanam asc";
    }

    $qThnTnm=mysql_query($sThnTnm) or die(mysql_error());
    while($rThnTnm=mysql_fetch_assoc($qThnTnm))
    {
        if(strlen($rThnTnm['thntnm'])=='4')
        {
            $dtThnTnm[]=$rThnTnm['thntnm'];
        }
    }    
//    exit("Error:Data Kosong");
}
$brdr=0;
$bgcoloraja='';
$cols=count($dataAfd)*3;
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=8 align=left><b>".$_GET['judul']."</b></td><td colspan=3 align=right><b>".$_SESSION['lang']['bulan']." : ".substr(tanggalnormal($periode),1,7)."</b></td></tr>
    <tr><td colspan=8 align=left>".$_SESSION['lang']['unit']." : ".$optNmOrg[$kdUnit]." </td></tr>";
    if($afdId!='')
    {
        $tab.="<tr><td colspan=8 align=left>".$_SESSION['lang']['unit']." : ".$optNmOrg[$kdUnit]." </td></tr>";
    }
    $tab.="<tr><td colspan=8 align=left>&nbsp;</td></tr>
    </table>";
}

	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['tahuntanam']."</td>
        <td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['umur']." (".$_SESSION['lang']['tahun'].")</td>
        <td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['luas']." (Ha)</td>";
        $tab.="<td ".$bgcoloraja." colspan=3>".$_SESSION['lang']['anggaran']." (TON)</td>";
        $tab.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['sensus']." (TON)</td>";
        $tab.="<td ".$bgcoloraja." colspan=2>".$_SESSION['lang']['realisasi']." (TON)</td>";
        $tab.="<td ".$bgcoloraja." colspan=2>% VARIAN REAL VS CENSUS</td>";
        $tab.="<td ".$bgcoloraja." colspan=2>% VARIAN REAL VS BUDGET</td>";
        $tab.="<td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['sbi']." (".$_SESSION['lang']['tahunlalu'].")</td><td ".$bgcoloraja." rowspan=2>CENSUS  SM-I/II</td>";
        $tab.="<td ".$bgcoloraja." rowspan=2>".$_SESSION['lang']['tahunlalu']."</td><td ".$bgcoloraja." rowspan=2>Potency ".$_SESSION['lang']['produksi']."</td></tr>";
        $tab.="<tr><td ".$bgcoloraja." >".$_SESSION['lang']['anggaran']."</td><td ".$bgcoloraja." >REAL</td><td ".$bgcoloraja." >".$_SESSION['lang']['setahun']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['bi']."</td>
               <td ".$bgcoloraja." >".$_SESSION['lang']['sbi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['bi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['sbi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['bi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['sbi']."</td>
               <td ".$bgcoloraja." >".$_SESSION['lang']['bi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['sbi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['bi']."</td><td ".$bgcoloraja." >".$_SESSION['lang']['sbi']."</td></tr>";
        $tab.="</thead>
	<tbody>";
        foreach($dtThnTnm as $lstThnTnm)
        {
            $tab.="<tr class=rowcontent><td>".$lstThnTnm."</td>";
            $umur=$periode-$lstThnTnm;
            $tab.="<td align=right>".$umur."</td>";
            $tab.="<td align=right>".number_format($lsAnggran[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($lsRealisasi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($kgSthn[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($kgSthnBi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($kgSthnsBi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($biSensus[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($sbiSensus[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($biRealisasi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($sbiRealisasi[$lstThnTnm],2)."</td>";
            @$snVsRealibi[$lstThnTnm]=$biRealisasi[$lstThnTnm]/$biSensus[$lstThnTnm]*100;
            @$snVsRealisbi[$lstThnTnm]=$sbiRealisasi[$lstThnTnm]/$sbiSensus[$lstThnTnm]*100;
            $tab.="<td align=right>".number_format($snVsRealibi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($snVsRealisbi[$lstThnTnm],2)."</td>";
            @$angVsRealibi[$lstThnTnm]=$biRealisasi[$lstThnTnm]/$kgSthnBi[$lstThnTnm]*100;
            @$angVsRealisbi[$lstThnTnm]=$sbiRealisasi[$lstThnTnm]/$kgSthnsBi[$lstThnTnm]*100;
            $tab.="<td align=right>".number_format($angVsRealibi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($angVsRealisbi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($prodThnLalusbi[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($senSmstr[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($prodThnLalu[$lstThnTnm],2)."</td>";
            $tab.="<td align=right>".number_format($potProd[$lstThnTnm],2)."</td>";
            $tab.="</tr>";
            $totLAngr+=$lsAnggran[$lstThnTnm];
            $totLReali+=$lsRealisasi[$lstThnTnm];
            $totKgStaon+=$kgSthn[$lstThnTnm];
            $totKgSthnBi+=$kgSthnBi[$lstThnTnm];
            $totkgSthnsBi+=$kgSthnsBi[$lstThnTnm];
            $totbiSensus+=$biSensus[$lstThnTnm];
            $totsbiSensus+=$sbiSensus[$lstThnTnm];
            $totbiRealisasi+=$biRealisasi[$lstThnTnm];
            $totsbiRealisasi+=$sbiRealisasi[$lstThnTnm];
            $totsnVsRealibi+=$snVsRealibi[$lstThnTnm];
            $totsnVsRealisbi+=$snVsRealisbi[$lstThnTnm];
            $totangVsRealibi+=$angVsRealibi[$lstThnTnm];
            $totprodThnLalusbi+=$prodThnLalusbi[$lstThnTnm];
            $totsenSmstr+=$senSmstr[$lstThnTnm];
            $totprodThnLalu+=$prodThnLalu[$lstThnTnm];
            $totpotProd+=$potProd[$lstThnTnm];
        }
            $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right>".number_format($totLAngr,2)."</td>";
            $tab.="<td align=right>".number_format($totLReali,2)."</td>";
            $tab.="<td align=right>".number_format($totKgStaon,2)."</td>";
            $tab.="<td align=right>".number_format($totKgSthnBi,2)."</td>";
            $tab.="<td align=right>".number_format($totkgSthnsBi,2)."</td>";
            $tab.="<td align=right>".number_format($totbiSensus,2)."</td>";
            $tab.="<td align=right>".number_format($totsbiSensus,2)."</td>";
            $tab.="<td align=right>".number_format($totbiRealisasi,2)."</td>";
            $tab.="<td align=right>".number_format($totsbiRealisasi,2)."</td>";
            $tab.="<td align=right>".number_format($totsnVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totsnVsRealisbi,2)."</td>";
            
            $tab.="<td align=right>".number_format($totangVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totangVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totprodThnLalusbi,2)."</td>";
            $tab.="<td align=right>".number_format($totsenSmstr,2)."</td>";
            $tab.="<td align=right>".number_format($totprodThnLalu,2)."</td>";
            $tab.="<td align=right>".number_format($totpotProd,2)."</td>";
            $tab.="</tr>";
        $tab.="</tbody></table>";
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_=$judul."_".$dte;
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
            global $periode,$judul;
            global $dataAfd;
            global $kdUnit;
            global $optNmOrg;  
            global $dbname;
            global $afdId;

                $this->SetFont('Arial','B',8);
                $this->Cell($width,$height,strtoupper($judul),0,1,'L');
                $this->Cell(790,$height,$_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periode),1,7),0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $this->Cell($width,$height,$_SESSION['lang']['unit'].' : '.$optNmOrg[$kdUnit],0,1,'L');
                if($afdId!='')
                {
                    $tinggiAkr=$this->GetY();
                    $ksamping=$this->GetX();
                    $this->SetY($tinggiAkr+20);
                    $this->SetX($ksamping);
                    $this->Cell($width,$height,$_SESSION['lang']['afdeling'].' : '.$optNmOrg[$afdId],0,1,'L');
                }
                $this->Cell(790,$height,' ',0,1,'R');
                $tinggiAkr=$this->GetY();
                $ksamping=$this->GetX();
                $this->SetY($tinggiAkr+20);
                $this->SetX($ksamping);
                $height = 15;
                $this->SetFillColor(220,220,220);
                $this->SetFont('Arial','B',7);
                $this->Cell(25,$height," ",TLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['umur'],TLR,0,'C',1);
                $this->Cell(60,$height," ",TLR,0,'C',1);
                $this->Cell(150,$height," ",TLR,0,'C',1);
                $this->Cell(100,$height," ",TLR,0,'C',1);
                $this->Cell(100,$height," ",TLR,0,'C',1);
                $this->Cell(60,$height,"% VARIAN",TLR,0,'C',1);
                $this->Cell(70,$height,"% VARIAN",TLR,0,'C',1);
                $this->Cell(30,$height,$_SESSION['lang']['sbi'],TLR,0,'C',1);
                $this->Cell(55,$height," ",TLR,0,'C',1);
                $this->Cell(55,$height,$_SESSION['lang']['tahunlalu'],TLR,0,'C',1);
                $this->Cell(40,$height," ",TLR,1,'C',1);
                
                $this->Cell(25,$height,$_SESSION['lang']['tahun'],LR,0,'C',1);
                $this->Cell(50,$height,'',LR,0,'C',1);
//                $tinggiAkr=$this->GetY();
//                $ksamping=$this->GetX();
//                $this->SetY($tinggiAkr);
//                $this->SetX($ksamping-572);
                $this->Cell(60,$height,$_SESSION['lang']['luas']." (Ha)",LR,0,'C',1);
                $this->Cell(150,$height,$_SESSION['lang']['anggaran']." (TON)",LR,0,'C',1);
                $this->Cell(100,$height,"CENSUS (TON)",LR,0,'C',1);
                $this->Cell(100,$height,$_SESSION['lang']['realisasi']." (TON)",LR,0,'C',1);
                $this->Cell(60,$height,"REAL VS",LR,0,'C',1);
                $this->Cell(70,$height,"REAL VS",LR,0,'C',1);
                $this->Cell(30,$height,"(".$_SESSION['lang']['tahunlalu'].")",LR,0,'C',1);
                $this->Cell(55,$height,"CENSUS",LR,0,'C',1);
                $this->Cell(55,$height,'',LR,0,'C',1);
                $this->Cell(40,$height,"POTENCY",LR,1,'C',1);
                
                $this->Cell(25,$height,$_SESSION['lang']['tanam'],LR,0,'C',1);
                $this->Cell(50,$height,($_SESSION['lang']['tahunlalu']),LR,0,'C',1);
                $this->Cell(60,$height," ",LR,0,'C',1);
                $this->Cell(150,$height," ",LR,0,'C',1);
                $this->Cell(100,$height," ",LR,0,'C',1);
                $this->Cell(100,$height," ",LR,0,'C',1);
                $this->Cell(60,$height,"CNS",LR,0,'C',1);
                $this->Cell(70,$height,"BUDGET",LR,0,'C',1);
                $this->Cell(30,$height,'',LR,0,'C',1);
                $this->Cell(55,$height,"SM-I/II ",LR,0,'C',1);
                $this->Cell(55,$height,"",LR,0,'C',1);
                $this->Cell(40,$height,$_SESSION['lang']['produksi'],LR,1,'C',1);
                
                $this->Cell(25,$height," ",BLR,0,'C',1);
                $this->Cell(50,$height," ",BLR,0,'C',1);
                $this->SetFont('Arial','B',6);
                $this->Cell(30,$height,"BUDGET",TBLR,0,'C',1);
                $this->Cell(30,$height,"REAL",TBLR,0,'C',1);
                
                $this->Cell(50,$height,$_SESSION['lang']['setahun'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['bi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['sbi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['bi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['sbi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['bi'],TBLR,0,'C',1);
                $this->Cell(50,$height,$_SESSION['lang']['sbi'],TBLR,0,'C',1);
                $this->Cell(30,$height,$_SESSION['lang']['bi'],TBLR,0,'C',1);
                $this->Cell(30,$height,$_SESSION['lang']['sbi'],TBLR,0,'C',1);
                $this->Cell(35,$height,$_SESSION['lang']['bi'],TBLR,0,'C',1);
                $this->Cell(35,$height,$_SESSION['lang']['sbi'],TBLR,0,'C',1);
                $this->SetFont('Arial','B',7);
                $this->Cell(30,$height,"",BLR,0,'C',1);#lalu
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(55,$height," ",BLR,0,'C',1);
                $this->Cell(40,$height," ",BLR,1,'C',1);
                
                 
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
            $height = 20;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','B',6);
            foreach($dtThnTnm as $lstThnTnm)
            {
                $umur=$periode-$lstThnTnm;
                $pdf->Cell(25,$height,$lstThnTnm,1,0,'C',1);
                $pdf->Cell(50,$height,$umur,1,0,'R',1);
                $pdf->Cell(30,$height,number_format($lsAnggran[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(30,$height,number_format($lsRealisasi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($kgSthn[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($kgSthnBi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($kgSthnsBi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($biSensus[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($sbiSensus[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($biRealisasi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(50,$height,number_format($sbiRealisasi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(30,$height,number_format($snVsRealibi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(30,$height,number_format($snVsRealisbi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(35,$height,number_format($angVsRealibi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(35,$height,number_format($angVsRealisbi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(30,$height,number_format($prodThnLalusbi[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(55,$height,number_format($senSmstr[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(55,$height,number_format($prodThnLalu[$lstThnTnm],2),1,0,'R',1);
                $pdf->Cell(40,$height,number_format($potProd[$lstThnTnm],2),1,1,'R',1);
            
             }
            
$tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right>".number_format($totLAngr,2)."</td>";
            $tab.="<td align=right>".number_format($totLReali,2)."</td>";
            $tab.="<td align=right>".number_format($totKgStaon,2)."</td>";
            $tab.="<td align=right>".number_format($totKgSthnBi,2)."</td>";
            $tab.="<td align=right>".number_format($totkgSthnsBi,2)."</td>";
            $tab.="<td align=right>".number_format($totbiSensus,2)."</td>";
            $tab.="<td align=right>".number_format($totsbiSensus,2)."</td>";
            $tab.="<td align=right>".number_format($totbiRealisasi,2)."</td>";
            $tab.="<td align=right>".number_format($totsbiRealisasi,2)."</td>";
            $tab.="<td align=right>".number_format($totsnVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totsnVsRealisbi,2)."</td>";
            
            $tab.="<td align=right>".number_format($totangVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totangVsRealibi,2)."</td>";
            $tab.="<td align=right>".number_format($totprodThnLalusbi,2)."</td>";
            $tab.="<td align=right>".number_format($totsenSmstr,2)."</td>";
            $tab.="<td align=right>".number_format($totprodThnLalu,2)."</td>";
            $tab.="<td align=right>".number_format($totpotProd,2)."</td>";
            $tab.="</tr>";
            $pdf->Cell(75,$height,$_SESSION['lang']['total'],1,0,'L',1);
            
            $pdf->Cell(30,$height,number_format($totLAngr,2),1,0,'R',1);
            $pdf->Cell(30,$height,number_format($totLReali,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totKgStaon,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totKgSthnBi,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totkgSthnsBi,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totbiSensus,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totsbiSensus,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totbiRealisasi,2),1,0,'R',1);
            $pdf->Cell(50,$height,number_format($totsbiRealisasi,2),1,0,'R',1);
            $pdf->Cell(30,$height,number_format($totsnVsRealibi,2),1,0,'R',1);
            $pdf->Cell(30,$height,number_format($totsnVsRealisbi,2),1,0,'R',1);
            $pdf->Cell(35,$height,number_format($totangVsRealibi,2),1,0,'R',1);
            $pdf->Cell(35,$height,number_format($totangVsRealibi,2),1,0,'R',1);
            $pdf->Cell(30,$height,number_format($prodThnLalusbi[$lstThnTnm],2),1,0,'R',1);
            $pdf->Cell(55,$height,number_format($totsenSmstr,2),1,0,'R',1);
            $pdf->Cell(55,$height,number_format($totprodThnLalu,2),1,0,'R',1);
            $pdf->Cell(40,$height,number_format($totpotProd,2),1,1,'R',1);
            $pdf->Output();	
                
                
            break;
	
            
	
	default:
	break;
}
      
?>
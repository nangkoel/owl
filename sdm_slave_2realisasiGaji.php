<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$bln=explode("-",$periode);
if($bln[1]=='01')
{
    $thnlalu=intval($bln[0])-1;
    $blnlalu="12";
}
else
{
    $thnlalu=$bln[0];
    $blnlalu=intval($bln[1])-1;
    if($blnlalu<10)
    {
        $blnlalu="0".$blnlalu;
    }
}
$periodelama=$thnlalu."-".$blnlalu;

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
$brd=0;
$bgclr="bgcolor=#DEDEDE ";
if($proses=='excel')
{
    $brd=1;
    $bgclr="bgcolor=#DEDEDE ";
    $tab.="<table cellpadding=1 cellspacing=1 border=0>";
    $tab.="<tr><td colspan=6>Laporan Realisasi Gaji</td></tr>";
    $tab.="<tr><td colspan=2>".$_SESSION['lang']['unit']."</td><td>:</td>";
    $tab.="<td colspan=3>".$optNm[$kdUnit]."</td></tr>";
    $tab.="<tr><td colspan=2>".$_SESSION['lang']['periode']."</td><td>:</td>";
    $tab.="<td colspan=3>".$periode."</td></tr>
           <tr><td colspan=6>&nbsp;</td></tr>
           </table>";
}
if($proses=='excel'||$proses=='preview')
{
    if($periode==''||$kdUnit=='')
    {
    exit("Error: Field Tidak Boleh Kosong");
    }
    //total estate bulan ini
    $sEstate="select distinct sum(jumlah) as jumlah,tipekaryawan,idkomponen,count(a.karyawanid) as org,left(tipe,7) as tipe from ".$dbname.".sdm_gajidetail_vw a
             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id
             where (plus=1 or idkomponen='30' or idkomponen='31' or idkomponen='37' ) and (a.kodeorg='".$kdUnit."' and lokasitugas='".$kdUnit."') and periodegaji='".$periode."' group by tipekaryawan,idkomponen";
    //exit("Error:".$sEstate);
    $qEstate=mysql_query($sEstate) or die(mysql_error($conn));
    $rowData=mysql_num_rows($qEstate);
    if($rowData==0)
    {
        exit("Error:Data Kosong");
    }
    while($rEstate=mysql_fetch_assoc($qEstate))
    {
        $totalOrg+=$rEstate['org'];
        if($rEstate['idkomponen']=='37')
        {
             $totalJumlah-=$rEstate['jumlah'];
        }
        else
        {
            $totalJumlah+=$rEstate['jumlah'];
        }
 
        if($rEstate['idkomponen']<3 or $rEstate['idkomponen']=='30' or $rEstate['idkomponen']=='31' or $rEstate['idkomponen']=='37')
        {
            if($rEstate['idkomponen']=='37')
            {
                $gapok[$rEstate['tipe'].pokok]-=$rEstate['jumlah'];
                
            }
            else
            {
               $gapok[$rEstate['tipe'].pokok]+=$rEstate['jumlah'];
            }
            $orggapo[$rEstate['tipe'].pokok]+=$rEstate['org'];
           

        }
       
        if($rEstate['idkomponen']=='33')
        {
            $lembur[$rEstate['tipe'].lemprem]+=$rEstate['jumlah'];
            $orglemb[$rEstate['tipe'].lemprem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='32')
        {
            $premi[panen]+=$rEstate['jumlah'];
            $orgpremi[panen]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='16')
        {
            $lembur[$rEstate['tipe'].prem]+=$rEstate['jumlah'];
            $orglemb[$rEstate['tipe'].prem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='26')
        {
            $bonus[$rEstate['tipe'].bonthr]=$rEstate['jumlah'];
            $orgbonthr[$rEstate['tipe'].bonthr]=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='28')
        {
            $bonus[bonthr]+=$rEstate['jumlah'];
            $orgbonthr[bonthr]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='14')
        {
            $rapel[rapel]+=$rEstate['jumlah'];
            $orgrapel[rapel]+=$rEstate['org'];
        }
         if($rEstate['idkomponen']=='40')
        {
            $premTtp[$rEstate['tipe'].premttp]+=$rEstate['jumlah'];
            $orgpremTtp[$rEstate['tipe'].premttp]+=$rEstate['org'];
        }
    }
    //total estate bulan lalu
    $sEstate="select distinct sum(jumlah) as jumlah,tipekaryawan,idkomponen,count(a.karyawanid) as org,left(tipe,7) as tipe from ".$dbname.".sdm_gajidetail_vw a
             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id
             where (plus=1 or idkomponen='30' or idkomponen='31' or idkomponen='37' ) and (a.kodeorg='".$kdUnit."' and lokasitugas='".$kdUnit."') and periodegaji='".$periodelama."' group by tipekaryawan,idkomponen";
    //exit("Error:".$sEstate);
    $qEstate=mysql_query($sEstate) or die(mysql_error($conn));
    while($rEstate=mysql_fetch_assoc($qEstate))
    {
        $totalOrgBl+=$rEstate['org'];

        
         if($rEstate['idkomponen']=='37')
        {
            $totalJumlahBl-=$rEstate['jumlah'];
        }
        else
        {
            $totalJumlahBl+=$rEstate['jumlah'];
        }
        if($rEstate['idkomponen']<3 or $rEstate['idkomponen']=='30' or $rEstate['idkomponen']=='31' or $rEstate['idkomponen']=='37')
        {
            if($rEstate['idkomponen']=='37')
            {
                $gapokL[$rEstate['tipe'].pokok]-=$rEstate['jumlah'];
            }
            else
            {
                $gapokL[$rEstate['tipe'].pokok]+=$rEstate['jumlah'];
            }
            $orggapoL[$rEstate['tipe'].pokok]+=$rEstate['org'];
        }
        
        if($rEstate['idkomponen']=='33')
        {
            $lemburL[$rEstate['tipe'].lemprem]+=$rEstate['jumlah'];
            $orglembL[$rEstate['tipe'].lemprem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='32')
        {
            $premiL[panen]+=$rEstate['jumlah'];
            $orgpremiL[panen]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='16')
        {
            $lemburL[$rEstate['tipe'].prem]+=$rEstate['jumlah'];
            $orglembL[$rEstate['tipe'].prem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='26')
        {
            $bonusL[$rEstate['tipe'].bonthr]=$rEstate['jumlah'];
            $orgbonthrL[$rEstate['tipe'].bonthr]=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='28')
        {
            $bonusL[bonthr]+=$rEstate['jumlah'];
            $orgbonthrL[bonthr]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='14')
        {
            $rapelL[rapel]+=$rEstate['jumlah'];
            $orgrapelL[rapel]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='40')
        {
            $premTtpL[$rEstate['tipe'].premttp]+=$rEstate['jumlah'];
            $orgpremTtpL[$rEstate['tipe'].premttp]+=$rEstate['org'];
        }
    }
    //subbagian
    //total subbagian estate bulan ini
    $sEstate="select distinct sum(jumlah) as jumlah,subbagian,idkomponen,count(a.karyawanid) as org,left(tipe,7) as tipe from ".$dbname.".sdm_gajidetail_vw a
             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id
             where (plus=1 or idkomponen='30' or idkomponen='31' or idkomponen='37' ) and (a.kodeorg='".$kdUnit."' and lokasitugas='".$kdUnit."') and periodegaji='".$periode."' group by subbagian,tipekaryawan,idkomponen";
    $qEstate=mysql_query($sEstate) or die(mysql_error($conn));
    while($rEstate=mysql_fetch_assoc($qEstate))
    {
        if($rEstate['subbagian']=='')
        {
            $rEstate['subbagian']="Kantor";
        }
        $totalOrg2[$rEstate['subbagian']]+=$rEstate['org'];

        if($rEstate['idkomponen']=='37')
        {
             $totalJumlah2[$rEstate['subbagian']]-=$rEstate['jumlah'];
        }
        else
        {
            $totalJumlah2[$rEstate['subbagian']]+=$rEstate['jumlah'];
        }
        $lstSub[$rEstate['subbagian']]=$rEstate['subbagian'];
        if($rEstate['idkomponen']<3 or $rEstate['idkomponen']=='30' or $rEstate['idkomponen']=='31' or $rEstate['idkomponen']=='37')
        {
            if($rEstate['idkomponen']=='37')
            {
               $gapok[$rEstate['subbagian']][$rEstate['tipe'].pokok]-=$rEstate['jumlah'];
            }
            else
            {
                $gapok[$rEstate['subbagian']][$rEstate['tipe'].pokok]+=$rEstate['jumlah'];
            }
            
            $orggapo[$rEstate['subbagian']][$rEstate['tipe'].pokok]+=$rEstate['org'];
        }
       
        if($rEstate['idkomponen']=='33')
        {
            $lembur[$rEstate['subbagian']][$rEstate['tipe'].lemprem]+=$rEstate['jumlah'];
            $orglemb[$rEstate['subbagian']][$rEstate['tipe'].lemprem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='32')
        {
            $premi[$rEstate['subbagian']][panen]+=$rEstate['jumlah'];
            $orgpremi[$rEstate['subbagian']][panen]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='16')
        {
            $lembur[$rEstate['subbagian']][$rEstate['tipe'].prem]+=$rEstate['jumlah'];
            $orglemb[$rEstate['subbagian']][$rEstate['tipe'].prem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='26')
        {
            $bonus[$rEstate['subbagian']][$rEstate['tipe'].bonthr]=$rEstate['jumlah'];
            $orgbonthr[$rEstate['subbagian']][$rEstate['tipe'].bonthr]=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='28')
        {
            $bonus[$rEstate['subbagian']][bonthr]+=$rEstate['jumlah'];
            $orgbonthr[$rEstate['subbagian']][bonthr]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='14')
        {
            $rapel[$rEstate['subbagian']][rapel]+=$rEstate['jumlah'];
            $orgrapel[$rEstate['subbagian']][rapel]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='40')
        {
            $premTtp[$rEstate['subbagian']][$rEstate['tipe'].premttp]+=$rEstate['jumlah'];
            $orgpremTtp[$rEstate['subbagian']][$rEstate['tipe'].premttp]+=$rEstate['org'];
        }
    }
    //total subbagian  estate bulan lalu
    $sEstate="select distinct sum(jumlah) as jumlah,subbagian,idkomponen,count(a.karyawanid) as org,left(tipe,7) as tipe from ".$dbname.".sdm_gajidetail_vw a
             left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid left join ".$dbname.".sdm_5tipekaryawan c on b.tipekaryawan=c.id
             where (plus=1 or idkomponen='30' or idkomponen='31' or idkomponen='37' ) and (a.kodeorg='".$kdUnit."' and lokasitugas='".$kdUnit."') and periodegaji='".$periodelama."' group by subbagian,tipekaryawan,idkomponen";
    //echo $sEstate;
    $qEstate=mysql_query($sEstate) or die(mysql_error($conn));
    while($rEstate=mysql_fetch_assoc($qEstate))
    {
      
         if($rEstate['subbagian']=='')
        {
            $rEstate['subbagian']="Kantor";
        }
        $totalOrgBl2[$rEstate['subbagian']]+=$rEstate['org'];
        if($rEstate['idkomponen']=='37')
        {
              $totalJumlahBl2[$rEstate['subbagian']]-=$rEstate['jumlah'];
        }
        else
        {
            $totalJumlahBl2[$rEstate['subbagian']]+=$rEstate['jumlah'];
        }
       
         $lstSub[$rEstate['subbagian']]=$rEstate['subbagian'];
         
        if($rEstate['idkomponen']<3 or $rEstate['idkomponen']=='30' or $rEstate['idkomponen']=='31' or $rEstate['idkomponen']=='37')
        {
            if($rEstate['idkomponen']=='37')
            {
              $gapokL[$rEstate['subbagian']][$rEstate['tipe'].pokok]-=$rEstate['jumlah'];
            }
            else
            {
               $gapokL[$rEstate['subbagian']][$rEstate['tipe'].pokok]+=$rEstate['jumlah'];
            }
            
            $orggapoL[$rEstate['subbagian']][$rEstate['tipe'].pokok]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='33')
        {
            $lemburL[$rEstate['subbagian']][$rEstate['tipe'].lemprem]+=$rEstate['jumlah'];
            $orglembL[$rEstate['subbagian']][$rEstate['tipe'].lemprem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='32')
        {
            $premiL[$rEstate['subbagian']][panen]+=$rEstate['jumlah'];
            $orgpremiL[$rEstate['subbagian']][panen]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='16')
        {
            $lemburL[$rEstate['subbagian']][$rEstate['tipe'].prem]+=$rEstate['jumlah'];
            $orglembL[$rEstate['subbagian']][$rEstate['tipe'].prem]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='26')
        {
            $bonusL[$rEstate['subbagian']][$rEstate['tipe'].bonthr]=$rEstate['jumlah'];
            $orgbonthrL[$rEstate['subbagian']][$rEstate['tipe'].bonthr]=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='28')
        {
            $bonusL[$rEstate['subbagian']][bonthr]+=$rEstate['jumlah'];
            $orgbonthrL[$rEstate['subbagian']][bonthr]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='14')
        {
            $rapelL[$rEstate['subbagian']][rapel]+=$rEstate['jumlah'];
            $orgrapelL[$rEstate['subbagian']][rapel]+=$rEstate['org'];
        }
        if($rEstate['idkomponen']=='40')
        {
            $premTtpL[$rEstate['subbagian']][$rEstate['tipe'].premttp]+=$rEstate['jumlah'];
            $orgpremTtpL[$rEstate['subbagian']][$rEstate['tipe'].premttp]+=$rEstate['org'];
        }
       
    }
    
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable><thead>";
    $tab.="<tr><td rowspan=2 ".$bgclr." align=center>No.</td>";
    $tab.="<td rowspan=2 ".$bgclr." align=center>".$_SESSION['lang']['jenisbiaya']."</td>";
    $tab.="<td colspan=2 ".$bgclr." align=center>".$_SESSION['lang']['bulanini']."</td>";
    $tab.="<td colspan=2 ".$bgclr." align=center>".$_SESSION['lang']['bulanlalu']."</td></tr>";
    $tab.="<tr><td ".$bgclr." align=center>".$_SESSION['lang']['orang']."</td><td ".$bgclr." align=center>".$_SESSION['lang']['rp']."</td>";
    $tab.="<td ".$bgclr." align=center>".$_SESSION['lang']['orang']."</td><td ".$bgclr." align=center>".$_SESSION['lang']['rp']."</td></tr></thead><tbody>";
    $tab.="<tr><td colspan=6>Estate</td></tr>";
    //=$orggapo[KBL.pokok]+$orglemb[KBL.lemprem]+$orggapo[KHT.pokok]+$orglemb[KHT.lemprem]+$orggapo[KHL.pokok]+$orglemb[KHL.lemprem]+$orggapo[Kontrak.pokok]
    $tab.="<tr class=rowcontent><td>1.</td><td>Kary. Bulanan (KBL)</td>
          <td align=right>".$orggapo[KBL.pokok]."</td>
          <td align=right>".number_format($gapok[KBL.pokok],0)."</td>
          <td align=right>".$orggapoL[KBL.pokok]."</td>
          <td align=right>".number_format($gapokL[KBL.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>2.</td><td>Lembur KBL</td>
          <td align=right>".$orglemb[KBL.lemprem]."</td>
          <td align=right>".number_format($lembur[KBL.lemprem],0)."</td>
          <td align=right>".$orglembL[KBL.lemprem]."</td>
          <td align=right>".number_format($lemburL[KBL.lemprem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>3.</td><td>Premi Pengawas KBL</td>
          <td align=right>".$orglemb[KBL.prem]."</td>
          <td align=right>".number_format($lembur[KBL.prem],0)."</td>
          <td align=right>".$orglembL[KBL.prem]."</td>
          <td align=right>".number_format($lemburL[KBL.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>4.</td><td>Kary. Harian Tetap (KHT)</td>
          <td align=right>".$orggapo[KHT.pokok]."</td>
          <td align=right>".number_format($gapok[KHT.pokok],0)."</td>
          <td align=right>".$orggapoL[KHT.pokok]."</td>
          <td align=right>".number_format($gapokL[KHT.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>5.</td><td>Lembur KHT</td>
          <td align=right>".$orglemb[KHT.lemprem]."</td>
          <td align=right>".number_format($lembur[KHT.lemprem],0)."</td>
          <td align=right>".$orglembL[KHT.lemprem]."</td>
          <td align=right>".number_format($lemburL[KHT.lemprem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>6.</td><td>Premi Pengawas KHT</td>
          <td align=right>".$orglemb[KHT.prem]."</td>
          <td align=right>".number_format($lembur[KHT.prem],0)."</td>
          <td align=right>".$orglembL[KHT.prem]."</td>
          <td align=right>".number_format($lemburL[KHT.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>7.</td><td>Buruh Harian Lepas (BHL)</td>
          <td align=right>".$orggapo[KHL.pokok]."</td>
          <td align=right>".number_format($gapok[KHL.pokok],0)."</td>
          <td align=right>".$orggapoL[KHL.pokok]."</td>
          <td align=right>".number_format($gapokL[KHL.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>8.</td><td>Lembur KHL</td>
          <td align=right>".$orglemb[KHL.lemprem]."</td>
          <td align=right>".number_format($lembur[KHL.lemprem],0)."</td>
          <td align=right>".$orglembL[KHL.lemprem]."</td>
          <td align=right>".number_format($lemburL[KHL.lemprem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>9.</td><td>Premi Pengawasan KHL</td>
          <td align=right>".$orglemb[KHL.prem]."</td>
          <td align=right>".number_format($lembur[KHL.prem],0)."</td>
          <td align=right>".$orglembL[KHL.prem]."</td>
          <td align=right>".number_format($lemburL[KHL.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>10.</td><td>Kary. Kontrak</td>
          <td align=right>".$orggapo[Kontrak.pokok]."</td>
          <td align=right>".number_format($gapok[Kontrak.pokok],0)."</td>
          <td align=right>".$orggapoL[Kontrak.pokok]."</td>
          <td align=right>".number_format($gapokL[Kontrak.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>11.</td><td>Lembur Kary. Kontrak</td>
          <td align=right>".$orglemb[Kontrak.lemprem]."</td>
          <td align=right>".number_format($lembur[Kontrak.lemprem],0)."</td>
          <td align=right>".$orglembL[Kontrak.lemprem]."</td>
          <td align=right>".number_format($lemburL[Kontrak.lemprem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>12.</td><td>Premi Pengawas Kary. Kontrak</td>
          <td align=right>".$orglemb[Kontrak.prem]."</td>
          <td align=right>".number_format($lembur[Kontrak.prem],0)."</td>
          <td align=right>".$orglembL[Kontrak.prem]."</td>
          <td align=right>".number_format($lemburL[Kontrak.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>13.</td><td>Premi Panen</td>
          <td align=right>".$orgpremi[panen]."</td>
          <td align=right>".number_format($premi[panen],0)."</td>
          <td align=right>".$orgpremiL[panen]."</td>
          <td align=right>".number_format($premiL[panen],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>14.</td><td>THR/Bonus Karyawan</td>
          <td align=right>".$orgbonthr[bonthr]."</td>
          <td align=right>".number_format($bonus[bonthr],0)."</td>
          <td align=right>".$orgbonthrL[bonthr]."</td>
          <td align=right>".number_format($bonusL[bonthr],0)."</td>
          </tr>";
 
      $tab.="<tr class=rowcontent><td>15.</td><td>Rapel</td>
          <td align=right>".$orgrapel[rapel]."</td>
          <td align=right>".number_format($rapel[rapel],0)."</td>
          <td align=right>".$orgrapelL[bonthr]."</td>
          <td align=right>".number_format($rapelL[rapel],0)."</td>
          </tr>";
      $tab.="<tr class=rowcontent><td>16.</td><td>Premi Tetap</td>
          <td align=right>".$orgpremTtp[premttp]."</td>
          <td align=right>".number_format($premTtp[premttp],0)."</td>
          <td align=right>".$orgpremTtpL[premttp]."</td>
          <td align=right>".number_format($premTtpL[premttp],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>
          <td align=right>".$totalOrg."</td>
          <td align=right>".number_format($totalJumlah,0)."</td>
          <td align=right>".$totalOrgBl."</td>
          <td align=right>".number_format($totalJumlahBl,0)."</td>
          </tr>";
    
    $nor=16;
    foreach($lstSub as $dtSub)
    {
    $nor+=1;
    $tab.="<tr><td colspan=6>".$dtSub."</td></tr>";
    //=$orggapo[KBL.pokok]+$orglemb[KBL.lemprem]+$orggapo[KHT.pokok]+$orglemb[KHT.lemprem]+$orggapo[KHL.pokok]+$orglemb[KHL.lemprem]+$orggapo[Kontrak.pokok]
   $tab.="<tr class=rowcontent><td>".$nor.".</td><td>Kary. Bulanan (KBL)</td>
          <td align=right>".$orggapo[$dtSub][KBL.pokok]."</td>
          <td align=right>".number_format($gapok[$dtSub][KBL.pokok],0)."</td>
          <td align=right>".$orggapoL[$dtSub][KBL.pokok]."</td>
          <td align=right>".number_format($gapokL[$dtSub][KBL.pokok],0)."</td>
          </tr>";
   $tab.="<tr class=rowcontent><td>".($nor+1).".</td><td>Lembur KBL</td>
          <td align=right>".$orglemb[$dtSub][KBL.lemprem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KBL.lemprem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KBL.lemprem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KBL.lemprem],0)."</td>
          </tr>";
   $tab.="<tr class=rowcontent><td>".($nor+2).".</td><td>Premi Pengawas KBL</td>
          <td align=right>".$orglemb[$dtSub][KBL.prem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KBL.prem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KBL.prem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KBL.prem],0)."</td>
          </tr>";
    
     $tab.="<tr class=rowcontent><td>".($nor+3).".</td><td>Kary. Harian Tetap (KHT)</td>
          <td align=right>".$orggapo[$dtSub][KHT.pokok]."</td>
          <td align=right>".number_format($gapok[$dtSub][KHT.pokok],0)."</td>
          <td align=right>".$orggapoL[$dtSub][KHT.pokok]."</td>
          <td align=right>".number_format($gapokL[$dtSub][KHT.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+4).".</td><td>Lembur KHT</td>
          <td align=right>".$orglemb[$dtSub][KHT.lemprem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KHT.lemprem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KHT.lemprem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KHT.lemprem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+5).".</td><td>Premi Pengawas KHT</td>
          <td align=right>".$orglemb[$dtSub][KHT.prem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KHT.prem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KHT.prem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KHT.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+6).".</td><td>Buruh Harian Lepas (BHL)</td>
          <td align=right>".$orggapo[$dtSub][KHL.pokok]."</td>
          <td align=right>".number_format($gapok[$dtSub][KHL.pokok],0)."</td>
          <td align=right>".$orggapoL[$dtSub][KHL.pokok]."</td>
          <td align=right>".number_format($gapokL[$dtSub][KHL.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+7).".</td><td>Lembur BHL</td>
          <td align=right>".$orglemb[$dtSub][KHL.lemprem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KHL.lemprem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KHL.lemprem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KHL.lemprem],0)."</td>
          </tr>";
     $tab.="<tr class=rowcontent><td>".($nor+8).".</td><td>Premi Pengawas BHL</td>
          <td align=right>".$orglemb[$dtSub][KHL.prem]."</td>
          <td align=right>".number_format($lembur[$dtSub][KHL.prem],0)."</td>
          <td align=right>".$orglembL[$dtSub][KHL.prem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][KHL.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+9).".</td><td>Kary. Kontrak</td>
          <td align=right>".$orggapo[$dtSub][Kontrak.pokok]."</td>
          <td align=right>".number_format($gapok[$dtSub][Kontrak.pokok],0)."</td>
          <td align=right>".$orggapoL[$dtSub][Kontrak.pokok]."</td>
          <td align=right>".number_format($gapokL[$dtSub][Kontrak.pokok],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+10).".</td><td>Lembur Kary. Kontrak</td>
          <td align=right>".$orglemb[$dtSub][Kontrak.lemprem]."</td>
          <td align=right>".number_format($lembur[$dtSub][Kontrak.lemprem],0)."</td>
          <td align=right>".$orglembL[$dtSub][Kontrak.lemprem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][Kontrak.lemprem],0)."</td>
          </tr>";
     $tab.="<tr class=rowcontent><td>".($nor+11).".</td><td>Premi Pengawas Kary. Kontrak</td>
          <td align=right>".$orglemb[$dtSub][Kontrak.prem]."</td>
          <td align=right>".number_format($lembur[$dtSub][Kontrak.prem],0)."</td>
          <td align=right>".$orglembL[$dtSub][Kontrak.prem]."</td>
          <td align=right>".number_format($lemburL[$dtSub][Kontrak.prem],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".($nor+12).".</td><td>Premi Panen</td>
          <td align=right>".$orgpremi[$dtSub][panen]."</td>
          <td align=right>".number_format($premi[$dtSub][panen],0)."</td>
          <td align=right>".$orgpremiL[$dtSub][panen]."</td>
          <td align=right>".number_format($premiL[$dtSub][panen],0)."</td>
          </tr>";
    $tab.="<tr class=rowcontent><td>".$nor=($nor+13).".</td><td>THR/Bonus Karyawan</td>
          <td align=right>".$orgbonthr[$dtSub][bonthr]."</td>
          <td align=right>".number_format($bonus[$dtSub][bonthr],0)."</td>
          <td align=right>".$orgbonthrL[$dtSub][bonthr]."</td>
          <td align=right>".number_format($bonusL[$dtSub][bonthr],0)."</td>
          </tr>";
     $tab.="<tr class=rowcontent><td>".$nor=($nor+14).".</td><td>Rapel</td>
          <td align=right>".$orgrapel[$dtSub][rapel]."</td>
          <td align=right>".number_format($rapel[$dtSub][rapel],0)."</td>
          <td align=right>".$orgrapelL[$dtSub][rapel]."</td>
          <td align=right>".number_format($rapelL[$dtSub][rapel],0)."</td>
          </tr>";
     $tab.="<tr class=rowcontent><td>".$nor=($nor+15).".</td><td>Premi Tetap</td>
       <td align=right>".$orgpremTtp[$dtSub][premttp]."</td>
          <td align=right>".number_format($premTtp[$dtSub][premttp],0)."</td>
          <td align=right>".$orgpremTtpL[$dtSub][premttp]."</td>
          <td align=right>".number_format($premTtpL[$dtSub][premttp],0)."</td>
          </tr>";
 
    $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['subtotal']."</td>
          <td align=right>".$totalOrg2[$dtSub]."</td>
          <td align=right>".number_format($totalJumlah2[$dtSub],0)."</td>
          <td align=right>".$totalOrgBl2[$dtSub]."</td>
          <td align=right>".number_format($totalJumlahBl2[$dtSub],0)."</td>
          </tr>";
    }
$tab.="</tbody></table>";
}
switch($proses)
{
    case'getPeriode':
    $opt="<option values=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sPeriode="select distinct periodegaji from ".$dbname.".sdm_gaji where kodeorg ='".$kdUnit."' order by periodegaji  desc";
    //exit("Error:".$sPeriode);
    $qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
    while($rPeriode=  mysql_fetch_assoc($qPeriode))
    {
        $opt.="<option values='".$rPeriode['periodegaji']."'>".$rPeriode['periodegaji']."</option>";
    }
    echo $opt;
    break;
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
        $dte=date("Hms");
        $nop_="realisasiGaji__".$dte;
        $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
	break;
        case'pdf':
	$perod=$_GET['perod'];
	$idAfd=$_GET['idAfd'];
	$idKry=$_GET['idKry'];
	$kdBag2=$_GET['kdBag2'];

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
{
var $col=0;
var $dbname;

function SetCol($col)
	{
	    //Move position to a column
	    $this->col=$col;
	    $x=10+$col*100;
	    $this->SetLeftMargin($x);
	    $this->SetX($x);
	}

function AcceptPageBreak()
	{ 
			if($this->col<1)
		    {
		        //Go to next column
		        $this->SetCol($this->col+1);
		        $this->SetY(10);
		        return false;
		    }
		    else
		    {
		        //Go back to first column and issue page break
				$this->SetCol(0);
		        return true;
		    }
	}

	function Header()
	{    
		$this->lMargin=5;  
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',5);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',5);
        //periode gaji
        $bln=explode('-',$perod);
        $idBln=intval($bln[1]);	
        
        //prepare array data gaji karyawan,nama,jabatan,tmk dan bagian
        $sSlip="select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from 
               ".$dbname.".sdm_gaji_vw a  left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan 
               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode
               where b.sistemgaji='Bulanan' and a.periodegaji='".$perod."' and ".$add."  ".$dtTipe." order by b.namakaryawan asc";
	$qSlip=mysql_query($sSlip) or die(mysql_error());
	$rCek=mysql_num_rows($qSlip);
	if($rCek>0)
	{
		while($rSlip=mysql_fetch_assoc($qSlip))
		{
                    if($rSlip['karyawanid']!='')
                    {
                    $arrKary[$rSlip['karyawanid']]=$rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']]=$rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']]=$rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']]=$rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']]=$rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']]=$rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']]=$rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']]=$rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']]=$rSlip['jumlah'];
                    }
                }
          
          //array data komponen penambah dan pengurang
          $sKomp="select id,name,plus from ".$dbname.".sdm_ho_component where plus=1 ";
          $qKomp=mysql_query($sKomp) or die(mysql_error());
          while($rKomp=mysql_fetch_assoc($qKomp))
          {
              $arrIdKompPls[]=$rKomp['id'];
              $arrNmKomPls[$rKomp['id']][1]=$rKomp['name'];
          }
          $sKomp2="select id,name,plus from ".$dbname.".sdm_ho_component where plus=0 ";
          $qKomp2=mysql_query($sKomp2) or die(mysql_error());
          while($rKomp2=mysql_fetch_assoc($qKomp2))
          {
              $arrIdKompPls[]=$rKomp2['id'];
              $arrNmKomPls[$rKomp2['id']][0]=$rKomp2['name'];
          }
          //komponen
            $arrMinusId=Array();
            $arrMinusName=Array();
            $str="select id,name from ".$dbname.".sdm_ho_component where plus='0' order by id";
            // echo $str;exit();
            $res=mysql_query($str,$conn);
            while($bar=mysql_fetch_object($res))
            {
                array_push($arrMinusId,$bar->id);
                array_push($arrMinusName,$bar->name);
            }
            //samakan
            $arrPlusId=$arrMinusId;
            $arrPlusName=$arrMinusName;
            //Kosongkan
            for($r=0;$r<count($arrMinusId);$r++)
            {
                 $arrPlusId[$r]='';
                 $arrPlusName[$r]='';
            }
            $str="select  id,name from ".$dbname.".sdm_ho_component where plus='1' and id not in ('26','28') order by id";
            $res=mysql_query($str,$conn);
            $n=-1;
            while($bar=mysql_fetch_object($res))
            {
                $n+=1;
                $arrPlusId[$n]=$bar->id;
                $arrPlusName[$n]=$bar->name;
            }
           $arrValPlus=Array();
           $arrValMinus=Array();
           for($x=0;$x<count($arrPlusId);$x++)
           {
                $arrValPlus[$x]=0;
                $arrValMinus[$x]=0;
           }
           $str3="select jumlah,idkomponen,a.karyawanid,c.plus from ".$dbname.".sdm_gaji_vw a 
                  left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id
                 where a.sistemgaji='Bulanan' and a.periodegaji='".$perod."' ";
           //exit("Error:".$str3);
           $res3=mysql_query($str3,$conn);
           while($bar3=mysql_fetch_assoc($res3))
           {
               if($bar3['plus']=='1')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValPlus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                    }
               }
               elseif($bar3['plus']=='0')
               {
                    if($bar3['jumlah']!='')
                    {
                        $arrValMinus[$bar3['karyawanid']][$bar3['idkomponen']]=$bar3['jumlah'];
                    }
               } 
            }	 
    
	foreach($arrKary as $dtKary)
        {
			$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
			$pdf->SetX($pdf->getX()+10);
			$pdf->SetFont('Arial','B',8);	
			$pdf->Cell(75,6,$_SESSION['org']['namaorganisasi'],0,1,'L');
			$pdf->SetFont('Arial','',7);	
			$pdf->Cell(71,4,$_SESSION['lang']['slipGaji'].': '.$arrBln[$idBln]."-".$bln[0],'T',0,'L');
			$pdf->SetFont('Arial','',6);
				$pdf->Cell(25,4,'Printed on: '.date('d-m-Y: H:i:s'),"T",1,'R');
			$pdf->SetFont('Arial','',6);		
			$pdf->Cell(15,4,$_SESSION['lang']['nik']."/".$_SESSION['lang']['tmk'],0,0,'L');
				$pdf->Cell(35,4,": ".$arrNik[$dtKary]."/".tanggalnormal($arrTglMsk[$dtKary]),0,0,'L');
			$pdf->Cell(18,4,$_SESSION['lang']['unit']."/".$_SESSION['lang']['bagian'],0,0,'L');	
				$pdf->Cell(28,4,': '.$idAfd." / ".$arrBag[$dtKary],0,1,'L');		
			$pdf->Cell(15,4,$_SESSION['lang']['namakaryawan'].":",0,0,'L');
				$pdf->Cell(35,4,': '.$arrNmKary[$dtKary],0,0,'L');	
			$pdf->Cell(18,3,$_SESSION['lang']['jabatan'],0,0,'L');
				$pdf->Cell(28,4,':'.$arrJbtn[$dtKary],0,1,'L');	
			$pdf->Cell(48,4,$_SESSION['lang']['penambah'],'TB',0,'C');
			$pdf->Cell(48,4,$_SESSION['lang']['pengurang'],'TB',1,'C');
                     
           
                     for($mn=0;$mn<count($arrPlusId);$mn++)
			{
				$pdf->Cell(25,4,$arrPlusName[$mn],0,0,'L');
				if($arrPlusName[$mn]=='')
				{
				  $pdf->Cell(5,4,"",0,0,'L');
				  $pdf->Cell(18,4,'','R',0,'R');
				}
				else
				{
                                    if($arrPlusId[$mn]=='')
                                    {
                                        $pdf->Cell(5,4,"",0,0,'L');
                                        $pdf->Cell(18,4,'','R',0,'R');
                                    }
                                    else
                                    {
                                        $pdf->Cell(5,4,":Rp.",0,0,'L');
                                        $pdf->Cell(18,4,number_format($arrValPlus[$dtKary][$arrPlusId[$mn]],2,'.',','),'R',0,'R');
                                        $arrPlus[$dtKary]+=$arrValPlus[$dtKary][$arrPlusId[$mn]];
                                    }
				}
				$pdf->Cell(25,4,$arrMinusName[$mn],0,0,'L');
				if($arrMinusName[$mn]=='')
				{
				  $pdf->Cell(5,4,"",0,0,'L');
				  $pdf->Cell(18,4,'',0,1,'R');
				}
				else
				{
                                    if($arrMinusId[$mn]=='')
                                    {
                                      $pdf->Cell(5,4,"",0,0,'L');
                                       $pdf->Cell(18,4,'',0,1,'R');
                                    }
                                    else
                                    {
                                      $pdf->Cell(5,4,":Rp.",0,0,'L');
                                      $pdf->Cell(18,4,number_format(($arrValMinus[$dtKary][$arrMinusId[$mn]]*-1),2,'.',','),0,1,'R');
                                      $arrMin[$dtKary]+=$arrValMinus[$dtKary][$arrMinusId[$mn]]*-1;
                                    }
				}
			}

				$pdf->Cell(25,4,$_SESSION['lang']['totalPendapatan'],'TB',0,'L');
				$pdf->Cell(5,4,":Rp.",'TB',0,'L');
					$pdf->Cell(18,4,number_format($arrPlus[$dtKary],2,'.',','),'TB',0,'R');
				$pdf->Cell(25,4,$_SESSION['lang']['totalPotongan'],'TB',0,'L');
				$pdf->Cell(5,4,":Rp.",'TB',0,'L');
					$pdf->Cell(18,4,number_format(($arrMin[$dtKary]*-1),2,'.',','),'TB',1,'R');
		
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(23,4,$_SESSION['lang']['gajiBersih'],0,0,'L');
			$pdf->Cell(5,4,":Rp.",0,0,'L');
				$pdf->Cell(18,4,number_format(($arrPlus[$dtKary]-($arrMin[$dtKary]*-1)),2,'.',','),0,0,'R');
				$pdf->Cell(47,4,"",0,1,'L');
				$terbilang=($arrPlus[$dtKary]-($arrMin[$dtKary]*-1));
				$blng=terbilang($terbilang,2)." rupiah";
			$pdf->SetFont('Arial','',7);	
			$pdf->Cell(23,4,'Terbilang',0,0,'L');
			$pdf->Cell(5,4,":",0,0,'L');
				$pdf->MultiCell(58,4,$blng,0,'L');
			$pdf->SetFont('Arial','I',5);
			$pdf->Cell(96,4,'Note: This is computer generated system, signature is not required','T',1,'L');	
			$pdf->SetFont('Arial','',6);	
			$pdf->Ln(10);	
			if($pdf->GetY()>225 and $pdf->col<1)
				$pdf->AcceptPageBreak();
			if ($pdf->GetY()>225 and $pdf->col>0)
			   {
				//$pdf->lewat=true;
				// $pdf->AcceptPageBreak();
				//$pdf->SetY(277-$pdf->GetY());
				$r=275-$pdf->GetY();
				$pdf->Cell(80,$r,'',0,1,'L');
				
				//$pdf->ln();
			   }
			//else   
			//$pdf->lewat=false; 	
					   
			$pdf->cell(-1,3,'',0,0,'L');	
		}
}
else
{
	$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
	$pdf->SetX($pdf->getX()+8);
	$pdf->SetFont('Arial','B',8);	
	$pdf->Cell(70,5,$_SESSION['org']['namaorganisasi'],0,1,'L');
	$pdf->SetFont('Arial','',5);	
	$pdf->Cell(60,3,'NOT FOUND','T',0,'L');
}
	$pdf->Output();

	break;
	
	
   
	default:
	break;
}
?>
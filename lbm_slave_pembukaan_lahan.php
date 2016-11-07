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
$qwe=explode('-',$periode); $tahun=$qwe[0]; $bulan=$qwe[1];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($unit==''||$periode=='')
{
    exit("Error:Fields required");
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

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

// cari kegiatan
$kegiatan="SELECT kodekegiatan, namakegiatan,namakegiatan1, satuan FROM ".$dbname.".setup_kegiatan WHERE `kelompok` = 'TB' order by kodekegiatan";
$query=mysql_query($kegiatan) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodekegiatan']][kode]=$res['kodekegiatan'];
    $listkegiatan[$res['kodekegiatan']]=$res['kodekegiatan'];
    if($_SESSION['language']=='EN'){
        $kamuskegiatan[$res['kodekegiatan']]=$res['namakegiatan1'];
    }else{
        $kamuskegiatan[$res['kodekegiatan']]=$res['namakegiatan'];
    }
    $kamussatuan[$res['kodekegiatan']]=$res['satuan'];
}   

// ambil data fisik anggaran setahun
$str="SELECT * FROM ".$dbname.".bgt_lbm_volume_kebun_vw 
    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][fisangset]=$res['volume'];
}

$str="SELECT * FROM ".$dbname.".bgt_lbm_porsi_kebun_vw 
    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][fisangbin]=$dzArr[$res['kegiatan']][fisangset]*$res['rp'.$bulan];
}

// bikin penjumlahan sd bulan ini
$bulanz=$bulan+0;
$porsi='(';
for ($i=1; $i<=$bulanz; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $porsi.='rp'.$ii.'+';   
}
$porsi=substr($porsi,0,-1);
$porsi.=') as porsi';

// ambil data fisik anggaran sampai dengan bulan ini
$str="SELECT kegiatan, ".$porsi." FROM ".$dbname.".bgt_lbm_porsi_kebun_vw 
    WHERE tahunbudget = '".$tahun."' and kebun = '".$unit."'";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][fisangsdb]=$dzArr[$res['kegiatan']][fisangset]*$res['porsi'];
}

// ambil data fisik realisasi bulan ini
$str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE tanggal like '".$periode."%' and unit = '".$unit."'
    GROUP BY kodekegiatan";
if($afdId!='')
{
$str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE tanggal like '".$periode."%' and kodeorg like '".$afdId."%'
    GROUP BY kodekegiatan";    
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodekegiatan']][fisreabin]=$res['volume'];
}

// ambil data fisik realisasi sampai dengan bulan ini
$str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and unit = '".$unit."'
    GROUP BY kodekegiatan";
if($afdId!='')
{
   $str="SELECT kodekegiatan, sum(hasilkerja) as volume FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%'
    GROUP BY kodekegiatan"; 
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodekegiatan']][fisreasdb]=$res['volume'];
}

// ambil data hkhm anggaran setahun
$str="SELECT kegiatan, (hm01+hm02+hm03+hm04+hm05+hm06+hm07+hm08+hm09+hm10+hm11+hm12) as hkhm FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][hkmangset]=$res['hkhm'];
}

// ambil data hkhm anggaran bulan ini
$str="SELECT kegiatan, hm".$bulan." as hkhm FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][hkmangbin]=$res['hkhm'];
}

// bikin penjumlahan sd bulan ini
$bulanz=$bulan+0;
$porsi='(';
for ($i=1; $i<=$bulanz; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $porsi.='hm'.$ii.'+';   
}
$porsi=substr($porsi,0,-1);
$porsi.=') as hkhm';

// ambil data hkhm anggaran sampai dengan bulan ini
$str="SELECT kegiatan, ".$porsi." FROM ".$dbname.".bgt_hkhm_per_kegiatan_kebun_vw 
    WHERE tahunbudget='".$tahun."' and kebun = '".$unit."'
    ";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][hkmangsdb]=$res['hkhm'];
}

// ambil data hkhm realisasi bulan ini
$str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE tanggal like '".$periode."%' and unit = '".$unit."'
    GROUP BY kodekegiatan";
if($afdId!='')
{
    $str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE tanggal like '".$periode."%' and kodeorg like '".$afdId."%'
    GROUP BY kodekegiatan";
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodekegiatan']][hkmreabin]=$res['jhk'];
}
//$str="SELECT kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt_vw 
//    WHERE tanggal like '".$periode."%' and alokasibiaya like '".$unit."%'
//    GROUP BY kegiatan";
$str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
    LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
    LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
    LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
    WHERE tanggal like '".$periode."%' and alokasibiaya like '".$unit."%'
    GROUP BY d.kodekegiatan";
if($afdId!='')
{
   $str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
    LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
    LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
    LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
    WHERE tanggal like '".$periode."%' and alokasibiaya like '".$afdId."%'
    GROUP BY d.kodekegiatan"; 
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][hkmreabin]+=$res['jumlah'];
}

// ambil data hkhm realisasi sampai dengan bulan ini
$str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and unit = '".$unit."'
    GROUP BY kodekegiatan";
if($afdId!='')
{
    $str="SELECT kodekegiatan, sum(jumlahhk) as jhk FROM ".$dbname.".kebun_perawatan_dan_spk_vw
    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and kodeorg like '".$afdId."%'
    GROUP BY kodekegiatan";
    
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kodekegiatan']][hkmreasdb]=$res['jhk'];
}
//$str="SELECT kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt_vw 
//    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$unit."%'
//    GROUP BY kegiatan";
$str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
      LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
      LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
      LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
     WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$unit."%'
     GROUP BY d.kodekegiatan";
if($afdId!='')
{
  $str="SELECT d.kodekegiatan as kegiatan, sum(jumlah) as jumlah FROM ".$dbname.".vhc_rundt a
      LEFT JOIN ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
      LEFT JOIN ".$dbname.".vhc_kegiatan c on a.jenispekerjaan=c.kodekegiatan
      LEFT JOIN ".$dbname.".setup_kegiatan d on c.noakun=d.noakun
     WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and alokasibiaya like '".$afdId."%'
     GROUP BY d.kodekegiatan";  
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['kegiatan']][hkmreasdb]+=$res['jumlah'];
}

//echo "<pre>";
//print_r($dzArr);
//echo "</pre>";

if(!empty($listkegiatan))foreach($listkegiatan as $keg){
    @$dzArr[$keg][fispenset]=$dzArr[$keg][fisreasdb]/$dzArr[$keg][fisangset]*100;
    @$dzArr[$keg][fispensdb]=$dzArr[$keg][fisreasdb]/$dzArr[$keg][fisangsdb]*100;
    @$dzArr[$keg][hkmpenset]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][hkmangset]*100;
    @$dzArr[$keg][hkmpensdb]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][hkmangsdb]*100;
    @$dzArr[$keg][humangset]=$dzArr[$keg][hkmangset]/$dzArr[$keg][fisangset];
    @$dzArr[$keg][humangbin]=$dzArr[$keg][hkmangbin]/$dzArr[$keg][fisangbin];
    @$dzArr[$keg][humangsdb]=$dzArr[$keg][hkmangsdb]/$dzArr[$keg][fisangsdb];
    @$dzArr[$keg][humreabin]=$dzArr[$keg][hkmreabin]/$dzArr[$keg][fisreabin];
    @$dzArr[$keg][humreasdb]=$dzArr[$keg][hkmreasdb]/$dzArr[$keg][fisreasdb];
}

function numberformat($qwe,$asd)
{
    if($qwe==0)$zxc='0'; 
    else{
        $zxc=number_format($qwe,$asd);
    }
    return $zxc;
}        

if($proses=='excel')
{
$bg=" bgcolor=#DEDEDE";
$brdr=1;
$tab.="<table border=0>
     <tr>
        <td colspan=15 align=left><font size=3>14. ".strtoupper($_SESSION['lang']['bukalahan'])."</font></td>
        <td colspan=5 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=20 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>";
if($afdId!='')
{
    $tab.="<tr><td colspan=20 align=left>".$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")</td></tr>";
}
$tab.="</table>";
}
else
{ 
    $bg="";
    $brdr=0;
}
if($proses!='excel')$tab.=$judul;
    $tab.="<table cellpadding=1 cellspacing=1 border=".$brdr." class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['pekerjaan']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['satuan']."</td>
    <td align=center colspan=7 ".$bg.">".$_SESSION['lang']['fisik']." (HA-UNIT)</td>
    <td align=center colspan=7 ".$bg.">HK-HM</td>
    <td align=center colspan=5 ".$bg.">HK-HM/HA-UNIT</td>
    </tr>
    <tr>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center colspan=2 ".$bg.">% ".$_SESSION['lang']['pencapaian']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center colspan=2 ".$bg.">% ".$_SESSION['lang']['pencapaian']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    </thead>
    <tbody>
";
    
    $dummy='';
// excel array content =========================================================================
    if(empty($dzArr)){
        $tab.="<tr class=rowcontent><td colspan=20>Data Empty.<td></tr>";
    }else
    if(!empty($listkegiatan))foreach($listkegiatan as $keg){
        $tab.= "<tr class=rowcontent>";
        $tab.= "<td>".$kamuskegiatan[$keg]."</td>";
        $tab.= "<td>".$kamussatuan[$keg]."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fisangset],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fisangbin],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fisangsdb],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fisreabin],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fisreasdb],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fispenset],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][fispensdb],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmangset],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmangbin],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmangsdb],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmreabin],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmreasdb],0)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmpenset],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][hkmpensdb],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][humangset],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][humangbin],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][humangsdb],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][humreabin],2)."</td>";
        $tab.= "<td align=right>".numberformat($dzArr[$keg][humreasdb],2)."</td>";
        $tab.= "</tr>";
    }
    $tab.="</tbody></table>";
			
switch($proses)
{
    case'preview':
    if($unit==''||$periode=='')
    {
        exit("Error:Field required");
    }
    echo $tab;
    break;

    case'excel':
    if($unit==''||$periode=='')
    {
        exit("Error:Field required");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="lbm_pembukaan_lahan".$unit.$periode;
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

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field required");
    }

            $cols=247.5;
            $wkiri=14;
            $wlain=4.3;

    class PDF extends FPDF {
    function Header() {
        global $periode;
        global $unit;
        global $optNm;
        global $optBulan;
        global $tahun;
        global $bulan;
        global $dbname;
        global $luas;
        global $wkiri, $wlain,$afdId;
            $width = $this->w - $this->lMargin - $this->rMargin;
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,'14. '.strtoupper($_SESSION['lang']['bukalahan']),NULL,0,'L',1);
        $this->Cell($width/2,$height,$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun,NULL,0,'R',1);
        $this->Ln();
        $this->Cell($width,$height,$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")",NULL,0,'L',1);
        if($afdId!='')
        {
            $this->Ln();
            $this->Cell($width,$height,$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")",NULL,0,'L',1);
        }
        $this->Ln();
        $this->Ln();
 
        $height = 15;
        $this->SetFont('Arial','B',7);
        $this->Cell($wkiri/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wlain*7/100*$width,$height,$_SESSION['lang']['fisik'].' (HA-UNIT)',1,0,'C',1);	
        $this->Cell($wlain*7/100*$width,$height,'HK-HM',1,0,'C',1);	
        $this->Cell($wlain*5/100*$width,$height,'HK-HM/HA-UNIT',1,0,'C',1);	
        $this->Ln();
        $this->Cell($wkiri/100*$width,$height,$_SESSION['lang']['pekerjaan'],RL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['satuan'],RL,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['pencapaian'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['pencapaian'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Ln();
        $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sbi'],1,0,'C',1);	
        $this->Ln();
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
    $height = 15;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',6);
    
// pdf array content =========================================================================
    if(!empty($listkegiatan))foreach($listkegiatan as $keg){
        $pdf->Cell($wkiri/100*$width,$height,$kamuskegiatan[$keg],1,0,'L',1);	
        $pdf->Cell($wlain/100*$width,$height,$kamussatuan[$keg],1,0,'L',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangset],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangbin],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisangsdb],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisreabin],0),1,0,'R',1);
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fisreasdb],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fispenset],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][fispensdb],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangset],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangbin],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmangsdb],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmreabin],0),1,0,'R',1);
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmreasdb],0),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmpenset],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][hkmpensdb],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangset],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangbin],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humangsdb],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humreabin],2),1,0,'R',1);	
        $pdf->Cell($wlain/100*$width,$height,numberformat($dzArr[$keg][humreasdb],2),1,0,'R',1);	
        $pdf->Ln();
    }
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>

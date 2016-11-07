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
    exit("Error:Field required");
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

// aresta real
$aresta="SELECT sum(luasareaproduktif) as luasareal FROM ".$dbname.".setup_blok
    WHERE kodeorg like '".$unit."%' and statusblok ='TBM'";
if($afdId!='')
{
    $aresta="SELECT sum(luasareaproduktif) as luasareal FROM ".$dbname.".setup_blok
    WHERE kodeorg like '".$afdId."%' and statusblok ='TBM'";
}
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $luasreal=$res['luasareal'];
}   

// aresta budget
$aresta="SELECT sum(hathnini) as luasareal FROM ".$dbname.".bgt_blok
    WHERE kodeblok like '".$unit."%' and statusblok in ('TBM','TB') and tahunbudget = '".$tahun."'";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $luasbudg=$res['luasareal'];
}   

// kamus akun TM
$aresta="SELECT noakun, namaakun,namaakun1 FROM ".$dbname.".keu_5akun
    WHERE ((length(noakun)=7)or(length(noakun)=5)) and (left(noakun,5) between '12606' and '12616')
    ORDER BY noakun";
$query=mysql_query($aresta) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['noakun']=$res['noakun'];
    if($_SESSION['language']=='EN'){
        $dzArr[$res['noakun']]['namaakun']=$res['namaakun1'];
    }else{
            $dzArr[$res['noakun']]['namaakun']=$res['namaakun'];
    }
}   

// cari kegiatan
$kegiatan="SELECT DISTINCT noakun, namakegiatan, satuan FROM ".$dbname.".setup_kegiatan order by kodekegiatan desc";
$query=mysql_query($kegiatan) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kamussatuan[$res['noakun']]=$res['satuan'];
}  

// data rp anggaran setahun
$str="SELECT noakun, setahun FROM ".$dbname.".bgt_summary_biaya_vw 
    WHERE tahunbudget = '".$tahun."' and unit = '".$unit."' and (left(noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['111']=$res['setahun'];
//    @$dzArr[$res['noakun']]['112']=$res['setahun']/$luasbudg;
}

// ambil data fisik anggaran setahun - taken from lbm_slave_pemeliharaan_tm.php
$str="SELECT a.*, b.noakun FROM ".$dbname.".bgt_lbm_volume_kebun_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan
    WHERE a.tahunbudget = '".$tahun."' and a.kebun = '".$unit."' and (left(b.noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['110']=$res['volume'];
    @$dzArr[$res['noakun']]['112']=$dzArr[$res['noakun']]['111']/$res['volume'];
}

// data rp anggaran bulan ini
$str="SELECT noakun, rp".$bulan." as bi FROM ".$dbname.".bgt_summary_biaya_vw 
    WHERE tahunbudget = '".$tahun."' and unit = '".$unit."' and (left(noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['121']=$res['bi'];
//    @$dzArr[$res['noakun']]['122']=$res['bi']/$luasbudg;
}

// ambil data fisik anggaran bulan ini - taken from lbm_slave_pemeliharaan_tm.php
$str="SELECT a.*, b.noakun FROM ".$dbname.".bgt_lbm_porsi_kebun_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan
    WHERE a.tahunbudget = '".$tahun."' and a.kebun = '".$unit."' and (left(b.noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
//    echo $dzArr[$res['noakun']]['110'].' x '.$res['rp'.$bulan];
    $dzArr[$res['noakun']]['120']=$dzArr[$res['noakun']]['110']*$res['rp'.$bulan];
    @$dzArr[$res['noakun']]['122']=$dzArr[$res['noakun']]['121']/$dzArr[$res['noakun']]['120'];
}

$addstr="(";
for($W=1;$W<=intval($bulan);$W++)
{
    if($W<10)$jack="rp0".$W;
    else $jack="rp".$W;
    if($W<intval($bulan))$addstr.=$jack."+";
    else $addstr.=$jack;
}
$addstr.=")";

// data rp anggaran sampai dengan bulan ini
$str="SELECT noakun, ".$addstr." as jumlah FROM ".$dbname.".bgt_summary_biaya_vw 
    WHERE tahunbudget = '".$tahun."' and unit = '".$unit."' and (left(noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['131']=$res['jumlah'];
//    @$dzArr[$res['noakun']]['132']=$res['jumlah']/$luasbudg;
}

// bikin penjumlahan sd bulan ini
$bulanz=$bulan+0;
$porsi='(';
for ($i=1; $i<=$bulanz; $i++)
{
    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
    $porsi.='a.rp'.$ii.'+';   
}
$porsi=substr($porsi,0,-1);
$porsi.=') as porsi';

// ambil data fisik anggaran sampai dengan bulan ini - taken from lbm_slave_pemeliharaan_tm.php
$str="SELECT a.kegiatan, ".$porsi.", b.noakun FROM ".$dbname.".bgt_lbm_porsi_kebun_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kegiatan=b.kodekegiatan
    WHERE a.tahunbudget = '".$tahun."' and a.kebun = '".$unit."' and (left(b.noakun,5) between '12606' and '12616')";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['130']=$dzArr[$res['noakun']]['110']*$res['porsi'];
    @$dzArr[$res['noakun']]['132']=$dzArr[$res['noakun']]['131']/$dzArr[$res['noakun']]['130'];
}

// data rp realisasi bulan ini
$str="SELECT noakun, sum(jumlah) as jumlah FROM ".$dbname.".keu_jurnaldt 
    WHERE tanggal like '".$periode."%' and nojurnal like '%".$unit."%' and (left(noakun,5) between '12606' and '12616')
    GROUP BY noakun";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['211']=$res['jumlah'];
//    @$dzArr[$res['noakun']]['212']=$res['jumlah']/$luasreal;
}

// ambil data fisik realisasi bulan ini - taken from lbm_slave_pemeliharaan_tm.php
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as volume, b.noakun FROM ".$dbname.".kebun_perawatan_dan_spk_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan
    WHERE a.tanggal like '".$periode."%' and a.unit = '".$unit."' and (left(b.noakun,5) between '12606' and '12616')
    GROUP BY b.noakun";
if($afdId!='')
{
    $str="SELECT a.kodekegiatan, sum(a.hasilkerja) as volume, b.noakun FROM ".$dbname.".kebun_perawatan_dan_spk_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan
    WHERE a.tanggal like '".$periode."%' and a.kodeorg like '".$afdId."%' and (left(b.noakun,5) between '12606' and '12616')
    GROUP BY b.noakun";
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['210']=$res['volume'];
    @$dzArr[$res['noakun']]['212']=$dzArr[$res['noakun']]['211']/$res['volume'];
}

// data rp realisasi sampai dengan bulan ini
$str="SELECT noakun, sum(jumlah) as jumlah FROM ".$dbname.".keu_jurnaldt 
    WHERE (tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and nojurnal like '%".$unit."%' and (left(noakun,5) between '12606' and '12616')
    GROUP BY noakun";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['221']=$res['jumlah'];
//    @$dzArr[$res['noakun']]['222']=$res['jumlah']/$luasreal;
}

// ambil data fisik realisasi sampai dengan bulan ini - taken from lbm_slave_pemeliharaan_tm.php
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as volume, b.noakun FROM ".$dbname.".kebun_perawatan_dan_spk_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan
    WHERE (a.tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and a.unit = '".$unit."' and (left(b.noakun,5) between '12606' and '12616')
    GROUP BY b.noakun";
if($afdId!='')
{
    $str="SELECT a.kodekegiatan, sum(a.hasilkerja) as volume, b.noakun FROM ".$dbname.".kebun_perawatan_dan_spk_vw a
    LEFT JOIN ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan
    WHERE (a.tanggal between '".$tahun."-01-01' and LAST_DAY('".$periode."-15')) and a.kodeorg like '".$afdId."%' and (left(b.noakun,5) between '12606' and '12616')
    GROUP BY b.noakun";
}
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['noakun']]['220']=$res['volume'];
    @$dzArr[$res['noakun']]['222']=$dzArr[$res['noakun']]['221']/$res['volume'];
}

if(!empty($dzArr))foreach($dzArr as $keg){
    @$dzArr[$keg['noakun']]['311']=100*$keg['221']/$keg['111'];
    @$dzArr[$keg['noakun']]['312']=100*$keg['221']/$keg['131'];
    $total['111']+=$keg['111'];
    $total['121']+=$keg['121'];
    $total['131']+=$keg['131'];
    $total['211']+=$keg['211'];
    $total['221']+=$keg['221'];
}
@$total['112']=$total['111']/$luasbudg;
@$total['122']=$total['121']/$luasbudg;
@$total['132']=$total['131']/$luasbudg;
@$total['212']=$total['211']/$luasreal;
@$total['222']=$total['221']/$luasreal;
@$total['311']=100*$total['221']/$total['111'];
@$total['312']=100*$total['221']/$total['131'];

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
        <td colspan=8 align=left><font size=3>19. ".strtoupper($_SESSION['lang']['biaya'])." ".strtoupper($_SESSION['lang']['pemeltanaman'])." TBM</font></td>
        <td colspan=6 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=14 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>";
if($afdId!='')
{
    $tab.="<tr><td colspan=14 align=left>".$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")</td></tr>";
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
    <td align=right colspan=3 ".$bg.">".$_SESSION['lang']['luasareal']." TBM:</td>
    <td align=right colspan=3 ".$bg.">".numberformat($luasbudg,2)."</td>
    <td align=left colspan=6 ".$bg.">Ha</td>
    <td align=right colspan=3 ".$bg.">".numberformat($luasreal,2)."</td>
    <td align=left colspan=5 ".$bg.">Ha</td>
    </tr>
    <tr>
    <td align=center rowspan=3 ".$bg.">No.</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['pekerjaan']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['satuan']."</td>
    <td align=center colspan=9 ".$bg.">".$_SESSION['lang']['anggaran']."</td>
    <td align=center colspan=6 ".$bg.">".$_SESSION['lang']['realisasi']."</td>
    <td align=center rowspan=2 colspan=2 ".$bg.">% ".$_SESSION['lang']['pencapaian']."</td>
    </tr>
    <tr>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['bulanini']."</td>
    <td align=center colspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">Volume</td>
    <td align=center ".$bg.">Rp. (000)</td>
    <td align=center ".$bg.">Rp./Sat</td>
    <td align=center ".$bg.">Volume</td>
    <td align=center ".$bg.">Rp. (000)</td>
    <td align=center ".$bg.">Rp./Sat</td>
    <td align=center ".$bg.">Volume</td>
    <td align=center ".$bg.">Rp. (000)</td>
    <td align=center ".$bg.">Rp./Sat</td>
    <td align=center ".$bg.">Volume</td>
    <td align=center ".$bg.">Rp. (000)</td>
    <td align=center ".$bg.">Rp./Sat</td>
    <td align=center ".$bg.">Volume</td>
    <td align=center ".$bg.">Rp. (000)</td>
    <td align=center ".$bg.">Rp./Sat</td>
    <td align=center ".$bg.">".$_SESSION['lang']['setahun']."</td>
    <td align=center ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    </thead>
    <tbody>
";
        
    $dummy='';
    $no=1;
// excel array content =========================================================================
    if(empty($dzArr)){
        $tab.="<tr class=rowcontent><td colspan=14>Data Empty.<td></tr>";
    }else
    if(!empty($dzArr))foreach($dzArr as $keg){
        if(strlen($keg['noakun'])==5){
            $tab.= "<tr class=rowcontent>";
            $tab.= "<td colspan=3>".$keg['noakun']." - ".$keg['namaakun']."</td>";
            $tab.= "<td colspan=17></td>";
            $tab.= "</tr>";
        }else{
            $tab.= "<tr class=rowcontent>";
            $tab.= "<td align=right>".$no."</td>";
            $tab.= "<td>".$keg['namaakun']."</td>";
            $tab.= "<td>".$kamussatuan[$keg['noakun']]."</td>";
            $tab.= "<td align=right>".numberformat($keg[110],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[111]/1000,0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[112],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[120],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[121]/1000,0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[122],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[130],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[131]/1000,0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[132],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[210],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[211]/1000,0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[212],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[220],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[221]/1000,0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[222],0)."</td>";
            $tab.= "<td align=right>".numberformat($keg[311],2)."</td>";
            $tab.= "<td align=right>".numberformat($keg[312],2)."</td>";
            $tab.= "</tr>";
            $no+=1;            
        }        
    }
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=center colspan=3>Total</td>";
    $tab.= "<td align=right></td>";
    $tab.= "<td align=right>".numberformat($total[111]/1000,0)."</td>";
    $tab.= "<td align=right>".numberformat($total[112],0)."</td>";
    $tab.= "<td align=right></td>";
    $tab.= "<td align=right>".numberformat($total[121]/1000,0)."</td>";
    $tab.= "<td align=right>".numberformat($total[122],0)."</td>";
    $tab.= "<td align=right></td>";
    $tab.= "<td align=right>".numberformat($total[131]/1000,0)."</td>";
    $tab.= "<td align=right>".numberformat($total[132],0)."</td>";
    $tab.= "<td align=right></td>";
    $tab.= "<td align=right>".numberformat($total[211]/1000,0)."</td>";
    $tab.= "<td align=right>".numberformat($total[212],0)."</td>";
    $tab.= "<td align=right></td>";
    $tab.= "<td align=right>".numberformat($total[221]/1000,0)."</td>";
    $tab.= "<td align=right>".numberformat($total[222],0)."</td>";
    $tab.= "<td align=right>".numberformat($total[311],2)."</td>";
    $tab.= "<td align=right>".numberformat($total[312],2)."</td>";
    $tab.= "</tr>";
    $tab.="</tbody></table>";
			
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
        exit("Error:Field required");
    }

    $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="lbm_biayapemeliharan_tbm_".$unit.$periode;
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
            $wkiri=15;
            $wlain=4.5;

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
        global $wkiri, $wlain;
        global $luasbudg, $luasreal;
            $width = $this->w - $this->lMargin - $this->rMargin;
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,"19. ".strtoupper($_SESSION['lang']['biaya'])." ".strtoupper($_SESSION['lang']['pemeltanaman'])." TBM",NULL,0,'L',1);
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
        $this->Cell((3/100*$width)+(($wlain+$wkiri)/100*$width),$height,$_SESSION['lang']['luasareal'].' TBM:',0,0,'R',1);	
        $this->Cell($wlain*9/100*$width,$height,numberformat($luasbudg,2).' Ha',0,0,'C',1);	
        $this->Cell($wlain*6/100*$width,$height,numberformat($luasreal,2).' Ha',0,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,'',0,0,'L',1);	
        $this->Ln();
        $this->Cell(3/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'',TRL,0,'C',1);	
        $this->Cell($wlain*9/100*$width,$height,$_SESSION['lang']['anggaran'],1,0,'C',1);	
        $this->Cell($wlain*6/100*$width,$height,$_SESSION['lang']['realisasi'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,'',TRL,0,'C',1);	
        $this->Ln(); 
        $this->Cell(3/100*$width,$height,'No.',RL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,$_SESSION['lang']['pekerjaan'],RL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['satuan'],RL,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['bulanini'],1,0,'C',1);	
        $this->Cell($wlain*3/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
        $this->Cell($wlain*2/100*$width,$height,$_SESSION['lang']['pencapaian'],BRL,0,'C',1);	
        $this->Ln();
        $this->Cell(3/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wkiri/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'',BRL,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Volume',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp. (000)',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp./Sat',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Volume',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp. (000)',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp./Sat',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Volume',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp. (000)',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp./Sat',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Volume',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp. (000)',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp./Sat',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Volume',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp. (000)',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,'Rp./Sat',1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['setahun'],1,0,'C',1);	
        $this->Cell($wlain/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
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
    $pdf->SetFont('Arial','',7);
    
    $no=1;
// pdf array content =========================================================================
    if(!empty($dzArr))foreach($dzArr as $keg){
        if(strlen($keg['noakun'])==5){
            $pdf->Cell((3+$wkiri+$wlain)/100*$width,$height,$keg['noakun'].' - '.$keg['namaakun'],1,0,'L',1);	
            $pdf->Cell(($wlain*17)/100*$width,$height,'',1,0,'R',1);	            
        }else{
            $pdf->Cell(3/100*$width,$height,$no,1,0,'R',1);	
            $pdf->Cell($wkiri/100*$width,$height,$keg['namaakun'],1,0,'L',1);	
            $pdf->Cell($wlain/100*$width,$height,$kamussatuan[$keg['noakun']],1,0,'L',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[110],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[111]/1000,0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[112],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[120],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[121]/1000,0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[122],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[130],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[131]/1000,0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[132],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[210],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[211]/1000,0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[212],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[220],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[221]/1000,0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[222],0),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[311],2),1,0,'R',1);	
            $pdf->Cell($wlain/100*$width,$height,numberformat($keg[312],2),1,0,'R',1);	
            $no+=1;            
        }
        $pdf->Ln();
    }else echo 'Data Empty.';
    $pdf->Cell((3/100*$width)+($wkiri/100*$width)+($wlain/100*$width),$height,'Total',1,0,'C',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[111]/1000,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[112],0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[121]/1000,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[122],0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[131]/1000,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[132],0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[211]/1000,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[212],0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,'',1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[221]/1000,0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[222],0),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[311],2),1,0,'R',1);	
    $pdf->Cell($wlain/100*$width,$height,numberformat($total[312],2),1,0,'R',1);
    
    $pdf->Output();	 
    break;

    default:
    break;
}
	
?>

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
    exit("Error:Field Tidak Boleh Kosong");
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

// areal statement
$sOrg="select sum(luasareaproduktif) as luas from ".$dbname.".setup_blok where kodeorg like '".$unit."%' and tahuntanam <= '".$tahun."'";
if($afdId!='')
{
    $sOrg="select sum(luasareaproduktif) as luas from ".$dbname.".setup_blok where kodeorg like '".$afdId."%' and tahuntanam <= '".$tahun."'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $luas=$rOrg['luas'];
}

// building array: dzArr (main data) =========================================================================
// as seen on sdm_slave_2prasarana.php
$dzArr=array();

// ambil data tahun perlolehan + jumlahnya
$sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
    $sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE a.kodeorg like '".$afdId."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][jenis]=$res['jenisprasarana'];
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][tahun]=$res['tahunperolehan'];
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][jumlah]+=$res['jumlahnya'];
    $tahuntahun[$res['tahunperolehan']]=$res['tahunperolehan'];
}

//ambil data awal tahun (posisi awal tahun)
$sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        a.tahunperolehan < '".$tahun."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
   $sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE a.kodeorg like '".$afdId."%' and  
        a.tahunperolehan < '".$tahun."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan"; 
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $awaldzArr[$res['jenisprasarana']][$res['tahunperolehan']]+=$res['jumlahnya'];
}

// cari bulan lalu
$tahunlalu=$tahun;
$bulanlalu=$bulan-1;
if($bulan==1){
    $tahunlalu=$tahun-1;
    $bulanlalu=12;
}
if(strlen($bulanlalu)==1)$bulanlalu='0'.$bulanlalu;

//ambil data sd bulan lalu (jumlah prasarana pada bulan lalu)
$sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu."".$bulanlalu."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
   $sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE a.kodeorg like '".$afdId."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu."".$bulanlalu."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan"; 
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $bulanlaludzArr[$res['jenisprasarana']][$res['tahunperolehan']]+=$res['jumlahnya'];
}

// ambil kondisi bulan lalu (kondisi bulan lalu)
$sPrasarana="SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu."".$bulanlalu."' and
        b.tanggal like '".$tahunlalu."".$bulanlalu."%' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
  $sPrasarana="SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana
    WHERE a.kodeorg like '".$unit."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu."".$bulanlalu."' and
        b.tanggal like '".$tahunlalu."".$bulanlalu."%' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";  
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kondisilaludzArr[$res['jenisprasarana']][$res['tahunperolehan']][$res['kondisi']]+=$res['jumlahnya'];
}

//ambil data sd bulan ini
$sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
   $sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE a.kodeorg like '".$afdId."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan"; 
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $bulaninidzArr[$res['jenisprasarana']][$res['tahunperolehan']]+=$res['jumlahnya'];
}

// ambil kondisi bulan ini (kondisi bulan ini)
$sPrasarana="SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and
        b.tanggal like '".$tahun."".$bulan."%' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
    $sPrasarana="SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana
    WHERE a.kodeorg like '".$afdId."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun."".$bulan."' and
        b.tanggal like '".$tahun."".$bulan."%' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kondisiinidzArr[$res['jenisprasarana']][$res['tahunperolehan']][$res['kondisi']]+=$res['jumlahnya'];
}

//ambil data bulan ini (penambahan bulan ini)
$sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE left(a.kodeorg,4) = '".$unit."' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) = '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan";
if($afdId!='')
{
   $sPrasarana="SELECT a.*, sum(a.jumlah) as jumlahnya FROM ".$dbname.".sdm_prasarana a 
    WHERE a.kodeorg like '".$afdId."%' and  
        concat(a.tahunperolehan,'',a.bulanperolehan) = '".$tahun."".$bulan."' and a.status = '1'
    GROUP BY a.tahunperolehan, a.jenisprasarana
    ORDER BY a.tahunperolehan"; 
}
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $inidzArr[$res['jenisprasarana']][$res['tahunperolehan']]+=$res['jumlahnya'];
}

$sPrasarana="SELECT * FROM ".$dbname.".sdm_5kl_prasarana";
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $kl_prasarana[$res['kode']]=$res['kode'];
    $namakl_prasarana[$res['kode']]=$res['nama'];
}

$sPrasarana="SELECT * FROM ".$dbname.".sdm_5jenis_prasarana";
$query=mysql_query($sPrasarana) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $jenis_prasarana[$res['jenis']]=$res['jenis'];
    $kelompokjenis_prasarana[$res['jenis']]=$res['kelompok'];
    $namajenis_prasarana[$res['jenis']]=$res['nama'];
    $satuanjenis_prasarana[$res['jenis']]=$res['satuan'];
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
        <td colspan=9 align=left><font size=3>04.7 INVENTARIS BANGUNAN, SARANA DAN PRASARAN</font></td>
        <td colspan=8 align=right>".$_SESSION['lang']['bulan']." : ".$optBulan[$bulan]." ".$tahun."</td>
     </tr> 
     <tr><td colspan=9 align=left>".$_SESSION['lang']['unit']." : ".$optNm[$unit]." (".$unit.")</td></tr>  " ;
if($afdId!='')
{
    $tab.="<tr><td colspan=9 align=left>".$_SESSION['lang']['afdeling']." : ".$optNm[$afdId]." (".$afdId.")</td></tr> ";
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
    <td align=center colspan=2 rowspan=3 ".$bg.">".$_SESSION['lang']['klSarana']."/".$_SESSION['lang']['jnsPrasarana']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['satuan']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['tahunperolehan']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['posisiawaltahun']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['sdbulanlalu']."</td>
    <td align=center colspan=2 ".$bg.">".$_SESSION['lang']['perubahan']."</td>
    <td align=center rowspan=3 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    <td align=center colspan=8 ".$bg.">".$_SESSION['lang']['kondisi']."</td>
    </tr>
    <tr>
    <td align=center rowspan=2 ".$bg.">+</td>
    <td align=center rowspan=2 ".$bg.">-</td>
    <td align=center colspan=4 ".$bg.">".$_SESSION['lang']['sdbulanlalu']."</td>
    <td align=center colspan=4 ".$bg.">".$_SESSION['lang']['sdbulanini']."</td>
    </tr>
    <tr>
    <td align=center ".$bg.">B-BD</td>
    <td align=center ".$bg.">B-TD</td>
    <td align=center ".$bg.">R-BD</td>
    <td align=center ".$bg.">R-TD</td>
    <td align=center ".$bg.">B-BD</td>
    <td align=center ".$bg.">B-TD</td>
    <td align=center ".$bg.">R-BD</td>
    <td align=center ".$bg.">R-TD</td>
    </tr>
    </thead>
    <tbody>
";
    
    $dummy='';
// excel array content =========================================================================
    if(empty($dzArr)){
        $tab.="<tr class=rowcontent><td colspan=16>Data Empty.<td></tr>";
    }else
    foreach($kl_prasarana as $kl){
        foreach($jenis_prasarana as $jenis){
            if($kelompokjenis_prasarana[$jenis]==$kl) {
                foreach($tahuntahun as $tetey){
                    if($dzArr[$jenis][$tetey][jenis]!=''){
                    $tab.= "<tr class=rowcontent>";
                    if($kl!=$dummy){
                        $tab.= "<td>".$namakl_prasarana[$kl]."</td>";
                        $dummy=$kl;
                    } else $tab.= "<td></td>";
                    $tab.= "<td>".$namajenis_prasarana[$jenis]."</td>";
                    $tab.= "<td>".$satuanjenis_prasarana[$dzArr[$jenis][$tetey][jenis]]."</td>";
                    $tab.= "<td>".$dzArr[$jenis][$tetey][tahun]."</td>";
                    $tab.= "<td>".number_format($awaldzArr[$jenis][$tetey])."</td>";
                    $tab.= "<td>".number_format($bulanlaludzArr[$jenis][$tetey])."</td>";
                    if(number_format($inidzArr[$jenis][$tetey])>0)$tab.= "<td>".number_format($inidzArr[$jenis][$tetey])."</td>"; else $tab.= "<td>0</td>";
                    if(number_format($inidzArr[$jenis][$tetey])<0)$tab.= "<td>".number_format(-1*$inidzArr[$jenis][$tetey])."</td>"; else $tab.= "<td>0</td>";
                    $tab.= "<td>".number_format($bulaninidzArr[$jenis][$tetey])."</td>";
                    // lihat di enum kondisi tabel sdm_kondisi_prasarana
                    $tab.= "<td>".number_format($kondisilaludzArr[$jenis][$tetey]['BDB'])."</td>";
                    $tab.= "<td>".number_format($kondisilaludzArr[$jenis][$tetey]['BTD'])."</td>";
                    $tab.= "<td>".number_format($kondisilaludzArr[$jenis][$tetey]['RBD'])."</td>";
                    $tab.= "<td>".number_format($kondisilaludzArr[$jenis][$tetey]['RTD'])."</td>";
                    $tab.= "<td>".number_format($kondisiinidzArr[$jenis][$tetey]['BDB'])."</td>";
                    $tab.= "<td>".number_format($kondisiinidzArr[$jenis][$tetey]['BTD'])."</td>";
                    $tab.= "<td>".number_format($kondisiinidzArr[$jenis][$tetey]['RBD'])."</td>";
                    $tab.= "<td>".number_format($kondisiinidzArr[$jenis][$tetey]['RTD'])."</td>";
                    $tab.= "</tr>";
                    }
                }
            }
        }
    }
    $tab.="</tbody></table>";
    $tab.="<br>Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.<br><br>";
			
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
    $nop_="lbm_prasarana_".$unit.$periode;
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
        exit("Error:Field Tidak Boleh Kosong");
    }

            $cols=247.5;

    class PDF extends FPDF {
    function Header() {
        global $periode;
        global $unit;
        global $optNm;
        global $optBulan;
        global $tahun;
        global $bulan;
        global $dbname;
        global $luas,$afdId;
            $width = $this->w - $this->lMargin - $this->rMargin;
  

        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);

        $this->Cell($width/2,$height,'04.7 INVENTARIS BANGUNAN, SARANA DAN PRASARANA ',NULL,0,'L',1);
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

        $this->SetFont('Arial','B',8);
            $this->Cell(30/100*$width,$height,'',TRL,0,'C',1);		
            $this->Cell(5/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell(8/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell(8/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell(7/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['perubahan'],1,0,'C',1);	
            $this->Cell(7/100*$width,$height,'',TRL,0,'C',1);	
            $this->Cell(28/100*$width,$height,$_SESSION['lang']['kondisi'],1,0,'C',1);	
            $this->Ln();
            $this->Cell(30/100*$width,$height,$_SESSION['lang']['klSarana']."/".$_SESSION['lang']['jnsPrasarana'],RL,0,'C',1);		
            $this->Cell(5/100*$width,$height,$_SESSION['lang']['satuan'],RL,0,'C',1);	
            $this->SetFont('Arial','',8);
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['tahunperolehan'],RL,0,'C',1);	
            $this->Cell(8/100*$width,$height,$_SESSION['lang']['posisiawaltahun'],RL,0,'C',1);	
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['sdbulanlalu'],RL,0,'C',1);	
            $this->SetFont('Arial','',8);
            $this->Cell(3.515/100*$width,$height,'+',TRL,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'-',TRL,0,'C',1);	
            $this->SetFont('Arial','',8);
            $this->Cell(7/100*$width,$height,$_SESSION['lang']['sdbulanini'],RL,0,'C',1);	
            $this->SetFont('Arial','',8);
            $this->Cell(14/100*$width,$height,$_SESSION['lang']['sdbulanlalu'],1,0,'C',1);	
            $this->Cell(14/100*$width,$height,$_SESSION['lang']['sdbulanini'],1,0,'C',1);	
            $this->Ln();
            $this->Cell(30/100*$width,$height,'',BRL,0,'C',1);		
            $this->Cell(5/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(8/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(8/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(7/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(7/100*$width,$height,'',BRL,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'B-BD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'B-TD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'R-BD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'R-TD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'B-BD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'B-TD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'R-BD',1,0,'C',1);	
            $this->Cell(3.5/100*$width,$height,'R-TD',1,0,'C',1);	
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
    $pdf->SetFont('Arial','',8);
    
    $dummy='';
// pdf array content =========================================================================
    if(empty($dzArr)){
        $pdf->Cell(100/100*$width,$height,'Data Empty.',1,1,'L',1);	
    }else
    foreach($kl_prasarana as $kl){
        foreach($jenis_prasarana as $jenis){
            if($kelompokjenis_prasarana[$jenis]==$kl) {
                foreach($tahuntahun as $tetey){
                    if($dzArr[$jenis][$tetey][jenis]!=''){
                        if($kl!=$dummy){
                            $pdf->Cell(10/100*$width,$height,$namakl_prasarana[$kl],TRL,0,'L',1);		
                            $dummy=$kl;
                        } else $pdf->Cell(10/100*$width,$height,'',RL,0,'L',1);
                        $pdf->Cell(20/100*$width,$height,$namajenis_prasarana[$jenis],1,0,'L',1);		
                        $pdf->Cell(5/100*$width,$height,$satuanjenis_prasarana[$dzArr[$jenis][$tetey][jenis]],1,0,'L',1);	
                        $pdf->Cell(8/100*$width,$height,$dzArr[$jenis][$tetey][tahun],1,0,'C',1);	
                        $pdf->Cell(8/100*$width,$height,number_format($awaldzArr[$jenis][$tetey]),1,0,'R',1);	
                        $pdf->Cell(7/100*$width,$height,number_format($bulanlaludzArr[$jenis][$tetey]),1,0,'R',1);	
                        if(number_format($inidzArr[$jenis][$tetey])>0)
                            $pdf->Cell(3.5/100*$width,$height,number_format($inidzArr[$jenis][$tetey]),1,0,'R',1);	
                        else $pdf->Cell(3.5/100*$width,$height,'0',1,0,'R',1);	
                        if(number_format($inidzArr[$jenis][$tetey])<0)
                            $pdf->Cell(3.5/100*$width,$height,number_format(-1*$inidzArr[$jenis][$tetey]),1,0,'R',1);	
                        else $pdf->Cell(3.5/100*$width,$height,'0',1,0,'R',1);	
                        $pdf->Cell(7/100*$width,$height,number_format($bulaninidzArr[$jenis][$tetey]),1,0,'R',1);	
                        // lihat di enum kondisi tabel sdm_kondisi_prasarana
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisilaludzArr[$jenis][$tetey]['BTD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisilaludzArr[$jenis][$tetey]['BTD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisilaludzArr[$jenis][$tetey]['RBD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisilaludzArr[$jenis][$tetey]['RTD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisiinidzArr[$jenis][$tetey]['BTD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisiinidzArr[$jenis][$tetey]['BTD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisiinidzArr[$jenis][$tetey]['RBD']),1,0,'R',1);	
                        $pdf->Cell(3.5/100*$width,$height,number_format($kondisiinidzArr[$jenis][$tetey]['RTD']),1,1,'R',1);	
                    }
                }
            }
        }
    }    
    $pdf->Cell(10/100*$width,$height,'',T,0,'L',1);		
    $pdf->Ln();
    $pdf->Cell($width,$height,'Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.',0,0,'L',1);		

    
    $pdf->Output();	
    break;

    default:
    break;
}
	
?>

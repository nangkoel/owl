<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']!=''?$proses=$_POST['proses']:$proses=$_GET['proses'];
$_POST['pt']!=''?$pt=$_POST['pt']:$pt=$_GET['pt'];
$_POST['unit']!=''?$unit=$_POST['unit']:$unit=$_GET['unit'];
$_POST['kepada']!=''?$kepada=$_POST['kepada']:$kepada=$_GET['kepada'];
$_POST['tipe']!=''?$tipe=$_POST['tipe']:$tipe=$_GET['tipe'];
$_POST['tanggal']!=''?$tanggal=$_POST['tanggal']:$tanggal=$_GET['tanggal'];
$_POST['sd']!=''?$sd=$_POST['sd']:$sd=$_GET['sd'];
$tanggal=tanggalsystem($tanggal); 
$tgldari=substr($tanggal,0,4).'-'.substr($tanggal,4,2).'-'.substr($tanggal,6,2);
$sd=tanggalsystem($sd); 
$tglsd=substr($sd,0,4).'-'.substr($sd,4,2).'-'.substr($sd,6,2);
if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if(($tanggal=='')or($sd=='')){
            echo"Error: Date is obligatory."; exit;
    }
    if($tgldari>$tglsd){
            echo"Error: First date must smaller than the secon date."; exit;
    }
}

# Ambil akun r/k tujuan
$listakun='(';
$no=0;
$s_rk = "select akunhutang,akunpiutang from ".$dbname.".keu_5caco where kodeorg='".$kepada."'";
$q_rk = mysql_query($s_rk) or die(mysql_error($conn));
while($r_rk = mysql_fetch_assoc($q_rk)){
    $no+=1;
    $listakun.="'".$r_rk['akunhutang']."',";
    $listakun.="'".$r_rk['akunpiutang']."',";
}
$listakun=substr($listakun,0,-1);
$listakun.=')';
if($no==0)$listakun="('')";

# Ambil transaksi
if($tipe=='Kredit Note'){$kolom='kredit';}
else{$kolom='debet';}
$s_transaksi = "select a.tanggal,a.noreferensi,a.keterangan,".$kolom." as kolom,notransaksireferensi from ".$dbname.".keu_jurnaldt_vw a 
                left join ".$dbname.".log_transaksi_vw b on a.noreferensi=b.notransaksi 
                where noakun in $listakun and a.tanggal between '".$tgldari."' and '".$tglsd."'
                and kodeorg='".$unit."' ";
$q_transaksi = mysql_query($s_transaksi) or die(mysql_error($conn));

if($proses=='excel'){
    $bg=" bgcolor=#DEDEDE";
    $brdr=1;
}
else{ 
    $bg="";
    $brdr=0;
}

if($proses=='excel'){
    $bgcoloraja="bgcolor=#DEDEDE ";
    $brdr=1;
    $stream.="
    <table border=0>
    <tr><td align=center colspan=5><b>Laporan ".$_SESSION['lang']['debetkreditnote']."</b></td></tr>
    <tr>
        <td align=left>".$_SESSION['lang']['namapt']."</td>
        <td>:".$pt."</td>
        <td colspan=3 align=center>".$tipe."</td>
    </tr>
    <tr>
        <td align=left>".$_SESSION['lang']['unitkerja']."</td>
        <td colspan=4>:".$unit."</td>
    </tr>
    <tr>
        <td align=left>".$_SESSION['lang']['kepada']."</td>
        <td colspan=4>:".$kepada."</td>
    </tr>
    <tr>
        <td align=left>".$_SESSION['lang']['periode']."</td>
        <td colspan=4>:".substr($tanggal,6,2).'-'.substr($tanggal,4,2).'-'.substr($tanggal,0,4)." 
        s/d ".substr($sd,6,2).'-'.substr($sd,4,2).'-'.substr($sd,0,4)."</td>
    </tr><tr><td colspan=5></td></tr>
    </table>";
}

$stream.="<div style=overflow:auto; height:300px;>";
$stream.="<table cellspacing='1' border='".$brdr."' class='sortable'>
<thead>
<tr class=rowheader>
<td align=center id=no>No.</td>
<td align=center id=tgl>".$_SESSION['lang']['tanggal']."</td>
<td align=center id=noref>".$_SESSION['lang']['noreferensi']."</td>
<td align=center id=noref>".$_SESSION['lang']['notransaksi']."</td>
<td align=center id=ket>".$_SESSION['lang']['keterangan']."</td>    
<td align=center id=kolom>".$_SESSION['lang']['jumlah']."</td>
</tr></thead>
<tbody>";
$no=0;
while($r_transaksi = mysql_fetch_assoc($q_transaksi)){
    $no++;
    $stream.="<tr class=rowcontent>
                  <td align=center id=no>".$no."</td>
                  <td align=center id=tgl>".$r_transaksi['tanggal']."</td>
                  <td align=left id=noref>".$r_transaksi['noreferensi']."</td>
                  <td align=left id=noref>".$r_transaksi['notransaksireferensi']."</td>
                  <td align=left id=ket>".$r_transaksi['keterangan']."</td>
                  <td align=right id=kolom>".number_format($r_transaksi['kolom'],0)."</td>
              </tr>";
    $jumlah+=$r_transaksi['kolom'];
}
$stream.="<tr><td colspan=4 align=center><b>".$_SESSION['lang']['jumlah']."</b></td>
          <td><b>".number_format($jumlah,0)."</b></td></tr>";
$stream.="</tbody></table>";

switch($proses){
case 'load_unit_kpd':
    $opt_unit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $s_unit="select * from ".$dbname.".organisasi where induk='".$pt."' order by kodeorganisasi asc";
    $q_unit=mysql_query($s_unit) or die(mysql_error($conn));
    while($r_unit=mysql_fetch_assoc($q_unit))
    {
        $opt_unit.="<option value='".$r_unit['kodeorganisasi']."'>".$r_unit['namaorganisasi']."</option>";  
        
    }
    echo $opt_unit;
break;
case 'load_kpd':
    $opt_kepada="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $s_kepada="select * from ".$dbname.".organisasi 
               where length(kodeorganisasi)=4 and kodeorganisasi != '".$unit."' 
               order by namaorganisasi asc";
//    exit("error: ".$s_kepada);
    $q_kepada=mysql_query($s_kepada) or die(mysql_error($conn));
    while($r_kepada=mysql_fetch_assoc($q_kepada))
    {
        $opt_kepada.="<option value='".$r_kepada['kodeorganisasi']."'>".$r_kepada['namaorganisasi']."</option>";  
        
    }
    echo $opt_kepada;
break;
case 'preview':
    echo $stream;
break;
case 'excel':   
    $stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
    $dte=date("YmdHis");
    $nop_="DebetKreditNote_".$kepada;
    if(strlen($stream)>0)
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
        if(!fwrite($handle,$stream))
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
class PDF extends FPDF
{
    function Header() 
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
            global $pt;
            global $unit;
            global $kepada;
            global $tanggal;
            global $sd;
            global $tipe;

        $query = selectQuery($dbname,'organisasi','alamat,telepon',
                 "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);

        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
        $cols=247.5;
        if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
        $this->Image($path,$this->lMargin,$this->tMargin,70);	
        $this->SetFont('Arial','B',8);
        $this->SetFillColor(255,255,255);	
        $this->SetX(100);   
        $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
        $this->SetX(100); 		
        $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
        $this->SetX(100); 			
        $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
        $this->Line($this->lMargin,$this->tMargin+($height*3),
        $this->lMargin+$width,$this->tMargin+($height*3));

        $s_pt = "select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
        $q_pt = mysql_query($s_pt) or die(mysql_error($conn));
        $r_pt = mysql_fetch_assoc($q_pt);
        $s_unit = "select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$unit."'";
        $q_unit = mysql_query($s_unit) or die(mysql_error($conn));
        $r_unit = mysql_fetch_assoc($q_unit);
        $s_kpd = "select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kepada."'";
        $q_kpd = mysql_query($s_kpd) or die(mysql_error($conn));
        $r_kpd = mysql_fetch_assoc($q_kpd);
        $this->Ln();
        $this->SetFont('Arial','',7);
        $this->SetX(100);  
        $this->Cell(28,$height,($_SESSION['lang']['namapt']),'',0,'L',1);
        $this->Cell(5,$height,":",'',0,'C',1);
        $this->Cell(200,$height,$r_pt['namaorganisasi'],'',0,'L',1);
        $this->Cell(157,$height,$tipe,'',1,'L',1);
        
        $this->SetX(100); 
        $this->Cell(28,$height,($_SESSION['lang']['unit']),'',0,'L',1);
        $this->Cell(5,$height,":",'',0,'C',1);
        $this->Cell(200,$height,$r_unit['namaorganisasi'],'',0,'L',1);
        $this->Cell(157,$height,'','',1,'R',1);
        
        $this->SetX(100); 
        $this->Cell(28,$height,($_SESSION['lang']['kepada']),'',0,'L',1);
        $this->Cell(5,$height,":",'',0,'C',1);
        $this->Cell(200,$height,$r_kpd['namaorganisasi'],'',0,'L',1);
        $this->Cell(157,$height,'','',1,'R',1);
        
        $this->SetX(100); 
        $this->Cell(28,$height,ucfirst($_SESSION['lang']['periode']),'',0,'L',1);
        $this->Cell(5,$height,":",'',0,'C',1);
        $this->Cell(200,$height,substr($tanggal,6,2)."-".substr($tanggal,4,2)."-".substr($tanggal,0,4)." s/d ".
               substr($sd,6,2)."-".substr($sd,4,2)."-".substr($sd,0,4),B,0,'L',1);
        $this->Cell(157,$height,'','',1,'R',1);
        
        $this->SetFont('Arial','',7);
        $this->SetFillColor(220,220,220);
        $this->SetX(100); 
        $this->Cell(20,$height,"No.",1,0,'C',1);	
        $this->Cell(50,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
        $this->Cell(70,$height,$_SESSION['lang']['noreferensi'],1,0,'C',1);	
        $this->Cell(200,$height,$_SESSION['lang']['keterangan'],1,0,'C',1);		
        $this->Cell(70,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);
   }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
 }
 //================================
$pdf=new PDF('P','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 10;
$pdf->AddPage();
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',7);

$q_transaksi = mysql_query($s_transaksi) or die(mysql_error($conn));
$no=0;
while($r_transaksi = mysql_fetch_assoc($q_transaksi)){
    $no++;
    $pdf->SetX(100);
    $pdf->Cell(20,$height,$no,1,0,'C',1);		
    $pdf->Cell(50,$height,$r_transaksi['tanggal'],TBLR,0,'C',1);	
    $pdf->Cell(70,$height,$r_transaksi['noreferensi'],TBLR,0,'L',1);		
    $pdf->Cell(200,$height,$r_transaksi['keterangan'],TBLR1,0,'L',1);
    $pdf->Cell(70,$height,number_format($r_transaksi['kolom']),TBLR,1,'R',1);
}
$pdf->SetX(100);
$pdf->Cell(340,$height,$_SESSION['lang']['jumlah'],TBLR,0,'C',1);	
$pdf->Cell(70,$height,number_format($jumlah,0),TBLR,1,'R',1);	
$pdf->Ln();$pdf->Ln();
$pdf->SetX(100);
$pdf->Cell(20,$height,'CC:',TL,0,'L',1);
$pdf->Cell(180,$height,'',TR,0,'L',1);
$pdf->Cell(70,$height,$_SESSION['lang']['disetujui'],TBLR,0,'C',1);	
$pdf->Cell(70,$height,$_SESSION['lang']['diperiksa'],TBLR,0,'C',1);		
$pdf->Cell(70,$height,$_SESSION['lang']['diperiksa'],TBLR,1,'C',1);

$pdf->SetX(100);
$pdf->Cell(20,$height,'',L,0,'L',1);
$pdf->Cell(180,$height,'- Accounting HO',R,0,'L',1);		
$pdf->Cell(70,$height,'',TLR,0,'C',1);	
$pdf->Cell(70,$height,'',TLR,0,'C',1);		
$pdf->Cell(70,$height,'',TLR,1,'C',1);

$pdf->SetX(100);
$pdf->Cell(20,$height,'',L,0,'L',1);
$pdf->Cell(180,$height,'- Arsip',R,0,'L',1);		
$pdf->Cell(70,$height,'',LR,0,'C',1);	
$pdf->Cell(70,$height,'',LR,0,'C',1);		
$pdf->Cell(70,$height,'',LR,1,'C',1);

$pdf->SetX(100);
$pdf->Cell(20,$height,'',L,0,'L',1);
$pdf->Cell(180,$height,'',R,0,'L',1);		
$pdf->Cell(70,$height,'',LR,0,'C',1);	
$pdf->Cell(70,$height,'',LR,0,'C',1);		
$pdf->Cell(70,$height,'',LR,1,'C',1);

$pdf->SetX(100);
$pdf->Cell(20,$height,'',L,0,'L',1);
$pdf->Cell(180,$height,'',R,0,'L',1);		
$pdf->Cell(70,$height,'---------------',LR,0,'C',1);	
$pdf->Cell(70,$height,'---------------',LR,0,'C',1);		
$pdf->Cell(70,$height,'---------------',LR,1,'C',1);

$pdf->SetX(100);
$pdf->Cell(20,$height,'',BL,0,'L',1);
$pdf->Cell(180,$height,'',BR,0,'L',1);
$pdf->Cell(70,$height,'Manager',TBLR,0,'C',1);	
$pdf->Cell(70,$height,'KTU/Kasie',TBLR,0,'C',1);		
$pdf->Cell(70,$height,'Accountant',TBLR,1,'C',1);

$pdf->Output();
break;
default:
break;	
}
?>
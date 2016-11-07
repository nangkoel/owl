<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

$matrixid=$_POST['matrixid'];
$method=$_POST['method'];
$karyawanid=$_POST['karyawanid'];
$tanggal1=$_POST['tanggal1'];
$tanggal2=$_POST['tanggal2'];
$catatan=$_POST['catatan'];

if($method==''){
$method=$_GET['method'];
$karyawanid=$_GET['karyawanid'];    
}

$updateby="".$_SESSION['standard']['userid'];

function puter_tanggal($tanggal){
    $tgl=explode("-",$tanggal);
    return $tgl[2]."-".$tgl[1]."-".$tgl[0];
}

$sJabat="select distinct * from ".$dbname.".sdm_5jabatan where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusJabat[$rJabat['kodejabatan']]=$rJabat['namajabatan'];
}

$sJabat="select distinct * from ".$dbname.".datakaryawan where tipekaryawan = 0";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusNama[$rJabat['karyawanid']]=$rJabat['namakaryawan'];
    $kamusJabatan[$rJabat['karyawanid']]=$rJabat['kodejabatan'];
    $kamusLokasi[$rJabat['karyawanid']]=$rJabat['lokasitugas'];
    $kamusDept[$rJabat['karyawanid']]=$rJabat['bagian'];
    $kamusTMK[$rJabat['karyawanid']]=$rJabat['tanggalmasuk'];
}

$sJabat="select * from ".$dbname.".sdm_5matriktraining where matrixid = '".$matrixid."'";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $jabatan=$rJabat['kodejabatan'];
}

$sJabat="select * from ".$dbname.".sdm_matriktraining where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $key=$rJabat['karyawanid'].$rJabat['matrikxid'];
    $udahambiltraining[$key]='1';
    $catatatan[$key]=$rJabat['catatan'];
}

$sJabat="select * from ".$dbname.".sdm_5matriktraining where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusKategori[$rJabat['matrixid']]=$rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']]=$rJabat['topik'];
}

$sJabat="select * from ".$dbname.".log_5supplier where 1";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
    $kamusSup[$rJabat['supplierid']]=$rJabat['namasupplier'];
}

switch($method)
{
case 'centang':    
    $str="insert into ".$dbname.".sdm_matriktraining (karyawanid,matrikxid,tanggaltraining,sampaitanggal,updateby,catatan)
        values('".$karyawanid."','".$matrixid."','".puter_tanggal($tanggal1)."','".puter_tanggal($tanggal2)."','".$updateby."','".$catatan."')";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));    
        exit;
    }	
break;
case 'uncentang':
    $str="delete from ".$dbname.".sdm_matriktraining
    where karyawanid='".$karyawanid."' and matrikxid='".$matrixid."'";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal, ".addslashes(mysql_error($conn));
        exit;
    }
break;
case 'pilihkaryawan':
$str1="select * from ".$dbname.".datakaryawan where kodejabatan = '".$jabatan."' and tipekaryawan = 0 and (tanggalkeluar='0000-00-00' or tanggalkeluar is null or tanggalkeluar > ".$_SESSION['org']['period']['start'].") order by namakaryawan";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['remark']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody id=container>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{ 
    $key=$bar1->karyawanid.$matrixid;
    
    if($udahambiltraining[$key]=='1')$ceket=' checked'; else $ceket='';
    $no+=1;
    echo"<tr class=rowcontent>
        <td>".$bar1->namakaryawan."</td>
        <td><input type=text class=myinputtext id=remark".$bar1->karyawanid." onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30 value='".$catatatan[$key]."'></td>
        <td align=center>
            <input type=checkbox name=cekbok value=cekbok id=cek".$bar1->karyawanid." onchange=klikbok('".$bar1->karyawanid."') ".$ceket.">
        </td>
    </tr>";
}	 
echo"</tbody>
    <tfoot>
    </tfoot>
    </table>";
break;
case 'rilotlis':
$str1="select * from ".$dbname.".sdm_matriktraining where 1 order by karyawanid";
$res1=mysql_query($str1);
echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
     <thead>
     <tr class=rowheader>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['lokasitugas']."</td>
        <td>".$_SESSION['lang']['departemen']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td width=100>".$_SESSION['lang']['action']."</td>
     </tr></thead>
     <tbody>";
$no=0;
while($bar1=mysql_fetch_object($res1))
{ 
    $no+=1;
    echo"<tr class=rowcontent>
        <td>".$kamusNama[$bar1->karyawanid]."</td>
        <td>".$kamusLokasi[$bar1->karyawanid]."</td>
        <td>".$kamusDept[$bar1->karyawanid]."</td>
        <td>".$kamusJabat[$kamusJabatan[$bar1->karyawanid]]."</td>
        <td align=center>
            <button class=mybutton onclick=\"lihatpdf(event,'sdm_slave_matrixTraining.php','".$bar1->karyawanid."');\">".$_SESSION['lang']['pdf']."</button>
        </td>
    </tr>";
}	 
echo"</tbody>
    <tfoot>
    </tfoot>
    </table>";
break;
case 'pdf':
    //=================================================
    class PDF extends FPDF {
//        function Header() {
//            global $jabatan;
//            global $kriteria;
//            $this->SetFont('Arial','B',11);
//            $this->Cell(190,6,strtoupper($_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']),0,1,'C');
//            $this->Ln();
//            $this->SetFont('Arial','',10);
//            $this->Cell(60,6,$_SESSION['lang']['jabatan'],1,0,'C');
//            $this->Cell(30,6,$_SESSION['lang']['kriteria'],1,0,'C');	
//            $this->Cell(100,6,$_SESSION['lang']['deskripsi'],1,0,'C');	
//            $this->Ln();						
//        }
    }
    //================================
    $pdf=new PDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    
    $pdf->Cell(185,6,'DAFTAR TRAINING',0,1,'C');
    $pdf->Ln();
    $pdf->SetFont('Arial','',8);
    $str="select * from ".$dbname.".datakaryawan 
        where karyawanid = '".$karyawanid."'
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $pdf->Cell(50,6,$_SESSION['lang']['namakaryawan'],0,0,'L');                 $pdf->Cell(100,6,': '.$bar->namakaryawan,0,1,'L');
        $pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                      $pdf->Cell(100,6,': '.$kamusJabat[$bar->kodejabatan],0,1,'L');
        $pdf->Cell(50,6,$_SESSION['lang']['lokasitugas'],0,0,'L');                  $pdf->Cell(100,6,': '.$bar->lokasitugas,0,1,'L');
        $pdf->Cell(50,6,$_SESSION['lang']['tmk'],0,0,'L');                          $pdf->Cell(100,6,': '.puter_tanggal($bar->tanggalmasuk),0,1,'L');
        $pdf->Ln();
        
        $jabatanku=$bar->kodejabatan;
    }
    
    $pdf->Ln();
    $pdf->Cell(185,6,'Training yang sudah diikuti:',0,1,'L');
    $pdf->Cell(40,6,$_SESSION['lang']['kategori'],1,0,'C');
    $pdf->Cell(40,6,$_SESSION['lang']['topik'],1,0,'C');
    $pdf->Cell(30,6,$_SESSION['lang']['tanggalmulai'],1,0,'C');
    $pdf->Cell(30,6,$_SESSION['lang']['tanggalsampai'],1,0,'C');
    $pdf->Cell(50,6,$_SESSION['lang']['catatan'],1,0,'C');
    $pdf->Ln();
    $str="select * from ".$dbname.".sdm_matriktraining
        where karyawanid = '".$karyawanid."'
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $pdf->Cell(40,6,$kamusKategori[$bar->matrikxid],0,0,'L');
        $pdf->Cell(40,6,$kamusTopik[$bar->matrikxid],0,0,'L');
        $pdf->Cell(30,6,puter_tanggal($bar->tanggaltraining),0,0,'L');
        $pdf->Cell(30,6,puter_tanggal($bar->sampaitanggal),0,0,'L');
        $pdf->MultiCell(50,6,$bar->catatan,0,'L',false);
        $pdf->Ln();
    }
    
    $pdf->Ln();
    $pdf->Cell(185,6,'Training yang harusnya diikuti:',0,1,'L');
    $pdf->Cell(40,6,$_SESSION['lang']['jabatan'],1,0,'C');
    $pdf->Cell(40,6,$_SESSION['lang']['kategori'],1,0,'C');
    $pdf->Cell(40,6,$_SESSION['lang']['topik'],1,0,'C');
    $pdf->Ln();
    $str="select * from ".$dbname.".sdm_5matriktraining
        where kodejabatan = '".$jabatanku."'
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $pdf->Cell(40,6,$kamusJabat[$bar->kodejabatan],0,0,'L');
        $pdf->Cell(40,6,$bar->kategori,0,0,'L');
        $pdf->Cell(40,6,$bar->topik,0,0,'L');
        $pdf->Ln();
    }
    
    $pdf->Ln();
    $pdf->Cell(185,6,'Additional Training yang sudah diikuti:',0,1,'L');
    $pdf->Cell(70,6,$_SESSION['lang']['namatraining'],1,0,'C');
    $pdf->Cell(60,6,$_SESSION['lang']['penyelenggara'],1,0,'C');
    $pdf->Cell(30,6,$_SESSION['lang']['tanggalmulai'],1,0,'C');
    $pdf->Cell(30,6,$_SESSION['lang']['tanggalsampai'],1,0,'C');
    $pdf->Ln();
    $str="select * from ".$dbname.".sdm_5training
        where karyawanid = '".$karyawanid."' and sthrd = '1'
        ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $pdf->Cell(70,6,$bar->namatraining,0,0,'L');
        $pdf->Cell(60,6,$kamusSup[$bar->penyelenggara],0,0,'L');
        $pdf->Cell(30,6,puter_tanggal($bar->tglmulai),0,0,'L');
        $pdf->Cell(30,6,puter_tanggal($bar->tglselesai),0,0,'L');
        $pdf->Ln();
    }
 
    $pdf->Output();	
break;
default:
break;					
}




?>

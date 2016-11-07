<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
require_once('lib/fpdf.php'); 
 
    $kamar          =$_POST['kamar'];
    if($kamar==''){
        $kamar=$_GET['kamar'];
        $karyawanid=$_GET['karyawanid'];
        $kodetraining=$_GET['kodetraining'];
    }else{
    $karyawanid        =$_POST['karyawanid'];
    $tahunbudget        =$_POST['tahunbudget'];
    $listtahun          =$_POST['listtahun'];
    $kodetraining       =$_POST['kodetraining'];
    $namatraining       =$_POST['namatraining'];
    $levelpeserta       =$_POST['levelpeserta'];
    $levelpeserta       =$_POST['levelpeserta'];
    $penyelenggara      =$_POST['penyelenggara'];
    $hargaperpeserta    =$_POST['hargaperpeserta'];
    $tanggal1    =$_POST['tanggal1'];
    $tanggal2    =$_POST['tanggal2'];
    $persetujuan    =$_POST['persetujuan'];
    $hrd    =$_POST['hrd'];
    $deskripsitraining  =$_POST['deskripsitraining'];
    $hasildiharapkan    =$_POST['hasildiharapkan'];
    }
    
    function putertanggal($tanggal){
        $qwe=explode("-",$tanggal);
        return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
    }
    
    if($tanggal1!='')
        $tanggal1=putertanggal($tanggal1);
    if($tanggal2!='')
        $tanggal2=putertanggal($tanggal2);
	
//kamus host
$str="select * from ".$dbname.".log_5supplier where kodekelompok = 'S001' order by namasupplier";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $host[$bar->supplierid]=$bar->namasupplier;
}
//kamus jabatan
$str="select * from ".$dbname.".sdm_5jabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $jab[$bar->kodejabatan]=$bar->namajabatan;
}

//kamus nama
$str="select namakaryawan,karyawanid,kodejabatan,bagian from ".$dbname.".datakaryawan
      where tipekaryawan=0 order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nam[$bar->karyawanid]=$bar->namakaryawan;
    $jabjab[$bar->karyawanid]=$bar->kodejabatan;
    $depdep[$bar->karyawanid]=$bar->bagian;
}	  	

$stat[0]='';
$stat[1]=$_SESSION['lang']['disetujui'];
$stat[2]=$_SESSION['lang']['ditolak'];

//if($kamar=='tahun')
//{
//    $str="select distinct tahunbudget from ".$dbname.".sdm_5training order by tahunbudget desc";
//    $res=mysql_query($str);
//    $opttahun="<option value=''>".$_SESSION['lang']['all']."</option>";
//    while($bar=mysql_fetch_object($res))
//    {
//        $opttahun.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
//    }
//    echo $opttahun;
//}    

if($kamar=='list')
{
$str="select * from ".$dbname.".sdm_5training where karyawanid = '".$karyawanid."'
      ";
//echo $str;
$res=mysql_query($str);
$no=1;
while($bar=mysql_fetch_object($res))
{
    echo"<tr class=rowcontent>
    <td>".$no."</td>
    <td>".$bar->tahunbudget."</td>
    <td>".$bar->kode."</td>
    <td>".$bar->namatraining."</td>
    <td>".$jab[$bar->kodejabatan]."</td>
    <td>".$host[$bar->penyelenggara]."</td>

    <td align=right>".number_format($bar->hargasatuan,0,'.',',')."</td>
    <td align=center>".tanggalnormal($bar->tglmulai)."</td>
    <td align=center>".tanggalnormal($bar->tglselesai)."</td>
    <td>".$nam[$bar->persetujuan1]."</td>
    <td>".$stat[$bar->stpersetujuan1]."</td>
    <td>".$nam[$bar->persetujuanhrd]."</td>
    <td>".$stat[$bar->sthrd]."</td>
    <td>";
    if(($bar->stpersetujuan1==0)&&($bar->sthrd==0))
        echo"<img src=images/application/application_edit.png class=resicon  title='edit' onclick=\"edittraining('".$bar->tahunbudget."','".$bar->kode."','".$bar->namatraining."','".$bar->kodejabatan."','".$bar->penyelenggara."','".$bar->hargasatuan."','".tanggalnormal($bar->tglmulai)."','".tanggalnormal($bar->tglselesai)."','".$bar->persetujuan1."','".$bar->persetujuanhrd."','".str_replace("\n", "\\n",$bar->desctraining)."','".str_replace("\n", "\\n",$bar->output)."');\">";
    echo"</td>
    <td>";
    if(($bar->stpersetujuan1==0)&&($bar->sthrd==0))
        echo"<img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"deletetraining('".$bar->kode."');\">";
    echo"</td>
    <td>";
//    if(($bar->stpersetujuan1==0)&&($bar->sthrd==0))
        echo"<img class=resicon src=images/pdf.jpg title='PDF' onclick=\"lihatpdf(event,'sdm_slave_5rencanatraining.php','".$bar->kode."')\">";
    echo"</td>
    </tr>";	
    $no+=1;
}	  
    
}  

if($kamar=='save')
{
    $strx="insert into ".$dbname.".sdm_5training
        (kode,namatraining,penyelenggara,
        hargasatuan,desctraining,output,
        tahunbudget,tglmulai,tglselesai,karyawanid,
        persetujuan1,persetujuanhrd,kodejabatan)
	values('".$kodetraining."','".$namatraining."','".$penyelenggara."',
	'".$hargaperpeserta."','".$deskripsitraining."','".$hasildiharapkan."',
	'".$tahunbudget."','".$tanggal1."','".$tanggal2."','".$karyawanid."',
	'".$persetujuan."','".$hrd."','".$levelpeserta."')";
//    echo "error:".$strx;
  if(mysql_query($strx))
    {
    }	
  else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}

if($kamar=='delete')
{
$strx="delete from ".$dbname.".sdm_5training 
    where kode='".$kodetraining."' and karyawanid = '".$karyawanid."'";
if(mysql_query($strx))
{
	
} else {
    echo " Gagal,".addslashes(mysql_error($conn));
}
}

if($kamar=='edit')
{
    $strx="update ".$dbname.".sdm_5training set
        namatraining = '".$namatraining."',
        penyelenggara = '".$penyelenggara."',
        hargasatuan = '".$hargaperpeserta."',
        desctraining = '".$deskripsitraining."',
        output = '".$hasildiharapkan."',
        tahunbudget = '".$tahunbudget."',   
        tglmulai = '".$tanggal1."',
        tglselesai = '".$tanggal2."',
        persetujuan1 = '".$persetujuan."',
        persetujuanhrd = '".$hrd."',            
        kodejabatan = '".$levelpeserta."'
        where kode = '".$kodetraining."' and karyawanid = '".$karyawanid."'";
    if(mysql_query($strx))
    {
    } else {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
}

if($kamar=='pdf')
{
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
    
$str="select * from ".$dbname.".sdm_5training where karyawanid = '".$karyawanid."' and kode = '".$kodetraining."'
      ";
$res=mysql_query($str);
$no=1;
while($bar=mysql_fetch_object($res))
{
    $namatraining=$bar->namatraining;    
    $penyelenggara=$host[$bar->penyelenggara];   
    $tanggalmulai=$bar->tglmulai;
    $tanggalselesai=$bar->tglselesai;
    $hargaperpeserta=$bar->hargasatuan;
    $deskripsi=$bar->desctraining;
    $hasil=$bar->output;
    $atasan=$bar->persetujuan1;
    $atasanstatus=$bar->stpersetujuan1;
    $atasancatatan=$bar->catatan1;
    $hrd=$bar->persetujuanhrd;
    $hrdstatus=$bar->sthrd;
    $hrdcatatan=$bar->catatanhrd;
}
    $pdf->Cell(185,6,'FORM PENGAJUAN TRAINING',0,1,'C');
    $pdf->Ln();
    $pdf->Cell(50,6,$_SESSION['lang']['namakaryawan'],0,0,'L');                 $pdf->Cell(100,6,': '.$nam[$karyawanid],0,1,'L');
    $pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                      $pdf->Cell(100,6,': '.$jab[$jabjab[$karyawanid]],0,1,'L');
    $pdf->Cell(50,6,$_SESSION['lang']['departemen'],0,0,'L');                   $pdf->Cell(100,6,': '.$depdep[$karyawanid],0,1,'L');
    $pdf->Ln();
    $pdf->Cell(50,6,$_SESSION['lang']['namatraining'],0,0,'L');                 $pdf->Cell(100,6,': '.$namatraining,0,1,'L');
    $pdf->Cell(50,6,$_SESSION['lang']['penyelenggara'],0,0,'L');                $pdf->Cell(100,6,': '.$penyelenggara,0,1,'L');
    $pdf->Cell(50,6,$_SESSION['lang']['tanggalmulai'],0,0,'L');                 $pdf->Cell(100,6,': '.tanggalnormal($tanggalmulai),0,1,'L'); 
    $pdf->Cell(50,6,$_SESSION['lang']['tanggalsampai'],0,0,'L');                $pdf->Cell(100,6,': '.tanggalnormal($tanggalselesai),0,1,'L');
    $pdf->Cell(50,6,$_SESSION['lang']['hargaperpeserta'],0,0,'L');              $pdf->Cell(100,6,': '.number_format($hargaperpeserta),0,1,'L');
    $pdf->Ln();
    $pdf->Cell(50,6,$_SESSION['lang']['deskripsitraining'],0,0,'L');            $pdf->MultiCell(100,6,': '.$deskripsi,0,'L',false);
    $pdf->Ln();
    $pdf->Cell(50,6,$_SESSION['lang']['hasildiharapkan'],0,0,'L');              $pdf->MultiCell(100,6,': '.$hasil,0,'L',false);
    $pdf->Ln();
    $pdf->Cell(40,6,$_SESSION['lang']['persetujuan'],0,1,'L');
    $pdf->Cell(40,6,$_SESSION['lang']['namakaryawan'],1,0,'L');
        $pdf->Cell(50,6,$_SESSION['lang']['jabatan'],1,0,'L');
        $pdf->Cell(20,6,$_SESSION['lang']['status'],1,0,'L');
        $pdf->Cell(80,6,$_SESSION['lang']['catatan'],1,1,'L');
    $pdf->Cell(40,6,substr($nam[$atasan],0,30),0,0,'L');
        $pdf->Cell(50,6,substr($jab[$jabjab[$atasan]],0,30),0,0,'L');
        $pdf->Cell(20,6,$stat[$atasanstatus],0,0,'L');
        $pdf->MultiCell(80,6,$atasancatatan,0,'L',false);
    $pdf->Ln();
    $pdf->Cell(40,6,$nam[$hrd],0,0,'L');
        $pdf->Cell(50,6,$jab[$jabjab[$hrd]],0,0,'L');
        $pdf->Cell(20,6,$stat[$hrdstatus],0,0,'L');
        $pdf->MultiCell(80,6,$hrdcatatan,0,'L',false);
    $pdf->Ln();
    
//    $str1="select * from ".$dbname.". sdm_5kriteriapsy where kodejabatan like '%".$jabatan2."%' order by kodejabatan, kriteria";
//    $res1=mysql_query($str1);
//    while($bar1=mysql_fetch_object($res1))
//    {
//        $pdf->Cell(60,6,$kamusJabat[$bar1->kodejabatan],0,0,'L');
//        $pdf->Cell(30,6,$bar1->kriteria,0,0,'L');	
//        $pdf->MultiCell(100, 6, $bar1->penjelasan, 0, 'L', false);
//    }	 
    $pdf->Output();		
}

?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');

$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
$periode=$_GET['periode'];
$periode1=$_GET['periode1'];
$revisi=$_GET['revisi'];

//cek periode dan periode1
if($periode1<$periode)
{  #ditukar
    $z=$periode;
    $periode=$periode1;
    $periode1=$z;
}        

/*$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$periode."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
$fromstart='';
$fromend='';
while($bar=mysql_fetch_object($res))
{
    $fromstart=$bar->tanggalmulai;
    $fromend=$bar->tanggalsampai;
}
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$periode1."' and kodeorg='".$gudang."'";
$res=mysql_query($str);
$tostart='';
$toend='';
while($bar=mysql_fetch_object($res))
{
    $tostart=$bar->tanggalmulai;
    $toend=$bar->tanggalsampai;
}*/
if ($gudang==''){
	#penambahan jamhari
	#mengambil kodeorganisasi dengan induk=pt dan tipe holding agar mendapatkan tanggalmulai dan tanggalsampai
	#tanggal : 06-02-2015
	$whrHed="induk='".$pt."' and tipe='HOLDING'";
	$sHold="select kodeorganisasi from ".$dbname.".organisasi where ".$whrHed."";
	$qHold=mysql_query($sHold) or die(mysql_error($conn));
	$rHold=mysql_fetch_assoc($qHold);
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode."' and kodeorg='".$rHold['kodeorganisasi']."'";
    $res=mysql_query($str);
    $fromstart='';
    $fromend='';
    while($bar=mysql_fetch_object($res))
    {
        $fromstart=$bar->tanggalmulai;
        $fromend=$bar->tanggalsampai;
    }
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode1."' and kodeorg='".$rHold['kodeorganisasi']."'";
    $res=mysql_query($str);
    $tostart='';
    $toend='';
    while($bar=mysql_fetch_object($res))
    {
        $tostart=$bar->tanggalmulai;
        $toend=$bar->tanggalsampai;
    }
} else {
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode."' and kodeorg='".$gudang."'";
    $res=mysql_query($str);
    $fromstart='';
    $fromend='';
    while($bar=mysql_fetch_object($res))
    {
        $fromstart=$bar->tanggalmulai;
        $fromend=$bar->tanggalsampai;
    }
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
          periode='".$periode1."' and kodeorg='".$gudang."'";
    $res=mysql_query($str);
    $tostart='';
    $toend='';
    while($bar=mysql_fetch_object($res))
    {
        $tostart=$bar->tanggalmulai;
        $toend=$bar->tanggalsampai;
    }
}

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}

//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}

//ambil akun laba rugi tahun berjalan:
$CLM='';
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal where kodeaplikasi='CLM'";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res))
{
    $CLM=$bar->noakundebet;
}

//ambil semua noakun dari bulan lalu dan bulan ini
$lmperiode=mktime(0,0,0,substr($periode,5,2)-1,4,substr($periode,0,4));
$lmperiode=date('Y-m',$lmperiode);
if($_SESSION['language']=='ID'){
    $str="select distinct noakun,namaakun from ".$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
}
else{
    $str="select distinct noakun,namaakun1 as namaakun from ".$dbname.".keu_5akun where  noakun!='".$CLM."' order by noakun";
}
$res=mysql_query($str);
$TAB=Array();
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['noakun']=$bar->noakun;
    $TAB[$bar->noakun]['namaakun']=$bar->namaakun;
    $TAB[$bar->noakun]['sawal']=0;
    $TAB[$bar->noakun]['salak']=0;
}

//ambil saldo awal
if($gudang=='' and $pt!='')
{
    $where =" and kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
}
else if($gudang!='')
{
    $where =" and kodeorg ='".$gudang."'";
}
else
{
  $where='';  
}   
//$str="select sum(awal".substr(str_replace("-","",$periode),4,2).") as sawal,noakun from ".$dbname.".keu_saldobulanan 
//      where periode ='".str_replace("-","",$periode)."' ".$where." 
//      and noakun!='3110400' group by noakun order by noakun";
$str="select sum(awal".substr(str_replace("-","",$periode),4,2).") as sawal,noakun from ".$dbname.".keu_saldobulanan 
      where periode ='".str_replace("-","",$periode)."' and noakun!='".$CLM."'  ".$where." group by noakun order by noakun";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $TAB[$bar->noakun]['sawal']=$bar->sawal;
    $TAB[$bar->noakun]['salak']=$bar->sawal;
}

//ambil mutasi-----------------------
if($gudang=='' and $pt=='')
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."' 
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."' 
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan
}
else if($gudang=='' and $pt!='')
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi 
//        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."' and kodeorg in(select kodeorganisasi 
        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan
} 
else
{
//    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnalsum_vw
//        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'
//        and noakun!='".$CLM."' group by noakun"; #tidak sama dengan laba/rugi berjalan   
    $str="select sum(debet) as debet,sum(kredit) as kredit,noakun from ".$dbname.".keu_jurnaldt_vw
        where tanggal between '".$fromstart."' and '".$toend."' and kodeorg ='".$gudang."'
        and noakun!='".$CLM."' and revisi <= '".$revisi."' group by noakun"; #tidak sama dengan laba/rugi berjalan   
} 

//=================================================
    $res=mysql_query($str);
    while($bar= mysql_fetch_object($res))
    {
        $TAB[$bar->noakun]['debet']=$bar->debet;
        $TAB[$bar->noakun]['kredit']=$bar->kredit;
        $TAB[$bar->noakun]['salak']=$TAB[$bar->noakun]['sawal']+$bar->debet-$bar->kredit;
    } 

//=================================================
class PDF extends FPDF {
    function Header() {
        global $namapt;
        global $periode;
        global $gudang;
        $this->SetFont('Arial','B',9); 
	$this->Cell(20,3,$namapt,'',1,'L');
        $this->SetFont('Arial','B',12);
	$this->Cell(190,3,strtoupper($_SESSION['lang']['neracasaldo']),0,1,'C');
        $this->SetFont('Arial','',9);
        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
        $this->Cell(150,3,'UNIT:'.$gudang,'',0,'L');
        $this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$this->PageNo(),'',1,'L');
        $this->Cell(150,3,"Periode:".$periode,'',0,'L');
        $this->Cell(15,3,'User','',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        $this->SetFont('Arial','',7);
        $this->Cell(15,5,$_SESSION['lang']['nomor'],1,0,'C');
        $this->Cell(20,5,$_SESSION['lang']['noakun'],1,0,'C');	
        $this->Cell(70,5,$_SESSION['lang']['namaakun'],1,0,'C');	
        $this->Cell(20,5,$_SESSION['lang']['saldoawal'],1,0,'C');	
        $this->Cell(20,5,$_SESSION['lang']['debet'],1,0,'C');
        $this->Cell(20,5,$_SESSION['lang']['kredit'],1,0,'C');
        $this->Cell(20,5,$_SESSION['lang']['saldoakhir'],1,0,'C');
        $this->Ln();						
        $this->Ln();						
    }
}
//================================
$pdf=new PDF('P','mm','A4');
$pdf->AddPage();
$sal_awal=0;
$sal_debet=0;
$sal_kredit=0;
$sal_salak=0;    
foreach($TAB as $baris => $data)
{
 /*   if($data['sawal']==0 && $data['debet']==0 && $data['kredit']==0 && $data['salak']==0)
    {
        
    }
    else
    {*/    
    $no+=1;

    $pdf->Cell(15,5,$no,0,0,'C');
    $pdf->Cell(20,5,$data['noakun'],0,0,'L');
    $pdf->Cell(70,5,$data['namaakun'],0,0,'L');				
    $pdf->Cell(20,5,number_format($data['sawal'],2),0,0,'R');	
    $pdf->Cell(20,5,number_format($data['debet'],2),0,0,'R');
    $pdf->Cell(20,5,number_format($data['kredit'],2),0,0,'R');	
    $pdf->Cell(20,5,number_format($data['salak'],2),0,1,'R');	
  //  }           
    $sal_awal+=$data['sawal'];
    $sal_debet+=$data['debet'];
    $sal_kredit+=$data['kredit'];
    $sal_salak+=$data['salak'];
} 
$pdf->Cell(105,5,'T O T A L',0,0,'C');			
$pdf->Cell(20,5,number_format($sal_awal,2),0,0,'R');	
$pdf->Cell(20,5,number_format($sal_debet,2),0,0,'R');
$pdf->Cell(20,5,number_format($sal_kredit,2),0,0,'R');	
$pdf->Cell(20,5,number_format($sal_salak,2),0,1,'R');      
$pdf->Output();		
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['kdBatch']==''?$kdBatch=$_GET['kdBatch']:$kdBatch=$_POST['kdBatch'];
$optnmSup=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

switch($proses)
{
    case'preview':
        if($kdUnit=='')
        {
            exit("Error: Unit code required");
        }
    break;

    default:
    break;
}

$_POST['pt1']==''?$pt1=$_GET['pt1']:$pt1=$_POST['pt1'];
$_POST['kebun1']==''?$kebun1=$_GET['kebun1']:$kebun1=$_POST['kebun1'];
$_POST['tanggal1']==''?$tanggal1=$_GET['tanggal1']:$tanggal1=$_POST['tanggal1'];

if($kebun1=='all')$kebun1='Seluruhnya';
if($proses=='excel1'){
    $hider=$_SESSION['lang']['laporanStockBIbit']."<br>PT: ".$pt1."<br>".$_SESSION['lang']['kebun'].": ".$kebun1."<br>".$_SESSION['lang']['sampai'].": ".$tanggal1;
    $bgcoloz="bgcolor=#dedede";
    $boder=1;
}else{
    $hider="";
    $bgcoloz="";
    $boder=0;
}
if($kebun1=='Seluruhnya')$kebun1='';

$qwe=explode("-",$tanggal1);
$tanggal1=$qwe[2]."-".$qwe[1]."-".$qwe[0];

function getMonths($start, $end) {
    $startqwe=explode("-",$start);
    $endqwe=explode("-",$end);

    $startMonth = $startqwe[1];
    $startYear = $startqwe[0];

    $endMonth = $endqwe[1];
    $endYear = $endqwe[0];

    return ($endYear - $startYear) * 12 + ($endMonth - $startMonth);
}   

$stab=$hider;
$stab.="<table cellpadding=1 cellspacing=1 border=".$boder." class=sortable>
<thead>
<tr class=rowheader>
    <td align=center ".$bgcoloz.">Batch</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['tgltanam']."</td>   
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['jenisbibit']."</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['diterima']."</td>
    <td align=center ".$bgcoloz.">Seleksi Awal(".$_SESSION['lang']['afkirbibit'].")</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['ditanam']."</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['afkirbibit']." PN</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['pindahpnmn']."</td>
    <td align=center ".$bgcoloz.">PN".$_SESSION['lang']['stock']."</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['afkirbibit']." MN</td>
    <td align=center ".$bgcoloz.">Total ".$_SESSION['lang']['afkirbibit']."</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['afkirbibit']."(%)</td>
    <td align=center ".$bgcoloz.">Doubletone</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['pengiriman']."</td>
    <td align=center ".$bgcoloz.">MN".$_SESSION['lang']['stock']."</td>
    <td align=center ".$bgcoloz.">Grand Total</td>
    <td align=center ".$bgcoloz.">".$_SESSION['lang']['umur']."</td>
</tr>
</thead><tbody>";
$stab.="<tr>
    <td align=center ".$bgcoloz.">a</td>
    <td align=center ".$bgcoloz."></td>   
    <td align=center ".$bgcoloz."></td>
    <td align=center ".$bgcoloz.">b</td>
    <td align=center ".$bgcoloz.">c</td>
    <td align=center ".$bgcoloz.">d=b-c</td>
    <td align=center ".$bgcoloz.">e</td>
    <td align=center ".$bgcoloz.">f</td>
    <td align=center ".$bgcoloz.">g=d-e-f</td>
    <td align=center ".$bgcoloz.">h</td>
    <td align=center ".$bgcoloz.">i=e+h</td>
    <td align=center ".$bgcoloz.">j=i/d*100</td>
    <td align=center ".$bgcoloz.">k</td>
    <td align=center ".$bgcoloz.">l</td>
    <td align=center ".$bgcoloz.">m=f-h+k-l</td>
    <td align=center ".$bgcoloz.">n=g+m</td>
    <td align=center ".$bgcoloz.">o</td>
</tr>";

$sData="select distinct b.kodeorg, a.*,
    COALESCE(ROUND(DATEDIFF('".$tanggal1."',a.tanggaltanam)/365.25,2),0)*12 as umurbulan
    from ".$dbname.".bibitan_batch a
    left join ".$dbname.".bibitan_mutasi b on a.batch = b.batch
    where substr(b.kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk = '".$pt1."') 
        and substr(b.kodeorg,1,4) like '".$kebun1."%' and a.tanggal<='".$tanggal1."'
    ";

$qData=mysql_query($sData) or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $batches[$rData['batch']]=$rData['batch'];
    $dzArr[$rData['batch']]['batch']=$rData['batch'];
    $dzArr[$rData['batch']]['tanggaltanam']=$rData['tanggaltanam'];
    $dzArr[$rData['batch']]['jenisbibit']=$rData['jenisbibit'];
    //$dzArr[$rData['batch']]['kecambahterima']=$rData['jumlahterima']+$rData['jumlahafkir'];
    $dzArr[$rData['batch']]['kecambahterima']=$rData['jumlahterima'];
    $dzArr[$rData['batch']]['seleksiawal']=$rData['jumlahafkir'];
//    $dzArr[$rData['batch']]['kecambahtanam']=$rData['jumlahterima'];
    $dzArr[$rData['batch']]['umurbibit']=$rData['umurbulan'];
//    $dzArr[$rData['batch']]['umurbibit']=getMonths($rData['tanggaltanam'],$tanggal1);
}

$sData="select * from ".$dbname.".bibitan_mutasi 
    where substr(kodeorg,1,4) in (select kodeorganisasi from ".$dbname.".organisasi where induk = '".$pt1."') 
        and substr(kodeorg,1,4) like '".$kebun1."%' and tanggal<='".$tanggal1."' and post='1'
    ";
//echo$sData;
$qData=mysql_query($sData) or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{
    $batches[$rData['batch']]=$rData['batch'];
    $dzArr[$rData['batch']]['batch']=$rData['batch'];
    if($rData['kodetransaksi']=='TMB'){
            $dzArr[$rData['batch']]['kecambahtanam']+=$rData['jumlah'];    
    }
    if($rData['kodetransaksi']=='TPB'){
        if(substr($rData['kodeorg'],6,2)=='PN')
            $dzArr[$rData['batch']]['kecambahtanam']+=$rData['jumlah'];    
    }
    if($rData['kodetransaksi']=='AFB'){
        if(substr($rData['kodeorg'],6,2)=='PN')
            $dzArr[$rData['batch']]['seleksibibitpn']+=$rData['jumlah'];    
        if(substr($rData['kodeorg'],6,2)=='MN')
            $dzArr[$rData['batch']]['seleksibibitmn']+=$rData['jumlah'];    
    }
//    if($rData['kodetransaksi']=='TPB'){
//        if(substr($rData['kodeorg'],6,2)=='PN')
//            $dzArr[$rData['batch']]['pindahbibitpnmn']+=$rData['jumlah'];    
//    }

    if($rData['kodetransaksi']=='TMB'){
        if(substr($rData['kodeorg'],6,2)=='MN')
            $dzArr[$rData['batch']]['pindahbibitpnmn']+=$rData['jumlah']*-1;    
    }
    if($rData['kodetransaksi']=='DBT'){
        $dzArr[$rData['batch']]['bibitdoubletone']+=$rData['jumlah'];    
    }
    if($rData['kodetransaksi']=='PNB'){
        $dzArr[$rData['batch']]['kirimbibit']+=$rData['jumlah'];    
    }
}
//echo"<pre>";
//print_r($dzArr);
//echo"</pre>";
if(!empty($batches))foreach($batches as $bat){
    $stab.="<tr class=rowcontent>";
    $stab.="<td align=center>".$dzArr[$bat]['batch']."</td>";
    $stab.="<td align=center>".$dzArr[$bat]['tanggaltanam']."</td>";
    $stab.="<td align=left>".$dzArr[$bat]['jenisbibit']."</td>";
    $stab.="<td align=right>".number_format($dzArr[$bat]['kecambahterima'])."</td>";//b
    $stab.="<td align=right>".number_format($dzArr[$bat]['seleksiawal'])."</td>";//c
    $stab.="<td align=right>".number_format($dzArr[$bat]['kecambahtanam'])."</td>";//d=b-c
    $stab.="<td align=right>".number_format($dzArr[$bat]['seleksibibitpn']*-1)."</td>";//e
    $stab.="<td align=right>".number_format($dzArr[$bat]['pindahbibitpnmn']*-1)."</td>";//f
    $saldobibitpn=$dzArr[$bat]['kecambahtanam']+$dzArr[$bat]['seleksibibitpn']+$dzArr[$bat]['pindahbibitpnmn'];
    $stab.="<td align=right>".number_format($saldobibitpn)."</td>";//g=d-e-f
    $stab.="<td align=right>".number_format($dzArr[$bat]['seleksibibitmn']*-1)."</td>";//h
    $totalseleksi=$dzArr[$bat]['seleksibibitpn']+$dzArr[$bat]['seleksibibitmn'];
    $stab.="<td align=right>".number_format($totalseleksi*-1)."</td>";//i=e+h
    @$persenseleksi=$totalseleksi*-1/$dzArr[$bat]['kecambahtanam']*100;
    $stab.="<td align=right>".number_format($persenseleksi,2)."</td>";//j=i/d*100
    $stab.="<td align=right>".number_format($dzArr[$bat]['bibitdoubletone'])."</td>";//k
    $stab.="<td align=right>".number_format($dzArr[$bat]['kirimbibit']*-1)."</td>";//l
    $saldobibitmn=($dzArr[$bat]['pindahbibitpnmn']*-1)+$dzArr[$bat]['seleksibibitmn']+$dzArr[$bat]['bibitdoubletone']+$dzArr[$bat]['kirimbibit'];
    $stab.="<td align=right>".number_format($saldobibitmn)."</td>";//m=f-h+k-l
    $saldobibit=$saldobibitpn+$saldobibitmn;
    $stab.="<td align=right>".number_format($saldobibit)."</td>";//n=g+m
    $stab.="<td align=right>".number_format($dzArr[$bat]['umurbibit'],2)."</td>";
    $stab.="</tr>";

    $dzTot['kecambahterima']+=$dzArr[$bat]['kecambahterima'];
    $dzTot['seleksiawal']+=$dzArr[$bat]['seleksiawal'];
    $dzTot['kecambahtanam']+=$dzArr[$bat]['kecambahtanam'];
    $dzTot['seleksibibitpn']+=$dzArr[$bat]['seleksibibitpn'];
    $dzTot['pindahbibitpnmn']+=$dzArr[$bat]['pindahbibitpnmn'];
    $dzTot['saldobibitpn']+=$saldobibitpn;
    $dzTot['seleksibibitmn']+=$dzArr[$bat]['seleksibibitmn'];
    $dzTot['totalseleksi']+=$totalseleksi;
    $dzTot['bibitdoubletone']+=$dzArr[$bat]['bibitdoubletone'];
    $dzTot['kirimbibit']+=$dzArr[$bat]['kirimbibit'];
    $dzTot['saldobibitmn']+=$saldobibitmn;
    $dzTot['saldobibit']+=$saldobibit;
}
$stab.="<tr class=rowcontent>";
$stab.="<td align=center colspan=3 ".$bgcoloz.">Total</td>";
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['kecambahterima'])."</td>";//b
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['seleksiawal'])."</td>";//c
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['kecambahtanam'])."</td>";//d=b-c
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['seleksibibitpn']*-1)."</td>";//e
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['pindahbibitpnmn']*-1)."</td>";//f
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['saldobibitpn'])."</td>";//g=d-e-f
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['seleksibibitmn']*-1)."</td>";//h
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['totalseleksi']*-1)."</td>";//i=e+h
@$persenseleksiTot=$dzTot['totalseleksi']*-1/$dzTot['kecambahtanam']*100;
$stab.="<td align=right ".$bgcoloz.">".number_format($persenseleksiTot,2)."</td>";//j=i/d*100
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['bibitdoubletone'])."</td>";//k
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['kirimbibit']*-1)."</td>";//l
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['saldobibitmn'])."</td>";//m=f-h+k-l
$stab.="<td align=right ".$bgcoloz.">".number_format($dzTot['saldobibit'])."</td>";//n=g+m
$stab.="<td ".$bgcoloz."></td>";
$stab.="</tr>";

$stab.="</tbody></table>";

switch($proses)
{
    case'getkebun':
        $optkebun1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
        $optkebun1.="<option value='all'>".$_SESSION['lang']['all']."</option>";
        $sData="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
            where induk = '".$pt1."' and tipe = 'kebun'
            order by namaorganisasi";
        $qData=mysql_query($sData) or die(mysql_error());
        while($rData=mysql_fetch_assoc($qData))
        {
            $optkebun1.="<option value=".$rData['kodeorganisasi'].">".$rData['namaorganisasi']."</option>";
        }
        echo $optkebun1;
    break;    
    case'preview1':
        echo $stab;
    break;
    case'excel1':
        $stab.="Print Time:".date('Y-m-d H:i:s')." By:".$_SESSION['empl']['name'];	
        $nop_="RekapStokBibit_".$pt1."_".$kebun1."_sd_".$tanggal1;
        if(strlen($stab)>0)
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
            if(!fwrite($handle,$stab))
            {
                echo "<script language=javascript1.2>
                parent.window.alert('Can not convert to excel format');
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
        case'preview':
          $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['saldo']."</td>
            <td>".$_SESSION['lang']['jenisbibit']."</td>
             <td>".$_SESSION['lang']['tgltanam']."</td>   
            <td>".$_SESSION['lang']['umur']." ".substr($_SESSION['lang']['afkirbibit'],5)."</td>
            </tr>
            </thead><tbody id=containDataStock>";
            if($kdUnit!='')
            {
                $where="  kodeorg like '%".$kdUnit."%'";
            }
            if($kdBatch!='')
            {
                $where.=" and batch='".$kdBatch."'";
            }
                $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where ".$where." group by batch,kodeorg order by tanggal desc ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=time();//tanggal pembuatan dokumen
                    /*
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    */

                    $jmlHari=($endtime-$starttime)/(60*60*24*30);

                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
                    $tab.="<td>".$rDataBatch['jenisbibit']."</td>";
                    $tab.="<td>".tanggalnormal($rDataBatch['tanggaltanam'])."</td>";
                    $tab.="<td align=right>".number_format($jmlHari,2)."</td>";
                    $tab.="</tr>";
                    $total+=$rData['jumlah'];
                }
                $tab.="<tr class=rowcontent><td colspan=3>".$_SESSION['lang']['total']."</td>";
                $tab.="<td align=right>".number_format($total)."</td><td colspan=3></td></tr>";
                $tab.="</tbody></table>";
                echo $tab;
        break;
        case'pdf':

         class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
                global $kdUnit;
                global $kdBatch;
                global $rData;
                global $optNm;

                            # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);

                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 15;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();

                $this->SetFont('Arial','B',12);
                        //	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanKendAb'],'',0,'L');
                        //	$this->Ln();
                                $this->SetFont('Arial','',8);

                                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
                                        $this->Cell(5,$height,':','',0,'L');
                                        $this->Cell(45/100*$width,$height,$optNm[$kdUnit],'',0,'L');
                                        $this->Ln();
                                        if($kdBatch=='')
                                        {
                                            $kdBatchdt=$_SESSION['lang']['all'];
                                        }
                                        else
                                        {
                                            $kdBatchdt=$kdBatch;
                                        }
                                        $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['batch'],'',0,'L');
                                        $this->Cell(5,$height,':','',0,'L');
                                        $this->Cell(45/100*$width,$height,$kdBatchdt,'',0,'L');
                                        $this->Ln();					



                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height, $_SESSION['lang']['laporanStockBIbit'],0,1,'C');	
                $this->Ln();	

                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['batch'],1,0,'C',1);	
                $this->Cell(17/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);		
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['saldo'],1,0,'C',1);		
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['jenisbibit'],1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tgltanam']." ".substr($_SESSION['lang']['afkirbibit'],5),1,0,'C',1);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['umur'],1,1,'C',1);	

            }

            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
                $pdf->AddPage();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',7);
                if($kdBatch!='')
                {
                    $where=" and batch='".$kdBatch."'";
                }
                $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where." group by batch,kodeorg order by tanggal desc ";
              // exit("error".$sData);

                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    $no+=1;
                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);		
                        $pdf->Cell(8/100*$width,$height,$rData['batch'],1,0,'C',1);		
                        $pdf->Cell(17/100*$width,$height,$optNm[$rData['kodeorg']],1,0,'C',1);		
                        $pdf->Cell(8/100*$width,$height,number_format($rData['jumlah'],0),1,0,'R',1);
                        $pdf->Cell(11/100*$width,$height,$rDataBatch['jenisbibit'],1,0,'C',1);
                        $pdf->Cell(8/100*$width,$height,tanggalnormal($rDataBatch['tanggaltanam']),1,0,'C',1);	
                        $pdf->Cell(8/100*$width,$height,$jmlHari,1,1,'C',1);
                        $total+=$rData['jumlah'];
                }
                $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
                $pdf->Cell(8/100*$width,$height,number_format($total),1,0,'R',1);
                $pdf->Cell(27/100*$width,$height,"",1,1,'R',1);

        $pdf->Output();
        break;
        case'excel':
           $tab.="
            <table>
            <tr><td colspan=7 align=center>".$_SESSION['lang']['laporanStockBIbit']."</td></tr>
            ".$tbl."
            <tr><td colspan=7></td><td></td></tr>
            </table>
            <table cellpadding=1 cellspacing=1 border=1 class=sortable>
            <thead>
            <tr class=rowheader>
            <td bgcolor=#DEDEDE align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['batch']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldo']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jenisbibit']."</td>
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tgltanam']."</td>   
            <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['umur']." ".substr($_SESSION['lang']['afkirbibit'],5)."</td>
            </tr>
            </thead><tbody id=containDataStock>";
            if($kdBatch!='')
            {
                $where=" and batch='".$kdBatch."'";
            }
                $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where." group by batch,kodeorg order by tanggal desc ";
               // exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=mysql_fetch_assoc($qData))
                {
                    $data='';
                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen
                    $timediffSecond = abs($endtime-$starttime);
                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
                    $jmlHari=date("j", $diff) - 1;
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$no."</td>";
                    $tab.="<td>".$rData['batch']."</td>";
                    $tab.="<td>".$optNm[$rData['kodeorg']]."</td>";
                    $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
                    $tab.="<td>".$rDataBatch['jenisbibit']."</td>";
                    $tab.="<td>".$rDataBatch['tanggaltanam']."</td>";
                    $tab.="<td align=right>".$jmlHari."</td>";
                    $tab.="</tr>";
                    $total+=$rData['jumlah'];
                }
                 $tab.="<tr class=rowcontent><td colspan=3>".$_SESSION['lang']['total']."</td>";
                $tab.="<td align=right>".number_format($total)."</td><td colspan=3></td></tr>";
                $tab.="</tbody></table>";



                        //echo "warning:".$strx;
                        //=================================================
                $tab.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                        $nop_="laporanStock_".$kdUnit;
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

        default:
        break;
}

?>
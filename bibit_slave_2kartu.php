<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_POST['kodeunit']==''?$kodeunit=$_GET['kodeunit']:$kodeunit=$_POST['kodeunit'];
$_POST['kodebatch']==''?$kodebatch=$_GET['kodebatch']:$kodebatch=$_POST['kodebatch'];
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];

//$optnmSup=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
//$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

if($kodeunit=='')
{
    exit("Error: Unit code required.".$kodeunit);
}

$where='';
if($kodeunit!='')
    $where=" b.kodeorg like '%".$kodeunit."%'";
if($kodebatch!='')
    $where.=" and a.batch='".$kodebatch."'";

$adadata=false;
//        $str="select batch from ".$dbname.".bibitan_batch_vw
//            where ".$where;
$str="select distinct a.batch from ".$dbname.".bibitan_batch a
    left join ".$dbname.".bibitan_mutasi b on a.batch=b.batch
    where ".$where."
    order by a.batch desc";

if($proses=='excel'){
    $border=1;
    $bg=" bgcolor='#dedede'";
}else{
    $border=0;
    $bg=" ";
}

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $adadata=true;
    $tab.=$_SESSION['lang']['batch']." : ".$bar->batch."<br>";

    if($_SESSION['language']=='EN'){
           $tab.="A. SEED SELECTION and REJECTION"."<br>";    
    }else{
        $tab.="A. SELEKSI KECAMBAH"."<br>";   
    }
    $tab.="<table cellpadding=1 cellspacing=1 border=".$border." class=sortable>
    <thead>
        <tr class=rowheader ".$bg.">
        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['diterima']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['diterima']."</td>
        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>
        </tr><tr class=rowheader ".$bg.">    
        <td align=center>".$_SESSION['lang']['jumlah']."</td>   
        <td align=center>%</td>
        </tr>
    </thead><tbody id=containdata>";

    $no=0;
    $sData="select * 
        from ".$dbname.".bibitan_batch_vw where kodeorg like '%".$kodeunit."%' and batch = '".$bar->batch."' 
        ";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $no+=1;
        @$persen=100*$rData['jumlahafkir']/$rData['jumlahterima'];
        $ditanam=$rData['jumlahterima']-$rData['jumlahafkir'];
        $tab.="<tr class=rowcontent>";
        if($proses=='excel')$tampiltanggal=$rData['tanggal']; else $tampiltanggal=tanggalnormal($rData['tanggal']);
        $tab.="<td align=center>".$tampiltanggal."</td>";
        $tab.="<td align=right>".number_format($rData['jumlahterima'])."</td>";
        $tab.="<td align=right>".number_format($rData['jumlahafkir'])."</td>";
        $tab.="<td>".number_format($persen,2)."</td>";
        $tab.="<td align=right>".number_format($ditanam)."</td>";
        $tab.="</tr>";
        $terimaDt+=$rData['jumlahterima'];
        $afkirDt+=$rData['jumlahafkir'];
        $dataa[$rData['tanggal']]['tanam']+=$ditanam;
    }
    if($no==0) {$tab.="<tr class=rowcontent><td colspan=5>No data.</td></tr>";}
    $tab.="<tr class=rowcontent><td>".$_SESSION['lang']['total']."</td>";
    $tab.="<td align=right>".number_format($terimaDt)."</td>";
    $tab.="<td align=right>".number_format(abs($afkirDt))."</td>";
    $tab.="<td colspan=2></td></tr>";
    $tab.="</tbody></table></br>";            

    $tab.="B. PRE NURSERY"."<br>";    
    $datab=array();
    $tab.="<table cellpadding=1 cellspacing=1 border=".$border." class=sortable>
    <thead>
        <tr class=rowheader ".$bg.">
        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['transplatingbibit']."</td>
        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['saldo']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>
        </tr><tr class=rowheader ".$bg.">    
        <td align=center>".$_SESSION['lang']['jumlah']."</td>   
        <td align=center>%</td>
        </tr>
    </thead><tbody id=containdata>";

    $sData="select * 
        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodeorg like '%PN%'  and post=1
        order by tanggal asc";
    //echo $sData;
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $datab[$rData['tanggal']]['tanggal']=$rData['tanggal'];
        if($rData['kodetransaksi']=='TPB')
            $datab[$rData['tanggal']]['TPB']+=$rData['jumlah'];
        else if($rData['kodetransaksi']=='AFB')
            $datab[$rData['tanggal']]['AFB']+=$rData['jumlah'];
        else $datab[$rData['tanggal']]['TMB']+=$rData['jumlah'];
    }

    $no=0;
    if(!empty($datab)) 
        foreach($datab as $data) {
            $no+=1;
            @$persen=100*$data['AFB']/$data['TMB'];
            $saldo+=$data['TMB']+$data['TPB']+$data['AFB'];
            $tab.="<tr class=rowcontent>";
            if($proses=='excel')$tampiltanggal=$data['tanggal']; else $tampiltanggal=tanggalnormal($data['tanggal']);
            $tab.="<td align=center>".$tampiltanggal."</td>";
            $tab.="<td align=right>".number_format($data['TMB'])."</td>";
            $tab.="<td align=right>".number_format($data['TPB'])."</td>";
            $tab.="<td align=right>".number_format($data['AFB'])."</td>";
            $tab.="<td>".number_format($persen,2)."</td>";
            $tab.="<td align=right>".number_format($saldo)."</td>";
            $tab.="<td></td>";
            $tab.="</tr>";
            $dtmb+=$data['TMB'];
            $dtpb+=$data['PNB'];
            $afbd+=$data['AFB'];
        } else {
            $tab.="<tr class=rowcontent><td colspan=7>No data.</td></tr>";
        }
    $tab.="<tr class=rowcontent><td >".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".number_format($dtmb)."</td>";
        $tab.="<td align=right>".number_format(abs($dtpb))."</td>";
        $tab.="<td align=right>".number_format(abs($afbd))."</td><td colspan=3></td></tr>";
    $tab.="</tbody></table></br>";   

    $tab.="C. MAIN NURSERY"."<br>";    
    $datac=array();
    $tab.="<table cellpadding=1 cellspacing=1 border=".$border." class=sortable>
    <thead>
        <tr class=rowheader ".$bg.">
        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['kodeorg']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['ditanam']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['pengiriman']."</td>
        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['saldo']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['almt_kirim']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>
        </tr><tr class=rowheader ".$bg.">    
        <td align=center>".$_SESSION['lang']['jumlah']."</td>   
        <td align=center>%</td>
        </tr>
    </thead><tbody id=containdata>";

    $sData="select * 
        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodeorg like '%MN%' and post=1
        order by tanggal asc";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $datac[$rData['tanggal']]['tanggal']=$rData['tanggal'];
        if($rData['kodetransaksi']=='TPB')
            $datac[$rData['tanggal']]['TPB']+=$rData['jumlah'];
        else if($rData['kodetransaksi']=='AFB')
            $datac[$rData['tanggal']]['AFB']+=$rData['jumlah'];
        else if($rData['kodetransaksi']=='PNB')
            $datac[$rData['tanggal']]['PNB']+=$rData['jumlah'];
        else $datac[$rData['tanggal']]['TMB']+=$rData['jumlah'];
        $datac[$rData['tanggal']]['lokasi'].=' '.$rData['lokasipengiriman'];
        $datac[$rData['tanggal']]['kodeorg']=$rData['kodeorg'];
    }

    $no=0;
    if(!empty($datac)) 
        foreach($datac as $data) {
            $no+=1;
           
            $saldo+=$data['TMB']+$data['TPB']+$data['AFB']+$data['PNB'];
            @$persen=($data['AFB']/$saldo)*100;
            $tab.="<tr class=rowcontent>";
            if($proses=='excel')$tampiltanggal=$data['tanggal']; else $tampiltanggal=tanggalnormal($data['tanggal']);
            $tab.="<td align=center>".$tampiltanggal."</td>";
            $tab.="<td align=left>".$data['kodeorg']."</td>";
            $tab.="<td align=right>".number_format(abs($data['TMB']))."</td>";
            $tab.="<td align=right>".number_format(abs($data['PNB']))."</td>";
            $tab.="<td align=right>".number_format(abs($data['AFB']))."</td>";
            $tab.="<td>".number_format($persen,2)."</td>";
            $tab.="<td align=right>".number_format($saldo)."</td>";
            $tab.="<td>".$data['lokasi']."</td>";
            $tab.="<td></td>";
            $tab.="</tr>";
            $dtnm+=$data['TMB'];
            $dkirim+=$data['PNB'];
            $dafb+=$data['AFB'];
        } else {
            $tab.="<tr class=rowcontent><td colspan=8>No data.</td></tr>";
        }
        $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>";
        $tab.="<td align=right>".number_format($dtnm)."</td>";
        $tab.="<td align=right>".number_format(abs($dkirim))."</td>";
        $tab.="<td align=right>".number_format(abs($dafb))."</td><td colspan=4></td></tr>";
    $tab.="</tbody></table></br>";   

    
        if($_SESSION['language']=='EN'){
               $tab.="D. REJECTION RECAP"."<br>";      
    }else{
            $tab.="D. REKAP SELEKSI BIBIT"."<br>";    
    }
    

    $datad=array();
    $tab.="<table cellpadding=1 cellspacing=1 border=".$border." class=sortable>
    <thead>
        <tr class=rowheader ".$bg.">
        <td rowspan=2 align=center>".$_SESSION['lang']['tanggal']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['blok']."</td>
        <td colspan=2 align=center>".$_SESSION['lang']['afkirbibit']."</td>
        <td rowspan=2 align=center>".$_SESSION['lang']['catatan']."</td>
        </tr><tr class=rowheader ".$bg.">    
        <td align=center>".$_SESSION['lang']['jumlah']."</td>   
        <td align=center>%</td>
        </tr>
    </thead><tbody id=containdata>";

    $sData="select * 
        from ".$dbname.".bibitan_mutasi where batch = '".$bar->batch."' and kodetransaksi = 'AFB'  and post=1
        order by tanggal desc";
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $datad[$rData['tanggal']]['tanggal']=$rData['tanggal'];
        if($rData['kodetransaksi']=='TPB')
            $datad[$rData['tanggal']]['TPB']+=$rData['jumlah'];
        else if($rData['kodetransaksi']=='AFB')
            $datad[$rData['tanggal']]['AFB']+=$rData['jumlah'];
        else $datad[$rData['tanggal']]['TMB']+=$rData['jumlah'];
        $datad[$rData['tanggal']]['blok'].=' '.$rData['kodeorg'];
        $datad[$rData['tanggal']]['ket'].=' '.$rData['keterangan'];
    }

    $no=0;
    if(!empty($datad)) 
        foreach($datad as $data) {
            $no+=1;
            $saldo+=$data['TMB']+$data['TPB']+$data['AFB']+$data['PNB'];
            @$persen=($data['AFB']/$saldo)*100;
            $tab.="<tr class=rowcontent>";
            if($proses=='excel')$tampiltanggal=$data['tanggal']; else $tampiltanggal=tanggalnormal($data['tanggal']);
            $tab.="<td align=center>".$tampiltanggal."</td>";
            $tab.="<td align=right>".$data['blok']."</td>";
            $tab.="<td align=right>".number_format(abs($data['AFB']))."</td>";
            $tab.="<td>".number_format($persen,2)."</td>";
            $tab.="<td>".$data['ket']."</td>";
            $tab.="</tr>";
            $afdData+=$data['AFB'];
        } else {
            $tab.="<tr class=rowcontent><td colspan=5>No data.</td></tr>";
        }
    $tab.="<tr class=rowcontent><td colspan=2>".$_SESSION['lang']['total']."</td>";
    $tab.="<td>".number_format(abs($afdData))."</td><td colspan=2></td></tr>";

    $tab.="</tbody></table><br>";               
}
if(!$adadata)$tab='No data.';

 
switch($proses)
{
    case'preview':        
        echo $tab;
    break;
    case'excel':
        $tab.="<br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

        $nop_="kartubibit_".$kodeunit.".".$kodebatch;
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
        
        
//	case'pdf':
//	
//	 class PDF extends FPDF
//        {
//            function Header() {
//                global $conn;
//                global $dbname;
//                global $align;
//                global $length;
//                global $colArr;
//                global $title;
//                global $kdUnit;
//                global $kdBatch;
//                global $rData;
//                global $optNm;
//				
//			    # Alamat & No Telp
//                $query = selectQuery($dbname,'organisasi','alamat,telepon',
//                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
//                $orgData = fetchData($query);
//                
//                $width = $this->w - $this->lMargin - $this->rMargin;
//                $height = 15;
//                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
//                $this->Image($path,$this->lMargin,$this->tMargin,70);	
//                $this->SetFont('Arial','B',9);
//                $this->SetFillColor(255,255,255);	
//                $this->SetX(100);   
//                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
//                $this->SetX(100); 		
//                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
//                $this->SetX(100); 			
//                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
//                $this->Line($this->lMargin,$this->tMargin+($height*4),
//                    $this->lMargin+$width,$this->tMargin+($height*4));
//                $this->Ln();
//                
//                $this->SetFont('Arial','B',12);
//			//	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanKendAb'],'',0,'L');
//			//	$this->Ln();
//				$this->SetFont('Arial','',8);
//					
//					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
//					$this->Cell(5,$height,':','',0,'L');
//					$this->Cell(45/100*$width,$height,$optNm[$kdUnit],'',0,'L');
//					$this->Ln();
//                                        if($kdBatch=='')
//                                        {
//                                            $kdBatchdt=$_SESSION['lang']['all'];
//                                        }
//                                        else
//                                        {
//                                            $kdBatchdt=$kdBatch;
//                                        }
//					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['batch'],'',0,'L');
//					$this->Cell(5,$height,':','',0,'L');
//					$this->Cell(45/100*$width,$height,$kdBatchdt,'',0,'L');
//					$this->Ln();					
//			
//				
//			
//                $this->SetFont('Arial','U',12);
//                $this->Cell($width,$height, $_SESSION['lang']['laporanStockBIbit'],0,1,'C');	
//                $this->Ln();	
//				
//                $this->SetFont('Arial','B',7);	
//                $this->SetFillColor(220,220,220);
//                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
//                $this->Cell(8/100*$width,$height,$_SESSION['lang']['batch'],1,0,'C',1);	
//                $this->Cell(17/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);		
//                $this->Cell(8/100*$width,$height,$_SESSION['lang']['saldo'],1,0,'C',1);		
//                $this->Cell(11/100*$width,$height,$_SESSION['lang']['supplier'],1,0,'C',1);		
//                $this->Cell(8/100*$width,$height,$_SESSION['lang']['tgltanam']." ".substr($_SESSION['lang']['afkirbibit'],5),1,0,'C',1);
//                $this->Cell(8/100*$width,$height,$_SESSION['lang']['umur'],1,1,'C',1);	
//                				
//            }
//                
//            function Footer()
//            {
//                $this->SetY(-15);
//                $this->SetFont('Arial','I',8);
//                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
//            }
//        }
//        $pdf=new PDF('L','pt','A4');
//        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
//        $height = 12;
//		$pdf->AddPage();
//		$pdf->SetFillColor(255,255,255);
//		$pdf->SetFont('Arial','',7);
//                if($kdBatch!='')
//                {
//                    $where=" and batch='".$kdBatch."'";
//                }
//	        $sData="select distinct batch,kodeorg,sum(jumlah) as jumlah from ".$dbname.".bibitan_mutasi where kodeorg like '%".$kdUnit."%'  ".$where." group by batch,kodeorg order by tanggal desc ";
//              // exit("error".$sData);
//                
//                $qData=mysql_query($sData) or die(mysql_error());
//                while($rData=mysql_fetch_assoc($qData))
//                {
//                    $data='';
//                    $sDatabatch="select distinct tanggaltanam,supplerid,jenisbibit,tanggalproduksi from ".$dbname.".bibitan_batch where batch='".$rData['batch']."' ";
//                    $qDataBatch=mysql_query($sDatabatch) or die(mysql_error($sDatabatch));
//                    $rDataBatch=mysql_fetch_assoc($qDataBatch);
//                    $thnData=substr($rDataBatch['tanggaltanam'],0,4);
//                    $starttime=strtotime($rDataBatch['tanggaltanam']);//time();// tanggal sekarang
//                    $endtime=strtotime($tglSkrng);//tanggal pembuatan dokumen
//                    $timediffSecond = abs($endtime-$starttime);
//                    $base_year = min(date("Y", $thnData), date("Y", $thnSkrng));
//                    $diff = mktime(0, 0, $timediffSecond, 1, 1, $base_year);
//                    $jmlHari=date("j", $diff) - 1;
//                    $no+=1;
//               
//                        
//                        $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);		
//			$pdf->Cell(8/100*$width,$height,$rData['batch'],1,0,'C',1);		
//			$pdf->Cell(17/100*$width,$height,$optNm[$rData['kodeorg']],1,0,'C',1);		
//			$pdf->Cell(8/100*$width,$height,number_format($rData['jumlah'],0),1,0,'R',1);
//			$pdf->Cell(11/100*$width,$height,$optnmSup[$rDataBatch['supplerid']],1,0,'C',1);	
//			$pdf->Cell(8/100*$width,$height,tanggalnormal($rDataBatch['tanggaltanam']),1,0,'C',1);	
//			$pdf->Cell(8/100*$width,$height,$jmlHari,1,1,'C',1);	
//			
//                }
//
//        $pdf->Output();
//	break;

	
    default:
    break;
}

?>
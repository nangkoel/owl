<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');
require_once('lib/nangkoelib.php');

$tahun=$_GET['tahun'];
$kebun=$_GET['kebun'];
        
	
//check, one-two
if($tahun==''){
    echo "WARNING: silakan mengisi tahun."; exit;
}
if($kebun==''){
    echo "WARNING: silakan mengisi kebun."; exit;
}

//echo $tahun.$kebun;

// ambil data
    $isidata=array();
//$str="select * from ".$dbname.".bgt_areal_per_afd_vw where tahunbudget = '".$tahun."' and afdeling like '".$kebun."%' order by afdeling, thntnm";
$str="select sum(hathnini) as hathnini,sum(hanonproduktif) as hanonproduktif,sum(pokokproduksi) as pokokproduksi,
      thntnm,substr(kodeblok,1,6) as afdeling,statusblok,sum(pokokthnini) as pokokthnini from ".$dbname.".bgt_blok where
      substr(kodeblok,1,4)='".$kebun."' and tahunbudget = '".$tahun."' and statusblok != 'BBT' group by substr(kodeblok,1,6),thntnm,statusblok
      order by substr(kodeblok,1,6),thntnm";

//            echo $str;
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
 if(($bar->thntnm+3)<$tahun){
     if($bar->statusblok!='CADANGAN')
      {
        $isidata[$bar->thntnm.$bar->statusblok][$bar->afdeling]+=$bar->hathnini;
        $totalrowdata[$bar->thntnm.$bar->statusblok][total]+=$bar->hathnini;
        $totalcolumndata[$bar->afdeling.$bar->statusblok][total]+=$bar->hathnini;
        $total[$bar->statusblok]+=$bar->hathnini;
        $rowdata0[$bar->thntnm.$bar->statusblok]=$bar->thntnm;
      }
    }
    else
    {
        if($bar->statusblok!='CADANGAN')
      {
          if($bar->statusblok=='TB')
          {
              $bar->statusblok='TBM';
          }
       
        $isidata1[$bar->thntnm.$bar->statusblok][$bar->afdeling]+=$bar->hathnini;
        $totalrowdata1[$bar->thntnm.$bar->statusblok][total]+=$bar->hathnini;
        $totalcolumndata1[$bar->afdeling.$bar->statusblok][total]+=$bar->hathnini;
        $total1[$bar->statusblok]+=$bar->hathnini;
        $rowdata1[$bar->thntnm.$bar->statusblok]=$bar->thntnm;
      }
    }
    if($bar->statusblok=='CADANGAN')
    {
        $bar->hanonproduktif=$bar->hathnini;
    }
    $unplanted[$bar->afdeling]+=$bar->hanonproduktif;
    $totalunplanted+=$bar->hanonproduktif;
    
    $kadaster[$bar->afdeling]+=$bar->hathnini+$bar->hanonproduktif;
    $totalkadaster+=$bar->hathnini+$bar->hanonproduktif;
    
    $isidata2[$bar->thntnm][$bar->afdeling]+=$bar->pokokthnini;
    $totalrowdata2[$bar->thntnm][total]+=$bar->pokokthnini;
    $totalcolumndata2[$bar->afdeling][total]+=$bar->pokokthnini;
    $total2+=$bar->pokokthnini;
    
    $pkkProduktif[$bar->thntnm][$bar->afdeling]+=$bar->pokokproduksi;
    $totPkkProduktif+=$bar->pokokproduksi;
    $totPerthnPkk[$bar->thntnm][total]+=$bar->pokokproduksi;
    $totAfdPkkProduktif[$bar->afdeling][total]+=$bar->pokokproduksi;
    
    $headerdata[$bar->afdeling]=$bar->afdeling;
    $rowdata[$bar->thntnm]=$bar->thntnm;
}

//echo "<pre>";
//print_r($rowdata1);
//echo "</pre>";
count($headerdata)>0?sort($headerdata):false;
count($rowdata)>0?sort($rowdata):false;
count($rowdata0)>0?sort($rowdata0):false;
count($rowdata1)>0?sort($rowdata1):false;


$jumlahafdeling=0;
if(!empty($headerdata))foreach($headerdata as $baris1)
{
    $jumlahafdeling+=1;
} 
$jumlahrow=0;
if(!empty($rowdata))foreach($rowdata as $baris2)
{
    $jumlahrow+=1;
}else{
    echo"Data tidak tersedia."; exit;
} 

        class PDF extends FPDF
        {
            function Header() {
                global $tahun;
                global $kebun;
                global $dbname;
                global $headerdata;
                global $isidata;
                global $jumlahafdeling;
                global $statTbm;
                
                # Bulan
               // $optBulan = 
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
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
                $this->SetFont('Arial','',8);
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['budgetyear'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$tahun,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,'Printed By','',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,$_SESSION['empl']['name'],'',1,'L');		
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$kebun,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
		$title=$_SESSION['lang']['arealstatement'];		
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','',10);
                $this->SetFillColor(220,220,220);
//                $this->Cell(5/100*$width,$height,'No',LRT,0,'C',1);
                $this->Cell(15/100*$width,$height,$_SESSION['lang']['uraian'],1,0,'C',1);
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);
                $this->Cell((10/100*$width)*$jumlahafdeling,$height,$_SESSION['lang']['afdeling'],1,0,'C',1);
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,1,'C',1);
                $this->Cell(15/100*$width,$height,'',LRB,0,'C',1); // uraian
                $this->Cell(10/100*$width,$height,'',LRB,0,'C',1); // tahuntanam
                if(!empty($headerdata))foreach($headerdata as $baris)
                {
                    $this->Cell(10/100*$width,$height,$baris,1,0,'C',1); // afdeling
                } 
                $this->Cell(10/100*$width,$height,'',LRB,1,'C',1); // total
                
          
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
		$pdf->SetFont('Arial','',10);
//TM
//body1
$statTm="TM";
$countdown=$jumlahrow;
if(!empty($rowdata0))foreach($rowdata0 as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    if($countdown==$jumlahrow)$pdf->Cell(15/100*$width,$height,'A. Luas Areal TM (ha)',LRT,0,'L',1); // uraian
        else $pdf->Cell(15/100*$width,$height,'',LR,0,'C',1);                               // uraian
    $pdf->Cell(10/100*$width,$height,$tt,1,0,'C',1);                                        // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
        $pdf->Cell(10/100*$width,$height,number_format($isidata[$tt.$statTm][$af],2),1,0,'R',1);    // data tiap afdeling
        $totalplanted_tm[$af]+=$isidata[$tt.$statTm][$af];
    }
    $pdf->Cell(10/100*$width,$height,number_format($totalrowdata[$tt.$statTm][total],2),1,1,'R',1); // total
    $countdown-=1;
    }
} 
if(!empty($rowdata0)){
    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'Subtotal',1,0,'C',1);                              // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af)
    {
        $pdf->Cell(10/100*$width,$height,number_format($totalcolumndata[$af.$statTm][total],2),1,0,'R',1); // data tiap afdeling
    } 
    $pdf->Cell(10/100*$width,$height,number_format($total[$statTm],2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);	
    
}   



 //TBM
      $statTbm="TBM";
$countdown=$jumlahrow;
if(!empty($rowdata1))foreach($rowdata1 as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    if($countdown==$jumlahrow)$pdf->Cell(15/100*$width,$height,'B. Luas Areal (ha)',LRT,0,'L',1); // uraian
        else $pdf->Cell(15/100*$width,$height,'',LR,0,'C',1);                               // uraian
    $pdf->Cell(10/100*$width,$height,$tt,1,0,'C',1);                                        // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
        $pdf->Cell(10/100*$width,$height,number_format($isidata1[$tt.$statTbm][$af],2),1,0,'R',1);    // data tiap afdeling
        $totalplanted_tbm[$af]+=$isidata1[$tt.$statTbm][$af];
    }
    $pdf->Cell(10/100*$width,$height,number_format($totalrowdata1[$tt.$statTbm][total],2),1,1,'R',1); // total
    $countdown-=1;
    }
} 
if(!empty($rowdata1)){    
    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'Subtotal',1,0,'C',1);                              // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af)
    {
        $pdf->Cell(10/100*$width,$height,number_format($totalcolumndata1[$af.$statTbm][total],2),1,0,'R',1); // data tiap afdeling
    } 
    $pdf->Cell(10/100*$width,$height,number_format($total1[$statTbm],2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);	    
}  
    //Total Planted
    
        $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'TOTAL PLANTED',1,0,'C',1);                              // tahun tanam
    
       if(!empty($headerdata))foreach($headerdata as $af)
        {
            $tp=$totalplanted_tbm[$af]+$totalplanted_tm[$af];
            $pdf->Cell(10/100*$width,$height,number_format($tp,2),1,0,'R',1); // data tiap afdeling
    }
        $ttp=$total1[$statTbm]+$total[$statTm];
            $pdf->Cell(10/100*$width,$height,number_format($ttp,2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);
  
    
    
//===========unplanted    

    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    
    $pdf->Cell(10/100*$width,$height,'Unplanted',1,0,'C',1);                              // tahun tanam
    if(!empty($unplanted))foreach($unplanted as $af)
    {
        $pdf->Cell(10/100*$width,$height,number_format($af,2),1,0,'R',1); // data tiap afdeling
    } 
    $pdf->Cell(10/100*$width,$height,number_format($totalunplanted,2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);
    
    
    //Grand Total

    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'GRAND TOTAL ',1,0,'C',1);                              // tahun tanam
    
if(!empty($headerdata)){   
   foreach($headerdata as $af)
    { 
       $gt=$totalplanted_tbm[$af]+$totalplanted_tm[$af]+$unplanted[$af];
      $pdf->Cell(10/100*$width,$height,number_format($gt,2),1,0,'R',1);
    } 
    $tgt=$ttp+$totalunplanted;
        $pdf->Cell(10/100*$width,$height,number_format($tgt,2),1,1,'R',1);
    $stream.="</tr>";
 }

 //kadaster
/*    
    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'TOTAL AREA',1,0,'C',1);                              // tahun tanam
    foreach($kadaster as $af)
    {
        $pdf->Cell(10/100*$width,$height,number_format($af,2),1,0,'R',1); // data tiap afdeling
    } 
    $pdf->Cell(10/100*$width,$height,number_format($totalkadaster,2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);
    
  */  

//body2
$countdown=$jumlahrow;
if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
        $pdf->SetFont('Arial','',8);
    if($countdown==$jumlahrow)$pdf->Cell(15/100*$width,$height,'C. Populasi (pkk)',LRT,0,'L',1); // uraian
        else $pdf->Cell(15/100*$width,$height,'',LR,0,'C',1);                               // uraian
        $pdf->SetFont('Arial','',6);
    $pdf->Cell(10/100*$width,$height,$tt,1,0,'C',1);                                        // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
        $pdf->Cell(5/100*$width,$height,number_format($isidata2[$tt][$af],2),1,0,'R',1);    // data tiap afdeling
        $pdf->Cell(5/100*$width,$height,number_format($pkkProduktif[$tt][$af],2),1,0,'R',1);    // data tiap afdeling
    }
                  $pdf->Cell(5/100*$width,$height,number_format($totalrowdata2[$tt][total],2),1,0,'R',1); // total
    $pdf->Cell(5/100*$width,$height,number_format($totAfdPkkProduktif[$tt][total],2),1,1,'R',1); // total
    $countdown-=1;
    }
} 
    $pdf->Cell(15/100*$width,$height,'',LRB,0,'C',1);                                       // uraian
    $pdf->SetFillColor(220,220,220);
    $pdf->Cell(10/100*$width,$height,'Total Areal',1,0,'C',1);                              // tahun tanam
    if(!empty($headerdata))foreach($headerdata as $af)
    {
        $pdf->Cell(5/100*$width,$height,number_format($totalcolumndata2[$af][total],2),1,0,'R',1); // data tiap afdeling
        $pdf->Cell(5/100*$width,$height,number_format($totAfdPkkProduktif[$af][total],2),1,0,'R',1); // data tiap afdeling
    } 
    $pdf->Cell(5/100*$width,$height,number_format($total2,2),1,0,'R',1); // total
    $pdf->Cell(5/100*$width,$height,number_format($totPkkProduktif,2),1,1,'R',1); // total
    $pdf->SetFillColor(255,255,255);	
    
    $pdf->Output();


exit;

$stream='';
//header
$stream.="<table class=sortable cellspacing=1 border=1 width=100%>
     <thead>
        <tr class=rowtitle>
            <td rowspan=2 align=center>".$_SESSION['lang']['uraian']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['tahuntanam']."</td>
            <td colspan=".$jumlahafdeling." align=center>Data per Afdeling</td>";
       $stream.="<td rowspan=2 align=center>".$_SESSION['lang']['total']."</td>
        </tr>";
    if(!empty($headerdata))foreach($headerdata as $baris)
    {
       $stream.="<td align=center>".$baris."</td>";
    } 
$stream.="</thead>
    <tbody>";

//body1
$countdown=$jumlahrow;
if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    $stream.="<tr class=rowcontent>";
    if($countdown==$jumlahrow)$stream.="<td align=left>A. Luas Areal (ha)</td>"; else $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>".$tt."</td>";
//    $tahuntanam=$baris2;
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
        $stream.="<td align=right>".number_format($isidata[$tt][$af],2)."</td>";
    } 
    $stream.="<td align=right>".number_format($totalrowdata[$tt][total],2)."</td>";
    $stream.="</tr>";
    $countdown-=1;
    }
} 
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>Total Areal</td>";
    if(!empty($headerdata))foreach($headerdata as $af)
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right>".number_format($totalcolumndata[$af][total],2)."</td>";
    } 
        $stream.="<td align=right>".number_format($total,2)."</td>";
    $stream.="</tr>";

//body2
$countdown=$jumlahrow;
if(!empty($rowdata))foreach($rowdata as $tt) // tahun tanam / row
{
    if($tt!=0)
    {
    $stream.="<tr class=rowcontent>";
    if($countdown==$jumlahrow)$stream.="<td align=left>B. Populasi Tanaman (pkk)</td>"; else $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>".$tt."</td>";
//    $tahuntanam=$baris2;
    if(!empty($headerdata))foreach($headerdata as $af) // afdeling / column
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right>".number_format($isidata2[$tt][$af])."</td>";
    } 
    $stream.="<td align=right>".number_format($totalrowdata2[$tt][total])."</td>";
    $stream.="</tr>";
    $countdown-=1;
    }
} 
    $stream.="<tr class=rowcontent>";
    $stream.="<td align=center>&nbsp;</td>";
    $stream.="<td align=center>Total Pokok</td>";
    if(!empty($headerdata))foreach($headerdata as $af) 
    {
//        $tahuntanam=$baris1;
        $stream.="<td align=right>".number_format($totalcolumndata2[$af][total])."</td>";
    } 
        $stream.="<td align=right>".number_format($total2)."</td>";
    $stream.="</tr>";


$stream.="    </tbody>
         <tfoot>
         </tfoot>		 
   </table>";    

$qwe=date("YmdHms");

$nop_="bgt_arealstatement".$tahun." ".$kebun;
//if(strlen($stream)>0)
//{
//     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
//     gzwrite($gztralala, $stream);
//     gzclose($gztralala);
//     echo "<script language=javascript1.2>
//        window.location='tempExcel/".$nop_.".xls.gz';
//        </script>";
//}  
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
?>
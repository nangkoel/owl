<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');
require_once('lib/nangkoelib.php');


$pt=$_GET['pt'];
$periode=$_GET['periode'];
$akundari=$_GET['akundari'];
$akunsampai=$_GET['akunsampai'];
//check, one-two
if($akundari==''){
    echo "WARNING: silakan memilih akun."; exit;
}
if($akunsampai==''){
    echo "WARNING: silakan memilih akun."; exit;
}
#================ 
#===========================================
//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
// exclude laba rugi tahun berjalan
$str="select noakundebet from ".$dbname.".keu_5parameterjurnal
    where kodeaplikasi = 'CLM'
    ";
$clm='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $clm=$bar->noakundebet;
}

#++++++++++++++++++++++++++++++++++++++++++
$qwe=explode("-",$periode);
$periode=$qwe[0].$qwe[1];
$bulan= $qwe[1];
$periode2=$qwe[1].'-'.$qwe[0];

// kamus akun
if($_SESSION['language']=='EN'){
    $zz='namaakun1 as namaakun';
}
else
{
    $zz='namaakun';
}
$str="select noakun,".$zz." from ".$dbname.".keu_5akun
                        where level = '5'
                        order by noakun
                        ";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
                $namaakun[$bar->noakun]=$bar->namaakun;

        }

//ambil saldo awal
if($gudang==''){
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."'";
    $wheregudang='';
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
	$wheregudang.="'".strtoupper($bar->kodeorganisasi)."',";
    }
    $wheregudang="and kodeorg in (".substr($wheregudang,0,-1).") ";
}else{
    $wheregudang="and kodeorg = '".$gudang."' ";
}
$str="select * from ".$dbname.".keu_saldobulanan where periode = '".$periode."' and noakun >= '".$akundari."' and noakun <= '".$akunsampai."' 
      and noakun !='".$clm."' ".$wheregudang." order by noakun, kodeorg";

$lebarno=6;
$lebarnoakun=8;
$lebarnamaakun=25;
$lebarkodeorg=10;
$lebarawal=11;
$lebardebet=11;

#==========================create page
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
       global $unit;
       global $lebarketerangan;
       global $lebarbulan;
       global $dbname;
       global $tahun;
       global $tahunlalu;
       global $lebarno;
       global $lebarnoakun;
       global $lebarnamaakun;
       global $lebarkodeorg;
       global $lebarawal;
       global $lebardebet;
       global $periode2;
       
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 4;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,25);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(40);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(40); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(40); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                $this->SetFont('Arial','',8);
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['pt'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$namapt,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,'Printed By','',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,$_SESSION['empl']['name'],'',1,'L');		
              	$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(70/100*$width,$height,$periode2,'',0,'L');		
              	$this->Cell((7/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
		$title=$_SESSION['lang']['catatanneraca'];		
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','',10);
                $this->SetFillColor(220,220,220);
//                $this->Cell(5/100*$width,$height,'No',LRT,0,'C',1);
                $this->Cell($lebarno/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'C',1);
                $this->Cell($lebarnoakun/100*$width,$height,$_SESSION['lang']['noakun'],1,0,'C',1);
                $this->Cell($lebarnamaakun/100*$width,$height,$_SESSION['lang']['namaakun'],1,0,'C',1);
                $this->Cell($lebarkodeorg/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);
                $this->Cell($lebarawal/100*$width,$height,$_SESSION['lang']['saldoawal'],1,0,'C',1); 
                $this->Cell($lebardebet/100*$width,$height,$_SESSION['lang']['debet'],1,0,'C',1);
                $this->Cell($lebardebet/100*$width,$height,$_SESSION['lang']['kredit'],1,0,'C',1);
                $this->Cell($lebarawal/100*$width,$height,$_SESSION['lang']['saldoakhir'],1,1,'C',1); 
     }
 	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}   
}

$pdf=new PDF('L','mm','A4'); 
$pdf->AddPage();

//        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 4;
//		$pdf->AddPage();
//		
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);

$no=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $qweawal="awal".$bulan;
    $qwedebet="debet".$bulan;
    $qwekredit="kredit".$bulan;
    $saldoawal=$bar->$qweawal; $totalawal+=$saldoawal;
    $saldodebet=$bar->$qwedebet; $totaldebet+=$saldodebet; 
    $saldokredit=$bar->$qwekredit; $totalkredit+=$saldokredit;
    $saldoakhir=$saldoawal+$saldodebet-$saldokredit; $totalakhir+=$saldoakhir;
                $pdf->Cell($lebarno/100*$width,$height,$no,1,0,'R',1);
                $pdf->Cell($lebarnoakun/100*$width,$height,$bar->noakun,1,0,'C',1);
                $pdf->Cell($lebarnamaakun/100*$width,$height,$namaakun[$bar->noakun],1,0,'L',1);
                $pdf->Cell($lebarkodeorg/100*$width,$height,$bar->kodeorg,1,0,'C',1);
                $pdf->Cell($lebarawal/100*$width,$height,number_format($saldoawal),1,0,'R',1); 
                $pdf->Cell($lebardebet/100*$width,$height,number_format($saldodebet),1,0,'R',1);
                $pdf->Cell($lebardebet/100*$width,$height,number_format($saldokredit),1,0,'R',1);
                $pdf->Cell($lebarawal/100*$width,$height,number_format($saldoakhir),1,1,'R',1); 
}
                $pdf->Cell(($lebarno+$lebarnoakun+$lebarnamaakun+$lebarkodeorg)/100*$width,$height,'Total',1,0,'C',1);
                $pdf->Cell($lebarawal/100*$width,$height,number_format($totalawal),1,0,'R',1); 
                $pdf->Cell($lebardebet/100*$width,$height,number_format($totaldebet),1,0,'R',1);
                $pdf->Cell($lebardebet/100*$width,$height,number_format($totalkredit),1,0,'R',1);
                $pdf->Cell($lebarawal/100*$width,$height,number_format($totalakhir),1,1,'R',1); 
    $stream.="<tr class=rowcontent>";
        $stream.="<td align=center colspan=4>Total</td>";
        $stream.="<td align=right>".number_format($totalawal)."</td>";
        $stream.="<td align=right>".number_format($totaldebet)."</td>";
        $stream.="<td align=right>".number_format($totalkredit)."</td>";
        $stream.="<td align=right>".number_format($totalakhir)."</td>";
     $stream.="</tr>";
$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";                
                
                
                
                
                
                
                
                $pdf->Output();		
exit;
while($bar=mysql_fetch_object($res))
{
    $tampildari=$bar->variableoutput;
    
    if($bar->tipe=='Header')
      {
                $pdf->Cell(($lebarketerangan+($lebarbulan*13))/100*$width,$height,$bar->keterangandisplay,1,1,'L',1);
      }
    else
    {
        $st12="select sum(awal01) as awal01, sum(awal02) as awal02, sum(awal03) as awal03, sum(awal04) as awal04,
            sum(awal05) as awal05, sum(awal06) as awal06, sum(awal07) as awal07, sum(awal08) as awal08,
                sum(awal09) as awal09, sum(awal10) as awal10, sum(awal11) as awal11, sum(awal12) as awal12
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and ".$where;
//        echo $st12."<br>";
        $res12=mysql_query($st12);
        $awal01=0;
        $awal02=0;
        $awal03=0;
        $awal04=0;
        $awal05=0;
        $awal06=0;
        $awal07=0;
        $awal08=0;
        $awal09=0;
        $awal10=0;
        $awal11=0;
        $awal12=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $awal01=$ba12->awal01;
            $awal02=$ba12->awal02;
            $awal03=$ba12->awal03;
            $awal04=$ba12->awal04;
            $awal05=$ba12->awal05;
            $awal06=$ba12->awal06;
            $awal07=$ba12->awal07;
            $awal08=$ba12->awal08;
            $awal09=$ba12->awal09;
            $awal10=$ba12->awal10;
            $awal11=$ba12->awal11;
            $awal12=$ba12->awal12;
        }
        
        if($bar->tipe=='Total'){
                $pdf->SetFillColor(220,220,220);
            
                $pdf->Cell($lebarketerangan/100*$width,$height,$bar->keterangandisplay,1,0,'L',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal01),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal02),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal03),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal04),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal05),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal06),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal07),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal08),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal09),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal10),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal11),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal12),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format(0),1,1,'R',1); 
                $pdf->SetFillColor(255,255,255);
                
            } //  end of Total
        
        else
        { // not Total
                $pdf->Cell($lebarketerangan/100*$width,$height,$bar->keterangandisplay,1,0,'L',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal01),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal02),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal03),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal04),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal05),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal06),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal07),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal08),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal09),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal10),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal11),1,0,'R',1);
                $pdf->Cell($lebarbulan/100*$width,$height,number_format($awal12),1,0,'R',1); 
                $pdf->Cell($lebarbulan/100*$width,$height,number_format(0),1,1,'R',1); 
        } // end of not Total   
    }   
}

$pdf->Output();		
?>
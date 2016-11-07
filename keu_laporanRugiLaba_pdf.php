<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');

	$pt=$_GET['pt'];
	$unit=$_GET['gudang'];
	$periode=$_GET['periode'];

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='COMPANY NAME';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namapt=strtoupper($bar->namaorganisasi);
}
#++++++++++++++++++++++++++++++++++++++++++
$kodelaporan='INCOME STATEMENT';

$periodesaldo=str_replace("-", "", $periode);
$tahunini=substr($periodesaldo,0,4);

#sekarang
$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
$periodCUR=date('Ym',$t);
$kolomCUR="awal".date('m',$t);

#captionsekarang============================
$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
$captionCUR=date('M-Y',$t);

#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);

#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";


#==========================create page
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
       global $unit;
       global $captionCUR;
        $this->SetFont('Arial','B',8); 
		$this->Cell(20,3,$namapt,'',1,'L');
                $this->Cell(20,3,"UNIT:".$unit,'',1,'L');
        $this->SetFont('Arial','B',12);
        $this->Ln();
	$this->Cell(190,3,strtoupper($_SESSION['lang']['rugilaba']),0,1,'C');
        $this->SetFont('Arial','',8);
        $this->Ln(); 
        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$this->PageNo(),'',1,'L');
        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,'User','',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->SetFont('Arial','',8);					
        $this->Line(10,36,200,36);
        $this->Ln();
        $this->Cell(110,5,'','',0,'L');
        $this->Cell(30,5,$captionCUR,'B',0,'R');
        $this->Cell(30,5,'YTD','B',1,'R');
        $this->Ln();
    }
 	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}   
}

$pdf=new PDF('P','mm','A4');
$pdf->AddPage();

$tnow2=0;
$ttill2=0;
$tnow3=0;
$ttill3=0;

while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,5,'','',0,'C');
        if($_SESSION['language']=='ID'){
            $pdf->Cell(100,5,$bar->keterangandisplay,'',0,'L');}
        else{
            $pdf->Cell(100,5,$bar->keterangandisplay1,'',0,'L');
        }
        $pdf->Cell(30,5,'','',0,'C');
        $pdf->Cell(30,5,'','',1,'C'); 
      }
    else
    {
        #ambil saldo akhir periode barjalan sebagai akumulasi
      /*  $st12="select sum(".$kolomCUR.") as akumilasi
               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periodCUR."' and ".$where;
        //echo $st12;
        */
        $st12="select sum(awal".substr($periodesaldo,4,2).")+sum(debet".substr($periodesaldo,4,2).") - sum(kredit".substr($periodesaldo,4,2).") as akumilasi
        from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
        and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where; 
        $res12=mysql_query($st12);
        
        $akumulasi=0;
        while($ba12=mysql_fetch_object($res12))
        {
            $akumulasi=$ba12->akumilasi;
        }
        #mutasi bulan berjalan
        $st13="select sum(debet) - sum(kredit) as sekarang
               from ".$dbname.".keu_jurnalsum_vw where noakun between '".$bar->noakundari."' 
               and '".$bar->noakunsampai."' and  periode='".$periode."' and ".$where;
        $res13=mysql_query($st13);
        $jlhsekarang=0;
        while($ba13=mysql_fetch_object($res13))
        {
            $jlhsekarang=$ba13->sekarang;
        }
        
        $tnow2+=$jlhsekarang;
        $ttill2+=$akumulasi;
        $tnow3+=$jlhsekarang;
        $ttill3+=$akumulasi;        
        if($bar->tipe=='Total'){
                if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='2')
                    {
                        $jlhsekarang=$tnow2;
                        $akumulasi=$ttill2; 
                        $tnow2=0;
                        $ttill2=0;
                    }
                    if($bar->variableoutput=='3')
                    {
                        $jlhsekarang=$tnow3;
                        $akumulasi=$ttill3; 
                        $tnow3=0;
                        $ttill3=0;
                    }                                        
                }       
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(10,5,'','',0,'C');
            $pdf->Cell(5,5,'','',0,'L');
            if($_SESSION['language']=='ID'){
                $pdf->Cell(95,5,$bar->keterangandisplay,'',0,'L'); }
            else{
                $pdf->Cell(95,5,$bar->keterangandisplay1,'',0,'L');
            }
            $pdf->Cell(30,5,number_format($jlhsekarang),'T',0,'R');
            $pdf->Cell(30,5,number_format($akumulasi),'T',1,'R'); 
            $pdf->Ln();
        }
        else
        {
            #escape yang nilainya nol
            if($jlhsekarang==0 and $akumulasi==0)
                continue;
            else{
                $pdf->Cell(10,5,'','',0,'C');
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(10,5,'','',0,'L');
                if($_SESSION['language']=='ID'){
                    $pdf->Cell(90,5,$bar->keterangandisplay,'',0,'L');}
                else{
                    $pdf->Cell(90,5,$bar->keterangandisplay1,'',0,'L');
                }
                $pdf->Cell(30,5,number_format($jlhsekarang),'',0,'R');
                $pdf->Cell(30,5,number_format($akumulasi),'',1,'R'); 
            }
        }   
    }   
}

$pdf->Output();		
?>
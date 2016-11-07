<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
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

//$periodesaldo=str_replace("-", "", $periode);
//$tahunini=substr($periodesaldo,0,4);
//
//#sekarang
//$t=mktime(0,0,0,substr($periodesaldo,4,2)+1,15,substr($periodesaldo,0,4));
//$periodCUR=date('Ym',$t);
//$kolomCUR="awal".date('m',$t);
//
//#captionsekarang============================
//$t=mktime(0,0,0,substr($periodesaldo,4,2),15,substr($periodesaldo,0,4));
//$captionCUR=date('M-Y',$t);

#ambil format mesinlaporan==========
$str="select * from ".$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res=mysql_query($str);

#query+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if($unit=='')
    $where=" kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."')";
else 
    $where=" kodeorg='".$unit."'";

$lebarkiri=40;
$lebarisi=18;
$lebarkanan=25;
       if($unit=='')$tampilunit=$_SESSION['lang']['all']; else $tampilunit=$unit;

#==========================create page
class PDF extends FPDF {
    function Header() {
       global $namapt;
       global $periode;
       global $tampilunit;
       global $lebarkiri;
       global $lebarisi;
       global $lebarkanan;
//       global $captionCUR;
        $this->SetFont('Arial','B',8); 
//		$this->Cell(20,3,$namapt,'',1,'L');
//                $this->Cell(20,3,"UNIT:".$tampilunit,'',1,'L');
        $this->SetFont('Arial','B',12);
        $this->Ln();
	$this->Cell(300,3,strtoupper($_SESSION['lang']['laporanrugilabaperiodik']),0,1,'C');
        $this->SetFont('Arial','',8);
        $this->Ln(); 
		$this->Cell(230,3,$namapt,'',0,'L');
//        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,$_SESSION['lang']['tanggal'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,date('d-m-Y H:i'),0,1,'L');
                $this->Cell(230,3,"UNIT:".$tampilunit,'',0,'L');
//        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,$_SESSION['lang']['page'],'',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$this->PageNo(),'',1,'L');
                $this->Cell(230,3,"Periode:".$periode,'',0,'L');
//        $this->Cell(150,3,' ','',0,'R');
        $this->Cell(15,3,'User','',0,'L');
        $this->Cell(2,3,':','',0,'L');
        $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
        $this->SetFont('Arial','',8);					
//        $this->Line(10,36,200,36);
        $this->Ln();
        $this->Cell($lebarkiri,5,'','B',0,'L');
        $this->Cell($lebarisi,5,numToMonth(1, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(2, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(3, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(4, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(5, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(6, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(7, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(8, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(9, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(10, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(11, 'E'),'B',0,'R');
        $this->Cell($lebarisi,5,numToMonth(12, 'E'),'B',0,'R');
        $this->Cell($lebarkanan,5,'YTD','B',1,'R');
        $this->Ln();
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

$tnow2[]=0;
$ttill2=0;
$tnow3[]=0;
$ttill3=0;

while($bar=mysql_fetch_object($res))
{
    if($bar->tipe=='Header')
      {
        if($_SESSION['language']=='ID'){
            $stream.="<tr class=rowcontent><td colspan=16><b>".$bar->keterangandisplay."</b></td></tr>";  }
        else{
            $stream.="<tr class=rowcontent><td colspan=16><b>".$bar->keterangandisplay1."</b></td></tr>";
        }
      }
    else
    {
//        #ambil saldo akhir periode barjalan sebagai akumulasi
//        $st12="select sum(".$kolomCUR.") as akumilasi
//               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
//               and '".$bar->noakunsampai."' and  periode like '".$periode."%' and ".$where;
//        $res12=mysql_query($st12);
////echo $st12.'<br>';        
//        $akumulasi=0;
//        while($ba12=mysql_fetch_object($res12))
//        {
//            $akumulasi=$ba12->akumilasi;
//        }
        #mutasi bulan berjalan
        $akum=0;
        for ($i = 1; $i <= 12; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                $st13="select sum(debet".$ii.") - sum(kredit".$ii.") as sekarang
                       from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' 
                       and '".$bar->noakunsampai."' and  periode like '".$periode."%' and ".$where;
                $res13=mysql_query($st13);
                $jlhsekarang[$ii]=0;
                while($ba13=mysql_fetch_object($res13))
                {
                    $jlhsekarang[$ii]=$ba13->sekarang;
                    $akum+=$ba13->sekarang;
                }
                $tnow201[$ii]+=$jlhsekarang[$ii];
                $tnow301[$ii]+=$jlhsekarang[$ii];
        }        
                $ttill2+=$akum;
                $ttill3+=$akum;
        if($bar->tipe=='Total'){
                if($bar->noakundari=='' or $bar->noakunsampai=='')
                {
                    if($bar->variableoutput=='2')
                    {
                                                $akum=$ttill2; 
                                                $ttill2=0;
                        for ($i = 1; $i <= 12; $i++) {
                            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                                                $jlhsekarang[$ii]=$tnow201[$ii];
                                                $tnow201[$ii]=0;
                        }                        
                    }
                    if($bar->variableoutput=='3')
                    {
                                                $akum=$ttill3; 
                                                $ttill3=0;
                        for ($i = 1; $i <= 12; $i++) {
                            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                                                $jlhsekarang[$ii]=$tnow301[$ii];
                                                $tnow301[$ii]=0;
                        }                        
                    }                                        
                }        
            $pdf->SetFont('Arial','B',7);
//            $pdf->Cell(10,5,'','',0,'C');
//            $pdf->Cell(5,5,'','',0,'L');
            if($_SESSION['language']=='ID'){
                $pdf->Cell($lebarkiri,5,substr($bar->keterangandisplay,0,25),'',0,'L');}
            else{
                $pdf->Cell($lebarkiri,5,substr($bar->keterangandisplay1,0,25),'',0,'L');
            }
            for ($i = 1; $i <= 12; $i++) {
                if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                                    $pdf->Cell($lebarisi,5,number_format($jlhsekarang[$ii]),'',0,'R');
//                                            $stream.="<td align=right><b>".number_format($jlhsekarang[$ii])."</b></td>";
            }    
            $pdf->Cell($lebarkanan,5,number_format($akum),'',1,'R'); 
            $pdf->Ln();
        }
        else
        {
            #escape yang nilainya nol
            if($jlhsekarang==0 and $akum==0)
                continue;
            else{
//                $pdf->Cell(10,5,'','',0,'C');
                $pdf->SetFont('Arial','',7);
//                $pdf->Cell(10,5,'','',0,'L');
                if($_SESSION['language']=='ID'){
                    $pdf->Cell($lebarkiri,5,substr($bar->keterangandisplay,0,25),'',0,'L');}
                else{
                    $pdf->Cell($lebarkiri,5,substr($bar->keterangandisplay1,0,25),'',0,'L');
                }
                for ($i = 1; $i <= 12; $i++) {
                    if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
                                        $pdf->Cell($lebarisi,5,number_format($jlhsekarang[$ii]),'',0,'R');
    //                                            $stream.="<td align=right><b>".number_format($jlhsekarang[$ii])."</b></td>";
                }    
                $pdf->Cell($lebarkanan,5,number_format($akum),'',1,'R'); 
            }
        }   
    }   
}

$pdf->Output();		
?>
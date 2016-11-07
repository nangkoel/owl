<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
require_once('terbilang.php');

$tgl=$_GET['tanggal'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
$product=$_GET['product'];
$notiket=$_GET['notiket'];

if(isset($_GET['notiket']))
{
 $str="select PRODUCTCODE from ".$dbname.".mstrxtbs where TICKETNO2='".$notiket."'";
 $res=mysql_query($str);
 while($bar=mysql_fetch_object($res))
 {
    $product=$bar->PRODUCTCODE;
 }
 $where="where TICKETNO2='".$notiket."'";
}
else
{
  $where="where DATEOUT like '".$tgl2."%' and productcode='".$product."'";
}

        $stz="select PRODUCTNAME from ".$dbname.".msproduct where PRODUCTCODE='".$product."'";
        $rez=mysql_query($stz);
        while ($ba=mysql_fetch_object($rez)){
                $productname=$ba->PRODUCTNAME;
        }

    $str="select TICKETNO2,DRIVER,VEHNOCODE,DATEIN,DATEOUT,WEI1ST,WEI2ND,NETTO,MILLCODE,CTRNO,NODOTRP,TRPNAME,SIPBNO,
              b.*  from ".$dbname.".mstrxtbs 
              left join ".$dbname.".wb_bukti b on TICKETNO2=nowb
              left join ".$dbname.".msvendortrp on mstrxtbs.TRPCODE=msvendortrp.TRPCODE  
                ".$where."
             and OUTIN=0 order by DATEOUT";


class PDF extends FPDF
{
        function AcceptPageBreak()
        { 
                        return true;
        }

}
#PDF==============================
$pdf=new PDF();
$pdf->AddPage();
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
        $no+=1;

        $dateinn=$bar->DATEIN;$dateout=$bar->DATEOUT;
        $tgl=substr($dateinn,8,2)."-".substr($dateinn,5,2)."-".substr($dateinn,0,4);
        $masuk=substr($dateinn,11,2).":".substr($dateinn,14,2).":".substr($dateinn,17,2);
        $keluar=substr($dateout,11,2).":".substr($dateout,14,2).":".substr($dateout,17,2);
      //ambil pembeli
          $pembeli='';
          $strx="select a.CTRNO, b.BUYERNAME,b.BUYERADDR,a.DESCRIPTION as kontrakpembeli from ".$dbname.".mscontract a
                 left join ".$dbname.".msvendorbuyer b 
                 on a.BUYERCODE=b.BUYERCODE where a.CTRNO='".$bar->CTRNO."'";	 
          $resx=mysql_query($strx);
          while($barx=mysql_fetch_object($resx))
          {
                $pembeli=$barx->BUYERNAME;
                $kontrakpembeli=$barx->kontrakpembeli;
		$alamatpembeli=$barx->BUYERADDR;
          }
      //ambil pemenuhan kontrak
           $stru="select sum(netto) as sda from ".$dbname.".mstrxtbs where CTRNO='".$bar->CTRNO."'
                  and ticketno2<='".$notiket."'";
           $resu=mysql_query($stru);
	   while($baru=mysql_fetch_object($resu)){
		$sda=$baru->sda;
		}           		 

                $pdf->SetFont('Arial','B',10);
                $pdf->Ln(10);	
                $pdf->Cell(60,10,'',0,0,'C');
                $pdf->Cell(70,10,'',0,0,'C');  
       	        $pdf->Cell(60,10,$pembeli,0,1,'L');

                $pdf->Cell(60,12,'',0,0,'C');
                $pdf->Cell(70,12,strtoupper($bar->nobuku),0,0,'C');
				$d=$pdf->GetY();
                $pdf->SetFont('Arial','B',8); 
       	        $pdf->MultiCell(60,10,$alamatpembeli,0,'J');
				$pdf->SetY($d);
                $pdf->SetFont('Arial','B',10);
                $pdf->Ln(15);
                $pdf->Cell(50,5,'',0,0,'C');     
                $pdf->Cell(50,5, $bar->VEHNOCODE,0,1,'L'); 
      
                $pdf->Ln(8);          
                $pdf->Cell(50,10, $productname,0,1,'L'); 
 
                $pdf->Cell(45,10, '',0,0,'L');  
                $pdf->Cell(35,10, $bar->ffa." %",0,0,'L'); 
                $pdf->Cell(20,10, '',0,0,'L');
                $pdf->Cell(20,10, number_format($bar->WEI2ND,0,",",".")." Kg",0,0,'R'); 
                $x=$pdf->GetX();    
                $pdf->Cell(20,5, '',0,0,'R');             
                $pdf->Cell(55,5, $bar->CTRNO,0,1,'L');                     
                $pdf->SetX($x);                
                $pdf->Cell(20,4, '',0,0,'R'); 
                $pdf->Cell(55,4, $kontrakpembeli,0,1,'C');                  
                
                $pdf->Cell(45,4, '',0,0,'L');  
                $pdf->Cell(35,4, $bar->air." %",0,0,'L'); 
                $pdf->Cell(20,4, '',0,0,'L');
                $pdf->Cell(20,4, number_format($bar->WEI1ST,0,",",".")." Kg",0,0,'R'); 
                $pdf->Cell(20,4, '',0,0,'R');             
                $pdf->Cell(55,4, $bar->SIPBNO,0,1,'L');            

                $pdf->Cell(45,4, '',0,0,'L');  
                $pdf->Cell(35,4, '',0,0,'L'); 
                $pdf->Cell(20,4, '',0,0,'L');
                $pdf->Cell(20,4, '',0,0,'R'); 
                $pdf->Cell(20,4, '',0,0,'R');             
                $pdf->Cell(55,4, $sda,0,1,'C'); 
 
                  $pdf->SetFont('Arial','B',8);
                
                $pdf->Cell(45,3, '',0,0,'L'); 
                $pdf->Cell(35,3, '',0,0,'L'); 
                $pdf->Cell(20,3, '',0,0,'L');
                $pdf->Cell(20,3, '',0,0,'R'); 
                $pdf->Cell(30,3, '',0,0,'R');             
                $pdf->Cell(45,3, $bar->manager,0,1,'L');  

                  $pdf->SetFont('Arial','B',12);
                $pdf->Cell(45,3, '',0,0,'L');  
                $pdf->Cell(35,3, $bar->kotoran." %",0,0,'L'); 
                $pdf->Cell(20,3, '',0,0,'L');
                $pdf->Cell(20,3, number_format($bar->NETTO,0,",",".")." Kg",0,0,'R'); 
                $pdf->Cell(30,3, '',0,0,'R');    
                $pdf->SetFont('Arial','B',8);         
                $pdf->Cell(45,3, $bar->nosegel,0,1,'C'); 
                $pdf->Ln(3);
                $pdf->Cell(45,9, '',0,0,'L');  
                $pdf->Cell(35,9, '',0,0,'L'); 
                $pdf->Cell(20,9, '',0,0,'L');
                $pdf->Cell(20,9, '',0,0,'R'); 
                $pdf->Cell(40,9, '',0,0,'R');             
                $pdf->Cell(50,9, $bar->TRPNAME,0,1,'L');
                    
                  $pdf->SetFont('Arial','B',12);
               $pdf->Ln(3);
                $pdf->Cell(40,5, '',0,0,'L');  
                $pdf->Cell(155,5,kekata($bar->NETTO)." kilogram",0,1,'L'); 
 
	           $pdf->Ln(3);
               
                $pdf->Cell(155,10, '',0,0,'L');  
                $pdf->Cell(40,10,$tgl,0,1,'L');                 
                
                $pdf->Ln(22);
                $pdf->SetFont('Arial','B',10);
               
                $pdf->Cell(35,10, '',0,0,'L');  
                $pdf->Cell(50,10,$bar->DRIVER,0,0,'C');    
                $pdf->Cell(50,10,'',0,0,'C'); 
                $pdf->Cell(50,10,$bar->kapabrik,0,1,'C'); 
                $pdf->Cell(35,5, '',0,0,'L');  
                $pdf->Cell(50,5,$bar->pbongkar,0,0,'C');    
                $pdf->Cell(50,5,'',0,0,'C'); 
                $pdf->Cell(50,5,'MILL MANAGER',0,1,'C');                 
                
                
                
      
}
$pdf->Output();
?>

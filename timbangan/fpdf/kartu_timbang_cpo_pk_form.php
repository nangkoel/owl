<?php
session_start();
require('fpdf.php');

class PDF extends FPDF
{

function Header()
{
	require_once('connection.php');
	$TICKETNO=$_GET['TICKETNO'];
	$IDWB=$_GET['IDWB'];
	$USERNAME=$_SESSION['standard']['username'];
   $str="select * from ".$dbname.".mstrxtbs where TICKETNO2='".$TICKETNO."' and IDWB ='".$IDWB."' and OUTIN='0'";
  // echo $query;
   $res=mysql_query($str);
   while ($bar=mysql_fetch_object($res)){;
   		//$trpname=$bar[0];
		$VEHNO=$bar->VEHNOCODE;$THNTNM=$bar->TAHUNTANAM;$IDWB=$bar->IDWB;$TICKETNO2=$bar->TICKETNO2;
		$CTRNO=$bar->CTRNO;$SIPBNO=$bar->SIPBNO;$NOSEGEL=$bar->NOSEGEL;
		$TRPCODE=$bar->TRPCODE;$SPNO=$bar->SPNO;
		$DRIVER=$bar->DRIVER;$NODO=$bar->NODOTRP;
		$DATEIN=$bar->DATEIN;$DATEOUT=$bar->DATEOUT;
		$WEIGH1=$bar->WEI1ST;$WEIGH2=$bar->WEI2ND;$NETTO=$bar->NETTO;
		$PRODUCTCODE=$bar->PRODUCTCODE;
		$tglin=substr($DATEIN,8,2)."-".substr($DATEIN,5,2)."-".substr($DATEIN,0,4)." ".substr($DATEIN,11,2).":".substr($DATEIN,14,2).":".substr($DATEIN,17,2);
		$tglout=substr($DATEOUT,8,2)."-".substr($DATEOUT,5,2)."-".substr($DATEOUT,0,4)." ".substr($DATEOUT,11,2).":".substr($DATEOUT,14,2).":".substr($DATEOUT,17,2);
	}
	$str2="select TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$TRPCODE."'";
	$res2=mysql_query($str2);
	while ($bar1=mysql_fetch_object($res2)){
		$TRPNAME=$bar1->TRPNAME;
	}
	
	$str3="select BUYERNAME from ".$dbname.".mscontract,".$dbname.".msvendorbuyer where mscontract.CTRNO = '".$CTRNO."' and msvendorbuyer.BUYERCODE=mscontract.BUYERCODE";
	$res3=mysql_query($str3);
	while ($bar2=mysql_fetch_object($res3)){
		$BUYERNAME=$bar2->BUYERNAME;
	}
           //ambil nama manager
		 $str="select MNGRNAME from ".$dbname.".mssystem";
		 $resh=mysql_query($str);
		 while($barh=mysql_fetch_object($resh)){
		  $mgr=$barh->MNGRNAME;
 		 } 
		 
	//if ($PRODUCTCODE=='40000000')
	if ($PRODUCTCODE=='40000006')
		$product='INTI/PK (PALM KERNEL)';
	else if ($PRODUCTCODE=='40000007')
		$product='CPO (CRUDE PALM OIL)';
		else
		$product='CK (CANGKANG)';
           $this->Ln(10);
           $this->SetFont('arial','',11);
           $this->Cell(30,5,'PT.HARDAYA INTI PLANTATIONS',0,1,'L');
	   $this->Cell(30,5,'PMKS BUOL SULTENG',0,l,'L');
           $this->Ln(10); 
           $this->Cell(30,5,'No. Tiket/Seri',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(22,5,$TICKETNO2,0,0,'L');
           
           $this->Cell(20,5,'Tgl.Cetak:',0,0,'L');           
           $this->Cell(15,5,date("d/m/Y"),0,1,'L');

           $this->Cell(30,5,'Pembeli',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$BUYERNAME,0,1,'L');

           $this->Cell(30,5,'No.Kontrak.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$CTRNO,0,1,'L');  

           $this->Cell(30,5,'No.DO.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$SIPBNO,0,1,'L');  
           
           
            $this->Cell(30,5,'Komoditi.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$product,0,1,'L');          

            $this->Cell(30,5,'Pengangkut.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$TRPNAME,0,1,'L');                
           $this->Cell(30,5,'No.Kend.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$VEHNO,0,1,'L');
           
            $this->Cell(30,5,'Supir.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(40,5,$DRIVER,0,1,'L');      
            $this->Ln(5);   
            $this->Cell(30,5,'Timbang II.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($WEIGH2,0,',','.'),'',0,'R'); 
            $this->Cell(6,5,'Kg','',0,'L');          
           $this->Cell(35,5,$tglout,'',1,'L');  
		   
            $this->Cell(30,5,'Timbang I.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($WEIGH1,0,',','.'),'B',0,'R'); 
            $this->Cell(6,5,'Kg','',0,'L');          
           $this->Cell(35,5,$tglin,0,1,'L');             
           
            $this->Cell(30,5,'Berat Bersih.',0,0,'L');
           $this->Cell(2,5,':',0,0,'L');           
           $this->Cell(20,5,number_format($NETTO,0,',','.'),0,0,'R'); 
            $this->Cell(6,5,'Kg','',1,'L');          
           //$this->Ln(20);
           $this->Ln(25);
           
           
          $this->Cell(40,5,strtoupper($USERNAME),'B',0,'C');
          $this->Cell(40,5,strtoupper($DRIVER),'B',1,'C');          
          $this->Cell(40,5,'OPERATOR WB',0,0,'C');
          $this->Cell(40,5,'DRIVER',0,1,'C');
        }
}

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->Output();
?>

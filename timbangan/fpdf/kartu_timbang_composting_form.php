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
		$VEHNO=$bar->VEHNOCODE;$THNTNM=$bar->TAHUNTANAM;$TICKET=$bar->TICKETNO;
		$CTRNO=$bar->CTRNO;$SIPBNO=$bar->SIPBNO;$PENERIMA=$bar->PENERIMA;$TICKETNO2=$bar->TICKETNO2;
		$TRPCODE=$bar->TRPCODE;$SPNO=$bar->SPNO;$SPNOBULKING=$bar->SPNOBULKING;
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
	

	$str4="select PRODUCTNAME from ".$dbname.".msproduct where PRODUCTCODE = '".$PRODUCTCODE."'";
	$res4=mysql_query($str4);
	while ($bar3=mysql_fetch_object($res4)){
		$product=$bar3->PRODUCTNAME;
	}

	//$this->Image('minanga.jpg',22,4.5,200,20,jpg);
	//$this->Image('BGA Logo.jpg',170,5,20,20,jpg); 	
  $this->SetFont('Times','B',16);
   //Geser ke kanan
   $this->Cell(10);
   //Judul dalam bingkai
   $this->Cell(170,10,'PT. MINANGA OGAN',0,0,'C');
   //Ganti baris
   
   $this->Ln(10);
   $this->SetFont('times','B',12);
   $this->Cell(166,10,'No. Tiket           :',0,0,'R');
   $this->Cell(18,10,$TICKETNO2,'',0,'R');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(166,10,'Tanggal Cetak  :',0,0,'R');
   $this->Cell(21.5,10,date("d/m/Y"),'',0,'R');
   $this->Ln(5);
   /*
$this->Cell(166.5,10,'Jam Cetak         :',0,0,'R');
   $this->Cell(16.5,10,date("H:m:s"),'',0,'R');
*/
   //Ganti baris
   $this->Ln(10);
   $this->Cell(27,10,'No. Kendaraan : ',0,0,'R');
   $this->Cell(200,10,$VEHNO,'',0,'L');
   //Ganti baris
   $this->Ln(10);
   $this->Line(0,50,210,50);
	//Ganti baris
   //$this->Ln(5);
   $this->Cell(39,10,'Customer                    : ',0,0,'R');
   $this->Cell(50,10,$PENERIMA,0,0,'L');
   $this->Cell(60.5,10,'Supir        :',0,0,'R');
   $this->Cell(50,10,$DRIVER,'',0,'L');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(39,10,'Product                       : ',0,0,'R');
   $this->Cell(50,10,$product,0,0,'L');
    //Ganti baris
   $this->Ln(5);
   $this->Cell(39,10,'Pengangkut                : ',0,0,'R');
   $this->Cell(50,10,$TRPNAME,0,0,'L');
	//Ganti baris
   $this->Ln(5);
   //$this->Cell(32.5,10,'Unit/Estate Pengirim  : ',0,0,'R');
   $this->Cell(39,10,'1st Weighing              : ',0,0,'R');
   $this->Cell(50,10,$tglin,0,0,'L');
   $this->Cell(12,10,$WEIGH1,0,0,'R');
   $this->Cell(50,10,'Kg','',0,'L');	
   $this->Ln(5);
   $this->Cell(39,10,'2nd Weighing             : ',0,0,'R');
   $this->Cell(50,10,$tglout,0,0,'L');
   $this->Cell(12,10,$WEIGH2,0,0,'R');
   $this->Cell(50,10,'Kg','',0,'L');
   //$this->Ln(2.5);
   $this->Line(97,77.5,117,77.5);
   $this->Ln(5);
   $this->Cell(39,10,'Netto                           : ',0,0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(12,10,$NETTO,0,0,'R');
   $this->Cell(50,10,'Kg','',0,'L');
   $this->Ln(5);
   $this->Line(0,85,210,85);
   $this->Ln(2);
   $this->Cell(39,10,'Weighed By                : ',0,0,'R');
   $this->Cell(50,10,$USERNAME,0,0,'L');
   $this->Cell(60.5,10,'Signature',0,0,'R');
   //$this->Ln(5);
   $this->Line(139,107.5,187,107.5);
   $this->Ln(23);
   $this->Cell(32,10,'',0,0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(71.5,10,'Checked By',0,0,'R');
   
   
}


//Colored table
/*
function FancyTable($header,$data)
{
$orientation='P';
     $format='A4';
    //Colors, line width and bold font
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	$this->FontSizePt=10;
	//Header
	$w=cell_width();
	for($i=0;$i<count($header);$i++)
	 $this->Cell($w[$i],7,$header[$i],1,0,'C',1);

	$this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=0;

    $tot_jjg=0;
    $tot_brt=0;
    $tot_net=0;
	foreach($data as $row)
	{
      $i = 0;
      foreach($row as $column)
      {
         $i<=10 && $i>=6 ? $align = 'R' : $align = 'C';
         $this->Cell($w[$i],7,$column,'TBLR',0,$align);
         $i++;
      }
      $this->Ln();
      $tot_jjg+=$row["jmlhjjg"];
      $tot_brt+=$row["beratkirim"];
      $tot_net+=$row["netto"];
   	}
   	$tanpaTotal = $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5];
   	// Total
   	$this->Cell($tanpaTotal,7,'Total   : ',0,0,'R');
   	$this->Cell($w[6],7,$tot_jjg,'TBLR',0,'R');
   	$this->Cell($w[7],7,$tot_brt,'TBLR',0,'R');
   	$this->Cell($w[8],7,$tot_net,'TBLR',0,'R');
   	$this->Cell($w[9],7,$tot_jjg,'TBLR',0,'R');
   	$this->Cell($w[10],7,$tot_jjg,'TBLR',0,'R');
   	
	//$this->Cell(array_sum($w),0,'','T');
}
*/
}

/*
function cell_width ()
{
 $cell_width = array();
   array_push($cell_width,30);
   array_push($cell_width,40);
    array_push($cell_width,30);
   array_push($cell_width,30);
   array_push($cell_width,30);
   array_push($cell_width,30);
    array_push($cell_width,30);
   array_push($cell_width,30);
   array_push($cell_width,30);
   array_push($cell_width,30);
 return $cell_width;
}
*/
$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->Output();
?>

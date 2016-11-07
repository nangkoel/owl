<?php
session_start();
//include('connection.php');
//require_once('connection.php');
//include('connection.php');
include('../lpt_per_unit.php');
require('fpdf.php');

class PDF extends FPDF
{

function Header()
{
	require_once('connection.php');
   $unit=$_GET['unit'];
	$USERID=$_SESSION['standard']['username'];
	$tanggal=$_GET['tanggal'];
   $query="select unitname from newwbridge.msunit where unitcode='".$unit."'";
  //echo $query;
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];

  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Laporan Penerimaan TBS Per Unit',0,0,'C');
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Tanggal :',0,0,'R');
   $this->Cell(20,10,$tanggal,'',0,'R');
   //Ganti baris
   $this->Ln(20);
   $this->SetFont('Arial','B',10);
   $this->Cell(300,10,'Dicetak Oleh :',0,0,'R');
   $this->Cell(17,10,$USERID,'',0,'R');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(300,10,'Tanggal Cetak :',0,0,'R');
   $this->Cell(20,10,date("d/m/Y"),'',0,'R');
   $this->Ln(5);
   $this->Cell(300,10,'Jam Cetak :',0,0,'R');
   $this->Cell(17,10,date("H:i:s"),'',0,'R');
   //Ganti baris
   $this->Ln(10);
   $this->Cell(17,10,'Unit : ','',0,'R');
   
   $this->Cell(100,10,$un,'',0,'L');

   
   //Ganti baris
   $this->Ln(10);

}
function Footer()
{
  $this->SetFont('Arial','B',15);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(300,10,'Diketahui Oleh :',0,0,'L');
   //Ganti baris
   $this->Ln(25);
   $this->Cell(10,10,'Manager',0,0,'L');
   $this->Cell(130,10,'Kasie',0,0,'R');
   $this->Cell(100,10,'Krani Timbang',0,0,'R');

}

//Colored table
function FancyTable($header,$data)
{
$orientation='L';
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
	  $tot_tarra+=$row["wei2nd"];
	  $tot_bruto+=$row["wei1st"];
   	}
   	$tanpaTotal = $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5];
   	// Total
   	$this->Cell($tanpaTotal,7,'Total   : ',0,0,'R');
   	$this->Cell($w[6],7,$tot_jjg,'TBLR',0,'R');
   	$this->Cell($w[7],7,$tot_brt,'TBLR',0,'R');
   	$this->Cell($w[8],7,$tot_bruto,'TBLR',0,'R');
   	$this->Cell($w[9],7,$tot_tarra,'TBLR',0,'R');
   	$this->Cell($w[10],7,$tot_net,'TBLR',0,'R');
   	
	//$this->Cell(array_sum($w),0,'','T');
}
}

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

require_once('connection.php');
//Column titles
$header = array('No. Tiket','No.SPB','No. Kendaraan','Nama Supir','Jam Masuk','Jam Keluar',
                'Jumlah JJG','Berat Dikirim','Bruto','Tarra', 'Netto');
// Query
$query="select TICKETNO2, spbno,vehnocode, driver,time(datein),time(dateout), jmlhjjg, beratkirim, wei1st,wei2nd,netto
        from ".$dbname.".mstrxtbs where date(dateout)='".$tgl2."' and unitcode='".$unit."' and OUTIN=0 and productcode='40000003' order by dateout";
//echo $query;
$hasil=mysql_query($query);
$result = array();
$i = 0;
$tot_jjg=0;
$tot_brt=0;
$tot_net=0;
while ($bar=mysql_fetch_assoc($hasil))
{
    $result[$i] = $bar;
    $tot_jjg+=$bar["jmlhjjg"];
    $tot_brt+=$bar["beratkirim"];
    $tot_net+=$bar["netto"];
	$tot_tarra+=$row["wei2nd"];
	$tot_bruto+=$row["wei1st"];
    $i++;

}
//echo $tot_jjg;
//echo $bar[6];
// PDF
$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
//print_r ($bar);
/*foreach($bar as $data)
{
$pdf->Cell(20,40,$data,1,1,'C');
} */

$pdf->FancyTable($header,$result);
$pdf->Output();
?>

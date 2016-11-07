<?php
include('../connection.php');
$con=mysql_connect($host,$uname,$pwd) or die('Invalid Connections');
include('../rekap_kirim.php');
require('fpdf2.php');

class PDF extends FPDF
{

function Header()
{
   $mill=$_GET['mill'];
   $produk=$_GET['produk'];
   $periode=$_GET['periode'];
   $vehno=$_GET['vehno'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
   $USERID=$_SESSION['USERID'];
   $query="select unitname from wbridge.msunit where unitcode='".$mill."'";
  // echo $query;
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
   
   $query1="select productname from wbridge.msproduct where productcode=".$produk."";
   $hasil2=mysql_query($query1);
   $bar2=mysql_fetch_array($hasil2);
   $prodname=$bar2[0];


  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Laporan Detail Transportir',0,0,'C');
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Periode :',0,0,'R');
   $this->Cell(20,10,$periode,'',0,'R');
   $this->Ln(20);
   
   $this->Cell(30,10,'Mill  ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$un,0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Dicetak Oleh ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(40,10,$USERID,'',0,'L');
   $this->Ln(5);
   
    $this->Cell(30,10,'Produk  ','',0,'L');
   $this->Cell(3,10,':',0,0,'L');
  $this->Cell(200,10,$prodname,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Tanggal cetak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(35,10,date('d/m/Y h:m:s'),'',0,'L');
   $this->Ln(5);

   $this->Cell(30,10,'No. Kendaraan  ','',0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$vehno,'',0,'L');
   $this->Ln(20);

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
   $this->Cell(300,10,'Demikian kami sampaikan agar maklum dan terima kasih',0,0,'L');
   //Ganti baris
   $this->Ln(10);
   $this->Cell(10,10,'C.C MARKETING JAKARTA',0,0,'L');
   $this->Ln(10);
   $this->Cell(130,10,' Hormat Kami,',0,0,'L');

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

    $this->Cell(40,7,'Supir',1,0,'C',1);
    $this->Cell(20,7,'Tiket',1,0,'C',1);
    $this->Cell(40,7,'No. SP',1,0,'C',1);
    $this->Cell(20,7,'Tanggal',1,0,'C',1);
    $this->Cell(20,7,'Jam Masuk',1,0,'C',1);
    $this->Cell(20,7,'Jam Keluar',1,0,'C',1);
    $this->Cell(20,7,'Bruto',1,0,'C',1);
    $this->Cell(20,7,'Tarra',1,0,'C',1);
    $this->Cell(20,7,'Netto',1,0,'C',1);
    $this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=0;

    if($data==array())
    {
        $this->Cell(220,7,'Tidak ada data','TBLR',0,'C');
    }
    else
    {
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
      $tot_bru+=$row["wei1st"];
      $tot_tar+=$row["wei2nd"];
      $tot_net+=$row["netto"];
   	}
   	$tanpaTotal = $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5];
   	// Total
   	$this->Cell($tanpaTotal,7,'Total   : ',0,0,'R');
   	$this->Cell($w[6],7,$tot_bru,'TBLR',0,'R');
   	$this->Cell($w[7],7,$tot_tar,'TBLR',0,'R');
   	$this->Cell($w[8],7,$tot_net,'TBLR',0,'R');

    }

	//$this->Cell(array_sum($w),0,'','T');
}
}

function cell_width ()
{
 $cell_width = array();
   array_push($cell_width,40);
   array_push($cell_width,20);
    array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
    array_push($cell_width,20);
    array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
 return $cell_width;
}
   $vehno=$_GET['vehno'];
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,3,4);
$query="select driver, ticketno, spno, date(datein),time(datein), time(dateout),wei1st,wei2nd,netto
        from wbridge.mstrxtbs
        where millcode='".$mill."' and substr(datein,6,2)='".$bi."' and productcode='".$produk."'
        and substr(datein,1,4)='".$thi."' and vehnocode='".$vehno."' order by datein";
// echo $query;
$hasil=mysql_query($query);
$result = array();
$i = 0;
$tot_jjg=0;
$tot_brt=0;
$tot_net=0;
while ($bar=mysql_fetch_assoc($hasil))
{
    $result[$i] = $bar;
    $i++;

}
//print_r($result);
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

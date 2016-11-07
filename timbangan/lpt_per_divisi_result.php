<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include('lpt_per_divisi.php');
$tanggal=$_GET['tanggal'];
$unit=$_GET['unit'];
$dv=$_GET['div'];
$USERID=$_SESSION['USERID'];
$tgl2=substr($tanggal,6,4).'-'.substr($tanggal,3,2).'-'.substr($tanggal,0,2);

class PDF extends FPDF
{
function Header()
{
  global $dbname;
   $unit=$_GET['unit'];
$tanggal=$_GET['tanggal'];
$USERID=$_SESSION['standard']['username'];
   $query="select unitname from ".$dbname.".msunit where unitcode='".$unit."'";
  //echo $query;
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];

   $dv=$_GET['div'];
   $query="select divcode from ".$dbname.".msdivisi where divcode='".$dv."'";
  //echo $query;
   $res=mysql_query($query);
   $bar2=mysql_fetch_array($res);
   $dv=$bar2[0];

  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Laporan Penerimaan TBS Per Divisi',0,0,'C');
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Tanggal :',0,0,'R');
   $this->Cell(20,10,$tanggal,'',0,'R');
   //Ganti baris
   $this->Ln(20);
   $this->SetFont('Arial','B',10);
   $this->Cell(300,10,'Dicetak Oleh :',0,0,'R');
   $this->Cell(13,10,$USERID,'',0,'R');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(300,10,'Tanggal Cetak :',0,0,'R');
   $this->Cell(20,10,date("d/m/Y"),'',0,'R');
   $this->Ln(5);
   $this->Cell(300,10,'Jam Cetak :',0,0,'R');
   $this->Cell(17,10,date("H:i:s"),'',0,'R');
   //Ganti baris
   $this->Ln(10);
   $this->Cell(17,10,'Unit     : ','',0,'L');
   $this->Cell(100,10,$un,'',0,'L');
   $this->Ln(5);

   $this->Cell(17,10,'Divisi  : ','',0,'L');
   $this->Cell(100,10,$dv,'',0,'L');

   //Ganti baris
   $this->Ln(10);

}
/*function Footer()
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

}*/

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
         $i<=12 && $i>=6 ? $align = 'R' : $align = 'C';
         $this->Cell($w[$i],7,$column,'TBLR',0,$align);
         $i++;
      }
      $this->Ln();
      $tot_jjg+=$row["jmlhjjg"];
      $tot_brt+=$row["beratkirim"];
      $tot_net+=$row["netto"];
       $tot_tarra+=$row["wei2nd"];
       $tot_bruto+=$row["wei1st"];
    $tot_pot+=$row["kgpotsortasi"];
    $tot_normal+=$row['normal'];       
   	}
   	$tanpaTotal = $w[0] + $w[1] + $w[2] + $w[3] + $w[4] + $w[5];
   	// Total
   	$this->Cell($tanpaTotal,7,'Total   : ',0,0,'R');
   	$this->Cell($w[6],7,$tot_jjg,'TBLR',0,'R');
   	$this->Cell($w[8],7,$tot_bruto,'TBLR',0,'R');
   	$this->Cell($w[9],7,$tot_tarra,'TBLR',0,'R');
   	$this->Cell($w[10],7,$tot_net,'TBLR',0,'R');
   	$this->Cell($w[7],7,$tot_pot,'TBLR',0,'R');
                    $this->Cell($w[7],7,$tot_normal,'TBLR',0,'R');   
}
}

function cell_width ()
{
 $cell_width = array();
   array_push($cell_width,30);
   array_push($cell_width,40);
    array_push($cell_width,30);
   array_push($cell_width,30);
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


//Column titles
$header = array('No. Tiket','No.SPB','No. Kendaraan','Nama Supir','Jam Masuk','Jam Keluar',
                'Jumlah JJG','Bruto','Tarra', 'Netto','Potongan','Normal');
// Query
$query="select TICKETNO2, spbno,vehnocode, driver,time(datein),time(dateout), jmlhjjg,  wei1st,wei2nd,netto,kgpotsortasi,(netto - kgpotsortasi) as normal
        from ".$dbname.".mstrxtbs where date(dateout)='".$tgl2."' and unitcode='".$unit."' and divcode='".$dv."' and OUTIN=0 and productcode='40000003'order by dateout";
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
    $tot_pot+=$bar["kgpotsortasi"];
    $tot_tarra+=$bar["wei2nd"];
    $tot_bruto+=$bar["wei1st"];
    $tot_normal+=$bar['normal'];
    $i++;

}
//echo $tot_bru;
//echo $bar[6];
// PDF
$pdf=new PDF('L','mm','Legal');
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

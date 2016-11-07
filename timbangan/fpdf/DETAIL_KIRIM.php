<?php
include('../connection.php');
$con=mysql_connect($host,$uname,$pwd) or die('Invalid Connections');
include('../detail_kirim.php');
require('fpdf2.php');

class PDF extends FPDF
{

function Header()
{
   $mill=$_GET['mill'];
   $produk=$_GET['produk'];
   $sipb=$_GET['sipb'];
   $tanggal=$_GET['tanggal'];
   $USERID=$_SESSION['USERID'];
   $query="select unitname from wbridge.msunit where unitcode='".$mill."'";
  // echo $query;
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
   
   $query1="select ctrno from wbridge.mssipb where sipbno='".$sipb."'";
   $hasil1=mysql_query($query1);
   $bar1=mysql_fetch_array($hasil1);
   $ctr=$bar1[0];

   $query1="select productname from wbridge.msproduct where productcode='".$produk."'";
   $hasil2=mysql_query($query1);
   $bar2=mysql_fetch_array($hasil2);
   $prodname=$bar2[0];
   
   $query2="select ctrqty from wbridge.mscontract where ctrno='".$ctr."'";
   $hasil3=mysql_query($query2);
   $bar3=mysql_fetch_array($hasil3);
   $ctrqty=$bar3[0];

   $query3="select sipbqty from wbridge.mssipb where sipbno='".$sipb."'";
   $hasil4=mysql_query($query3);
   $bar4=mysql_fetch_array($hasil4);
   $sipbqty=$bar4[0];

  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Detail Pengiriman',0,0,'C');
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Tanggal :',0,0,'R');
   $this->Cell(20,10,$tanggal,'',0,'R');
   $this->Ln(20);
   
   $this->Cell(25,10,'Mill  ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$un,0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Dicetak Oleh ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(40,10,$USERID,'',0,'L');
   $this->Ln(5);
   
    $this->Cell(25,10,'Produk  ','',0,'L');
   $this->Cell(3,10,':',0,0,'L');
  $this->Cell(200,10,$prodname,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Tanggal cetak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(35,10,date('d/m/Y h:m:s'),'',0,'L');
   $this->Ln(10);
   
   $this->Cell(25,10,'No. Kontrak  ','',0,'L');
  $this->Cell(3,10,':',0,0,'L');
   $this->Cell(60,10,$ctr,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Qty. Kontrak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(20,10,$ctrqty,'',0,'L');
   $this->Ln(5);
   
   $this->Cell(25,10,'No. SIPB  ','',0,'L');
  $this->Cell(3,10,':',0,0,'L');
   $this->Cell(60,10,$sipb,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Qty. SIPB ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(20,10,$sipbqty,'',0,'L');
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
        if($data==array())
    {
        $this->Cell(260,7,'Tidak ada data','TBLR',0,'C');
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
    }
   	$tanpaTotal = $w[0] + $w[1] + $w[2] + $w[3] + $w[4]+ $w[5] + $w[6];
   	// Total
   	$this->Cell($tanpaTotal,7,'Total   : ',0,0,'R');
   	$this->Cell($w[8],7,$tot_bru,'TBLR',0,'R');
   	$this->Cell($w[9],7,$tot_tar,'TBLR',0,'R');
   	$this->Cell($w[10],7,$tot_net,'TBLR',0,'R');

	//$this->Cell(array_sum($w),0,'','T');
}
}

function cell_width ()
{
 $cell_width = array();
   array_push($cell_width,30);
   array_push($cell_width,40);
    array_push($cell_width,20);
   array_push($cell_width,50);
   array_push($cell_width,60);
   array_push($cell_width,20);
    array_push($cell_width,20);
    array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
 return $cell_width;
}
$query="select trpcode,trpname from wbridge.msvendortrp order by trpcode";
$hasil=mysql_query($query);
while($bar1=mysql_fetch_assoc($hasil))
{
$trpname[$bar1['trpcode']]=$bar1['trpname'];
}
//print_r ($trpname);
//Column titles
$header = array('Truk','Nama Supir','No. Tiket','Transportir','No.SP','Jam Masuk','Jam Keluar',
                'Bruto','Tarra', 'Netto');
// Query
$query="select vehnocode,driver,ticketno, trpcode,spno, time(datein),time(dateout), wei1st,wei2nd,netto
        from wbridge.mstrxtbs where date(datein)='".$tgl2."' and millcode='".$mill."' and productcode=".$produk."
        and sipbno='".$sipb."' and outin=0 order by datein";
//echo $query;
$hasil=mysql_query($query);
$result = array();
$i = 0;
$tot_jjg=0;
$tot_brt=0;
$tot_net=0;
while ($bar=mysql_fetch_assoc($hasil))
{
    $result[$i]['no_urut'] = $i;
    $result[$i] = $bar;
    $result[$i]['trpcode'] = $trpname[$bar['trpcode']];
    $tot_jjg+=$bar["jmlhjjg"];
    $tot_brt+=$bar["beratkirim"];
    $tot_net+=$bar["netto"];
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

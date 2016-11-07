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
   $this->Cell(250,10,'Rekapitulasi Penerimaan TBS (Netto)',0,0,'C');
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Periode :',0,0,'R');
   $this->Cell(20,10,$periode,'',0,'R');
   $this->Ln(20);
   
   $this->Cell(25,10,'Mill  ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$un,0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Dicetak Oleh ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(40,10,$USERID,'',0,'L');
   $this->Ln(5);
   
   $this->Cell(228,10,'',0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Tanggal cetak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(35,10,date('d/m/Y h:m:s'),'',0,'L');
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
$unit=$_GET['mill'];
$orientation='L';
$format='A4';

// Query
$query="select wilcode from wbridge.msunit where unitcode='".$unit."'";
$hasil=mysql_query($query);
$res=mysql_fetch_assoc($hasil);
$wil=$res["wilcode"];
$query2="select unitcode from wbridge.msunit where wilcode='".$wil."' and unitcode like'%E'";
$hasil2=mysql_query($query2);

// Extract Query to Array
$unit = array();
while ($res2=mysql_fetch_assoc($hasil2))
{
   $unit[] = $res2;
}
$totalUnit = count($unit);


$query3="select trpname from wbridge.msvendortrp where trpcode like'1%'";
$hasil3=mysql_query($query3);

// Extract Query to Array
$vendor = array();
while ($res3=mysql_fetch_assoc($hasil3))
{
   $vendor[] = $res3;
}
$totalvendor = count($vendor);

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

    $this->Cell(30,14,'Tanggal',1,0,'C',1);
    $this->Cell(30*($totalUnit+1),7,'Internal',1,0,'C',1);
    $this->Cell(30*($totalvendor+1),7,'Eksternal',1,0,'C',1);
    $this->Ln();
    $this->Cell(30,7,'',0,0,'C',0);

// Extract to Display
foreach($unit as $res2)
{
   $this->Cell(30,7,$res2["unitcode"],1,0,'C',1);
}
   $this->Cell(30,7,'Total (Kg)',1,0,'C',1);

foreach($vendor as $res3)
{
   $this->Cell(30,7,$res3["trpname"],1,0,'C',1);
}
   $this->Cell(30,7,'Total (Kg)',1,0,'C',1);

    $this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=0;

    $tot_brt=0;
    $tot_net=0;
    if($data==array())
    {
        $this->Cell(210,7,'Tidak ada data','TBLR',0,'C');
    }
    else
    {
	foreach($data as $row)
	{
	$sisa=$row["budget"]-$row["realisasi"];
    $tanggal=substr($row["tanggal"],8,2).'-'.substr($row["tanggal"],5,2).'-'.substr($row["tanggal"],0,4);
   	$this->Cell(30,7,$row["sipb"],'TBLR',0,'R');
   	$this->Cell(30,7,$row["buyercode"],'TBLR',0,'R');
   	$this->Cell(30,7,$row["budget"],'TBLR',0,'R');
   	$this->Cell(30,7,$tanggal,'TBLR',0,'R');
   	$this->Cell(30,7,$row["realisasi"],'TBLR',0,'R');
   	$this->Cell(30,7,$sisa,'TBLR',0,'R');
   	$this->Cell(30,7,$row["ctrno"],'TBLR',0,'R');
      $i = 0;
      $this->Ln();
    }

    }

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
   array_push($cell_width,20);
    array_push($cell_width,20);
    array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
   array_push($cell_width,20);
 return $cell_width;
}

$query="select buyercode,buyername from wbridge.msvendorbuyer order by buyercode";
$hasil=mysql_query($query);
while($bar1=mysql_fetch_assoc($hasil))
{
$buyername[$bar1['buyercode']]=$bar1['buyername'];
}
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,3,4);
$query="select a.sipbno sipb, b.buyercode,c.sipbqty budget,date(a.datein) tanggal,a.sipbqty realisasi,a.ctrno
        from wbridge.mstrxtbs a, wbridge.mscontract b, wbridge.mssipb c
        where a.ctrno=c.ctrno and b.ctrno=c.ctrno and a.ctrno=b.ctrno
        and a.millcode='".$mill."' and a.productcode =".$produk." and substr(a.datein,6,2)='".$bi."'
        and substr(a.datein,1,4)='".$thi."' order by datein";
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
    $result[$i]['buyercode'] = $buyername[$bar['buyercode']];
    $tot_jjg+=$bar["jmlhjjg"];
    $tot_brt+=$bar["beratkirim"];
    $tot_net+=$bar["netto"];
    $tgl=$bar["tanggal"];
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

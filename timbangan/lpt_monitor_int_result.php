<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include('ambil_monitoring.php');
$tanggal=$_GET['tanggal'];
$tgl=substr($tanggal,6,4).'-'.substr($tanggal,3,2).'-'.substr($tanggal,0,2);
$array_bawah=Array('00:00:00','01:00:00','02:00:00','03:00:00','04:00:00','05:00:00','06:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00');
$array_atas=Array('01:00:00','02:00:00','03:00:00','04:00:00','05:00:00','06:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00','23:59:59');

#ambil unitkerja
$unit=Array();
$str="select unitcode from ".$dbname.".msunit order by unitcode";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
 $unit[]=$bar->unitcode;
}
for($x=0;$x<=23;$x++)
{
	$query="select sum(netto-kgpotsortasi) as kg,unitcode from ".$dbname.".mstrxtbs where (unitcode!=''  or unitcode is null) and right(dateout,8)>'".$array_bawah[$x]."'
	and right(dateout,8)<='".$array_atas[$x]."' and outin=0 and dateout like '".$tgl."%'
	and productcode='40000003' group by unitcode order by unitcode";
    $res=mysql_query($query);
	while($bar=mysql_fetch_object($res)){
       $hasil[$bar->unitcode][$x]=$bar->kg;
	}	
}

class PDF extends FPDF
{

function Header()
{
   global $dbname;
   global $unit;	
   $tanggal=$_GET['tanggal'];
   $tgl=substr($tanggal,8,2).'-'.substr($tanggal,5,2).'-'.substr($tanggal,0,4);
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
   $USERID=$_SESSION['standard']['username'];
   $query="select MILLNAME from ".$dbname.".mssystem";
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
   
  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Monitoring Penerimaan TBS Internal Per Jam',0,0,'C');
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Tanggal :',0,0,'R');
   $this->Cell(20,10,$tanggal,'',0,'R');
   $this->Ln(15);
   
   $this->Cell(25,10,'Mill  ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$un,0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Dicetak Oleh ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(40,10,$USERID,'',0,'L');
   $this->Ln(5);

   $this->Cell(228,10,$prodname,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Tanggal cetak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(35,10,date('d/m/Y h:i:s'),'',0,'L');
   $this->Ln(15);
   
   #print header kolom
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255,255,255);   
   $this->Cell(40,5,'Jam','TBLR',0,'C',1);
   foreach ($unit as $key=>$val){
      $this->Cell(25,5,$val,'TBLR',0,'C',1);
   }
   $this->Cell(25,5,'TOTAL','TBLR',1,'C',1);   
}
}
$pdf=new PDF('L','mm','Legal');
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
for($x=0;$x<=23;$x++)
{
  $stot=0;
  $pdf->Cell(40,5,$array_bawah[$x].'-'.$array_atas[$x],'TBLR',0,'C');
  foreach($unit as $key=>$val){
    $pdf->Cell(25,5,number_format($hasil[$val][$x], 2, '.', ','),'TBLR',0,'R');
	$stot+=$hasil[$val][$x];
	$gt[$val]+=$hasil[$val][$x];
  } 
  $pdf->Cell(25,5,number_format($stot, 2, '.', ','),'TBLR',1,'R');
}
#print grand total
  $pdf->Cell(40,5,TOTAL,'TBLR',0,'C');
 foreach($unit as $key=>$val){
   $pdf->Cell(25,5,number_format($gt[$val], 2, '.', ','),'TBLR',0,'R');
   $gtX+=$gt[$val];
 }
   $pdf->Cell(25,5,number_format($gtX, 2, '.', ','),'TBLR',1,'R');
$pdf->Output();
?>

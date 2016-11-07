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
   $tanggal=$_GET['tanggal'];
   $tgl=substr($tanggal,8,2).'-'.substr($tanggal,5,2).'-'.substr($tanggal,0,4);
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
   $USERID=$_SESSION['USERNAME'];
   $query="select unitname from wbridge.msunit where unitcode='".$mill."'";
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
   
//
//   $query1="select productname from wbridge.msproduct where productcode=".$produk."";
//   $hasil2=mysql_query($query1);
//   $bar2=mysql_fetch_array($hasil2);
//   $prodname=$bar2[0];
   
  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Monitoring Penerimaan TBS Per Jam',0,0,'C');
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
       
     $mill=$_GET['mill'];
     $tanggal=$_GET['tanggal'];
     $tgl=substr($tanggal,6,4).'-'.substr($tanggal,3,2).'-'.substr($tanggal,0,2);
     $this->Cell(22,7,'Jam',1,0,'C',1);
     $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4 and millcode='".$mill."'
             and outin=0 and dateout like '".$tgl."%' order by unitcode";

     $hasil=mysql_query($query);
          while($res=mysql_fetch_array($hasil))
             {
                 $this->Cell(22,7,$res[0],1,0,'C',1);
             }
    $this->Cell(22,7,'Total',1,0,'C',1);

    $this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	
	//Data
	$fill=0;

	foreach($data as $row)
	{
    $array_bawah=Array('00:00:00','01:00:00','02:00:00','03:00:00','04:00:00','05:00:00','06:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00');
    $array_atas=Array('01:00:00','02:00:00','03:00:00','04:00:00','05:00:00','06:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00','23:59:59');
    for($x=0;$x<=23;$x++)
    {
      $totalunit=0;
      $totalvendor=0;
      //untuk kolom Jam
      $jam=substr($array_bawah[$x],0,5).' - '.substr($array_atas[$x],0,5);
   	  $this->Cell(22,7,$jam,'TBLR',0,'R');

   	  //untuk kolom2 per unit
      $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4  and millcode='".$mill."' and outin=0 and dateout like '".$tgl."%'
             order by unitcode";
      $hasil=mysql_query($query);
      while ($res2=mysql_fetch_array($hasil))
      {
        $query="select sum(netto) from wbridge.mstrxtbs where unitcode='".$res2[0]."' and right(dateout,8)>'".$array_bawah[$x]."'
                and right(dateout,8)<='".$array_atas[$x]."' and millcode='".$mill."' and outin=0 and dateout like '".$tgl."%'
                and productcode='40000003' order by unitcode";
				
				//echo $query;
				//exit;
        $res=mysql_query($query);
        while ($bar=mysql_fetch_array($res))
        {
          $totalunit+=$bar[0];
   	      $this->Cell(22,7,number_format($bar[0], 2, '.', ','),'TBLR',0,'R');
  	    }
		//echo $query;
   	  }
      //untuk total baris
   	  $totalbaris=$totalunit;
   	  $totalsemua+=$totalbaris;
  	  $this->Cell(22,7,number_format($totalbaris, 2, '.', ','),'TBLR',0,'R');
      $this->Ln();
    }
    //untuk total unit per kolom
   	$this->Cell(22,7,'Total','TBLR',0,'R',1);
    $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4
             and millcode='".$mill."' and outin=0 and dateout like '".$tgl."%' order by unitcode";
    $hasil=mysql_query($query);
    while ($res2=mysql_fetch_array($hasil))
    {
        $totalunit=0;
        $query="select sum(netto) from wbridge.mstrxtbs where unitcode='".$res2[0]."'
                and right(dateout,8)>'00:00:00' and right(dateout,8)<='23:59:59' and millcode='".$mill."' and outin=0 and dateout like '".$tgl."%'
                and productcode='40000003' order by unitcode";
        //echo $query;
        $res=mysql_query($query);
        while ($bar2=mysql_fetch_array($res))
        {
          $totalunit+=$bar2[0];
   	    }
         $this->Cell(22,7,number_format($totalunit, 2, '.', ','),'TBLR',0,'R',1);
    }
    //untuk total keseluruhan
   	$this->Cell(22,7,number_format($totalsemua, 2, '.', ','),'TBLR',0,'R',1);
    $this->Ln();
   }
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
// PDF
$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->FancyTable($header,$result);
$pdf->Output();
?>

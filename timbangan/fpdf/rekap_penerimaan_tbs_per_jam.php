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
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
   $USERID=$_SESSION['USERNAME'];
   $USERNAME=$_SESSION['USERNAME'];
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
   $this->Cell(250,10,'Rekap Penerimaan TBS (Netto)',0,0,'C');
   //Ganti baris
   $this->Ln(10);
   $this->SetFont('Arial','B',10);
   $this->Cell(160,10,'Periode :',0,0,'R');
   $this->Cell(20,10,$periode,'',0,'R');
   $this->Ln(15);
   
   $this->Cell(25,10,'Mill  ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(200,10,$un,0,0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Dicetak Oleh ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(40,10,$USERNAME,'',0,'L');
   $this->Ln(5);

  $this->Cell(228,10,$prodname,'',0,'L');
   $this->SetFont('Arial','B',10);
   $this->Cell(30,10,'Tanggal cetak ',0,0,'L');
   $this->Cell(3,10,':',0,0,'L');
   $this->Cell(35,10,date('d/m/Y h:m:s'),'',0,'L');
   $this->Ln(15);



}

//Colored table
function FancyTable($header,$data)
{
$orientation='L';
     //$format='A4';
	 $format='A3';
    //Colors, line width and bold font
	$this->SetFillColor(255,0,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	$this->FontSizePt=8;
	//Header
	$w=cell_width();
	for($i=0;$i<count($header);$i++)
       $this->Cell($w[$i],7,$header[$i],1,0,'C',1);
       
   $mill=$_GET['mill'];
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
     $this->Cell(8,7,'Tgl',1,0,'C',1);
    /*
 $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4 and millcode='".$mill."'
             and outin='0' and substr(date(datein),6,2)='".$bi."' order by unitcode";

     $hasil=mysql_query($query);
     while($res=mysql_fetch_array($hasil))
     {
    	$this->Cell(30,7,$res[0],1,0,'C',1);
     }
*/
	for($x=0;$x<=17;$x++)
    {
		$array_bawah=Array('00:00:00','07:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00');
	    $array_atas=Array('01:00:00','08:00:00','09:00:00','10:00:00','11:00:00','12:00:00','13:00:00','14:00:00','15:00:00','16:00:00','17:00:00','18:00:00','19:00:00','20:00:00','21:00:00','22:00:00','23:00:00','23:59:59');
		$jam=substr($array_bawah[$x],0,5).' - '.substr($array_atas[$x],0,5);
		$this->Cell(18,7,$jam,1,0,'C',1);
	}
     /*$query="select distinct trpcode from wbridge.mstrxtbs where unitcode is null and trpcode like '10%'
             and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by trpcode";
     $hasil=mysql_query($query);
     while($res2=mysql_fetch_array($hasil))
     {
     $query2="select distinct trpname from wbridge.msvendortrp where trpcode='".$res2[0]."'
              order by trpname";
     $hasil2=mysql_query($query2);
     while ($res3=mysql_fetch_array($hasil2))
     {
    	$this->Cell(30,7,$res3[0],1,0,'C',1);
     }
     } */
    $this->Cell(15,7,'Total',1,0,'C',1);

    $this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	
	//Data
	$fill=0;

	foreach($data as $row)
	{
    for($x=1;$x<=31;$x++)
    {
      if ($x<10)
      {
      $x='0'.$x;
      }
      else
      {
       $x=$x;
      }
   	  $this->Cell(15,7,$x,'TBLR',0,'C');

       $totalunit=0;
      $totalvendor=0;
   	  //untuk kolom2 per unit
      $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4
               and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by unitcode";
      $hasil=mysql_query($query);
      while ($res2=mysql_fetch_array($hasil))
      {
        $query="select sum(netto) from wbridge.mstrxtbs where unitcode='".$res2[0]."'
                and substr(date(datein),9,2)='".$x."'  and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by unitcode";
        $res=mysql_query($query);
        if (mysql_num_rows($res)<1)
        {
   	      $this->Cell(30,7,'0','TBLR',0,'R');
        }
        else {
        while ($bar=mysql_fetch_array($res))
        {
          $totalunit+=$bar[0];
   	      $this->Cell(30,7,number_format($bar[0], 2, '.', ','),'TBLR',0,'R');
  	    }
   	  }
   	  }
      //untuk kolom2 per vendor
      /*$query="select distinct trpcode from wbridge.mstrxtbs where unitcode is null and trpcode like '10%'
               and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by trpcode";
      $hasil=mysql_query($query);
      while($res2=mysql_fetch_array($hasil))
      {
        $query="select sum(netto) from wbridge.mstrxtbs where TRPCODE='".$res2[0]."'
                 and substr(date(datein),9,2)='".$x."'  and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by TRPCODE";
        $res=mysql_query($query);
        while ($bar1=mysql_fetch_array($res))
        {
          $totalvendor+=$bar1[0];
     	  $this->Cell(30,7,number_format($bar1[0], 2, '.', ','),'TBLR',0,'R');
   	    }
   	  }*/
      //untuk total baris
      $totalbaris=0;
   	  //$totalbaris=$totalunit+$totalvendor;
   	  $totalbaris=$totalunit;
   	  $totalsemua+=$totalbaris;
  	  $this->Cell(30,7,number_format($totalbaris, 2, '.', ','),'TBLR',0,'R');
      $this->Ln();
    }
    //untuk total unit per kolom
   	$this->Cell(15,7,'Total','TBLR',0,'R',1);
    $query="select distinct unitcode from wbridge.mstrxtbs where length(unitcode)=4
            and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by unitcode";
    $hasil=mysql_query($query);
    while ($res2=mysql_fetch_array($hasil))
    {
        $totalunit=0;
        $query="select sum(netto) from wbridge.mstrxtbs where unitcode='".$res2[0]."'
               and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."'
                order by unitcode";
        //echo $query;
        $res=mysql_query($query);
        while ($bar2=mysql_fetch_array($res))
        {
          $totalunit+=$bar2[0];
   	    }
         $this->Cell(30,7,number_format($totalunit, 2, '.', ','),'TBLR',0,'R',1);
    }

      //untuk total vendor per kolom
    /*$query="select distinct trpcode from wbridge.mstrxtbs where unitcode is null and trpcode like '10%'
            and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by trpcode";
    $hasil=mysql_query($query);
    while($res2=mysql_fetch_array($hasil))
    {
      $query="select sum(netto) from wbridge.mstrxtbs where unitcode='".$res2[0]."'
      and substr(date(datein),9,2)='".$x."'  and millcode='".$mill."' and outin='0'
                and substr(date(datein),6,2)='".$bi."' order by unitcode";
      $res=mysql_query($query);
      while ($bar1=mysql_fetch_array($res))
      {
        $totalvendor+=$bar1[0];
        $this->Cell(30,7,number_format($totalvendor, 2, '.', ','),'TBLR',0,'R',1);
      }
    } */
    //untuk total keseluruhan
   	$this->Cell(30,7,number_format($totalsemua, 2, '.', ','),'TBLR',0,'R',1);
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
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include('ambil_monitoring.php');

class PDF extends FPDF
{

function Header()
{
   global $dbname;
   //$mill=$_GET['mill'];
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,4,4);
   $USERNAME=$_SESSION['standard']['username'];
   $query="select MILLNAME from ".$dbname.".mssystem";
   $res=mysql_query($query);
   $bar=mysql_fetch_array($res);
   $un=$bar[0];
   
  $this->SetFont('Helvetica','B',20);
   //Geser ke kanan
   $this->Cell(40);
   //Judul dalam bingkai
   $this->Cell(250,10,'Rekap Penerimaan TBS Internal Per Bulan',0,0,'C');
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
   $this->Cell(35,10,date('d/m/Y h:i:s'),'',0,'L');
   $this->Ln(15);



}

//Colored table
function FancyTable($header,$data)
{
	global $dbname;
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

   //$mill=$_GET['mill'];
   $periode=$_GET['periode'];
   $bi=substr($periode,0,2);
   $thi=substr($periode,3,4);
     $this->Cell(15,7,'Tanggal',1,0,'C',1);
     $query="select distinct unitcode from ".$dbname.".mstrxtbs where length(unitcode)=4
                  and outin='0' and dateout like '".$thi."-".$bi."%' order by unitcode";
     $hasil=mysql_query($query);
     while($res=mysql_fetch_array($hasil))
     {
    $this->Cell(30,7,$res[0],1,0,'C',1);
     }
     
    $this->Cell(30,7,'Total',1,0,'C',1);

    $this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	$this->bulan = (int) $bi;
			$bln = $this->bulan;
			$this->tahun = (int) $thi;
			$tahun = $this->tahun;
			$numdays=date('t',mktime(0,0,0,$bln,1,$tahun));
	//Data
	$fill=0;

	foreach($data as $row)
	{
    for($x=1;$x<=$numdays;$x++)
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
      $query="select distinct unitcode from ".$dbname.".mstrxtbs where length(unitcode)=4
                    and outin='0' and dateout like '".$thi."-".$bi."%' order by unitcode";

      $hasil=mysql_query($query);
      while ($res2=mysql_fetch_array($hasil))
      {
        $query="select sum(netto-kgpotsortasi) from ".$dbname.".mstrxtbs where unitcode='".$res2[0]."'
                and substr(date(dateout),9,2)='".$x."' and outin='0'
                and dateout like '".$thi."-".$bi."%' and productcode='40000003' order by unitcode";
				
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
     
      //untuk total baris
      $totalbaris=0;
   	  //$totalbaris=$totalunit+$totalvendor;
   	  $totalbaris=$totalunit;
  	  $this->Cell(30,7,number_format($totalbaris, 2, '.', ','),'TBLR',0,'R');
      $this->Ln();
    }
    //untuk total unit per kolom kotor
    $this->Cell(15,7,'Kotor','TBLR',0,'R',1);
    $query="select distinct unitcode from ".$dbname.".mstrxtbs where length(unitcode)=4
                 and outin='0' and dateout like '".$thi."-".$bi."%' order by unitcode";
    $hasil=mysql_query($query);
    while ($res2=mysql_fetch_array($hasil))
    {
        $totalunit=0;
        $query="select sum(netto) as netto from ".$dbname.".mstrxtbs where unitcode='".$res2[0]."'
                        and outin='0' and dateout like '".$thi."-".$bi."%' and productcode='40000003'
                        order by unitcode";
        //echo $query;
        $res=mysql_query($query);
        while ($bar2=mysql_fetch_array($res))
        {
          $totalunit=$bar2[0];
          $totalsemuakot+=$bar2[0];
             }
         $this->Cell(30,7,number_format($totalunit, 2, '.', ','),'TBLR',0,'R',1);
    }

    //untuk total keseluruhan kotor
      $this->Cell(30,7,number_format($totalsemuakot, 2, '.', ','),'TBLR',1,'R',1);
      
      
   //total keseluruhan potongan
    $this->Cell(15,7,'Potongan','TBLR',0,'R',1);
    $query="select distinct unitcode from ".$dbname.".mstrxtbs where length(unitcode)=4
                 and outin='0' and dateout like '".$thi."-".$bi."%' order by unitcode";
    $hasil=mysql_query($query);
    while ($res2=mysql_fetch_array($hasil))
    {
        $totalunit=0;
        $query="select sum(kgpotsortasi) as netto from ".$dbname.".mstrxtbs where unitcode='".$res2[0]."'
                        and outin='0' and dateout like '".$thi."-".$bi."%' and productcode='40000003'
                        order by unitcode";
        //echo $query;
        $res=mysql_query($query);
        while ($bar2=mysql_fetch_array($res))
        {
          $totalunit=$bar2[0];
          $totalsemuapot+=$bar2[0];
             }
         $this->Cell(30,7,number_format($totalunit, 2, '.', ','),'TBLR',0,'R',1);
    }

    //untuk total keseluruhan potongan
      $this->Cell(30,7,number_format($totalsemuapot, 2, '.', ','),'TBLR',1,'R',1);   

      
   //total keseluruhan potongan
    $this->Cell(15,7,'Normal','TBLR',0,'R',1);
    $query="select distinct unitcode from ".$dbname.".mstrxtbs where length(unitcode)=4
                 and outin='0' and dateout like '".$thi."-".$bi."%' order by unitcode";
    $hasil=mysql_query($query);
    while ($res2=mysql_fetch_array($hasil))
    {
        $totalunit=0;
        $query="select sum(netto-kgpotsortasi) as netto from ".$dbname.".mstrxtbs where unitcode='".$res2[0]."'
                        and outin='0' and dateout like '".$thi."-".$bi."%' and productcode='40000003'
                        order by unitcode";
        //echo $query;
        $res=mysql_query($query);
        while ($bar2=mysql_fetch_array($res))
        {
          $totalunit=$bar2[0];
          $totalsemuanor+=$bar2[0];
             }
         $this->Cell(30,7,number_format($totalunit, 2, '.', ','),'TBLR',0,'R',1);
    }

    //untuk total keseluruhan normal
      $this->Cell(30,7,number_format($totalsemuanor, 2, '.', ','),'TBLR',1,'R',1);         
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
$pdf=new PDF('L','mm','Legal');
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->FancyTable($header,$result);
$pdf->Output();
?>
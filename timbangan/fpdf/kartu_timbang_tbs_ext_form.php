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
		$VEHNO=$bar->VEHNOCODE;$THNTNM=$bar->TAHUNTANAM;$THNTNM2=$bar->TAHUNTANAM2;$THNTNM3=$bar->TAHUNTANAM3;
		$SPBNO=$bar->SPBNO;$TICKETNO2=$bar->TICKETNO2;
		$TRPCODE=$bar->TRPCODE;
		$DRIVER=$bar->DRIVER;
		$UNIT=$bar->UNITCODE;
		$DIV=$bar->DIVCODE;
		$JJG=$bar->JMLHJJG;$JJG2=$bar->JMLHJJG2;$JJG3=$bar->JMLHJJG3;
		$BRONDOLAN=$bar->BRONDOLAN;$BRONDOLAN2=$bar->BRONDOLAN2;$BRONDOLAN3=$bar->BRONDOLAN3;
		$BERATKIRIM=$bar->BERATKIRIM;
		$DATEIN=$bar->DATEIN;$DATEOUT=$bar->DATEOUT;
		$WEIGH1=$bar->WEI1ST;$WEIGH2=$bar->WEI2ND;$NETTO=$bar->NETTO;
		$tglin=substr($DATEIN,8,2)."-".substr($DATEIN,5,2)."-".substr($DATEIN,0,4)." ".substr($DATEIN,11,2).":".substr($DATEIN,14,2).":".substr($DATEIN,17,2);
		$tglout=substr($DATEOUT,8,2)."-".substr($DATEOUT,5,2)."-".substr($DATEOUT,0,4)." ".substr($DATEOUT,11,2).":".substr($DATEOUT,14,2).":".substr($DATEOUT,17,2);
	        $potongan=$bar->KGPOTSORTASI;
                
        }
	//Ambil jenis kendaraan:
	$stru="select vehtypename from ".$dbname.".truk_vw where VEHNOCODE='".$VEHNO."'";
	$resu=mysql_query($stru);
	$tipekend='';
	while($baru=mysql_fetch_object($resu))
	{
	 $tipekend=$baru->vehtypename;
	}		
	//$str2="select TRPNAME from ".$dbname.".msvendortrp where TRPCODE='".$TRPCODE."'";
	$str2="select BUYERNAME from ".$dbname.".msvendorbuyer where BUYERCODE='".$TRPCODE."'";
	$res2=mysql_query($str2);
	while ($bar1=mysql_fetch_object($res2)){
		$TRPNAME=$bar1->BUYERNAME;
	}
	//$this->Image('minanga.jpg',22,4.5,200,20,jpg);
	//$this->Image('BGA Logo.jpg',170,5,20,20,jpg);
  $this->SetFont('times','B',20);
   //Geser ke kanan
   //$this->Cell(10);
   //Judul dalam bingkai
   $this->Cell(170,10,'PT. NAFASINDO',0,0,'C');
   //Ganti baris

   $this->Ln(10);
   $this->SetFont('Times','B',12);
   $this->Cell(166,10,'No. Tiket           :',0,0,'R');
   $this->Cell(17,10,$TICKETNO2,'',0,'R');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(166,10,'Tanggal Cetak  :',0,0,'R');
   $this->Cell(21,10,date("d/m/Y"),'',0,'R');
   $this->Ln(5);

   //Ganti baris
   //$this->Ln(10);
   $this->Cell(27,10,'No. Kendaraan : ',0,0,'R');
   $this->Cell(200,10,$VEHNO." - ".$tipekend,'',0,'L');
   //Ganti baris
   $this->Ln(10);
   $this->Line(0,40,210,40);
	//Ganti baris
   //$this->Ln(5);
   $this->Cell(39.5,10,'Unit/Estate Pengirim  : ',0,0,'R');
   $this->Cell(50,10,$TRPNAME,0,0,'L');
   //$this->Cell(60,10,'Jml.JJG   :',0,0,'R');
   //$this->Cell(50,10,$JJG,'',0,'L');
   $this->Cell(60.5,10,'Supir        :',0,0,'R');
   $this->Cell(50,10,$DRIVER,'',0,'L');
   //Ganti baris
   $this->Ln(5);
   $this->Cell(39.5,10,'Product                        : ',0,0,'R');
   $this->Cell(50,10,'TBS',0,0,'L');

	//Ganti baris
   $this->Ln(5);
   //$this->Cell(32.5,10,'Unit/Estate Pengirim  : ',0,0,'R');
   $this->Cell(39.5,10,'Timbang I                : ',0,0,'R');
   $this->Cell(50,10,$tglin,0,0,'L');
   $this->Cell(11.5,10,$WEIGH1,0,0,'R');
   $this->Cell(10,10,'Kg','',0,'L');
   $this->Ln(5);
   $this->Cell(39.5,10,'Timbang II              : ',0,0,'R');
   $this->Cell(50,10,$tglout,0,0,'L');
   $this->Cell(11.5,10,$WEIGH2,0,0,'R');
   $this->Cell(40,10,'Kg','',0,'L');
   //$this->Ln(2.5);
   $this->Line(98,62.5,120,62.5);
   $this->Ln(5);
   $this->Cell(39.5,10,'Netto                             : ',0,0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(11.5,10,$NETTO,0,0,'R');
   $this->Cell(10,10,'Kg','',0,'L');
   $this->Ln(5);
   $this->Cell(39.5,10,'Potongan                      : ','I',0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(11.5,10,$potongan,0,0,'R');
   $this->Cell(10,10,'Kg','',0,'L');
   $this->Line(98,72.5,120,72.5);
   $this->Ln(5);  
   $this->SetFont('','I');
   $this->Cell(39.5,10,'Hasil                      : ',0,0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(11.5,10,($NETTO-$potongan),0,0,'R');
   $this->Cell(10,10,'Kg','',0,'L');
   $this->SetFont('Times','B',12);
   $this->Ln(5);
   $this->Cell(39.5,10,'No.SPB                         : ',0,0,'R');
   $this->Cell(50,10,$SPBNO,0,0,'L');


   $this->Ln(5);
   $this->Cell(39.45,10,'Thn.Tnm                      : ',0,0,'R');
   $this->Cell(40,10,$THNTNM,0,0,'L');
   $this->Cell(23,10,'Jml.JJG   :',0,0,'R');
   $this->Cell(10,10,$JJG,0,0,'L');
   $this->Cell(44.25,10,'Brondolan   :',0,0,'R');
   $this->Cell(10,10,$BRONDOLAN,0,0,'L');
   $this->Cell(20,10,'Kg','',0,'L');
   $this->Ln(5);
   $this->Cell(39.45,10,'Thn.Tnm II                  : ',0,0,'R');
   $this->Cell(40,10,$THNTNM2,0,0,'L');
   $this->Cell(23,10,'Jml.JJG   :',0,0,'R');
   $this->Cell(10,10,$JJG2,0,0,'L');
   $this->Cell(44.25,10,'Brondolan   :',0,0,'R');
   $this->Cell(10,10,$BRONDOLAN2,0,0,'L');
   $this->Cell(20,10,'Kg','',0,'L');
   $this->Ln(5);
   $this->Cell(39.5,10,'Thn.Tnm III                 : ',0,0,'R');
   $this->Cell(40,10,$THNTNM3,0,0,'L');
   $this->Cell(23,10,'Jml.JJG   :',0,0,'R');
   $this->Cell(10,10,$JJG3,0,0,'L');
   $this->Cell(44.25,10,'Brondolan   :',0,0,'R');
   $this->Cell(10,10,$BRONDOLAN3,0,0,'L');
   $this->Cell(20,10,'Kg','',0,'L');
   $this->Line(0,97,210,97);   
   $this->Ln(5);
   $this->Cell(39.5,10,'Operator                       : ',0,0,'R');
   $this->Cell(50,10,$USERNAME,0,0,'L');
   $this->Cell(60.5,10,'Td.Tangan',0,0,'R');
   //$this->Ln(5);
   $this->Line(10,115,50,115);
   $this->Ln(18);
   $this->Cell(30,10,'P.Bongkar',0,0,'R');
     $this->Line(136,115,165,115);
   $this->Cell(39,10,'',0,0,'R');
   $this->Cell(50,10,'',0,0,'L');
   $this->Cell(35,10,'Checked By',0,0,'R');


}


//Colored table

}

$pdf=new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',8);
$pdf->Output();
?>

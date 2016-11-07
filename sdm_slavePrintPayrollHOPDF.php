<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');	
require_once('lib/fpdf.php');
require_once('config/connection.php');
$periode=$_GET['periode'];
$tipe  =$_GET['tipe'];
$username=$_GET['username'];
if($username=='')
{
	$username=$_SESSION['standard']['username'];
}
else
{
	$username=$username;
}	
	
//+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
{
var $col=0;

function SetCol($col)
	{
	    //Move position to a column
	    $this->col=$col;
	    $x=10+$col*100;
	    $this->SetLeftMargin($x);
	    $this->SetX($x);
	}

function AcceptPageBreak()
	{ 
			if($this->col<1)
		    {
		        //Go to next column
		        $this->SetCol($this->col+1);
		        $this->SetY(10);
		        return false;
		    }
		    else
		    {
		        //Go back to first column and issue page break
				$this->SetCol(0);
		        return true;
		    }
	}

	function Header()
	{    
	}
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',5);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}
}
	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',5);
//	$pdf->SetY(5);
//	$pdf->SetX(5);
	
 $arrMinusId=Array();
 $arrMinusName=Array();
 $str="select id,name from ".$dbname.".sdm_ho_component where plus=0 order by id";
 $res=mysql_query($str,$conn);
 while($bar=mysql_fetch_object($res))
 {
 	array_push($arrMinusId,$bar->id);
	array_push($arrMinusName,$bar->name);
 }
//samakan
 $arrPlusId=$arrMinusId;
 $arrPlusName=$arrMinusName;
 //Kosongkan
 for($r=0;$r<count($arrMinusId);$r++)
 {
 	 $arrPlusId[$r]='';
	 $arrPlusName[$r]='';
 }
 $str="select id,name from ".$dbname.".sdm_ho_component where plus=1 order by id";
 $res=mysql_query($str,$conn);
 $n=-1;
 while($bar=mysql_fetch_object($res))
 {
 	$n+=1;
	$arrPlusId[$n]=$bar->id;
	$arrPlusName[$n]=$bar->name;
 }

if($tipe=='thr')
{
  $arrPlusName[0]='Tunj. Hari Raya/THR';
}
if($tipe=='jaspro')
{
  $arrPlusName[0]='Jasa Produksi/Bonus';
}

//get All user id from employee table
$str1="select distinct e.karyawanid,e.name from ".$dbname.".sdm_ho_employee e,".$dbname.".sdm_ho_detailmonthly m
       where e.operator='".$username."'
	   and e.karyawanid=m.karyawanid and periode='".$periode."'
       order by e.name";
$res1=mysql_query($str1,$conn);
$jml=0;
while($bar1=mysql_fetch_object($res1))
{
	$jml+=1;
	//kosongkan array plus
	
	$arrValPlus=Array();
	$arrValMinus=Array();
	for($x=0;$x<count($arrPlusId);$x++)
	{
		$arrValPlus[$x]=0;
		$arrValMinus[$x]=0;
	}
	//get terilang
	$terbilang='';
	$strg="select terbilang from ".$dbname.".sdm_ho_payrollterbilang
	       where userid=".$bar1->karyawanid." and periode='".$periode."'
		   and `type`='".$tipe."'";
	$resg=mysql_query($strg,$conn);
	while($barg=mysql_fetch_object($resg))
	{
		$terbilang=$barg->terbilang;
	}	   
   //get jabatan, departement, tmk
   $stg="select a.kodejabatan,a.tanggalmasuk,a.bagian,b.namajabatan from ".$dbname.".datakaryawan a
         left join ".$dbname.".sdm_5jabatan b
		 on a.kodejabatan=b.kodejabatan
         where a.karyawanid=".$bar1->karyawanid;	 
	$reg=mysql_query($stg,$conn);
	$tglmasuk='';
	$title='';
	$dept='';
	while($barg=mysql_fetch_object($reg))
	{
		$tglmasuk=tanggalnormal($barg->tanggalmasuk);	
		$dept=$barg->bagian;	
		$title=$barg->namajabatan;
	}	 

	  
	$str3="select component,value,plus from ".$dbname.".sdm_ho_detailmonthly
	       where karyawanid=".$bar1->karyawanid." and periode='".$periode."'
		   and `type`='".$tipe."'";
	$res3=mysql_query($str3,$conn);
	while($bar3=mysql_fetch_object($res3))
	{
		for($g=0;$g<count($arrPlusId);$g++)
		{
		    if($bar3->component==$arrPlusId[$g])
			{
				$arrValPlus[$g]=$bar3->value;
			}
			if($bar3->component==$arrMinusId[$g])
			{
				$arrValMinus[$g]=$bar3->value;
			}			
		}	
	}	 
	
	$pdf->Image('images/logo.jpg',$pdf->GetX(),$pdf->GetY(),10);
	$pdf->SetX($pdf->getX()+8);
    $pdf->SetFont('Arial','B',8);	
	$pdf->Cell(70,5,'PT.PERKEBUNAN MINANGA OGAN',0,1,'L');
    $pdf->SetFont('Arial','',5);	
	$pdf->Cell(60,3,'PAY SLYP/SLIP GAJI : '.numToMonth(substr($periode,5,2),'I','short')." ".substr($periode,0,4),'T',0,'L');
    $pdf->SetFont('Arial','',4);
		$pdf->Cell(20,3,'Printed on: '.date('d-m-Y: H:i:s'),"T",1,'R');
    $pdf->SetFont('Arial','',5);		
	$pdf->Cell(10,3,'NIP/TMK',0,0,'L');
		$pdf->Cell(30,3,": ".$bar1->karyawanid." / ".$tglmasuk,0,0,'L');
	$pdf->Cell(15,3,'UNIT/BAGIAN',0,0,'L');	
		$pdf->Cell(25,3,': '.$dept,0,1,'L');		
	$pdf->Cell(10,3,'NAMA',0,0,'L');
		$pdf->Cell(30,3,': '.$bar1->name,0,0,'L');	
	$pdf->Cell(15,3,'JABATAN',0,0,'L');
		$pdf->Cell(25,3,':'.$title,0,1,'L');	
	$pdf->Cell(40,3,'PENAMBAH','TB',0,'C');
	$pdf->Cell(40,3,'PENGURANG','TB',1,'C');
	for($mn=0;$mn<count($arrPlusId);$mn++)
	{
		$pdf->Cell(20,3,$arrPlusName[$mn],0,0,'L');
		if($arrPlusName[$mn]=='')
		{
		  $pdf->Cell(2,3,"",0,0,'L');
		  $pdf->Cell(18,3,'','R',0,'R');
		}
		else
		{
		  $pdf->Cell(2,3,":Rp.",0,0,'L');
			  $pdf->Cell(18,3,number_format($arrValPlus[$mn],2,'.',','),'R',0,'R');
		}
		$pdf->Cell(20,3,$arrMinusName[$mn],0,0,'L');
		if($arrMinusName[$mn]=='')
		{
		  $pdf->Cell(2,3,"",0,0,'L');
		  $pdf->Cell(18,3,'',0,1,'R');
		}
		else
		{
		  $pdf->Cell(2,3,":Rp.",0,0,'L');
			  $pdf->Cell(18,3,number_format(($arrValMinus[$mn]*-1),2,'.',','),0,1,'R');
		}
	}
		$pdf->Cell(20,3,'Total.Pendapatan','TB',0,'L');
		$pdf->Cell(2,3,":Rp.",'TB',0,'L');
			$pdf->Cell(18,3,number_format(array_sum($arrValPlus),2,'.',','),'TB',0,'R');
		$pdf->Cell(20,3,'Total.Pengurangan','TB',0,'L');
		$pdf->Cell(2,3,":Rp.",'TB',0,'L');
			$pdf->Cell(18,3,number_format((array_sum($arrValMinus)*-1),2,'.',','),'TB',1,'R');

    $pdf->SetFont('Arial','B',5);
	$pdf->Cell(20,3,'Gaji.Bersih',0,0,'L');
	$pdf->Cell(2,3,":Rp.",0,0,'L');
		$pdf->Cell(18,3,number_format((array_sum($arrValPlus)+array_sum($arrValMinus)),2,'.',','),0,0,'R');
		$pdf->Cell(42,3,"",0,1,'L');
		
	$pdf->SetFont('Arial','',5);	
	$pdf->Cell(20,3,'Terbilang',0,0,'L');
	$pdf->Cell(2,3,":",0,0,'L');
		$pdf->MultiCell(58,3,$terbilang." rupiah",0,'L');
	$pdf->SetFont('Arial','I',4);
	$pdf->Cell(80,3,'Note: This is computer generated system, signature is not required','T',1,'L');	
	$pdf->SetFont('Arial','',5);	
	$pdf->Ln(5);	
    if($pdf->GetY()>225 and $pdf->col<1)
	    $pdf->AcceptPageBreak();
	if ($pdf->GetY()>225 and $pdf->col>0)
	   {
	   	//$pdf->lewat=true;
		// $pdf->AcceptPageBreak();
		//$pdf->SetY(277-$pdf->GetY());
		$r=275-$pdf->GetY();
	   	$pdf->Cell(80,$r,'',0,1,'L');
		//$pdf->ln(10);
	   }
	//else   
	//$pdf->lewat=false; 	
	           
//	$pdf->cell(10,3,$pdf->GetY(),0,0,'L');	
}
	$pdf->Output();
?>

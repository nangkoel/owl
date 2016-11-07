<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/fpdf.php');

$proses = $_GET['proses'];
$kdOrg=$_GET['cmpId'];
$tgl=explode('-',$_GET['period']);


$param = $_POST;
//$where=" kodeorg='".$kdOrg."' and tanggal like '%".$tngl."%'";


/** Report Prep **/
$cols = 'no,tanggal,pagi,sore,note';
$colArr = explode(',',$cols);

//$query = selectQuery($dbname,'kebun_curahhujan','kodeorg, tanggal, pagi, sore, catatan',$where);
//$data = fetchData($query);

$title = $_SESSION['lang']['laporanCurahHujan'];
$align = explode(",","L,L,R,R,L");
$length = explode(",","10,15,20,20,35");

/** Output Format **/
switch($proses) {
    case 'pdf':
        class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdOrg;
				global $tgl;
                
                # Bulan
               // $optBulan = 
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                
                $this->SetFont('Arial','',8);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$_SESSION['empl']['lokasitugas'],'',0,'L');
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,
                   $_SESSION['standard']['username'],0,0,'L');
                $this->Ln();
				$query2 = selectQuery($dbname,'organisasi','namaorganisasi',
				"kodeorganisasi='".$kdOrg."'");
				$orgData2 = fetchData($query2);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kebun'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$orgData2[0]['namaorganisasi'],'',0,'L');
              	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$tgl[1]."-".$tgl[0],'',0,'L');
				
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }
                $this->Ln();
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);

		
			$ts=mktime(0,0,0,$tgl[1],1,$tgl[0]);
			$jmlhHari=intval(date("t",$ts));
			$tglDb=tanggalnormal($res['tanggal']);
			//echo"warning:".$jmlhHari;exit();
			for($a=1;$a<=$jmlhHari;$a++)
			{
					$i+=1;
					if(strlen($a)<2)
					{
						$a="0".$a;
					}
					$tglProg=$a."-".$tgl[1]."-".$tgl[0];
			
			/*		$sql2="select * from ".$dbname.".kebun_curahhujan where kodeorg='".$kdOrg."' and tanggal like '%".$tgl."%'  "; 
					$query2=mysql_query($sql2) or die(mysql_error());
					$res2=mysql_fetch_assoc($query2);*/
					$sql="select * from ".$dbname.".kebun_curahhujan where kodeorg='".$kdOrg."' and tanggal='".tanggalsystem($tglProg)."'  "; 
					//echo "warning:".$sql."__".$tglProg;exit();
					$query=mysql_query($sql) or die(mysql_error());
					$res=mysql_fetch_assoc($query);
					
						$pdf->Cell(10/100*$width,$height,$i,1,0,'L',1);
						$pdf->Cell(15/100*$width,$height,$tglProg,1,0,'L',1);
						$pdf->Cell(20/100*$width,$height,$res['pagi'],1,0,'R',1);
						$pdf->Cell(20/100*$width,$height,$res['sore'],1,0,'R',1);
						$pdf->Cell(35/100*$width,$height,$res['catatan'],1,1,'L',1);	
					
				}
				$pdf->Cell((20/100*$width)-5,$height,$_SESSION['lang']['ketCurahHUjan'],'',0,'L');
				
		//}
	/*	foreach($data as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
				$pdf->Cell(10/100*$width,$height,$i,1,0,0,1);
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }*/
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>
<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/fpdf.php');

$proses = $_GET['proses'];
$param = $_POST;


/** Report Prep **/
# Data
$cols = 'notransaksi,tanggal,kodetangki,kuantitas,suhu,cporendemen,cpoffa,'.
    'cpokdair,cpokdkot,kernelquantity,kernelrendemen,kernelkdair,kernelkdkot,kernelffa';
$colArr = explode(',',$cols);
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$query = selectQuery($dbname,'pabrik_masukkeluartangki',$cols,$where);
$data = fetchData($query);

$title = $_SESSION['lang']['pabrikhasil'];
$align = explode(",","L,L,L,R,R,R,R,R,R,R,R,R,R,R");
$length = explode(",","5,5,5,6,4,8,8,6,7,6,6,6,7,9");
//3 2 3 2 2 0 -2 0 -2
# Options
$whereOrg = "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$whereOrg,'0',true);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
  //  $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
    $dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
}

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
                
                # Bulan
                $optBulan = 
                
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
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,
                    numToMonth($_SESSION['org']['period']['bulan'],'I','long')." ".
                    $_SESSION['org']['period']['tahun'],0,0,'L');
                $this->Ln();
                
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
                
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                $this->SetFont('Arial','B',5);	
                $this->SetFillColor(220,220,220);
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
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',5);
        foreach($dataShow as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>
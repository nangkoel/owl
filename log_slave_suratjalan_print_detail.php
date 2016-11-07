<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');
include_once('lib/fpdf.php');

// Class PDF Custom Surat Jalan
class sjPdf extends zPdfMaster {
	public $dataH;
	public $dataD;
	public $franco;
	
	function Footer() {
		$width = $this->w - $this->lMargin - $this->rMargin;
        $height = 12;
		$dataH = $this->dataH;
		$this->Ln($height*3);
		// print_r($dataH);
                $this->SetFont('Arial','',8);
		$this->Cell(10/100*$width,$height,'',0,0,'C');
		$this->Cell(20/100*$width,$height,'CHECKED BY',0,0,'L');
		$this->Cell(70/100*$width,$height,': '.$dataH['checkedby'],0,0,'L');
		$this->Ln();
		
		$this->Cell(10/100*$width,$height,'',0,0,'C');
		$this->Cell(20/100*$width,$height,'DRIVER',0,0,'L');
		$this->Cell(70/100*$width,$height,': '.$dataH['driver'].' Ph.'.$dataH['hpdriver'],0,0,'L');
		$this->Ln($height*2);
		
		// Narasi penutup
		$this->Cell($width,$height,'Barang-barang tersebut akan dikirim ke perkebunan milik '.$this->_orgName,0,1,'L');
		$this->Cell($width,$height,'yang berada di ',0,0,'L');
		$this->Ln($height*2);
		$this->Cell($width,$height,'Demikian Surat Pengantar Barang ini di buat untuk dipergunakan dengan semestinya',0,0,'L');
		$this->Ln($height*2);
		
		$this->Cell(33/100*$width,$height,'Pengirim',0,0,'C');
		$this->Cell(33/100*$width,$height,'Angkutan',0,0,'C');
		$this->Cell(33/100*$width,$height,'Penerima',0,0,'C');
		$this->Ln($height*4);
		
		$this->Cell(33/100*$width,$height,$this->_orgName,0,0,'C');
		$this->Cell(33/100*$width,$height,$dataH['jeniskend'].' : '.$dataH['nopol'],0,0,'C');
		$this->Cell(33/100*$width,$height,$dataH['penerima'],0,0,'C');
	}
}

$proses = $_GET['proses'];
$param = $_GET;

/** Report Prep **/
$where = "nosj='".$param['nosj']."'";
$cols = 'kodept,kodebarang,jenis,jumlah,satuanpo,nopo,nopp';

$colArr = explode(',',$cols);
$query = selectQuery($dbname,'log_suratjalandt',$cols,$where,'nosj desc');
$data = fetchData($query);
$resData = $data;
$barang = '';
foreach($data as $row) {
	if(!empty($barang)) {$barang .= ',';}
	$barang .= "'".$row['kodebarang']."'";
}

// Header
$queryH = selectQuery($dbname,'log_suratjalanht','*',$where);
$dataH = fetchData($queryH);
$dataH = $dataH[0];
$tmpTgl = explode('-',$dataH['tanggal']);
$tglStr = date('d F Y',mktime(0,0,0,$tmpTgl[1],$tmpTgl[2],$tmpTgl[0]));

// Option
$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',"kodebarang in (".$barang.")");

// Franco
$qFranco = selectQuery($dbname,'setup_franco','*',"id_franco='".$dataH['franco']."'");
$resFranco = fetchData($qFranco);
$franco = $resFranco[0];

$align = explode(",","L,L,L,R,L,L,L");
$length = explode(",","15,15,10,10,10,20,20");

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new sjPdf('P','pt','A4');
        $pdf->_kopOnly = true;
		$pdf->_kodeOrg = $dataH['kodept'];
		$pdf->dataH = $dataH;
		$pdf->dataD = $data;
		$pdf->franco = $franco;
		$pdf->_logoOrg = $dataH['kodept'];
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		
		// Title
		$pdf->SetFont('Arial','BU',15);
		$pdf->Cell($width,15,'Surat Jalan',0,1,'C');
		$pdf->Ln();
		
		// Kop
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10/100*$width,$height,'TO:',0,0,'L');
		$pdf->Cell(50/100*$width,$height,$franco['franco_name'],0,0,'L');
		$pdf->Cell(40/100*$width,$height,'Jakarta, '.$tglStr,0,0,'L');
		$pdf->Ln();
		
		$pdf->Cell(10/100*$width,$height,'',0,0,'L');
		$pdf->Cell(50/100*$width,$height,$franco['alamat'],0,0,'L');
		$pdf->Ln();
		
		$pdf->Cell(10/100*$width,$height,'',0,0,'L');
		$pdf->Cell(50/100*$width,$height,'Phone : '.$franco['handphone'],0,0,'L');
		$pdf->Cell(40/100*$width,$height,'UP : '.$franco['contact'].' ('.$franco['handphone'].')',0,0,'L');
		$pdf->Ln($height*2);
               // $pdf->MultiCell($w.)
		
		// Narasi

		$pdf->Cell($width,$height,'Terkirim barang-barang milik '.$pdf->_orgName.' yang terdiri dari:',0,0,'L');
		$pdf->Ln();
        
        $pdf->SetFillColor(200,200,200);
		$pdf->SetFont('Arial','',8);
		
		// Table Header
		$pdf->Cell(5/100*$width,$height,'NO',1,0,'C',1);
		$pdf->Cell(45/100*$width,$height,'NAMA BARANG',1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,'QTY',1,0,'C',1);
		$pdf->Cell(7/100*$width,$height,'UNIT',1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,'PO NO',1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,'PP NO',1,0,'C',1);
		$pdf->Ln();
		
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7.5);
		$i=0;
		foreach($resData as $row) {
			$i++;
             
			        
             if(strlen($optBarang[$row['kodebarang']])>60)
			 {
				 $pdf->Cell(5/100*$width,$height,$i,TRL,0,'R');  
				 if(isset($optBarang[$row['kodebarang']])) {
					$pdf->Cell(45/100*$width,$height,substr($optBarang[$row['kodebarang']],0,60).' -',TRL,0,'L');
								 
				} else {
					$pdf->Cell(45/100*$width,$height,$row['kodebarang'],0,0,'L');
				}
				$pdf->Cell(5/100*$width,$height,$row['jumlah'],RT,0,'R');
				$pdf->Cell(7/100*$width,$height,$row['satuanpo'],RT,0,'L');
				$pdf->Cell(20/100*$width,$height,$row['nopo'],RT,0,'L');
				$pdf->Cell(20/100*$width,$height,$row['nopp'],TRL,1,'L');
				
				 $pdf->Cell(5/100*$width,$height,'',LRB,0,'R');  
				$pdf->Cell(45/100*$width,$height,substr($optBarang[$row['kodebarang']],61,120),LRB,0,'L');
				$pdf->Cell(5/100*$width,$height,'',RB,0,'R');
				$pdf->Cell(7/100*$width,$height,'',RB,0,'L');
				$pdf->Cell(20/100*$width,$height,'',RB,0,'L');
				$pdf->Cell(20/100*$width,$height,'',LRB,0,'L');
				
				$pdf->Ln();
			 }
			 else
			 {
				 $pdf->Cell(5/100*$width,$height,$i,1,0,'R');  
				 if(isset($optBarang[$row['kodebarang']])) {
					$pdf->Cell(45/100*$width,$height,$optBarang[$row['kodebarang']],1,0,'L');
					//$pdf->MultiCell(35/100*$width,$height,$optBarang[$row['kodebarang']],0,0,'L');
								 
				} else {
					$pdf->Cell(45/100*$width,$height,$row['kodebarang'],1,0,'L');
				}
				$pdf->Cell(5/100*$width,$height,$row['jumlah'],1,0,'R');
				$pdf->Cell(7/100*$width,$height,$row['satuanpo'],1,0,'L');
				$pdf->Cell(20/100*$width,$height,$row['nopo'],1,0,'L');
				$pdf->Cell(20/100*$width,$height,$row['nopp'],1,0,'L');
				$pdf->Ln();
			 }
                        
				
			
        }
		
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>
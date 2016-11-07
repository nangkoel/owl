<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
//include_once('lib/formTable.php');
include_once('lib/fpdf.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_GET;


/** Report Prep **/
$cols = array();

#=============================== Header =======================================
$whereH = "novp='".$param['novp']."'";
$queryH = selectQuery($dbname,'keu_vpht','*',$whereH);
$resH = fetchData($queryH);
$queryInv = selectQuery($dbname,'keu_vp_inv','*',$whereH);
$resInv = fetchData($queryInv);
$dataH = $resH[0];

$whTp="nopo='".$resH[0]['nopo'];
$tipe=makeOption($dbname,'keu_tagihanht','nopo,tipeinvoice');


#=============================== Detail =======================================
# Data
$col1 = 'noakun,jumlah';
$cols = array('noakun','jumlah');
$where = "novp='".$param['novp']."'";
$query = selectQuery($dbname,'keu_vpdt',$col1,$where);
$resD = fetchData($query);
$dataD = array(
	'debet' => array(),
	'kredit' => array()
);

# Data Empty
if(empty($resD)) {
    exit('Data Empty');
}

$total = 0;
$totalReal = 0;
// Rearrange Data Detail
foreach($resD as $row) {
	$totalReal+=$row['jumlah'];
	if($row['jumlah']>=0) {
		$dataD['debet'][] = $row;
               // echo substr($row['noakun'],0,3)."--";
                                        
                } else {
		$dataD['kredit'][] = $row;
                                        if(substr($row['noakun'],0,3)=='211'){
                                            $total+=$row['jumlah']*-1;
                                        }
	}
}
/*if($totalReal!=0) {
	exit("Data Detail belum balance");
}*/

# Options
switch($tipe[$resH[0]['nopo']]) {
	case 's':
		$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".keu_tagihanht a
			left join ".$dbname.".log_5supplier b
			on a.kodesupplier = b.supplierid
			where a.nopo = '".$resH[0]['nopo']."'";
		break;
		
	case 'n':
		$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".keu_tagihanht a
			left join ".$dbname.".log_5supplier b
			on a.shipper = b.supplierid
			where a.nopo = '".$resH[0]['nopo']."'";
		break;
	
	case'o':
	$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".keu_tagihanht a
			left join ".$dbname.".log_5supplier b
			on a.kodesupplier = b.supplierid
			where a.noinvoice = '".$resInv[0]['noinv']."'";
		break;	
		
	case'p':
	$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".keu_tagihanht a
				left join ".$dbname.".log_5supplier b
				on a.kodesupplier = b.supplierid
				where a.nopo = '".$resH[0]['nopo']."'";
		break;	
		
	case'k':
	$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".keu_tagihanht a
				left join ".$dbname.".log_5supplier b
				on a.kodesupplier = b.supplierid
				where a.nopo = '".$resH[0]['nopo']."'";
		break;	
		
		
	default:
		/*$tmp = explode('PO',$resH[0]['nopo']);
		if(count($tmp)>1) {
			// PO
			$qSupp = "select a.kodesupplier,b.namasupplier from ".$dbname.".log_poht a
				left join ".$dbname.".log_5supplier b
				on a.kodesupplier = b.supplierid
				where a.nopo = '".$resH[0]['nopo']."'";
			
			
				
				
		} else {
			// Kontrak
			$qSupp = "select a.koderekanan,b.namasupplier from ".$dbname.".log_spkht a
				left join ".$dbname.".log_5supplier b
				on a.koderekanan = b.supplierid
				where a.notransaksi = '".$resH[0]['nopo']."'";
		}*/
}
$resSupp = (!empty($qSupp))? fetchData($qSupp): array();

#================================ Prep Data ===================================
/** Output Format **/
$pdf=new fpdf('P','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();

$pdf->SetFillColor(255,255,255);

$pdf->SetFont('Arial','B',12);
$pdf->Cell(60/100*$width,$height,$_SESSION['org']['namaorganisasi'],0,0,'L',1);

// Kop
$pdf->SetFont('Arial','',12);
$pdf->Cell(10/100*$width,$height,$_SESSION['lang']['novp'],0,0,'R',1);
$pdf->Cell(20/100*$width,$height,": ".$dataH['novp'],0,0,'L',1);
$pdf->Ln();
$pdf->Cell(70/100*$width,$height,$_SESSION['lang']['tanggal'],0,0,'R',1);
$pdf->Cell(20/100*$width,$height,": ".tanggalnormal($dataH['tanggal']),0,0,'L',1);
$pdf->Ln(30);

$pdf->Cell(35/100*$width,$height,"C R E D I T O R",0,0,'L',1);
$pdf->Cell(60/100*$width,$height,": ".$resSupp[0]['namasupplier'],0,1,'L',1);
$pdf->Cell(35/100*$width,$height,"PURCHASE ORDER NO",0,0,'L',1);
$pdf->Cell(60/100*$width,$height,": ".$dataH['nopo'],0,1,'L',1);
$pdf->Cell(35/100*$width,$height,"DATE OF INVOICE RECEIVED",0,0,'L',1);
$pdf->Cell(60/100*$width,$height,": ".tanggalnormal($dataH['tanggalterima']),0,1,'L',1);
$pdf->Cell(35/100*$width,$height,"DUE DATE",0,0,'L',1);
$pdf->Cell(60/100*$width,$height,": ".tanggalnormal($dataH['tanggaljatuhtempo']),0,1,'L',1);//tanggaljatuhtempo
//$pdf->Cell(60/100*$width,$height,": ".tanggalnormal($dataH['tanggalbayar']),0,1,'L',1);//tanggaljatuhtempo
$pdf->Cell(35/100*$width,$height,"EXPLANATION",0,0,'L',1);
$pdf->Cell(1/100*$width,$height,":",0,0,'L',1);
$ketY1 = $pdf->GetY();
$pdf->MultiCell(59/100*$width,$height,$dataH['penjelasan'],0,'L',1);
$ketY2 = $pdf->GetY();
$ketSumY = $ketY2 - $ketY1 - $height;
$pdf->Ln();



/*$a="select distinct(matauang) from ".$dbname.".keu_vpdt where novp='".$param['novp']."'";
$b=mysql_query($a) or die (mysql_error($conn));
$c=mysql_fetch_assoc($b);
*/
	


//$wrmt="nopo='".$dataH['nopo']."'";
$mtUang=makeOption($dbname,'keu_vpdt','novp,matauang',$whereH);
$keNm=makeOption($dbname,'setup_matauang','kode,matauang');
$keSimbol=makeOption($dbname,'setup_matauang','kode,simbol');

if(!empty($mtUang)) {
	$mataUang = isset($keNm[$mtUang[$param['novp']]])? $keNm[$mtUang[$param['novp']]]: 'IDR';
	$simbolmataUang= isset($keSimbol[$mtUang[$param['novp']]])? $keSimbol[$mtUang[$param['novp']]]: 'Rp.';
} else {
	$mataUang='Rupiah';
	$simbolmataUang='Rp';
}

//if($mataUang!='' or $mataUang!=NULL)
//{
//	$mataUang=$mataUang;
//	$simbolmataUang=$simbolmataUang;
//	
//}
//else
//{
//	$mataUang='Rupiah';
//	$simbolmataUang='Rp';
//}
$sKurs="select distinct ((sum(jumlah)*kurs)*-1) as rupiahtotal,kurs from ".$dbname.".keu_vpdt where novp='".$dataH['novp']."' and jumlah<0 and (noakun like '211%')";
$qKurs=  mysql_query($sKurs) or die(mysql_error($conn));
$rKurs=  mysql_fetch_assoc($qKurs);
if($mataUang!='Rupiah'){
    ///echo $sKurs;
    $pdf->Cell(35/100*$width,$height,"TOTAL AMOUNT (Rupiah)",0,0,'L',1);
    $pdf->Cell(60/100*$width,$height,": Rp ".number_format($rKurs['rupiahtotal'],2),0,1,'L',1);
    $pdf->Cell(35/100*$width,$height,"VALAS (".$mataUang.")",0,0,'L',1);
    $pdf->Cell(60/100*$width,$height,": ".$simbolmataUang." ".number_format($total,2)."                          ".number_format($rKurs['kurs'],2),0,1,'L',1);
    $pdf->Ln(15);
}else{
    $pdf->Cell(35/100*$width,$height,"TOTAL AMOUNT (".$mataUang.")",0,0,'L',1);
    $pdf->Cell(60/100*$width,$height,": ".$simbolmataUang." ".number_format($total,2),0,1,'L',1);
    $pdf->Ln(30);
}



$height = 11;
$pdf->Cell(65/100*$width,$height,"VOUCHER PAYABLE SYSTEM",0,1,'C',1);
$pdf->Cell(65/100*$width,$height,"",0,0,'C',1);
$pdf->Cell(40/100*$width,$height,"PREPARED BY :",0,1,'L',1);
$pdf->Cell(30/100*$width,$height,"Account Code",0,0,'L',1);
$pdf->Cell(33/100*$width,$height,"Amount",0,1,'R',1);
$pdf->Cell(65/100*$width,$height,"",0,0,'C',1);
$pdf->Cell(40/100*$width,$height,"VERIFIED BY :",0,1,'L',1);

$optNmakun=makeOption($dbname,'keu_5akun','noakun,namaakun');
// Detail Debet Row 1  //optNmakun
$pdf->SetFont('Arial','',10);
$pdf->Cell(30/100*$width,$height,$dataD['debet'][0]['noakun']." ".ucwords($optNmakun[$dataD['debet'][0]['noakun']]),0,0,'L',1);
$pdf->Cell(33/100*$width,$height,number_format(($dataD['debet'][0]['jumlah']*$rKurs['kurs']),2),0,1,'R',1);

if(isset($dataD['debet'][1])) {
	$onDebet = true;
	// Debet Row 2
	$pdf->Cell(30/100*$width,$height,$dataD['debet'][1]['noakun']." ".ucwords($optNmakun[$dataD['debet'][1]['noakun']]),0,0,'L',1);
	$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['debet'][1]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
	$pdf->Cell(2/100*$width,$height,"",0,0,'L',1);
} else {
	$onDebet = false;
	$currCredit = 0;
	// Kredit Row 1
	$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
	$pdf->Cell(27/100*$width,$height,$dataD['kredit'][0]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][0]['noakun']]),0,0,'L',1);
	$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][0]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
	$pdf->Cell(2/100*$width,$height,"",0,0,'L',1);
}
$pdf->SetFont('Arial','',12);
$pdf->Cell(40/100*$width,$height,"APPROVED BY :",0,1,'L',1);

$pdf->SetFont('Arial','',10);
if($onDebet) {
	if(isset($dataD['debet'][2])) {
		$onDebet = true;
		// Debet Row 3
		$pdf->Cell(30/100*$width,$height,$dataD['debet'][2]['noakun']." ".ucwords($optNmakun[$dataD['debet'][2]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['debet'][2]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
	} else {
		$onDebet = false;
		$currCredit = 0;
		// Kredit Row 1
		$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
		$pdf->Cell(27/100*$width,$height,$dataD['kredit'][0]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][0]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][0]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
	}
} else {
	if(isset($dataD['kredit'][1])) {
		// Kredit Row 2
		$currCredit++;
		$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
		$pdf->Cell(27/100*$width,$height,$dataD['kredit'][1]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][1]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][1]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
	} else {
		$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
	}
}

if($onDebet) {
	if(isset($dataD['debet'][3])) {
		$onDebet = true;
		// Debet Row 4
		$pdf->Cell(30/100*$width,$height,$dataD['debet'][3]['noakun']." ".ucwords($optNmakun[$dataD['debet'][3]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['debet'][3]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,0,'L',1);
	} else {
		$onDebet = false;
		$currCredit = 0;
		// Kredit Row 1
		$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
		$pdf->Cell(27/100*$width,$height,$dataD['kredit'][0]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][0]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][0]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,0,'L',1);
	}
} else {
	if(isset($dataD['kredit'][$currCredit+1])) {
		// Kredit Row 1
		$currCredit++;
		$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
		$pdf->Cell(27/100*$width,$height,$dataD['kredit'][$currCredit]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][$currCredit]['noakun']]),0,0,'L',1);
		$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][$currCredit]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
		$pdf->Cell(2/100*$width,$height,"",0,0,'L',1);
	} else {
		$pdf->Cell(65/100*$width,$height,"",0,0,'C',1);
	}
}
$pdf->SetFont('Arial','',12);
$pdf->Cell(40/100*$width,$height,"POSTED BY :",0,1,'L',1);

$pdf->SetFont('Arial','',10);
if($onDebet) {
	if(isset($dataD['debet'][4])) {
		$tmpDebet = count($dataD['debet']);
		for($i=4;$i<$tmpDebet;$i++) {
			$pdf->Cell(30/100*$width,$height,$dataD['debet'][$i]['noakun']." ".ucwords($optNmakun[$dataD['debet'][$i]['noakun']]),0,0,'L',1);

			$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['debet'][$i]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
			$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
		}
	}
	$currCredit = 0;
} else {
	$currCredit++;
}

$tmpKredit = count($dataD['kredit']);
for($i=($currCredit);$i<$tmpKredit;$i++) {
	$pdf->Cell(3/100*$width,$height,"",0,0,'L',1);
	$pdf->Cell(27/100*$width,$height,$dataD['kredit'][$i]['noakun']." ".ucwords($optNmakun[$dataD['kredit'][$i]['noakun']]),0,0,'L',1);
	$pdf->Cell(33/100*$width,$height,number_format((abs($dataD['kredit'][$i]['jumlah'])*$rKurs['kurs']),2),0,0,'R',1);
	$pdf->Cell(2/100*$width,$height,"",0,1,'L',1);
}

//echo '<pre>';
//print_r($_SESSION);

$totalRow = count($resD);
if($totalRow>5) {
	$addHeight = ($totalRow-5)*11;
} else {
	$addHeight = 0;
}

// Drawing
$pdf->Rect($pdf->lMargin-10, $pdf->tMargin-10, $width+20, 300+$addHeight+$ketSumY);
$pdf->Rect($pdf->lMargin-7, $pdf->tMargin-7, $width+14, 180+$ketSumY);
$pdf->Rect($pdf->lMargin-7, $pdf->tMargin-7+183+$ketSumY, 65/100*$width, 17);

$pdf->Rect($pdf->lMargin-7, $pdf->tMargin-7+203+$ketSumY, 40/100*$width, 20);
$pdf->Rect($pdf->lMargin-4+(40/100*$width), $pdf->tMargin-7+203+$ketSumY, (25/100*$width)-3, 20);

$pdf->Rect($pdf->lMargin-7, $pdf->tMargin-7+226+$ketSumY, 40/100*$width, 67+$addHeight);
$pdf->Rect($pdf->lMargin-4+(40/100*$width), $pdf->tMargin-7+226+$ketSumY, (25/100*$width)-3, 67+$addHeight);

//// TTD
$pdf->Rect($pdf->lMargin-7+(65.5/100*$width), $pdf->tMargin-7+183+$ketSumY, (34.5/100*$width)+14, 110+$addHeight);

$pdf->Output();
?>
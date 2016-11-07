<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

$proses = $_GET['proses'];
$param = $_GET;


/** Report Prep **/
$cols = array();

# Get Data
$where = "karyawanid=".$param['karyawanid'];
$cols = "*";
$query = selectQuery($dbname,'sdm_pesangonht',$cols,$where);
$data = fetchData($query);
$dataH = $data[0];

$queryD = selectQuery($dbname,'sdm_pesangondt',$cols,$where,"no asc");
$dataD = fetchData($queryD);

# Data Empty
if(empty($dataD)) {
    echo 'Data Empty';
    exit;
}

# Options
$whereKary = "karyawanid=".$param['karyawanid'];
$qKary = selectQuery($dbname,'datakaryawan','*',$whereKary);
$resKary = fetchData($qKary);
$infoKary = $resKary[0];
$namaKary = $resKary[0]['namakaryawan'];
$nikKary = $resKary[0]['nik'];

$whrHrd="kodejabatan=33 and kodegolongan>=7 and bagian='HRD' and tanggalkeluar='0000-00-00' and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qHrd = selectQuery($dbname,'datakaryawan','*',$whrHrd);
$rHrd=fetchData($qHrd);

$whrFin="kodejabatan in (21,33) and kodegolongan>=7 and bagian='FIN' and tanggalkeluar='0000-00-00' and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qFin = selectQuery($dbname,'datakaryawan','*',$whrFin);
$rFin=fetchData($qFin);

$whrGM="kodejabatan=21 and kodegolongan=8 and bagian='AGR' and tanggalkeluar='0000-00-00' and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
$qGM=selectQuery($dbname,'datakaryawan','*',$whrGM);
$rGM=fetchData($qGM);

$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',"kodeorganisasi='".$infoKary['lokasitugas']."'");
$optJabatan = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$optBagian = makeOption($dbname,'sdm_5departemen','kode,nama');

// Rearrange Data
$tgl = explode('-',$dataH['tanggal']);
$tglStr = date('j F Y',mktime(0,0,0,$tgl[1],$tgl[2],$tgl[0]));
$tglMasuk = explode('-',$infoKary['tanggalmasuk']);
$tglMasukStr = date('j F Y',mktime(0,0,0,$tglMasuk[1],$tglMasuk[2],$tglMasuk[0]));

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->_kopOnly=true;
		$pdf->_logoOrg = strtolower($_SESSION['org']['kodeorganisasi']);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',14);
        $pdf->Cell($width,$height,"Perhitungan Penyelesaian Gaji/Uang Jasa",0,1,'C');
		$pdf->Ln($height*1.5);
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell($width,$height,"PER ".$tglStr,0,1,'L');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell($width,$height,"Nomor : ".$dataH['nodok'],0,1,'L');
		$pdf->Ln($height*0.5);
		$pdf->Cell(20/100*$width,$height,"NIK",0,0,'L');
		$pdf->Cell(30/100*$width,$height,": ".$nikKary,0,1,'L');
		$pdf->Cell(20/100*$width,$height,"Nama",0,0,'L');
		$pdf->Cell(30/100*$width,$height,": ".$namaKary,0,1,'L');
		$pdf->Cell(20/100*$width,$height,"Unit Kerja",0,0,'L');
		$pdf->Cell(30/100*$width,$height,": ".$optOrg[$infoKary['lokasitugas']],0,1,'L');
		$pdf->Cell(20/100*$width,$height,"Masuk Kerja",0,0,'L');
		$pdf->Cell(30/100*$width,$height,": ".$tglMasukStr,0,1,'L');
		$pdf->Ln($height);
		
		$pdf->SetFont('Arial','U',9);
		$pdf->Cell(30/100*$width,$height,"Perincian Besarnya Uang Penyelesaian :",0,1,'L');
		$pdf->Ln($height);
		
		// Uang Penggantian Hak
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(30/100*$width,$height,"1. Uang Penggantian Hak",0,1,'L');
		$pdf->Ln($height*0.5);
		$pdf->SetFont('Arial','',9);
		$subTotal = 0;
		foreach($dataD as $row) {
			if($row['tipe']=='uang ganti') {
				$pdf->Cell(35/100*$width,$height,'- '.$row['narasi'],0,0,'L');
				$pdf->Cell(5/100*$width,$height,$row['pengali'],0,0,'R');
				$pdf->Cell(15/100*$width,$height,'X',0,0,'C');
				$pdf->Cell(15/100*$width,$height,number_format($row['rp'],0),0,0,'R');
				$pdf->Cell(15/100*$width,$height,' = Rp.',0,0,'L');
				$pdf->Cell(15/100*$width,$height,number_format($row['total'],0),0,0,'R');
				$pdf->Ln();
				$subTotal += $row['total'];
			}
		}
		$pdf->Ln();
		
		// Uang Pesangon
		$pesangon = $dataH['perusahaan']+$dataH['kesalahanbiasa']+$dataH['kesalahanberat'];
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(85/100*$width,$height,"2. Uang Pesangon",0,0,'L');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(15/100*$width,$height,number_format($pesangon,0),0,1,'R');
		$pdf->Ln();
		$subTotal += $pesangon;
		
		// Uang Pisah
		$pesangon = $dataH['perusahaan']+$dataH['kesalahanbiasa']+$dataH['kesalahanberat'];
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(85/100*$width,$height,"3. Uang Pisah",0,0,'L');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(15/100*$width,$height,number_format($dataH['uangpisah'],0),'B',1,'R');
		$pdf->Ln($height*0.5);
		$subTotal += $dataH['uangpisah'];
		
		// Sub Total & Pph
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(80/100*$width,$height,"Sub Total",0,0,'R');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20/100*$width,$height,number_format($subTotal,0),0,1,'R');
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(80/100*$width,$height,"PPh",0,0,'R');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20/100*$width,$height,number_format($dataH['pph'],0),0,1,'R');
		$pdf->Ln($height);
		
		// Potongan
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(85/100*$width,$height,"4. Potongan",0,1,'L');
		$pdf->SetFont('Arial','',9);
		foreach($dataD as $row) {
			if($row['tipe']=='potongan') {
				$pdf->Cell(40/100*$width,$height,'',0,0,'L');
				$pdf->Cell(30/100*$width,$height,$row['narasi'],0,0,'L');
				$pdf->Cell(15/100*$width,$height,' = Rp.',0,0,'L');
				$pdf->Cell(15/100*$width,$height,number_format($row['total'],0),'B',0,'R');
				$pdf->Ln();
				$subTotal += $row['total'];
			}
		}
		$pdf->Ln($height*0.5);
		
		// Diterima
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(40/100*$width,$height,'',0,0,'L');
		$pdf->Cell(30/100*$width,$height,'Diterima',0,0,'L');
		$pdf->Cell(15/100*$width,$height,' = Rp.',0,0,'L');
		$pdf->Cell(15/100*$width,$height,number_format($dataH['total'],0),0,0,'R');
		$pdf->Ln($height*2.5);
		
		// Total
		$pdf->Cell(20/100*$width,$height,'T o t a l :',0,0,'L');
		$pdf->Cell(5/100*$width,$height,'Rp.',0,0,'L');
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(20/100*$width,$height,number_format($dataH['total'],0),0,0,'R');
		$pdf->Ln($height*2.5);
		
		// Terbilang
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10/100*$width,$height,'Terbilang :',0,0,'L');
		$pdf->MultiCell(90/100*$width,$height,'# '.terbilang($dataH['total'],0).' #',0,'L');
		$pdf->Ln($height*3);
		
		// TTD
		$pdf->Cell(25/100*$width,$height,'Diajukan Oleh,',0,0,'C');
		$pdf->Cell(25/100*$width,$height,'Diketahui Oleh,',0,0,'C');
		$pdf->Cell(25/100*$width,$height,'Disetujui Oleh,',0,0,'C');
		$pdf->Cell(25/100*$width,$height,'Diterima Oleh,',0,0,'C');
		
		$pdf->Ln($height*6);
                $pdf->SetFont('Arial','UB',9);
		$pdf->Cell(25/100*$width,$height,$rHrd[0]['namakaryawan'],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$rFin[0]['namakaryawan'],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$rGM[0]['namakaryawan'],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$namaKary,0,0,'C');

                $pdf->Ln();
                $pdf->SetFont('Arial','B',9);
		$pdf->Cell(25/100*$width,$height,$rHrd[0]['pangkat']." ".$rHrd[0]['bagian'],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$rFin[0]['pangkat']." ".$optBagian[$rFin[0]['bagian']],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$rGM[0]['pangkat'],0,0,'C');
		$pdf->Cell(25/100*$width,$height,$optJabatan[$resKary[0]['kodejabatan']],0,0,'C');
		
		$pdf->Output();
        break;
    default:
    break;
}
?>
<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');


require_once('config/connection.php');
require_once('lib/fpdf.php');



$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_POST;


/** Report Prep **/
$str="select periode, tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku = '0'";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    $periodeaktif=$res['periode'];
    $periodemulai=$res['tanggalmulai'];
    $periodesampai=$res['tanggalsampai'];
}



//////////////////
//////////////////



$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');





//////////////////////
///////////////////////

$cols = 'notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat';
if($tipe=="PNN")$colArr = array(
    'notransaksi','kodeorg','tanggal','nikmandor','nikmandor1','keraniproduksi','keranimuat'
); else
$colArr = explode(',',$cols);
$query = selectQuery($dbname,'kebun_aktifitas',$cols,
    "kodeorg='".$_SESSION['empl']['lokasitugas']."' and tipetransaksi='".$tipe."' and tanggal >= '".$periodemulai."' and tanggal <= '".$periodesampai."'","tanggal desc, notransaksi desc");
$data = fetchData($query);
$totalRow = getTotalRow($dbname,'kebun_aktifitas',$whereCont);
//if(!empty($data)) {
//    $whereKarRow = "karyawanid in (";
//    $notFirst = false;
//    foreach($data as $key=>$row) {
//        if($row['jurnal']==1) {
//            $data[$key]['switched']=true;
//        }
//        $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
//        unset($data[$key]['jurnal']);
//        
//        if($notFirst==false) {
//	    if($row['nikmandor']!='') {
//		$whereKarRow .= $row['nikmandor'];
//		$notFirst=true;
//	    }
//	    if($row['nikmandor1']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['nikmandor1'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['nikmandor1'];
//		}
//	    }
//	    if($row['nikasisten']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['nikasisten'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['nikasisten'];
//		}
//	    }
//	    if($row['keranimuat']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['keranimuat'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['keranimuat'];
//		}
//	    }
//	} else {
//	    if($row['nikmandor']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['nikmandor'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['nikmandor'];
//		}
//	    }
//	    if($row['nikmandor1']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['nikmandor1'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['nikmandor1'];
//		}
//	    }
//	    if($row['nikasisten']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['nikasisten'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['nikasisten'];
//		}
//	    }
//	    if($row['keranimuat']!='') {
//		if($notFirst==false) {
//		    $whereKarRow .= $row['keranimuat'];
//		    $notFirst=true;
//		} else {
//		    $whereKarRow .= ",".$row['keranimuat'];
//		}
//	    }
//	}
//    }
//    $whereKarRow .= ")";
//} else {
//    $whereKarRow = "";
//}
$whereKarRow = "";
$optKarRow = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',$whereKarRow,'0',true);

# Data Show
$dataShow = $data;
foreach($dataShow as $key=>$row) {
    $dataShow[$key]['nikmandor'] = substr($optKarRow[$row['nikmandor']],0,12);
    $dataShow[$key]['nikmandor1'] = substr($optKarRow[$row['nikmandor1']],0,12);
    $dataShow[$key]['nikasisten'] = substr($optKarRow[$row['nikasisten']],0,12);
    $dataShow[$key]['keranimuat'] = substr($optKarRow[$row['keranimuat']],0,12);
}

switch($tipe) {
    case "LC":
	$title = "Pembukaan Lahan";
	break;
    case "BBT":
	$title = "Pembibitan";
	break;
    case "TBM":
	$title = "Tanaman Belum Menghasilkan";
	break;
    case "TM":
	$title = "Tanaman Menghasilkan";
	break;
    case "PNN":
	$title = "Panen";
	break;
    default:
	echo "Error : Atribut Status tidak terdefinisi";
	exit;
	break;
}
$align = explode(",","L,L,L,L,L,L,L");
$length = explode(",","25,15,10,13,12,13,12");

/** Output Format **/
switch($proses) {
	
		/*global $data;
		global $dataShow;
		#header
       $pdf=new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        foreach($dataShow as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
	
	
	   
	   
	    $pdf->Output();
		
		break;*/
		
		
		case 'pdf':
		
		class PDF extends FPDF
				{
					function Header() {
						//declarasi header variabel
						global $conn;
						global $dbname;
						global $align;
						global $length;
						global $colArr;
						global $title;
						
						global $conn;
						global $dbname;
						global $align;
						
						global $type;
						global $tipe;
						global $cols;
						
						global $arrNmkary;
						
						
						
						//alamat PT minanga dan logo
						$query = selectQuery($dbname,'organisasi','alamat,telepon',
							"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
						$orgData = fetchData($query);
						
						$width = $this->w - $this->lMargin - $this->rMargin;
						$height = 20;
						if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
						$this->Image($path,$this->lMargin,$this->tMargin,70);	
						$this->SetFont('Arial','B',10);
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
						//tutup logo dan alamat
						
						//untuk sub judul
						$this->SetFont('Arial','B',8);
						if ($tipe==LC)
						{
							$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['pembukaan'].' '.$_SESSION['lang']['lahan'],'',0,'L');	
						}
						else if($tipe==BBT)
						{
							$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['pembibitan'],'',0,'L');
						}
						else if($tipe==TBM)
						{
							$this->Cell((20/100*$width)-5,$height,substr($_SESSION['lang']['tanamanpkk'],0,7).' '.substr($_SESSION['lang']['belumbayar'],0,5).' '.$_SESSION['lang']['menghasilkan'],'',0,'L');//Tanaman Belum Menghasilkan
						} 
						else if($tipe==TM)
						{
						    $this->Cell((20/100*$width)-5,$height,substr($_SESSION['lang']['tanamanpkk'],0,7).' '.$_SESSION['lang']['menghasilkan'],'',0,'L');	
						}
						else if($tipe==PNN)
						{
						    $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kegiatan'].' '.$_SESSION['lang']['panen'],'',0,'L');	
						}
						
						$this->Ln();
						$this->SetFont('Arial','',8);
						$this->Cell((100/100*$width)-5,$height, "Printed By : ".$arrNmkary[$_SESSION['standard']['userid']],'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Date : ".date('d-m-Y'),'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Time : ".date('h:i:s'),'',0,'R');
						$this->Ln();
						$this->Ln();
						//tutup sub judul
						
						//judul tengah
						$this->SetFont('Arial','B',12);
						
						
						
						if ($tipe==LC)
						{
							$this->Cell($width,$height,strtoupper($_SESSION['lang']['pembukaan'].' '.$_SESSION['lang']['lahan']),'',0,'C');
						}
						else if($tipe==BBT)
						{
							$this->Cell($width,$height,strtoupper($_SESSION['lang']['pembibitan']),'',0,'C');
						}
						else if($tipe==TBM)
						{
							$this->Cell($width,$height,strtoupper(substr($_SESSION['lang']['tanamanpkk'],0,7).' '.substr($_SESSION['lang']['belumbayar'],0,5).' '.$_SESSION['lang']['menghasilkan']),'',0,'C');
						}
						else if($tipe==TM)
						{
						   $this->Cell($width,$height,strtoupper(substr($_SESSION['lang']['tanamanpkk'],0,7).' '.$_SESSION['lang']['menghasilkan']),'',0,'C');
						}
						else if($tipe==PNN)
						{
						   $this->Cell($width,$height,strtoupper($_SESSION['lang']['kegiatan'].' '.$_SESSION['lang']['panen']),'',0,'C');
						}
						$this->Ln();
						$this->Cell($width,$height,strtoupper($_SESSION['lang']['unit'].' '.$_SESSION['lang']['lahan'].' : '.$_SESSION['empl']['lokasitugas']),'',0,'C');
						$this->Ln();
						$this->Ln();
						//tutup judul tengah
						
						//isi atas tabel
						$this->SetFont('Arial','B',7);
						$this->SetFillColor(220,220,220);
						if($tipe==PNN)
						{
						$this->Cell(18/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'C',1);
						$this->Cell(9/100*$width,$height,$_SESSION['lang']['organisasi'],1,0,'C',1);
						$this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['mandor'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['mandor'].substr($_SESSION['lang']['angka'],1,1),1,0,'C',1);
						$this->Cell(20/100*$width,$height,$_SESSION['lang']['keraniproduksi'],1,0,'C',1);
						$this->Cell(13/100*$width,$height,$_SESSION['lang']['keranimuat'],1,1,'C',1);
						}
						else 
						{
						$this->Cell(18/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'C',1);
						$this->Cell(9/100*$width,$height,$_SESSION['lang']['organisasi'],1,0,'C',1);
						$this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['mandor'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['mandor'].substr($_SESSION['lang']['angka'],1,1),1,0,'C',1);
						$this->Cell(20/100*$width,$height,$_SESSION['lang']['asisten'],1,0,'C',1);
						$this->Cell(13/100*$width,$height,$_SESSION['lang']['keranimuat'],1,1,'C',1);
						}
						//tutup isi tabel
					}//tutup header pdfnya
					
					
					function Footer()
					{
						$this->SetY(-15);
						$this->SetFont('Arial','I',8);
						$this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
					}
				}
				//untuk tampilan setting pdf
				$pdf=new PDF('P','pt','Legal');//untuk kertas L=len p=pot
				$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
				$height = 20;
				$pdf->AddPage();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('Arial','',7);//ukuran tulisan
				//tutup tampilan setting
		
		
				//isi tabel dan tabelnya
				$no=0;
				$sql="select * from ".$dbname.".kebun_aktifitas where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tipetransaksi='".$tipe."'  order by notransaksi desc";
				//echo $sql;
				$qDet=mysql_query($sql) or die(mysql_error());
				while($res=mysql_fetch_assoc($qDet))
				{
					$no+=1;
					if($tipe==PNN)
					{
						$pdf->Cell(18/100*$width,$height,$res['notransaksi'],1,0,'L',1);	
						$pdf->Cell(9/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
						$pdf->Cell(8/100*$width,$height,$res['tanggal'],1,0,'L',1);	
						$pdf->Cell(15/100*$width,$height,$arrNmkary[$res['nikmandor']],1,0,'L',1);	
						$pdf->Cell(15/100*$width,$height,$arrNmkary[$res['nikmandor1']],1,0,'L',1);		
						$pdf->Cell(20/100*$width,$height,$arrNmkary[$res['nikasisten']],1,0,'L',1);
						$pdf->Cell(13/100*$width,$height,$arrNmkary[$res['keranimuat']],1,0,'L',1);                   
						$pdf->Ln();	
					}
					else 
					{
						$pdf->Cell(18/100*$width,$height,$res['notransaksi'],1,0,'L',1);	
						$pdf->Cell(9/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
						$pdf->Cell(8/100*$width,$height,$res['tanggal'],1,0,'L',1);	
						$pdf->Cell(15/100*$width,$height,$arrNmkary[$res['nikmandor']],1,0,'L',1);	
						$pdf->Cell(15/100*$width,$height,$arrNmkary[$res['nikmandor1']],1,0,'L',1);		
						$pdf->Cell(20/100*$width,$height,$arrNmkary[$res['nikasisten']],1,0,'L',1);
						$pdf->Cell(13/100*$width,$height,$arrNmkary[$res['keranimuat']],1,0,'L',1);                   
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
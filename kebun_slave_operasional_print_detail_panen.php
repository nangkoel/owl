<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_GET;


/** Report Prep **/
$cols = array();

# Prestasi
//$col1 = 'nik,kodekegiatan,kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$col1 = 'tanggal,nik,a.kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,rupiahpenalty,bloklama';
$cols[] = explode(',',$col1);
//$query = selectQuery($dbname,'kebun_prestasi',$col1,
//    "notransaksi='".$param['notransaksi']."'");
$query="select ".$col1." from ".$dbname.".kebun_prestasi a 
        left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi
        left join ".$dbname.".setup_blok c on a.kodeorg=c.kodeorg where a.notransaksi='".$param['notransaksi']."'";
//exit("Error".$query);
$data[] = fetchData($query);
$align[] = explode(",","L,L,L,R,R,R,R,R");
$length[] = explode(",","10,10,15,10,10,15,15,15");



//getNamakaryawan
$sDtKaryawn="select karyawanid,namakaryawan from ".$dbname.".datakaryawan order by namakaryawan asc";
$rData=fetchData($sDtKaryawn);
foreach($rData as $brKary =>$rNamakaryawan)
{
    $RnamaKary[$rNamakaryawan['karyawanid']]=$rNamakaryawan['namakaryawan'];
}
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi order by namaorganisasi asc";
//exit("Error".$sOrg);
$rDataOrg=fetchData($sOrg);
foreach($rDataOrg as $brOrg =>$rNamaOrg)
{
    $rNmOrg[$rNamaOrg['kodeorganisasi']]=$rNamaOrg['namaorganisasi'];
}
switch($tipe) {
    case "LC":
        $title = "Land Clearing";
        break;
    case "BBT":
	$title = $_SESSION['lang']['pembibitan'];
	break;
    case "TBM":
	$title = "UPKEEP-".$_SESSION['lang']['tbm'];
	break;
    case "TM":
	$title = "UPKEEP-".$_SESSION['lang']['tm'];
	break;
	case "PNN":
	$title = $_SESSION['lang']['panen'];
	break;
    default:
	echo "Error : Attribut not defined";
	exit;
	break;
}
$titleDetail = array($_SESSION['lang']['prestasi'],$_SESSION['lang']['absensi'],$_SESSION['lang']['material']);

/** Output Format **/
switch($proses) {
    case 'pdf':
        
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        $pdf->Ln();
        $pdf->SetFillColor(255,255,255);  
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".$param['notransaksi'],0,1,'L',1);
        $pdf->SetFillColor(220,220,220);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
        $pdf->Cell(25/100*$width,$height,$_SESSION['lang']['namakaryawan'],1,0,'C',1);
        $pdf->Cell(13/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);
        $pdf->Cell(8/100*$width,$height,$_SESSION['lang']['bloklama'],1,0,'C',1);
        $pdf->Cell(7/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);
        //$pdf->Cell(5/100*$width,$height,$_SESSION['lang']['jumlahhk'],1,0,'C',1);
        $pdf->Cell(8/100*$width,$height,$_SESSION['lang']['upahkerja'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['upahpremi'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['rupiahpenalty'],1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,1,'C',1);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',8);
        $qData=mysql_query($query) or die(mysql_error($conn));
        while($rData=mysql_fetch_assoc($qData))
        {
            $pdf->Cell(10/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);
            $pdf->Cell(25/100*$width,$height,$RnamaKary[$rData['nik']],1,0,'L',1);
            $pdf->Cell(13/100*$width,$height,$rData['kodeorg'],1,0,'C',1);
            $pdf->Cell(8/100*$width,$height,$rData['bloklama'],1,0,'C',1);
            $pdf->Cell(7/100*$width,$height,$rData['hasilkerja'],1,0,'R',1);
            //$pdf->Cell(5/100*$width,$height,$rData['jumlahhk'],1,0,'R',1);
            $pdf->Cell(8/100*$width,$height,number_format($rData['upahkerja'],0),1,0,'R',1);
            $pdf->Cell(10/100*$width,$height,number_format($rData['upahpremi'],0),1,0,'R',1);
            $pdf->Cell(10/100*$width,$height,number_format($rData['rupiahpenalty'],0),1,0,'R',1);
            $sisa=$rData['upahkerja']+$rData['upahpremi']-$rData['rupiahpenalty'];
            $pdf->Cell(10/100*$width,$height,number_format($sisa,0),1,1,'R',1);
            $totJanjang+=$rData['hasilkerja'];
            $totUpahKerja+=$rData['upahkerja'];
            $totUpahPremi+=$rData['upahpremi'];
            $totUpahDenda+=$rData['rupiahpenalty'];
            $totSisa+=$sisa;
        }
        $pdf->Cell(56/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
        $pdf->Cell(7/100*$width,$height,number_format($totJanjang,0),1,0,'R',1);
        $pdf->Cell(8/100*$width,$height,number_format($totUpahKerja,0),1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,number_format($totUpahPremi,0),1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,number_format($totUpahDenda,0),1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,number_format($totSisa,0),1,1,'R',1);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',8);
        $sAsis="select distinct nikmandor,nikmandor1,nikasisten,keranimuat,tanggal,kodeorg from ".$dbname.".kebun_aktifitas where notransaksi='".$param['notransaksi']."'";
        $qAsis=mysql_query($sAsis) or die(mysql_error($conn));
        $rAsis=mysql_fetch_assoc($qAsis);
        $pdf->ln(10);
        $pdf->Cell(85/100*$width,$height,$rAsis['kodeorg'].",".tanggalnormal($rAsis['tanggal']),0,1,'R',0);
        $pdf->ln(35);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['disetujui'],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['diperiksa'],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$_SESSION['lang']['dbuat_oleh'],0,1,'C',0);
        $pdf->ln(65);
        $pdf->SetFont('Arial','U',8);
        $pdf->Cell(28/100*$width,$height,$RnamaKary[$rAsis['nikasisten']],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$RnamaKary[$rAsis['nikmandor1']],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$RnamaKary[$rAsis['nikmandor']],0,1,'C',0);
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['kerani'],0,0,'C',0);
        $pdf->Cell(28/100*$width,$height,$_SESSION['lang']['nikmandor1'],0,0,'C',0);
        $pdf->Cell(29/100*$width,$height,$_SESSION['lang']['mandor'],0,1,'C',0);
//        foreach($data as $c=>$dataD) {
//            # Header
//            $pdf->SetFont('Arial','B',9);
//            $pdf->Cell($width,$height,$titleDetail[$c],0,1,'L',1);
//            $pdf->SetFillColor(220,220,220);
//            $i=0;
//            foreach($cols[$c] as $column) {
//                if(substr($column,1,1)==".")
//                {
//                    $column=substr($column,2,7);
//                    //exit("error".$column);
//                }
//                
//                $pdf->Cell($length[$c][$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
//                $i++;
//            }
//            $pdf->Ln();
//            
//            # Content
//            $pdf->SetFillColor(255,255,255);
//            $pdf->SetFont('Arial','',9);
//            foreach($dataD as $key=>$row) {    
//                $i=0;
//                foreach($row as $cont) {
//                    if(strlen($cont)==10)
//                    {
//                        if(substr($cont,4,1)=='-')
//                        {
//                            $cont=tanggalnormal($cont);
//                        }
//                        else
//                        {
//                            $cont=$RnamaKary[$cont];
//                            if($cont=='')
//                            {
//                                 $cont=$rNmOrg[$row['kodeorg']];
//                                // exit("Error".$cont);
//                            }
//                             // exit("Error".$cont);
//                        }
//                       
//                    }
//                   
//                        $pdf->Cell($length[$c][$i]/100*$width,$height,$cont,1,0,$align[$c][$i],1);
//                        
//                    
//                    $i++;
//                }
//                $pdf->Ln();
////                echo"<pre>";
////                print_r($dataD);
////                echo"</pre>";
////                
////                exit("Error".$cont);
//            }
//            $pdf->Ln();
//        }
	
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>
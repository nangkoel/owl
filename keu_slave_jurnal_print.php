<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');

$level = $_GET['level'];
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}
if($mode=='pdf') {
    $param = $_GET;
    unset($param['mode']);
    unset($param['level']);
} else {
    $param = $_POST;
}


//print_r($param);
unset($param['nojurnal_0_until']);
foreach($param as $key=>$row) {
    if(substr($key,0,8)=='nojurnal') {
        $nojurnal = $row;
    }
}
//DOM Definition Error : nojurnal_0_until

# Level
switch($level) {
    case '0':
        break;
    case '1':
        # Data
//        $cols = 'nojurnal,noakun,keterangan,jumlah,nodok';
//        $where = "nojurnal='".$nojurnal."'";
//        $query = selectQuery($dbname,'keu_jurnaldt',$cols,$where);
        $query = "SELECT b.noreferensi,a.nojurnal,a.noakun,a.keterangan,a.jumlah,a.nodok 
            FROM ".$dbname.".keu_jurnaldt a
            LEFT JOIN ".$dbname.".keu_jurnalht b on a.nojurnal=b.nojurnal
            WHERE a.nojurnal = '".$nojurnal."'";
		
	//	echo $query;	
			
        $data = fetchData($query);
        
        if($_SESSION['language']=='EN'){
            $kegiatan="SELECT noakun, namaakun1 as namaakun FROM ".$dbname.".keu_5akun";
        }else{
            $kegiatan="SELECT noakun, namaakun FROM ".$dbname.".keu_5akun";
        }
        $query=mysql_query($kegiatan) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($query))
        {
            $kamusakun[$res['noakun']]=$res['namaakun'];
        }    
        
	
	# Total
	$total = array(
	    'debet'=>0,
	    'kredit'=>0
	);
	foreach($data as $row) {
	    if($row['jumlah']<0) {
		$total['kredit'] += $row['jumlah']*(-1);
	    } else {
		$total['debet'] += $row['jumlah'];
	    }
	}
        break;
    default:
}

# Mode
switch($mode) {
    case 'pdf':
        /** Report Prep **/
	$colsNew = 'noakun,namaakun,keterangan,debet,kredit,nodok';
        $colPdf = explode(',',$colsNew);
        $title = $_SESSION['lang']['nojurnal'].": ".$row['nojurnal'];
        $title .= " ".$_SESSION['lang']['noreferensi'].": ".$row['noreferensi'];
        $align = explode(",","L,L,L,R,R,L");
        $length = explode(",","12,20,30,12,12,13");
        
        $pdf = new zPdfMaster('P','pt','A4');
	$pdf->SetFont('Arial','',8);
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	
	foreach($data as $row) {
	    $i=0;
            $pdf->Cell($length[0]/100*$width,$height,$row['noakun'],1,0,$align[0]);
            $pdf->Cell($length[1]/100*$width,$height,substr($kamusakun[$row['noakun']],0,25),1,0,$align[1]);
            $pdf->Cell($length[2]/100*$width,$height,substr($row['keterangan'],0,35),1,0,$align[2]);
            if($row['jumlah']<0){
                $pdf->Cell($length[3]/100*$width,$height,0,1,0,$align[3]);
                $pdf->Cell($length[4]/100*$width,$height,number_format($row['jumlah']*(-1)),1,0,$align[4]);                
            }else{
                $pdf->Cell($length[3]/100*$width,$height,number_format($row['jumlah']),1,0,$align[3]);                                
                $pdf->Cell($length[4]/100*$width,$height,0,1,0,$align[4]);
            }
            $pdf->Cell($length[5]/100*$width,$height,substr($row['nodok'],0,32),1,0,$align[5]);
//	    foreach($row as $head=>$cont) {
//		if($head=='jumlah') {
//		    if($cont<0) {
//			$pdf->Cell($length[$i]/100*$width,$height,0,1,0,$align[$i]);
//			$i++;
//			$pdf->Cell($length[$i]/100*$width,$height,number_format($cont*(-1)),1,0,$align[$i]);
//		    } else {
//			$pdf->Cell($length[$i]/100*$width,$height,number_format($cont),1,0,$align[$i]);
//			$i++;
//			$pdf->Cell($length[$i]/100*$width,$height,0,1,0,$align[$i]);
//		    }
//		}if($head=='keterangan') {
//		    $pdf->Cell($length[$i]/100*$width,$height,substr($kamusakun[$noakun],0,32),1,0,$align[$i]);                    
//			$i++;
//                }
//                else {
//		    $pdf->Cell($length[$i]/100*$width,$height,substr($cont,0,32),1,0,$align[$i]);
//                    $noakun=$cont;
//		$i++;
//		}
//	    }
	    $pdf->Ln();
	}
	# Total
	$lenTotal = $length[0]+$length[1]+$length[2];
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell($lenTotal/100*$width,$height,'TOTAL',1,0,'C');
	$pdf->Cell($length[3]/100*$width,$height,number_format($total['debet']),1,0,'R');
	$pdf->Cell($length[4]/100*$width,$height,number_format($total['kredit']),1,0,'R');
	$pdf->Cell($length[5]/100*$width,$height,'',1,0,'R');
	$pdf->Ln();
	$pdf->SetFont('Arial','',8);
	
	$pdf->Output();
        break;
    default:
}
?>
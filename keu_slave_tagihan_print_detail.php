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

#=============================== Header =======================================
$whereH = "noinvoice='".$param['noinvoice']."'";
$queryH = selectQuery($dbname,'keu_tagihanht','*',$whereH);
$resH = fetchData($queryH);

# Get Nama Pembuat
$userId = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan',
    "karyawanid='".$resH[0]['userid']."'");
# Get Nama Akun Hutang
$namaakunhutang = makeOption($dbname,'keu_5akun','noakun,namaakun',
    "noakun='".$resH[0]['noakunhutang']."'");

#=============================== Detail =======================================
# Data
$col1 = 'noakun,jumlah,noaruskas,matauang,kode';
$cols = array('nomor','noakun','namaakun','matauang','debet','kredit');
//$col1 = 'noakun,jumlah,noaruskas,matauang,kode,hutangunit1';
//$cols = array('nomor','noakun','namaakun','matauang','debet','kredit','hutangunit');
$where = "notransaksi='".$param['notransaksi']."'";
$query = selectQuery($dbname,'keu_tagihandt',$col1,$where);
$res = fetchData($query);

# Data Empty
if(empty($res)) {
    echo 'Data Empty';
    exit;
}

# Options
$whereAkun = "noakun in (";
$whereAkun .= "'".$resH[0]['noakun']."'";
$whereAkun .= ",'".$resH[0]['noakunhutang']."'"; // tambahin kamus nama akun hutangunit
foreach($res as $key=>$row) {
    $whereAkun .= ",'".$row['noakun']."'";
}
$whereAkun .= ")";
$optAkun = makeOption($dbname,'keu_5akun','noakun,namaakun',$whereAkun);
$optHutangUnit = array('0'=>'Tidak','1'=>'Ya');

# Data Show
$data = array();

#================================ Prep Data ===================================
# Total
$totalDebet = 0;$totalKredit = 0;

# Dari Header
$i=1;
$data[$i] = array(
    'nomor'=>$i,
    'noakun'=>$resH[0]['noakun'],
    'namaakun'=>$optAkun[$resH[0]['noakun']],
    'matauang'=>$resH[0]['matauang'],
    'debet'=>0,
    'kredit'=>0
);
//    'hutangunit'=>$optHutangUnit[$resH[0]['hutangunit']],
if($param['tipetransaksi']=='M') {
    $data[$i]['debet'] = $resH[0]['jumlah'];
    $totalDebet += $resH[0]['jumlah'];
} else {
    $data[$i]['kredit'] = $resH[0]['jumlah'];
    $totalKredit += $resH[0]['jumlah'];
}
$i++;

# Dari Detail
foreach($res as $row) {
    $data[$i] = array(
	'nomor'=>$i,
	'noakun'=>$row['noakun'],
	'namaakun'=>isset($optAkun[$row['noakun']])?$optAkun[$row['noakun']]:'',
	'matauang'=>$row['matauang'],
	'debet'=>0,
	'kredit'=>0
    );
//	'hutangunit1'=>$optHutangUnit[$row['hutangunit1']]
    if($param['tipetransaksi']=='M' and $row['jumlah']>0) {
	$data[$i]['kredit'] = $row['jumlah'];
	$totalKredit += $row['jumlah'];
    }
    else if($param['tipetransaksi']=='K' and $row['jumlah']<0){
	$data[$i]['kredit'] = $row['jumlah']*-1;
	$totalKredit += $row['jumlah']*-1;        
    }
    else if($param['tipetransaksi']=='M' and $row['jumlah']<0){
	$data[$i]['debet'] = $row['jumlah']*-1;
	$totalDebet += $row['jumlah']*-1;        
    }    
    else {
	$data[$i]['debet'] = $row['jumlah'];
	$totalDebet += $row['jumlah'];
    }
    $i++;
}

// nyusun berdasarkan debet dulu, abis itu baru kredit. by dz
if(!empty($data)) foreach($data as $c=>$key) {
    $sort_debet[] = $key['debet'];
    $sort_kredit[] = $key['kredit'];
}

// sort
if(!empty($data))array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);

$align = explode(",","R,R,L,L,R,R");
$length = explode(",","7,12,35,10,18,18");
//$align = explode(",","R,R,L,L,R,R,C");
//$length = explode(",","7,12,35,10,13,13,10");
$title = $_SESSION['lang']['kasbank'];
$titleDetail = 'Detail';

/** Output Format **/
switch($proses) {
    case 'pdf':
        $pdf=new zPdfMaster('P','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".
            $res[0]['kode']."/".$param['notransaksi'],0,1,'L',1);
        $pdf->Cell($width,$height,$_SESSION['lang']['cgttu']." : ".
            $resH[0]['cgttu'],0,1,'L',1);
        $pdf->Ln();
	
	# Header
	$pdf->SetFont('Arial','B',9);
	#$pdf->Cell($width,$height,$titleDetail,0,1,'L',1);
	$pdf->MultiCell($width,$height,$_SESSION['lang']['terbilang'].' : '.terbilang($resH[0]['jumlah'],2).
	    ' rupiah',0);
	$pdf->SetFillColor(220,220,220);
	$i=0;
	foreach($cols as $column) {
	    $pdf->Cell($length[$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
	    $i++;
	}
	$pdf->Ln();
	
//        echo"<pre>";
//        print_r($data);
//        echo"</pre>";
        
        
	# Content $optAkun
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        // nyusun ulang nomor setelah disort by debet. dz
        $nyomor=0;
        $flaghutangunit=0; // ni buat flag hutangunit (namaakun)
	foreach($data as $key=>$row) {    
            $nyomor+=1;
	    $i=0;
	    foreach($row as $key=>$cont) {
                if($key=='nomor'){
		    $pdf->Cell($length[$i]/100*$width,$height,$nyomor,1,0,$align[$i],1);                    
                }else{
                    if($key=='debet' or $key=='kredit') {
                        $pdf->Cell($length[$i]/100*$width,$height,number_format($cont,0),1,0,$align[$i],1);
                    }else{
                        if($key=='noakun'){
                            if((substr($cont,0,3)=='121')and($resH[0]['hutangunit']==1)){ // kalo hutangunit, ganti noakunnya pake noakunhutangunit
                                $cont=$resH[0]['noakunhutang'];
                                $flaghutangunit=1;
                            }    
                            $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                        }else{
                            if($key=='namaakun'){
                                if($flaghutangunit==1){ // kalo sebelumnya menampilkan hutangunit, ganti jadi tulisan namaakun hutangunit
                                    $cont= $optAkun[$resH[0]['noakunhutang']];
                                    $flaghutangunit=0;
                                }    
                                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                            }else{
                                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                            }
                        }
                    }                    
                }
		$i++;
	    }
	    $pdf->Ln();
	}
	# Total
	$pdf->SetFont('Arial','B',9);
	$lenTotal = $length[0]+$length[1]+$length[2]+$length[3];
	$pdf->Cell($lenTotal/100*$width,$height,'Total',1,0,'C',1);
	$pdf->Cell($length[4]/100*$width,$height,number_format($totalDebet,0),1,0,'R',1);
	$pdf->Cell($length[5]/100*$width,$height,number_format($totalKredit,0),1,0,'R',1);
//	$pdf->Cell($length[6]/100*$width,$height,'',1,0,'C',1);
	$pdf->Ln();
	
	# Keterangan
	$pdf->MultiCell($width,$height,$_SESSION['lang']['remark'].' : '.$resH[0]['keterangan']);
	# Hutang Unit
        if($resH[0]['hutangunit']==1){
            $pdf->MultiCell($width,$height,'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']]);
        }
        $pdf->Ln();
	
	# TTD
	$pdf->SetFillColor(220,220,220);
	if($param['tipetransaksi']=='M') {
		$pdf->Cell(33/100*$width,$height,$_SESSION['lang']['disetujui'],1,0,'C',1);
		$pdf->Cell(33/100*$width,$height,$_SESSION['lang']['diperiksa'],1,0,'C',1);
		$pdf->Cell(34/100*$width,$height,$_SESSION['lang']['diterimaoleh'],1,0,'C',1);
		$pdf->Ln();
		$pdf->SetFillColor(255,255,255);
		for($i=0;$i<3;$i++) {
			$pdf->Cell(33/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(33/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(34/100*$width,$height,'','LR',0,'C',1);
			$pdf->Ln();
		}
		$pdf->Cell(33/100*$width,$height,'','BLR',0,'C',1);
		// if(isset($userId[$resH[0]['userid']])) {
			// $pdf->Cell(21/100*$width,$height,$userId[$resH[0]['userid']],'BLR',0,'C',1);
		// } else {
			// $pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
		// }
		$pdf->Cell(33/100*$width,$height,'','BLR',0,'C',1);
		$pdf->Cell(34/100*$width,$height,'','BLR',0,'C',1);
		
	} else { // Tipe = K
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['disetujui'],1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['diperiksa'],1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['diketahuioleh'],1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['dibayaroleh'],1,0,'C',1);
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['diterimaoleh'],1,0,'C',1);
		$pdf->Ln();
		$pdf->SetFillColor(255,255,255);
		for($i=0;$i<3;$i++) {
			$pdf->Cell(20/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(20/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(20/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(20/100*$width,$height,'','LR',0,'C',1);
			$pdf->Cell(20/100*$width,$height,'','LR',0,'C',1);
			$pdf->Ln();
		}
		$pdf->Cell(20/100*$width,$height,'','BLR',0,'C',1);
		// if(isset($userId[$resH[0]['userid']])) {
			// $pdf->Cell(21/100*$width,$height,$userId[$resH[0]['userid']],'BLR',0,'C',1);
		// } else {
			// $pdf->Cell(21/100*$width,$height,'','BLR',0,'C',1);
		// }
		$pdf->Cell(20/100*$width,$height,'','BLR',0,'C',1);
		$pdf->Cell(20/100*$width,$height,'','BLR',0,'C',1);
		$pdf->Cell(20/100*$width,$height,'','BLR',0,'C',1);
		$pdf->Cell(20/100*$width,$height,'','BLR',0,'C',1);
	}
    
        $pdf->Output();
        break;
    case 'excel':
        break;
    case 'html':
        $tab.="<link rel=stylesheet type=text/css href=style/generic.css>";
        $tab.="<fieldset><legend>".$title."</legend>";
        $tab.="<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><tbody class=rowcontent>";
        $tab.="<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td> :</td><td> ".$_SESSION['empl']['lokasitugas']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['notransaksi']."</td><td> :</td><td> ".$res[0]['kode']."/".$param['notransaksi']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['cgttu']."</td><td> :</td><td> ".$resH[0]['cgttu']."</td></tr>";
        $tab.="<tr><td>".$_SESSION['lang']['terbilang']."</td><td> :</td><td> ".terbilang($resH[0]['jumlah'],2).
	    ' rupiah'."</td></tr>";
        if($resH[0]['hutangunit']==1){
            $tab.="<tr><td>".$_SESSION['lang']['hutangunit']."</td><td> :</td><td> ".'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']]."</td></tr>";            
        }
        $tab.="</tbody></table><br />";
       
            $tab.="<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><thead><tr class=rowheader>";
            foreach($cols as $column) {
                $tab.="<td>".$_SESSION['lang'][$column]."</td>";
            }
            $tab.="</tr></thead><tbody class=rowcontent>";
        // nyusun ulang nomor setelah disort by debet. dz
            $nyomor=0;
            foreach($data as $key=>$row) {    
                $nyomor+=1;
                $tab.="<tr>";
                foreach($row as $key=>$cont) {
                    if($key=='nomor'){
                        $tab.="<td>".$nyomor."</td>";
                    }else{
                        if($key=='debet' or $key=='kredit') {
                            $tab.="<td>".number_format($cont,0)."</td>";
                        } else {
                            $tab.="<td>".$cont."</td>";
                        }                    
                    }
                }
                $tab.="</tr>";
            }
        $tab.="<tr><td colspan=4 align=center>Total</td><td align=right>".number_format($totalDebet,0)."</td><td align=right>".number_format($totalKredit,0)."</td></tr>";
             $tab.="</tbody></table> <br />";
       
        echo $tab;
        
    break;
    default:
    break;
}
?>
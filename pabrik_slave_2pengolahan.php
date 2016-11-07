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

$kodeorg=$param[kodeorg];
$periode_bulan=$param[periode_bulan];
$periode_tahun=$param[periode_tahun];

# Tanggal Margin
$currTahun = $tahun = $param['periode_tahun'];
$currBulan = $bulan = $param['periode_bulan'];

$currPeriod = $currTahun.'-'.addZero($currBulan,2);

switch($level) {
    case '0':
        # Data
        $cols = "a.tanggal,sum(jamstagnasi),sum(jamdinasbruto),sum(jumlahlori),sum(a.tbsdiolah),oer,oerpk,nopengolahan";
		//$cols = "a.tanggal,sum(jamstagnasi),sum(jamdinasbruto),sum(jumlahlori),sum(a.tbsdiolah),sum(oer),sum(oerpk),nopengolahan";
        $cols2 = "nomor,tanggal,jamstagnasi,jamoperasional,jumlahlori,tbsdiolah,cpo,oerpk,detail";
        $cols2e = "nomor,tanggal,jamstagnasi,jamoperasional,jumlahlori,tbsdiolah,cpo,oerpk";
        $where = "a.kodeorg='".$param['kodeorg']."' and left(a.tanggal,7)='".$currPeriod."'";
       // $query = selectQuery($dbname,'pabrik_pengolahan',$cols,$where)." group by tanggal";
        $query="select distinct ".$cols." from ".$dbname.".pabrik_pengolahan a left join ".$dbname.".pabrik_produksi b 
                 on (a.kodeorg=b.kodeorg and a.tanggal=b.tanggal) where ".$where." group by a.tanggal";
        
        $tmpRes = fetchData($query);
        if(empty($tmpRes)) {
            echo 'Warning : Data empty';
            exit;
        }
        
        # Set Data
        $data = $tmpRes;
        $dataShow = $dataExcel = $data;
        foreach($data as $key=>$row) {
            $dataShow[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
            $dataShow[$key]['jumlahlori'] = number_format($row['jumlahlori'],0);
            $dataShow[$key]['tbsdiolah'] = number_format($row['tbsdiolah'],0);
        }
        
        # Report Gen
        $theCols = array();
        if($mode!='excel')$tmpCol = explode(',',$cols2); else $tmpCol = explode(',',$cols2e); 

        foreach($tmpCol as $row) {
            $theCols[] = $_SESSION['lang'][$row];
        }
        $align = explode(",","R,R,R,R,R,R,R,R,R");
        break;
    default:
    break;
}

// Kalo lebih dari 59, tambahin menit ke jam
function fixHours($hours)
{
    if (strpos($hours, '.') !== false)
    {
        // Split hours and minutes.
        list($hours, $minutes) = explode('.', $hours);
		$minutes=substr($minutes,0,2);
		if(strlen($minutes)==1) $minutes=$minutes*10;
    }
	if($minutes>=60){
		$minutes=$minutes-60;
		$hours=$hours+1;
	}
//	return $hours;
	return sprintf("%d:%02.0f", $hours, $minutes);
} 

switch($mode) {
    case 'pdf':
	
        /** Report Prep **/
        $colPdf = explode(',',$cols2e);
        $title = $_SESSION['lang']['pabrik']." ".$kodeorg;
        $length = explode(",","10,10,10,10,10,10,10,10");
        
        $pdf = new zPdfMaster('L','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        
        # Content
	$pdf->SetFont('Arial','',9);
	

	
	foreach($data as $key=>$row) {
	    $i=0;
		$j+=1; // nomor baris
                $pdf->Cell($length[$i]/100*$width,$height,$j,1,0,$align[$i],1);
	    foreach($row as $head=>$cont) {
		{
			if($i==0){ // tanggal
				$tanggal=$cont;
				$qwe=date('D', strtotime($tanggal));
				if($qwe=='Sun')$pdf->SetFillColor(255,192,192);
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
			}else
			if(($i==1)){ // jam stagnasi
				$pdf->SetFillColor(255,255,255);
				$jamstag=$cont;
				if($jamstag=="0:00")$jamstag="";
                $pdf->Cell($length[$i]/100*$width,$height,$jamstag,1,0,$align[$i],1);
			}else
			if(($i==2)){ // jam operasional
				$pdf->SetFillColor(255,255,255);
				$jambrut=$cont;
			    list($hoursb, $minutesb) = split(':', $jambrut);
			    list($hourss, $minutess) = split(':', $jamstag);
			    $minutes = $minutesb-$minutess;
			    $hours = $hoursb-$hourss;
				if($minutes<0){
					$minutes=60+$minutes;
					$hours-=1;
				}
				$minutes=addZero($minutes,2);
				$jamop="$hours:$minutes";
				if(($jambrut=="0:00")and($jamstag==""))$jamop="";
                $pdf->Cell($length[$i]/100*$width,$height,$jamop,1,0,$align[$i],1);
			}else
			if(($i==5)){ // detail
				$pdf->SetFillColor(255,255,255);
			}else{ // lori & tbs
				$pdf->SetFillColor(255,255,255);
				$jumlah=number_format($cont,0);
				if($jumlah==0)$jumlah="";
                $pdf->Cell($length[$i]/100*$width,$height,$jumlah,1,0,$align[$i],1);
			}
		}
		$i++;
	    }
 	    $pdf->Ln();
       }
        $pdf->Output();
        break;
    default:
        # Redefine Align
	$alignPrev = array();
	foreach($align as $key=>$row) {
	    switch($row) {
		case 'L':
		    $alignPrev[$key] = 'left';
		    break;
		case 'R':
		    $alignPrev[$key] = 'right';
		    break;
		case 'C':
		    $alignPrev[$key] = 'center';
		    break;
		default:
	    }
	}
	
	/** Mode Header **/
        if($mode=='excel') {
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='laporanpengolahan' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }
        
        /** Generate Table **/
        foreach($theCols as $head) {
            $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";

        # Content
    $j=0;    
	foreach($data as $key=>$row) {
            $tab .= "<tr class='rowcontent'>";
	    $i=0;
		$j+=1; // nomor baris
		$tab .= "<td align='right'>".$j."</td>";
	    foreach($row as $head=>$cont) {
		{
			if($i==0){ // tanggal
				$tab .= "<td align='".$alignPrev[$i]."'>";
				$tanggal=$dataShow[$key][$head];
				$qwe=date('D', strtotime($tanggal));
				if($qwe=='Sun') $tab .="<font color=red>".$tanggal."</font>"; else
					$tab .= $tanggal;
		    	$tab .= "</td>";
			}else
			if(($i==1)){ // jam stagnasi
				$jamstag=$dataShow[$key][$head];
				if($jamstag=="0:00")$jamstag="";
		    	$tab .= "<td align='".$alignPrev[$i]."'>".$jamstag."</td>";
			}else
			if(($i==2)){ // jam operasional
				$jambrut=$dataShow[$key][$head];
				if($jambrut=="0:00")$jambrut="";
			    /*list($hoursb, $minutesb) = split(':', $jambrut);
			    list($hourss, $minutess) = split(':', $jamstag);
			    $minutes = $minutesb-$minutess;
			    $hours = $hoursb-$hourss;
				if($minutes<0){
					$minutes=60+$minutes;
					$hours-=1;
				}
				$minutes=addZero($minutes,2);
				$jamop="$hours:$minutes";
				if(($jambrut=="0:00")and($jamstag==""))$jamop="";*/
		    	$tab .= "<td align='".$alignPrev[$i]."'>".$jambrut."</td>";
			}else
			if(($i==7)){ // detail
				if(($mode!='excel')){
				$tab .= "<td align='".$alignPrev[$i]."'><img onclick=\"viewDetail('".$dataShow[$key][$head]."','".$tanggal."','".$kodeorg."','".$periode_tahun."','".$periode_bulan."',event);\" title='".$_SESSION['lang']['klikdetail']."' class=\"resicon\" src=\"images/icons/clipboard_sign.png\"></td>";					
				}
			}else{ // lori & tbs
				$jumlah=number_format($dataShow[$key][$head],0);
				if($jumlah==0)$jumlah="";
		    	$tab .= "<td align='".$alignPrev[$i]."'>".$jumlah."</td>";
			}
		}
		$i++;
	    }
	    $tab .= "</tr>";
        }
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
//            $nop_="PakaiMaterial";
            $nop_="LaporanPengolahan_".$kodeorg."_".$periode_tahun."-".$periode_bulan;
            if(strlen($stream)>0) {
                # Delete if exist
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            @unlink('tempExcel/'.$file);
                        }
                    }	
                    closedir($handle);
                }
                
                # Write to File
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$stream)) {
                    echo "Error : Tidak bisa menulis ke format excel";
                    exit;
                } else {
                    echo $nop_;
                }
                fclose($handle);
            }
        } else {
            echo $tab;
        }
        break;
}
?>
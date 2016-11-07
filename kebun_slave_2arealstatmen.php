<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');

// INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`) VALUES ('luaskerangka', 'Luas Kerangka', 'areal statement', NULL, 'Luas Kerangka', 'Broad Framework');

$level = $_GET['level'];
$ispo=$_GET['ispo'];
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
    $ispo=$param['ispo'];
}

# Tanggal Margin
$currTahun = $tahun = $param['periode_tahun'];
$currBulan = $bulan = $param['periode_bulan'];
$bulan++;
if($bulan>12) {
    $bulan = 1;
    $tahun++;
}
if($bulan<10) {
    $bulan = '0'.$bulan;
}
$tanggalM = $tahun."-".$bulan."-01";

# Current Periode
if($currBulan<10) {
    $currBulan = '0'.$currBulan;
}
$currPeriod = $currTahun."-".$currBulan;
$arrTopografo=array("D1"=>"DATAR","D2"=>"BERGELOMBANG","B1"=>"BUKIT");
switch($level) {
    case '0':
        # Data
        # Afdeling dan Blok
        $optBelow = getOrgBelow($dbname,$param['unit']);
        
        # Mutasi Blok
        $cols = "kodeorg,tahuntanam,kodeorg,bloklama,statusblok,luasareanonproduktif,luasareaproduktif,jumlahpokok,cadangan,okupasi,rendahan,sungai,rumah,kantor,pabrik,jalan,kolam,umum,tanggalpengakuan,topografi";
     //   $where = "tanggalpengakuan<'".$tanggalM."' and left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'";
	 if($param['unit']=='')
	 {
		$where = "tanggalpengakuan<'".$tanggalM."'"; 	 	
	 }
	 else
	 {
		$where = "tanggalpengakuan<'".$tanggalM."' and left(kodeorg,4)='".$param['unit']."'";
	 }
        if($param['tahuntanam']!='') {
            $where .= " and tahuntanam=".$param['tahuntanam'];
        }
        if($param['afdeling']!='') {
            $where .= " and left(kodeorg,6)='".$param['afdeling']."'";
        }
        if ($ispo!=''){
            $where.=" and ispo=".$ispo;
        }
        $where .= " and (luasareaproduktif+luasareanonproduktif > 0)"; // hanya munculkan yang punya luas kerangka
        $query = selectQuery($dbname,'setup_blok',$cols,$where,"tahuntanam asc,kodeorg");
        //echo $query;
        $tmpBlok = fetchData($query);
        
        if(empty($tmpBlok)) {
            echo 'Warning : No data found';
            exit;
        }
       
        # Rearrange Blok
        $resBlok = array();
        foreach($tmpBlok as $row) {
            # Init
            if(!isset($resBlok[$row['tahuntanam']][$row['kodeorg']])) {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal'] = array(
                    'luas'=>0,
                    'luasareaproduktif'=>0,
                    'luasareanonproduktif'=>0,
                    'pokok'=>0
                );
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi'] = array(
                    'luas'=>0,
                    'luasareaproduktif'=>0,
                    'luasareanonproduktif'=>0,
                    'pokok'=>0
                );
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kodeorg'] = $row['kodeorg'];
				 $resBlok[$row['tahuntanam']][$row['kodeorg']]['bloklama'] = $row['bloklama'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['statusblok'] = $row['statusblok'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['cadangan'] = $row['cadangan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['okupasi'] = $row['okupasi'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['rendahan'] = $row['rendahan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['sungai'] = $row['sungai'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['rumah'] = $row['rumah'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kantor'] = $row['kantor'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['pabrik'] = $row['pabrik'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['jalan'] = $row['jalan'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['kolam'] = $row['kolam'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['umum'] = $row['umum'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['luasareaproduktif'] = $row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['luasareanonproduktif'] = $row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['topografi'] = $row['topografi'];
            }
            
            # Update Value
            if($currPeriod==substr($row['tanggalpengakuan'],0,7)) {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luas']+=$row['luasareaproduktif']+$row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luasareaproduktif']+=$row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['luasareanonproduktif']+=$row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['mutasi']['pokok']+=$row['jumlahpokok'];
            } else {
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luas']+=$row['luasareaproduktif']+$row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luasareaproduktif']+=$row['luasareaproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['luasareanonproduktif']+=$row['luasareanonproduktif'];
                $resBlok[$row['tahuntanam']][$row['kodeorg']]['awal']['pokok']+=$row['jumlahpokok'];
            }
            //$resBlok[$row['tahuntanam']]=$row['cadangan'];
        }

        # Rearrange Data
        $data = array();
        $i=1;
        
        foreach($resBlok as $tt=>$rowH) {
            foreach($rowH as $org=>$row) {
                if(($row['awal']['luas']+$row['mutasi']['luas'])==0) {
                    $rapat = 0;
                } else {
//                    $rapat = $row['awal']['pokok']/($row['awal']['luas']+$row['mutasi']['luas']);
                    @$rapat = $row['awal']['pokok']/($row['awal']['luasareaproduktif']);
                }
                $data[$tt][$org] = array(
                    #'luas'=>$row['mutasi']['luas']+$row['akhir']['luas'],
                    'kodeorg'=>$row['kodeorg'],
					 'bloklama'=>$row['bloklama'],
                    'statusblok'=>$row['statusblok'],
                    'topografi'=>$row['topografi'],
                    'awal'=>$row['awal']['luas'],
//                    'mutasi'=>$row['mutasi']['luas'],
//                    'akhir'=>$row['awal']['luas']+$row['mutasi']['luas'],
                    'luasareaproduktif'=>$row['mutasi']['luasareaproduktif']+$row['awal']['luasareaproduktif'],
                    'luasareanonproduktif'=>$row['mutasi']['luasareanonproduktif']+$row['awal']['luasareanonproduktif'],
                    'pokok'=>$row['mutasi']['pokok']+$row['awal']['pokok'],
                    'cadangan'=>$row['cadangan'],
                    'okupasi'=>$row['okupasi'],
                    'rendahan'=>$row['rendahan'],
                    'sungai'=>$row['sungai'],
                    'rumah'=>$row['rumah'],
                    'kantor'=>$row['kantor'],
                    'pabrik'=>$row['pabrik'],
                    'jalan'=>$row['jalan'],
                    'kolam'=>$row['kolam'],
                    'umum'=>$row['umum'],
                    
                    'kerapatan'=>$rapat
                );
                $i++;
            }
        }
        
        # Data Show & Total
        $dataShow = array();
        $total = array();
        $gTotal = array(
            #'luas'=>0,
            'awal'=>0,
            'luasareaproduktif'=>0,
            'luasareanonproduktif'=>0,
//            'mutasi'=>0,'akhir'=>0,
            'pokok'=>0,
        );
        
        foreach($data as $tt=>$rowH) {
            foreach($rowH as $blok=>$row) {
                # Sub Total
                if(isset($total[$tt])) {
                    #$total[$afd][$blok]['luas'] += $row['luas'];
                    $total[$tt]['awal'] += $row['awal'];
                    $total[$tt]['luasareaproduktif'] += $row['luasareaproduktif'];
                    $total[$tt]['luasareanonproduktif'] += $row['luasareanonproduktif'];
//                    $total[$tt]['mutasi'] += $row['mutasi'];
//                    $total[$tt]['akhir'] += $row['akhir'];
                    $total[$tt]['pokok'] += $row['pokok'];
                    if($mode!='pdf')
                        {
                            $total[$tt]['cadangan'] += $row['cadangan'];
                            $total[$tt]['okupasi'] += $row['okupasi'];
                            $total[$tt]['rendahan'] += $row['rendahan'];
                            $total[$tt]['sungai'] += $row['sungai'];
                            $total[$tt]['rumah'] += $row['rumah'];
                            $total[$tt]['kantor'] += $row['kantor'];
                            $total[$tt]['pabrik'] += $row['pabrik'];
                            $total[$tt]['jalan'] += $row['jalan'];
                            $total[$tt]['kolam'] += $row['kolam'];
                            $total[$tt]['umum'] += $row['umum'];
                        }
                } else {
                    #$total[$afd][$blok]['luas'] = $row['luas'];
                    $total[$tt]['awal'] = $row['awal'];
                    $total[$tt]['luasareaproduktif'] = $row['luasareaproduktif'];
                    $total[$tt]['luasareanonproduktif'] = $row['luasareanonproduktif'];
//                    $total[$tt]['mutasi'] = $row['mutasi'];
//                    $total[$tt]['akhir'] = $row['akhir'];
                    $total[$tt]['pokok'] = $row['pokok'];
                        if($mode!='pdf')
                        {
                            $total[$tt]['cadangan'] += $row['cadangan'];
                            $total[$tt]['okupasi'] += $row['okupasi'];
                            $total[$tt]['rendahan'] += $row['rendahan'];
                            $total[$tt]['sungai'] += $row['sungai'];
                            $total[$tt]['rumah'] += $row['rumah'];
                            $total[$tt]['kantor'] += $row['kantor'];
                            $total[$tt]['pabrik'] += $row['pabrik'];
                            $total[$tt]['jalan'] += $row['jalan'];
                            $total[$tt]['kolam'] += $row['kolam'];
                            $total[$tt]['umum'] += $row['umum'];
                        }
                }
                
                # Grand Total
                #$gTotal['luas'] += $row['luas'];
                $gTotal['awal'] += $row['awal'];
                $gTotal['luasareaproduktif'] += $row['luasareaproduktif'];
                $gTotal['luasareanonproduktif'] += $row['luasareanonproduktif'];
//                $gTotal['mutasi'] += $row['mutasi'];
//                $gTotal['akhir'] += $row['akhir'];
                $gTotal['pokok'] += $row['pokok'];
                 if($mode!='pdf')
                {
                $gTotal['cadangan'] += $row['cadangan'];
                $gTotal['okupasi'] += $row['okupasi'];
                $gTotal['rendahan'] += $row['rendahan'];
                $gTotal['sungai'] += $row['sungai'];
                $gTotal['rumah'] += $row['rumah'];
                $gTotal['kantor'] += $row['kantor'];
                $gTotal['pabrik'] += $row['pabrik'];
                $gTotal['jalan'] += $row['jalan'];
                $gTotal['kolam'] += $row['kolam'];
                $gTotal['umum'] += $row['umum'];
                }
                
                # Data Show  
                #$dataShow[$afd][$blok]['luas'] = number_format($row['luas'],2);
                #$dataShow[$tt][$blok]['tahuntanam'] = $row['tahuntanam'];
                $dataShow[$tt][$blok]['kodeorg'] = $row['kodeorg'];
				
				$dataShow[$tt][$blok]['bloklama'] = $row['bloklama'];
                $dataShow[$tt][$blok]['statusblok'] = $row['statusblok'];
                $dataShow[$tt][$blok]['topografi'] = $row['topografi'];
                $dataShow[$tt][$blok]['awal'] = number_format($row['awal'],2);
                $dataShow[$tt][$blok]['luasareaproduktif'] = number_format($row['luasareaproduktif'],2);
                $dataShow[$tt][$blok]['luasareanonproduktif'] = number_format($row['luasareanonproduktif'],2);
//                $dataShow[$tt][$blok]['mutasi'] = number_format($row['mutasi'],2);
//                $dataShow[$tt][$blok]['akhir'] = number_format($row['akhir'],2);
                $dataShow[$tt][$blok]['pokok'] = number_format($row['pokok'],2);
                if($mode!='pdf')
                {
                $dataShow[$tt][$blok]['cadangan'] = number_format($row['cadangan'],2);
                $dataShow[$tt][$blok]['okupasi'] = number_format($row['okupasi'],2);
                $dataShow[$tt][$blok]['rendahan'] = number_format($row['rendahan'],2);
                $dataShow[$tt][$blok]['sungai'] = number_format($row['sungai'],2);
                $dataShow[$tt][$blok]['rumah'] = number_format($row['rumah'],2);
                $dataShow[$tt][$blok]['kantor'] = number_format($row['kantor'],2);
                $dataShow[$tt][$blok]['pabrik'] = number_format($row['pabrik'],2);
                $dataShow[$tt][$blok]['jalan'] = number_format($row['jalan'],2);
                $dataShow[$tt][$blok]['kolam'] = number_format($row['kolam'],2);
                $dataShow[$tt][$blok]['umum'] = number_format($row['umum'],2);
                }
                if($row['awal']==0) {
                    $dataShow[$tt][$blok]['kerapatan'] = 0;
                } else {
//                    $dataShow[$tt][$blok]['kerapatan'] = number_format($row['pokok']/$row['akhir'],0);
                    @$dataShow[$tt][$blok]['kerapatan'] = number_format($row['pokok']/$row['luasareaproduktif'],0);
                }
            }
        }
        
        foreach($data as $tt=>$rowH) {
            if($total[$tt]['awal']==0) {
                $total[$tt]['kerapatan'] = 0;
            } else {
//                $total[$tt]['kerapatan'] = $total[$tt]['pokok']/$total[$tt]['akhir'];
                @$total[$tt]['kerapatan'] = $total[$tt]['pokok']/$total[$tt]['luasareaproduktif'];
            }
        }
        if($gTotal['awal']==0) {
            $gTotal['kerapatan'] = 0;
        } else {
//            $gTotal['kerapatan'] = $gTotal['pokok']/$gTotal['akhir'];
            @$gTotal['kerapatan'] = $gTotal['pokok']/$gTotal['luasareaproduktif'];
        }
        
        # Report Gen
        $theCols = array(
            #$_SESSION['lang']['afdeling'],,,,,,,,,,,tanggalpengakuan
            $_SESSION['lang']['thntnm'],
            $_SESSION['lang']['blok'],
            $_SESSION['lang']['kodeorg'],
			$_SESSION['lang']['bloklama'],
            $_SESSION['lang']['statusblok'],
            $_SESSION['lang']['topografi'],
            #$_SESSION['lang']['luas'],
            $_SESSION['lang']['luaskerangka'],
            $_SESSION['lang']['luasareaproduktif'],
            $_SESSION['lang']['luasareanonproduktif'],
//            $_SESSION['lang']['mutasiarea'],
//            $_SESSION['lang']['luasakhir'],
            $_SESSION['lang']['jumlahpokok'],
            $_SESSION['lang']['cadangan'],
            $_SESSION['lang']['okupasi'],
            $_SESSION['lang']['rendahan'],
            $_SESSION['lang']['sungai'],
            $_SESSION['lang']['rumah'],
            $_SESSION['lang']['kantor'],
            $_SESSION['lang']['pabrik'],
            $_SESSION['lang']['jalan'],
            $_SESSION['lang']['kolam'],
            $_SESSION['lang']['umum'],
            $_SESSION['lang']['kerapatan'],
        );
        break;
    default:
    break;
}

switch($mode) {
    case 'pdf':
        /** Report Prep **/
//        $colPdf = array('thntnm','blok','kodeorg','statusblok','luasawal',
//            'mutasiarea','luasakhir','jumlahpokok','kerapatan');
        $colPdf = array('thntnm','blok','kodeorg','bloklama','statusblok','topografi','luasawal',
            'luasareaproduktif','luasareanonproduktif','jumlahpokok','kerapatan');
        $title = $_SESSION['lang']['arealstatement'];
        $align = explode(",","R,L,L,L,L,R,R,R,R,R");
        $length = explode(",","5,15,10,7,7,6,10,10,10,10");
        
        $pdf = new zPdfMaster('L','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',6);
        
        foreach($dataShow as $afd=>$rowH) {    
            $i=0;$afdC=false;
            if($afdC==false) {
                $pdf->Cell($length[$i]/100*$width,$height,$afd,'TLR',0,$align[$i],1);
            }
            $i++;
            foreach($rowH as $blok=>$row) {
                if($afdC==true) {
                    $i=0;
                    $pdf->Cell($length[$i]/100*$width,$height,'','LR',$align[$i],1);
                    $i++;
                } else {
                    $afdC=true;
                }
                $pdf->Cell($length[$i]/100*$width,$height,$optBelow[$blok],1,0,$align[$i],1);
                $i++;
                foreach($row as $cont) {
                                        if($_SESSION['language']=='EN'){
                        if($cont=='TM')
                            $cont='Mature';
                        if($cont=='TBM')
                            $cont='Imature';
                        if($cont=='TB')
                            $cont='NewPlanting';
                        if($cont=='CADANGAN')
                            $cont='Reserved';   
                        if($cont=='BBT')
                            $cont='Nursery';                           
                    }
                    $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                    $i++;
                }
                $pdf->Ln();
            }
            # Sub Total
            $lenJudul = $length[0]+$length[1]+$length[2]+$length[3]+$length[4]+$length[5];
            $pdf->Cell($lenJudul/100*$width,$height,'Sub Total Tahun Tanam '.$afd,1,0,'L',1);
            $i=6;
            foreach($total[$afd] as $head=>$val) {
                if($head=='kerapatan') {
                    $pdf->Cell($length[$i]/100*$width,$height,number_format($val,0),1,0,$align[$i],1);
                } else {
                    $pdf->Cell($length[$i]/100*$width,$height,number_format($val,2),1,0,$align[$i],1);
                }
                $i++;
            }
            $pdf->Ln();
        }
        # Grand Total
        $lenJudul = $length[0]+$length[1]+$length[2]+$length[3]+$length[4]+$length[5];
        $pdf->Cell($lenJudul/100*$width,$height,'Grand Total',1,0,'L',1);
        $i=6;
        foreach($gTotal as $head=>$val) {
            if($head=='kerapatan') {
                $pdf->Cell($length[$i]/100*$width,$height,number_format($val,0),1,0,$align[$i],1);
            } else {
                $pdf->Cell($length[$i]/100*$width,$height,number_format($val,0),1,0,$align[$i],1);
            }
            $i++;
        }
        $pdf->Ln();
        
        $pdf->Output();
        break;
    default:
        if($mode=='excel') {
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='arealstatement' class='sortable' cellspacing=1 boreder=0>";
            $tab .= "<thead><tr class='rowheader'>";
        }
        foreach($theCols as $head) {
            $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        foreach($data as $afd=>$row) {
            $tmpRow = count($row)-1;
            $i=0;$afdC=false;$blankC=false;
            foreach($row as $blok=>$row2) {
                $tab .= "<tr class='rowcontent'>";
                if($afdC==false) {
                    $tab .= "<td id='afd_".$i."' value='".$afd."'>".$afd."</td>";
                    $afdC = true;
                } else {
                    if($blankC==false) {
                        $tab .= "<td id='afd_".$i."' rowspan='".$tmpRow."'></td>";
                        $blankC = true;
                    }
                }
                $tab .= "<td id='blok_".$i."' value='".$blok."'>".$optBelow[$blok]."</td>";
                foreach($row2 as $field=>$cont) {
                    if($_SESSION['language']=='EN'){
                        if($dataShow[$afd][$blok][$field]=='TM')
                            $dataShow[$afd][$blok][$field]='Mature';
                        if($dataShow[$afd][$blok][$field]=='TBM')
                            $dataShow[$afd][$blok][$field]='Imature';
                        if($dataShow[$afd][$blok][$field]=='TB')
                            $dataShow[$afd][$blok][$field]='NewPlanting';
                        if($dataShow[$afd][$blok][$field]=='CADANGAN')
                            $dataShow[$afd][$blok][$field]='Reserved';   
                        if($dataShow[$afd][$blok][$field]=='BBT')
                            $dataShow[$afd][$blok][$field]='Nursery';                           
                    }
                    if($field=='topografi'){
                            $tab .= "<td><b>".$arrTopografo[$dataShow[$afd][$blok][$field]]."</b></td>";
                    }else{
                        $tab .= "<td id='".$field."_".$i."' value='".$cont."' align='right'>".$dataShow[$afd][$blok][$field]."</td>";
                    }
                }
                $i++;
                $tab .= "</tr>";
            }
            # Sub Total
            $tab .= "<tr class='rowcontent'>";
            $tab .= "<td colspan='6' align='right'><b>Sub Total Tahun Tanam ".$afd."</b></td>";
            foreach($total[$afd] as $head=>$val) {
                if($head=='kerapatan') {
                    $tab .= "<td align='right'><b>".number_format($val,0)."</b></td>";
                } else {
                    $tab .= "<td align='right'><b>".number_format($val,2)."</b></td>";
                }
                
            }
            $tab .= "</tr>";
        }
        # Grand Total
        $tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='6' align='right'><b>Grand Total</b></td>";
        foreach($gTotal as $head=>$val) {
            if($head=='kerapatan') {
                $tab .= "<td align='right'><b>".number_format($val,0)."</b></td>";
            } else {
                $tab .= "<td align='right'><b>".number_format($val,2)."</b></td>";
            }
        }
        $tab .= "</tr>";
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        # Output Type
        if($mode=='excel') {
            $stream = $tab;
            $nop_="ArealStatement";
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
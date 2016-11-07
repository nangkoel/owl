<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

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

# Validasi Periode
$periode1 = $param['periode_from'];
$periode2 = $param['periode_until'];
#1. Empty
if($periode1=='' or $periode2=='') {
    echo 'Warning : Transaction period required';
    exit;
}
#2. Range Terbalik
if(tanggalsystem($periode1)>tanggalsystem($periode2)) {
    $tmp = $periode1;
    $periode1 = $periode2;
    $periode2 = $tmp;
}

//ambil namaunit
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$param['kodeorg']."'";
$namagudang='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namagudang=strtoupper($bar->namaorganisasi);
}

switch($level) {
    case '0':
        # Data
        # Range Akun Kas
        $whereAKB = "kodeaplikasi='GL' and aktif=1 and jurnalid in ('KK','KM')";
	$queryAKB = selectQuery($dbname,'keu_5parameterjurnal','noakundebet,sampaidebet',$whereAKB);
	$optAKB = fetchData($queryAKB);
        $mulaidebet = $optAKB[0]['noakundebet'];
	$sampaidebet = $optAKB[0]['sampaidebet'];
	if($optAKB[1]['noakundebet']<$mulaidebet or $mulaidebet=='') {
	    $mulaidebet = $optAKB[1]['noakundebet'];
	}
	if($optAKB[1]['sampaidebet']>$sampaidebet) {
	    $sampaidebet = $optAKB[1]['sampaidebet'];
	}
        #jika no akun dipilih(tidak seluruhnya)
        if($param['noakun']!=0)
        {
           $mulaidebet= $param['noakun'];
           $sampaidebet=$param['noakun'];
        }
	
        # Header Kas Bank
        if(isset($param['kodeorg']))
            $kodeorg=$param['kodeorg'];
        else
            $kodeorg=$_SESSION['empl']['lokasitugas'];
        
        $cols = "noakun,notransaksi,tipetransaksi,tanggal,jumlah,keterangan";
        $where = "tanggal>='".tanggalsystem($periode1)."' and tanggal<='".
            tanggalsystem($periode2)."' and noakun>='".$mulaidebet.
            "' and noakun<='".$sampaidebet."' and ".
            "kodeorg='".$kodeorg."' and posting=1";
        $query = selectQuery($dbname,'keu_kasbankht',$cols,$where,"tanggal,notransaksi");

	$resH = fetchData($query);
	if(empty($resH)) {
	    echo 'Warning : No data found';
	    exit;
	}
	
	# Summary Saldo sebelumnya
        $persbl=substr(tanggalsystem($periode1),0,6);
	$cols2 = "sum(jumlah) as jumlah,tipetransaksi";
        $where2 = "tanggal<'".tanggalsystem($periode1)."' and tanggal>='".$persbl."01"."' 
            and noakun>='".$mulaidebet.
            "' and noakun<='".$sampaidebet."' and ".
            "kodeorg='".$kodeorg."' and posting=1 group by tipetransaksi";
        $query2 = selectQuery($dbname,'keu_kasbankht',$cols2,$where2,"tanggal,notransaksi");
        $res=mysql_query($query2);
        $saldoAwal=0;
        while($bar=mysql_fetch_object($res))
        {
            if($bar->tipetransaksi=='M')
                $saldoAwal+=$bar->jumlah;
            else
                 $saldoAwal-=$bar->jumlah;
        }
 
       $query1 = "select b.jumlah, b.tipetransaksi from ".$dbname.".keu_kasbankdt b 
                      left join ".$dbname.".keu_kasbankht a on b.notransaksi=a.notransaksi
                      where b.noakun = '".$param['noakun']."' and b.kodeorg='".$kodeorg."' and
                      a.tanggal>='".$persbl."01"."' and a.tanggal<'".tanggalsystem($periode1)."' ";

        $res=mysql_query($query1);   
        while($bar=mysql_fetch_object($res))
        {
            if($bar->tipetransaksi=='K')
                $saldoAwal+=$bar->jumlah;
            else
                $saldoAwal-=$bar->jumlah;
        }           

        #ambil saldo akhir bl lalu
        $thnawl=substr($persbl,0,4);
        $blnawl=substr($persbl,4,2);
	$queryx="select sum(awal".$blnawl.") as jumlah from ".$dbname.".keu_saldobulanan where noakun>='".$mulaidebet.
            "' and noakun<='".$sampaidebet."' and ".
            "kodeorg='".$kodeorg."' and periode='".$persbl."'";
        $res=mysql_query($queryx);
        while($bar=mysql_fetch_object($res))
        {
            $saldoAwal+=$bar->jumlah;
        }
	# Rearrange Data & Total
	$saldoKK=0;$saldoKM=0;
	$data = array();
	foreach($resH as $key=>$row) {
	    $data[$key] = array(
		'no'=>$key+1,
                'tanggal'=>tanggalnormal($row['tanggal']),
		'keterangan'=>$row['keterangan'],
		'km'=>'',
		'saldokm'=>'',
		'kk'=>'',
		'saldokk'=>''
	    );
	    if($row['tipetransaksi']=='K') {
		$data[$key]['kk'] = $row['notransaksi'];
		$data[$key]['saldokk'] = $row['jumlah'];
		$saldoKK+=$row['jumlah'];
	    } else {
		$data[$key]['km'] = $row['notransaksi'];
		$data[$key]['saldokm'] = $row['jumlah'];
		$saldoKM+=$row['jumlah'];
	    }
            $z=$key;
	}
      
   // paling terakhir                
   $query1 = "select b.jumlah, b.tipetransaksi,b.keterangan2,b.notransaksi,a.tanggal from ".$dbname.".keu_kasbankdt b 
              left join ".$dbname.".keu_kasbankht a on b.notransaksi=a.notransaksi
              where b.noakun = '".$param['noakun']."' and b.kodeorg='".$kodeorg."' and
              a.tanggal>='".tanggalsystem($periode1)."' and a.tanggal<='".tanggalsystem($periode2)."'";
    $resH1 = fetchData($query1);
            foreach($resH1 as $key=>$row) {
                $z+=1;
            $data[$z] = array(
                'no'=>$z+1,
                'tanggal'=>tanggalnormal($row['tanggal']),
                'keterangan'=>$row['keterangan2'],
                'km'=>'',
                'saldokm'=>'',
                'kk'=>'',
                'saldokk'=>''
            );
            if($row['tipetransaksi']=='M') {
                $data[$z]['kk'] = $row['notransaksi'];
                $data[$z]['saldokk'] = $row['jumlah'];
                $saldoKK+=$row['jumlah'];
            } else {
                $data[$z]['km'] = $row['notransaksi'];
                $data[$z]['saldokm'] = $row['jumlah'];
                $saldoKM+=$row['jumlah'];
            }

        }
        
//echo "<pre>";
//print_r($data);
//echo "</pre>";
        
if(!empty($data)) foreach($data as $c=>$key) {
    $sort_tangg[] = $key['tanggal'];
    $sort_debet[] = $key['saldokm'];
}

// sort
if(!empty($data))array_multisort($sort_tangg, SORT_ASC, $sort_debet, SORT_DESC, $data);        
	
	$dataShow = $data;
	$dataExcel = $data;
	foreach($dataShow as $key=>$row) {
	    $row['saldokk']!='' ? $dataShow[$key]['saldokk'] = number_format($row['saldokk'],0) : null;
	    $row['saldokm']!='' ? $dataShow[$key]['saldokm'] = number_format($row['saldokm'],0) : null;
	}
	
        # Report Gen
        $theCols = array(
            $_SESSION['lang']['nomor'],
            $_SESSION['lang']['tanggal'],
            $_SESSION['lang']['keterangan'],
            $_SESSION['lang']['kasmasuk'],
            $_SESSION['lang']['penerimaan'],
            $_SESSION['lang']['kaskeluar'],
            $_SESSION['lang']['pengeluaran'],
        );
	$align = explode(",","L,R,L,R,R,R,R");
	
	# Total for not excel
	if($mode!='excel') {
	    //$saldoTerbilang = $saldoKM-$saldoKK;
            $saldoTerbilang = round($saldoKM +$saldoAwal-$saldoKK);
	    $saldoSelisih = number_format(($saldoKM +$saldoAwal-$saldoKK),0);
	   //tambahan jamhari
	    $saldoKM = number_format(($saldoKM+$saldoAwal),0);
            $saldoAwal = number_format($saldoAwal,0);
           //end tambahan
	    $saldoKK = number_format($saldoKK,0);
	} else {
	    $saldoSelisih = $saldoKM +$saldoAwal - $saldoKK;
	}
        break;
    default:
    break;
}

switch($mode) {
    case 'pdf':
        /** Report Prep **/
	# Options
	$optJab = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan',
	    "kodejabatan='".$_SESSION['empl']['kodejabatan']."'");
	
        $colPdf = array('nomor','tanggal','keterangan','kasmasuk','penerimaan',
            'kaskeluar','pengeluaran');
        $title = $_SESSION['lang']['kasharian'];
        $length = explode(",","5,12,35,10,14,10,14");
        
        $pdf = new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	
	# Saldo Awal
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell($length[0]/100*$width,$height,'','TLR',0,'C',1);
	$pdf->Cell($length[1]/100*$width,$height,'','TLR',0,'C',1);        
	$pdf->Cell($length[2]/100*$width,$height,'Saldo Awal '.$periode1,'TLR',0,'C',1);
	$pdf->Cell($length[3]/100*$width,$height,'','TLR',0,'R',1);
	$pdf->Cell($length[4]/100*$width,$height,$saldoAwal,'TLR',0,'R',1);
	$pdf->Cell($length[5]/100*$width,$height,'','TLR',0,'R',1);
	$pdf->Cell($length[6]/100*$width,$height,'','TLR',0,'R',1);
	$pdf->Ln();
	
	# Content
	$pdf->SetFont('Arial','',9);
        foreach($dataShow as $key=>$row) {
	    $i=0;
            foreach($row as $head=>$cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,'LR',0,$align[$i],1);
                $i++;
            }
	    $pdf->Ln();
        }
        
	$lenJudul = $length[0]+$length[1]+$length[2]+$length[3];
        # Total
	$pdf->Cell($lenJudul/100*$width,$height,'','TLR',0,'L',1);
        $pdf->Cell($length[4]/100*$width,$height,$saldoKM,'TLR',0,$align[3],1);
	$pdf->Cell($length[5]/100*$width,$height,'','TLR',0,$align[4],1);
	$pdf->Cell($length[6]/100*$width,$height,$saldoKK,'TLR',0,$align[5],1);
	$pdf->Ln();
	# Saldo
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell($lenJudul/100*$width,$height,$_SESSION['lang']['saldo'],'LR',0,'C',1);
        $pdf->Cell($length[4]/100*$width,$height,'','LR',0,$align[3],1);
	$pdf->Cell($length[5]/100*$width,$height,'','LR',0,$align[4],1);
	$pdf->Cell($length[6]/100*$width,$height,$saldoSelisih,'LR',0,$align[5],1);
	$pdf->Ln();
	# Jumlah
	$pdf->Cell($lenJudul/100*$width,$height,$_SESSION['lang']['jumlah'],'LR',0,'C',1);
	$pdf->SetFont('Arial','',9);
        $pdf->Cell($length[4]/100*$width,$height,$saldoKM,'BLR',0,$align[3],1);
	$pdf->Cell($length[5]/100*$width,$height,'','BLR',0,$align[4],1);
	$pdf->Cell($length[6]/100*$width,$height,$saldoKM,'BLR',0,$align[5],1);
	$pdf->Ln();
	$pdf->Cell($lenJudul/100*$width,$height,'','L',0,$align[4],1);
	$pdf->Cell((100-$lenJudul)/100*$width,$height,'','TR',0,$align[4],1);
	$pdf->Ln();
	
	# Terbilang
	$pdf->SetFont('Arial','I',9);
	$pdf->MultiCell($width,$height,
	    'Terbilang : [ '.terbilang($saldoTerbilang,0)." rupiah. ]",'LR','L');
	$pdf->Cell($width,$height,'','LR',0,$align[4],0);
	$pdf->Ln();
	
	# Tempat, Tanggal
	$pdf->SetFont('Arial','',9);
	$pdf->Cell(2/3*$width,$height,'','L',0,$align[4],0);
	$pdf->Cell(1/3*$width,$height,$periode1,'R',0,'C',0);
	$pdf->Ln();
	
	# Mengetahui dll
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['mengetahui'],'L',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['diperiksa'],0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['lang']['disetujui'],'R',0,'C',0);
	$pdf->Ln();
	
	# Add few line
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	$pdf->Cell($width,$height,'','LR',1,$align[4],0);
	
	# Nama
	$pdf->SetFont('Arial','BU',9);
	$pdf->Cell(1/3*$width,$height,'                  ','L',0,'C',0);
	$pdf->Cell(1/3*$width,$height,'                  ','',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$_SESSION['empl']['name'],'R',0,'C',0);
	$pdf->Ln();
	
	# Jabatan
	$pdf->SetFont('Arial','I',9);
	$pdf->Cell(1/3*$width,$height,'','LB',0,'C',0);
	$pdf->Cell(1/3*$width,$height,'','B',0,'C',0);
	$pdf->Cell(1/3*$width,$height,$optJab[$_SESSION['empl']['kodejabatan']],'RB',0,'C',0);
        
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
            //tambahan jamhari
        $sald=intval($saldoKM+$saldoAwal);
        $saldoKM=number_format($sald,0);
        //end tambahan
            $tab = strtoupper($_SESSION['lang']['kasharian'])." : ".$namagudang."<br>".
        strtoupper($_SESSION['lang']['noakun'])." : ".$param['noakun']."<br>".
        strtoupper($_SESSION['lang']['periode'])." : ".$periode1." s/d ".$periode2.
        "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='kasharian' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }
	
	/** Generate Table **/
        foreach($theCols as $head) {
            $tab .= "<td>".$head."</td>";
        }
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        
	# Saldo Awal
	$tab .= "<tr class='rowcontent'>";
	$tab .= "<td></td><td align='center' colspan=2>Saldo Awal ".$periode1."</td>";
	$tab .= "<td></td><td align='right'>".$saldoAwal."</td>";
	$tab .= "<td></td><td></td>";
	$tab .= "</tr>";
	
	# Content
	foreach($data as $key=>$row) {
            $tab .= "<tr class='rowcontent'>";
	    $i=0;
	    foreach($row as $head=>$cont) {
		if($mode=='excel') {
		    $tab .= "<td align='".$alignPrev[$i]."'>".$dataExcel[$key][$head]."</td>";
		} else {
		    $tab .= "<td align='".$alignPrev[$i]."'>".$dataShow[$key][$head]."</td>";
		}
		$i++;
	    }
	    $tab .= "</tr>";
        }
        
	# Grand Total
        
        $tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='4' align='right'></td>";
        $tab .= "<td align='right'>".$saldoKM."</td><td></td>";
	$tab .= "<td align='right'>".$saldoKK."</td>";
        $tab .= "</tr>";
	# Saldo
	$tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='4' align='center'>".$_SESSION['lang']['saldo']."</td>";
        $tab .= "<td align='right'></td><td></td>";
	$tab .= "<td align='right'><b>".$saldoSelisih."</b></td>";
        $tab .= "</tr>";
	# Jumlah
	$tab .= "<tr class='rowcontent'>";
        $tab .= "<td colspan='4' align='center'>".$_SESSION['lang']['jumlah']."</td>";
        $tab .= "<td align='right'>".$saldoKM."</td><td></td>";
	$tab .= "<td align='right'>".$saldoKM."</td>";
        $tab .= "</tr>";
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="KasHarian_".$kodeorg;
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
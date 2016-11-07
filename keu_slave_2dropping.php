<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');


$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

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



$tglBgt=tanggalsystem($param['periode_from']);
$thnBgt=substr($tglBgt,0,4);
$blnBgt=substr($tglBgt,4,2);




	
	

	

//and a.notransaksi like '%".$param['kdorg']."%'

/*$query = "select a.tanggal,b.keterangan2,b.jumlah,a.keterangan,a.kodeorg,'Sudah terbayar' from ".$dbname.".keu_kasbankht a left join ".$dbname.".keu_kasbankdt b
	on a.notransaksi = b.notransaksi where a.tanggal between '".tanggalsystem($periode1)."' and '".tanggalsystem($periode2).
	"' and a.notransaksi like '%".$param['kdorg']."%' and a.posting=1 and a.tipetransaksi='K'";//echo $query;*/
$query = "select a.tanggalposting as tanggal,a.nobayar,b.noakun,b.keterangan2,b.jumlah,a.keterangan,'Sudah terbayar',a.kodeorg from "
        .$dbname.".keu_kasbankht a left join ".$dbname.".keu_kasbankdt b
	on a.notransaksi = b.notransaksi where a.tanggalposting between '".tanggalsystem($periode1)."' and '".tanggalsystem($periode2).
	"' and a.kodeorg='".$param['kodeorg']."'  and a.posting=1 and a.tipetransaksi='K' 
        order by b.noakun,a.tanggalposting";//echo $query;
$data = fetchData($query);


$optKary = makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$qJab = "select a.karyawanid,b.namajabatan from ".$dbname.".datakaryawan a left join ".$dbname.".sdm_5jabatan b
	on a.kodejabatan = b.kodejabatan where a.karyawanid in (".$param['pengaju'].",".$param['pemeriksa'].",".$param['penyetuju'].")";
$resJab = fetchData($qJab);
$optJab = array();

$sCount = "select b.noakun, count(b.noakun) as jumlah from "
        .$dbname.".keu_kasbankht a left join ".$dbname.".keu_kasbankdt b
	on a.notransaksi = b.notransaksi where a.tanggalposting between '".tanggalsystem($periode1)."' and '".tanggalsystem($periode2).
	"' and a.kodeorg='".$param['kodeorg']."'  and a.posting=1 and a.tipetransaksi='K'
        group by b.noakun order by b.noakun,a.tanggalposting";//echo $query;
$resCount = fetchData($sCount);
foreach ($resCount as $key => $jumlah) {
    $maxRow[$jumlah['noakun']]=$jumlah['jumlah'];
    
}



//echo $tglBgt.___.$thnBgt.___.$blnBgt;








foreach($resJab as $r) {
	$optJab[$r['karyawanid']] = $r['namajabatan'];
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
	
	
	
	
	//exit("Error:MASUK");
		/** Generate Table **/
		$tab = "<tr style='font-weight:bold'>
			<td>To</td><td colspan=3>: ".$optKary[$param['untuk']]."</td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td>CC</td><td colspan=3>: ".$optKary[$param['cc']]."</td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td>From</td><td colspan=3>: ".$optKary[$_SESSION['standard']['userid']]."</td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td>Date</td><td colspan=3>: ".date('d F Y')."</td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td>No</td><td colspan=3>: ".$param['nodok']."</td></tr>";
			
		if($param['kodeorg']=='')
			$kdorgC=":  Regional ".$_SESSION['empl']['regional'];
		else
			$kdorgC=": ".$param['kodeorg']." [".$nmOrg[$param['kodeorg']]."]";
		
		$tab .= "<tr style='font-weight:bold'>
			<td>Unit</td><td colspan=3>".$kdorgC."</td></tr><tr><td><br></td></tr>";
	
		
	//	exit("Error:$a");
	
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=3>Permintaan Penggantian Dana</td></tr>";
				
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=4>Realisasi Per. ".$periode1." s/d ".$periode2."</td></tr><tr><td><br></td></tr>";
		$tab .= "<tr style='font-weight:bold' class=\"rowheader\">
		
			<td style='text-align:center'>Realisasi</td>
			<td style='text-align:center'>No. Pembayaran</td>
			<td style='text-align:center'>No akun</td>
			<td style='text-align:center'>Uraian</td>
			<td style='text-align:center'>Jumlah</td>
			
			
			<td style='text-align:center'>Keterangan</td>
			<td style='text-align:center'>Status</td>
		</tr>";

/*$dtBgt=array();
$i="select kodeorg,noakun,sum(rp".$blnBgt.") as rupiah from ".$dbname.".bgt_budget_detail where tahunbudget='".$thnBgt."'  and left(kodeorg,4)='".$param['kodeorg']."' group by left(kodeorg,4),noakun";
$n=mysql_query($i) or die (mysql_error($i));
while($d=mysql_fetch_assoc($n))
{
	$dtBgt[$d['kodeorg']][$d['noakun']]=$d['rupiah'];
	//$dataJur[$c['nik']]=$c['jumlah'];
}


echo "<pre>";
print_r($dtBgt);
echo "</pre>";*/

//print_r($dtBgt);
		
		
		
		
		$total = 0;
		foreach($data as $row) {
			$total += $row['jumlah'];
			$tab .= "<tr>";
			foreach($row as $attr=>$val) {
				if ($attr=='noakun'){
                                    if($tmpAkun!=$val){
                                        $tmpAkun=$val;
                                        $hitung=1;
                                    } else {
                                        $hitung+=1;
                                    }
                                }
				
				
								
				
				
				 if($attr=='jumlah') {
					$tab .= "<td style='text-align:right'>".number_format($val,2);
                                        $subTotal[$tmpAkun]+=$val;
				} elseif($attr=='tanggal') {
					$tab .= "<td>".tanggalnormal($val);
				}
				//else if ($attr=='noakun')
				//{
					
					//$tab .= "<td style='text-align:right'>";
						//$tab .= "<td style='text-align:right'>".number_format($d['rupiah']);
					
				
				//}
				
				
				
				 else {
					$tab .= "<td>".$val;
				}
				$tab .= "</td>";
			}
			$tab .= "</tr>";
                        if ($hitung==$maxRow[$tmpAkun]){
                        $tab .= "<tr style='font-weight:bold'>
                                <td colspan=3 style='text-align:center'>Sub Total</td>
                                <td colspan=2 style='text-align:right;border-top:1px solid;border-bottom:1px double'>: ".number_format($subTotal[$tmpAkun],2)."</td></tr>";
                        }
		}
		$tab .= "<tr><td><br></td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=3 style='text-align:center'>Jumlah</td>
			<td colspan=2 style='text-align:right;border-top:1px solid;border-bottom:1px double'>: ".number_format($total,2)."</td></tr>";
		$tab .= "<tr><td><br></td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=3>Diajukan,</td>
			<td colspan=3>Diperiksa,</td>
			<td colspan=3>Menyetujui,</td></tr>";
		$tab .= "<tr><td><br></td></tr>";
		$tab .= "<tr><td><br></td></tr>";
		$tab .= "<tr><td><br></td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=3 style='text-decoration:underline'>".$optKary[$param['pengaju']]."</td>
			<td colspan=3 style='text-decoration:underline'>".$optKary[$param['pemeriksa']]."</td>
			<td colspan=3 style='text-decoration:underline'>".$optKary[$param['penyetuju']]."</td></tr>";
		$tab .= "<tr style='font-weight:bold'>
			<td colspan=3>".$optJab[$param['pengaju']]."</td>
			<td colspan=3>".$optJab[$param['pemeriksa']]."</td>
			<td colspan=3>".$optJab[$param['penyetuju']]."</td></tr>";
		$tab .= "</table>";
		
        /** Output Type **/
        if($mode=='excel') {
            $tab = "<table>".$tab;
            $stream = $tab;
            $nop_="Dropping_".date('YmdHis');
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
            $tab = "<table>".$tab;
            echo $tab;
        }
        break;
}
?>
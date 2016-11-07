<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kodeOrg=$_GET['kdUnit']:$kodeOrg=$_POST['kdUnit'];
$_POST['thnBudget']==''?$thnBudget=$_GET['thnBudget']:$thnBudget=$_POST['thnBudget'];
$_POST['kdTraksi']==''?$kdTraksi=$_GET['kdTraksi']:$kdTraksi=$_POST['kdTraksi'];
$_POST['kdVhc']==''?$kdVhc=$_GET['kdVhc']:$kdVhc=$_POST['kdVhc'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmbrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where karyawanid=".$_SESSION['standard']['userid']. "";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namakar[$bar->karyawanid]=$bar->namakaryawan;
}


$where=" kodetraksi='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$sKodeOrg="select * from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi where  ".$where." order by tahunbudget asc";
//echo $sKodeOrg;
$qKodeOrg=mysql_query($sKodeOrg) or die(mysql_error($conn));
while($rKode=mysql_fetch_assoc($qKodeOrg))
{
    $dtKdtraksi[]=$rKode['kodetraksi'];
    $dtKdvhc[]=$rKode['kodevhc'];
    $dtRpSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']]=$rKode['rpsetahun'];
    $dtJamSthn[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']]=$rKode['jamsetahun'];
    $dtRpJam[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']]=$rKode['rpperjam'];
    $dtAlokasi[$rKode['tahunbudget']][$rKode['kodetraksi']][$rKode['kodevhc']]=$rKode['teralokasi'];
}

$cek=count($dtKdtraksi);



switch($proses)
{			
			
	case'preview':
			
			//$no=0;
			if($kodeOrg==''||$thnBudget=='')
			{
				exit("Error:Field Tidak Boleh Kosong");
			}
            if($cek==0)
            {
            exit("Error: Data Kosong");
            }
            $tab="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td align=center>No.</td>";
            $tab.="<td align=center>".$_SESSION['lang']['kodetraksi']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['kodevhc']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['jamperthn']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['rpperthn']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['kmperthn']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['alokasijam']."</td>";
            //$tab.="<td align=center>".$_SESSION['lang']['alokasirp']."</td>";
            $tab.="<td align=center>".$_SESSION['lang']['action']."</td>";
            $tab.="</tr></thead><tbody>";
            
            
                
                 $terAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]]=$dtAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]]*$dtRpJam[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]];
                foreach($dtKdvhc as $lisTraksi)
            {
				$no+=1;
				 $tab.="<tr class=rowcontent>";
                 $tab.="<td align=center>".$no."</td>";
                 $tab.="<td align=center>".$kodeOrg."</td>";
		   
                 $tab.="<td align=center>".$lisTraksi."</td>";
                 $tab.="<td align=right>".number_format($dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab.="<td align=right>".number_format($dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab.="<td align=right>".number_format($dtRpJam[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab.="<td align=right>".number_format($dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 //$tab.="<td align=right>".number_format($terAlokasi[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                   $tab.="<td align=center>
                       <button class=\"mybutton\" name=\"preview\" id=\"preview\" onclick=\"getAlokasi('".$kodeOrg."','".$lisTraksi."','".$thnBudget."')\">".$_SESSION['lang']['alokasi']."</button>
                           <button class=\"mybutton\" name=\"preview\" id=\"preview\" onclick=\"getBiaya('".$kodeOrg."','".$lisTraksi."','".$thnBudget."')\">".$_SESSION['lang']['biayaRinci']."</button>
                       </td>";
                 $tab.="</tr>";
                 $totJam+=$dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi];
                 $totRup+=$dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi];
                 $totKmThn+=$dtRpJam[$thnBudget][$kodeOrg][$lisTraksi];
				 $totAlokasiJam+=$dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
				 
                 //$totAlokasiRp+=$terAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
            }
            //$no!=0?$rataRupi=$totRup/$no:$rataRupi=0;
            //$totJam!=0?$totRpkm=$rataRupi/$totJam:$totRpkm=0;
            
            $tab.="</tbody><thead><tr class=rowheader>";
            $tab.="<td align=center  colspan=3 align=center>".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right>".number_format($totJam,2)."</td>";
            $tab.="<td align=right>".number_format($totRup,2)."</td>";
            $tab.="<td align=right>".number_format($totKmThn,2)."</td>";
            $tab.="<td align=right>".number_format($totAlokasiJam,2)."</td>";
            //$tab.="<td align=right>".number_format($totAlokasiRp,2)."</td>";
            $tab.="<td align=right>&nbsp</td>";
            $tab.="</tr>";
            $tab.="</thead></table>";
            echo $tab;
	break;
            
			
			
			
	case 'excel':
			
			if($thnBudget=='')
			{
				echo "warning : Tahun masih kosong";
				exit();	
			}
			else if($kodeOrg=='')
			{
				echo "warning : Kode organisasi masih kosong";
				exit();	
			}
			
			$tab2="Laporan Rp/Jam per Kendaraan <br>";
			$tab2.=" ".$optNm[$kodeOrg]."  tahun ".$thnBudget." ";
			$tab2.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
            $tab2.="<tr class=rowheader bgcolor=#CCCCCC>";
            $tab2.="<td align=center>No.</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['kodetraksi']."</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['kodevhc']."</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['jamperthn']."</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['rpperthn']."</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['kmperthn']."</td>";
            $tab2.="<td align=center>".$_SESSION['lang']['alokasijam']."</td>";
            //$tab.="<td align=center>".$_SESSION['lang']['alokasirp']."</td>";
            $tab2.="</tr></thead><tbody>";
            
            
                
                 $terAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]]=$dtAlokasi[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]]*$dtRpJam[$thnBudget][$lisTraksi][$dtKdvhc[$thnBudget][$lisTraksi]];
                foreach($dtKdvhc as $lisTraksi)
            {
				$no+=1;
				 $tab2.="<tr class=rowcontent>";
                 $tab2.="<td align=center>".$no."</td>";
                 $tab2.="<td align=center>".$kodeOrg."</td>";
		   
                 $tab2.="<td align=center>".$lisTraksi."</td>";
                 $tab2.="<td align=right>".number_format($dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab2.="<td align=right>".number_format($dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab2.="<td align=right>".number_format($dtRpJam[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab2.="<td align=right>".number_format($dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 //$tab.="<td align=right>".number_format($terAlokasi[$thnBudget][$kodeOrg][$lisTraksi],2)."</td>";
                 $tab2.="</tr>";
                 $totJam+=$dtJamSthn[$thnBudget][$kodeOrg][$lisTraksi];
                 $totRup+=$dtRpSthn[$thnBudget][$kodeOrg][$lisTraksi];
                 $totKmThn+=$dtRpJam[$thnBudget][$kodeOrg][$lisTraksi];
				 $totAlokasiJam+=$dtAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
				 
                 //$totAlokasiRp+=$terAlokasi[$thnBudget][$kodeOrg][$lisTraksi];
            }
            //$no!=0?$rataRupi=$totRup/$no:$rataRupi=0;
            //$totJam!=0?$totRpkm=$rataRupi/$totJam:$totRpkm=0;
            
            $tab2.="</tbody><thead><tr class=rowheader bgcolor=#CCCCCC>";
            $tab2.="<td align=center  colspan=3 align=center>".$_SESSION['lang']['total']."</td>";
            $tab2.="<td align=right>".number_format($totJam,2)."</td>";
            $tab2.="<td align=right>".number_format($totRup,2)."</td>";
            $tab2.="<td align=right>".number_format($totKmThn,2)."</td>";
            $tab2.="<td align=right>".number_format($totAlokasiJam,2)."</td>";
            //$tab.="<td align=right>".number_format($totAlokasiRp,2)."</td>";
            $tab2.="</tr>";
            $tab2.="</thead></table>";
		
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Exel_".$tglSkrg;
		//$nop_"Laporan Daftar Asset ".$nmOrg."_".$nmAst;
		//$nop_="Daftar Asset : ".$nmOrg." ".$nmAst;
		if(strlen($tab2)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$tab2))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}           
		break;			
		// tutup tampilakn panggil exel //
		
		
		
		
	case'pdf':
	
		if($thnBudget=='')
			{
				echo "warning : Tahun masih kosong";
				exit();	
			}
			else if($kodeOrg=='')
			{
				echo "warning : Kode organisasi masih kosong";
				exit();	
			}
		
		//buat header pdf
		class PDF extends FPDF
		{
            function Header() 
			{
				global $nmOrg;
				global $optNm;
				global $thnBudget;
				global $kodeOrg;
				global $kdUnit;
				global $totRp;
				global $conn;
				global $dbname;
				global $align;
				global $length;
				global $colArr;
				global $title;
				global $total;
				global $namakar;
				
				//total
				global $totJam;
                global $totRup;
                global $totKmThn;
				global $totAlokasiJam;

				/*global $dataKary;
				global $dataKaryIstri;
				global $dataTanggugan;
				global $dtKode2;
				global $kodeOrg;
				
				global $dataTipeKary;
				global $totalTipe;
				global $dbname;*/
            
        //alamat PT minanga dan logo
						$query = selectQuery($dbname,'organisasi','alamat,telepon',
							"kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
						$orgData = fetchData($query);
						
						$width = $this->w - $this->lMargin - $this->rMargin;
						$height = 20;
						if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
						$this->Image($path,$this->lMargin,$this->tMargin,70);	
						$this->SetFont('Arial','B',9);
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
						$this->SetFont('Arial','B',10);
						$this->Cell((20/100*$width)-5,$height,"Biaya Kendaraan",'',0,'L');
						$this->Ln();
						$this->SetFont('Arial','',10);
						$this->Cell((100/100*$width)-5,$height,"Printed By : ".$namakar[$_SESSION['standard']['userid']],'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Date : ".date('d-m-Y'),'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Time : ".date('h:i:s'),'',0,'R');
						$this->Ln();
						$this->Ln();
						//tutup sub judul
						
						//judul tengah
						$this->SetFont('Arial','B',12);
						$this->Cell($width,$height,strtoupper("Biaya Kendaraan ".$optNm[$kodeOrg]),'',0,'C');
						$this->Ln();
						$this->Cell($width,$height,strtoupper("Tahun " .$thnBudget),'',0,'C');
						$this->Ln();
						$this->Ln();
						//tutup judul tengah
						
						//isi atas tabel
						$this->SetFont('Arial','B',10);
						$this->SetFillColor(220,220,220);
						$this->Cell(2/100*$width,$height,"No",1,0,'C',1);
						$this->Cell(13/100*$width,$height,$_SESSION['lang']['kodetraksi'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['jamperthn'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['rpperthn'],1,0,'C',1);
						$this->Cell(10/100*$width,$height,$_SESSION['lang']['kmperthn'],1,0,'C',1);
						$this->Cell(13/100*$width,$height,$_SESSION['lang']['alokasijam'],1,1,'C',1);
						//$this->Cell(15/100*$width,$height,$_SESSION['lang']['alokasirp'],1,1,'C',1);
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
				$pdf=new PDF('L','pt','Legal');//untuk kertas L=len p=pot
				$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
				$height = 20;
				$pdf->AddPage();
				$pdf->SetFillColor(255,255,255);
				$pdf->SetFont('Arial','',10);//ukuran tulisan
				//tutup tampilan setting
/*		SELECT *
FROM `bgt_biaya_jam_ken_vs_alokasi`
WHERE `tahunbudget` =2011
AND `kodetraksi` LIKE 'SSRO31'
LIMIT 0 , 30*/
		
				//isi tabel dan tabelnya
				//$no=0;
				$sql="select * from ".$dbname.".bgt_biaya_jam_ken_vs_alokasi where tahunbudget='".$thnBudget."' and kodetraksi='".$kodeOrg."' ";
				//echo $sql;
				$qDet=mysql_query($sql) or die(mysql_error());
				while($res=mysql_fetch_assoc($qDet))
				{
					$no+=1;
					$pdf->Cell(2/100*$width,$height,$no,1,0,'C',1);
					$pdf->Cell(13/100*$width,$height,$res['kodetraksi'],1,0,'L',1);	
					$pdf->Cell(15/100*$width,$height,$res['kodevhc'],1,0,'L',1);	
					$pdf->Cell(15/100*$width,$height,number_format($res['jamsetahun'],2),1,0,'R',1);	
					$pdf->Cell(15/100*$width,$height,number_format($res['rpsetahun'],2),1,0,'R',1); 
					$pdf->Cell(10/100*$width,$height,number_format($res['rpperjam'],2),1,0,'R',1);
					$pdf->Cell(13/100*$width,$height,number_format($res['teralokasi'],2),1,0,'R',1);	
					//$pdf->Cell(15/100*$width,$height,$res[''],1,0,'R',1);	            
					$pdf->Ln();	
					
					$totJam+=$res['jamsetahun'];
					$totRup+=$res['rpsetahun'];
					$totKmThn+=$res['rpperjam'];
					$totAlokasiJam+=$res['teralokasi'];
					
				}
				$pdf->SetFont('Arial','B',12);
				$pdf->SetFillColor(220,220,220);
				$pdf->Cell(30/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
				//$pdf->SetFontSize(9);
				
				$pdf->SetFont('Arial','',10);
				$pdf->SetFontSize(10);
				$pdf->Cell(15/100*$width,$height,number_format($totJam,2),1,0,'R',1);	
				$pdf->Cell(15/100*$width,$height,number_format($totRup,2),1,0,'R',1);	
				$pdf->Cell(10/100*$width,$height,number_format($totKmThn,2),1,0,'R',1);	
				$pdf->Cell(13/100*$width,$height,number_format($totAlokasiJam,2),1,0,'R',1);	
					
			$pdf->Output();
	##### Tutup PDF #####

	break;
			
			
			
			
			
		case'getAlokasi':
			
            $tab="<fieldset><legend>".$_SESSION['lang']['alokasi']." ".$kdVhc." ".$_SESSION['lang']['budgetyear'].": ".$thnBudget."</legend>";
            $tab.="<img title=\"MS.Excel\" class=\"resicon\" src=\"images/excel.jpg\" onclick=\"dataKeExcelAlokasi(event,'".$kdTraksi."','".$kdVhc."','".$thnBudget."')\">
				   <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"dataKePdfAlokasi(event,'".$kdTraksi."','".$kdVhc."','".$thnBudget."');\"> ";				
			$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td>No</td>";
            $tab.="<td>".$_SESSION['lang']['kodeorg']."</td>";
            $tab.="<td>".$_SESSION['lang']['jam']."</td>";
            $tab.="<td>".$_SESSION['lang']['rp']."</td></tr></thead><tbody>";
            $sDetail="select jumlah,kodeorg,rupiah from ".$dbname.".bgt_budget where tipebudget<>'TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
            $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
            while($rDetail=mysql_fetch_assoc($qDetail))
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$rDetail['kodeorg']."</td>";
                $tab.="<td align=right>".number_format($rDetail['jumlah'],2)."</td>";
                $tab.="<td align=right>".number_format($rDetail['rupiah'],2)."</td>";
                $tab.="</tr>";
                $totRupiahDet+=$rDetail['rupiah'];
                $totJamDet+=$rDetail['jumlah'];
            }
            $tab.="<tr class=rowcontent>";
			$tab.="<td align=center colspan=2>Total</td>";
            $tab.="<td align=right>".number_format($totJamDet,2)."</td>";
            $tab.="<td  align=right>".number_format($totRupiahDet,2)."</td>";
            $tab.="</tbody></table></fieldset";
            echo $tab;
            break;
			
			
			/*TRAKSI WILAYAH SUMATERA SELATAN			
Alokasi 10KVAGNT01 Tahun Budget: 2011	*/		

			
			
			
		
			
			
			
			
            case'excelAlokasi':
            
             $tab.="<table>
             <tr><td colspan=4 align=left>".$optNm[$kdTraksi]."</td></tr>   
             <tr><td colspan=4>".$_SESSION['lang']['alokasi']." ".$kdVhc." ".$_SESSION['lang']['budgetyear'].": ".$thnBudget."</td></tr>   
             </table>";
            $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td bgcolor=#DEDEDE align=center>No</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jam']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['rp']."</td></tr></thead><tbody>";
            $sDetail="select jumlah,kodeorg,rupiah from ".$dbname.".bgt_budget where tipebudget<>'TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
            $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
            while($rDetail=mysql_fetch_assoc($qDetail))
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$rDetail['kodeorg']."</td>";
                $tab.="<td align=right>".number_format($rDetail['jumlah'],2)."</td>";
                $tab.="<td align=right>".number_format($rDetail['rupiah'],2)."</td>";
                $tab.="</tr>";
                $totRupiahDet+=$rDetail['rupiah'];
                $totJamDet+=$rDetail['jumlah'];
            }
            $tab.="<tr class=rowcontent bgcolor=#CCCCCC>";
			$tab.="<td align=center colspan=2>Total</td>";
            $tab.="<td align=right>".number_format($totJamDet,2)."</td>";
            $tab.="<td  align=right>".number_format($totRupiahDet,2)."</td>";
            $tab.="</tbody></table>";
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="detailAlokasi";
            if(strlen($tab)>0)
            {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                    }
                }	
               closedir($handle);
            }
             $handle=fopen("tempExcel/".$nop_.".xls",'w');
             if(!fwrite($handle,$tab))
             {
              echo "<script language=javascript1.2>
                    parent.window.alert('Can't convert to excel format');
                    </script>";
               exit;
             }
             else
             {
              echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls';
                    </script>";
             }
            closedir($handle);
            }
            break;
            
			
			
			
case'pdfAlokasi':

//create Header

		class PDF extends FPDF
        {
            function Header() {
                
				global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
	
				global $kodeTraksi;
				global $kdTraksi;
				global $kdVhc;
				global $kdkend;
				global $thnBudget;
				global $thnbdget;
       
				

				
                //alamat PT

                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
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
                
				//untuk sub judul
                $this->SetFont('Arial','B',10);
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,"Detail Laporan Rp Jam/Kendaraan",'',0,'L');
				$this->Ln();
				$this->Ln();
				
				//judul tengah
				$this->Cell($width,$height,strtoupper("Detail Laporan Rp Jam/Kendaraan "."$kdVhc"),'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper("Tahun "."$thnBudget"),'',0,'C');
				$this->Ln();
				$this->Ln();
				
				//isi atas tabel
              	$this->SetFont('Arial','B',8);
                $this->SetFillColor(220,220,220);
				$this->Cell(5/100*$width,$height,"No",1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['jam'],1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['rp'],1,1,'C',1);	
            }
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
		
		//untuk kertas L=len p=potraid
        $pdf=new PDF('P','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 20;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);


		//isi tabel dan tabelnya
		$no=0;
		$sql="select jumlah,kodeorg,rupiah from ".$dbname.".bgt_budget where tipebudget!='TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
		// != identik dengan <>
		//exit ("Error:$sql");
		//$sql="select * from ".$dbname.".bgt_biaya_ws_per_jam a, ".$dbname.".bgt_budget b where a.tahunbudget = b.tahunbudget and kodews='".$kdWs."' and a.tahunbudget='".$thnbudget."' ";
		$qDet=mysql_query($sql) or die(mysql_error());
		while($res=mysql_fetch_assoc($qDet))
		{
			$no+=1;
			$pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(15/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
			$pdf->Cell(15/100*$width,$height,$res['jumlah'],1,0,'R',1);	
			$pdf->Cell(15/100*$width,$height,number_format($res['rupiah'],2),1,0,'R',1);	 						                   
			$pdf->Ln();	
			
			$totDetailPdfJam+=$res['jumlah'];
			$totDetailPdfRp+=$res['rupiah'];
		}
		
		$pdf->Cell(20/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
		$pdf->Cell(15/100*$width,$height,number_format($totDetailPdfJam,2),1,0,'R',1);
		$pdf->Cell(15/100*$width,$height,number_format($totDetailPdfRp,2),1,0,'R',1);
	$pdf->Output();
	
	break;			
			
			

			
	case'getBiaya':
            $tab="<fieldset><legend>".$_SESSION['lang']['biayaRinci']." ".$kdVhc." ".$_SESSION['lang']['budgetyear'].": ".$thnBudget."</legend>";
            $tab.="<img title=\"MS.Excel\" class=\"resicon\" src=\"images/excel.jpg\" onclick=\"dataKeExcel(event,'".$kdTraksi."','".$kdVhc."','".$thnBudget."')\">
			 	   <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"dataKePdfBiaya(event,'".$kdTraksi."','".$kdVhc."','".$thnBudget."');\"> ";
			
			$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>";
			$tab.="<tr class=rowheader>";
            $tab.="<td>No</td>";
            $tab.="<td>".$_SESSION['lang']['kodeorg']."</td>";
            $tab.="<td>".$_SESSION['lang']['kodeanggaran']."</td>";
            $tab.="<td>".$_SESSION['lang']['kodebarang']."</td>";
            $tab.="<td>".$_SESSION['lang']['namabarang']."</td>";
            $tab.="<td>".$_SESSION['lang']['volume']."</td>";
            $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
            $tab.="<td>".$_SESSION['lang']['jumlah']."</td>";
            $tab.="<td>".$_SESSION['lang']['satuan']."</td>";
            $tab.="<td>".$_SESSION['lang']['rp']."</td></tr></thead><tbody>";
            $sDetail="select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ".$dbname.".bgt_budget where tipebudget='TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
            $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
            while($rDetail=mysql_fetch_assoc($qDetail))
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$rDetail['kodeorg']."</td>";
                $tab.="<td>".$rDetail['kodebudget']."</td>";
                $tab.="<td>".$rDetail['kodebarang']."</td>";
                $tab.="<td>".$optNmbrg[$rDetail['kodebarang']]."</td>";
                $tab.="<td align=right>".number_format($rDetail['volume'],2)."</td>";
                $tab.="<td>".$rDetail['satuanv']."</td>";
                $tab.="<td align=right>".number_format($rDetail['jumlah'],2)."</td>";
                $tab.="<td>".$rDetail['satuanj']."</td>";
                $tab.="<td align=right>".number_format($rDetail['rupiah'],2)."</td>";
                $tab.="</tr>";
                $totVol+=$rDetail['volume'];
                $totJum+=$rDetail['jumlah'];
                $totRp+=$rDetail['rupiah'];
            }
            $tab.="<tr class=rowcontent>";
			$tab.="<td align=center colspan=5>Total</td>";
            $tab.="<td  align=right>".number_format($totVol,2)."</td>";
            $tab.="<td  align=right>&nbsp;</td>";
            $tab.="<td  align=right>".number_format($totJum,2)."</td>";
            $tab.="<td  align=right>&nbsp;</td>";
            $tab.="<td  align=right>".number_format($totRp,2)."</td>";
            $tab.="</tbody></table></fieldset";
            echo $tab;
            break;
			
			
			
	 case'excelBiaya':
                $tab.="<table>
             <tr><td colspan=4 align=left>".$optNm[$kdTraksi]."</td></tr>   
             <tr><td colspan=4>".$_SESSION['lang']['biayaRinci']." ".$kdVhc." ".$_SESSION['lang']['budgetyear'].": ".$thnBudget."</td></tr>   
             </table>";
            $tab.="<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td bgcolor=#DEDEDE align=center>No</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeanggaran']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['volume']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>";
            $tab.="<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['rp']."</td></tr></thead><tbody>";
            $sDetail="select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ".$dbname.".bgt_budget where tipebudget='TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
            $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
            while($rDetail=mysql_fetch_assoc($qDetail))
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$rDetail['kodeorg']."</td>";
                $tab.="<td>".$rDetail['kodebudget']."</td>";
                $tab.="<td>".$rDetail['kodebarang']."</td>";
                $tab.="<td>".$optNmbrg[$rDetail['kodebarang']]."</td>";
                $tab.="<td align=right>".number_format($rDetail['volume'],2)."</td>";
                $tab.="<td>".$rDetail['satuanv']."</td>";
                $tab.="<td align=right>".number_format($rDetail['jumlah'],2)."</td>";
                $tab.="<td>".$rDetail['satuanj']."</td>";
                $tab.="<td align=right>".number_format($rDetail['rupiah'],2)."</td>";
                $tab.="</tr>";
                $totVol+=$rDetail['volume'];
                $totJum+=$rDetail['jumlah'];
                $totRp+=$rDetail['rupiah'];
            }
            $tab.="<tr class=rowcontent bgcolor=#CCCCCC>";
			$tab.="<td align=center colspan=5>Total</td>";
            $tab.="<td  align=right>".number_format($totVol,2)."</td>";
            $tab.="<td  align=right>&nbsp;</td>";
            $tab.="<td  align=right>".number_format($totJum,2)."</td>";
            $tab.="<td  align=right>&nbsp;</td>";
            $tab.="<td  align=right>".number_format($totRp,2)."</td>";
            $tab.="</tbody></table>";
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="detailRincianBiaya";
            if(strlen($tab)>0)
            {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                    }
                }	
               closedir($handle);
            }
             $handle=fopen("tempExcel/".$nop_.".xls",'w');
             if(!fwrite($handle,$tab))
             {
              echo "<script language=javascript1.2>
                    parent.window.alert('Can't convert to excel format');
                    </script>";
               exit;
             }
             else
             {
              echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls';
                    </script>";
             }
            closedir($handle);
            }
            break;
			
			
			
			
			
case'pdfBiaya':

//create Header

		class PDF extends FPDF
        {
            function Header() {
                
				global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
	
				global $kodeTraksi;
				global $kdTraksi;
				global $kdVhc;
				global $kdkend;
				global $thnBudget;
				global $thnbdget;
       
				

				
                //alamat PT

                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
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
                
				//untuk sub judul
                $this->SetFont('Arial','B',10);
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,"Biaya Rinci Kendaraan",'',0,'L');
				$this->Ln();
				$this->Ln();
				
				//judul tengah
				$this->Cell($width,$height,strtoupper("Biaya Rinci Kendaraan "."$kdVhc"),'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper("Tahun "."$thnBudget"),'',0,'C');
				$this->Ln();
				$this->Ln();
				
				//isi atas tabel
              	$this->SetFont('Arial','B',8);
                $this->SetFillColor(220,220,220);
				$this->Cell(5/100*$width,$height,"No",1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['kodeanggaran'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['volume'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['rp'],1,1,'C',1);	
				
            }
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
		
		//untuk kertas L=len p=potraid
        $pdf=new PDF('L','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 20;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);


		//isi tabel dan tabelnya
		$no=0;
		$sql="select kodeorg,kodebudget,kodebarang,volume,satuanv,jumlah,satuanj ,rupiah from ".$dbname.".bgt_budget where tipebudget='TRK' and kodevhc='".$kdVhc."' and tahunbudget='".$thnBudget."'";
		// != identik dengan <>
		//exit ("Error:$sql");
		//$sql="select * from ".$dbname.".bgt_biaya_ws_per_jam a, ".$dbname.".bgt_budget b where a.tahunbudget = b.tahunbudget and kodews='".$kdWs."' and a.tahunbudget='".$thnbudget."' ";
		$qDet=mysql_query($sql) or die(mysql_error());
		while($res=mysql_fetch_assoc($qDet))
		{
			$no+=1;
			$pdf->Cell(5/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(15/100*$width,$height,$res['kodeorg'],1,0,'L',1);	
			$pdf->Cell(15/100*$width,$height,$res['kodebudget'],1,0,'L',1);	
			$pdf->Cell(10/100*$width,$height,$res['kodebarang'],1,0,'L',1);	
			
			$pdf->Cell(10/100*$width,$height,number_format($res['volume'],2),1,0,'R',1);	
			$pdf->Cell(8/100*$width,$height,$res['satuanv'],1,0,'L',1);	
			$pdf->Cell(10/100*$width,$height,number_format($res['jumlah'],2),1,0,'R',1);	
			$pdf->Cell(8/100*$width,$height,$res['satuanj'],1,0,'L',1);	
			$pdf->Cell(10/100*$width,$height,number_format($res['rupiah'],2),1,0,'R',1);	
			

			 						                   
			$pdf->Ln();	
			
			//$totDetailPdfJam+=$res['jumlah'];
			//$totDetailPdfRp+=$res['rupiah'];
			
			$tota+=$res['volume'];
			$totb+=$res['jumlah'];
			$totc+=$res['rupiah'];
			
		}
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(35/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10/100*$width,$height,"",1,0,'R',1);
		$pdf->Cell(10/100*$width,$height,number_format($tota,2),1,0,'R',1);
		$pdf->Cell(8/100*$width,$height,"",1,0,'R',1);
		$pdf->Cell(10/100*$width,$height,number_format($totb,2),1,0,'R',1);
		$pdf->Cell(8/100*$width,$height,"",1,0,'R',1);
		$pdf->Cell(10/100*$width,$height,number_format($totc,2),1,0,'R',1);
	$pdf->Output();
	
	break;						
			
			
			
			
			
			
			// tampilkan exel //
		
			
			
			
           /* case'pdf':
            class PDF extends FPDF {
            function Header() {
            global $dataKary;
            global $dataKaryIstri;
            global $dataTanggugan;
            global $dtKode2;
            global $kodeOrg;
            global $total;
            global $dataTipeKary;
            global $totalTipe;
            global $dbname;
            
        $sTipe="select lokasitugas,tipe from ".$dbname.".bgt_datakaryawan_vw where lokasitugas='".$kodeOrg."' group by tipe";
        $qTipe=mysql_query($sTipe) or die(mysql_error($conn));
        while($rTipe=mysql_fetch_assoc($qTipe))
        {
            $a+=1;
            $dataTipeKary[$a]=$rTipe['tipe'];
        }
    $totalTipe=count($dataTipeKary);
         
                $this->SetFont('Arial','B',12);
                $this->Cell(275,5,strtoupper($_SESSION['lang']['lapPersonel']),0,1,'C');
                	
                //$this->Cell(275,5,strtoupper($_SESSION['lang']['rprodksiPabrik']),0,1,'C');
                $this->Cell(275,5,$_SESSION['lang']['unit'].' : '.$kodeOrg,0,1,'C');
                $this->SetFont('Arial','',8);
                $this->Cell(230,5,$_SESSION['lang']['tanggal'],0,0,'R');
                $this->Cell(2,5,':','',0,'L');
                $this->Cell(35,5,date('d-m-Y H:i'),0,1,'L');
                $this->Cell(230,5,$_SESSION['lang']['page'],'',0,'R');
                $this->Cell(2,5,':','',0,'L');
                $this->Cell(35,5,$this->PageNo(),'',1,'L');
                $this->Cell(230,3,'User','',0,'R');
                $this->Cell(2,3,':','',0,'L');
                $this->Cell(35,3,$_SESSION['standard']['username'],'',1,'L');
                $this->Ln();
                

                $this->SetFont('Arial','B',7);
                $this->Cell(7,10,'No.',1,0,'C');
                //$this->Cell(25,10,$_SESSION['lang']['kodeorganisasi'],1,0,'C');
                $this->Cell(35,10,$_SESSION['lang']['status'],1,1,'C');
                $total2=count($dtKode2);
                $tinggiMin=($total2*10)+10;
               
                foreach($dtKode2 as $brsKd =>$lstKd)
                {
                    $no+=1;
                    $this->Cell(7,10,$no,1,0,'C');
                    $this->Cell(35,10,$lstKd,1,1,'C');
                }
               // $this->Cell(100,5,$_SESSION['lang']['kernel'],1,1,'C');
                $yTinggi=$this->GetY();
                
                $this->setY($yTinggi-$tinggiMin);
                $this->setX(52);
                for($c=1;$c<=$totalTipe;$c++)
                {
                   if($c!=$totalTipe)
                   {$this->Cell(45,5,$dataTipeKary[$c],1,0,'C');}
                   else
                   {$this->Cell(45,5,$dataTipeKary[$c],1,1,'C');}
                }
                $this->setY($yTinggi-$tinggiMin+5);
                $this->setX(52);
                $this->SetFont('Arial','',6);
                $this->Cell(18,5,$_SESSION['lang']['karyawan'],1,0,'C');
                $this->Cell(17,5,$_SESSION['lang']['jumlahanak'],1,0,'C');
                $this->Cell(10,5,$_SESSION['lang']['istri'],1,0,'C');
                
//                $this->Cell(20,5,'HI',1,0,'C');
//                $this->Cell(20,5,'S/D',1,0,'C');
//                $this->Cell(20,5,$_SESSION['lang']['cpo'].'(Kg) HI',1,0,'C');
//                $this->Cell(20,5,$_SESSION['lang']['cpo'].'(Kg) S/D',1,0,'C');
//                $this->Cell(15,5,$_SESSION['lang']['oer'].'(%)',1,0,'C');
//                $this->Cell(15,5,'(FFa)(%)',1,0,'C');
//                $this->Cell(15,5,$_SESSION['lang']['kotoran'].'(%)',1,0,'C');	
//                $this->Cell(15,5,$_SESSION['lang']['kadarair'].'(%)',1,0,'C');		
//
//                $this->Cell(20,5,$_SESSION['lang']['kernel'].'(Kg) HI',1,0,'C');
//                $this->Cell(20,5,$_SESSION['lang']['kernel'].'(Kg) S/D',1,0,'C');
//                $this->Cell(15,5,$_SESSION['lang']['oer'].'(%)',1,0,'C');
//                $this->Cell(15,5,'(FFa)(%)',1,0,'C');
//                $this->Cell(15,5,$_SESSION['lang']['kotoran'].'(%)',1,0,'C');	
//                $this->Cell(15,5,$_SESSION['lang']['kadarair'].'(%)',1,0,'C');
//                $y=$this->getY()-5;
//                $x=$this->GetX();
//                $this->SetY($y);
//                $this->SetX($x);
//                $this->Cell(20,10,$_SESSION['lang']['jampengolahan'],1,0,'C');
//                $this->Cell(20,10,$_SESSION['lang']['jamstagnasi'],1,0,'C');
//                $this->Cell(15,10,$_SESSION['lang']['sisa'].'(Kg) ',1,1,'C');	

            }
            }
            //================================

            $pdf=new PDF('L','mm','LEGAL');
            $pdf->AddPage();
            $pdf->SetFont('Arial','',7);

//
//            //bulanan
//            $str="select * from ".$dbname.".pabrik_produksi where tanggal like '".$periode."%'
//              and kodeorg='".$pabrik."'
//                  order by tanggal asc";
//            $res2=mysql_query($str);
//            $res=mysql_query($str);
//            while($datArr=  mysql_fetch_assoc($res2))
//            {
//            $tbs[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['tbsdiolah'];
//            $jmOer[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['oer'];
//            $jmOerPk[$datArr['kodeorg']][$datArr['tanggal']]=$datArr['oerpk'];
//            }
//            $no=0;
//            $tgl=1;
//            while($bar=mysql_fetch_object($res))
//            {
//            if(strlen($tgl)==1)
//            {
//               $agl="0".$tgl;
//            }
//
//            $tbsSd=$tbs[$bar->kodeorg][$tglServ.$agl+1];
//            $tbsSd2=$tbs[$bar->kodeorg][$bar->tanggal];
//            $tbsTot=$tbsSd2+$tbsSd;
//            $des+=$tbsTot;
//            //get cpo 
//            $oerSd=$jmOer[$bar->kodeorg][$tglServ.$agl+1];
//            $oerSd2=$jmOer[$bar->kodeorg][$bar->tanggal];
//            $oerTot=$oerSd2+$oerSd;
//            $oerTotal+=$oerTot;
//
//            //get pk
//            $oerpkSd=$jmOerPk[$bar->kodeorg][$tglServ.$agl+1];
//            $oerpkSd2=$jmOerPk[$bar->kodeorg][$bar->tanggal];
//            $oerpkTot=$oerpkSd+$oerpkSd2;
//            $oerpkTotal+=$oerpkTot;
//
//            $sPengolahan="select sum(jamdinasbruto) as jampengolahan, sum(jamstagnasi) as jamstagnasi from ".$dbname.".pabrik_pengolahan 
//               where kodeorg='".$bar->kodeorg."' and tanggal='".$bar->tanggal."'";
//            //echo $sPengolahan."__\n";
//            $qPengolahan=mysql_query($sPengolahan) or die(mysql_error($conn));
//            $rPengolahan=mysql_fetch_assoc($qPengolahan);
//            $no+=1;	
//                $pdf->Cell(5,5,$no,1,0,'C');
//                //$pdf->Cell(25,5,$bar->kodeorg,1,0,'C');
//                $pdf->Cell(20,5,tanggalnormal($bar->tanggal),1,0,'C');
//                $pdf->Cell(25,5,number_format($bar->tbsmasuk+$bar->sisatbskemarin,0,'.',','),1,0,'R');	
//                $pdf->Cell(20,5,number_format($bar->tbsdiolah,0,'.',',.'),1,0,'R');	
//                $pdf->Cell(20,5,number_format($des,0,'.',',.'),1,0,'R');
//                //	
//                $pdf->Cell(20,5,number_format($bar->oer,0,'.',','),1,0,'R');
//                $pdf->Cell(20,5,number_format($oerTotal,0,'.',','),1,0,'R');
//                $pdf->Cell(15,5,(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
//                $pdf->Cell(15,5,$bar->ffa,1,0,'R');
//                $pdf->Cell(15,5,$bar->kadarkotoran,1,0,'R');	
//                $pdf->Cell(15,5,$bar->kadarair,1,0,'R');		
//                $pdf->Cell(20,5,number_format($bar->oerpk,0,'.',','),1,0,'R');
//                $pdf->Cell(20,5,number_format($oerpkTotal,0,'.',','),1,0,'R');
//                $pdf->Cell(15,5,(@number_format($bar->oerpk/$bar->tbsdiolah*100,2,'.',',')),1,0,'R');
//                $pdf->Cell(15,5,$bar->ffapk,1,0,'R');
//                $pdf->Cell(15,5,$bar->kadarkotoranpk,1,0,'R');	
//                $pdf->Cell(15,5,$bar->kadarairpk,1,0,'R');	
//                $pdf->Cell(20,5,number_format($rPengolahan['jampengolahan'],0,'.','.'),1,0,'R');	
//                $pdf->Cell(20,5,  number_format($rPengolahan['jamstagnasi'],0,'.','.'),1,0,'R');
//                $pdf->Cell(15,5,number_format($bar->sisahariini,0,'.',','),1,1,'R');
//            }
            $pdf->Output();	
                
                
            break;*/
                
            default:
            break;
        }
	
?>

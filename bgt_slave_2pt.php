<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
//$_POST['kdUnit']==''?$kodeOrg=$_GET['kdUnit']:$kodeOrg=$_POST['kdUnit'];
$_POST['thnBudget']==''?$thnBudget=$_GET['thnBudget']:$thnBudget=$_POST['thnBudget'];
$_POST['kdWS']==''?$kdWS=$_GET['kdWS']:$kdWS=$_POST['kdWS'];
//$_POST['kdVhc']==''?$kdVhc=$_GET['kdVhc']:$kdVhc=$_POST['kdVhc'];
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmbrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optAk=makeOption($dbname, 'keu_5akun', 'noakun,namaakun','level=5');


//ambil pt
$str="select induk,kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4";
 $res=mysql_query($str);
 while($bar=  mysql_fetch_object($res))
 {
     $kodept[$bar->kodeorganisasi]=$bar->induk;
 }

$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan where karyawanid=".$_SESSION['standard']['userid']. "";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$namakar[$bar->karyawanid]=$bar->namakaryawan;
}

//kamus nama


$where="  tahunbudget='".$thnBudget."'";
if($_GET['proses']=='excel')
{
     $bg=" bgcolor=#DEDEDE";
    $brdr=1;
     $tab.="<table>   
             <tr><td colspan=4>".$_SESSION['lang']['budget'].$_SESSION['lang']['detail']." ".$_SESSION['lang']['budgetyear'].": ".$thnBudget."</td></tr>   
             </table>";
}
else
{
    $bg="";
    $brdr=0;
}
 $sDetail="select left(a.kodeorg,4) as unit,a.noakun,b.namaakun,sum(rupiah) as total, rp01,rp02,rp03,rp04,rp05,rp06,rp07,rp08,rp09,rp10,rp11,rp12 
               from ".$dbname.".bgt_budget_detail a left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
               where ".$where." and a.noakun is not null and a.noakun!='' 
               group by left(a.kodeorg,4),a.noakun";
 $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
 $brscek=mysql_num_rows($qDetail);
 if($brscek!=0)
 {
            $tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable><thead>";
            $tab.="<tr class=rowheader>";
            $tab.="<td rowspan=2 align=center ".$bg.">No</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['budgetyear']."</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['kodeorg']."</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['pt']."</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['noakun']."</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['namaakun']."</td>";
            $tab.="<td rowspan=2 align=center ".$bg.">".$_SESSION['lang']['jumlahsetahun']."</td>";

            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jan']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['peb']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['mar']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['apr']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['mei']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jun']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['jul']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['agt']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['sep']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['okt']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['nov']."</td>";
            $tab.="<td align=center ".$bg.">".$_SESSION['lang']['dec']."</td></tr><tr>";
       for($x=1;$x<13;$x++){
            $tab.="<td align=center ".$bg.">Rp.</td>";

       }


$tab.="</tr></thead><tbody>";
            
           
            while($rDetail=mysql_fetch_assoc($qDetail))
            {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$thnBudget."</td>";
                $tab.="<td>".$rDetail['unit']."</td>";
                $tab.="<td>". $kodept[$rDetail['unit']]."</td>";
                $tab.="<td>".$rDetail['noakun']."</td>";
                $tab.="<td>".$rDetail['namaakun']."</td>";
                $tab.="<td align=right>".number_format($rDetail['total'],2)."</td>";
//                $tot=array();
               for($x=1;$x<13;$x++){
                   if(strlen($x)==1){
                       $rprp='rp0'.$x;
                   }else{
                       $rprp='rp'.$x;
                   }

                    if($rDetail[$rprp]!=0)$tab.="<td align=right>".number_format($rDetail[$rprp],2)."</td>"; else $tab.="<td align=right></td>";
                    
                    $tot[$rprp]+=$rDetail[$rprp];
               }

                $tab.="</tr>";
                //$totVol+=$rDetail['volume'];
                //$totJum+=$rDetail['jumlah'];
                $totRp+=$rDetail['total'];
            }
            
            $tab.="</tbody><thead><tr class=rowheader>";
            $tab.="<td align=center align=right colspan=6 ".$bg.">".$_SESSION['lang']['total']."</td>";
            $tab.="<td align=right ".$bg.">".number_format($totRp,2)."</td>";
               for($x=1;$x<13;$x++){
                   if(strlen($x)==1){
                       $rprp='rp0'.$x;
                   }else{
                       $rprp='rp'.$x;
                   }

                    $tab.="<td align=right ".$bg.">".number_format($tot[$rprp],2)."</td>";
                    
               }
            $tab.="</tr>";
            $tab.="</thead></table>";
            
 }


	switch($proses)
        {
            case'preview':
            	echo $tab;
            break;
                        
            case'excel':
           
            $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
            $dte=date("YmdHis");
            $nop_="laporanBudgePT_".$thnBudget;
            $stream=$tab;
            if(strlen($stream)>0)
            {
                 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                 gzwrite($gztralala, $stream);
                 gzclose($gztralala);
                 echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls.gz';
                    </script>";
            } 
            break;
			
			
			
			
			
				
case'pdf':

		if($thnBudget=='')
		{
			echo "warning : a";
			exit();	
		}
		else if($kdWS=='')
		{
			echo "warning : b";
			exit();	
		}

			
	class PDF extends FPDF
	{
            function Header() 
			{
				
				global $thnBudget;
				global $kdWs;
				global $kdWS;
				global $totRp;
				global $conn;
				global $dbname;
				global $align;
				global $length;
				global $colArr;
				global $title;
				global $total;
				global $optKar;
				global $namakar;
				global $optNm;
				

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
						$this->Cell((20/100*$width)-5,$height,"Biaya Bengkel",'',0,'L');
						$this->Ln();
						$this->SetFont('Arial','',10);
						$this->Cell((100/100*$width)-5,$height,"Printed By : ".$namakar[$_SESSION['standard']['userid']],'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Tanggal By : ".date('d-m-Y'),'',0,'R');
						$this->Ln();
						$this->Cell((100/100*$width)-5,$height,"Time By : ".date('h:i:s'),'',0,'R');
						///
						$this->Ln();
						$this->Ln();
						//tutup sub judul
						
						//judul tengah
						$this->SetFont('Arial','B',12);
						//$this->Cell($width,$height,strtoupper("Biaya Kendaraan ".$optNm[$kodeOrg]),'',0,'C');
						$this->Cell($width,$height,strtoupper("Biaya ".$optNm[$kdWS]),'',0,'C');//
						$this->Ln();
						$this->Cell($width,$height,strtoupper("Tahun ".$thnBudget),'',0,'C');
						$this->Ln();
						$this->Ln();
						//tutup judul tengah
						
						//isi atas tabel
						$this->SetFont('Arial','B',10);
						$this->SetFillColor(220,220,220);
						$this->Cell(2/100*$width,$height,"No",1,0,'C',1);
						$this->Cell(8/100*$width,$height,$_SESSION['lang']['workshop'],1,0,'C',1);
						$this->Cell(10/100*$width,$height,$_SESSION['lang']['kodeanggaran'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['namaakun'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
						$this->Cell(10/100*$width,$height,$_SESSION['lang']['volume'],1,0,'C',1);
						$this->Cell(8/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
						$this->Cell(15/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
						$this->Cell(8/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
						$this->Cell(10/100*$width,$height,$_SESSION['lang']['rp'],1,1,'C',1);	
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
				$pdf->SetFont('Arial','',7);//ukuran tulisan
				//tutup tampilan setting
		
		
				//isi tabel dan tabelnya
				$no=0;
				$sql="select * from ".$dbname.".bgt_budget where tipebudget='WS' and ".$where." ";
				//echo $sql;
				$qDet=mysql_query($sql) or die(mysql_error());
				while($res=mysql_fetch_assoc($qDet))
				{
					$no+=1;
					$pdf->SetFontSize(10);
					$pdf->Cell(2/100*$width,$height,$no,1,0,'C',1);
					$pdf->Cell(8/100*$width,$height,$kdWS,1,0,'L',1);	
					$pdf->Cell(10/100*$width,$height,$res['kodebudget'],1,0,'L',1);	
					$pdf->Cell(15/100*$width,$height,$optAk[$res['noakun']],1,0,'L',1);	
					$pdf->Cell(15/100*$width,$height,$optNmbrg[$res['kodebarang']],1,0,'L',1);
					$pdf->Cell(10/100*$width,$height,number_format($res['volume'],2),1,0,'R',1);	//60
					$pdf->Cell(8/100*$width,$height,$res['satuanv'],1,0,'R',1);	
					$pdf->Cell(15/100*$width,$height,number_format($res['jumlah'],2),1,0,'R',1);	
					$pdf->Cell(8/100*$width,$height,$res['satuanj'],1,0,'R',1);	
					$pdf->Cell(10/100*$width,$height,number_format($res['rupiah'],2),1,0,'R',1);	                 
					$pdf->Ln();	
				}
				$pdf->SetFont('Arial','B',12);
				$pdf->SetFillColor(220,220,220);
				$pdf->Cell(91/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(10/100*$width,$height,number_format($totRp,2),1,1,'R',1);	
			$pdf->Output();
	##### Tutup PDF #####
	
	break;
	default;
	
	
}    
?>
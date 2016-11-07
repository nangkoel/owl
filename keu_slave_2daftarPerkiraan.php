<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];






//proses excel




$nmbarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$satuanbarang=makeOption($dbname,'log_5masterbarang','kodebarang,satuan');

//echo $daftargudang.____.$tgl1;
if($proses=='excel')
{
 $stream="<table class=sortable cellspacing=1 border=1>";
}
else
{ $stream="<table class=sortable cellspacing=1>";

}
 $stream.="<thead class=rowheader>
                 <tr  bgcolor=#CCCCCC class=rowheader>
				 	<td align=center>".$_SESSION['lang']['nomorperkiraan']."</td>
					<td align=center>".$_SESSION['lang']['namaperkiraan']."</td>
					<td align=center>".$_SESSION['lang']['tipe']."</td>
					<td align=center>".$_SESSION['lang']['level']."</td>
					<td align=center>".$_SESSION['lang']['matauang']."</td>
					<td align=center>".$_SESSION['lang']['tampilkan']."</td>
					<td align=center>".$_SESSION['lang']['detail']."</td>
  				</tr></thead>";
				
$sql="select * from ".$dbname.".keu_5akun";				
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
//echo $sql;
while($bar=mysql_fetch_assoc($qry))
{
	
		$no+=1;
		$stream.="<tr class=rowcontent>
		<td>".$bar['noakun']."</td>";
		 if($_SESSION['language']=='EN')
			$stream.="<td>".$bar['namaakun1']."</td>";
		 else
		 	$stream.="<td>".$bar['namaakun']."</td>";
		$stream.="
		<td>".$bar['tipeakun']."</td>
		<td>".$bar['level']."</td>
		<td>".$bar['matauang']."</td>
		<td>".$bar['pemilik']."</td>";
		
		 if ($bar['detail']==1)
			$stream.="<td>Y</td>";
		 else
		 	$stream.="<td></td>";	
       
		$stream.="</tr>";
}

				
				

$stream.="<tbody></table>";
switch($proses)
{
######PREVIEW
	case 'preview':
		echo $stream;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="Laporan_Riwayat_Potongan_Angsuran_Karyawan".$tglSkrg;
		if(strlen($stream)>0)
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
			if(!fwrite($handle,$stream))
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

###############	
#panggil PDFnya
###############
	
		case'pdf':
		
		$table = "keu_5akun";
		
		
		
		#====================== Prepare Data
		$query = selectQuery($dbname,$table);
		#print_r ($_GET);
		#exit;
		$result = fetchData($query);
		$header = array();
		foreach($result[0] as $key=>$row) {
			$header[] = $key;
		}


            #====================== Prepare Header PDF
			class masterpdf extends FPDF {
				function Header() {
					global $table;
					global $header;
			
					# Panjang, Lebar
					$width = $this->w - $this->lMargin - $this->rMargin;
							$height = 12;
					$this->SetFont('Arial','B',8);
							$this->Cell(20,$height,$_SESSION['org']['namaorganisasi'],'',1,'L');
					$this->SetFont('Arial','B',12);
			#		$this->Cell($width,$height,'Tabel : '.$table,'',1,'L');
							$this->Cell($width,$height,strtoupper($_SESSION['lang']['daftarperkiraan']),'',1,'C');
					$this->SetFont('Arial','B',8);
							$this->Cell(420,$height,' ','',0,'R');
							$this->Cell(38,$height,$_SESSION['lang']['tanggal'],'',0,'L');
							$this->Cell(5,$height,':','',0,'L');
							$this->Cell(40,$height,date('d-m-Y H:i'),'',1,'L');
							$this->Cell(420,$height,' ','',0,'R');
							$this->Cell(38,$height,$_SESSION['lang']['page'],'',0,'L');
							$this->Cell(8,$height,':','',0,'L');
							$this->Cell(15,$height,$this->PageNo(),'',1,'L');
			#        $this->Ln();
							$this->Cell(420,$height,' ','',0,'R');
							$this->Cell(38,$height,'User','',0,'L');
							$this->Cell(8,$height,':','',0,'L');
							$this->Cell(20,$height,$_SESSION['standard']['username'],'',1,'L');
					$this->Ln();
			
					# Generate Header
			#        foreach($header as $hName) {
			#            $this->Cell($width/count($header),$height,ucfirst($hName),'TBLR',0,'L');
			#        }
					$this->Cell(60,1.5*$height,$_SESSION['lang']['nomorperkiraan'],'TBLR',0,'C');
					$this->Cell(260,1.5*$height,$_SESSION['lang']['namaperkiraan'],'TBLR',0,'C');
					$this->Cell(38,1.5*$height,$_SESSION['lang']['tipe'],'TBLR',0,'C');
					$this->Cell(45,1.5*$height,$_SESSION['lang']['level'],'TBLR',0,'C');
					$this->Cell(47,1.5*$height,$_SESSION['lang']['matauang'],'TBLR',0,'C');
					$this->Cell(40,1.5*$height,$_SESSION['lang']['tampilkan'],'TBLR',0,'C');        
					$this->Cell(40,1.5*$height,$_SESSION['lang']['detail'],'TBLR',0,'C');
					$this->Ln();
					$this->Ln();
				}
			}
			
			#====================== Prepare PDF Setting
			$pdf = new masterpdf('P','pt','A4');
			$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
			$height = 12;
			$pdf->SetFont('Arial','',8);
			$pdf->AddPage();
			
			# Generate Data
			#print_r($result);
			#exit;
			#foreach($result as $row) {
			#    foreach($row as $data) {
			#        $pdf->Cell($width/count($header),$height,$data,'',0,'L');
			#    }
			#    $pdf->Ln();
			#}
			foreach($result as $data) {
					$pdf->Cell(60,$height,$data['noakun'],'',0,'L');
					if($_SESSION['language']=='EN'){
						$pdf->Cell(260,$height,$data['namaakun1'],'',0,'L');
					}else{
						$pdf->Cell(260,$height,$data['namaakun'],'',0,'L');
					}
					$pdf->Cell(40,$height,$data['tipeakun'],'',0,'C');
					$pdf->Cell(40,$height,$data['level'],'',0,'C');
					$pdf->Cell(60,$height,$data['matauang'],'',0,'C');
					$pdf->Cell(40,$height,$data['pemilik'],'',0,'C');        
					if ($data['detail']==1)
					{
					$pdf->Cell(40,$height,'Y','',0,'C');
					}
				$pdf->Ln();
			}

		
		# Print Out
		$pdf->Output();
					
			break;	
	
}


?>













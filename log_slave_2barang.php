<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];

$kel=$_POST['kel'];
if($proses=='excel')
{
	$kel=$_GET['kel'];
}


$st=array('0'=>'Aktif','1'=>'Tidak Aktif');
$nmkl=makeOption($dbname,'log_5klbarang','kode,kelompok');
//proses excel

if($kel!='')
	$where="where kelompokbarang='".$kel."'";
else
	$where="";


$nmbarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$satuanbarang=makeOption($dbname,'log_5masterbarang','kodebarang,satuan');


if($proses=='excel')
{
 $stream="<table class=sortable cellspacing=1 border=1>";
}
else
{ $stream="<table class=sortable cellspacing=1>";

}
 $stream.="<thead class=rowheader>
                 <tr   class=rowheader>
				 	<td bgcolor=#CCCCCC align=center>No</td>
					<td bgcolor=#CCCCCC align=center>Kelompok</td>
					<td bgcolor=#CCCCCC align=center>Nama Kelompok</td>
					<td bgcolor=#CCCCCC align=center>Kartu Bin</td>
					<td bgcolor=#CCCCCC align=center>Kode Barang</td>
					<td bgcolor=#CCCCCC align=center>Nama Barang</td>
					<td bgcolor=#CCCCCC align=center>Satuan</td>
					<td bgcolor=#CCCCCC align=center>Status</td>
  				</tr></thead>";
				
$sql="select a.kelompokbarang,a.kodebarang,a.namabarang,a.satuan,a.inactive,b.nokartu from ".$dbname.
        ".log_5masterbarang a left join ".$dbname.
        ".log_5kartubin b on a.kodebarang=b.kodebarang ".$where." order by a.kelompokbarang";

$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
//echo $sql;
while($bar=mysql_fetch_assoc($qry))
{
		$no+=1;
		$stream.="<tr class=rowcontent>
		<td>".$no."</td>
		<td>'".$bar['kelompokbarang']."</td>
		<td>".$nmkl[$bar['kelompokbarang']]."</td>
		<td>".$bar['nokartu']."</td>
		<td>'".$bar['kodebarang']."</td>
		<td>".$bar['namabarang']."</td>
		<td>".$bar['satuan']."</td>
		<td>".$st[$bar['inactive']]."</td>

		</tr>";/*		<td>'".$bar['kodebarang']."</td>
		<td>".$bar['namabarang']."</td>
		<td>".$bar['satuan']."</td>*/
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
		$nop_="laporan_daftar_barang_".$tglSkrg;
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

            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
							global $kdorg;
							global $kdAfd;
							global $tgl1;
							global $tgl2;
							global $where;
							global $nmOrg;
							global $lok;
							global $notrans;
							global $bulan;
							global $ang;
							global $kar;
							global $namaang;
							global $namakar;
							

                            //$cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                            $path='images/logo.jpg';
                            //$this->Image($path,$this->lMargin,$this->tMargin,50);	
							$this->Image($path,30,15,55);
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(90); 
							  
                            $this->Cell($width-80,12,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                            $this->SetX(90); 		
			                $this->SetFont('Arial','',9);
							$height = 12;
                            $this->Cell($width-80,$height,$orgData[0]['alamat'],0,1,'L');	
                            $this->SetX(90); 			
                            $this->Cell($width-80,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Ln();
                            $this->Line($this->lMargin,$this->tMargin+($height*4),
                            $this->lMargin+$width,$this->tMargin+($height*4));

                            $this->SetFont('Arial','B',12);
                                            $this->Ln();
                            $height = 15;
                                            $this->Cell($width,$height,'Laporan Stock Opname','',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','',10);
                                            
                            $this->SetFont('Arial','B',7);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(3/100*$width,15,substr($_SESSION['lang']['nomor'],0,2),1,0,'C',1);		
                                            $this->Cell(15/100*$width,15,'Kode Barang',1,0,'C',1);
											$this->Cell(35/100*$width,15,'Nama Barang',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Saldo Fisik OWL',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Saldo Fisik Gudang',1,0,'C',1);
											$this->Cell(15/100*$width,15,'Selisih',1,1,'C',1);
						
						                       }

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',8);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','A4');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 15;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',7);
		

		
		$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());//tinggal tarik $res karna sudah di declarasi di atas
		$no=0;
		//$ttl=0;
		while($bar=mysql_fetch_assoc($qry))
		{	

			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$bar['kodebarang'],1,0,'R',1);		
			$pdf->Cell(35/100*$width,$height,$nmbarang[$bar['kodebarang']],1,0,'L',1);		
			$pdf->Cell(15/100*$width,$height,number_format($bar['saldoqty']),1,0,'R',1);
			$pdf->Cell(15/100*$width,$height,'',1,0,'R',1);		
			$pdf->Cell(15/100*$width,$height,'',1,1,'R',1);	
	
		}
		$pdf->Output();
            
	break;	
	
}


?>













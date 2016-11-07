<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdorg=$_POST['kdorg'];
$per=$_POST['per'];
$tipe=$_POST['tipe'];

if(($proses=='excel')or($proses=='pdf'))
{
	$kdorg=$_GET['kdorg'];
	$per=$_GET['per'];
	$tipe=$_GET['tipe'];
}


if($proses=='excel')
{
	$bgcolor="bgcolor=#CCCCCC";
	$border="border='1'";
}
else
{ 
}

$tglOption=$per.'-01';
$nmAng=makeOption($dbname,'sdm_ho_component','id,name');

//echo $tglOption;
###########prepare selisih tanggal


	
			
		//	echo $str;






 
 
$stream="<table cellspacing='1'  class='sortable' ".$bgcolor." ".$border."><thead>"; 
 $stream.="
                <tr class=rowheader>
                            <td align=center>No.</td>
                                <td align=center>".$_SESSION['lang']['karyawanid']."</td>
                            	<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                                <td align=center>".$_SESSION['lang']['jennisangsuran']."</td>
                                <td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['nilaihutang']."<br>(Rp.)</td>
                                <td align=center>".$_SESSION['lang']['bulanawal']."</td>
                                <td align=center>".$_SESSION['lang']['sampai']."</td>
                                <td align=center>".$_SESSION['lang']['jumlah']."<br>(".$_SESSION['lang']['bulan'].")</td>
                                <td align=center>".$_SESSION['lang']['potongan']."/".$_SESSION['lang']['bulan'].".<br>(Rp.)</td>		
								<td align=center>Terbayar<br>(Rp.)</td>		
								<td align=center>SisaTerbayar<br>(Rp.)</td>		
                                <td align=center>".$_SESSION['lang']['status']."</td>
                          </tr> 
                          </thead>";
				



if($tipe=='lunas')
{
	$str="	select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			where  u.lokasitugas='".$kdorg."'  and a.end< '".$per."' 
			order by namakaryawan";	 
}
else if($tipe=='blmlunas')
{
	$str="	select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			where  u.lokasitugas='".$kdorg."'    and a.end > '".$per."'
			order by namakaryawan";
}
else if($tipe=='active')
{
	$str="	select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			where  u.lokasitugas='".$kdorg."'    and a.active=1
			order by namakaryawan";
}
else if($tipe=='notactive')
{
	$str="	select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			where  u.lokasitugas='".$kdorg."'   and a.active=0
			order by namakaryawan";
}		
$qry = mysql_query($str) or die ("SQL ERR : ".mysql_error($conn));
while($bar=mysql_fetch_assoc($qry))
{	
	
	$a="select sum(jumlah) as jumlah from ".$dbname.".sdm_gaji where karyawanid='".$bar['karyawanid']."' and idkomponen='".$bar['jenis']."' 
		and periodegaji between '".$bar['start']."' and '".$per."' group by karyawanid";
	$b=mysql_query($a) or die (mysql_error($conn));
	$c=mysql_fetch_assoc($b);
	//echo $bar['karyawanid']._.$bar['jenis'].______;
	$no+=1;
	
	
	
	$stream.="<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$bar['nik']."</td>
				<td>".$bar['namakaryawan']."</td>
				<td>".$nmAng[$bar['jenis']]."</td>
				<td align=right>".number_format($bar['total'],2,'.',',')."</td>
				<td align=center>".$bar['start']."</td>
				<td align=center>".$bar['end']."</td>
				<td align=right>".$bar['jlhbln']."</td>
				<td align=right>".number_format($bar['bulanan'],2,'.',',')."</td>	
				<td align=right>".$c['jumlah']."</td>		
				<td align=right>".number_format($bar['total']-$c['jumlah'],2,'.',',')."</td>					
				<td align=center>".($bar['active']==1?"Active":"Not Active")."</td>
						  </tr>"; 			
				  $ttl+=$bar['bulanan'];	
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













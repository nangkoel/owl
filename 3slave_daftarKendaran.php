<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdKbn=$_POST['kdKbn'];
$klpmkVhc=$_POST['klpmkVhc'];
if($_SESSION['language']=='EN'){
    $arrKlmpk=array("KD"=>"Vehicle","MS"=>"Machinery","AB"=>"Heavy Equipment");
}else{
        $arrKlmpk=array("KD"=>"Kendaraan","MS"=>"Mesin","AB"=>"Alat Berat");
}
switch($proses)
{
	case'preview':
	echo"<table class=sortable cellspacing=1 border=0>
	<thead>
 		<tr class=rowheader>
		  <td>No</td>
		   <td align=center>".$_SESSION['lang']['kodeorganisasi']."</td>		 
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jenkendabmes'])."</td>
		   <td align=center>".$_SESSION['lang']['kodenopol']."</td>		
           <td align=center>".$_SESSION['lang']['namabarang']."</td>		
		   <td align=center>".$_SESSION['lang']['tahunperolehan']."</td>
		   <td align=center>".$_SESSION['lang']['noakun']."</td>
		   <td align=center>".$_SESSION['lang']['beratkosong']."</td>
		   <td align=center>".$_SESSION['lang']['nomorrangka']."</td>
		   <td align=center>".$_SESSION['lang']['nomormesin']."</td>
		   <td align=center>".$_SESSION['lang']['detail']."</td>	   
		   <td align=center>".$_SESSION['lang']['kepemilikan']."</td>
		  </tr>
		   </thead><tbody>
	";
	if(($kdKbn!='0')&&($klpmkVhc!='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' and kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
	}
	elseif(($kdKbn!='0')&&($klpmkVhc=='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' order by kodeorg,kodevhc";
	}
	elseif(($kdKbn=='0')&&($klpmkVhc!='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
	}
	elseif(($kdKbn=='0')&&($klpmkVhc=='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master order by kodeorg,kodevhc";
	}
	//echo "warning".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($query);
	if($row>0)
	{
		while($res=mysql_fetch_assoc($query))
		{
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			$no+=1;		
			$namabarang=$rBrg['namabarang'];
			
			$sJnsvhc="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$res['jenisvhc']."'";
			$qJnsVhc=mysql_query($sJnsvhc) or die(mysql_error());
			$rJnsVhc=mysql_fetch_assoc($qJnsVhc);

			
			
			if($res['kepemilikan']==1)
			{
			  $dptk=$_SESSION['lang']['miliksendiri'];	
			}
			else
			{
				$dptk=$_SESSION['lang']['sewa'];
			}		
			echo"<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$res['kodeorg']."</td>				 
				 <td>".$rJnsVhc['namajenisvhc']."</td>			 		
				 <td>".$res['kodevhc']."</td>
				 <td>".$namabarang."</td>
				 <td>".$res['tahunperolehan']."</td>
				 <td>".$res['noakun']."</td>
				 <td>".$res['beratkosong']."</td>		
				 <td>".$res['nomorrangka']."</td>	
				 <td>".$res['nomormesin']."</td> 
				 <td>".$res['detailvhc']."</td> 	
				 <td>".$dptk."</td>		
			</tr>
			";
		}
	}
	else
	{
		echo"<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>";
	}
	echo"</tbody></table>";
	break;
	case'pdf':
	$kdKbn=$_GET['kdKbn'];
	$klpmkVhc=$_GET['klpmkVhc'];
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdKbn;
				global $klpmkVhc;
				global $sDet;
				global $arrKlmpk;
				
			    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 15;
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
                
                $this->SetFont('Arial','B',12);
			//	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanKendAb'],'',0,'L');
			//	$this->Ln();
				$this->SetFont('Arial','',8);
				if(($kdKbn!='0')&&($klpmkVhc!='0'))
				{
					$sDet="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' and kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$kdKbn,'',0,'L');
					$this->Ln();
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodekelompok'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$arrKlmpk[$klpmkVhc],'',0,'L');
					$this->Ln();					
				}
				elseif(($kdKbn!='0')&&($klpmkVhc=='0'))
				{
					$sDet="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' order by kodeorg,kodevhc";
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$kdKbn,'',0,'L');
					$this->Ln();
				}
				elseif(($kdKbn=='0')&&($klpmkVhc!='0'))
				{
					$sDet="select * from ".$dbname.".vhc_5master where kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
					$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodekelompok'],'',0,'L');
					$this->Cell(5,$height,':','',0,'L');
					$this->Cell(45/100*$width,$height,$arrKlmpk[$klpmkVhc],'',0,'L');
					$this->Ln();
				}
				elseif(($kdKbn=='0')&&($klpmkVhc=='0'))
				{
					$sDet="select * from ".$dbname.".vhc_5master order by kodeorg,kodevhc";
				}
			
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height, $_SESSION['lang']['laporanKendAb'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
	
				
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				if($kdKbn=='0')
				{
					$this->Cell(8/100*$width,$height,$_SESSION['lang']['kodeorganisasi'],1,0,'C',1);	
				}
				if($klpmkVhc=='0')
				{
					$this->Cell(8/100*$width,$height,$_SESSION['lang']['kodekelompok'],1,0,'C',1);	
				}
				$this->Cell(17/100*$width,$height,$_SESSION['lang']['jenkendabmes'],1,0,'C',1);		
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['kodenopol'],1,0,'C',1);		
				$this->Cell(11/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);		
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['tahunperolehan'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['noakun'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['beratkosong'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['nomorrangka'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['nomormesin'],1,0,'C',1);				
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['kepemilikan'],1,1,'C',1);					
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('L','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
	
		$qDet=mysql_query($sDet) or die(mysql_error());
		while($rDet=mysql_fetch_assoc($qDet))
		{
			$no+=1;
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rDet['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			
			$sJnsvhc="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$rDet['jenisvhc']."'";
			$qJnsVhc=mysql_query($sJnsvhc) or die(mysql_error());
			$rJnsVhc=mysql_fetch_assoc($qJnsVhc);
			
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			if($kdKbn=='0')
			{
				$pdf->Cell(8/100*$width,$height,$rDet['kodeorg'],1,0,'C',1);	
			}
			if($klpmkVhc=='0')
			{
				$pdf->Cell(8/100*$width,$height,$arrKlmpk[$rDet['kelompokvhc']],1,0,'C',1);	
			}
			if($res['kepemilikan']==1)
			{
			  $dptk=$_SESSION['lang']['miliksendiri'];	
			}
			else
			{
				$dptk=$_SESSION['lang']['sewa'];
			}		
			$pdf->Cell(17/100*$width,$height,$rJnsVhc['namajenisvhc'],1,0,'C',1);		
			$pdf->Cell(8/100*$width,$height,$rDet['kodevhc'],1,0,'C',1);		
			$pdf->Cell(11/100*$width,$height,$rBrg['namabarang'],1,0,'C',1);		
			$pdf->Cell(8/100*$width,$height,$rDet['tahunperolehan'],1,0,'C',1);
			$pdf->Cell(8/100*$width,$height,$rDet['noakun'],1,0,'C',1);	
			$pdf->Cell(8/100*$width,$height,$rDet['beratkosong'],1,0,'C',1);	
			$pdf->Cell(8/100*$width,$height,$rDet['nomorrangka'],1,0,'C',1);	
			$pdf->Cell(8/100*$width,$height,$rDet['nomormesin'],1,0,'C',1);				
			$pdf->Cell(8/100*$width,$height,$dptk,1,1,'C',1);		
		}
			
        $pdf->Output();
	break;
	case'excel':
	$kdKbn=$_GET['kdKbn'];
	$klpmkVhc=$_GET['klpmkVhc'];
	if(($kdKbn!='0')&&($klpmkVhc!='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' and kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
		$tbl="<tr><td colspan=3>".$_SESSION['lang']['unit']."</td><td>".$kdKbn."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['kodekelompok']."</td><td>".$klpmkVhc."</td></tr>";
	}
	elseif(($kdKbn!='0')&&($klpmkVhc=='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kodeorg='".$kdKbn."' order by kodeorg,kodevhc";
		$tbl="<tr><td colspan=3>".$_SESSION['lang']['unit']."</td><td>".$kdKbn."</td></tr>";
	}
	elseif(($kdKbn=='0')&&($klpmkVhc!='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master where kelompokvhc='".$klpmkVhc."' order by kodeorg,kodevhc";
		$tbl="<tr><td colspan=3>".$_SESSION['lang']['kodekelompok']."</td><td>".$klpmkVhc."</td></tr>";
	}
	elseif(($kdKbn=='0')&&($klpmkVhc=='0'))
	{
		$sql="select * from ".$dbname.".vhc_5master order by kodeorg,kodevhc";
		$tbl="";
	}
			
			
			
			$stream.="
			<table>
			<tr><td colspan=13 align=center>".$_SESSION['lang']['laporanKendAb']."</td></tr>
			".$tbl."
			<tr><td colspan=3></td><td></td></tr>
			</table>
			<table border=1>
			<tr>
				<td bgcolor=#DEDEDE align=center valign=top>No.</td>
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['kodeorganisasi']."</td>
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['kodekelompok']."</td>
				<td bgcolor=#DEDEDE align=center valign=top>".str_replace(" ","<br>",$_SESSION['lang']['jenkendabmes'])."</td>
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['kodenopol']."</td>	
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['namabarang']."</td>	
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['tahunperolehan']."</td>	
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['noakun']."</td>		
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['beratkosong']."</td>		
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['nomorrangka']."</td>		
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['nomormesin']."</td>	
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['detail']."</td>
				<td bgcolor=#DEDEDE align=center valign=top>".$_SESSION['lang']['kepemilikan']."</td>	
			</tr>";
	
	//echo "warning".$sql;
	$query=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($query);
	if($row>0)
	{
		while($res=mysql_fetch_assoc($query))
		{
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			$no+=1;
		
			$namabarang=$rBrg['namabarang'];
			
			
			if($res['kepemilikan']==1)
			{
			  $dptk=$_SESSION['lang']['miliksendiri'];	
			}
			else
			{
				$dptk=$_SESSION['lang']['sewa'];
			}		
			$stream.="<tr class=rowcontent>
				 <td>".$no."</td>
				 <td>".$res['kodeorg']."</td>
				 <td>".$arrKlmpk[$res['kelompokvhc']]."</td>				 
				 <td>".$res['jenisvhc']."</td>			 		
				 <td>".$res['kodevhc']."</td>
				 <td>".$namabarang."</td>
				 <td>".$res['tahunperolehan']."</td>
				 <td>".$res['noakun']."</td>
				 <td>".$res['beratkosong']."</td>		
				 <td>".$res['nomorrangka']."</td>	
				 <td>".$res['nomormesin']."</td> 
				 <td>".$res['detailvhc']."</td> 	
				 <td>".$dptk."</td>		
			</tr>
			";
		}
	}
	else
	{
		$stream.="<tr class=rowcontent><td colspan=13 align=center>Not Found</td></tr>";
	}
	$stream.="</tbody></table>";
	
			
			//echo "warning:".$strx;
			//=================================================
		$stream.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="daftarKendaraan";
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
	case'getDetail':
	echo"<link rel=stylesheet type=text/css href=style/generic.css>";
	$nokontrak=$_GET['nokontrak'];
	$sHed="select  a.tanggalkontrak,a.koderekanan,a.kodebarang from ".$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
	$qHead=mysql_query($sHed) or die(mysql_error());
	$rHead=mysql_fetch_assoc($qHead);
	$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
	$qBrg=mysql_query($sBrg) or die(mysql_error());
	$rBrg=mysql_fetch_assoc($qBrg);
	
	$sCust="select namacustomer  from ".$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
	$qCust=mysql_query($sCust) or die(mysql_error());
	$rCust=mysql_fetch_assoc($qCust);
	echo"<fieldset><legend>".$_SESSION['lang']['detailPengiriman']."</legend>
	<table cellspacing=1 border=0 class=myinputtext>
	<tr>
		<td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>".$nokontrak."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tglKontrak']."</td><td>:</td><td>".tanggalnormal($rHead['tanggalkontrak'])."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['komoditi']."</td><td>:</td><td>".$rBrg['namabarang']."</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['Pembeli']."</td><td>:</td><td>".$rCust['namacustomer']."</td>
	</tr>
	</table><br />
	<table cellspacing=1 border=0 class=sortable><thead>
	<tr class=data>
	<td>".$_SESSION['lang']['notransaksi']."</td>
	<td>".$_SESSION['lang']['tanggal']."</td>
	<td>".$_SESSION['lang']['nodo']."</td>
	<td>".$_SESSION['lang']['nosipb']."</td>
	<td>".$_SESSION['lang']['beratBersih']."</td>
	<td>".$_SESSION['lang']['kodenopol']."</td>
	<td>".$_SESSION['lang']['sopir']."</td>
	</tr></thead><tbody>
	";
/*	$sDet="select a.tanggalkontrak,a.pembeli,a.komoditi,b.* from ".$dbname.".pmn_kontrakjual a inner join ".$dbname.".pabrik_timbangan on a.nokontrak=b.nokontrak where a.nokontrak='".$nokontrak."'";
*/	

	$sDet="select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from ".$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
	$qDet=mysql_query($sDet) or die(mysql_error());
	$rCek=mysql_num_rows($qDet);
	if($rCek>0)
	{
		while($rDet=mysql_fetch_assoc($qDet))
		{
			echo"<tr class=rowcontent>
			<td>".$rDet['notransaksi']."</td>
			<td>".tanggalnormal($rDet['tanggal'])."</td>
			<td>".$rDet['nodo']."</td>
			<td>".$rDet['nosipb']."</td>
			<td align=right>".number_format($rDet['beratbersih'],2)."</td>
			<td>".$rDet['nokendaraan']."</td>
			<td>".ucfirst($rDet['supir'])."</td>
			</tr>";
		}
	}
	else
	{
		echo"<tr><td colspan=7>Not Found</td></tr>";
	}
	echo"</tbody></table></fieldset>";

	break;
	default:
	break;
}

?>
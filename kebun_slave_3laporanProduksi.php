<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

//$arr="##periode##tipeIntex##unit";
$periode=$_POST['periode'];
$tipeIntex=$_POST['tipeIntex'];
$unit=$_POST['unit'];
$kodeOrg=$_POST['kodeOrg'];
$brsKe=$_POST['brsKe'];
$tgl=tanggalsystem($_POST['tgl']);
$tglAfd=$_POST['tglAfd'];
$kdBlok=$_POST['kdBlok'];
$endKe=$_POST['endKe'];
$nospb=$_POST['nospb'];
$kodePabrik=$_POST['kodePabrik'];
$kodeUnit=$_POST['kodeUnit'];
$ispo=$_POST['ispo'];
switch($proses)
{
	case'getKdorg':
	//echo "warning:masuk";
	$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	if($tipeIntex==1)
	{
		//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk in(select induk from ".$dbname.".organisasi where tipe='PABRIK') order by namaorganisasi asc";
            $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk ='PMO' order by namaorganisasi asc";
	}
	elseif($tipeIntex==0)
	{
		$sOrg="SELECT namasupplier,`kodetimbangan` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' and kodetimbangan!='NULL' order by namasupplier asc";//echo "warning:".$sOrg;
	}
	elseif($tipeIntex==2)
	{
		//$sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk not in(select induk from ".$dbname.".organisasi where tipe='PABRIK') order by namaorganisasi asc";
            $sOrg="SELECT namaorganisasi,kodeorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' and induk <>'PMO' order by namaorganisasi asc";
	}
	//echo "warning".$sOrg;exit();
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		if($tipeIntex!=0)
		{
			$optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
		}
		else
		{
			$optorg.="<option value=".$rOrg['kodetimbangan'].">".$rOrg['namasupplier']."</option>";
		}
	}
	echo $optorg;
	break;
	case'getAfdeling':
	
	echo"
	<img onclick=\"closeAfd(".$brsKe.");\" title=\"Tutup\" class=\"resicon\" src=\"images/close.gif\">
	<table cellspacing=1 border=0 class=sortable width=100%>
		<thead>
			<tr class=rowheader>
				<td>".$_SESSION['lang']['nospb']."</td>
				<td>Kode ".$_SESSION['lang']['afdeling']."</td>
				<td>".$_SESSION['lang']['afdeling']."</td>
			</tr>
		</thead><tbody>";
	//$sGet="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$kodeOrg."' and tipe='AFDELING'";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$kodeOrg."' and tipe='AFDELING'";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rData=mysql_fetch_assoc($qOrg))
	{
		$rDataOrg[$rData['kodeorganisasi']]=$rData;
	}
	$sGet="select substr(a.nospb,9,6) as kodeorganisasi,a.nospb from ".$dbname.".kebun_spbdt a left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb where a.nospb like '%".$kodeOrg."%' and b.tanggal ='".$tglAfd."' group by nospb";
	//echo $sGet;
	$qGet=mysql_query($sGet) or die(mysql_error());
	while($rGet=mysql_fetch_assoc($qGet))
	{
		$no+=1;
		
		echo"<tr class=rowcontent id=detail_".$brsKe."_".$no." onclick=detailBlok(".$brsKe.",".$no.") style=\"cursor: pointer;\" >
				<td id=nospb_".$brsKe."_".$no.">".$rGet['nospb']."</td>
				<td id=kdBlok_".$brsKe."_".$no.">".$rGet['kodeorganisasi']."</td>
				<td>".$rDataOrg[$rGet['kodeorganisasi']]['namaorganisasi']."</td>
			</tr><tr><td colspan=2><div id=detailBlok_".$brsKe."_".$no."></div></td></tr>";
	}
	echo"		</tbody>
		</table>";

	break;
	case'getUnit':
	
	$sOrg="select distinct kodeorg from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%".$kodePabrik."%' order by kodeorg";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	echo"<option value=''>".$_SESSION['lang']['all']."</option>";
	while($rData=mysql_fetch_assoc($qOrg))
	{
		echo"<option value=".$rData['kodeorg'].">".$rData['kodeorg']."</option>";
	}
	break;
	case'getAfdeling2':
$sOrg2="select distinct kodeorg from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%".$kodePabrik."%' order by kodeorg";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
$unitintimbangan='(';
while($rData2=mysql_fetch_assoc($qOrg2))
{
        $unitintimbangan.="'".$rData2['kodeorg']."',";
}
$unitintimbangan=substr($unitintimbangan,0,-1);
$unitintimbangan.=')'; if($unitintimbangan==')')$unitintimbangan="('')";
if($kodeUnit=='')
	$sOrg="select kodeorganisasi from ".$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan." order by kodeorganisasi"; else
	$sOrg="select kodeorganisasi from ".$dbname.".organisasi where tipe = 'AFDELING' and induk like '%".$kodeUnit."%' order by kodeorganisasi";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	echo"<option value=''>".$_SESSION['lang']['all']."</option>";
	while($rData=mysql_fetch_assoc($qOrg))
	{
		echo"<option value=".$rData['kodeorganisasi'].">".$rData['kodeorganisasi']."</option>";
	}
	break;
        case'getPrestasi':
	//setup_kegiatan
	echo"<table cellspacing=1 border=0 class=sortable>
	<thead>
	<tr class=rowheader>
	<td>".$_SESSION['lang']['blok']."</td>
	<td>".$_SESSION['lang']['brondolan']."</td>
	<td>".$_SESSION['lang']['kgbjr']."</td>
	<td>".$_SESSION['lang']['janjang']."</td>
	</tr>
	</thead><tbody>
	";
	/*$sPrestasi="select b.namakegiatan,b.satuan,a.hasilkerja,a.jumlahhk,c.tipetransaksi from ".$dbname.".kebun_prestasi a left join ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi where a.kodeorg='".$kdBlok."' and c.tanggal='".$tgl."'";*/
	$sPrestasi="select * from ".$dbname.".kebun_spbdt where nospb='".$nospb."'";
	$qPrestasi=mysql_query($sPrestasi) or die(mysql_error());
	$bPrestasi=mysql_num_rows($qPrestasi);
	if($bPrestasi>0)
	{
		while($rPrestasi=mysql_fetch_assoc($qPrestasi))
		{
			$no+=1;
			echo"<tr class=rowcontent onclick=\"closeBlok(".$brsKe.",".$endKe.")\">
			<td>".$rPrestasi['blok']."</td>
			<td>".$rPrestasi['kgbjr']."</td>
			<td>".$rPrestasi['brondolan']."</td>
			<td align=right>".$rPrestasi['jjg']."</td>
			</tr>";
			$total+=$rPrestasi['jjg'];
		}
		echo"<tr class=\"rowcontent\"><td colspan=3>Total</td><td align=right>".number_format($total,2)."</td></tr>";
		
	}
	else
	{
		echo"<tr class=rowcontent onclick=\"closeBlok(".$brsKe.",".$endKe.")\"><td colspan=5>Data Kosong</td></tr>";
	}
	echo"</tbody></table>";
	break;
	case'preview':
	if($tipeIntex!='')
	{
		$where.=" and intex='".$tipeIntex."'";
	}
	else
	{
		echo"warning:Pilih salah satu Sumber TBS";
		exit();
	}
	if($unit!="")
	{
		if($tipeIntex==0)
		{
			$where.=" and kodecustomer='".$unit."'";
		}
		elseif($tipeIntex!=0)
		{
			$where.=" and a.kodeorg='".$unit."' ";
		}
	}
	if($periode!='')
	{
		$where.=" and tanggal like '%".$periode."%'";
	}
        if ($ispo!=''){
            $where.=" and ispo=".$ispo;
        }
	

	echo"<table cellspacing=1 border=0 class=sortable>
	<thead class=rowheader>
	<tr>
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['janjang']."</td>
		<td>".$_SESSION['lang']['beratBersih']." (KG)</td>
	</tr>
	</thead>
	<tbody>";
	
	$sData="select a.kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal from ".$dbname.".pabrik_timbangan a
                left join ".$dbname.".kebun_spbdt b on a.nospb=b.nospb
                left join ".$dbname.".setup_blok c on b.blok=c.kodeorg
                where kodebarang='40000003' ".$where." group by substr(tanggal,1,10)";
	//echo "warning".$sData;exit();
	$qData=mysql_query($sData) or die(mysql_error());
	
	$brs=mysql_num_rows($qData);
	if($brs>0)
	{
		
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
			
			if($tipeIntex!=0)
			{
				$sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namaorganisasi'];
				$kd=$rData['kodeorg'];
				$isi=" value=".$kd."";
				
				//$stat=" onclick=getAfd(".$no.") style=\"cursor: pointer;\"";
	
			}
			else
			{
				$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namasupplier'];	
				$stat="";	
				$isi="";		
			}
			
			echo"
			<tr class=rowcontent id=row_".$no." ".$stat.">
			<td>".$no."</td>
			<td id=kdOrg_".$no." ".$isi.">".$nm."</td>
			<td id=tanggal_".$no." value=".$rData['tanggal'].">".tanggalnormal($rData['tanggal'])."</td>
			<td  align=right>".$rData['jjg']."</td>
			<td align=right>".number_format($rData['netto'],2)."</td>
			</tr>";
                        //<tr><td colspan=5><div id=detail_".$no."></div></td></tr>";
			$subtota+=$rData['netto'];
		}
		echo"<tr class=rowcontent ><td colspan=4 align=right>Total (KG)</td><td align=right>".number_format($subtota,2)."</td></tr>";
	}
	else
	{
		echo"<tr class=rowcontent><td colspan=5 align=center>Data Kosong</td></tr>";
	}
	break;
	case'pdf':
	$periode=$_GET['periode'];
	$tipeIntex=$_GET['tipeIntex'];
	$unit=$_GET['unit'];
	
	
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $tipeIntex;
				global $periode;
				global $unit;
				global $where;
				
				
				$tglPeriode=explode("-",$periode);
				$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
                # Alamat & No Telp
       /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                    "kodeorganisasi='".$kdPt."'");
                $orgData = fetchData($query);*/
				$sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
				$qAlamat=mysql_query($sAlmat) or die(mysql_error());
				$rAlamat=mysql_fetch_assoc($qAlamat);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 11;
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();	
                $this->Ln();
				$this->Ln();
                $this->SetFont('Arial','B',11);
                $this->Cell($width,$height, $_SESSION['lang']['rProdKebun'],0,1,'C');	
			 	$this->SetFont('Arial','',8);
			 	$this->Cell($width,$height, "Periode : ".$tanggal,0,1,'C');	
				$this->Ln();$this->Ln();
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
				
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(18/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);		
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);			
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['beratBersih']." (KG)",1,1,'C',1);	            
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
        $height = 9;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		if($tipeIntex!='')
		{
			$where.=" and intex='".$tipeIntex."'";
		}
		else
		{
			echo"warning:Pilih salah satu Sumber TBS";
			exit();
		}
		if($unit!="")
		{
			if($tipeIntex==0)
			{
			$where.=" and kodecustomer='".$unit."'";
			}
			elseif($tipeIntex!=0)
			{
			$where.=" and kodeorg='".$unit."' ";
			}
		}
		if($periode!='')
		{
			$where.=" and tanggal like '%".$periode."%'";
		}
		
		$sList="select kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." group by substr(tanggal,1,10)";
		$qList=mysql_query($sList) or die(mysql_error());
		while($rData=mysql_fetch_assoc($qList))
		{			
			if($tipeIntex!=0)
			{
				$sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namaorganisasi'];
				$kd=$rData['kodeorg'];
				$isi=" value=".$kd."";
				
				$stat=" onclick=getAfd(".$no.") style=\"cursor: pointer;\"";
	
			}
			else
			{
				$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namasupplier'];	
				$stat="";	
				$isi="";		
			}
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(18/100*$width,$height,$nm,1,0,'C',1);		
			$pdf->Cell(12/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
			$pdf->Cell(10/100*$width,$height,number_format($rData['jjg']),1,0,'R',1);			
			$pdf->Cell(15/100*$width,$height,number_format($rData['netto'],2),1,1,'R',1);
			$subtota+=$rData['netto'];
		}
		$pdf->Cell(43/100*$width,$height,"Total",1,0,'C',1);
		$pdf->Cell(15/100*$width,$height,number_format($subtota,2),1,1,'R',1);
		
			
    $pdf->Output();
	break;
	case'excel':
	$periode=$_GET['periode'];
	$tipeIntex=$_GET['tipeIntex'];
	$unit=$_GET['unit'];
	$tglPeriode=explode("-",$periode);
	$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
	
	if($tipeIntex!='')
	{
		$where.=" and intex='".$tipeIntex."'";
	}
	else
	{
		echo"warning:Pilih salah satu Sumber TBS";
		exit();
	}
	if($unit!="")
	{
		if($tipeIntex==0)
		{
			$where.=" and kodecustomer='".$unit."'";
		}
		elseif($tipeIntex!=0)
		{
			$where.=" and kodeorg='".$unit."' ";
		}
	}
	if($periode!='')
	{
		$where.=" and tanggal like '%".$periode."%'";
	}
	$tab.="<table cellspacing=1 border=0>
	<tr><td colspan=5 align=center>".$_SESSION['lang']['rProdKebun']."</td></tr>
	<tr><td colspan=2  align=left>Periode</td><td colspan=3 align=left>".$tanggal."</td></tr>
	</table>
	";

	$tab.="<table cellspacing=1 border=1 class=sortable>
	<thead >
	<tr class=rowheader>
		<td bgcolor=#DEDEDE>No.</td>";
	
	$tab.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['kodeorg']."</td>";
	$tab.="<td bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['janjang']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']." (KG)</td>
	</tr>
	</thead>
	<tbody>";
	
	$sData="select kodeorg,sum(jumlahtandan1) as jjg,sum(beratbersih-kgpotsortasi) as netto,kodecustomer,tanggal from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." group by substr(tanggal,1,10)";
	//echo "warning".$sData;exit();
	$qData=mysql_query($sData) or die(mysql_error());
	
	$brs=mysql_num_rows($qData);
	if($brs>0)
	{
		
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
			
			if($tipeIntex!=0)
			{
				$sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namaorganisasi'];
				$kd=$rData['kodeorg'];
				$isi=" value=".$kd."";
				
				$stat=" onclick=getAfd(".$no.") style=\"cursor: pointer;\"";
	
			}
			else
			{
				$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
				$qNm=mysql_query($sNm) or die(mysql_error());
				$rNm=mysql_fetch_assoc($qNm);
				$nm=$rNm['namasupplier'];	
				$stat="";	
				$isi="";		
			}
			
			$tab.="
			<tr class=rowcontent id=row_".$no." ".$stat.">
			<td>".$no."</td>
			<td id=kdOrg_".$no." ".$isi.">".$nm."</td>
			<td id=tanggal_".$no.">".tanggalnormal($rData['tanggal'])."</td>
			<td  align=right>".number_format($rData['jjg'])."</td>
			<td align=right>".number_format($rData['netto'],2)."</td>
			</tr>";
			$subtota+=$rData['netto'];
		}
		$tab.="<tr class=rowcontent ><td colspan=4 align=right>Total (KG)</td><td align=right>".number_format($subtota,2)."</td></tr>";
	}
	else
	{
		$tab.="<tr class=rowcontent><td colspan=5 align=center>Data Kosong</td></tr>";
	}
	
			
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			$tglSkrg=date("Ymd");
			$nop_="LaporanProduksi_".$unit."_".$periode;
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
	default:
	break;
}
?>
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
$kdPt=$_POST['kdPt'];
//$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
$kdSup=$_POST['kdSup'];
$kdUnit=$_POST['kdUnit'];
$tglDr=tanggalsystem($_POST['tglDr']);
$tanggalSampai=tanggalsystem($_POST['tanggalSampai']);
$lokBeli=$_POST['lokBeli'];

switch($proses)
{
	case'getKdorg':
	//echo "warning:masuk";
	$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdPt."'";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	}
	echo $optorg;
	break;
	case'preview':
	
	if(($tglDr=='')||($tanggalSampai==''))
	{
		echo"warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong";
		exit();
	}
	else
	{
		if($kdPt!='')
		{
			$where.=" and a.kodeorg='".$kdPt."'";
		}
		if($kdUnit!='')
		{
			$where.=" and substring(b.nopp,16,4)='".$kdUnit."'";
		}
		if($kdSup!="")
		{
			$where.=" and a.kodesupplier='".$kdSup."'";
		}
		if(($tglDr!='')||($tanggalSampai!=''))
		{
			$where.=" and (a.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
		}
                if($lokBeli!='')
                {
                    $where.=" and lokalpusat='".$lokBeli."'";
                }
	}
	echo"<table cellspacing=1 border=0 class=sortable>
	<thead class=rowheader>
	<tr>
		<td>No.</td>
		<td>".$_SESSION['lang']['supplier']."</td>
		<td>".$_SESSION['lang']['nopo']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['kodebarang']."</td>
		<td>".$_SESSION['lang']['namabarang']."</td>
		<td>".$_SESSION['lang']['matauang']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>
		<td>".$_SESSION['lang']['satuan']."</td>
		<td>".$_SESSION['lang']['total']."</td>
		<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['prmntaanPembelian']." </td>
		<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['bapb']."</td>
	</tr>
	</thead>
	<tbody>";
	$data=array();
	$sData="select a.kodesupplier from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 ".$where." group by kodesupplier order by a.tanggal asc";
        //echo $sData;
	$qData=mysql_query($sData) or die(mysql_error());
	
	while($rData=mysql_fetch_assoc($qData))
	{
		/*$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rData['kodesupplier']."'";
		$qNm=mysql_query($sNm) or die(mysql_error());
		$rNm=mysql_fetch_assoc($qNm);
		*/
		$data[]=$rData;
		
	}
        
        if($kdPt!='') {
                $where2.=" and a.kodeorg='".$kdPt."'";
                $where3.=" and b.kodeorg='".$kdPt."'";
        }
        if(($tglDr!='')||($tanggalSampai!='')) {
                $where2.=" and (a.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
                $where3.=" and (b.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
        }
        if($lokBeli!='') {
                $where2.=" and lokalpusat='".$lokBeli."'";
                $where3.=" and lokalpusat='".$lokBeli."'";
        }
        
	foreach($data as $row => $dt)
	{
		$no+=1;
                
		$afdC=false;$blankC=false;
		$sList="select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan,a.kodeorg from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 and a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where2;
		$qList=mysql_query($sList) or die(mysql_error());
		$grandTot=array();
		while($rList=mysql_fetch_assoc($qList))
		{
			$sRow="select a.nopo from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo where b.statuspo>1 and b.statuspo<5 and b.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where3;	
			$qRow=mysql_query($sRow) or die(mysql_error());
			$rRow=mysql_num_rows($qRow);
			
			$tmpRow = $rRow-1;
			$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$dt['kodesupplier']."'";
			$qNm=mysql_query($sNm) or die(mysql_error());
			$rNm=mysql_fetch_assoc($qNm);
			
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rList['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			if($rList['matauang']!='IDR')
			{
				$sKurs="select kurs from ".$dbname.".setup_matauangrate where kode='".$rList['matauang']."' and daritanggal='".$rList['tanggal']."'";
				$qKurs=mysql_query($sKurs) or die(mysql_error());
				$rKurs=mysql_fetch_assoc($qKurs);
				if($rKurs!='')
				{
					$hrg=$rKurs['kurs']*$rList['hargasatuan'];
					$totHrg=$rList['jumlahpesan']*$hrg;
				}
				else
				{
					if($rList['matauang']=='USD')
					{
						$hrg=$rList['hargasatuan']*8850;
						$totHrg=$rList['jumlahpesan']*$hrg;
						$rList['matauang']="IDR";
					}
					elseif($rList['matauang']=='EUR') 
					{
						$hrg=$rList['hargasatuan']*12643;
						$totHrg=$rList['jumlahpesan']*$hrg;
						$rList['matauang']="IDR";
					}
					elseif(($rList['matauang']=='')||($rList['matauang']=='NULL'))
					{
						$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
					}
				}
			}
			else
			{
				$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
			}
			$grandTot['total']+=$totHrg;
			if($rList['nopp']!="")
			{
				$sTgl="select tanggal from ".$dbname.".log_prapoht where nopp='".$rList['nopp']."'";
				$qTgl=mysql_query($sTgl) or die(mysql_error());
				$rTgl=mysql_fetch_assoc($qTgl);
				
				if(($rTgl['tanggal']!="")||($rTgl['tanggal']!="000-00-00"))
				{
						$tglPP=tanggalnormal($rTgl['tanggal']);		
				}
				else
				{
					$tglPP="";
				}
			}
			else
			{
				$tglPP="";
			}
			if($rList['nopo']!="")
			{
				$sTgl2="select tanggal from ".$dbname.".log_transaksiht where nopo='".$rList['nopo']."' and tipetransaksi=1";
				$qTgl2=mysql_query($sTgl2) or die(mysql_error());
				$rTgl2=mysql_fetch_assoc($qTgl2);
				if($rTgl2['tanggal']!="")
				{
						$tglBapb=tanggalnormal($rTgl2['tanggal']);		
				}
				else
				{
					$tglBapb="";
				}
			}
			else
			{
				$tglBapb="";
			}
			
			$tab.="<tr class='rowcontent'>";
			if($afdC==false) {
				$tab .= "<td>".$no."</td>";
				$tab .= "<td value='".$dt['kodesupplier']."'>".$rNm['namasupplier']."</td>";
				$afdC = true;
			} else {
				if($blankC==false) {
					$tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
					$tab .= "<td  rowspan='".$tmpRow."'>&nbsp;</td>";
					$blankC = true;
				}
			}	
			$tab.="<td>".$rList['nopo']."</td>";
			$tab.="<td nowrap>".tanggalnormal($rList['tanggal'])."</td>";
			$tab.="<td>".$rList['kodebarang']."</td>";
			$tab.="<td>".$rBrg['namabarang']."</td>";
			$tab.="<td align=center>".$rList['matauang']."</td>";
			$tab.="<td align=right>".$rList['jumlahpesan']."</td>";
			$tab.="<td align=center>".$rList['satuan']."</td>";
			$tab.="<td align=right>".number_format($totHrg,2)."</td>";
			$tab.="<td nowrap>".$tglPP."</td>";
			$tab.="<td nowrap>".$tglBapb."</td>";
			$tab.="</tr>";
		}
		$tab .= "<tr class='rowcontent'>";
		$tab .= "<td colspan='9' align='right'><b>Sub Total </b></td>";
		$tab .= "<td align=right>".number_format($grandTot['total'],2)."</td>";
		$tab .= "<td colspan='2' >&nbsp;</td>";
		$tab .= "</tr>";
		
	}
	echo $tab;
	break;
	case'pdf':
	$kdPt=$_GET['kdPt'];
	//$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
	$kdSup=$_GET['kdSup'];
	$kdUnit=$_GET['kdUnit'];
	$tglDari=tanggalsystem($_GET['tglDr']);
	$tanggalSampai=tanggalsystem($_GET['tanggalSampai']);	
        $lokBeli=$_GET['lokBeli'];
	//echo $tglDari."__".$tanggalSampai;exit();
	if(($tglDari=='')||($tanggalSampai==''))
	{
		echo"warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong";
		exit();
	}
	else
	{
		if($kdPt!='')
				{
					$where.=" and a.kodeorg='".$kdPt."'";
				}
				if($kdUnit!='')
				{
					$where.=" and substring(b.nopp,16,4)='".$kdUnit."'";
				}
				if($kdSup!="")
				{
					$where.=" and a.kodesupplier='".$kdSup."'";
				}
				if(($tglDr!='')||($tanggalSampai!=''))
				{
					$where.=" and (a.tanggal between '".$tglDari."' and '".tanggalsystem($_GET['tanggalSampai'])."')";
				}
                                if($lokBeli!='')
                                {
                                    $where.=" and lokalpusat='".$lokBeli."'";
                                }
	}
	
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdPt;
				global $kdSup;
				global $kdUnit;
				global $tglDari;
				global $tanggalSampai;
				global $where;
				global $isi;
				
				$isi=array();
				if($kdPt=="")
				{
					$pt='MHO';
				}
				else
				{
					$pt=$kdPt;
				}
                # Alamat & No Telp
       /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                    "kodeorganisasi='".$kdPt."'");
                $orgData = fetchData($query);*/
				$sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
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
                $this->Cell($width,$height, $_SESSION['lang']['detPemb'],0,1,'C');	
			 	$this->SetFont('Arial','',8);
			 	$this->Cell($width,$height, "Periode : ".$_GET['tglDr']." s.d. ".$_GET['tanggalSampai'],0,1,'C');	
				$this->Ln();$this->Ln();
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);

				
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['supplier'],1,0,'C',1);		
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['nopo'],1,0,'C',1);		
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);			
				$this->Cell(22/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);	
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['matauang'],1,0,'C',1);		
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
				$this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." PP",1,0,'C',1);	
				$this->Cell(7/100*$width,$height,$_SESSION['lang']['tanggal']." BAPB",1,1,'C',1);					
            
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
        $height = 9;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		$sData="select a.kodesupplier from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 ".$where." group by kodesupplier order by a.tanggal asc";
	$qData=mysql_query($sData) or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$isi[]=$rData;
	}

        if($kdPt!='') {
                $where2.=" and a.kodeorg='".$kdPt."'";
        }
        if(($tglDr!='')||($tanggalSampai!='')) {
                $where2.=" and (a.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
        }
        if($lokBeli!='') {
                $where2.=" and lokalpusat='".$lokBeli."'";
        }
        
        $totalAll=array();
	foreach($isi as $test => $dt)
	{
		$no+=1;
		
		$i=0;$afdC=false;
		$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$dt['kodesupplier']."'";
		$qNm=mysql_query($sNm) or die(mysql_error());
		$rNm=mysql_fetch_assoc($qNm);
		if($afdC==false)
		{
			$pdf->Cell(3/100*$width,$height,$no,'TLR',0,'C',1);
			$pdf->Cell(15/100*$width,$height,$rNm['namasupplier'],'TLR',0,'C',1);	
		}
		
		$sList="select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 and a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where2;
		$qList=mysql_query($sList) or die(mysql_error());
		$grandTot=array();

		while($rList=mysql_fetch_assoc($qList))
		{		
			$limit++;
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rList['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			if($rList['matauang']!='IDR')
			{
				$sKurs="select kurs from ".$dbname.".setup_matauangrate where kode='".$rList['matauang']."' and daritanggal='".$rList['tanggal']."'";
				$qKurs=mysql_query($sKurs) or die(mysql_error());
				$rKurs=mysql_fetch_assoc($qKurs);
				if($rKurs!='')
				{
					$hrg=$rKurs['kurs']*$rList['hargasatuan'];
					$totHrg=$rList['jumlahpesan']*$hrg;
				}
				else
				{
					if($rList['matauang']=='USD')
					{
						$hrg=$rList['hargasatuan']*8850;
						$totHrg=$rList['jumlahpesan']*$hrg;
						$rList['matauang']="IDR";
					}
					elseif($rList['matauang']=='EUR') 
					{
						$hrg=$rList['hargasatuan']*12643;
						$totHrg=$rList['jumlahpesan']*$hrg;
						$rList['matauang']="IDR";
					}
					elseif(($rList['matauang']=='')||($rList['matauang']=='NULL'))
					{
						$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
					}
				}
			}
			else
			{
				$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
			}
			//$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
			$grandTot['total']+=$totHrg;
			if($rList['nopp']!="")
			{
				$sTgl="select tanggal from ".$dbname.".log_prapoht where nopp='".$rList['nopp']."'";
				$qTgl=mysql_query($sTgl) or die(mysql_error());
				$rTgl=mysql_fetch_assoc($qTgl);
				
				if(($rTgl['tanggal']!="")||($rTgl['tanggal']!="000-00-00"))
				{
						$tglPP=tanggalnormal($rTgl['tanggal']);		
				}
				else
				{
					$tglPP="";
				}
			}
			else
			{
				$tglPP="";
			}
			if($rList['nopo']!="")
			{
				$sTgl2="select tanggal from ".$dbname.".log_transaksiht where nopo='".$rList['nopo']."' and tipetransaksi=1";
				$qTgl2=mysql_query($sTgl2) or die(mysql_error());
				$rTgl2=mysql_fetch_assoc($qTgl2);
				if($rTgl2['tanggal']!="")
				{
						$tglBapb=tanggalnormal($rTgl2['tanggal']);		
				}
				else
				{
					$tglBapb="";
				}
			}
			else
			{
				$tglBapb="";
			}
			if($afdC==true) {
				$i=0;
				$pdf->Cell(3/100*$width,$height,'','LR',$align[$i],1);
				$pdf->Cell(15/100*$width,$height,'','LR',$align[$i],1);
				//$pdf->Cell($length[$i]/100*$width,$height,'','LR',$align[$i],1);
				$i++;
			} else {
				$afdC = true;
			}	
			$pdf->Cell(12/100*$width,$height,$rList['nopo'],1,0,'L',1);		
			$pdf->Cell(6/100*$width,$height,tanggalnormal($rList['tanggal']),1,0,'C',1);			
			$pdf->Cell(22/100*$width,$height,$rBrg['namabarang'],1,0,'L',1);	
			$pdf->Cell(6/100*$width,$height,$rList['matauang'],1,0,'C',1);		
			$pdf->Cell(6/100*$width,$height,$rList['jumlahpesan'],1,0,'R',1);
			$pdf->Cell(6/100*$width,$height,$rList['satuan'],1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,number_format($totHrg,2),1,0,'R',1);	
			$pdf->Cell(7/100*$width,$height,$tglPP,1,0,'C',1);	
			$pdf->Cell(7/100*$width,$height,$tglBapb,1,1,'C',1);
			//if($limit==46)				
//			{	
//				$limit=0;
//				$pdf->AddPage();
//			}
			
		}
		$totalAll['totalSemua']+=$grandTot['total'];
		$pdf->Cell(76/100*$width,$height,"Sub Total",1,0,'C',1);
		$pdf->Cell(10/100*$width,$height,number_format($grandTot['total'],2),1,0,'R',1);
		$pdf->Cell(14/100*$width,$height,'',1,1,'R',1);
	}
	$pdf->Cell(76/100*$width,$height,"Total",1,0,'C',1);
	$pdf->Cell(10/100*$width,$height,number_format($totalAll['totalSemua'],2),1,0,'R',1);
	$pdf->Cell(14/100*$width,$height,'',1,1,'R',1);
	$pdf->Cell($width,$height,terbilang($totalAll['totalSemua'],2),1,1,'C',1);

				
        $pdf->Output();
	break;
	case'excel':
	$kdPt=$_GET['kdPt'];
	//$arr="##kdPt##kdSup##kdUnit##tglDr##tglSmp";
	$kdSup=$_GET['kdSup'];
	$kdUnit=$_GET['kdUnit'];
	$tglDr=tanggalsystem($_GET['tglDr']);
	$tanggalSampai=tanggalsystem($_GET['tanggalSampai']);	
        $lokBeli=$_GET['lokBeli'];
	$data=array();
	if(($tglDr=='')||($tanggalSampai==''))
	{
		echo"warning:Tanggal Dari dan Sampai Tanggal Tidak Boleh Kosong";
		exit();
	}
	else
	{
		if($kdPt!='')
		{
			$where.=" and a.kodeorg='".$kdPt."'";
		}
		if($kdUnit!='')
		{
			$where.=" and substring(b.nopp,16,4)='".$kdUnit."'";
		}
		if($kdSup!="")
		{
			$where.=" and a.kodesupplier='".$kdSup."'";
		}
		if(($tglDr!='')||($tanggalSampai!=''))
		{
			$where.=" and (a.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
		}
                  if($lokBeli!='')
                    {
                        $where.=" and lokalpusat='".$lokBeli."'";
                    }
	}
	$tab.="
	<table>
			<tr><td colspan=12 align=center>LAPORAN DETAIL PEMBELIAN</td></tr>
			<tr><td colspan=12 align=center>Periode : ".$_GET['tglDr']." s.d. ".$_GET['tanggalSampai']."</td></tr>
			</table>
	<table cellspacing=1 border=1 class=sortable>
	<thead class=rowheader>
	<tr>
		<td bgcolor=#DEDEDE align=center>No.</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['supplier']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopo']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['matauang']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['prmntaanPembelian']."</td>
		<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['bapb']."</td>
	</tr>
	</thead>
	<tbody>";
	
	$sData="select a.kodesupplier from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 ".$where." group by kodesupplier order by a.tanggal asc";
	$qData=mysql_query($sData) or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		/*$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rData['kodesupplier']."'";
		$qNm=mysql_query($sNm) or die(mysql_error());
		$rNm=mysql_fetch_assoc($qNm);
		*/
		$data[]=$rData;
		
	}
        
        if($kdPt!='') {
                $where2.=" and a.kodeorg='".$kdPt."'";
                $where3.=" and b.kodeorg='".$kdPt."'";
        }
        if(($tglDr!='')||($tanggalSampai!='')) {
                $where2.=" and (a.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
                $where3.=" and (b.tanggal between '".$tglDr."' and '".$tanggalSampai."')";
        }
        if($lokBeli!='') {
                $where2.=" and lokalpusat='".$lokBeli."'";
                $where3.=" and lokalpusat='".$lokBeli."'";
        }
        
	$totalAll=array();
	foreach($data as $row => $dt)
	{
		$no+=1;
		
		$afdC=false;$blankC=false;
		$sList="select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan,a.ppn from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 and a.statuspo<5 and a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where2;
		//echo $sList;
		$qList=mysql_query($sList) or die(mysql_error());
		$grandTot=array();
		while($rList=mysql_fetch_assoc($qList))
		{
			$sRow="select a.nopo from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo where b.statuspo>1 and b.statuspo<5 and b.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' ".$where3;	
			$qRow=mysql_query($sRow) or die(mysql_error());
			$rRow=mysql_num_rows($qRow);
			
			$tmpRow = $rRow-1;
			$sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$dt['kodesupplier']."'";
			$qNm=mysql_query($sNm) or die(mysql_error());
			$rNm=mysql_fetch_assoc($qNm);
			
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rList['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			if($rList['matauang']!='IDR')
			{
				$sKurs="select kurs from ".$dbname.".setup_matauangrate where kode='".$rList['matauang']."' and daritanggal='".$rList['tanggal']."'";
				$qKurs=mysql_query($sKurs) or die(mysql_error());
				$rKurs=mysql_fetch_assoc($qKurs);
				if($rKurs!='')
				{
					$hrg=$rKurs['kurs']*$rList['hargasatuan'];
					//$ppnDlr=$rList['ppn']*$rKurs['kurs'];
					$totHrg=$rList['jumlahpesan']*$hrg;
				}
				else
				{
					if($rList['matauang']=='USD')
					{
						$hrg=$rList['hargasatuan']*8850;
						//$ppnDlr=$rList['ppn']*8850;
						$totHrg=$rList['jumlahpesan']*$hrg;
						//$rList['matauang']="IDR";
					}
					elseif($rList['matauang']=='EUR') 
					{
						$hrg=$rList['hargasatuan']*12643;
						//$ppnDlr=$rList['ppn']*12643;
						$totHrg=$rList['jumlahpesan']*$hrg;
						//$rList['matauang']="IDR";
					}
					elseif(($rList['matauang']=='')||($rList['matauang']=='NULL'))
					{
						$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
					}
				}
			}
			
			else
			{
				$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
			}
			//$totHrg=$rList['jumlahpesan']*$rList['hargasatuan'];
			$grandTot['total']+=$totHrg;
			if($rList['nopp']!="")
			{
				$sTgl="select tanggal from ".$dbname.".log_prapoht where nopp='".$rList['nopp']."'";
				$qTgl=mysql_query($sTgl) or die(mysql_error());
				$rTgl=mysql_fetch_assoc($qTgl);
				
				if(($rTgl['tanggal']!="")||($rTgl['tanggal']!="000-00-00"))
				{
						$tglPP=tanggalnormal($rTgl['tanggal']);		
				}
				else
				{
					$tglPP="";
				}
			}
			else
			{
				$tglPP="";
			}
			if($rList['nopo']!="")
			{
				$sTgl2="select tanggal from ".$dbname.".log_transaksiht where nopo='".$rList['nopo']."' and tipetransaksi=1";
				$qTgl2=mysql_query($sTgl2) or die(mysql_error());
				$rTgl2=mysql_fetch_assoc($qTgl2);
				if($rTgl2['tanggal']!="")
				{
						$tglBapb=tanggalnormal($rTgl2['tanggal']);		
				}
				else
				{
					$tglBapb="";
				}
			}
			else
			{
				$tglBapb="";
			}
			
			$tab.="<tr class='rowcontent'>";
			if($afdC==false) {
				$tab .= "<td>".$no."</td>";
				$tab .= "<td value='".$dt['kodesupplier']."'>".$rNm['namasupplier']."</td>";
				$afdC = true;
			} else {
				if($blankC==false) {
					$tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
					$tab .= "<td  rowspan='".$tmpRow."'>&nbsp;</td>";
					$blankC = true;
				}
			}	
			$tab.="<td>".$rList['nopo']."</td>";
			$tab.="<td>".tanggalnormal($rList['tanggal'])."</td>";
			$tab.="<td>".$rList['kodebarang']."</td>";
			$tab.="<td>".$rBrg['namabarang']."</td>";
			$tab.="<td align=center>".$rList['matauang']."</td>";
			$tab.="<td align=right>".$rList['jumlahpesan']."</td>";
			$tab.="<td align=center>".$rList['satuan']."</td>";
			$tab.="<td align=right>".number_format($totHrg,2)."</td>";
			$tab.="<td>".$tglPP."</td>";
			$tab.="<td>".$tglBapb."</td>";
			$tab.="</tr>";
		}
		$totalAll['totalSemua']+=$grandTot['total'];
		$tab .= "<tr class='rowcontent'>";
		$tab .= "<td colspan='9' align='right'><b>Sub Total </b></td>";
		$tab .= "<td align=right>".number_format($grandTot['total'],2)."</td>";
		$tab .= "<td colspan='2' >&nbsp;</td>";
		$tab .= "</tr>";
	}
		$tab.="<tr><td colspan=9>Grand Total</td><td>".number_format($totalAll['totalSemua'])."</td><td colspan=2>&nbsp;</td></tr>";
		//$tab.="<tr><td colspan=12 align=right>".terbilang($totalAll['totalSemua'])."</td></tr>";
	
			
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="</table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			$dte=date("YmdHms");
			$nop_="Laporan_Pembelian_".$dte;
                         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                         gzwrite($gztralala, $tab);
                         gzclose($gztralala);
                         echo "<script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls.gz';
                            </script>";
			
	break;
        case'getTgl':
	if($periode!='')
	{
		$tgl=$periode;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
	elseif($period!='')
	{
		$tgl=$period;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
        if($kdUnit=='')
        {
            $kdUnit=$_SESSION['lang']['lokasitugas'];
        }
	$sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit,0,4)."' and periode='".$tanggal."' ";
	//echo"warning".$sTgl;
	$qTgl=mysql_query($sTgl) or die(mysql_error());
	$rTgl=mysql_fetch_assoc($qTgl);
	echo tanggalnormal($rTgl['tanggalmulai'])."###".tanggalnormal($rTgl['tanggalsampai']);
	break;
	
	default:
	break;
}
?>
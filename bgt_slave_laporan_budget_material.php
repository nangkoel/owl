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
$sKlmpk="select kode,kelompok from ".$dbname.".log_5klbarang order by kode";
$qKlmpk=mysql_query($sKlmpk) or die(mysql_error());
while($rKlmpk=mysql_fetch_assoc($qKlmpk))
{
    $rKelompok[$rKlmpk['kode']]=$rKlmpk['kelompok'];
}
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmBarang=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSatuan=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$arrBln=array("1"=>$_SESSION['lang']['jan'],"2"=>$_SESSION['lang']['peb'],"3"=>$_SESSION['lang']['mar'],"4"=>$_SESSION['lang']['apr'],"5"=>$_SESSION['lang']['mei'],"6"=>$_SESSION['lang']['jun']
             ,"7"=>$_SESSION['lang']['jul'],"8"=>$_SESSION['lang']['agt'],"9"=>$_SESSION['lang']['sep'],"10"=>$_SESSION['lang']['okt'],"11"=>$_SESSION['lang']['nov'],"12"=>$_SESSION['lang']['dec']);
$_POST['kdPt']==''?$kdPt=$_GET['kdPt']:$kdPt=$_POST['kdPt'];
$_POST['thnBudget']==''?$thnBudget=$_GET['thnBudget']:$thnBudget=$_POST['thnBudget'];
$_POST['kdBudget']==''?$kdBudget=$_GET['kdBudget']:$kdBudget=$_POST['kdBudget'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['pilMode']==''?$pilMode=$_GET['pilMode']:$pilMode=$_POST['pilMode'];
//get data kod budget barang
$data=array();
$unitId=$_SESSION['lang']['all'];
$nmPrshn="Holding";
$sData="select distinct substr(kodebudget,3,3) as kelompokbarang from ".$dbname.".bgt_budget_detail where kodebudget like 'M%' order by substr(kodebudget,3,3) asc";
$qData=mysql_query($sData) or die(mysql_error());
while($rData=mysql_fetch_assoc($qData))
{  
   $data[]=$rData;
}
if($thnBudget!='')
{
    $where=" and tahunbudget='".$thnBudget."'";
}
else
{
    exit("Error: ".$_SESSION['lang']['budgetyear']." tidak boleh kosong");
}

if($kdPt!=''&&$kdUnit=='')
{
        $where.= " and substr(kodeorg,1,4) in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$kdPt."')";
        $nmPrshn=$optNmOrg[$kdPt];
}
if($kdUnit!='')
{
        $where.=" and substr(kodeorg,1,4)='".$kdUnit."'";
        $unitId=$optNmOrg[$kdUnit];
}
if($kdBudget!="")
{
        $where.=" and substr(kodebudget,3,3)='".$kdBudget."'";
}
$brdr=0;
$bgcoloraja='';
$arrPilMode=array("0"=>$_SESSION['lang']['fisik'],"1"=>$_SESSION['lang']['rp']);
if($proses=='excel')
{
    //exit("error:".$arrPilMode[$pilMode]."__".$pilMode);
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=17 align=left><b><font size=5>".$_SESSION['lang']['bgtMaterial']."</font></b></td></tr>
    <tr><td colspan=17 align=left>".$_SESSION['lang']['pt']." : ".$nmPrshn."</td></tr>
    <tr><td colspan=17 align=left>".$_SESSION['lang']['unit']." : ".$unitId."</td></tr>
    <tr><td colspan=17 align=left>".$_SESSION['lang']['budgetyear']." : ".$thnBudget."</td></tr>
    <tr><td colspan=17 align=left>".$_SESSION['lang']['pilih']." : ".$arrPilMode[$pilMode]."</td></tr>
    </table>";
}
if($pilMode=='0')
{
$sListData="select distinct tahunbudget,substr(kodeorg,1,4) as kodeorg,substr(kodebudget,3,3) as kelompokbarang,kodebarang,sum(fis01) as fis1,sum(fis02) as fis2,
            sum(fis03) as fis3,sum(fis04) as fis4,sum(fis05) as fis5,sum(fis06) as fis6,sum(fis07) as fis7,sum(fis08) as fis8,sum(fis09) as fis9,
            sum(fis10) as fis10,sum(fis11) as fis11,sum(fis12) as fis12,sum(jumlah) as jmlhStaon,satuanj from ".$dbname.".bgt_budget_detail where substr(kodebudget,1,1)='M' ".$where."
            group by kodebarang";
}
else
{
    $sListData="select distinct tahunbudget,substr(kodeorg,1,4) as kodeorg,substr(kodebudget,3,3) as kelompokbarang,kodebarang,sum(rp01) as fis1,sum(rp02) as fis2,
            sum(rp03) as fis3,sum(rp04) as fis4,sum(rp05) as fis5,sum(rp06) as fis6,sum(rp07) as fis7,sum(rp08) as fis8,sum(rp09) as fis9,
            sum(rp10) as fis10,sum(rp11) as fis11,sum(rp12) as fis12,sum(rupiah) as jmlhStaon,satuanj from ".$dbname.".bgt_budget_detail where substr(kodebudget,1,1)='M' ".$where."
            group by kodebarang";
}
//echo $sListData;
$qListData=mysql_query($sListData) or die(mysql_error());
while($rListData=mysql_fetch_assoc($qListData))
{
    $jmlhSetaon[$rListData['tahunbudget']][$rListData['kodebarang']]+=$rListData['jmlhStaon'];
    $listSatuan[$rListData['tahunbudget']][$rListData['kelompokbarang']][$rListData['kodebarang']]=$rListData['satuanj'];
    $listKodebarang[]=$rListData['kodebarang'];
    for($ard=1;$ard<13;$ard++)
    {
        $jmlhFis[$rListData['tahunbudget']][$rListData['kodebarang']][$ard]=$rListData['fis'.$ard];
    }
}
$cekData=count($listKodebarang);
if($cekData==0)
{
    exit("Error:data kosong");
}
	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
		<td ".$bgcoloraja.">No.</td>
		<td ".$bgcoloraja.">".$_SESSION['lang']['kodebarang']."</td>
		<td ".$bgcoloraja.">".$_SESSION['lang']['namabarang']."</td>
		<td ".$bgcoloraja.">".$_SESSION['lang']['satuan']."</td>
		<td ".$bgcoloraja.">".$_SESSION['lang']['jumlahsetahun']."</td>";
	foreach($arrBln as $listBulan)	
        {
            $tab.="<td ".$bgcoloraja.">".$listBulan."</td>";
        }
	$tab.="</tr>
	</thead>
	<tbody>";

            foreach($listKodebarang as $daftarKodebarang)
            {
     
                if($klmpkBarang!=substr($daftarKodebarang,0,3))
                 {       
                     $brs=1; 
                 }
                if($brs==1) 
                {
                    $no+=1;
                    $klmpkBarang=substr($daftarKodebarang,0,3);
                    $tab.="<tr class='rowcontent'>";
                    $tab.="<td>".$no."</td><td><b>".$klmpkBarang."</b></td><td colspan=3><b>".$rKelompok[$klmpkBarang]."</b></td>";
                    $tab.="<td colspan=12>&nbsp;</td>";
                    $tab.="</tr>";
                    $brs=0;
                 }
                $tab.="<tr class='rowcontent'>";
                $tab.="<td>&nbsp;</td>";
                $tab.="<td>".$daftarKodebarang."</td>";
                $tab.="<td>".$optNmBarang[$daftarKodebarang]."</td>";
                $tab.="<td>".$optSatuan[$daftarKodebarang]."</td>";
                $tab.="<td align=right>".number_format($jmlhSetaon[$thnBudget][$daftarKodebarang],2)."</td>";
                for($ard=1;$ard<=12;$ard++)
                {
                  $tab.="<td align=right>".number_format($jmlhFis[$thnBudget][$daftarKodebarang][$ard],2)."</td>";
                }
                $tab.="</tr>";
            }
            


        $tab.="</tbody></table>";
switch($proses)
{
	case'getKdorg':
	//echo "warning:masuk";
	$optorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$kdPt."'";
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optorg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	}
	echo $optorg;
	break;
	case'preview':
	echo $tab;
	break;
    
    case'excel':

        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("YmdHms");
        $nop_="budgetMaterial".$dte;
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
		$sData="select a.kodesupplier from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.statuspo>1 ".$where." group by kodesupplier order by a.tanggal asc";
	$qData=mysql_query($sData) or die(mysql_error());
	while($rData=mysql_fetch_assoc($qData))
	{
		$isi[]=$rData;
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
		
		$sList="select distinct a.tanggal,a.matauang,b.kodebarang,b.satuan,b.nopo,b.jumlahpesan,b.nopp,b.hargasatuan from ".$dbname.".log_poht a left join ".$dbname.".log_podt b on a.nopo=b.nopo where a.kodesupplier='".$dt['kodesupplier']."' and b.nopo!='NULL' and a.tanggal between '".$tglDari."' and '".$tanggalSampai."'";
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
	
	
	default:
	break;
}
?>
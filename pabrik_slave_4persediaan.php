<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];

$_POST['kdPbrik']==''?$kdPbrik=$_GET['kdPbrik']:$kdPbrik=$_POST['kdPbrik'];
$_POST['kdTangki']==''?$kdTangki=$_GET['kdTangki']:$kdTangki=$_POST['kdTangki'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$periode==''?$tampilperiode='Seluruhnya':$tampilperiode=$periode;

	if($kdPbrik!='')
	{
		$where="kodeorg='".$kdPbrik."'";
		if($periode!='')
		{
		$where.=" and tanggal like '".$periode."%'";
		}
		if($kdTangki!='')
		{
		$where.=" and kodetangki='".$kdTangki."'";
		}
	}
if($kdPbrik=='')
{
    echo"warning:Pabrik Tidak Boleh Kosong";
    exit();
}



#tinggi

$aMax="select volume from ".$dbname.".pabrik_5vtangki where kodetangki='".$kdTangki."' order by volume desc";
$bMax=mysql_query($aMax) or die (mysql_error($conn));
$cMax=mysql_fetch_assoc($bMax);
	//echo $cMax['volume'];


#selesai







    $sql="select * from ".$dbname.".pabrik_masukkeluartangki where ".$where." 
        order by tanggal desc";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
        // data hari ini
        $cpokua2=$res['kuantitas'];
        $cporen2=$res['cporendemen'];
        $cpoffa2=$res['cpoffa'];
        $cpokai2=$res['cpokdair'];
        $cpokko2=$res['cpokdkot'];
        
        $kerkua2=$res['kernelquantity'];
        $kerren2=$res['kernelrendemen'];
        $kerffa2=$res['kernelffa'];
        $kerkai2=$res['kernelkdair'];
        $kerkko2=$res['kernelkdkot'];
        
        $tanggal2=$res['tanggal'];
        
        if($tanggal1!=$tanggal2){ // beda tanggal, nolin
            $cpokua1=0;
            $cporen1=0;
            $cpoffa1=0;
            $cpokai1=0;
            $cpokko1=0;

            $kerkua1=0;
            $kerren1=0;
            $kerffa1=0;
            $kerkai1=0;
            $kerkko1=0;

            $tanggal1='';
        }
        
        // total
        $cpokua3=$cpokua1+$cpokua2;
        $kerkua3=$kerkua1+$kerkua2;
        
        // bagi2an cpo
        @$cporen31=$cpokua1/$cpokua3*$cporen1;
        @$cporen32=$cpokua2/$cpokua3*$cporen2;
        $cporen3=$cporen31+$cporen32;
        @$cpoffa31=$cpokua1/$cpokua3*$cpoffa1;
        @$cpoffa32=$cpokua2/$cpokua3*$cpoffa2;
        $cpoffa3=$cpoffa31+$cpoffa32;
        @$cpokai31=$cpokua1/$cpokua3*$cpokai1;
        @$cpokai32=$cpokua2/$cpokua3*$cpokai2;
        $cpokai3=$cpokai31+$cpokai32;
        @$cpokko31=$cpokua1/$cpokua3*$cpokko1;
        @$cpokko32=$cpokua2/$cpokua3*$cpokko2;
        $cpokko3=$cpokko31+$cpokko32;
        
        // bagi2an kernel
        @$kerren31=$kerkua1/$kerkua3*$kerren1;
        @$kerren32=$kerkua2/$kerkua3*$kerren2;
        $kerren3=$kerren31+$kerren32;
        @$kerffa31=$kerkua1/$kerkua3*$kerffa1;
        @$kerffa32=$kerkua2/$kerkua3*$kerffa2;
        $kerffa3=$kerffa31+$kerffa32;
        @$kerkai31=$kerkua1/$kerkua3*$kerkai1;
        @$kerkai32=$kerkua2/$kerkua3*$kerkai2;
        $kerkai3=$kerkai31+$kerkai32;
        @$kerkko31=$kerkua1/$kerkua3*$kerkko1;
        @$kerkko32=$kerkua2/$kerkua3*$kerkko2;
        $kerkko3=$kerkko31+$kerkko32;

        if($kdTangki=='')$tangcity=$_SESSION['lang']['all']; else $tangcity=$kdTangki;
        
        $tanger[$res['tanggal']]=$res['tanggal'];
        $tanker[$res['tanggal']]['kodorg']=$res['kodeorg'];
        $tanker[$res['tanggal']]['tangga']=$res['tanggal'];
        $tanker[$res['tanggal']]['kotang']=$tangcity;
        $tanker[$res['tanggal']]['cpokua']+=$res['kuantitas'];
        $tanker[$res['tanggal']]['cporen']=$cporen3;
        $tanker[$res['tanggal']]['cpoffa']=$cpoffa3;
        $tanker[$res['tanggal']]['cpokai']=$cpokai3;
        $tanker[$res['tanggal']]['cpokko']=$cpokko3;
        $tanker[$res['tanggal']]['kerkua']+=$res['kernelquantity'];
        $tanker[$res['tanggal']]['kerren']=$kerren3;
        $tanker[$res['tanggal']]['kerffa']=$kerffa3;
        $tanker[$res['tanggal']]['kerkai']=$kerkai3;
        $tanker[$res['tanggal']]['kerkko']=$kerkko3;

        if($tanggal1!=$tanggal2){
            $cpokua1=$res['kuantitas'];
            $cporen1=$res['cporendemen'];
            $cpoffa1=$res['cpoffa'];
            $cpokai1=$res['cpokai'];
            $cpokko1=$res['cpokko'];
            
            $kerkua1=$res['kernelquantity'];
            $kerren1=$res['kernelrendemen'];
            $kerffa1=$res['kernelffa'];
            $kerkai1=$res['kernelkdair'];
            $kerkko1=$res['kernelkdkot'];

            $tanggal1=$res['tanggal'];
        }else{ // kalo tanggal ketiga masih sama, bawa data ke tanggal ketiga
            $cpokua1=$cpokua3;
            $cporen1=$cporen3;
            $cpoffa1=$cpoffa3;
            $cpokai1=$cpokai3;
            $cpokko1=$cpokko3;
            
            $kerkua1=$kerkua3;
            $kerren1=$kerren3;
            $kerffa1=$kerffa3;
            $kerkai1=$kerkai3;
            $kerkko1=$kerkko3;
        }
    }
            


switch($proses)
{
	case'preview':
            //<td align=right>".$_SESSION['lang']['cporendemen']." (%)</td>
            //<td align=right>".$_SESSION['lang']['kernelrendemen']." (%)</td>
            
		echo"<table class=sortable cellspacing=1 border=0>
		<thead><tr class=rowheader>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['kodetangki']."</td>
		
		<td align=center>".$_SESSION['lang']['max']."<br />Kg</td>
		
		
		<td align=right>".$_SESSION['lang']['cpokuantitas']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['cpoffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdkot']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelquantity']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['kernelffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdkot']." (%)</td>
		</tr></thead><tbody>";
		
	
	

if(!empty($tanger))
foreach($tanger as $tgl){
		echo"<tr class=rowcontent>
		<td>".$tanker[$tgl]['kodorg']."</td>
		<td>".tanggalnormal($tgl)."</td>
		<td>".$tanker[$tgl]['kotang']."</td>
		
		<td>".number_format($cMax['volume'])."</td>
		
		
		<td align=right>".number_format($tanker[$tgl]['cpokua'],0)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpoffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokko'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkua'],0)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkko'],2)."</td>
		</tr>
		";    
}            
	echo"</tbody></table>";
	break;
	case'pdf':
	$periode=$_GET['periode'];
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdPbrik;
				global $kdTangki;
				global $periode;
				global $tampilperiode;
				
				$sql="select nokontrak,kodebarang,tanggalkontrak,koderekanan,tanggalkirim,sdtanggal,kuantitaskontrak,kodept from ".$dbname.".pmn_kontrakjual where tanggalkontrak like '%".$periode."%'";
				$query=mysql_query($sql) or die(mysql_error());
				$res=mysql_fetch_assoc($query);
				
                $tkdOperasi=$res['jlhharitdkoperasi'];
				$jmlhHariOperasi=$res['jlhharioperasi'];
				$meter=$res['merterperhari'];
				$kdOrg=$res['orgdata'];
				
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$res['kodept']."'");
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
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanstok']." ".$kdPbrik." ".$kdTangki,'',0,'L');
				$this->Ln();
				$this->SetFont('Arial','',8);
				$this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(45/100*$width,$height,$tampilperiode,'',0,'L');
			
              
				
				$this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height, $_SESSION['lang']['laporanstok'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','',8);	
                $this->SetFillColor(220,220,220);
				
				$this->Cell(3/100*$width,$height,"No.",1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['kodetangki'],1,0,'C',1);	
				$this->Cell(10/100*$width,$height,"Max (KG)",1,0,'C',1);		
				$this->Cell(10/100*$width,$height,"CPO - qty (KG)",1,0,'C',1);		
				//$this->Cell(7/100*$width,$height,"CPO - rend",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"CPO - FFA",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"CPO - kd.Air",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"CPO - kd.Kot",1,0,'C',1);		
				$this->Cell(10/100*$width,$height,"Kernel - qty (KG)",1,0,'C',1);		
				//$this->Cell(7/100*$width,$height,"Kernel - rend",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"Kernel - FFA",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"Kernel - kd.Air",1,0,'C',1);		
				$this->Cell(7/100*$width,$height,"Kernel - kd.Kot",1,1,'C',1);		
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
        $height = 10;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
                
if(!empty($tanger))foreach($tanger as $tgl){
			$no+=1;
				$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
				$pdf->Cell(10/100*$width,$height,tanggalnormal($tgl),1,0,'C',1);	
				$pdf->Cell(8/100*$width,$height,$tanker[$tgl]['kotang'],1,0,'C',1);	
				$pdf->Cell(10/100*$width,$height,number_format($cMax['volume'],0),1,0,'R',1);	
				$pdf->Cell(10/100*$width,$height,number_format($tanker[$tgl]['cpokua'],0),1,0,'R',1);		
				//$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['cporen'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['cpoffa'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['cpokai'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['cpokko'],2),1,0,'R',1);		
				$pdf->Cell(10/100*$width,$height,number_format($tanker[$tgl]['kerkua'],0),1,0,'R',1);		
				//$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['kerren'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['kerffa'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['kerkai'],2),1,0,'R',1);		
				$pdf->Cell(7/100*$width,$height,number_format($tanker[$tgl]['kerkko'],2),1,1,'R',1);		

			
//		echo"<tr class=rowcontent>
//		<td>".$tanker[$tgl]['kodorg']."</td>
//		<td>".tanggalnormal($tgl)."</td>
//		<td>".$tanker[$tgl]['kotang']."</td>
//		<td align=right>".number_format($tanker[$tgl]['cpokua'],0)."</td>
//		<td align=right>".number_format($tanker[$tgl]['cporen'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['cpoffa'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['cpokai'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['cpokko'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['kerkua'],0)."</td>
//		<td align=right>".number_format($tanker[$tgl]['kerren'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['kerffa'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['kerkai'],2)."</td>
//		<td align=right>".number_format($tanker[$tgl]['kerkko'],2)."</td>
//		</tr>
//		";    
}            
                
//	$sql="select * from ".$dbname.".pabrik_masukkeluartangki where ".$where."";
//	
//	$query=mysql_query($sql) or die(mysql_error());
//	while($res=mysql_fetch_assoc($query))
//		{
//			$no+=1;
//				$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
//				$pdf->Cell(10/100*$width,$height,tanggalnormal($res['tanggal']),1,0,'C',1);	
//				$pdf->Cell(8/100*$width,$height,$res['kodetangki'],1,0,'C',1);	
//				$pdf->Cell(10/100*$width,$height,number_format($res['kuantitas'],0),1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['cporendemen'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['cpoffa'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['cpokdair'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['cpokdkot'],1,0,'R',1);		
//				$pdf->Cell(10/100*$width,$height,number_format($res['kernelquantity'],0),1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['kernelrendemen'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['kernelffa'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['kernelkdair'],1,0,'R',1);		
//				$pdf->Cell(7/100*$width,$height,$res['kernelkdkot'],1,1,'R',1);		
//
//			
//		}
			
        $pdf->Output();
	break;
	case'excel':
            //<td align=right>".$_SESSION['lang']['cporendemen']." (%)</td>
            //<td align=right>".$_SESSION['lang']['kernelrendemen']." (%)</td>
	$periode=$_GET['periode'];
			$stream.="
			<table>
			<tr><td>".$_SESSION['lang']['laporanstok']." ".$kdPbrik." ".$kdTangki."</td></tr>
			<tr><td>".$_SESSION['lang']['periode']."</td><td>".$tampilperiode."</td></tr>
			<tr></tr>
			</table>
			<table border=1>
			<tr bgcolor=#DEDEDE>
			
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['kodetangki']."</td>
		<td>".$_SESSION['lang']['max']." Kg</td>
		<td align=right>".$_SESSION['lang']['cpokuantitas']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['cpoffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['cpokdkot']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelquantity']." (KG)</td>
		
		<td align=right>".$_SESSION['lang']['kernelffa']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdair']." (%)</td>
		<td align=right>".$_SESSION['lang']['kernelkdkot']." (%)</td>
			
			
			
			</tr>";

if(!empty($tanger))foreach($tanger as $tgl){
		$stream.="<tr class=rowcontent>
		<td>".$tanker[$tgl]['kodorg']."</td>
		<td>".$tgl."</td>
		<td>".$tanker[$tgl]['kotang']."</td>
		
		<td>".number_format($cMax['volume'])."</td>
		
		<td align=right>".number_format($tanker[$tgl]['cpokua'],0)."</td>
		
		<td align=right>".number_format($tanker[$tgl]['cpoffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['cpokko'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkua'],0)."</td>
		
		<td align=right>".number_format($tanker[$tgl]['kerffa'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkai'],2)."</td>
		<td align=right>".number_format($tanker[$tgl]['kerkko'],2)."</td>
		</tr>
		";    
}                                    
                        
//	$sql="select * from ".$dbname.".pabrik_masukkeluartangki where ".$where."";
//	$query=mysql_query($sql) or die(mysql_error());
//	$row=mysql_fetch_row($query);
//	if($row<1)
//	{
//		$stream.="<tr class=rowcontent>
//		<td colspan=8 align=center>Not Avaliable</td></tr>
//		";
//	}
//	$query=mysql_query($sql) or die(mysql_error());
//	while($res=mysql_fetch_assoc($query))
//	{
//		$stream.="<tr class=rowcontent>
//		<td>".$res['kodeorg']."</td>
//		<td>".$res['tanggal']."</td>
//		<td>".$res['kodetangki']."</td>
//		<td align=right>".number_format($res['kuantitas'],0)."</td>
//		<td align=right>".$res['cporendemen']."</td>
//		<td align=right>".$res['cpoffa']."</td>
//		<td align=right>".$res['cpokdair']."</td>
//		<td align=right>".$res['cpokdkot']."</td>
//		<td align=right>".number_format($res['kernelquantity'],0)."</td>
//		<td align=right>".$res['kernelrendemen']."</td>
//		<td align=right>".$res['kernelffa']."</td>
//		<td align=right>".$res['kernelkdair']."</td>
//		<td align=right>".$res['kernelkdkot']."</td>
//		</tr>";
//
//	}
			//echo "warning:".$strx;
			//=================================================
			$stream.="</table>";
						$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="Laporan Stok-".$kdPbrik.$periode.$kdTangki;
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

	case'getTangki':
	$sGet="select kodetangki,keterangan from ".$dbname.".pabrik_5tangki where kodeorg='".$kdPbrik."'";
	$qGet=mysql_query($sGet) or die(mysql_error());
		$optTangki.="<option value=''>".$_SESSION['lang']['all']."</option>";
	while($rGet=mysql_fetch_assoc($qGet))
	{
		$optTangki.="<option value=".$rGet['kodetangki'].">".$rGet['keterangan']."</option>";
	}
	echo $optTangki;
	break;
	default:
	break;
}

?>
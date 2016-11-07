<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');


$proses=$_GET['proses'];
$tahun=$_POST['thn'];
$kdVhc=$_POST['kdVhc'];


switch($proses)
{
	case'preview':
	echo"<table cellspacing=1 border=0>
	<thead>
	<tr><td>".$_SESSION['lang']['anggaranTraksiDetail']."</td><td>".$_SESSION['lang']['anggaranTraksiAlokasi']."</td></tr></thead><tbody>";
	echo"<td valign=top><table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['namabarang']."</td>
		<td>".$_SESSION['lang']['jumlah']."</td>
		</tr></thead><tbody id=containDetailTraksi>";
		$str="select * from ".$dbname.".keu_anggaranvhcdt where tahun='".$tahun."' and kodevhc='".$kdVhc."'  order by `tahun` desc";
		//echo $str;
		if($res=mysql_query($str))
		{
			while($bar=mysql_fetch_assoc($res))
			{
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar['kodebarang']."'";
			$qBrg=mysql_query($sBrg) or die(mysql_error());
			$rBrg=mysql_fetch_assoc($qBrg);
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$rBrg['namabarang']."</td>
			<td>".number_format($bar['jumlah'],2)."</td></tr>";
			}	
		}
	echo"</tbody></table></td>";
	echo"<td valign=top>  <table class=sortable cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$_SESSION['lang']['jmlhMeter']."</td>
		<td>Jan</td>
		<td>Feb</td>
		<td>Mar</td>
		<td>Apr</td>
		<td>Mei</td>
		<td>Jun</td>
		<td>Jul</td>
		<td>Aug</td>
		<td>Sep</td>
		<td>Okt</td>
		<td>Nov</td>
		<td>Des</td>
		</tr></thead><tbody id=containAlokasi>";
		$sql="select * from ".$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'  order by `tahun` desc ";
		//echo $sql;
		if($query=mysql_query($sql))
		{
			while($bar=mysql_fetch_assoc($query))
			{
				$nor+=1;
			echo"
			<tr class=rowcontent>
			<td>".$nor."</td>
			<td>".$bar['kodeorg']."</td>
			<td>".$bar['jlhmeter']."</td>
			<td>".$bar['jan']."</td>
			<td>".$bar['feb']."</td>
			<td>".$bar['mar']."</td>
			<td>".$bar['apr']."</td>
			<td>".$bar['mei']."</td>
			<td>".$bar['jun']."</td>
			<td>".$bar['jul']."</td>
			<td>".$bar['agu']."</td>
			<td>".$bar['sep']."</td>
			<td>".$bar['okt']."</td>
			<td>".$bar['nov']."</td>
			<td>".$bar['des']."</td>
			</tr>";
			}	
		}
	
	echo"</tbody></table></td>";
	echo"</tbody></table>";
	break;
	case'pdf':
	$tahun=$_GET['thn'];
	$kdVhc=$_GET['kdVhc'];
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $tahun;
				global $kdVhc;
				global $kdOrg;
				global $tkdOperasi;
				global $jmlhHariOperasi;
				global $meter;
				
				
				$sql="select * from ".$dbname.".keu_anggaranvhcht where kodevhc='".$kdVhc."' and tahun='".$tahun."'";
				$query=mysql_query($sql) or die(mysql_error());
				$res=mysql_fetch_assoc($query);
				
                $tkdOperasi=$res['jlhharitdkoperasi'];
				$jmlhHariOperasi=$res['jlhharioperasi'];
				$meter=$res['merterperhari'];
				$kdOrg=$res['orgdata'];
				
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
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
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['anggaranTraksi'],'',0,'L');
				$this->Ln();
				 $this->SetFont('Arial','',8);
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tahunanggaran'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(45/100*$width,$height,$kdVhc,'',0,'L');
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['jmlhHariOperasi'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(15/100*$width,$height,$jmlhHariOperasi,0,0,'L');
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['pemakaianHmKm'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(45/100*$width,$height,$meter,'',0,'L');
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['jmlhHariTdkOpr'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(15/100*$width,$height,$tkdOperasi,0,0,'L');
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(45/100*$width,$height,date('d-m-Y H:i:s'),'',0,'L');
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(15/100*$width,$height,$_SESSION['standard']['username'],0,0,'L');
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(45/100*$width,$height,$kdOrg,'',0,'L');
              
				
                $this->Ln();
				$this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$_SESSION['lang']['anggaranTraksiDetail'],0,1,'C');	
                $this->Ln();	
				
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                /*foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }*/
				
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(25/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);	
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);						
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['grnd_total'],1,1,'C',1);
            
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
		$pdf->SetFont('Arial','',9);
		$sDet="select * from ".$dbname.".keu_anggaranvhcdt where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
		$qDet=mysql_query($sDet) or die(mysql_error());
		while($rDet=mysql_fetch_assoc($qDet))
		{
			$no+=1;
			$sCust="select namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang='".$rDet['kodebarang']."'";
			$qCust=mysql_query($sCust) or die(mysql_error($conn));
			$rCust=mysql_fetch_assoc($qCust);
			
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(25/100*$width,$height,$rCust['namabarang'],1,0,'L',1);	
			$pdf->Cell(8/100*$width,$height,number_format($rDet['jumlah'],2)." ".$rCust['satuan'],1,0,'R',1);						
			$pdf->Cell(10/100*$width,$height,number_format($rDet['hargatotal'],2),1,1,'R',1);
		}
		$pdf->Ln();
		$pdf->SetFont('Arial','U',12);
		$pdf->Cell($width,$height,$_SESSION['lang']['anggaranTraksiAlokasi'],0,1,'C');	
		$pdf->Ln();	
		
		$pdf->SetFont('Arial','B',9);	
		$pdf->SetFillColor(220,220,220);
		$pdf->Cell(3/100*$width,$height,'No',1,0,'C',1);
		$pdf->Cell(12/100*$width,$height,$_SESSION['lang']['kodeorg'],1,0,'C',1);	
		$pdf->Cell(15/100*$width,$height,$_SESSION['lang']['jmlhMeter'],1,0,'C',1);						
		$pdf->Cell(5/100*$width,$height,"Jan",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"FEB",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"MAR",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"APR",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"MEI",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"JUN",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"JUL",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"AGU",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"SEP",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"OKT",1,0,'C',1);
		$pdf->Cell(5/100*$width,$height,"NOV",1,0,'C',1);						
		$pdf->Cell(5/100*$width,$height,"Des",1,1,'C',1);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);	
		$sDetBuged="select * from ".$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
		$qDetBudged=mysql_query($sDetBuged) or die(mysql_error());
		while($rDetBugdeg=mysql_fetch_assoc($qDetBudged))
		{
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(12/100*$width,$height,$rDetBugdeg['kodeorg'],1,0,'C',1);	
			$pdf->Cell(15/100*$width,$height,$rDetBugdeg['jlhmeter'],1,0,'C',1);						
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['jan'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['feb'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['mar'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['apr'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['mei'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['jun'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['jul'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['agu'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['sep'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['okt'],1,0,'C',1);
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['nov'],1,0,'C',1);						
			$pdf->Cell(5/100*$width,$height,$rDetBugdeg['des'],1,1,'C',1);
		}
	
        $pdf->Output();
	break;
	case'excel':
	$tahun=$_GET['thn'];
	$kdVhc=$_GET['kdVhc'];
			$sHeader="select * from ".$dbname.".keu_anggaranvhcht where tahun='".$tahun."' and kodevhc ='".$kdVhc."'";
			$qHeader=mysql_query($sHeader) or die(mysql_error());
			$rHeader=mysql_fetch_assoc($qHeader);
			$strx="select * from ".$dbname.".keu_anggaranvhcdt a  where a.tahun='".$tahun."' and a.kodevhc='".$kdVhc."' order by a.tahun desc";
			//echo"warning:".$strx;exit();       
			/*$tkdOperasi=$res['jlhharitdkoperasi'];
				$jmlhHariOperasi=$res['jlhharioperasi'];
				$meter=$res['merterperhari'];
				$kdOrg=$res['orgdata'];*/
			$stream.="
			<table>
			<tr><td colspan=15 align=center>".$_SESSION['lang']['anggaranTraksi']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['tahunanggaran']."</td><td>".$tahun."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['jmlhHariOperasi']."</td><td>".$rHeader['jlhharioperasi']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['pemakaianHmKm']."</td><td>".$rHeader['jlhharioperasi']."</td></tr>
			<tr><td colspan=3>".$_SESSION['lang']['jmlhHariTdkOpr']."</td><td>".$rHeader['merterperhari']."</td></tr>
			<tr><td colspan=3></td><td></td></tr>
			</table>
			<table border=1>
			<tr><td colspan=5 align=center>".$_SESSION['lang']['anggaranTraksiDetail']."</td></tr>
			<tr>
				<td bgcolor=#DEDEDE align=center>No.</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['grnd_total']."</td>		
			</tr>";
			
			$resx=mysql_query($strx);
			$row=mysql_fetch_row($resx);
			if($row<1)
			{
			$stream.="	<tr class=rowcontent>
			<td colspan=5 align=center>Not Avaliable</td></tr>
			";
			}
			else
			{
			$no=0;
			$resx=mysql_query($strx);
				while($barx=mysql_fetch_assoc($resx))
				{
				$no+=1;
				$sKdBrg="select  a.namabarang,b.hargasatuan from ".$dbname.".log_5masterbarang a inner join  ".$dbname.".log_5masterbaranganggaran b on a.kodebarang=b.kodebarang where a.kodebarang='".$barx['kodebarang']."'";//echo $skdBrg;		
				$qKdBrg=mysql_query($sKdBrg) or die(mysql_error());
				$rKdBrg=mysql_fetch_assoc($qKdBrg);
				$stream.="	<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$rKdBrg['namabarang']."</td>
				<td>".number_format($barx['jumlah'],2)."</td>
				<td>"."Rp. ".number_format($rKdBrg['hargasatuan'],2)."</td>
				<td>"."Rp. ".number_format($barx['hargatotal'],2)."</td>	
				</tr>";
				}
			}
			
			//echo "warning:".$strx;
			//=================================================
			$stream.="</table>";
			$sql="select * from ".$dbname.".keu_anggaranalokasivhc where tahun='".$tahun."' and kodevhc='".$kdVhc."'";
			$stream.="
			<br />
			<table border=1>
			<tr>
				<td bgcolor=#DEDEDE align=center>No.</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeorg']."</td>
				<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jmlhMeter']."</td>
				<td bgcolor=#DEDEDE align=center>Jan</td>
				<td bgcolor=#DEDEDE align=center>FEB</td>		
				<td bgcolor=#DEDEDE align=center>MAR</td>		
				<td bgcolor=#DEDEDE align=center>APR</td>		
				<td bgcolor=#DEDEDE align=center>MEI</td>		
				<td bgcolor=#DEDEDE align=center>JUN</td>		
				<td bgcolor=#DEDEDE align=center>JUL</td>		
				<td bgcolor=#DEDEDE align=center>AGU</td>		
				<td bgcolor=#DEDEDE align=center>SEP</td>		
				<td bgcolor=#DEDEDE align=center>OKT</td>		
				<td bgcolor=#DEDEDE align=center>NOV</td>				
				<td bgcolor=#DEDEDE align=center>DES</td>
			</tr>";
			
			$res=mysql_query($sql);
			$rowx=mysql_fetch_row($res);
			if($rowx<1)
			{
			$stream.="	<tr class=rowcontent>
			<td colspan=15 align=center>Not Avaliable</td></tr>
			";
			}
			else
			{
			$no=0;
			$res=mysql_query($sql);
				while($barx=mysql_fetch_assoc($res))
				{
				$nox+=1;
				
				$stream.="	<tr class=rowcontent>
				<td>".$nox."</td>
				<td>".$barx['kodeorg']."</td>
				<td>".number_format($barx['jlhmeter'],2)."</td>
				<td>".number_format($barx['jan'],2)."</td>
				<td>".number_format($barx['feb'],2)."</td>	
				<td>".number_format($barx['mar'],2)."</td>
				<td>".number_format($barx['apr'],2)."</td>
				<td>".number_format($barx['mei'],2)."</td>
				<td>".number_format($barx['jun'],2)."</td>
				<td>".number_format($barx['jul'],2)."</td>
				<td>".number_format($barx['agu'],2)."</td>
				<td>".number_format($barx['sep'],2)."</td>	
				<td>".number_format($barx['okt'],2)."</td>
				<td>".number_format($barx['nov'],2)."</td>
				<td>".number_format($barx['des'],2)."</td>
				</tr>";
				}
			}
			$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="AnggaranTraksi";
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
	default:
	break;
}

/*class Org {
    public $_parent;
    public $_name;
    
    function Org($orgName,$theParent) {
        $this->_name = $orgName;
        $this->_parent = $theParent;
    }
    
    function getParent() {
        return $this->_parent;
    }
}

function search($oName,$tArr) {
    $res = false;
    foreach($tArr as $tOrg) {
        if($tOrg->_name==$oName) {
            $res = $tOrg;
            break;
        }
    }
    return $res;
}

$proses = $_GET['proses'];
if(empty($_POST)) {
    $param = $_GET;
    unset($param['proses']);
} else {
    $param = $_POST;
}

#=== Get Data ===
# Get Org Structure
$arrOrg = array(new Org($param['kodeorg'],null));
$tmpOrg = array($param['kodeorg']);
while(!empty($tmpOrg)) {
    foreach($tmpOrg as $key=>$tOrg) {
        unset($tmpOrg[$key]);
        $cols = 'kodeorganisasi,namaorganisasi,tipe';
        $query = selectQuery($dbname,'organisasi',$cols,
            "induk='".$tOrg."'");
        $data = fetchData($query);
        foreach($data as $row) {
            $contOrg[$row['tipe']][$row['kodeorganisasi']] = $row['namaorganisasi'];
            $tmpOrg[] = $row['kodeorganisasi'];
            $arrOrg[] = new Org($row['kodeorganisasi'],$tOrg);
        }
    }
}

#=== Header ===
# Get Nama Kebun
$maskKebun = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$param['kodeorg']."'");
$namaKebun = $maskKebun[$param['kodeorg']];

$listOrg = "";
$i=0;
foreach($arrOrg as $key=>$row) {
    if($i==0) {
        $listOrg .= "kodeorg='".$row->_name."'";
    } else {
        $listOrg .= " or kodeorg='".$row->_name."'";
    }
    $i++;
}
$where = "tahun=".$param['tahun']." and revisi=".$param['revisi']." and (".$listOrg.")";
$cols1 = "kodeanggaran,tipeanggaran,tahun,kodeorg";
$query1 = selectQuery($dbname,'keu_anggaran',$cols1,$where);
$resHead = fetchData($query1);

# Mask Tipe Budget
$tipeBud = array();
foreach($resHead as $row) {
    $tipeBud[$row['kodeorg']] = $row['tipeanggaran'];
}

#=== Detail ===
$query2 = selectQuery($dbname,'keu_anggarandt',"*",$where);
$resDetail = fetchData($query2);

# Get Nama & Kelompok
$whereBarang = "";
foreach($resDetail as $key=>$row) {
    if($key==0) {
        $whereBarang .= "kodebarang='".$row['kodebarang']."'";
    } else {
        $whereBarang .= " or kodebarang='".$row['kodebarang']."'";
    }
}
if($whereBarang!='') {
    $queryBar = selectQuery($dbname,'log_5masterbarang',
        'kodebarang,namabarang,kelompokbarang,satuan',$whereBarang);
    $tmpBar = fetchData($queryBar);
} else {
    $tmpBar = array();
}

# Get Nama Kelompok
$whereKlp = "";
foreach($tmpBar as $key=>$row) {
    if($key==0) {
        $whereKlp .= "kode='".$row['kelompokbarang']."'";
    } else {
        $whereKlp .= " or kode='".$row['kelompokbarang']."'";
    }
}
if($whereKlp!='') {
    $klBarang = makeOption($dbname,'log_5klbarang','kode,kelompok',$whereKlp);
} else {
    $klBarang = array();
}

# Mask Barang
$maskBarang = array();
$maskSatuan = array();
foreach($tmpBar as $row) {
    $maskBarang[$row['kodebarang']] = $klBarang[$row['kelompokbarang']].
        ", ".$row['namabarang'];
    $maskSatuan[$row['kodebarang']] = $row['satuan'];
}

#=== Rearrange Data ===
$data = array();
$tmpDetail = array();
foreach($resDetail as $row) {
    $jumlahsetahun = ($row['jan']+$row['peb']+$row['mar']+$row['apr']+
        $row['mei']+$row['jun']+$row['jul']+$row['agt']+$row['sep']+
        $row['okt']+$row['nov']+$row['dec']) * $row['hargasatuan'];
    $tmpDetail[$row['kodeorg']][$row['kodekegiatan']] = array(
        'kodeanggaran'=>$row['kodeanggaran'],
        'tipeanggaran'=>$tipeBud[$row['kodeorg']],
        'namabarang'=>$maskBarang[$row['kodebarang']],
        'jumlah'=>$row['jumlah'],'uom'=>$maskSatuan[$row['kodebarang']],
        'hargasatuan'=>$row['hargasatuan'],
        'jumlahsetahun'=>$jumlahsetahun,
        'jan'=>$row['jan']*$row['hargasatuan'],'peb'=>$row['peb']*$row['hargasatuan'],
        'mar'=>$row['mar']*$row['hargasatuan'],'apr'=>$row['apr']*$row['hargasatuan'],
        'mei'=>$row['mei']*$row['hargasatuan'],'jun'=>$row['jun']*$row['hargasatuan'],
        'jul'=>$row['jul']*$row['hargasatuan'],'agt'=>$row['agt']*$row['hargasatuan'],
        'sep'=>$row['sep']*$row['hargasatuan'],'okt'=>$row['okt']*$row['hargasatuan'],
        'nov'=>$row['nov']*$row['hargasatuan'],'dec'=>$row['dec']*$row['hargasatuan'],
        'biayaha'=>$row['biayaha'],'biayalain'=>$row['biayalain']
    );
}

# Transform Detail
$detDone = false;
while($detDone=='false') {
    $detDone = true;
    foreach($resDetail as $key=>$row) {
        $tmp = search($row['kodeorg'],$arrOrg);
        if($tmp->getParent()!=null or $tmp->getParent()!=$param['kodeorg']) {
            $resDetail[$key]['kodeorg'] = $tmp->getParent();
            $detDone = false;
        }
    }
}

foreach($tmpDetail as $key=>$row) {
    if($key==$param['kodeorg']) {
        $data[$param['kodeorg']]['-'] = $row;
    } else {
        $data[$param['kodeorg']][$key] = $row;
    }
}

# Jumlah
$jumlah = array();
$cols = array();$first=true;
foreach($data as $kebun=>$row0) {
    foreach($row0 as $div=>$row1) {
        foreach($row1 as $kKeg=>$row2) {
            foreach($row2 as $key=>$row) {
                $jumlah[$kebun][$div][$kKeg][$key] += $row;
                $jumlah[$kebun][$div]['total'][$key] += $row;
                $jumlah[$kebun]['total'][$key] += $row;
                if($first==true) {
                    $cols[] = $key;
                }
            }
            $first = false;
        }
    }
}

switch($proses) {
    case 'preview':
        #=== Prep Table ===
        $table = "<table class='sortable' cellpadding='2' width='1500px'>";
        $table .= "<thead><tr class='rowheader'>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['kodeanggaran']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['tipeanggaran']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['namabarang']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['jumlah']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['satuan']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['hargasatuan']."</td>";
        $table .= "<td rowspan='2'>".$_SESSION['lang']['jumlahsetahun']."</td>";
        $table .= "<td colspan='12' align='center'>".$_SESSION['lang']['rincianbulanan']."</td>";
        $table .= "<td colspan='2' align='center'>".$_SESSION['lang']['totalbiaya']."</td></tr>";
        $table .= "<tr><td>".$_SESSION['lang']['jan']."</td>";
        $table .= "<td>".$_SESSION['lang']['peb']."</td>";
        $table .= "<td>".$_SESSION['lang']['mar']."</td>";
        $table .= "<td>".$_SESSION['lang']['apr']."</td>";
        $table .= "<td>".$_SESSION['lang']['mei']."</td>";
        $table .= "<td>".$_SESSION['lang']['jun']."</td>";
        $table .= "<td>".$_SESSION['lang']['jul']."</td>";
        $table .= "<td>".$_SESSION['lang']['agt']."</td>";
        $table .= "<td>".$_SESSION['lang']['sep']."</td>";
        $table .= "<td>".$_SESSION['lang']['okt']."</td>";
        $table .= "<td>".$_SESSION['lang']['nov']."</td>";
        $table .= "<td>".$_SESSION['lang']['dec']."</td>";
        $table .= "<td>".$_SESSION['lang']['biayaha']."</td>";
        $table .= "<td>".$_SESSION['lang']['biayalain']."</td>";
        $table .= "</tr></thead>";
        $table .= "<tbody>";
        foreach($data as $kebun=>$row0) {
            foreach($row0 as $div=>$row1) {
                #foreach($row1 as $kBag=>$row2) {
                    foreach($row1 as $kKeg=>$row3) {
                        # Divisi & Kegiatan
                        $table .= "<tr class='rowcontent'><td colspan='21'>";
                        $table .= "<div style='float:left;width:170px;'><b>Divisi</b> : ".$div."</div>";
                        #$table .= "<div style='float:left;width:170px;'><b>Bagian</b> : ".$kBag."</div>";
                        $table .= "<div style='float:left;width:170px;'><b>Kegiatan</b> : ".$kKeg."</div></td></tr>";
                        
                        # Data
                        $table .= "<tr class='rowcontent'>";
                        foreach($row3 as $key=>$row) {
                            $table .= "<td>".$row."</td>";
                        }
                        $table .= "</tr>";
                        
                        # Total
                        $table .= "<tr class='rowcontent'>";
                        $table .= "<td colspan='3'>Subtotal</td>";
                        foreach($row3 as $key=>$row) {
                            if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                                if($key=='uom') {
                                    $table .= "<td></td>";
                                } else {
                                    $table .= "<td>".$jumlah[$kebun][$div][$kKeg][$key]."</td>";
                                }
                            }
                        }
                        $table .= "</tr>";
                    }
                    $table .= "<tr class='rowcontent'>";
                    $table .= "<td colspan='3'>Subtotal ".$div."</td>";
                    foreach($cols as $key) {
                        if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                            if($key=='uom') {
                                $table .= "<td></td>";
                            } else {
                                $table .= "<td>".$jumlah[$kebun][$div]['total'][$key]."</td>";
                            }
                        }
                    }
                    $table .= "</tr>";
                #}
            }
            $table .= "<tr class='rowcontent'>";
            $table .= "<td colspan='3'>Grand Total</td>";
            foreach($cols as $key) {
                if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                    if($key=='uom') {
                        $table .= "<td></td>";
                    } else {
                        $table .= "<td>".$jumlah[$kebun]['total'][$key]."</td>";
                    }
                }
            }
            $table .= "</tr>";
        }
        $table .= "</tbody>";
        $table .= "</table>";
        
        #=== View ===
        echo $table;
        break;
    case 'pdf':
        class pdfBudget extends pdf_keu1 {
            function Header() {
                parent::Header();
                $this->SetFont('Arial','B',8);
                $width = $this->_width;
                $height = $this->_height;
                $this->MultiCell(5/100*$width,$height,$_SESSION['lang']['kodeanggaran'],'TBLR','C');
                $this->x = $this->x+5/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(5/100*$width,$height,$_SESSION['lang']['tipeanggaran'],'TBLR','C');
                $this->x = $this->x+10/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(20/100*$width,$height*2,$_SESSION['lang']['namabarang'],'TBLR','C');
                $this->x = $this->x+30/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(5/100*$width,$height*2,$_SESSION['lang']['jumlah'],'TBLR','C');
                $this->x = $this->x+35/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(3/100*$width,$height,$_SESSION['lang']['satuan'],'TBLR','C');
                $this->x = $this->x+38/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(5/100*$width,$height,$_SESSION['lang']['hargasatuan'],'TBLR','C');
                $this->x = $this->x+43/100*$width;$this->y = $this->y - $height*2;
                $this->MultiCell(6/100*$width,$height,$_SESSION['lang']['jumlahsetahun'],'TBLR','C');
                $this->x = $this->x+49/100*$width;$this->y = $this->y - $height*2;
                $this->Cell(42/100*$width,$height,$_SESSION['lang']['rincianbulanan'],'TBR',0,'C');
                $this->Cell(9/100*$width,$height,$_SESSION['lang']['totalbiaya'],'TBR',1,'C');
                $this->x = $this->x+49/100*$width;
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['jan'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['peb'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['mar'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['apr'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['mei'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['jun'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['jul'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['agt'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['sep'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['okt'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['nov'],'TBR',0,'C');
                $this->Cell(3.5/100*$width,$height,$_SESSION['lang']['dec'],'TBR',0,'C');
                $this->Cell(4.5/100*$width,$height,$_SESSION['lang']['biayaha'],'TBR',0,'C');
                $this->Cell(4.5/100*$width,$height,$_SESSION['lang']['biayalain'],'TBR',1,'C');
            }
        }
        
        $pdf = new pdfBudget('L','pt','A4');
        
        #=== Setting Header ===
        # Additional Info
        $pdf->addAddsHeader('Kebun',$namaKebun);
        $pdf->addAddsHeader('Periode',"1 Januari ".$param['tahun'].
            " - 31 Desember ".$param['tahun']);
        $pdf->addAddsHeader('Revisi',$param['revisi']);
        
        # Set Title
        $pdf->SetTitle($_SESSION['lang']['budget']);
        
        # Content Data
        $table = "";
        $widthArr = array(5,5,20,5,3,5,6,
            3.5,3.5,3.5,3.5,3.5,3.5,3.5,3.5,3.5,3.5,3.5,3.5,
            4.5,4.5);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
        $pdf->AddPage();
        $pdf->SetFont('Arial','',8);
        foreach($data as $kebun=>$row0) {
            foreach($row0 as $div=>$row1) {
                #foreach($row1 as $kBag=>$row2) {
                    foreach($row1 as $kKeg=>$row3) {
                        # Divisi & Kegiatan
                        $pdf->Cell(10/100*$width,$height,"Divisi : ".$div,'TBL',0,'L');
                        $pdf->Cell(90/100*$width,$height,"Kegiatan : ".$kKeg,'TBR',1,'L');
                        
                        # Data
                        $i=0;
                        foreach($row3 as $key=>$row) {
                            if($key=='namabarang') {$row = substr($row,0,30);}
                            if($i==3 or $i>4) {
                                $pdf->Cell($widthArr[$i]/100*$width,$height,number_format($row,0),'TBLR',0,'R');
                            } else {
                                $pdf->Cell($widthArr[$i]/100*$width,$height,$row,'TBLR',0,'L');
                            }
                            
                            $i++;
                        }
                        $pdf->Ln();
                        
                        # Total
                        $pdf->Cell(30/100*$width,$height,"Subtotal ".$kKeg." : ",'TBLR',0,'L');
                        $i=0;
                        foreach($row3 as $key=>$row) {
                            if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                                if($key=='uom') {
                                    $pdf->Cell($widthArr[$i]/100*$width,$height,
                                        '','TBLR',0,$tmpAlign);
                                } else {
                                    $pdf->Cell($widthArr[$i]/100*$width,$height,
                                        number_format($jumlah[$kebun][$div][$kKeg][$key],0),'TBLR',0,'R');
                                }
                            }
                            $i++;
                        }
                        $pdf->Ln();
                    }
                    # Total
                    $pdf->Cell(30/100*$width,$height,"Subtotal ".$div." : ",'TBLR',0,'L');
                    $i=0;
                    foreach($row3 as $key=>$row) {
                        if($i==3 or $i>4) {
                            $tmpAlign = 'R';
                        } else {
                            $tmpAlign = 'L';
                        }
                        if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                            if($key=='uom') {
                                $pdf->Cell($widthArr[$i]/100*$width,$height,
                                    '','TBLR',0,$tmpAlign);
                            } else {
                                $pdf->Cell($widthArr[$i]/100*$width,$height,
                                    number_format($jumlah[$kebun][$div]['total'][$key],0),'TBLR',0,'R');
                            }
                        }
                        $i++;
                    }
                    $pdf->Ln();
                #}
            }
            # Grand Total
            $pdf->Cell(30/100*$width,$height,"Grand Total : ",'TBLR',0,'L');
            $i=0;
            foreach($row3 as $key=>$row) {
                if($i==3 or $i>4) {
                    $tmpAlign = 'R';
                } else {
                    $tmpAlign = 'L';
                }
                if($key!='kodeanggaran' and $key!='tipeanggaran' and $key!='namabarang') {
                    if($key=='uom') {
                        $pdf->Cell($widthArr[$i]/100*$width,$height,
                            '','TBLR',0,$tmpAlign);
                    } else {
                        $pdf->Cell($widthArr[$i]/100*$width,$height,
                            number_format($jumlah[$kebun]['total'][$key],0),'TBLR',0,'R');
                    }
                }
                $i++;
            }
            $pdf->Ln();
        }
        
        $pdf->Output();
    default:
        break;
}
*/?>
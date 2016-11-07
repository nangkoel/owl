<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');
#echo "<pre>";
#print_r($_SESSION);
#exit;
# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$order = 'noakun, kodeklp';

#====================== Prepare Data
//$query = selectQuery($dbname,$table,'*',$where,$order);
if($_SESSION['empl']['tipeinduk']=='HOLDING')
{
	$query="select * from ".$dbname.".`log_prapoht` a inner join ".$dbname.".`log_prapodt` b on a.nopp=b.nopp where substring(a.nopp,16,4)='".$_SESSION['empl']['lokasitugas']."'
	and b.create_po='0' group by a.nopp order by a.`nopp` desc";
}
else
{
	$query="select * from ".$dbname.".`log_prapoht` a inner join ".$dbname.".`log_prapodt` b on a.nopp=b.nopp where substring(a.nopp,16,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and b.create_po='0' group by a.nopp  order by a.`nopp` desc";
}
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
		//$this->Cell(20,$height,$_SESSION['org']['namaorganisasi'],'',1,'L');
        $this->SetFont('Arial','B',12);
#		$this->Cell($width,$height,'Tabel : '.$table,'',1,'L');
		$this->Cell($width,$height,strtoupper($_SESSION['lang']['list_pp']),'',1,'C');
        $this->SetFont('Arial','B',8);
		$this->Cell(415,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['tanggal'],'',0,'L');
		$this->Cell(5,$height,':','',0,'L');
		$this->Cell(40,$height,date('d-m-Y H:i'),'',1,'L');
		$this->Cell(415,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['page'],'',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(15,$height,$this->PageNo(),'',1,'L');
#        $this->Ln();
		$this->Cell(415,$height,' ','',0,'R');
		$this->Cell(40,$height,$_SESSION['lang']['user'],'',0,'L');
		$this->Cell(8,$height,':','',0,'L');
		$this->Cell(20,$height,$_SESSION['standard']['username'],'',1,'L');
        $this->Ln();
        
        # Generate Header
#        foreach($header as $hName) {
#            $this->Cell($width/count($header),$height,ucfirst($hName),'TBLR',0,'L');
#        }
        $this->Cell(40,1.5*$height,'No','TBLR',0,'C');
		$this->Cell(120,1.5*$height,$_SESSION['lang']['nopp'],'TBLR',0,'C');
        $this->Cell(60,1.5*$height,$_SESSION['lang']['tanggal'],'TBLR',0,'C');
        $this->Cell(150,1.5*$height,$_SESSION['lang']['namaorganisasi'],'TBLR',0,'C');
		$this->Cell(80,1.5*$height,'Progress','TBLR',0,'C');
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
$no=0;
foreach($result as $data) {
	$no++;
	$kode_org=substr($data['nopp'],15,4);
	$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$kode_org."' or induk='".$kode_org."'"; //echo $spr;
	$rep=mysql_query($spr) or die(mysql_error($conn));
	$bas=mysql_fetch_assoc($rep);
	if($data['close']=='0')
		{
		
			$b="Need Approval";
		}
		elseif($data['close']=='1')
		{
			$b="Waiting Approval";
		}
		elseif($data['close']=='2')
		{
			if($no<6)
			{
				if($data['hasilpersetujuan'.$no]==1)
				{
					$b='Approved';
				}
				elseif($data['hasilpersetujuan'.$no]==3)
				{
					$b='Rejected';
				}
				elseif($data['hasilpersetujuan'.$no]==0)
				{
					$b='Waiting Approval';
				}
			}
			//$b="Approved";
		}
	$data['tanggal']=tanggalnormal($data['tanggal']);
	$pdf->Cell(40,$height,$no,'TBLR',0,'L');
	$pdf->Cell(120,$height,$data['nopp'],'TBLR',0,'L');
	$pdf->Cell(60,$height,$data['tanggal'],'TBLR',0,'C');
	$pdf->Cell(150,$height,$bas['namaorganisasi'],'TBLR',0,'L');
	$pdf->Cell(80,$height,$b,'TBLR',0,'L');
    $pdf->Ln();
	
}


# Print Out
$pdf->Output();
?>
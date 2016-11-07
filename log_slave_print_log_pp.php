<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/fpdf.php');
include_once('lib/zMysql.php');

#echo "<pre>";
#print_r($_SESSION);
#exit;
# Get Data
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];


#====================== Prepare Data
$ql="select a.catatan,a.kodeorg,a.nopp,a.tanggal,a.dibuat from ".$dbname.".`log_prapoht` a where a.nopp='".$column."'"; //echo $ql;
$pq=mysql_query($ql) or die(mysql_error());
$hsl=mysql_fetch_assoc($pq);
$kdr=$hsl['kodeorg'];


$sNmKry="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$hsl['dibuat']."'";
$qNmKry=mysql_query($sNmKry) or die(mysql_error());
$rNmKry=mysql_fetch_assoc($qNmKry);
$dibuat=$rNmKry['namakaryawan'];


$sNmkntr="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdr."'";
$qNmkntr=mysql_query($sNmkntr) or die(mysql_error());
$rNmkntr=mysql_fetch_assoc($qNmkntr);
$sNmkntr2="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".substr($hsl['nopp'],-4,4)."'";
$qNmkntr2=mysql_query($sNmkntr2) or die(mysql_error());
$rNmkntr2=mysql_fetch_assoc($qNmkntr2);
$nmKntr=$rNmkntr['namaorganisasi'];
$nmunit=$rNmkntr2['namaorganisasi'];
$tgl=tanggalnormal($hsl['tanggal']);

$query="select a.*,b.*,c.namabarang,c.satuan,d.spesifikasi from ".$dbname.".".$table." a inner join ".$dbname.".`log_prapodt` b on a.nopp=b.nopp inner join ".$dbname.".`log_5masterbarang` c on b.kodebarang=c.kodebarang  left join ".$dbname.".`log_5photobarang` d on c.kodebarang=d.kodebarang where a.nopp='".$column."' and status!=3 "; //echo $query; exit();
$result = fetchData($query);

#====================== Prepare Header PDF
class masterpdf extends FPDF {
    function Header() {
        global $table;
        global $header;
        global $column;
        global $dbname;
        global $tgl;
        global $nmKntr;
        global $dibuat;
        global $kdr;
        global $nmunit;
				
        # Panjang, Lebar
        $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                //
               
                if($kdr=='HIP'){  $path='images/hip_logo.jpg'; } else if($kdr=='SIL'){  $path='images/sil_logo.jpg'; } else if($kdr=='SIP'){  $path='images/sip_logo.jpg'; }
                $a=$this->Image($path,20,10,120,65,'jpg','');
                $this->Cell(120,$height,$a,' ',0,'L');
        $this->SetFont('Arial','B',10);
                $this->Cell(40/100*$width,$height,$nmKntr,'',0,'L');
                $this->Cell(40/100*$width,$height,'TO :','',1,'L');
                $this->Cell(120,$height,' ','',0,'L');
                //$this->Cell(22/100*$width,$height,' ','',0,'L');
                $this->SetFont('Arial','B',10);
                $this->Cell(12/100*$width,$height,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(2/100*$width,$height,':','',0,'L');
                $this->Cell(1/100*$width,$height,$nmunit,'',0,'L');		
                $this->Cell(25/100*$width,$height,' ','',0,'L');
                $this->SetFont('Arial','B',10);
                $this->Cell(12/100*$width,$height,'PURCHASING DEPARTEMENT','',0,'L');
                $this->Cell(2/100*$width,$height,'','',0,'L');
                $this->Cell(1/100*$width,$height,'','',1,'L');

                //$this->Cell(40/100*$width,$height,strtoupper($_SESSION['org']['namaorganisasi']),'',0,'L');
                $this->Cell(120,$height,' ','',0,'L');
                $this->SetFont('Arial','B',10);
                $this->Cell(12/100*$width,$height,'PP NO','',0,'L');
                $this->Cell(2/100*$width,$height,':','',0,'L');
                $this->Cell(1/100*$width,$height,$column,'',0,'L');		
                $this->Cell(25/100*$width,$height,' ','',0,'L');
                $this->SetFont('Arial','B',10);
                $this->Cell(14/100*$width,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(2/100*$width,$height,':','',0,'L');
                $this->Cell(1/100*$width,$height,$tgl,'',1,'L');
                $this->Cell(120,$height,' ','',0,'L');
                $this->SetFont('Arial','B',10);
                $this->Cell(12/100*$width,$height,'PAGE','',0,'L');
                $this->Cell(2/100*$width,$height,':','',0,'L');
                $this->Cell(1/100*$width,$height,$this->PageNo(),'',0,'L');

        $this->Ln();

    }
}

#====================== Prepare PDF Setting
$pdf = new masterpdf('P','pt','A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 11;

$pdf->SetFont('Arial','B',8);
$pdf->AddPage();
        $pdf->Cell(20,1.5*$height,'No.',1,0,'C');
        $pdf->Cell(55,1.5*$height,$_SESSION['lang']['kodebarang'],1,0,'L');
        $pdf->Cell(165,1.5*$height,$_SESSION['lang']['namabarang'],1,0,'C');
        //$pdf->Cell(80,1.5*$height,$_SESSION['lang']['spesifikasi'],1,0,'C');
        $pdf->Cell(43,1.5*$height,$_SESSION['lang']['jumlah'],1,0,'L');
	$pdf->Cell(43,1.5*$height,$_SESSION['lang']['stok'],1,0,'C');
        $pdf->Cell(35,1.5*$height,$_SESSION['lang']['satuan'],1,0,'C');
		
        $pdf->Cell(40,1.5*$height,'Required',1,0,'C');
        $pdf->Cell(160,1.5*$height,$_SESSION['lang']['keterangan'],1,0,'C');
        $pdf->Ln();
        $no=0;
		
		//print_r($_SESSION['empl']);


/*gudang
pt
divisi=regional*/
$stok[]=array();
			$x="select sum(saldoqty) as saldoqty,kodebarang from ".$dbname.".log_5masterbarangdt where 
			kodegudang like '".substr($column,-4,4)."%' group by kodebarang";
                        //echo $x;
			$y=mysql_query($x) or die (mysql_error($conn));
			while($z=mysql_fetch_assoc($y))
			{
				$kodebarang=$z['kodebarang'];
				$stok[$kodebarang]=$z['saldoqty'];
			}
			
	
			
		//echo $x;	
        $turunin=11*65;//batesin baris yg tampil
        foreach($result as $data) {
			
			
//	print_r($stok);	
		
			
			
            $pdf->SetFont('Arial','',7);
                $no+=1;
                //$tr=substr($data['namabarang'],0,20);
                 if($no!=1){
                    $pdf->SetY($akhirY);
                 }
                 $akhirY=$pdf->GetY();
                 if($akhirY>=$turunin){
                    $pdf->AddPage();
                    $akhirY=$pdf->GetY();
                 }    
                $height2=$height;
//                if (strlen(trim($data['namabarang']))>40 || strlen(trim($data['keterangan']))>40){
//                    $height2=$height*2;
                //}
                
                $pdf->Cell(20,$height2,$no,0,0,'L');
                $pdf->Cell(55,$height2,$data['kodebarang'],0,0,'L');
                $pdf->SetX($pdf->GetX());
                $posisiY=round($pdf->GetY());
                $pdf->MultiCell(165,$height2,$data['namabarang'],0,'L',0);
                $akhirY=$pdf->GetY();

                //naik lagi kursornya
                $pdf->SetY($posisiY);
                $pdf->SetX($pdf->GetX()+70);
                
                //$akhirY=$pdf->GetY();
                //$pdf->SetY($akhirY);
                $pdf->SetX($pdf->GetX()+175);
                //$pdf->Cell(80,$height2,$data['spesifikasi'],1,0,'L');
                $jumlah=explode(".", $data['jumlah']);
                $pdf->Cell(40,$height2,number_format($data['jumlah'],strlen($jumlah[1])),0,0,'R');
				$x="select sum(saldoqty) as saldoqty,kodebarang from ".$dbname.".log_5masterbarangdt where kodebarang='".$data['kodebarang']."' and
				kodegudang in (select kodeorganisasi from ".$dbname.".organisasi where induk in
				(select kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['kodeorganisasi']."')) group by kodebarang";
				$y=mysql_query($x) or die (mysql_error($conn));
				$z=mysql_fetch_assoc($y);
				
		$stokdata=explode(".",$stok[$data['kodebarang']]);
                if (strlen($stokdata[1])>3){
                    $pdf->Cell(43,$height2,number_format($stok[$data['kodebarang']],2),0,0,'R');
                } else {
                    $pdf->Cell(43,$height2,number_format($stok[$data['kodebarang']],strlen($stokdata[1])),0,0,'R');
                }
                //$pdf->Cell(35,$height2,$z['saldoqty'],0,0,'R');
	        $pdf->Cell(30,$height2,$data['satuan'],0,0,'C');		
	        $pdf->Cell(40,$height2,tanggalnormal($data['tgl_sdt']),0,0,'L');
                $pdf->SetFont('Arial','',6.5);
                $pdf->MultiCell(170, $height2, $data['keterangan'],0,'L',0);
                if ($akhirY<$pdf->GetY()) $akhirY=$pdf->GetY();

                //naik lagi kursornya
                $pdf->SetY($pdf->GetY());
                $totTinggHal+=$height2;
        }
        $pdf->MultiCell(560,5,"",'T','L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(120,$height,$_SESSION['lang']['dbuat_oleh'].':'.$dibuat,'',1,L);
        $pdf->Cell(40,$height,$_SESSION['lang']['catatan'].' :','',0,L);
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell(500,$height,$hsl['catatan'],0,'L',0);
        $totTinggHal+=5;
        $totTinggHal=$totTinggHal+($height*3);
	$pdf->SetFont('Arial','B',8);
        
        $akhirY=$pdf->GetY();
        $selisihtinggi=$pdf->h-$akhirY;
        $tinggSma=((1.5*$height)*6)+60;
        //exit("error:".$selisihtinggi."___".$akhirY."__".$pdf->h."__".$totTinggHal);
        
        
        if($totTinggHal>=599){
            $pdf->SetY($akhirY+20);
        }else{
            $pdf->SetY($akhirY+20);
        }
        $pdf->Cell(120,$height,$_SESSION['lang']['approval_status'].':','',0,L);
        $pdf->Ln();
        $ko=0;

                $pdf->Cell(20,1.5*$height,'No.',1,0,'C');
                $pdf->Cell(120,1.5*$height,$_SESSION['lang']['nama'],1,0,'C');
                $pdf->Cell(80,1.5*$height,$_SESSION['lang']['kodejabatan'],1,0,'C');
                $pdf->Cell(70,1.5*$height,$_SESSION['lang']['lokasitugas'],1,0,'C');
                $pdf->Cell(100,1.5*$height,$_SESSION['lang']['keputusan'],1,0,'C');
                $pdf->Cell(170,1.5*$height,$_SESSION['lang']['note'],1,0,'C');
                $pdf->Ln();	
                 $sCek="select nopp from ".$dbname.".log_prapodt where nopp='".$column."'";
                //echo $sCek;exit();
                $qCek=mysql_query($sCek) or die(mysql_error());
                $rCek=mysql_num_rows($qCek);
                if($rCek>0)
                {
                        $qp="select * from ".$dbname.".`log_prapoht` where `nopp`='".$column."'"; //echo $qp;
                        $qyr=fetchData($qp);
                        foreach($qyr as $hsl)
                        {


                                for($i=1;$i<6;$i++)
                                {
                                        if($hsl['hasilpersetujuan'.$i]==1)
                                        {
                                                $b['status']=$_SESSION['lang']['disetujui'];
                                        }
                                        elseif($hsl['hasilpersetujuan'.$i]==3)
                                        {
                                                $b['status']=$_SESSION['lang']['ditolak'];
                                        }
                                        elseif($hsl['hasilpersetujuan'.$i]==''||$hsl['hasilpersetujuan'.$i]==0)
                                        {
                                                $b['status']=$_SESSION['lang']['wait_approve'];
                                        }
                                        if($hsl['persetujuan'.$i]!=0000000000)
                                        {
                                                $sql="select * from ".$dbname.".`datakaryawan` where `karyawanid`='".$hsl['persetujuan'.$i]."'"; //echo $sql;//exit();
                                                $keterangan=$hsl['komentar'.$i];
                                                $tanggal="";
                                                if($hsl['tglp'.$i]!=''){
                                                    $tanggal=tanggalnormal($hsl['tglp'.$i]);
                                                }
                                                
                                                $query=mysql_query($sql) or die(mysql_error());
                                                $res3=mysql_fetch_object($query);

                                                $sql2="select * from ".$dbname.".`sdm_5jabatan` where kodejabatan='".$res3->kodejabatan."'";
                                                $query2=mysql_query($sql2) or die(mysql_error());
                                                $res2=mysql_fetch_object($query2);

                                                $height3=$height;
//                                                if (strlen($keterangan)>40) {
//                                                    $height3=$height*2;
//                                                }
                                                $pdf->SetFont('Arial','',7);
                                                $pdf->Cell(20,1.5*$height3,$i,0,0,'C');
                                                $pdf->Cell(120,1.5*$height3,$res3->namakaryawan."(".$tanggal.") ",0,0,'L');
                                                $pdf->SetFont('Arial','',5.5);
                                                $pdf->Cell(80,1.5*$height3,$res2->namajabatan,0,0,'L');
                                                $pdf->SetFont('Arial','',7);
                                                $pdf->Cell(70,1.5*$height3,$res3->lokasitugas,0,0,'L');
                                                $pdf->Cell(100,1.5*$height3,$b['status'],0,0,'L');
                                                $pdf->MultiCell(170,1.5*$height,$keterangan,0,'L',0);
                                                //$pdf->MultiCell(130,1.5*$height3,$keterangan,1,'L',0);
                                        }
                                        else
                                        {
                                                break;
                                        }



                                }
                        }
                        
        }
        else
        {
                        $pdf->SetFont('Arial','',7);
                        $pdf->Cell(560,1.5*$height,"Not Found",1,1,'C');
        }
        
        //$pdf->Cell(15,$height,'Page '.$pdf->PageNo(),'',1,'L');

# Print Out
$pdf->Output();

?>
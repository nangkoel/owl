<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

$table = $_GET['table'];
$column = $_GET['column'];
$optSup=makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$where = $_GET['cond'];
//=============

 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $dataTR;
				
                
				$dataTR=explode(",",$_GET['column']);

                
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
                
//                $this->SetFont('Arial','',8);
//                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
//                $this->Cell(5,$height,':','',0,'L');
//                $this->Cell(45/100*$width,$height,$_SESSION['standard']['username'],'',0,'L');
//
//                $this->Ln();
//                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
//                $this->Cell(5,$height,':','',0,'L');
//                $this->Cell(45/100*$width,$height,date('d-m-Y H:i:s'),'',0,'L');
              	
              
				
                $this->Ln();
                $this->SetFont('Arial','U',11);
                $this->Cell($width,$height,$_SESSION['lang']['pengirimanBibit'],0,1,'C');	
                $this->Ln(35);
				
//                $this->SetFont('Arial','B',9);
//                $this->SetFillColor(220,220,220);
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                /*foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }*/
				

            
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
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',9);
		
		$str="select * from ".$dbname.".".$_GET['table']."   where kodeorg='".$dataTR[3]."' and kodetransaksi='".$dataTR[1]."'
                     and rit='".$dataTR[4]."' and kodevhc='".$dataTR[5]."' and batch='".$dataTR[2]."'  and tanggal='".$dataTR[0]."'"; //echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		$res=mysql_fetch_assoc($re);
		
                        $arr=array("External","Internal","Afliasi");
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['batch'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['batch'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['tanggal'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,tanggalnormal($res['tanggal']),0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['kodeorg'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$optNm[$res['kodeorg']],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['jumlah'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['jumlah'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['nospb'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['keterangan'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['kodevhc'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['kodevhc'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,"RIT",0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['rit'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['sopir'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['sopir'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['Intex'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$arr[$res['intex']],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,substr($_SESSION['lang']['customerlist'],5),0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        if($res['intex']!=0)
                        {
                            $optPlngn=$optNm[$res['pelanggan']];
                        }
                        else
                        {
                            $optPlngn=$optSup[$res['pelanggan']];
                        }
                        $pdf->Cell(23/100*$width,$height,$optPlngn,0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['kodeblok'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['afdeling']."[ ".$optNm[$res['afdeling']]."]",0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,"Detail Lokasi",0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(30/100*$width,$height,$res['lokasipengiriman'],0,1,'L',1);
                        $pdf->Ln(3);
                        $pdf->Cell(17/100*$width,$height,$_SESSION['lang']['kegiatan'],0,0,'L',1);
                        $pdf->Cell(3/100*$width,$height,":",0,0,'L',1);
                        $pdf->Cell(23/100*$width,$height,$res['jenistanam'],0,1,'L',1);
                        if($res['posting']==1)
                        {
                        $pdf->Cell(100/100*$width,$height,strtoupper($_SESSION['lang']['asisten'])." ".$optNm[substr($res['kodeorg'],0,4)],0,1,'R');
                        $pdf->Ln(69);

                        $pdf->Cell(100/100*$width,$height,$optNmKary[$res['penanggungjawab']],0,1,'R');
                        }

		
	
        $pdf->Output();
?>

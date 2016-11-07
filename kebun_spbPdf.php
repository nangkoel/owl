<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
$table = $_GET['table'];
$column = $_GET['column'];
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
                                global $noSpb;


                                $noSpb=$_GET['column'];
                                $sH="select updateby from ".$dbname.".kebun_spbht where nospb='".$noSpb."'";
                                $qh=mysql_query($sH) or die(mysql_error());
                                $rH=mysql_fetch_assoc($qh);

                                $sN="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$rH['updateby']."'";
                                $qN=mysql_query($sN) or die(mysql_error());
                                $rN=mysql_fetch_assoc($qN);
                                $nospb=substr($noSpb,8,6);

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
                $this->Ln();
                $this->SetFont('Arial','',10);
                                $this->Cell($width,$height,  strtoupper($_SESSION['lang']['suratPengantarBuah']),'',0,'L');
                                $this->Ln();
                $this->SetFont('Arial','',8);
                                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$nospb,'',0,'L');
                $this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['nospb'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,$_GET['column'],0,0,'L');
                $this->Ln();
                                $query2 = selectQuery($dbname,'organisasi','namaalias',
                                "kodeorganisasi='".$nospb."'");
                                $orgData2 = fetchData($query2);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['namaorganisasi'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$orgData2[0]['namaalias'],'',0,'L');
                $this->Cell((10/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');
                                 $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['dbuat_oleh'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height,
                   $rN['namakaryawan'],0,0,'L');		

                $this->Ln();
                $this->Ln();	
                $this->SetFont('Arial','B',9);	
                $this->SetFillColor(220,220,220);
                                $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);	
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['janjang'],1,0,'C',1);						
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['bjr'],1,0,'C',1);
                                $this->Cell(10/100*$width,$height,$_SESSION['lang']['brondolan'],1,1,'C',1);

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

                $str="select * from ".$dbname.".kebun_spbdt   where nospb='".$noSpb."'"; //echo $str;exit();
                $re=mysql_query($str);
                $no=0;
                while($res=mysql_fetch_assoc($re))
                {
                        $no+=1;

                        $pdf->Cell(3/100*$width,$height,$no,1,0,'L',1);
                        $pdf->Cell(15/100*$width,$height,$res['blok'],1,0,'L',1);	
                        $pdf->Cell(10/100*$width,$height,number_format($res['jjg'],2),1,0,'R',1);
                        $pdf->Cell(10/100*$width,$height,number_format($res['bjr'],2),1,0,'R',1);
                        $pdf->Cell(10/100*$width,$height,number_format($res['brondolan'],2),1,1,'R',1);
                }

        $pdf->Output();
?>

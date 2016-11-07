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

//create Header
class PDF extends FPDF
{

        function Header()
        {
        global $conn;
        global $dbname;
    global $userid;
        global $posting;
        global $noKontrak;
        global $kodePt;
        global $kdBrg;
        global $tlgKontrk;
        global $kdCust;
        global $nmBrg;
        global $wilKota;
        global $nama;

                        $noKontrak=$_GET['column'];
                        //$nospb=substr($noSpb,0,4);

                        $str="select * from ".$dbname.".".$_GET['table']."  where nokontrak='".$noKontrak."' ";
                        //echo $str;exit();
                        $res=mysql_query($str);
                        $bar=mysql_fetch_assoc($res);
                        $kodePt=$bar['kodept'];
                        $kdBrg=$bar['kodebarang'];
                        $tlgKontrk=tanggalnormal($bar['tanggalkontrak']);
                        $kdCust=$bar['koderekanan'];

                        //echo $posting; exit();	
                        //ambil nama pt
                           $str1="select * from ".$dbname.".organisasi where kodeorganisasi='".$kodePt."'"; 
                           $res1=mysql_query($str1);
                           while($bar1=mysql_fetch_object($res1))
                           {
                                 $nama=$bar1->namaorganisasi;
                                 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
                                 $telp=$bar1->telepon;	
                                 $wilKota=$bar1->wilayahkota;			 
                           }    

                        $sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error());
                        $rBrg=mysql_fetch_assoc($qBrg);
                        $nmBrg=$rBrg['namabarang'];
                $path='images/hip_logo.jpg';
                if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
            $this->Image($path,15,5,40);	
                $this->SetFont('Arial','B',10);
                $this->SetFillColor(255,255,255);	
                $this->SetX(55);   
            $this->Cell(60,5,$nama,0,1,'L');	 
                $this->SetX(55); 		
            $this->Cell(60,5,$alamatpt,0,1,'L');	
                $this->SetX(55); 			
                $this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
                $this->Line(10,30,200,30);
                $this->Ln();
                $this->SetX(160);
                $this->SetFont('Arial','','10');
                $this->Cell(15,5,ucfirst(strtolower($wilKota)).", ".$tlgKontrk,0,1,'L');
                $this->Ln();
                $this->SetFont('Arial','B','12');
               // $this->SetX(50);
                $this->Cell(180,5,strtoupper($_SESSION['lang']['kontrakJual']." ".$rBrg['namabarang']),0,1,'C');
                $this->SetFont('Arial','','10');
                $this->SetX(85);
                $this->Cell(35,5,$_SESSION['lang']['nourut'].": ".$noKontrak,0,1,'L');
                $this->Ln();

     $this->Ln();
        }


        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }

}

        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();

//ambil kelengkapan
        $sCust="select *  from ".$dbname.".pmn_4customer where kodecustomer='".$kdCust."'";
        $qCust=mysql_query($sCust) or die(mysql_error($sCust));
        $rCust=mysql_fetch_assoc($qCust);
        $pdf->Ln();
        $pdf->SetFont('Arial','U','10');
        $pdf->Cell(35,5,$_SESSION['lang']['custInformation'],0,1,'L');
        $pdf->SetFont('Arial','','10');
        $pdf->Cell(35,5,$_SESSION['lang']['nmcust'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rCust['namacustomer'],'',0,'L');
        $pdf->Ln();

        $pdf->Cell(35,5,$_SESSION['lang']['address'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rCust['alamat'],'',0,'L');	
        $pdf->Ln();
        $pdf->Cell(35,5,$_SESSION['lang']['fax'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rCust['fax'],'',0,'L');	
        $pdf->Ln();
        $pdf->Cell(35,5,$_SESSION['lang']['cperson'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rCust['kontakperson'],'',0,'L');	
        $pdf->Ln();
/*	$pdf->Cell(35,5,$_SESSION['lang']['nmcust']." : ".$rCust['namacustomer'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['address']." : ".$rCust['alamat'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['fax']." : ".$rCust['fax'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['cperson']." : ".$rCust['kontakperson'],0,1,'L');*/
        $sKntrk="select * from ".$dbname.".pmn_kontrakjual where nokontrak='".$noKontrak."'";
        $qKntrk=mysql_query($sKntrk) or die(mysql_error());
        $rKntrk=mysql_fetch_assoc($qKntrk);
        $pdf->Ln();
        $pdf->SetFont('Arial','U','10');
        $pdf->Cell(35,5,$_SESSION['lang']['orderInfor'],0,1,'L');
        $pdf->SetFont('Arial','','10');
        $pdf->Cell(35,5,$_SESSION['lang']['namabarang'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$nmBrg,'',0,'L');
        $pdf->Ln();
        //$pdf->Cell(35,5,$_SESSION['lang']['namabarang']." : ".$nmBrg,0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['jmlhBrg'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rKntrk['kuantitaskontrak'],'',0,'L');
        $pdf->Ln();
        //$pdf->Cell(35,5,$_SESSION['lang']['jmlhBrg']." : ".$rKntrk['kuantitaskontrak'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['hargasatuan'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,'','',0,'L');
        $pdf->Ln();

        $pdf->Cell(35,5,$_SESSION['lang']['kualitas'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->MultiCell(100,5,$rKntrk['kualitas'],0,'L');

        $pdf->Cell(35,5,$_SESSION['lang']['penyerahan'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->MultiCell(160,5,"Mulai tgl.".tanggalnormal($rKntrk['tanggalkirim'])." s.d. ".tanggalnormal($rKntrk['sdtanggal'])." Toleransi kuantitas kurang lebih ".$rKntrk['toleransi']."% atas kuantitas barang",0,'L');

        $pdf->Cell(35,5,$_SESSION['lang']['payment'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['syratpembayaran'],0,'L');
        //$pdf->Cell(35,5,$_SESSION['lang']['payment']." : ".$rKntrk['syratpembayaran'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['infoTmbngn'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['standartimbangan'],0,'L');
        //$pdf->Cell(35,5,$_SESSION['lang']['infoTmbngn']." : ".$rKntrk['standartimbangan'],0,1,'L');
        $pdf->Cell(35,5,$_SESSION['lang']['nodo'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        $pdf->Cell(100,5,$rKntrk['nodo'],'',0,'L');
        $pdf->Ln();
        $pdf->Cell(35,5,$_SESSION['lang']['note'],'',0,'L');
        $pdf->Cell(2,5,':','',0,'L');
        if($_SESSION['language']=='EN'){
            $pdf->Cell(100,5,"If the quality of ".$nmBrg." beyond standard, claim would be charged as below:",'',0,'L');
        }else{
            $pdf->Cell(100,5,"Apabila Mutu ".$nmBrg." diatas Standard, maka Klaim diperhitungkan sbb:",'',0,'L');
        }
        $pdf->Ln();
//	$pdf->Cell(35,5,$_SESSION['lang']['note']." : Apabila Mutu ".$nmBrg." diatas Standard, maka Klaim diperhitungkan sbb:",0,1,'L');
        $pdf->Cell(35,5,'','',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['catatan1'],0,'L');

        $pdf->Cell(35,5,'','',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['catatan2'],0,'L');

        $pdf->Cell(35,5,'','',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['catatan3'],0,'L');

        $pdf->Cell(35,5,'','',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['catatan4'],0,'L');

        $pdf->Cell(35,5,'','',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->MultiCell(160,5,$rKntrk['catatan5'],0,'L');


        if($rKntrk['catatanlain']!='')
        {
                $pdf->Cell(35,5,$_SESSION['lang']['catatanlain'],'',0,'L');
                $pdf->Cell(2,5,':','',0,'L');
                $pdf->MultiCell(160,5,$rKntrk['catatanlain'],0,'L');
                //$pdf->Cell(35,5,$_SESSION['lang']['catatanlain']." : ".$rKntrk['catatanlain'],0,1,'L');
        }
        if($_SESSION['language']=='EN'){
            $pdf->Cell(35,5,"Thank you for your cooperations.",0,1,'L');
        }else{
                $pdf->Cell(35,5,"Atas Perhatian dan kerjasamanya diucapkan terima kasih.",0,1,'L');
        }
        $pdf->Ln();
        $pdf->Cell(35,5,ucfirst(strtolower($wilKota)).", ".$tlgKontrk,'',0,'L');
        $pdf->Ln();
        $pdf->Cell(12,5,$_SESSION['lang']['penjual'],'',0,'L');
        $pdf->Cell(2,5,';','',0,'L');
        $pdf->Cell(120,5,'','',0,'L');	
        $pdf->Cell(18,5,$_SESSION['lang']['Pembeli'],'',0,'L');
        $pdf->Cell(2,5,';','',0,'L');
        $pdf->Cell(35,5,'','',1,'L');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial','U','10');
        $pdf->Cell(12,5,$rKntrk['penandatangan'],'',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->Cell(120,5,'','',0,'L');	
        $pdf->Cell(18,5,$rKntrk['penandatangan2'],'',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->Cell(35,5,'','',1,'L');
        $pdf->SetFont('Arial','I','9');
        $pdf->Cell(12,5,$nama,'',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->Cell(120,5,'','',0,'L');	
        $pdf->Cell(18,5,$rCust['namacustomer'],'',0,'L');
        $pdf->Cell(2,5,'','',0,'L');
        $pdf->Cell(35,5,'','',1,'L');


        $pdf->Output();
?>

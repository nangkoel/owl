<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');
include_once('lib/zLib.php');

/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
 $optNmkry=makeOption($dbname,'datakaryawan', 'karyawanid,namakaryawan');
//=============

//create Header
class PDF extends FPDF
{

        function Header()
        {
        global $conn;
        global $dbname;
        global $nomor;
        global $userid;
        global $posted;
        global $tanggal;
        global $optNmkry;
        global $barHt;
        global $isi;

        $isi=explode(",",$_GET['column']);
		
		$kodePT=explode("/",$isi[0]);
		//echo $kodePT[4];
		

                $str="select * from ".$dbname.".log_perintaanhargaht  where nomor='".$isi[0]."' and nourut='".$isi[1]."'";
                //echo $str."___".$_GET['column'];exit();
                $res=mysql_query($str);
                $barHt=mysql_fetch_object($res);

                        //ambil nama pt
                           $str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$kodePT[4]."'"; 
                           $res1=mysql_query($str1);
                           while($bar1=mysql_fetch_object($res1))
                           {
                                 $namapt=$bar1->namaorganisasi;
                                 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
                                 $telp=$bar1->telepon;				 
                                 $fax=$bar1->fax;				 
                           } 
           $sql="select * from ".$dbname.".log_5supplier where supplierid='".$barHt->supplierid."'"; //echo $sql;
           $query=mysql_query($sql) or die(mysql_error());
           $res=mysql_fetch_object($query);

            $sNpwp="select npwp,alamatnpwp from ".$dbname.".setup_org_npwp where kodeorg='".$_SESSION['org']['kodeorganisasi']."'";
            // echo"<pre>";print_r($_SESSION);echo"</pre>";echo $sNpwp;exit();
            $qNpwp=mysql_query($sNpwp) or die(mysql_error());
            $rNpwp=mysql_fetch_assoc($qNpwp);
            $this->SetMargins(15,10,0);
            
                $path='images/'.strtolower($kodePT[4]).'_logo.jpg'; 
                $this->Image($path,15,5,40);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(55);   
                $this->Cell(60,5,$namapt,0,1,'L');	 
                $this->SetX(55); 		
                $this->Cell(60,5,$alamatpt,0,1,'L');	
                $this->SetX(55); 			
                $this->Cell(50,5,"Tel: ".$telp,0,0,'L');	
                $this->Cell(50,5,"Fax: ".$fax,0,1,'L');	
                $this->SetFont('Arial','B',7);
                $this->SetX(55); 			
                $this->Cell(60,5,"NPWP: ".$rNpwp['npwp'],0,1,'L');	
                $this->SetX(55); 			
                $this->Cell(60,5,$_SESSION['lang']['alamat']." NPWP: ".$rNpwp['alamatnpwp'],0,1,'L');	

                $this->Ln();
                $this->SetFont('Arial','U',13);
                $this->SetY(40);
                $this->Cell(190,5,strtoupper($_SESSION['lang']['permintaan_harga']),0,1,'C');
				$this->SetFont('Arial','',9);
				$this->Cell(190,5,strtoupper($isi[0]),0,1,'C');		
                $this->SetFont('Arial','',6); 
                $this->SetY(32);
                $this->SetX(163);
                $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
                //$this->Line(10,27,200,27);	
                $this->Line(15,35,205,35);
                $this->SetY(50);
                $this->SetFont('Arial','',9);
                if($_SESSION['language']=='EN'){
                    $this->Cell(30,4,$_SESSION['lang']['kepada'].":",0,0,'L'); 
                }else{
                    $this->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
                }
                $this->Ln();
                $this->Ln();
                //$this->Cell(40,4,": ".$kodegudang,0,1,'L'); 
                $this->Cell(30,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
                $this->Cell(40,4,": ".$res->namasupplier,0,1,'L'); 				
                $this->Cell(30,4,$_SESSION['lang']['alamat'],0,0,'L'); 
                $this->Cell(40,4,": ".$res->alamat,0,1,'L'); 		  
                $this->Cell(30,4,$_SESSION['lang']['telp'],0,0,'L'); 
                $this->Cell(40,4,": ".$res->telepon,0,1,'L'); 
                $this->Cell(30,4,$_SESSION['lang']['fax'],0,0,'L'); 
                $this->Cell(40,4,": ".$res->fax,0,1,'L'); 
                $this->Cell(30,4,$_SESSION['lang']['matauang'],0,0,'L'); 
                $this->Cell(40,4,": ".$barHt->matauang,0,1,'L'); 
                $this->Cell(30,4,$_SESSION['lang']['kurs'],0,0,'L'); 
                $this->Cell(40,4,": ".$barHt->kurs,0,1,'L'); 

        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }

}

/*
    print"<pre>";
        print_r($_SESSION);
        print"</pre>";
*/
        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true,20);

//ambil kelengkapan

        $pdf->Ln();
    $pdf->Ln();	
        $pdf->Cell(30,4,strtoupper($_SESSION['lang']['hal']),0,0,'L'); 
        $pdf->Ln();	
    $pdf->MultiCell(170,5,$_SESSION['lang']['isi_permintaan'],0,'L');		 		
    $pdf->Ln();
        $pdf->SetFont('Arial','B',9);	
        $pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    //$pdf->Cell(30,5,$_SESSION['lang']['kodebarang'],1,0,'C',1);
    $pdf->Cell(105,5,$_SESSION['lang']['namabarang'],1,0,'C',1);	
    $pdf->Cell(12,5,$_SESSION['lang']['satuan'],1,0,'C',1);		
    $pdf->Cell(20,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
    $pdf->Cell(25,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
    $pdf->Cell(20,5,$_SESSION['lang']['nopp'],1,1,'C',1);
        $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',9);

                $str="select * from ".$dbname.".log_permintaanhargadt 
                      where nomor='".$isi[0]."' and nourut='".$isi[1]."'"; //echo $str;
                $res=mysql_query($str);
                $no=0;
                while($bar=mysql_fetch_object($res))
                {
                        $no+=1;
                   $kodebarang=$bar->kodebarang;
                   $jumlah=$bar->jumlah;
                   $namabarang='';
				   
				   if($bar->keterangan=='')
							$keterangandt="";
						else
							$keterangandt="\nKet : ".$bar->keterangan;
                   
                   $strv="select * from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
                   $resv=mysql_query($strv);
                   while($barv=mysql_fetch_object($resv))
                   {
                        $namabarang=$barv->namabarang;
                        $satuan=$barv->satuan;
                   }
				   $a1="select spesifikasi from ".$dbname.".log_5photobarang where kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
                   $b1=mysql_query($a1);
                   $c1=mysql_fetch_assoc($b1);
                   		if($c1['spesifikasi']=='')
							$spek="";
						else
							$spek="\n Spek : ".$c1['spesifikasi'];
							
                                if($no!=1)
                                {

                                                $pdf->SetY($akhirY);

                                }
                            $posisiY=$pdf->GetY();
                            $detH=5;
                            $pdf->Cell(8,$detH,$no,0,0,'L',0);
                                $pdf->MultiCell(105,$detH,$namabarang." ".$spek." ".$keterangandt,0,'J',0);
                                $akhirY=$pdf->GetY();
                                $pdf->SetY($posisiY);
                                $pdf->SetX($pdf->GetX()+113);
                                if ($posisiY<=250){
                                    //$pdf->MultiCell(105,5,$namabarang."\n".$bar->spec,1,0,'J',1);	
                                    $pdf->Cell(12,$detH,$satuan,0,0,'L',0);	
                                    $pdf->Cell(20,$detH,number_format($bar->jumlah,2,'.',','),0,0,'R',0);
                                    if($bar->harga==0){
                                        $pdf->Cell(25,$detH,"",0,0,'R',0);
                                    }else{
                                        $pdf->Cell(25,$detH,number_format($bar->harga,2,'.',','),0,0,'R',0);		
                                    }
                                    $pdf->Cell(20,$detH,substr($bar->nopp,0,6).substr($bar->nopp,-4,4),0,0,'L',0);
                                }

                }
           $pdf->SetY($akhirY);
           $pdf->MultiCell(190,5,"Note: ".$_SESSION['lang']['note_permintaan'],'T','L');			
//footer================================
//        $pdf->Ln();
//get user;

                $pdf->Cell(45,4,$_SESSION['lang']['franco'],0,0,'L'); 
                $pdf->Cell(40,4,": ",0,1,'L');
                $pdf->Cell(45,4,$_SESSION['lang']['waktu']." ".$_SESSION['lang']['pengiriman'],0,0,'L'); 
                $pdf->Cell(40,4,": ",0,1,'L');
                $pdf->Cell(45,4,$_SESSION['lang']['syaratPem'],0,0,'L'); 
                $pdf->Cell(40,4,": ",0,1,'L');
				
				$pdf->Cell(45,4,$_SESSION['lang']['keterangan'],0,0,'L'); 
                $pdf->Cell(5,4,": ",0,0,'L');
				$pdf->MultiCell(130,4,$barHt->catatan,0,'L');
			
				
                $pdf->ln();
     if($_SESSION['language']=='EN'){
         $pdf->MultiCell(170,5,"Please explain the price conditions and tax if included. Thank you for your cooperation.",0,'L');
     }else{
        $pdf->MultiCell(170,5,"Harga harap di jelaskan secara lengkap termasuk PPn atau tidak. Terima kasih atas perhatian dan kerjasamanya.",0,'L');
     }
     $pdf->ln(); $pdf->ln();
     if($_SESSION['language']=='EN'){
                $pdf->Cell(20,4,"Regards,",0,1,'L'); 
     }else{
                 $pdf->Cell(20,4,"Salam,",0,1,'L');         
     }
                 $pdf->ln(); $pdf->ln(); $pdf->ln(); $pdf->ln();
                $pdf->Cell(20,4,$optNmkry[$barHt->purchaser],0,1,'L'); 
        $pdf->Output();
?>

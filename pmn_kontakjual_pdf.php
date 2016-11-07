<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
require_once('lib/zLib.php');
include_once('lib/zMysql.php');
include_once('lib/terbilang.php');
include_once('lib/spesialCharacter.php');


$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];


//=============
$nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$nmBrg=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$mtUang=makeOption($dbname,'setup_matauang','kode,simbol');


//============


//create Header
class PDF extends FPDF
{
        /*function Header()
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
        }*/
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','',8);
           // $this->Cell(200,10,strtoupper("member of ccm group"),0,0,'C');
        }

}

        $pdf=new PDF('P','mm','A4');
        $pdf->AddPage();
		
		




		

		#data kontrak
		$i="select * from ".$dbname.".pmn_kontrakjual where nokontrak='".$_GET['column']."' ";
		$n=mysql_query($i) or die(mysql_error($i));
		$d=mysql_fetch_assoc($n);
		
		
		/*
		if 1 = CPO dan HIP
		if 2 = CPO dan SIL
		if 3 = PK dan HIP
		if 4 = PK dan SIL
		
		#kodebarang
		CPO=40000001
		PK=40000002
		
		#kodept
		HIP
		SIL
		
		*/
		
		##if PPN
		if($d['ppn']=='0')
		{
			$ppn="tidak termasuk PPN 10%";
		}
		else
		{
			$ppn="termasuk PPN 10% ";
		}
		
		$isiKualitas=explode(' ',  $d['kualitas']);
		$ffa=$isiKualitas[0];
		$mi=$isiKualitas[1];
		
		//echo $mi;
		//$txtMuat=$d['kualitas'];
		
		
		## if untuk macem2
		if($d['kodebarang']=='40000001' && $d['kodept']=='HIP')
		{
			
			$mutu="FFA ".$ffa."% max; M&I ".$mi."% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Hardaya Inti Plantation dari campuran contoh CPO yang diambil dari bagian atas, tengah dan bawah tanki timbul bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ".$ffa."% ";
			$dasarBerat="Berat final berdasarkan data hasil sounding tangki timbun PT Hardaya Inti Plantation di Kumaligon, dengan menggunakan TABEL DENSITY yang dikeluarkan oleh Surveyor SUCOFINDO.";
		}
		else if($d['kodebarang']=='40000001' && $d['kodept']=='SIL')
		{
			$mutu="FFA ".$ffa."% max; M&I ".$mi."% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Sebakis Inti Lestari bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ".$ffa."% ";
			$dasarBerat="Berat final berdasarkan laporan hasil penimbangan di PMKS PT. Sebakis Inti Lestari, Kab. Nunukan, Kalimantan Timur.";
		}
		else if($d['kodebarang']=='40000002' && $d['kodept']=='HIP')
		{
			$mutu="FFA ".$ffa."% max; M&I total ".$mi."% max; berdasarkan hasil laboratorium PMKS PT. Hardaya Inti Plantation disaksikan dari wakil pihak Pembeli";
			$dasarBerat="";
		}
		else if($d['kodebarang']=='40000002' && $d['kodept']=='SIL')
		{
			$mutu="FFA ".$ffa."% max; M&I total ".$mi."% max; berdasarkan hasil laboratorium PMKS PT. Sebakis Inti Lestari disaksikan dari wakil pihak Pembeli";			
			$dasarBerat="";
		}

		
		$derajat="º";
		$derajat=em($derajat);
		$derajat=urldecode($derajat);
		
		if($d['kodept']=='HIP')
		{
			$harga1="Kumaligon";
			$pelmuat="Teluk Bilang, Desa Kumaligon, Buol, Sulawesi Tengah. 01".$derajat." 14' 01'' LU, dan 121".$derajat." 25' 12'' BT";
			$lokasiTtd="Buol";
			$nmPt="PT. Hardaya Inti Plantation";
			$jbtnTdd="Direktur";
		}
		else if ($d['kodept']=='SIL')
		{
			$harga1="Sebakis";
			$pelmuat="Pelabuhan Sebakis, Desa Pembilangan, Nunukan, Kalimantan Timur. 04".$derajat." 04' 59'' LU dan 117".$derajat." 16' 32'' BT";
			$lokasiTtd="Nunukan";
			$nmPt="PT. Sebakis Inti Lestari";
			$jbtnTdd="Kuasa Direksi";
		}
		$pel=explode('.',  $pelmuat);
		
		
		
		if($d['kodebarang']=='40000001')
		{
			$klaimMutu="Apabila FFA CPO di atas standard maka akan diklaim secara proporsional";
			$barang="Crude Palm Oil";
		}
		else if($d['kodebarang']=='40000002')
		{
			$klaimMutu="Apabila FFA Kernel di atas standard maka akan diklaim secara proporsional";
			$barang="Palm Kernel";
		}
		
		$pdf->Ln(30);
		$pdf->SetFont('Arial','BU','10');
		$pdf->Cell(200,5,'PERJANJIAN JUAL BELI',0,1,'C');
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(200,5,$d['nokontrak'],0,1,'C');
		$pdf->Ln();
		
		
		
		
		
		
		

		
		
		
		
		$x="select * from ".$dbname.".organisasi where kodeorganisasi='".$d['kodept']."'"; 
		$y=mysql_query($x);
		$z=mysql_fetch_assoc($y);
		//$alamat=$z['alamat']
		
			$pdf->Cell(35,5,'Penjual',0,0,'L');
			$pdf->Cell(5,5,':',0,0,'L');
			$pdf->SetFont('Arial','B','10');
			$pdf->Cell(10,5,$nmPt,0,1,'L');
					
			$pdf->SetFont('Arial','','10');
			$pdf->Cell(40,5,'',0,0,'L');
			$pdf->Cell(10,5,$z['alamat'].' '.$z['kodepos'],0,1,'L');
		
		
		$o="select * from ".$dbname.".pmn_4customer where kodecustomer='".$d['koderekanan']."'"; 
		$p=mysql_query($o);
		$q=mysql_fetch_assoc($p);
		
			$pdf->Cell(35,5,'Pembeli',0,0,'L');
			$pdf->Cell(5,5,':',0,0,'L');
			$pdf->SetFont('Arial','B','10');
			$pdf->Cell(10,5,$q['namacustomer'],0,1,'L');
					
			$pdf->SetFont('Arial','','10');
			$pdf->Cell(40,5,'',0,0,'L');
			$pdf->Cell(10,5,$q['alamat'],0,1,'L');
			$pdf->Cell(40,5,'',0,0,'L');
			$pdf->Cell(10,5,$q['kota'],0,1,'L');
			$pdf->Cell(40,5,'',0,0,'L');
			$pdf->Cell(10,5,'NPWP : '.$q['npwp'],0,1,'L');
		
		$pdf->Cell(35,5,'Jenis Minyak',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(10,5,$barang,0,1,'L');
	
		$pdf->Cell(35,5,'Kuantity',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->SetFont('Arial','B','10');
		$pdf->Cell(10,5,number_format($d['kuantitaskontrak']).' '.$d['satuan'],0,1,'L');		
		
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(35,5,'Harga',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->SetFont('Arial','B','10');
		$pdf->Cell(10,5,'FOB '.$harga1.' Rp. '.number_format($d['hargasatuan']).',- '.$d['satuan'].' '.$ppn ,0,1,'L');
		
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(35,5,'Pembayaran',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->MultiCell(125,5,$d['syratpembayaran'],0,'L');
		
		
		$pdf->Cell(35,5,'Pelabuhan Muat',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(10,5,$pel[0],0,1,'L');	
		$pdf->Cell(40,5,'',0,0,'L');
		$pdf->Cell(10,5,$pel[1],0,1,'L');	
		
		
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(35,5,'Pembayaran',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->MultiCell(125,5,$d['syratpembayaran'],0,'J');
		
		##pelabuhan bongkar
		if($d['pelabuhan']!='')
		{
			$pdf->Cell(35,5,'Pelabuhan Bongkar',0,0,'L');
			$pdf->Cell(5,5,':',0,0,'L');
			$pdf->Cell(10,5,$d['pelabuhan'],0,1,'L');	
		}
		
		
		$pdf->Cell(35,5,'Pengapalan',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->SetFont('Arial','B','10');
			$tglKirim=explode('-',  $d['tanggalkirim']);
			$tglSd=explode('-',  $d['sdtanggal']);
			$nmBlnKirim=numToMonth($tglKirim[1],'I','long');
			$nmBlnSd=numToMonth($tglSd[1],'I','long');
			$tglisiKirim=$tglKirim[2].' '.$nmBlnKirim.' '.$tglKirim[0];
			$tglisiSd=$tglSd[2].' '.$nmBlnSd.' '.$tglSd[0];
		$pdf->Cell(10,5,'Antara tanggal '.$tglisiKirim.' s.d. '.$tglisiSd,0,1,'L');	
		
		
		
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(35,5,'Lama Pemuatan',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(60,5,$d['lamamuat'].' ('.strtolower(terbilang($d['lamamuat'])).' hari)',0,0,'L');
		$pdf->Cell(10,5,'Demurage : '.$mtUang[$d['matauang']].' '.number_format($d['demurage']).' per hari.',0,1,'L');
		
		
			
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(35,5,'Mutu',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->MultiCell(125,5,$mutu,0,'J');
		
		$pdf->Cell(35,5,'Klaim Mutu',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(10,5,$klaimMutu,0,1,'L');
		
		
		if($dasarBerat!="")
		{
			$pdf->Cell(35,5,'Dasar Berat',0,0,'L');
			$pdf->Cell(5,5,':',0,0,'L');
			$pdf->MultiCell(125,5,$dasarBerat,0,'J');
		}
		
		$pdf->Ln();
		
		$tglTtd=explode('-',  $d['tanggalkontrak']);
		$nmBlnTtd=numToMonth($tglTtd[1],'I','long');
		$tglisiTtd=$tglTtd[2].' '.$nmBlnTtd.' '.$tglTtd[0];
		
		$pdf->SetX(125);
		$pdf->Cell(10,5,$lokasiTtd.', '.$tglisiTtd,0,1,'L');
		
		$pdf->Cell(10,5,'Pembeli,',0,0,'L');
		$pdf->SetX(125);
		$pdf->Cell(10,5,'Penjual,',0,1,'L');
		
		$pdf->SetFont('Arial','B','10');
		$pdf->Cell(10,5,$q['namacustomer'],0,0,'L');
		$pdf->SetX(125);
		$pdf->Cell(10,5,$nmPt,0,1,'L');
		
		
		$ysekarang=$pdf->GetY();
		$path='images/ttd_seri.jpg';
		$pdf->Image($path,125,$ysekarang,40);
		
		$pdf->Ln(30);
		
			
	
		
		
		$pdf->SetFont('Arial','BU','10');
		$pdf->Cell(10,5,$q['pk'],0,0,'L');
		$pdf->SetX(125);
		$pdf->Cell(10,5,$d['penandatangan'],0,1,'L');
		
		$pdf->SetFont('Arial','','10');
		$pdf->Cell(10,5,$q['jpk'],0,0,'L');
		$pdf->SetX(130);
		$pdf->Cell(10,5,$jbtnTdd,0,1,'L');
	
		
		//$i=html_entity_decode ( string $string [, int $quote_style = ENT_COMPAT [, string $charset = 'UTF-8' ]] );
		
		
	
		/*$pdf->Cell(35,5,'',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(10,5,$d[''],0,1,'L');		

		$pdf->Cell(35,5,'',0,0,'L');
		$pdf->Cell(5,5,':',0,0,'L');
		$pdf->Cell(10,5,$d[''],0,1,'L');		
		*/
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
        


        $pdf->Output();
?>

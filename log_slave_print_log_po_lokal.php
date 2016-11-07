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
	global $posted;
	global $tanggal;
	global $norek_sup;
	global $npwp_sup;
	global $nm_kary;
	global $nm_pt;
	global $namapt;
        global $nmSupplier;
	global $almtSupplier;
	global $tlpSupplier;
	global $faxSupplier;
	global $nopo;
	global $tglPo;
	global $kdBank;
	global $an;
	global $syartByr;
        global $optNmkry;
        $optNmkry=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
	
		$str="select kodeorg,kodesupplier,purchaser,nopo,tanggal,statusbayar from ".$dbname.".log_poht  where nopo='".$_GET['column']."'";
		//echo $str;exit();
		$res=mysql_query($str);
		$bar=mysql_fetch_object($res);
		
			//ambil nama pt
			  // $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			  if($bar->kodeorg=='')
			  {
				 $bar->kodeorg=$_SESSION['org']['kodeorganisasi']; 
			  }
			  $str1="select namaorganisasi,alamat,wilayahkota,telepon,fax from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;
				 $fax=$bar1->fax;
			   } 
	   $sNpwp="select npwp,alamatnpwp from ".$dbname.".setup_org_npwp where kodeorg='".$bar->kodeorg."'";
	  // echo"<pre>";print_r($_SESSION);echo"</pre>";echo $sNpwp;exit();
	   $qNpwp=mysql_query($sNpwp) or die(mysql_error());
	   $rNpwp=mysql_fetch_assoc($qNpwp);
	   
	   $sql="select * from ".$dbname.".log_5supplier where supplierid='".$bar->kodesupplier."'"; //echo $sql;
	   $query=mysql_query($sql) or die(mysql_error());
	   $res=mysql_fetch_object($query);
	   
	   $sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->purchaser."'";
	   $query2=mysql_query($sql2) or die(mysql_error());
	   $res2=mysql_fetch_object($query2);
	   
	   $sql3="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
	   $query3=mysql_query($sql3) or die(mysql_error());
	   $res3=mysql_fetch_object($query3); 
	
	   $norek_sup=$res->rekening;
	   $kdBank=$res->bank;
	   $npwp_sup=$res->npwp;
	   $an=$res->an;   
	   $nm_kary=$res2->namakaryawan;
	   $nm_pt=$res3->namaorganisasi;
	   //data PO
	   $nopo=$bar->nopo;
	   $tglPo=tanggalnormal($bar->tanggal);
	   //data supplier//
	   $syartByr=$bar->statusbayar;
	   $nmSupplier=$res->namasupplier;
	   $almtSupplier=$res->alamat;
	   $tlpSupplier=$res->telepon;
	   $faxSupplier=$res->fax;
	   $kota=$res->kota;
	 //  $this->SetMargins(15,10,0);
	      $this->SetMargins(15,10,0);
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
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
		
		$this->Line(15,35,205,35);	
		$this->SetFont('Arial','',6); 	
		//$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');
		//$this->SetY(70);
		
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
	$pdf->SetFont('Arial','B',8);
        
$pdf->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
$pdf->Ln();

$pdf->Cell(35,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
$pdf->Cell(40,4,": ".$nmSupplier,0,1,'L'); 				
$pdf->Cell(35,4,$_SESSION['lang']['alamat'],0,0,'L'); 
$pdf->Cell(40,4,": ".$almtSupplier,0,1,'L'); 		  
$pdf->Cell(35,4,$_SESSION['lang']['telp'],0,0,'L'); 
$pdf->Cell(40,4,": ".$tlpSupplier,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['fax'],0,0,'L'); 
$pdf->Cell(40,4,": ".$faxSupplier,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['namabank'],0,0,'L'); 
$pdf->Cell(40,4,": ".$kdBank." ".$kdBank,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['norekeningbank'],0,0,'L'); 
$pdf->Cell(40,4,": ".$an." ".$norek_sup,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['npwp'],0,0,'L'); 
$pdf->Cell(40,4,": ".$npwp_sup,0,1,'L'); 
$pdf->Cell(35,4,$_SESSION['lang']['kota'],0,0,'L'); 
$pdf->Cell(40,4,": ".$kota,0,1,'L'); 

//title
$pdf->SetFont('Arial','U',12);
$ar=round($pdf->GetY());
$pdf->SetY($ar+5);
$pdf->Cell(190,5,strtoupper("Purchase Order"),0,1,'C');		
$pdf->SetY($ar+12);

//no po + tanggal po
$pdf->SetFont('Arial','',8);		
$pdf->Cell(10,4,"No.PO",0,0,'L'); 
$pdf->Cell(20,4,": ".$nopo,0,0,'L'); 
$pdf->SetX(163);
$pdf->Cell(20,4,$_SESSION['lang']['tanggal']." PO.",0,0,'L'); 
$pdf->Cell(20,4,": ".$tglPo,0,0,'L'); 
$pdf->SetY($ar+17);

//title
$pdf->SetFont('Arial','B',8);	
$pdf->SetFillColor(220,220,220);
$pdf->Cell(8,5,'No',1,0,'L',1);
$pdf->Cell(12,5,$_SESSION['lang']['kodeabs'],1,0,'C',1);	
$pdf->Cell(60,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
$pdf->Cell(12,5,$_SESSION['lang']['nopp'],1,0,'C',1);	
$pdf->Cell(12,5,$_SESSION['lang']['untukunit'],1,0,'C',1);		
$pdf->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
$pdf->Cell(14,5,$_SESSION['lang']['satuan'],1,0,'C',1);	
$pdf->Cell(29,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
$pdf->Cell(26,5,'Total',1,1,'C',1);

$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',8);
		
$str="select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$_GET['column']."'";
//echo $str;exit();
$re=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($re))
{
    $no+=1;

    $kodebarang=$bar->kodebarang;
    $jumlah=floatval($bar->jumlahpesan);
    $harga_sat=$bar->hargasbldiskon;
    $total=$jumlah*$harga_sat;
    $unit=substr($bar->nopp,15,4);
    $namabarang='';
    $nopp=substr($bar->nopp,0,3);
    $strv="select b.spesifikasi from  ".$dbname.".log_5photobarang b  where b.kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
    $resv=mysql_query($strv);
    $barv=mysql_fetch_object($resv);

    if($barv->spesifikasi!='')
    {
        $spek=$barv->spesifikasi."\n";
    }
    else
    {
        $spek="";
    }

    $sSat="select satuan,namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
    $qSat=mysql_query($sSat) or die(mysql_error());
    $rSat=mysql_fetch_assoc($qSat);
    $satuan=$rSat['satuan'];
    $namabarang=$rSat['namabarang'];

    $i++;

    if($no!=1)
    {
        $pdf->SetY($akhirY);
    }
    $akhirY=$pdf->GetY();
    if($akhirY>=260){
        $pdf->AddPage();
        $akhirY=$pdf->GetY();;
    }    
    //no
    $pdf->Cell(8,4,$no,0,0,'L',0);
    $pdf->SetX($pdf->GetX());
    $posisiY=round($pdf->GetY());
    $pdf->Cell(12,5,substr($bar->kodebarang,0,3),0,0,'C',0);
    //nama barang
    $pdf->MultiCell(60,5," ".$namabarang."\n".$spek.$bar->catatan,0,'J',0);
    $akhirY=$pdf->GetY();

    //naik lagi kursornya
    $pdf->SetY($posisiY);
    $pdf->SetX($pdf->GetX()+82);

    //no pp + pt + jumlah + satuan + harga + total
    $pdf->Cell(12,5,$nopp,0,0,'C',0);	
    $pdf->Cell(12,5,$unit,0,0,'C',0);
    $pdf->Cell(14,5,number_format($jumlah,2,'.',','),0,0,'R',0);
    $pdf->Cell(14,5,$bar->satuan,0,0,'C',0);
    $pdf->Cell(29,5,$bar->matauang." ".number_format($harga_sat,2,'.',','),0,0,'R',0);	
    $desimal=2;

    $pdf->Cell(26,5,number_format($total,2,'.',','),0,1,'R',0);
////    if($i==15)
//    {
//        $i=0;
//        $akhirY=$akhirY-20;
//        $akhirY=$pdf->GetY()-$akhirY;
//        $akhirY=$akhirY+35;
//        //$pdf->SetY($posisiY+25);
////        $pdf->AddPage();
//    }
}
$akhirSubtot=$pdf->GetY();
$pdf->SetY($akhirY);
$slopoht="select * from ".$dbname.".log_poht where nopo='".$_GET['column']."'";

$qlopoht=mysql_query($slopoht) or die(mysql_error());
$rlopoht=mysql_fetch_object($qlopoht);
$sb_tot=$rlopoht->subtotal;
$nil_diskon=$rlopoht->nilaidiskon;
$misc=$rlopoht->misc;
$nppn=$rlopoht->ppn;
$stat_release=$rlopoht->stat_release ;
$user_release=$rlopoht->persetujuan1;
$gr_total=$rlopoht->nilaipo;
                	
if($akhirSubtot>=240){
    $pdf->AddPage();
    $akhirY=$pdf->GetY();
}
$pdf->MultiCell(134,4,"Keterangan :"."\n".$rlopoht->uraian,'T',1,'J',0);
$pdf->SetY($akhirY);
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,5,$_SESSION['lang']['subtotal'],'T',0,'L',1);	
$pdf->Cell(26,5,number_format($rlopoht->subtotal,2,'.',','),'T',1,'R',1);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,5,'Discount'." (".$rlopoht->diskonpersen."% )",0,0,'L',1);	
$pdf->Cell(26,5,number_format($rlopoht->nilaidiskon,$desimal,'.',','),0,1,'R',1);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,5,'PPh/PPn (10 %)',0,0,'L',1);	
$pdf->Cell(26,5,number_format($rlopoht->ppn,$desimal,'.',','),0,1,'R',1);
//$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);
$pdf->Cell(29,5,'Misc',0,0,'L',1);	
$pdf->Cell(26,5,number_format($rlopoht->misc,$desimal,'.',','),'',1,'R',1);
$pdf->SetFont('Arial','B',8);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX()+134);

$pdf->Cell(29,5,$_SESSION['lang']['grnd_total'],0,0,'L',1);	
$pdf->Cell(26,5,$rlopoht->matauang." ".number_format($gr_total,$desimal,'.',','),0,1,'R',1);	
if(strlen($rlopoht->uraian)>620)
{
    $tmbhBrs=80;
    $tmbhBrs2=105;
    $tmbhBrs3=75;
    $tmbhBrs5=135;
}
else
{
    $tmbhBrs=45;
    $tmbhBrs2=65;
    $tmbhBrs3=55;
    $tmbhBrs5=95;
}
# kalo terlalu ke bawah, pindah halaman aja                
if($akhirY>=175){
    $akhirY=0;
    $pdf->AddPage();
}
# $dz

$pdf->SetY($akhirY+$tmbhBrs);
$pdf->SetFont('Arial','',8);
	   $pdf->SetFont('Arial','',8);
           $pdf->Cell(126,5,$_SESSION['lang']['tgl_kirim'].": ".tanggalnormald($rlopoht->tanggalkirim),0,1,'L',0);
	   $pdf->Cell(126,5,$_SESSION['lang']['almt_kirim'].": ".$rlopoht->lokasipengiriman,0,1,'L',0); 
	   $pdf->Cell(126,5,$_SESSION['lang']['syaratPem'].": ".$syartByr." : ".$rlopoht->syaratbayar,0,1,'L',0);
	   $pdf->Cell(126,5,$_SESSION['lang']['norekeningbank'].": ".$norek_sup,0,1,'L',0);
	   $pdf->Cell(126,5,$_SESSION['lang']['npwp'].": ".$npwp_sup,0,1,'L',0);
	   $pdf->Cell(126,5,$_SESSION['lang']['purchaser'].": ".$nm_kary,0,1,'L',0);
	   
	   $pdf->Cell(190,4,$namapt,0,0,'R'); 


		
$pdf->SetY($akhirY+$tmbhBrs5);
$sPo="select persetujuan1,persetujuan2 from ".$dbname.".log_poht where nopo='".$nopo."'";
$qPo=mysql_query($sPo) or die(mysql_error($conn));
$rPo=mysql_fetch_assoc($qPo);
$pdf->SetFont('Arial','',8);
$pdf->Cell(10,4,strtoupper($_SESSION['lang']['purchaser']).": ".strtoupper($nm_kary),0,0,'L',0);
$pdf->SetFont('Arial','',8);
$sql_kry="select namakaryawan, b.namajabatan from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where a.karyawanid='".$user_release."' "; //echo $sql_kry;
//exit("error".$sql_kry);
$query_kry=mysql_query($sql_kry) or die(mysql_error());
$resv=mysql_fetch_assoc($query_kry);
$pdf->SetFont('Arial','U',9);
$pdf->Cell(180,4,strtoupper($resv['namakaryawan']),0,0,'R');
$pdf->Ln();
$pdf->SetFont('Arial','',9);
//$pdf->Cell(160,4,'Jabatan :',0,0,'R');
$pdf->Cell(190,4,$resv['namajabatan'],0,0,'R');
$akrhr=$tmbhBrs5+5;
$pdf->SetY($akhirY+$akrhr);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(10,4,$_SESSION['lang']['fyiGudang'],0,0,'L',0);

//ambil kelengkapan
//        $pdf->SetFont('Arial','',8);
//               $pdf->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
//		$pdf->Ln();
//		//$this->Cell(40,4,": ".$kodegudang,0,1,'L'); 
//		$pdf->Cell(35,4,$_SESSION['lang']['nm_perusahaan'],0,0,'L'); 
//		$pdf->Cell(40,4,": ".$nmSupplier,0,1,'L'); 				
//		$pdf->Cell(35,4,$_SESSION['lang']['alamat'],0,0,'L'); 
//		$pdf->Cell(40,4,": ".$almtSupplier,0,1,'L'); 		  
//		$pdf->Cell(35,4,$_SESSION['lang']['telp'],0,0,'L'); 
//		$pdf->Cell(40,4,": ".$tlpSupplier,0,1,'L'); 
//		$pdf->Cell(35,4,$_SESSION['lang']['fax'],0,0,'L'); 
//		$pdf->Cell(40,4,": ".$faxSupplier,0,1,'L'); 
//	
//		$ar=$pdf->GetY();
//		$pdf->SetY($ar+5);
//		$pdf->SetFont('Arial','U',12);
//		//$pdf->SetY(60);
//		$pdf->Cell(190,5,strtoupper("Purchase Order"),0,1,'C');		
//		//$this->SetFont('Arial','',15);
//	    //$this->Cell(190,5,strtoupper($_SESSION['lang']['permintaan_harga']),0,1,'C');
//		$pdf->SetY($ar+12);
//                $pdf->SetFont('Arial','',8);		
//                $pdf->Cell(10,4,"No.PO",0,0,'L'); 
//                $pdf->Cell(20,4,": ".$nopo,0,1,'L'); 
//		$pdf->SetY(77);
//                $wth=$pdf->GetX();
//		$pdf->SetX($wth+153);
//		$pdf->Cell(20,4,"Tanggal PO.",0,0,'L'); 
//		$pdf->Cell(20,4,": ".tanggalnormal($tglPo),0,0,'L');
//                $pdf->SetY($ar+17);
//            $pdf->SetFont('Arial','B',9);	
//            $pdf->SetFillColor(220,220,220);
//            $pdf->Cell(8,5,'No',1,0,'L',1);
//            $pdf->Cell(60,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
//            $pdf->Cell(20,5,$_SESSION['lang']['untukunit'],1,0,'C',1);		
//            $pdf->Cell(25,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
//            $pdf->Cell(13,5,$_SESSION['lang']['satuan'],1,0,'C',1);	
//            $pdf->Cell(15,5,$_SESSION['lang']['kurs'],1,0,'C',1);
//            $pdf->Cell(25,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
//            $pdf->Cell(25,5,'Total',1,1,'C',1);
//
//		$pdf->SetFillColor(255,255,255);
//	    $pdf->SetFont('Arial','',8);
//		
//		$str="select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$_GET['column']."'";
//		//echo $str;exit();
//		$re=mysql_query($str);
//		$no=0;
//		while($bar=mysql_fetch_object($re))
//		{
//			$no+=1;
//			
//		   $kodebarang=$bar->kodebarang;
//		   $jumlah=$bar->jumlahpesan;
//		   $harga_sat=$bar->hargasbldiskon;
//		   $total=$jumlah*$harga_sat;
//		   $unit=substr($bar->nopp,15,4);
//		   $namabarang='';
//		  /* if($bar->matauang==1)
//		  	$kr="IDR";
//			else
//		  	$kr="USD";*/
//		  /* $sMt="select kode,kodeiso from ".$dbname.".setup_matauang where kode='".$bar->matauang."'";
//		   echo $sMt;exit();
//		   $qmt=mysql_query($sMt) or die(mysql_error());
//		   $rMt=mysql_fetch_assoc($qmt);
//		   $kr=$rMt['kodeiso'];*/
//		   $strv="select b.spesifikasi from  ".$dbname.".log_5photobarang b  where b.kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
//		   $resv=mysql_query($strv);
//		   while($barv=mysql_fetch_object($resv))
//		   {
//			 if($barv->spesifikasi!='')
//			   {
//				   	$spek=$barv->spesifikasi."\n";
//			   }
//			   else
//			   {
//				   $spek="";
//			   }
//		   }
//			$sSat="select satuan,namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->kodebarang."'";
//			$qSat=mysql_query($sSat) or die(mysql_error());
//			$rSat=mysql_fetch_assoc($qSat);
//			$satuan=$rSat['satuan'];
//			$namabarang=$rSat['namabarang'];
//                        $i++;
//                         if($no!=1)
//                        {
//
//                                        $pdf->SetY($akhirY);
//
//                        }
//                        $posisiY=$pdf->GetY();
//                        $pdf->Cell(8,4,$no,0,0,'L',0);
//                        $pdf->SetX($pdf->GetX());
//                        $pdf->MultiCell(60,4,$namabarang."\n".$spek,0,'J',0);
//                        $akhirY=$pdf->GetY();
//                        $pdf->SetY($posisiY);
//                        $pdf->SetX($pdf->GetX()+68);
//                        $pdf->Cell(20,5,$unit,0,0,'C',0);
//                        $pdf->Cell(25,5,number_format($jumlah,2,'.',','),0,'R',0);
//                        $pdf->Cell(13,5,$bar->satuan,0,0,'C',0);
//                        $pdf->Cell(15,5,$bar->matauang,0,0,'C',0);			  
//                        $pdf->Cell(25,5,number_format($harga_sat,2,'.',','),0,0,'R',0);	
//                        $pdf->Cell(25,5,number_format($total,2,'.',','),0,1,'R',0);
//                        $akhirSubtot=$pdf->GetY();
//                        $spek="";	   
//                        if($i==15)
//			 {
//				$i=0;
//				$akhirY=$akhirY-20;
//				$akhirY=$pdf->GetY()-$akhirY;
//				$akhirY=$akhirY+25;
//				//$pdf->SetY($posisiY+25);
//				$pdf->AddPage();
//			 }
//		}
//		$pdf->SetY($akhirY);
//		$slopoht="select * from ".$dbname.".log_poht where nopo='".$_GET['column']."'";
//		$qlopoht=mysql_query($slopoht) or die(mysql_error());
//		$rlopoht=mysql_fetch_object($qlopoht);
//		$sb_tot=$rlopoht->subtotal;
//		$nil_diskon=$rlopoht->nilaidiskon;
//		$nppn=$rlopoht->ppn;
//		//$stat_release=$rlopoht->stat_release ;
//		$stat_release=$rlopoht->statuspo;
//		$user_release=$rlopoht->persetujuan1;
//		$gr_total=($sb_tot-$nil_diskon)+$nppn;
//	/*	$pdf->MultiCell(126,50,$_SESSION['lang']['tgl_kirim'].": ".tanggalnormald($rlopoht->tanggalkirim).$pdf->Ln('4').
//		$_SESSION['lang']['almt_kirim'].": ".$rlopoht->lokasipengiriman.$_SESSION['lang']['norekeningbank'].": ".$norek_sup,'1',0,'L');*/
//
//	   $pdf->MultiCell(141,4,"Urian :"."\n".$rlopoht->uraian,'TR',1,'J',1);
//	   $pdf->SetY($akhirY);
//	   $pdf->SetX($pdf->GetX()+141);
//	   $pdf->Cell(25,5,$_SESSION['lang']['subtotal'],'L',0,'R',1);	
//	   $pdf->Cell(25,5,number_format($rlopoht->subtotal,2,'.',','),0,1,'R',1);
//	  // $pdf->Cell(65,5,'',1,1,'C',1);	
//	  // $pdf->Cell(96,5,": ".tanggalnormald($rlopoht->tanggalkirim),0,0,'L');
//	   $pdf->SetY($pdf->GetY());
//	   $pdf->SetX($pdf->GetX()+141);
//	   $pdf->Cell(25,5,'Discount'." (".$rlopoht->diskonpersen."% )",'L',0,'R',1);	
//	   $pdf->Cell(25,5,number_format($rlopoht->nilaidiskon,2,'.',','),0,1,'R',1);
//	   $pdf->SetY($pdf->GetY());
//	   $pdf->SetX($pdf->GetX()+141);
//	   $pdf->Cell(25,5,'PPh/PPn (10 %)','LB',0,'R',1);	
//	   $pdf->Cell(25,5,number_format($rlopoht->ppn,2,'.',','),'B',1,'R',1);
//	   $pdf->SetFont('Arial','B',8);    
//           $pdf->SetY($pdf->GetY());
//           $pdf->SetX($pdf->GetX()+141);
//	   $pdf->Cell(25,5,$_SESSION['lang']['grnd_total'],0,0,'R',1);	
//	   $pdf->Cell(25,5,number_format($gr_total,2,'.',','),0,1,'R',1);	
//	   $pdf->Ln();
//	   $pdf->SetFont('Arial','',8);
//           $pdf->Cell(126,5,$_SESSION['lang']['tgl_kirim'].": ".tanggalnormald($rlopoht->tanggalkirim),0,1,'L',0);
//	   $pdf->Cell(126,5,$_SESSION['lang']['almt_kirim'].": ".$rlopoht->lokasipengiriman,0,1,'L',0); 
//	   $pdf->Cell(126,5,$_SESSION['lang']['syaratPem'].": ".$syartByr." : ".$rlopoht->syaratbayar,0,1,'L',0);
//	   $pdf->Cell(126,5,$_SESSION['lang']['norekeningbank'].": ".$norek_sup,0,1,'L',0);
//	   $pdf->Cell(126,5,$_SESSION['lang']['npwp'].": ".$npwp_sup,0,1,'L',0);
//	   $pdf->Cell(126,5,$_SESSION['lang']['purchaser'].": ".$nm_kary,0,1,'L',0);
//	   $pdf->Ln();
//	   $pdf->Ln();
//	   $pdf->Cell(190,4,$namapt,0,0,'R'); 
//       //$pdf->MultiCell(170,5,"Note: ".$_SESSION['lang']['note_permintaan'],0,'L');			
////footer================================
//        $pdf->Ln();
//		$pdf->Ln();
//		$pdf->Ln();
//		$pdf->Ln();
//		$pdf->Ln();
//		$pdf->Ln();
//		
//
//                $sql_kry="select namakaryawan, b.namajabatan from ".$dbname.".datakaryawan a inner join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where a.karyawanid='".$user_release."' "; //echo $sql_kry;
//                $query_kry=mysql_query($sql_kry) or die(mysql_error());
//                $resv=mysql_fetch_assoc($query_kry);
//                $pdf->SetFont('Arial','U',9);
//                $pdf->Cell(190,4,strtoupper($resv['namakaryawan']),0,0,'R');
//                $pdf->Ln();
//                $pdf->SetFont('Arial','',9);
//                //$pdf->Cell(160,4,'Jabatan :',0,0,'R');
//                $pdf->Cell(190,4,$resv['namajabatan'],0,0,'R');
//                $pdf->Ln();
//                $pdf->Ln();
//                $pdf->SetFont('Arial','I',8);
//                $pdf->Cell(190,4,$_SESSION['lang']['fyiGudang'],0,0,'L',0);
//		
	$pdf->Output();
?>

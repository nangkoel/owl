<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
$periode=$_POST['periodeBeli'];



switch($proses)
{
	case'preview':
	echo"<table cellspacing=1 border=0 class=sortable><thead><tr class=rowheader>
	<td>".$_SESSION['lang']['nm_perusahaan']."</td>
	<td>".$_SESSION['lang']['namasupplier']."</td>
	<td>".$_SESSION['lang']['tanggalRelease']."</td>
	<td>".$_SESSION['lang']['nopo']."</td>
	<td>".$_SESSION['lang']['subtotal']."</td>
	<td>".$_SESSION['lang']['nilaippn']."</td>
	<td>".$_SESSION['lang']['grnd_total']."</td>
	</tr></thead><tbody>";
	$sPembelian="select * from ".$dbname.".log_poht where tglrelease like '%".$periode."%'";
	//echo $sSlip;
	$qPembelian=mysql_query($sPembelian) or die(mysql_error());
	$row=mysql_num_rows($qPembelian);
	if($row>0)
	{
		while($rPembelian=mysql_fetch_assoc($qPembelian))
		{
			if(strlen($rPembelian['kodeorg'])==1)
			{
				$kdOrg=substr($rPembelian['nopo'],-3);
			}
			else
			{		
				$kdOrg=$rPembelian['kodeorg'];
			}
			$sComp="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
			//echo $sComp;
			$qComp=mysql_query($sComp) or die(mysql_error());
			$rComp=mysql_fetch_assoc($qComp);
			
			$sSupplier="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rPembelian['kodesupplier']."'";
			$qSupplier=mysql_query($sSupplier) or die(mysql_error());
			$rSupplier=mysql_fetch_assoc($qSupplier);
			$test="noPo"."##".$rPembelian['nopo'];
//			onclick=\"masterPDF('log_poht','".$rPembelian['nopo']."','','log_slave_print_log_po',event);\"
			echo"<tr class=rowcontent onclick=\"zPdfInputan('log_slave_2alokasibiayaPembelian','".$test."','printContainer')\" title=Click>
			<td>".$rComp['namaorganisasi']."</td>
			<td>".$rSupplier['namasupplier']."</td>
			<td>".tanggalnormal($rPembelian['tglrelease'])."</td>
			<td>".$rPembelian['nopo']."</td>
			<td align=right>".number_format($rPembelian['subtotal'],2)."</td>
			<td align=right>".number_format($rPembelian['ppn'],2)."</td>
			<td align=right>".number_format($rPembelian['nilaipo'],2)."</td>
			</tr>";		
		}
	}
	else
	{
		echo"<tr class=rowcontent><td colspan=7 align=center>Not Found</td></tr>";
	}
		echo"</tbody></table>";
	break;
	case'pdf':
	$nopo=$_GET['noPo'];
	
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
	global $nopo;

	
		$str="select * from ".$dbname.".log_poht  where nopo='".$nopo."'";
		//echo $str;exit();
		$res=mysql_query($str);
		$bar=mysql_fetch_object($res);
		
			//ambil nama pt
			   $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   } 
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
	   $npwp_sup=$res->npwp;   
	   $nm_kary=$res2->namakaryawan;
	   $nm_pt=$res3->namaorganisasi;
		if($_SESSION['org']['kodeorganisasi']=='HIP'){  $path='images/hip_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIL'){  $path='images/sil_logo.jpg'; } else if($_SESSION['org']['kodeorganisasi']=='SIP'){  $path='images/sip_logo.jpg'; }
	    $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$namapt,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->Cell(30,4,"KEPADA YTH :",0,0,'L'); 
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
	
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial','U',15);
		$this->SetY(60);
		$this->Cell(190,5,strtoupper("Purchase Order"),0,1,'C');		
		//$this->SetFont('Arial','',15);
	    //$this->Cell(190,5,strtoupper($_SESSION['lang']['permintaan_harga']),0,1,'C');
		$this->SetFont('Arial','',6); 
		$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,27,200,27);	
		$this->SetY(70);
	    $this->SetFont('Arial','',9);		
		$this->Cell(10,4,"No.",0,0,'L'); 
		$this->Cell(20,4,": ".$bar->nopo,0,1,'L'); 
		$this->SetY(70);
		$this->SetX(145);
		$this->Cell(20,4,"Tanggal PO.",0,0,'L'); 
		$this->Cell(20,4,": ".tanggalnormal($bar->tanggal),0,1,'L'); 
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
    
	$pdf->Ln();
   // $pdf->Ln();	
	//$pdf->Cell(30,4,strtoupper($_SESSION['lang']['hal']),0,0,'L'); 
	//$pdf->Ln();	
  //  $pdf->MultiCell(170,5,$_SESSION['lang']['isi_permintaan'],0,'L');		 		
  //  $pdf->Ln();
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    $pdf->Cell(60,5,$_SESSION['lang']['namabarang'],1,0,'C',1);
    $pdf->Cell(30,5,$_SESSION['lang']['spesifikasi'],1,0,'C',1);		
    $pdf->Cell(15,5,$_SESSION['lang']['jumlah'],1,0,'C',1);	
  	$pdf->Cell(10,5,$_SESSION['lang']['satuan'],1,0,'C',1);	
	$pdf->Cell(20,5,$_SESSION['lang']['kurs'],1,0,'C',1);
	$pdf->Cell(25,5,$_SESSION['lang']['hargasatuan'],1,0,'C',1);
	$pdf->Cell(25,5,'Total',1,1,'C',1);

		$pdf->SetFillColor(255,255,255);
	    $pdf->SetFont('Arial','',9);
		
		$str="select * from ".$dbname.".log_podt a inner join ".$dbname.".log_poht b on a.nopo=b.nopo  where a.nopo='".$nopo."'"; //echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($bar=mysql_fetch_object($re))
		{
			$no+=1;
			
		   $kodebarang=$bar->kodebarang;
		   $jumlah=$bar->jumlahpesan;
		   $harga_sat=$bar->hargasbldiskon;
		   $total=$jumlah*$harga_sat;
		   $namabarang='';
		   if($bar->matauang==1)
		  	$kr="IDR";
			else
		  	$kr="USD";
		   $strv="select * from ".$dbname.".log_5masterbarang a left join ".$dbname.".log_5photobarang b on a.kodebarang=b.kodebarang 
		   		  left join ".$dbname.".log_5stkonversi c on b.kodebarang=c.kodebarang where a.kodebarang='".$bar->kodebarang."'"; //echo $strv;exit();	
		   $resv=mysql_query($strv);
		   while($barv=mysql_fetch_object($resv))
		   {
		   	$namabarang=$barv->namabarang;
			$satuan=$barv->satuankonversi;
			$spek=$barv->spesifikasi;
		   }
			    $pdf->Cell(8,5,$no,1,0,'L',1);
			    $pdf->Cell(60,5,$namabarang,1,0,'L',1);
			    $pdf->Cell(30,5,$spek,1,0,'L',1);	
				$pdf->Cell(15,5,number_format($jumlah,2,'.',','),1,0,'R',1);	
			    $pdf->Cell(10,5,$satuan,1,0,'L',1);	
				$pdf->Cell(20,5,$kr,1,0,'C',1);			
				$pdf->Cell(25,5,number_format($harga_sat,2,'.',','),1,0,'C',1);	
				$pdf->Cell(25,5,number_format($total,2,'.',','),1,1,'R',1);		
			    	   
		}
		$slopoht="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
		$qlopoht=mysql_query($slopoht) or die(mysql_error());
		$rlopoht=mysql_fetch_object($qlopoht);
		$sb_tot=$rlopoht->subtotal;
		$nil_diskon=$rlopoht->nilaidiskon;
		$nppn=$rlopoht->ppn;
		$stat_release=$rlopoht->stat_release ;
		$user_release=$rlopoht->useridreleasae;
		$gr_total=($sb_tot-$nil_diskon)+$nppn;
	   $pdf->Cell(168,5,$_SESSION['lang']['subtotal'],1,0,'C',1);	
	   $pdf->Cell(25,5,number_format($rlopoht->subtotal,2,'.',','),1,1,'R',1);
	   $pdf->Cell(168,5,'Discount(%)',1,0,'C',1);	
	   $pdf->Cell(25,5,$rlopoht->diskonpersen,1,1,'R',1);
	   $pdf->Cell(168,5,'PPh/PPn(%)',1,0,'C',1);	
	   $pdf->Cell(25,5,number_format($rlopoht->ppn,2,'.',','),1,1,'R',1);
	   $pdf->Cell(168,5,$_SESSION['lang']['grnd_total'],1,0,'C',1);	
	   $pdf->Cell(25,5,number_format($gr_total,2,'.',','),1,1,'R',1);	
	   $pdf->Ln();
	   $pdf->Cell(30,4,$_SESSION['lang']['tgl_kirim'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".tanggalnormald($rlopoht->tanggalkirim),0,1,'L');
	   $pdf->Cell(30,4,$_SESSION['lang']['almt_kirim'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".$rlopoht->lokasipengiriman,0,1,'L');
	   $pdf->Cell(30,4,$_SESSION['lang']['syaratPem'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".$rlopoht->syaratbayar,0,1,'L');
	    $pdf->Cell(30,4,$_SESSION['lang']['norekeningbank'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".$norek_sup,0,1,'L');
	    $pdf->Cell(30,4,$_SESSION['lang']['npwp'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".$npwp_sup,0,1,'L');
	    $pdf->Cell(30,4,$_SESSION['lang']['purchaser'],0,0,'L'); 
	   $pdf->Cell(40,4,": ".$nm_kary,0,1,'L');
	   $pdf->Ln();
	   $pdf->Ln();
	   $pdf->Cell(193,4,$nm_pt,0,0,'R'); 
       //$pdf->MultiCell(170,5,"Note: ".$_SESSION['lang']['note_permintaan'],0,'L');			
//footer================================
        $pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		$pdf->Ln();
		if(($stat_release=='0')&&($user_release=='0000000000'))
		{
			$pdf->SetFont('Arial','U',9);
			$pdf->Cell(187,4,'UNRELEASE PO, Please Contact Your Purchasing Manager',0,0,'R');
			$pdf->Ln();
			$pdf->SetFont('Arial','',9);
			//$pdf->Cell(187,4,'( .......................................... )',0,0,'R');
			//$pdf->Cell(160,4,'Unrelease PO',0,0,'R');
	
		}
		else
		{
			$pdf->Cell(187,4,'( .......................................... )',0,0,'R');
			$pdf->Ln();
			$pdf->Cell(160,4,'Jabatan :',0,0,'R');
		}
//get user;
/*		$namakaryawan=namakaryawan($dbname,$conn,$userid);		
		$pdf->Cell(20,4,$_SESSION['lang']['dbuat_oleh'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$namakaryawan,0,1,'L'); 
*/
//get posted by
      /* if($posted!='')
	      $posted=namakaryawan($dbname,$conn,$posted);		
	   else
	      $posted='';
	   	$pdf->Cell(20,4,$_SESSION['lang']['posted'],0,0,'L'); 
		$pdf->Cell(40,4,": ".$posted,0,1,'L');*/		
	$pdf->Output();

	break;
	case'excel':
	$periode=$_GET['periodeBeli'];
	$strx="select * from ".$dbname.".log_poht where tglrelease like '%".$periode."%'";
						
			$stream.="
			<table>
			<tr><td colspan=8 align=center>".$_SESSION['lang']['list']." ".$_SESSION['lang']['pembelianBarang']."</td></tr>
			<tr><td colspan=8 align=center>Periode : ".$periode."</td></tr>
			</table>
			<table border=1>
			<tr>
				<td bgcolor=#DEDEDE align=center >No.</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['nm_perusahaan']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['namasupplier']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['tanggalRelease']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['nopo']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['subtotal']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['nilaippn']."</td>
				<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['grnd_total']."</td>";
				$stream.="</tr>";
				
	$query=mysql_query($strx) or die(mysql_error());
	$row=mysql_num_rows($query);
	if($row>0)
	{
		while($res=mysql_fetch_assoc($query))
		{
			$no+=1;
			if(strlen($res['kodeorg'])==1)
			{
				$kdOrg=substr($res['nopo'],-3);
			}
			else
			{		
				$kdOrg=$res['kodeorg'];
			}
			$sComp="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
			//echo $sComp;
			$qComp=mysql_query($sComp) or die(mysql_error());
			$rComp=mysql_fetch_assoc($qComp);
			
			$sSupplier="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$res['kodesupplier']."'";
			$qSupplier=mysql_query($sSupplier) or die(mysql_error());
			$rSupplier=mysql_fetch_assoc($qSupplier);
			$stream.="<tr>
				<td>".$no."</td>
				<td>".$rComp['namaorganisasi']."</td>
				<td>".$rSupplier['namasupplier']."</td>
				<td>".tanggalnormal($res['tglrelease'])."</td>
				<td>".$res['nopo']."</td>
				<td align=right>".$res['subtotal']."</td>
				<td align=right>".$res['ppn']."</td>
				<td align=right>".$res['nilaipo']."</td>";
		}
	}
	else
	{
		$stream.="<tr><td colpsan=8>Not Found</td></tr>";			
	}
			

			//echo "warning:".$strx;
			//=================================================

			
			$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
			
			$nop_="PembelianBarang";
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
?>
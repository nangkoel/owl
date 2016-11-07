<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];

//echo"gudang :".$gudang.", periode: ".$periode.", mulai: ".$tanggalmulai.", sampai: ".$tanggalsampai;

$str="select distinct tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg = '".$gudang."' and periode = '".$periode."'";
$res=mysql_query($str);
if($periode==''){
	echo "Warning: silakan mengisi periode"; exit;
}
while($bar=mysql_fetch_object($res))
{
	$tanggalmulai=$bar->tanggalmulai;
	$tanggalsampai=$bar->tanggalsampai;
}	

$str="select distinct kodebarang, namabarang from ".$dbname.".log_5masterbarang";
$res=mysql_query($str);
$optper="";
while($bar=mysql_fetch_object($res))
{
	$barang[$bar->kodebarang]=$bar->namabarang;
}	
	
if($periode=='')
	$str="select a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan, nopo 
		  from ".$dbname.".log_transaksi_vw a
		  left join ".$dbname.".log_5supplier b on a.idsupplier=b.supplierid
		  where a.kodegudang='".$gudang."' and a.tipetransaksi=1 
		  order by a.tanggal";
else
	$str="select a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan, nopo 
		  from ".$dbname.".log_transaksi_vw a
		  left join ".$dbname.".log_5supplier b on a.idsupplier=b.supplierid
		  where a.kodegudang='".$gudang."' and a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and a.tipetransaksi=1 
		  order by a.tanggal";

//echo"str :".$str;
//=================================================
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		$stream.=$_SESSION['lang']['hutangsupplierbpb'].": ".$gudang." : ".$periode."<br>
		<table border=1>
				    <tr>
			  <td bgcolor=#DEDEDE align=center>No.</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodesupplier']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nopo']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['hargasatuan']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['total']."</td>
					</tr>";
	while($bar=mysql_fetch_object($res))
	{
		$no+=1; $total=0;
//		$kodebarang=$bar->kodebarang;
//		$namabarang=$bar->namabarang; 


//		$salakqty	=$bar->salakqty;
//		$masukqty	=$bar->masukqty;
//		$keluarqty	=$bar->keluarqty;
//		$sawalQTY	=$bar->sawalqty;
			$total=$bar->jumlah*$bar->hargasatuan;
			  
		$stream.="<tr>
				  <td align=right>".$no."</td>
				  <td>".$bar->tanggal."</td>
				  <td align=right>".$bar->kodebarang."</td>
				  <td>".$barang[$bar->kodebarang]."</td>
				  <td align=right>".number_format($bar->jumlah)."</td>
				  <td>".$bar->satuan."</td>
				  <td align=right>".$bar->idsupplier."</td>
				  <td>".$bar->namasupplier."</td>
				  <td>".$bar->nopo."</td>
				  <td align=right>".number_format($bar->hargasatuan)."</td>
				  <td align=right>".number_format($total)."</td>
			</tr>"; 	
			$gtotal+=$total;	
	}
				$stream.="<tr class=rowheader>
				  <td colspan=9 align=right>TOTAL</td>
				  <td align=right>".number_format($gtotal)."</td>
				</tr>";

	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }
	
$nop_="HutangSupplier";
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
?>
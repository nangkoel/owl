<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];

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
	
//echo"gudang :".$gudang.", periode: ".$periode.", mulai: ".$tanggalmulai.", sampai: ".$tanggalsampai;

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
		echo"<tr class=rowcontent><td colspan=17>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$no+=1; $total=0;
//			$periode=date('Y-m-d H:i:s');
//			$kodebarang=$bar->kodebarang;
//			$namabarang=$bar->namabarang; 
//			$kuantitas =$bar->kuan;
//			echo"<tr class=rowcontent  class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarangHarga(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
			$total=$bar->jumlah*$bar->hargasatuan;
			echo"<tr class=rowcontent>
				  <td align=right>".$no."</td>
				  <td>".tanggalnormal($bar->tanggal)."</td>
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
			echo"<tr class=rowheader>
				  <td colspan=9 align=right>TOTAL</td>
				  <td align=right>".number_format($gtotal)."</td>
				</tr>";
	}
?>
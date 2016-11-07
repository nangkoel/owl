<?php
include('connection.php');
include('function/functions.php');
$ip=$_POST['ip'];
$id=$_POST['id'];

$str="select * from wbridge.mstrxtbs where id=".$id." and TRANSACTIONTYPE=0 and OUTIN=0 and UNITCODE!='' and PRODUCTCODE='40000003' and BPS='0'";
$res=mysql_query($str);
	 $mill='';
	 $no_wb=''; 
	 $unit='';
	 $no_spb=''; 
	 $product='';
	 $tanggal='';
	 $kendaraan=''; 
	 $jjg='';
	 $berondolan='';
	 $supir='';
	 $berat='';
	 $trp_code='';

while($barx=mysql_fetch_object($res))	
{
	 $mill=$barx->MILLCODE;
	 $no_wb=$barx->TICKETNO2;
	 $unit=$barx->UNITCODE;
	 $no_spb=$barx->SPBNO; 
	 $product=$barx->PRODUCTCODE;
	 $tanggal=substr($barx->DATEOUT,0,10);
	 $kendaraan=$barx->VEHNOCODE; 
	 $jjg=$barx->JMLHJJG;
	 $trp_code=substr($barx->TRPCODE,0,6);
	 $berondolan=$barx->BRONDOLAN;
	 $supir=substr($barx->DRIVER,0,29);
	 $berat=$barx->NETTO;
}	
	 
if($mill!='' && $no_wb!='' && $unit!='' && $no_spb!='' && $product!='' && $tanggal!='' && $kendaraan!='' && $jjg!='' && $supir!='' && $berat!='' && $trp_code!='')
 {
 	$strc="insert into ".$remote_database.".spb (
     UNIT_CODE,MILL,PRODUCT_CODE,NO_SPB,TANGGAL_SPB,
	 NO_WB,KODE_KEND_ALAT,JANJANG,KG_WB,TRPCODE,NAMA_SUPIR
	 ) values(
	 '".$unit."','".$mill."','".$product."','".$no_spb."',
	 '".$tanggal."','".$no_wb."','".$kendaraan."',".$jjg.",
	  ".$berat.",'".$trp_code."','".$supir."'
	 )";

   $conn_remote = mysql_connect($ip.':'.$port, $remote_uname, $remote_password);
   if(mysql_query($strc))
   {
   	 $con=mysql_connect($host,$uname,$pwd);
	 $strg="update wbridge.mstrxtbs set BPS='".date('Ymd H')."' where id=".$id;
	 mysql_query($strg);
	 //echo "Berhasil";
   }
   else
   {
   	 echo " Gagal :".mysql_error($conn_remote);
   }	 
 }
else
{
	echo " Gagal, data pada baris tersebut tidak lengkap";
	//echo $mill.",".$no_wb.",".$unit.",".$no_spb.",".$product.",".$tanggal.",".$kendaraan.",".$jjg.",".$supir.",".$berat.",".$trp_code." Gagal";
} 
?>


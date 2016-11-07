<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi	=$_POST['notransaksi'];
$tanggal		=tanggalsystem($_POST['tanggal']);
$jlhbbm			=$_POST['jlhbbm'];
$method     	=$_POST['method'];
$totalharga	=$_POST['totalharga'];
			
if($method=='delete')
{
$tanggal=$_POST['tanggal'];
	$str="delete from ".$dbname.".sdm_penggantiantransportdt where
	 notransaksi='".$notransaksi."' and tanggal='".$tanggal."'";
	//ambil nilai solar yang diambil 
	$stru="select hargatotal from  ".$dbname.".sdm_penggantiantransportdt where
	 notransaksi='".$notransaksi."' and tanggal='".$tanggal."'";
	$resf=mysql_query($stru);
    while($baru=mysql_fetch_object($resf))
    {
	  $totalharga=$baru->hargatotal;
	}	 
	 //create string untuk meubah nilai claim pada header
	$str1="update  ".$dbname.".sdm_penggantiantransport set totalklaim=(totalklaim-".$totalharga.") where
	 notransaksi='".$notransaksi."'";
	
}			
else if($method=='insert')
{
	$str="insert into ".$dbname.".sdm_penggantiantransportdt 
	      (`notransaksi`,`tanggal`,`jlhbbm`,`hargatotal`)
		  values(
		   '".$notransaksi."',".$tanggal.",".$jlhbbm.",".$totalharga.")";
	$str1="update  ".$dbname.".sdm_penggantiantransport set totalklaim=(totalklaim+".$totalharga.") where
	 notransaksi='".$notransaksi."'";   
}
else
{
	$str="select 1=1";
	$str1=$str;
}

if(mysql_query($str))
{
	//jika berhasil semua
	if(mysql_query($str1))
	{
	$str="select * from ".$dbname.".sdm_penggantiantransportdt
	      where notransaksi='".$notransaksi."'";
	$res=mysql_query($str);
	$no=0;
	$tkuantitas=0;
	$tharga=0;
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		     <td>".$no."</td>
			 <td>".tanggalnormal($bar->tanggal)."</td>
			 <td align=right>".number_format($bar->jlhbbm,2,'.',',')."</td>
			 <td align=right id='x".$no."'>".number_format($bar->hargatotal,2,'.',',')."</td>
			 <td><img src='images/application/application_delete.png' class=resicon onclick=\"deleteSolar('".$bar->notransaksi."','".$bar->tanggal."','x".$no."');\"></td>
		   </tr>";
		$tkuantitas+= $bar->jlhbbm;
		$tharga+=  $bar->hargatotal;	
	}
echo"<tr class=rowcontent>
     <td></td>
	 <td>".$_SESSION['lang']['total']."</td>
	 <td align=right>".number_format($tkuantitas,2,'.',',')."</td>
	 <td align=right>".number_format($tharga,2,'.',',')."</td>
	 <td>-</td>
   </tr>";	
}
else//jika tidak berhasil maka BBM dihapus dan harus input ulang
{
   $strx="delete from ".$dbname.".sdm_penggantian transportdt where
	 notransaksi='".$notransaksi."'";
   mysql_query($strx);
   echo $str1;
   echo "Error: Inconsistence calculation on Detail transaction, please re-input again";
   exit(0);   
}   
	
}
else
{
	echo " Gagal ".addslashes(mysql_error($conn));
}
?>
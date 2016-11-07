<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi=$_POST['notransaksi'];
$tanggal=tanggalsystem($_POST['tanggal']);
$jenisby=$_POST['jenisby'];
$keterangan=$_POST['keterangan'];
$jumlah=$_POST['jumlah']; 
$method=$_POST['method'];


if($jumlah=='')
  $jumlah=0;

if($method=='insert')
{

	$str="insert into ".$dbname.".sdm_pjdinasdt (
		  `notransaksi`,`jenisbiaya`,`keterangan`,
		  `tanggal`,`jumlah`
		  ) values(
				'".$notransaksi."',".$jenisby.",'".trim($keterangan)."',
				".$tanggal.",".$jumlah." 
		  )";
	if(mysql_query($str))
	{
		$str="update ".$dbname.".sdm_pjdinasht set tglpertanggungjawaban=".date('Ymd')."
		      where notransaksi='".$notransaksi."'";
        mysql_query($str);
	}	
	else{
   		echo " Gagal:".addslashes(mysql_error($conn));	  
	    exit(0);
	}
}
else if($method=='delete')
{
	$str="delete from ".$dbname.".sdm_pjdinasdt
	      where jenisbiaya=".$jenisby." and notransaksi='".$notransaksi."'
		  and tanggal=".$tanggal." and jumlah=".$jumlah." and keterangan='".$keterangan."'"; 
		 
	if(mysql_query($str))
		{}
	else
   		{
   			echo " Gagal:".addslashes(mysql_error($conn));	 
		 exit(0);
		}
}

$str="select a.*,b.keterangan as jns from ".$dbname.".sdm_pjdinasdt a
      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id
	  where a.notransaksi='".$notransaksi."' order by tanggal,jns";
$res=mysql_query($str);
$no=0;
$total=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	echo"<tr class=rowcontent>
	     	<td>".$no."</td>
                    <td nowrap>".tanggalnormal($bar->tanggal)."</td>
		    <td>".$bar->jns."</td>
			<td>".$bar->keterangan."</td>
			<td align=right>".number_format($bar->jumlah,2,'.','.')."</td>
		    <td><img src='images/close.png' class=resicon onclick=\"deleteDetail('".$bar->notransaksi."','".$bar->jenisbiaya."','".tanggalnormal($bar->tanggal)."','".$bar->jumlah."','".$bar->keterangan."')\" title='delete'></td>
			</tr>";
	$total+=$bar->jumlah;		
}
	echo"<tr class=rowcontent>
	     	<td colspan=4>TOTAL</td>
			<td align=right>".number_format($total,2,'.','.')."</td>
		    <td></td>
			</tr>";

?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');

$txtfind		=$_POST['txtfind'];

$str=" select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%' limit 12";

  if($res=mysql_query($str))
  {
  	echo"<table class=data cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		 <td class=firsttd>
		 No.
		 </td>
		 <td>Kode.Kelompok</td>
		 <td>Kode Barang</td>
		 <td>Nama Barang</td>
		 <td>Satuan</td>
		 </tr>
		 </thead>
		 <tbody>";
	$no=0;	 
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setKodeBarang('".$bar->kelompokbarang."','".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >
		      <td class=firsttd>".$no."</td>
		      <td>".$bar->kelompokbarang."</td>
			  <td>".$bar->kodebarang."</td><td>".$bar->namabarang."</td>
			  <td>".$bar->satuan."</td>
			 </tr>";
	}	 
	echo "</tbody>
	      <tfoot>
		  </tfoot>
		  </table>";	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>
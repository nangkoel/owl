<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

		$txtfind=$_POST['txtfind'];
		$str="select * from ".$dbname.".log_prapoht where nopp like '%".$txtfind."%' and close='2'";
		/*"SELECT a.*, b.*, c.harga FROM  ".$dbname.".log_prapodt a INNER JOIN ".$dbname.".log_5masterbarang b ON a.kodebarang = b.kodebarang LEFT JOIN ".$dbname.".log_5masterbarangdt c ON b.kodebarang = c.kodebarang WHERE a.nopp like '%".$txtfind."%' and limit 12";// echo $str; exit();*/
		 if($res=mysql_query($str))
		  {

		  
			echo"<table class=data cellspacing=1 cellpadding=2  border=0>
				 <thead>
				 <tr class=rowheader>
				 <td class=firsttd>
				 No.
				 </td>
				 <td>No. PP</td>
				 <td>Kode Barang</td>
				 <td>Nama Barang</td>
				 <td>Jumlah Diminta</td>
				 </tr>
				 </thead>
				 <tbody>";
			$no=0;	 
			while($bar=mysql_fetch_object($res))
			{
			  	//query detail pp
				$sql="select * from ".$dbname.".log_prapodt where nopp='".$bar->nopp."'";
				$query=mysql_query($sql) or die(mysql_error());
				$res2=mysql_fetch_object($query);
				
				//get data dari log_5masterbarang, master barang
				$sql2="select * from ".$dbname.".log_5masterbarang where kodebarang='".$res2->kodebarang."'";
				$query2=mysql_query($sql2) or die(mysql_error());
				$res3=mysql_fetch_object($query2);
				
				//get data dari log_5masterbarangdt, master barang detail
				$sql3="select * from ".$dbname.".log_5masterbarangdt where kodebarang='".$res3->kodebarang."'";
				$query3=mysql_query($sql3) or die(mysql_error());
				$res4=mysql_fetch_object($query3);
				
				$no+=1;
				echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setPp('".$bar->nopp."','".$bar->kodebarang."','".$bar->namabarang."','".$bar->jumlah."','".$bar->satuan."')\" title='Click' >
					  <td class=firsttd>".$no."</td>
					   <td>".$bar->nopp."</td>
					  <td>".$bar->kodebarang."</td>
					  <td>".$bar->namabarang."</td>
					  <td>".$bar->jumlah."</td>
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
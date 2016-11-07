<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
//=============================================

      //  echo " Error:".$_POST['induk'];
		$txtcari=$_POST['txtcari'];
		$str="select a.kodebarang,a.namabarang,a.satuan from
		      ".$dbname.".log_5masterbarang a where a.namabarang like '%".$txtcari."%' order by a.namabarang";
		$res=mysql_query($str);
		if(mysql_num_rows($res)<1)
		{
			echo"Error: ".$_SESSION['lang']['tidakditemukan'];			
		}
		else
		{
		echo"<table class=sortable cellspacing=1 border=0>
		     <thead>
			      <tr class=rowheader>
				      <td>No</td>
					  <td>".$_SESSION['lang']['kodebarang']."</td>
					  <td>".$_SESSION['lang']['namabarang']."</td>
					  <td>".$_SESSION['lang']['satuan']."</td>
				  </tr>
		     </thead>
			 <tbody>";
			$no=0;	 
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"loadField('".$bar->kodebarang."');\">
				   <td>".$no."</td>
				  <td>".$bar->kodebarang."</td>
				  <td>".$bar->namabarang."</td>
				  <td>".$bar->satuan."</td>
			      </tr>";			   	
			}
		echo    "
				 </tbody>
				 <tfoot></tfoot>
				 </table>";	
		}  

?>
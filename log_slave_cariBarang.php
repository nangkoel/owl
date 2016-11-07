<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');
//=============================================

if(isTransactionPeriod())//check if transaction period is normal
{
      //  echo " Error:".$_POST['induk'];
		$txtcari=$_POST['txtcari'];
		$gudang=$_POST['gudang'];
                $tanggal=explode("-",$_POST['tanggal']);
		$pemilikbarang=$_POST['pemilikbarang'];
		$str="select a.kodebarang,a.namabarang,a.satuan from
		      ".$dbname.".log_5masterbarang a where inactive=0 and a.namabarang like '%".$txtcari."%' or kodebarang like '%".$txtcari."%'";
			  
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
					  <td>".$_SESSION['lang']['saldo']."</td>
				  </tr>
		     </thead>
			 <tbody>";
			$no=0;	 
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				//ambil saldo barang
				$saldoqty=0;
				$str1="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$bar->kodebarang."'
				       and kodegudang='".$gudang."'";
                               
				$res1=mysql_query($str1);
				while($bar1=mysql_fetch_object($res1))
				{
					$saldoqty=$bar1->saldoqty;
				}

				//ambil pemasukan barang yang belum di posting
//				$qtynotpostedin=0;
//				$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
//                       b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$bar->kodebarang."' 
//					   and a.tipetransaksi<5
//					   and a.kodegudang='".$gudang."'
//					   and a.post=0
//					   group by kodebarang";
//                                echo $str2;
//                                 
//				$res2=mysql_query($str2);
//				while($bar2=mysql_fetch_object($res2))
//				{
//					$qtynotpostedin=$bar2->jumlah;
//				}
//				if($qtynotpostedin=='')
//				   $qtynotpostedin=0;


				//ambil pengeluaran barang yang belum di posting
				$qtynotposted=0;
				$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                       b on a.notransaksi=b.notransaksi where kodept='".$pemilikbarang."' and b.kodebarang='".$bar->kodebarang."' 
					   and a.tipetransaksi>4
					   and a.kodegudang='".$gudang."'
					   and a.post=0 and tanggal like '".$tanggal[2]."-".$tanggal[1]."%'
					   group by kodebarang";
                                //echo $str2."____".$gudang;
				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
					$qtynotposted=$bar2->jumlah;
				}
				if($qtynotposted=='')
				   $qtynotposted=0;
				   
				// echo $saldoqty._.$qtynotpostedin._.$qtynotposted.___________;  
				 
				 
			
				$saldoqty=($saldoqty+$qtynotpostedin)-$qtynotposted;
				
			  if($saldoqty==0)
			   {	
				echo"<tr class=rowcontent>
				   <td>".$no."</td>
				  <td>".$bar->kodebarang."</td>
				  <td>".$bar->namabarang."</td>
				  <td>".$bar->satuan."</td>
				  <td align=right>".number_format($saldoqty,2,',','.')."</td>
			  </tr>";
			   }
			   else
			   {
				echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"loadField('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."');\">
				   <td>".$no."</td>
				  <td>".$bar->kodebarang."</td>
				  <td>".$bar->namabarang."</td>
				  <td>".$bar->satuan."</td>
				  <td align=right>".number_format($saldoqty,2,',','.')."</td>
			      </tr>";			   	
			   }
			}
		echo    "
				 </tbody>
				 <tfoot></tfoot>
				 </table>";	
		}  
}
else
{
	echo " Error: Transaction Period missing";
}
?>
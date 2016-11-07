<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	
	if((isset($_POST['txtfind']))!='')
	{
			$txtfind=$_POST['txtfind'];
			$str="select * from ".$dbname.".log_5masterbarang where namabarang like '%".$txtfind."%' or kodebarang like '%".$txtfind."%' ";
			 if($res=mysql_query($str))
		  {
			echo"
          <fieldset>
        <legend>Result</legend>
        <div style=\"overflow:auto; height:300px;\" >
        <table class=data cellspacing=1 cellpadding=2  border=0>
				 <thead>
				 <tr class=rowheader>
				 <td class=firsttd>
				 No.
				 </td>
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
		//===========================pengambilan saldo
		//ambil saldo barang
				$saldoqty=0;
				$str1="select sum(saldoqty) as saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$bar->kodebarang."'
				       and kodeorg='".$_SESSION['empl']['kodeorganisasi']."'";
				$res1=mysql_query($str1);
				while($bar1=mysql_fetch_object($res1))
				{
					$saldoqty=$bar1->saldoqty;
				}

				//ambil pemasukan barang yang belum di posting
				$qtynotpostedin=0;
				$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                       b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' 
					   and a.tipetransaksi<5
					   and a.post=0
					   group by kodebarang";

				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
					$qtynotpostedin=$bar2->jumlah;
				}
				if($qtynotpostedin=='')
				   $qtynotpostedin=0;


				//ambil pengeluaran barang yang belum di posting
				$qtynotposted=0;
				$str2="select sum(b.jumlah) as jumlah,b.kodebarang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt
                       b on a.notransaksi=b.notransaksi where kodept='".$_SESSION['empl']['kodeorganisasi']."' and b.kodebarang='".$bar->kodebarang."' 
					   and a.tipetransaksi>4
					   and a.post=0
					   group by kodebarang";
                 
				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
					$qtynotposted=$bar2->jumlah;
				}
				if($qtynotposted=='')
				   $qtynotposted=0;
			
				$saldoqty=($saldoqty+$qtynotpostedin)-$qtynotposted;
        //============================================		
				
				if($bar->inactive==1)
				{
				    echo"<tr bgcolor='red' style='cursor:pointer;'  title='Inactive' >";
					$bar->namabarang=$bar->namabarang. " [Inactive]";
				}
				else
				{				
				    echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setBrg('".$bar->kodebarang."','".$bar->namabarang."','".$bar->satuan."')\" title='Click' >";
				 }   
				echo" <td class=firsttd>".$no."</td>
					  <td>".$bar->kodebarang."</td>
					  <td>".$bar->namabarang."</td>
					  <td>".$bar->satuan."</td>
					  <td align=right>".number_format($saldoqty,2,',','.')."</td>
					 </tr>";
			}	 
			echo "</tbody>
				  <tfoot>
				  </tfoot>
				  </table></div></fieldset>";
		  }	
		  else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}	
	}
	else
	{
	//keu_anggaran
	$txtfind2=$_POST['txtfind2'];
	$str="select * from ".$dbname.".keu_anggaran where kodeanggaran like '%".$txtfind2."%' or keterangan like '%".$txtfind2."%' or kodeorg  like '%".$txtfind2."%'";
	 if($res=mysql_query($str))
  {
  	echo"
        <fieldset>
        <legend>Result</legend>
        <div style=\"overflow:auto; height:300px;\">
        <table class=data cellspacing=1 cellpadding=2  border=0>
	     <thead>
		 <tr class=rowheader>
		 <td class=firsttd>
		 No.
		 </td>
		 <td>Kode Anggaran</td>
		 <td>Keterangan Anggaran</td>
		 <td>Tipe Anggaran</td>
		 </tr>
		 </thead>
		 <tbody>";
	$no=0;	 
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent style='cursor:pointer;' onclick=\"setAngrn('".$bar->kodeanggaran."','".$bar->tipeanggaran."')\" title='Click' >
		      <td class=firsttd>".$no."</td>
		      <td>".$bar->kodeanggaran."</td>
			  <td>".$bar->keterangan."</td>
			  <td>".$bar->tipeanggaran ."</td>
			 </tr>";
	}	 
	echo "</tbody>
	      <tfoot>
		  </tfoot>
		  </table></div></fieldset>";
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
	}	
?>
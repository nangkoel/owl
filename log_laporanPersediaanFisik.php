<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$periode=$_POST['periode'];

if($periode=='' and $gudang=='')
{
	$str="select a.kodebarang,sum(a.saldoqty) as kuan, 
	      b.namabarang,b.satuan,a.kodeorg from ".$dbname.".log_5masterbarangdt a
		  left join ".$dbname.".log_5masterbarang b
		  on a.kodebarang=b.kodebarang
		  where kodeorg='".$pt."' group by a.kodeorg,a.kodebarang order by kodebarang";
}
else if($periode=='' and $gudang!='')
{
	$str="select a.kodebarang,sum(a.saldoqty) as kuan, 
	      b.namabarang,b.satuan from ".$dbname.".log_5masterbarangdt a
		  left join ".$dbname.".log_5masterbarang b
		  on a.kodebarang=b.kodebarang
		  where kodeorg='".$pt."' 
		  and kodegudang='".$gudang."'
		  group by a.kodeorg,a.kodebarang  order by kodebarang";	
}
else{
	if($gudang=='')
	{
		$str="select 
			  a.kodeorg,
			  a.kodebarang,
			  sum(a.saldoakhirqty) as salakqty,
			  sum(a.qtymasuk) as masukqty,
			  sum(a.qtykeluar) as keluarqty,
			  sum(a.saldoawalqty) as sawalqty,
		      b.namabarang,b.satuan    
		      from ".$dbname.".log_5saldobulanan a
		      left join ".$dbname.".log_5masterbarang b
			  on a.kodebarang=b.kodebarang
			  where kodeorg='".$pt."' 
			  and periode='".$periode."'
			  group by a.kodebarang order by a.kodebarang";	  
	}
	else
	{
		$str="select
			  a.kodeorg,
			  a.kodebarang,
			  sum(a.saldoakhirqty) as salakqty,
			  sum(a.qtymasuk) as masukqty,
			  sum(a.qtykeluar) as keluarqty,
			  sum(a.saldoawalqty) as sawalqty,
		      b.namabarang,b.satuan  		 		      
			  from ".$dbname.".log_5saldobulanan a
		      left join ".$dbname.".log_5masterbarang b
			  on a.kodebarang=b.kodebarang
			  where kodeorg='".$pt."' 
			  and periode='".$periode."'
			  and kodegudang='".$gudang."'
			  group by a.kodebarang order by a.kodebarang";		
	}	
}
//=================================================
//echo $str;
if($periode=='')
{
	 $sawalQTY		='';
         $masukQTY		='';
	 $keluarQTY		='';
	 $kuantitas=0;
		$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('Y-m-d H:i:s');
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
			$kuantitas =$bar->kuan;
			echo"<tr class=rowcontent  style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarang(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
				  <td>".$no."</td>
				  <td>".$pt."</td>
				  <td>".$gudang."</td>
				  <td>".$periode."</td>
				  <td>".$kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td>".$bar->satuan."</td>
				   <td align=right>".$sawalQTY."</td>
				   <td align=right>".$masukQTY."</td>
				   <td align=right>".$keluarQTY."</td>
				   <td align=right class=firsttd>".number_format($kuantitas,2,'.',',')."</td>		   
				</tr>";
		}
	}
}
else
	{
    $salakqty	=0;
    $masukqty	=0;
    $keluarqty	=0;
    $sawalQTY	=0;
	 

	//
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=11>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$kodebarang=$bar->kodebarang;
		$namabarang=$bar->namabarang; 


		$salakqty	=$bar->salakqty;
		$masukqty	=$bar->masukqty;
		$keluarqty	=$bar->keluarqty;
		$sawalQTY	=$bar->sawalqty;
			  
		echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"detailMutasiBarang(event,'".$pt."','".$periode."','".$gudang."','".$kodebarang."','".$namabarang."','".$bar->satuan."');\">
			  <td>".$no."</td>
			  <td>".$pt."</td>
			  <td>".$gudang."</td>
			  <td>".$periode."</td>
			  <td>".$kodebarang."</td>
			  <td>".$namabarang."</td>
			  <td>".$bar->satuan."</td>
			   <td align=right class=firsttd>".number_format($sawalQTY,2,'.',',')."</td>
			   <td align=right class=firsttd>".number_format($masukqty,2,'.',',')."</td>
			   <td align=right class=firsttd>".number_format($keluarqty,2,'.',',')."</td>
			   <td align=right class=firsttd>".number_format($salakqty,2,'.',',')."</td>	   
			</tr>"; 		
	}	
  }
}	

?>
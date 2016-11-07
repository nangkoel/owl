<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_GET['pt'];
	$gudang=$_GET['gudang'];
	$periode=$_GET['periode'];
    $stream='';
	
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
		$stream.=$_SESSION['lang']['laporanstok'].":<br>
		<table border=1>
				    <tr>
					  <td bgcolor=#DEDEDE align=center>No.</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pt']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sloc']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['periode']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['masuk']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keluar']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldo']."</td>
					</tr>";
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			$periode=date('d-m-Y H:i:s');
			$kodebarang=$bar->kodebarang;
			$namabarang=$bar->namabarang; 
			$kuantitas =$bar->kuan;
			$stream.="<tr>
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
				   <td align=right class=firsttd>".number_format($kuantitas,2,'.','')."</td>		   
				</tr>";
		}
	  $stream.="</table>";	
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
		$stream.=$_SESSION['lang']['laporanstok'].":<br>
		<table border=1>
				    <tr>
					  <td bgcolor=#DEDEDE align=center>No.</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['pt']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sloc']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['periode']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodebarang']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namabarang']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['satuan']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldoawal']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['masuk']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keluar']."</td>
					  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['saldo']."</td>
					</tr>";
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$kodebarang=$bar->kodebarang;
		$namabarang=$bar->namabarang; 


		$salakqty	=$bar->salakqty;
		$masukqty	=$bar->masukqty;
		$keluarqty	=$bar->keluarqty;
		$sawalQTY	=$bar->sawalqty;
			  
		$stream.="<tr>
			  <td>".$no."</td>
			  <td>".$pt."</td>
			  <td>".$gudang."</td>
			  <td>".$periode."</td>
			  <td>'".$kodebarang."</td>
			  <td>".$namabarang."</td>
			  <td>".$bar->satuan."</td>
			   <td align=right class=firsttd>".number_format($sawalQTY,2,'.','')."</td>
			   <td align=right class=firsttd>".number_format($masukqty,2,'.','')."</td>
			   <td align=right class=firsttd>".number_format($keluarqty,2,'.','')."</td>
			   <td align=right class=firsttd>".number_format($salakqty,2,'.','')."</td>	   
			</tr>"; 		
	}
	$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
  }
}	
$nop_="MaterialBalance";
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}
?>
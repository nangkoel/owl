<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
$kodeorg=$_POST['kodeorg'];
$periode=$_POST['periode'];

	$str1="select a.*,b.namakaryawan,b.tanggalmasuk
	       from ".$dbname.".sdm_cutiht a
		   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	       where lokasitugas='".$kodeorg."'
		   and periodecuti='".$periode."'"; 
		   
	$res1=mysql_query($str1); 
	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		    <td>No</td>
			<td>".$_SESSION['lang']['kodeorganisasi']."</td>		 
		    <td>".$_SESSION['lang']['nokaryawan']."</td>
		    <td>".$_SESSION['lang']['namakaryawan']."</td>
			<td>".$_SESSION['lang']['tanggalmasuk']."</td>			
			<td>".$_SESSION['lang']['periode']."</td>			
			<td>".$_SESSION['lang']['dari']."</td>
			<td>".$_SESSION['lang']['tanggalsampai']."</td>
			<td>".$_SESSION['lang']['hakcuti']."</td>
			<td>".$_SESSION['lang']['diambil']."</td>
			<td>".$_SESSION['lang']['sisa']."</td>
			</tr>
		 </thead>
		 <tbody id=container>"; 
	$no=0;	 
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		
		echo"<tr class=rowcontent id=baris".$no.">
		           <td>".$no."</td>
				   <td>".substr($bar1->kodeorg,0,4)."</td>
		           <td>".$bar1->karyawanid."</td>
				   <td>".$bar1->namakaryawan."</td>
				   <td>".tanggalnormal($bar1->tanggalmasuk)."</td>
				   <td>".$periode."</td>				   
				   <td>".tanggalnormal($bar1->dari)."</td>
				   <td>".tanggalnormal($bar1->sampai)."</td>
				   <td>".$bar1->hakcuti."</td>
				   <td>".$bar1->diambil."</td>
				   <td>".$bar1->sisa."</td>
			</tr>	   
				   ";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

   $notransaksi	=$_POST['notransaksi'];
$str="select a.*, b.*,c.namakaryawan,c.kodegolongan,c.bagian,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
  ".$dbname.".sdm_5rs b on a.rs=b.id 
  left join ".$dbname.".datakaryawan c
  on a.karyawanid=c.karyawanid
  left join ".$dbname.".sdm_5diagnosa d
  on a.diagnosa=d.id
  where a.notransaksi='".$notransaksi."'
  order by a.updatetime desc, a.tanggal desc";
$stream='';
$res=mysql_query($str);  
  $no=0;
  while($bar=mysql_fetch_object($res))
  {
	   
	   $periode=substr($bar->periode,5,2)."-".substr($bar->periode,0,4);
	   $tanggal=tanggalnormal($bar->tanggal);
	   $karyawanid=$bar->karyawanid;
	   $namakaryawan=$bar->namakaryawan;
	   $doagnosa=$bar->ketdiag;
	   $namars=$bar->namars."[".$bar->kota."]";
	   $jenisbiaya=$bar->kodebiaya;
	   $keterangan=$bar->keterangan;
	   $totalbayar=$bar->jlhbayar;
	   $totalklaim=$bar->totalklaim;
	   $tahunplafon=$bar->tahunplafon;
	   $bagian=$bar->bagian;
	   $tanggalbayar=tanggalnormal($bar->tanggalbayar);
	   $golongan=$bar->kodegolongan;
	   
	   $jasars=$bar->jasars;
	   $jasadr=$bar->jasadr;
	   $jasalab=$bar->jasalab;
	   $byobat=$bar->byobat;
	   $bypendaftaran=$bar->bypendaftaran;
           $bytransport=$bar->bytransport;
	   
	   if($bar->ygsakit==0)
	   {
	   	$ygsakit['namaygsakit']=$namakaryawan;
	   }
	   else
	   {
	       $str1=" select nama,jeniskelamin,hubungankeluarga, 
		           ROUND(DATEDIFF(NOW(),tanggallahir)/365,2) as umur
		           from ".$dbname.".sdm_karyawankeluarga
	           where nomor=".$bar->ygsakit;
	       $res1=mysql_query($str1);
		   while($bar1=mysql_fetch_object($res1))
		   {
		   	 $ygsakit['namaygsakit']=$bar1->nama;
			 $ygsakit['jk']=$bar1->jeniskelamin;
			 $ygsakit['hubungankeluarga']=$bar1->hubungankeluarga;
			 $ygsakit['umur']=$bar1->umur;
		   }
	   }
  }	
 
  echo"<fieldset><legend>".$_SESSION['lang']['karyawan']."<legend>
       <table class=sortable cellspacing=1 borde=0>
	   <thead></thead>
	   <tbody>
       <tr class=rowcontent><td>".$_SESSION['lang']['notransaksi']."</td><td>".$notransaksi."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td>".$tanggal."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['thnplafon']."</td><td>".$tahunplafon."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['periode']."</td><td>".$periode."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['namakaryawan']."</td><td>".$namakaryawan."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['bagian']."</td><td>".$bagian."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['keterangan']."</td><td>".$keterangan."</td></tr>
       </tbody>
	   <tfoot>
	   </tfoot>
	   </table>
	   </fieldset>
	   <fieldset><legend>".$_SESSION['lang']['pasien']."<legend>
       <table class=sortable cellspacing=1 borde=0>
	   <thead></thead>
	   <tbody>	   
       <tr class=rowcontent><td>".$_SESSION['lang']['jenisbiayapengobatan']."</td><td>".$jenisbiaya."</td></tr>	   
       <tr class=rowcontent><td>".$_SESSION['lang']['namapasien']."</td><td>".$ygsakit['namaygsakit']."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['jeniskelamin']."</td><td>".$ygsakit['jk']."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['umur']."</td><td>".$ygsakit['umur']." ".$_SESSION['lang']['tahun']."</td></tr>	   
	   <tr class=rowcontent><td>".$_SESSION['lang']['hubungan']."</td><td>".$ygsakit['hubungankeluarga']."</td></tr>
       </tbody>
	   <tfoot>
	   </tfoot>
       </table>
	   </fieldset>
	   
	   <fieldset><legend>".$namars."<legend>
       <table class=sortable cellspacing=1 borde=0>	
	   <thead></thead>
	   <tbody>	      
       <tr class=rowcontent><td>".$_SESSION['lang']['biayaadministrasi']."</td><td align=right>".number_format($bypendaftaran,2,'.',',')."</td></tr>
           <tr class=rowcontent><td>".$_SESSION['lang']['biayatransport']."</td><td align=right>".number_format($bytransport,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['jasars']."</td><td align=right>".number_format($jasars,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['biayadr']."</td><td align=right>".number_format($jasadr,2,'.',',')."</td></tr>	   
	   <tr class=rowcontent><td>".$_SESSION['lang']['biayalab']."</td><td align=right>".number_format($jasalab,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['biayaobat']."</td><td align=right>".number_format($byobat,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['nilaiklaim']."</td><td align=right>".number_format($totalklaim,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['dibayar']."</td><td align=right>".number_format($totalbayar,2,'.',',')."</td></tr>
	   <tr class=rowcontent><td>".$_SESSION['lang']['tanggal']."</td><td>".$tanggalbayar."</td></tr>
       </tbody>
	   <tfoot>
	   </tfoot>
       </table>
	   </fieldset>
	   ";	 

	//ambil gaji pokok dan plafod
	$str="select value from ".$dbname.".sdm_ho_basicsalary where component=1 and  karyawanid=".$karyawanid;
	$res=mysql_query($str);
	$gp=0;
	while($bar=mysql_fetch_object($res))
	{
		$gp=$bar->value;
	}
	
	$str="select a.kode,b.persen from ".$dbname.".sdm_5jenisbiayapengobatan a
	      left join ".$dbname.".sdm_pengobatanplafond b
		  on a.kode=b.kodejenisbiaya
	      where b.kodegolongan='".$golongan."'";	  
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$plaf[$bar->kode]=$bar->persen*$gp/100;
	}	       	   	  		
	//ambil jumlah pengobatan sesuai tahunplafon
	$str="select sum(jlhbayar) as jlhbayar,kodebiaya from ".$dbname.".sdm_pengobatanht
	      where karyawanid=".$karyawanid." and tahunplafon=".$tahunplafon."
		  group by kodebiaya"; 
	$res=mysql_query($str);
	echo mysql_error($conn);
	echo "<fieldset><legend>".$_SESSION['lang']['plafon']."</legend>
	      <table class=sortable cellspacing=1 borde=0>	
		   <thead>
		   <tr clas=rowheader>
		    <td>".$_SESSION['lang']['kodegolongan']."</td>
			<td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
			<td>".$_SESSION['lang']['plafon']."</td>
			<td>".$_SESSION['lang']['sudahdipakai']."</td>
		   </tr>
		   </thead>
		   <tbody>";	
	 while($bar=mysql_fetch_object($res))
	 {
	 	  echo"<tr class=rowcontent>
		    <td>".$golongan."</td>
			<td>".$bar->kodebiaya."</td>
			<td align=right>".number_format($plaf[$bar->kodebiaya],2,',','.')."</td>
			<td align=right>".number_format($bar->jlhbayar,2,',','.')."</td>
		   </tr>";
	 }	     
	echo"</tbody>
		   <tfoot>
		   </tfoot>
	       </table>
		  </fieldset>";	  
	
?>

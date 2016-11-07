<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$notransaksi=$_GET['notransaksi'];



  $str="select * from ".$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";	
  $res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {

	  	$jabatan='';
		$namakaryawan='';
		$bagian='';	
		$karyawanid='';
		 $strc="select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan 
		    from ".$dbname.".datakaryawan a left join  ".$dbname.".sdm_5jabatan b
			on a.kodejabatan=b.kodejabatan
			where a.karyawanid=".$bar->karyawanid;
      $resc=mysql_query($strc);
	  while($barc=mysql_fetch_object($resc))
	  {
	  	$jabatan=$barc->namajabatan;
		$namakaryawan=$barc->namakaryawan;
		$bagian=$barc->bagian;
		$karyawanid=$barc->karyawanid;
	  }

	  //===============================	  

		$kodeorg=$bar->kodeorg;
		$persetujuan=$bar->persetujuan;
		$hrd=$bar->hrd; 
		$tujuan3=$bar->tujuan3;
		$tujuan2=$bar->tujuan2;	
		$tujuan1=$bar->tujuan1;
		$tanggalperjalanan=tanggalnormal($bar->tanggalperjalanan);
		$tanggalkembali=tanggalnormal($bar->tanggalkembali);
		$uangmuka=$bar->uangmuka;
		$tugas1=$bar->tugas1;
		$tugas2=$bar->tugas2;
		$tugas3=$bar->tugas3;
		$tujuanlain=$bar->tujuanlain;
		$tugaslain=$bar->tugaslain;
		$pesawat=$bar->pesawat;
		$darat=$bar->darat;
		$laut=$bar->laut;
		$mess=$bar->mess;
		$hotel=$bar->hotel;	
		$statushrd=$bar->statushrd;
		$xhrd=$bar->statushrd;
		$xper=$bar->statuspersetujuan;
		if($statushrd==0)
		    $statushrd=$_SESSION['lang']['wait_approval'];
        else if($statushrd==1)
		    $statushrd=$_SESSION['lang']['disetujui'];
        else 
		    $statushrd=$_SESSION['lang']['ditolak'];
			
		$statuspersetujuan=$bar->statuspersetujuan;
		if($statuspersetujuan==0)
		    $perstatus=$_SESSION['lang']['wait_approval'];
        else if($statuspersetujuan==1)
		    $perstatus=$_SESSION['lang']['disetujui'];
        else 
		    $perstatus=$_SESSION['lang']['ditolak'];
	//ambil bagian,jabatan persetujuan
		$perjabatan='';
		$perbagian='';
		$pernama='';
	$strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
	       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
		   where karyawanid=".$persetujuan;	   
	$resf=mysql_query($strf);
	while($barf=mysql_fetch_object($resf))
	{
		$perjabatan=$barf->namajabatan;
		$perbagian=$barf->bagian;
		$pernama=$barf->namakaryawan;
	}	 
//ambil jabatan, hrd

	$hjabatan='';
	$hbagian='';
	$hnama='';
	$strf="select a.bagian,b.namajabatan,a.namakaryawan from ".$dbname.".datakaryawan a left join
	       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
		   where karyawanid=".$hrd;	
	$resf=mysql_query($strf);
	while($barf=mysql_fetch_object($resf))
	{
		$hjabatan=$barf->namajabatan;
		$hbagian=$barf->bagian;
		$hnama=$barf->namakaryawan;
	}
	  	
  }
   echo $_SESSION['lang']['perjalanandinas'].":
      <table class=standard cellspacing=1>
	 <tr class=rowcontent>
	    <td>".$_SESSION['lang']['nama']."</td>
		<td>".$namakaryawan."</td>
	 </tr>
	 <tr class=rowcontent>
	    <td>".$_SESSION['lang']['kodeorg']."</td>
		<td>".$kodeorg."</td>
	 </tr>	 
	 <tr class=rowcontent>
	    <td>".$_SESSION['lang']['tanggaldinas']."</td>
		<td>".$tanggalperjalanan.". &nbsp 
		    ".$_SESSION['lang']['tanggalkembali']." 
			".$tanggalkembali."
		</td>
	 </tr>	
	 <tr class=rowcontent>
	    <td>".$_SESSION['lang']['transportasi']."/".$_SESSION['lang']['akomodasi']."</td>
		<td>
		     <input type=checkbox id=pesawat disabled ".($pesawat==1?'checked':'')."> ".$_SESSION['lang']['pesawatudara']."
			 <input type=checkbox id=darat disabled ".($darat==1?'checked':'')."> ".$_SESSION['lang']['transportasidarat']."
			 <input type=checkbox id=laut disabled ".($laut==1?'checked':'')."> ".$_SESSION['lang']['transportasiair']."
			 <input type=checkbox id=mess disabled ".($mess==1?'checked':'')."> ".$_SESSION['lang']['mess']."
			 <input type=checkbox id=hotel disabled ".($hotel==1?'checked':'')."> ".$_SESSION['lang']['hotel']."
        </td>
	 </tr>	
	 <tr class=rowcontent>
	   <td>
	      ".$_SESSION['lang']['uangmuka']."
	   </td>
	   <td>
	    <input type=hidden id=nitransaksipjd value='".$notransaksi."'>
	     <span id=oldval>".number_format($uangmuka,2,'.',',')."</span>";
		
	if($xhrd==0 or $xper==0)
	  {	 
		echo  $_SESSION['lang']['ganti'].":
		 <input type=text class=myinputtextnumber id=newvalpjd onkeypress=\"return tanpa_kutip(event);\" size=15 maxlength=17>
	     <button class=mybutton onclick=saveUpdateValPJD()>".$_SESSION['lang']['save']."</button>";
	  }
	echo"   
	   </td>
	 </tr> 	 	 
	 </table>
	 <table class=standard  cellspacing=1>
	   <tr class=rowcontent>
	     <td>
		     ".$_SESSION['lang']['tujuan']."1
		 </td>
	     <td>
		   ".$tujuan1.":
		   ".$tugas1."
		  </td> 
		</tr>
		<tr class=rowcontent> 
	     <td>
		    ".$_SESSION['lang']['tujuan']."2
		 </td>
	     <td>
		   ".$tujuan2.":
		   ".$tugas2."		 
		  </td>		 		 		 
	   </tr>
	   
	   <tr class=rowcontent>
	     <td>
		     ".$_SESSION['lang']['tujuan']."3
		 </td>
	     <td>
		   ".$tujuan3.":
		   ".$tugas3."		 
		 </td>
		</tr>
		<tr class=rowcontent>		 
	     <td>
		    ".$_SESSION['lang']['tujuan']."4
		 </td>
	     <td>
		   ".$tujuanlain.":
		   ".$tugaslain."		 </td>		 		 		 
	   </tr>
	 </table>";
	
?>

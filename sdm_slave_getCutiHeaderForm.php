<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
$kodeorg=$_POST['kodeorg'];
$periode=$_POST['periode'];

if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
    if($kodeorg!=''){
        $str1="select a.*,b.nik,b.namakaryawan,b.tanggalmasuk,b.lokasitugas
	       from ".$dbname.".sdm_cutiht a
		   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	       where lokasitugas='".$kodeorg."' and alokasi=0
		   and periodecuti='".$periode."'
                   and tanggalkeluar='0000-00-00'"; 
    } else {
        $str1="select a.*,b.nik,b.namakaryawan,b.tanggalmasuk,b.lokasitugas
	       from ".$dbname.".sdm_cutiht a
		   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	       where and alokasi=0
		   and periodecuti='".$periode."'
                   and tanggalkeluar='0000-00-00'"; 
    }
}
else{
    if($kodeorg!=''){
        $str1="select a.*,b.nik,b.namakaryawan,b.tanggalmasuk,b.lokasitugas
	       from ".$dbname.".sdm_cutiht a
		   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	       where lokasitugas='".$kodeorg."' and alokasi=1
		   and periodecuti='".$periode."'
                   and tanggalkeluar='0000-00-00'"; 
    } else {
        $str1="select a.*,b.nik,b.namakaryawan,b.tanggalmasuk,b.lokasitugas
	       from ".$dbname.".sdm_cutiht a
		   left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
	       where alokasi=1
		   and periodecuti='".$periode."'
                   and tanggalkeluar='0000-00-00'"; 
    }
}
        $str1.=" order by b.namakaryawan";
        //echo $str1;
	$res1=mysql_query($str1); 
	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		    <td>No</td>
			<td>".$_SESSION['lang']['kodeorganisasi']."</td>		 
		    <td>".$_SESSION['lang']['nik']."</td>
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
				   <td id=kodeorg".$no.">".$kodeorg."</td>
		           <td id=nik".$no.">".$bar1->nik."</td>
		           <td id=karyawanid".$no.">".$bar1->karyawanid."</td>
				   <td class=firsttd id=nama".$no."  title='Click for detail' style='cursor:pointer'  onclick=showByUser('".$bar1->karyawanid."',event)>".$bar1->namakaryawan."</td>
				   <td>".tanggalnormal($bar1->tanggalmasuk)."</td>
				   <td id=periode".$no.">".$periode."</td>				   
				   <td id=dari".$no.">".tanggalnormal($bar1->dari)."</td>
				   <td id=sampai".$no.">".tanggalnormal($bar1->sampai)."</td>
				   <td id=hak".$no." align=right>".$bar1->hakcuti."</td>
				   <td id=diambil".$no." align=right>".$bar1->diambil."</td>
				   <td><input type=text id=sisa".$no." class=myinputtextnumber size=4 conkeypress=\"return angka_doang(event);\" value='".$bar1->sisa."'>
				   <img src='images/save.png'  title='Save' class=resicon onclick=updateSisa('".$periode."','".$bar1->karyawanid."','".$kodeorg."','sisa".$no."')>
				   <img src='images/application/application_edit.png'  title='".$_SESSION['lang']['tambah']."' class=resicon onclick=\"tambahData('".$periode."','".$bar1->karyawanid."','".$kodeorg."','".$bar1->namakaryawan."');\">
				   </td>
			</tr>	   
				   ";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
?>
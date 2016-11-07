<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//ambil karyawan permanen
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 and karyawanid <>".$_SESSION['standard']['userid']. " and kodegolongan in ('7a','7b') order by namakaryawan";
$res=mysql_query($str);
$optKar="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}	  	


#atasan dari atasan
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 and kodegolongan>='7B' and karyawanid <>".$_SESSION['standard']['userid']. " order by namakaryawan";
$res=mysql_query($str);
$optKar2="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKar2.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}	 


#HRD
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where tipekaryawan=0 and bagian='HRD' and karyawanid <>".$_SESSION['standard']['userid']. " order by namakaryawan";
$res=mysql_query($str);
$optKarHrd="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optKarHrd.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}

//limit/page
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi.=$_POST['tex'];
  }
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where notransaksi like '%".$notransaksi."%'
		and karyawanid=".$_SESSION['standard']['userid']."
		order by jlhbrs desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$jlhbrs=$bar->jlhbrs;
}		
//==================
		 
  if(isset($_POST['page']))
     {
	 	$page=$_POST['page'];
	    if($page<0)
		  $page=0;
	 }
	 
  
  $offset=$page*$limit;
  

  $str="select * from ".$dbname.".sdm_pjdinasht 
        where notransaksi like '%".$notransaksi."%'
        and karyawanid=".$_SESSION['standard']['userid']."
		order by tanggalbuat desc limit ".$offset.",20";	
//		order by notransaksi desc limit ".$offset.",20";	 
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;

	  $namakaryawan='';
	  $strx="select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$bar->karyawanid;

	  $resx=mysql_query($strx);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namakaryawan=$barx->namakaryawan;
	  }
	  $add='';
	  if($bar->statuspersetujuan==0 && $bar->statushrd==0)
	  {
	  	$add.="&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">
		 &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">
         ";
	  }
    if($bar->statuspersetujuan==2)
     $stpersetujuan=$_SESSION['lang']['ditolak'];
   else if($bar->statuspersetujuan==1)
    $stpersetujuan=$_SESSION['lang']['disetujui'];
   else {
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	
	$stpersetujuan.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan','".$bar->notransaksi."')>".$optKar."</select>";
   }
   
   
     if($bar->statuspersetujuan2==2)
     $stpersetujuan2=$_SESSION['lang']['ditolak'];
   else if($bar->statuspersetujuan2==1)
    $stpersetujuan2=$_SESSION['lang']['disetujui'];
   else {
    $stpersetujuan2=$_SESSION['lang']['wait_approve'];	
	$stpersetujuan2.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan2','".$bar->notransaksi."')>".$optKar2."</select>";
   }
   


		
   if($bar->statushrd==2)
     $sthrd=$_SESSION['lang']['ditolak'];
  else if($bar->statushrd==1)
     $sthrd=$_SESSION['lang']['disetujui'];
  else{
     $sthrd=$_SESSION['lang']['wait_approve'];
	 $sthrd.="<br> &nbsp ".$_SESSION['lang']['ganti'].":<select   style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'hrd','".$bar->notransaksi."')>".$optKarHrd."</select>";
  }
  
#### cek bila persetuju pertama telah menolak   
if($bar->statuspersetujuan==2)
{
	$stpersetujuan2='';
	$sthrd='';
}
########################	  
  
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td>".$stpersetujuan."</td>
	  <td>".$stpersetujuan2."</td>
	  <td>".$sthrd."</td>	
	  
	  <td align=center>
	     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
       ".$add."
	  </td>
	  </tr>";
  }
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";
?>
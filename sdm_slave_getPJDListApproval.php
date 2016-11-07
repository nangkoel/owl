<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('config/connection.php');


$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  if(isset($_POST['tex']))
  {
  	$notransaksi=" and notransaksi like '%".$_POST['tex']."%' ";
  }
  else
  $notransaksi='';
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where
		(persetujuan=".$_SESSION['standard']['userid']."
		or hrd=".$_SESSION['standard']['userid'].")
		".$notransaksi."
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
        where
		(persetujuan=".$_SESSION['standard']['userid']."
		or hrd=".$_SESSION['standard']['userid'].")
		".$notransaksi."
		order by tanggalbuat desc  limit ".$offset.",20";	
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;

	  if($bar->persetujuan==$_SESSION['standard']['userid'])
	  {
	  	$per='persetujuan';
	  }
	  else
	  {
	  	$per='hrd';
	  }
	  $namakaryawan='';
	  $strx="select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$bar->karyawanid;

	  $resx=mysql_query($strx);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namakaryawan=$barx->namakaryawan;
	  }
	  $add='';
	  if($bar->statuspersetujuan==0 && $per=='persetujuan')
	  {
	  	$add.="&nbsp <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\">
		       &nbsp <img src=images/onebit_33 class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\">
         ";
	  }
	  if($bar->statushrd==0 && $per=='hrd')
	  {
	  	$add.="&nbsp <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\">
		       &nbsp <img src=images/onebit_33 class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\">
         ";
	  }	  
   if($bar->statuspersetujuan==2)
     $stpersetujuan=$_SESSION['lang']['ditolak'];
   else if($bar->statuspersetujuan==1)
    $stpersetujuan=$_SESSION['lang']['disetujui'];
   else 
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	  

   if($bar->statushrd==2)
     $sthrd=$_SESSION['lang']['ditolak'];
  else if($bar->statushrd==1)
     $sthrd=$_SESSION['lang']['disetujui'];
  else
     $sthrd=$_SESSION['lang']['wait_approve'];
	 
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td>".$stpersetujuan."</td>
	  <td>".$sthrd."</td>	
	  <td align=center>
	     <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
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
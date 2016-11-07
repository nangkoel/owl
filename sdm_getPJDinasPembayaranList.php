<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
$limit=20;
$page=0;
//========================
//ambil jumlah baris dalam tahun ini
  $notransaksi='';
  if(isset($_POST['tex']))
  {
  	$notransaksi.=" and notransaksi like '%".$_POST['tex']."%' ";
  }
$lokasitugas=substr($_SESSION['empl']['lokasitugas'],0,4);  
$str="select count(*) as jlhbrs from ".$dbname.".sdm_pjdinasht 
        where
		kodeorg='".$lokasitugas."'
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
        kodeorg='".$lokasitugas."'
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
	$dissa='';  
  if($bar->statuspersetujuan==2){
     $stpersetujuan=$_SESSION['lang']['ditolak'];
     $dissa=' disabled ';
  }
   else if($bar->statuspersetujuan==1){
    $stpersetujuan=$_SESSION['lang']['disetujui'];
	$dissa='';
	}
   else {
    $stpersetujuan=$_SESSION['lang']['wait_approve'];	  
    $dissa=' disabled '; 
   }
   if($bar->statushrd==2){
     $sthrd=$_SESSION['lang']['ditolak'];
     $dissa=' disabled '; 
   }
  else if($bar->statushrd==1){
     $sthrd=$_SESSION['lang']['disetujui'];
     $dissa='';
  }
  else{
     $sthrd=$_SESSION['lang']['wait_approve'];
     $dissa= ' disabled ';
  }
  
  if($bar->lunas==1)
     $dissa=' disabled ';
	 
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".$namakaryawan."</td>
	  <td>".tanggalnormal($bar->tanggalbuat)."</td>
	  <td>".$bar->tujuan1."</td>
	  <td>".$stpersetujuan."</td>
	  <td>".$sthrd."</td>
	  <td align=right>".number_format($bar->uangmuka,2,',','.')."</td>	
		  <td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value='".number_format($bar->uangmuka,2,'.',',')."'\">
		                  <input ".$dissa." type=text id=bayar".$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12 value='".number_format($bar->dibayar,2,'.',',')."'></td>
		  <td align=right><input ".$dissa." type=text id=tglbayar".$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".tanggalnormal($bar->tglbayar)."'></td>
		  <td><img src='images/save.png' title='Save' class=resicon onclick=saveBayarPJD('".$no."','".$bar->notransaksi."')>
		      <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
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
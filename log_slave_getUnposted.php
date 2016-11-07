<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
//limit/page
$limit=20;
$page=0;
//========================
  $gudang=$_POST['gudang'];
//ambil jumlah baris dalam tahun ini
  $add='';//default serach id nothing
  if(isset($_POST['tex']))
  {
  	$notransaksi="%".$_POST['tex']."%-".$gudang;
	$add=" and notransaksi like '%".$notransaksi."%'";
  }
$str="select count(*) as jlhbrs from ".$dbname.".log_transaksiht where kodegudang='".$gudang."'
		".$add."
		and post=0
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
  

  $str="select * from ".$dbname.".log_transaksiht where kodegudang='".$gudang."'
		".$add."
		and post=0
		order by tanggal asc,notransaksi asc limit ".$offset.",20";
	
  $res=mysql_query($str);
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;
	//===================smbil nama supplier
	  $namasupplier=$bar->idsupplier;
	  $strx="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$bar->idsupplier."'";

	  $resx=mysql_query($strx);
	  while($barx=mysql_fetch_object($resx))
	  {
	  	$namasupplier=$barx->namasupplier;
	  }
	//====================ambil username pembuat
	  $namapembuat='';
	  $stry="select namauser from ".$dbname.".user where karyawanid=".$bar->user;
	  $resy=mysql_query($stry);
	  while($bary=mysql_fetch_object($resy))
	  {
	  	$namapembuat=$bary->namauser;
	  }   
	echo"<tr class=rowcontent id=indukrow".$no.">
	  <td>".$no."</td>
	  <td>".$bar->kodegudang."</td>
	  <td title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\" align=center>".$bar->tipetransaksi."</td>
	  <td nowrap>".$bar->notransaksi."</td>
	  <td nowrap>".tanggalnormal($bar->tanggal)."</td>
	  <td>".$bar->kodept."</td>
	  <td>".$bar->nopo."</td>	
	  <td>".$namasupplier."</td> 
	  <td>".$bar->gudangx."</td> 
	  <td>".$bar->notransaksireferensi."</td>	  	  
	  <td>".$namapembuat."</td>
	  <td align=center>
        <button class=mybutton onclick=\"previewPosting(".$bar->tipetransaksi.",'".$bar->notransaksi."','".$gudang."',event);\">".$_SESSION['lang']['proses']."</button>
	  </td>
	  </tr>";
  }
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariUnconfirmed(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariUnconfirmed(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";
}
else
{
	echo " Error: Transaction Period missing";
}
?>
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
  $add='';//default serach id nothing
  if(isset($_POST['tex']))
  {
  	$notransaksi=$_POST['tex']."%";
	$add=" and notransaksi like '".$notransaksi."'";
  }  
//ambil jumlah baris dalam tahun ini
$str="select count(*) as jlhbrs from ".$dbname.".log_transaksiht where notransaksi in (
                select a.notransaksi from ".$dbname.".log_transaksi_vw a left join ".$dbname.".log_transaksidt b on a.notransaksireferensi=b.notransaksi and a.`kodebarang`=b.`kodebarang`
                where tipetransaksi=7 and gudangx='".$gudang."' and (a.jumlah-b.jumlah>0 or a.notransaksireferensi is null))
		".$add."		
		and gudangx='".$gudang."' order by jlhbrs desc";
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
  

  $str="select * from ".$dbname.".log_transaksiht where notransaksi in (
                select a.notransaksi from ".$dbname.".log_transaksi_vw a left join ".$dbname.".log_transaksidt b on a.notransaksireferensi=b.notransaksi and a.`kodebarang`=b.`kodebarang`
                where tipetransaksi=7 and gudangx='".$gudang."' and (a.jumlah-b.jumlah>0 or (a.notransaksireferensi='' or a.notransaksireferensi is null)))
		".$add."		
		and gudangx='".$gudang."'	
		order by notransaksi desc limit ".$offset.",20";
  $res=mysql_query($str);
  
  $no=$page*$limit;
  while($bar=mysql_fetch_object($res))
  {
  	$no+=1;
	//====================ambil username pembuat
	  $namapembuat='';
	  $stry="select namauser from ".$dbname.".user where karyawanid=".$bar->user;
	  $resy=mysql_query($stry);
	  while($bary=mysql_fetch_object($resy))
	  {
	  	$namapembuat=$bary->namauser;
	  }   
	//====================ambil username posting
	  $namaposting='Hold';
	  if(intval($bar->postedby)!=0)
	  {
		  $stry="select namauser from ".$dbname.".user where karyawanid=".$bar->postedby;
		  $resy=mysql_query($stry);
		  while($bary=mysql_fetch_object($resy))
		  {
		  	$namaposting=$bary->namauser;
		  }
	  }
	  
	 if($namaposting=='Hold' && $bar->post==1)
	  {
	  	$namaposting=" Release By ???";
	  }
//status apakah sudah diterima
	$status=$_SESSION['lang']['belumterima'];
	if($bar->notransaksireferensi!='')
	{
                $strCek="select sum(jumlah) as jumlah from ".$dbname.".log_transaksi_vw
                              where notransaksi='".$bar->notransaksi."'";
                $resCek=mysql_query($strCek);
                while($barCek=mysql_fetch_object($resCek)){
                    $jumlah=$barCek->jumlah;
                }
                $strCekBpb="select sum(jumlah) as diterima from ".$dbname.".log_transaksi_vw
                              where notransaksireferensi='".$bar->notransaksi."'";
                $resCekBpb=mysql_query($strCekBpb);
                while($barCek=mysql_fetch_object($resCekBpb))
                {
                    $diterima=$barCek->diterima;
//                    exit($jumlah." ".$diterima);
                    if ($jumlah-$diterima>0){
                        $status=$_SESSION['lang']['sudahditerimasebagian'];
                        $add="<img src=images/application/application_go.png class=resicon  title='Process' onclick=\"processReceipt('".$bar->notransaksi."');\">";
                    } else {
        		$add='';
                        $status=$_SESSION['lang']['sudahditerima'];
                    }   
                }
	}
	else if($bar->post>0)
	{

		//jika sudah di post oleh sumber maka dapat diterima
		//karena setelah posting baru ada hargasatuan
		$add="<img src=images/application/application_go.png class=resicon  title='Process' onclick=\"processReceipt('".$bar->notransaksi."');\">";

	}  
    else
	{
		$add='';
	}			     

	  
	echo"<tr class=rowcontent>
	  <td>".$no."</td>
	  <td>".$bar->kodegudang."</td>
	  <td title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
	  <td>".$bar->notransaksi."</td>
	  <td>".tanggalnormal($bar->tanggal)."</td>
	  <td>".$bar->kodept."</td>
	  <td>".$bar->gudangx."</td>			  
	  <td>".$namapembuat."</td>
	  <td>".$namaposting."</td>
	  <td>".$status."</td>
	  <td align=center>
	     ".$add."
	     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewMutasi('".$bar->notransaksi."',event);\"> 
	  </td>
	  </tr>";
  }
  echo"<tr><td colspan=11 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";
}
else
{
	echo " Error: Transaction Period missing";
}
?>
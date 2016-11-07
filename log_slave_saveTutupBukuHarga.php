<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//========================
  $user  =$_SESSION['standard']['userid'];
  $period=$_POST['periode'];
  $pt	 =$_POST['pt'];
  $gudang=$pt;
  $kodebarang=$_POST['kodebarang'];  
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
//=============================
//ambil saldo saldoawal bulan ini
		$str="select sum(saldoawalqty) as sawal,sum(nilaisaldoawal) as sawalrp 
		      from ".$dbname.".log_5saldobulanan
		      where kodegudang='".$gudang."'
			  and kodebarang='".$kodebarang."' and periode='".$period."'";
		$res=mysql_query($str);
		$sawal=0;
		$nilaisaldoawal=0;
		while($bar=mysql_fetch_object($res))
		{
			$sawal=$bar->sawal;
			$nilaisaldoawal=$bar->sawalrp;
		}
		if($sawal=='')
		   $sawal=0;
		if($nilaisaldoawal=='')
		   $nilaisaldoawal=0;   
        if($sawal==0 or $nilaisaldoawal==0)
		{
			$haratsawal=0;
		}
		else
		{
			$haratsawal=$nilaisaldoawal/$sawal;
		}
   
//===============ambil semua penerimaan yan sudah posting dan harga
 $str="select sum(a.jumlah) as jumlah,sum(a.hargasatuan*a.jumlah) as hartot from ".$dbname.".log_transaksidt a
       left join ".$dbname.".log_transaksiht b on
	   a.notransaksi=b.notransaksi
      where b.kodegudang='".$gudang."'
	  and a.kodebarang='".$kodebarang."'
	  and b.tanggal>=".$awal." and b.tanggal<=".$akhir." 
	  and b.tipetransaksi<5 and b.post=1";	  
 $masuk=0;
 $hartotmasuk=0;
 $res=mysql_query($str);
 while($bar=mysql_fetch_object($res))
 {
 	$masuk=$bar->jumlah;
	$hartotmasuk=$bar->hartot;
 }
 if($masuk=='')
    $masuk=0;
 if($hartotmasuk=='')
    $hartotmasuk=0;
   
   if($masuk<=0)
     $haratmasuk=0;
   else 	 
     $haratmasuk=$hartotmasuk/$masuk;	
   	
	
//=======================================================
    if(($sawal+$masuk)<=0)
	{
		$haratbaru=0;
	}
	else
	{
	  $haratbaru=	($hartotmasuk+$nilaisaldoawal)/($sawal+$masuk);
	}

#jika harga rata-rata baru adalah 0
if($haratbaru==0)
    $haratbaru=$haratmasuk;
if($haratbaru==0)
    $haratbaru=$haratsawal;
if($haratbaru==0)
{
    $str="select hargarata from ".$dbname.".log_5saldobulanan where kodebarang='".$kodebarang."' and hargarata>0
          order by lastupdate desc limit 1";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $haratbaru=$bar->hargarata;
    }
}
if($haratbaru=='')
    $haratbaru=1;

//==========================================
 //update harga pada 
 //untuk mutasi sudah tidak perlu dilakukan pemberian harga lagi
  $str="update ".$dbname.".log_transaksidt set hargarata=".$haratbaru." 
        where kodebarang='".$kodebarang."'
         and notransaksi in(select notransaksi from ".$dbname.".log_transaksiht b
		where b.kodegudang='".$gudang."'  
		and b.tanggal>=".$awal." and b.tanggal<=".$akhir."
	    and b.post=1)"; 
 if(mysql_query($str))
 {
 	$str="update ".$dbname.".log_5saldobulanan 
	      set hargarata=".$haratbaru.",
		  nilaisaldoakhir=saldoakhirqty*".$haratbaru.",
		  qtymasukxharga=qtymasuk*".$haratmasuk.",
		  qtykeluarxharga=qtykeluar*".$haratbaru." 
	      where kodebarang='".$kodebarang."' and kodegudang='".$gudang."' 
		  and periode='".$period."'";
	if(mysql_query($str))
	{
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	  
	
 }
 else
 {
 	echo " Gagal,".addslashes(mysql_error($conn));
 }			
?>
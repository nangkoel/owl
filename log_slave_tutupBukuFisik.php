<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
//========================
  $gudang=$_POST['gudang'];
  $user  =$_SESSION['standard']['userid'];
  $awal  =$_POST['awal'];
  $akhir =$_POST['akhir'];
  $period=$_SESSION['gudang'][$gudang]['tahun']."-".$_SESSION['gudang'][$gudang]['bulan'];
//=============================
//next period is
  $tg=mktime(0,0,0,$_SESSION['gudang'][$gudang]['bulan']+1,15,$_SESSION['gudang'][$gudang]['tahun']);
  $nextPeriod=date('Y-m',$tg);
  $tg=mktime(0,0,0,substr($akhir,4,2),intval(substr($akhir,6,2)+1),$_SESSION['gudang'][$gudang]['tahun']);
  $nextAwal=date('Ymd',$tg);
  $tg=mktime(0,0,0,intval(substr($akhir,4,2))+1,date('t',$tg),$_SESSION['gudang'][$gudang]['tahun']);
  $nextAkhir=date('Ymd',$tg); 

//================================================
//periksa periode
$str="select tutupbuku from ".$dbname.".setup_periodeakuntansi where periode='".$period."'
      and kodeorg='".$gudang."'";
$res=mysql_query($str);
$periode='benar';
if(mysql_num_rows($res)>0)
{
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tutupbuku==0)
		{
			$periode='benar';
		}
		else
		{
			$periode='salah';
		}
	}
}
else
{
	$periode='salah';
}
//==========================================  
  
//cel apakah sudah posting semua pada periode tersebut;
$str="select count(tanggal) as tgl from ".$dbname.".log_transaksiht
      where kodegudang='".$gudang."' and tanggal>=".$awal." and tanggal<=".$akhir."
	  and post=0";
$res=mysql_query($str);
$jlhNotPost=0;
while($bar=mysql_fetch_object($res))
{
	$jlhNotPost=$bar->tgl;
}

if($jlhNotPost>0)
{
	echo " Error: ".$_SESSION['lang']['belumposting']." > 0";
}  
else if($periode=='salah')
{
	echo " Error: Transaction period not defined";
} 
else
{
   //update setup_periodeakuntansi
   $str="update ".$dbname.".setup_periodeakuntansi set tutupbuku=1
          where kodeorg='".$gudang."' and periode='".$period."'";
   if(mysql_query($str))
   {
   	$str="INSERT INTO `".$dbname."`.`setup_periodeakuntansi`
		(`kodeorg`,
		`periode`,
		`tanggalmulai`,
		`tanggalsampai`,
		`tutupbuku`)
		VALUES
		('".$gudang."',
		 '".$nextPeriod."',
		 ".$nextAwal.",
		 ".$nextAkhir.",
		 0
		 )";
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
 }
}
else
{
	echo " Error: Transaction Period missing";
}
?>
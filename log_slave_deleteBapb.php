<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
  $notransaksi	=$_POST['notransaksi'];
  
//==============================update ind dr pak gin
          $str="select * from ".$dbname.".log_transaksidt where notransaksi='".$notransaksi."' and statussaldo=1";
        if(mysql_num_rows(mysql_query($str))>0){
           exit(" Error, transaksi sudah dalam proses posting");
        }
//========================     
  
  
  $str="select post from ".$dbname.".log_transaksiht where notransaksi='".$notransaksi."'";
  $res=mysql_query($str);
  $ststus=0;
  while($bar=mysql_fetch_object($res))
  { 
  	$status=$bar->post;
  }
  if($status==1)
  {
  	//block if posted
  	echo " Gagal/Error, Document has been posted";
  }
  else
  {
	//delete detail first
	$str="delete from ".$dbname.".log_transaksidt where notransaksi='".$notransaksi."'";
	if(mysql_query($str))
	{
	//delete header
	$str="delete from ".$dbname.".log_transaksiht where notransaksi='".$notransaksi."'";
	    mysql_query($str);
	}
  }
}
?>
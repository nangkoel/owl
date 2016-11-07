<?php //@Copy nangkoelframework
//ini tidak memerlukan master validation untuk menghindari
//replace session timeout
session_start();
include('config/connection.php');
include('lib/nangkoelib.php');
$userid=$_SESSION['standard']['userid'];
//cek persetujuan PP============================================
  $str=" select persetujuan1,persetujuan2,persetujuan3,
         persetujuan4,persetujuan5,
		 hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3,
		 hasilpersetujuan4,hasilpersetujuan5
		 from ".$dbname.".log_prapoht 
		 where close<2 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))
		 or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))
		 or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3=''))
		 or (persetujuan4=".$userid." and (hasilpersetujuan4 is null or hasilpersetujuan4=''))
		 or (persetujuan5=".$userid." and (hasilpersetujuan5 is null or hasilpersetujuan5='')))";

  $res=mysql_query($str);
  if(mysql_num_rows($res)>0)
  {
  	echo "<hr>Purchase Request(s) need your approval<br><a href=# onclick=\"window.location='log_persetuuanPp.php'\">Click Here</a>";
  }
//cek persetujuan PO===========================================
  $str=" select persetujuan1,persetujuan2,persetujuan3,
		 hasilpersetujuan1,hasilpersetujuan2,hasilpersetujuan3
		 from ".$dbname.".log_poht 
		 where stat_release<1 and((persetujuan1=".$userid." and (hasilpersetujuan1 is null or hasilpersetujuan1=''))
		 or (persetujuan2=".$userid." and (hasilpersetujuan2 is null or hasilpersetujuan2=''))
		 or (persetujuan3=".$userid." and (hasilpersetujuan3 is null or hasilpersetujuan3='')))";

  $res=mysql_query($str);
  if(mysql_num_rows($res)>0)
  {
  	echo "<hr>Purchase Order(s) need your approval<br><a href=# onclick=\"window.location='log_persetujuan_po.php'\">Click Here</a>";
  }
  //Perjalanan dinas ========================================
    $str="select * from ".$dbname.".sdm_pjdinasht 
        where
        (persetujuan=".$_SESSION['standard']['userid']." and statuspersetujuan=0)
		or (hrd=".$_SESSION['standard']['userid']." and statushrd=0)";
  $res=mysql_query($str);
  if(mysql_num_rows($res)>0)
  {
  	echo "<hr>".$_SESSION['lang']['perjalanandinas']." need your approval<br><a href=# onclick=\"window.location='sdm_3persetujuanPJD.php'\">Click Here</a>";
  }		
?>
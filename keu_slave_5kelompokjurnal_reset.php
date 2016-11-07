<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');

if($_POST['proses']=="resetData")
{
    $b=0;
    $kodePt=$_POST['kodePt'];
    $sCek="select tutupbuku from ".$dbname.".setup_periodeakuntansi where 
        kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$kodePt."')";
   $qCek=mysql_query($sCek) or die(mysql_error($conn));
   $brs=mysql_num_rows($qCek);
   for($a=0;$a<$brs;$a++)
   {
       $rBrs=mysql_fetch_row($qCek);
           
       if($rBrs[$a]==0)
       {
           $b+=1;
       }
   }
   if($b!=0)
   {
       echo"warning:Organisasi di Sub ".$kodePt.",belum tutup Buku ";
       exit();
   }
   elseif($b==0)
   {
       $sUp="update ".$dbname.".keu_5kelompokjurnal set nokounter=0 where kodeorg='".$kodePt."'";
       if(mysql_query($sUp))//insert detail
			{	
			  //update PO jumlah masuk
			   echo"1";
			}   
			else
			{
		     echo " Gagal".addslashes(mysql_error($conn));
			 exit(0);
			}	
      // $brt=mysql_query($sUp) or die(mysql_error($conn));
       
       
   }
}
?>

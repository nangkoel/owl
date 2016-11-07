<?php
require_once('master_validation.php');
require_once('config/connection.php');

$code=$_POST['code'];
   $sta="select * from ".$dbname.".organisasi where kodeorganisasi='".$code."'";
   $re=mysql_query($sta);
if(mysql_num_rows($re)>0){
   while($be=mysql_fetch_object($re))
   {
	 echo $be->kodeorganisasi."|".$be->namaorganisasi."|".$be->tipe."|".$be->alamat."|".$be->telepon."|".$be->wilayahkota."|".$be->kodepos."|".$be->negara."|".$be->alokasi."|".$be->noakun."|".$be->fax."|".$be->namaalias; 	
   }
 }
else
{
	echo "-1";
} 
?>

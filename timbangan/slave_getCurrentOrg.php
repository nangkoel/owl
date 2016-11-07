<?php
require_once('master_validation.php');
require_once('config/connection.php');

$code=$_POST['code'];
   $sta="select * from ".$dbname.".org where code='".$code."'";
   $re=mysql_query($sta);
if(mysql_num_rows($re)>0){
   while($be=mysql_fetch_object($re))
   {
	 echo $be->code."|".$be->emplname."|".$be->type."|".$be->address."|".$be->telp."|".$be->city."|".$be->zipcode."|".$be->country."|".$be->active_period."|".$be->parent."|".$be->addf1."|".$be->addf2."|".$be->addf3; 	
   }
 }
else
{
	echo "-1";
} 
?>

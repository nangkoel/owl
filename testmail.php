 <?php 
require_once('config/connection.php');
//$subject="[Notifikasi] user nonaktif";
//                                $body="<html>
//                                         <head>
//                                         <body>
//                                           <dd>Dengan Hormat,</dd><br>
//                                           <br>
//                                         test                                 <br>
//                                           Regards,<br>
//                                           Owl-Plantation System.
//                                         </body>
//                                         </head>
//                                       </html>
//                                       ";
//                                $to='blasius.cosa@hardaya.co.id';
//                                //$to='nangkoel@gmail.com';
//                               if($to!=''){ 
//                                $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;     
//                               }
$str="select * from ".$dbname.".namabahasa order by code";
$res=mysql_query($str);

echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
  echo "<option value='".$bar->code."'";
  # Default Language
  if($bar->code=='ID') {
	echo " selected";
  }
  echo ">".$bar->name."</option>";
}
?>
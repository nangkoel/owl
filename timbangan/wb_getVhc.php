<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

 $TRP=$_POST['trp'];
 
 $stg="select VEHNOCODE from ".$dbname.".msvehicle where FLAG='T' 
       and TRPCODE='".$TRP."'
	   order by VEHNOCODE"; 
$reg=mysql_query($stg);
$opt_vehicle="<option value='0'></option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_vehicle.="<option value='".$bag[0]."'>".$bag[0]."</option>";
		}
echo $opt_vehicle;	
?>

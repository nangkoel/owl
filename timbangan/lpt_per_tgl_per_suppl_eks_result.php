<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//include('validator.php');
$tgl=$_GET['tanggal'];
$tgl2=substr($tgl,6,4).'-'.substr($tgl,3,2).'-'.substr($tgl,0,2);
$TRPCODE=$_GET['trpcode'];
$NAMASUPPLIER=$_GET['namasupplier'];
$str="select sum(NETTO-kgpotsortasi) as jml
        from ".$dbname.".mstrxtbs where DATEOUT like '".$tgl2."%' and UNITCODE is null and productcode='40000003'
        and OUTIN=0 and TRPCODE='".$TRPCODE."' group by TRPCODE";
$jum=0;
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{

		$jum=$bar->jml;
}	  			
echo"
			Tanggal	&nbsp &nbsp: ".$tgl."<br >
		    Supplier :".$NAMASUPPLIER."<br> 
			Jumlah &nbsp &nbsp:".number_format($jum,2,',','.')." Kg";
?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$jabatan=$_POST['jabatan'];
$lokasitugas=$_POST['lokasitugas'];
$status='LOKASI';
/*
if (preg_match("/HO/i",$lokasitugas) or preg_match("/RO/i",$lokasitugas)) {
    $status='KOTA';
}
*/
$x=substr($lokasitugas,2,2);
if($x=='RO' or $x=='HO')
  $status='KOTA';  

$str="SELECT * FROM ".$dbname.".sdm_5stdtunjangan  where jabatan=".$jabatan." and penempatan='".$status."'";

$res=mysql_query($str);
if(mysql_num_rows($res)>0)
{
    while($bar=mysql_fetch_object($res))
    {
        echo"<?xml version='1.0' ?>
	     <tunjangan>
			 <tjjabatan>".($bar->tjjabatan!=""?$bar->tjjabatan:"*")."</tjjabatan>
			 <tjkota>".($bar->tjkota!=""?$bar->tjkota:"*")."</tjkota>
			 <tjtransport>".($bar->tjtransport!=""?$bar->tjtransport:"*")."</tjtransport>
			 <tjmakan>".($bar->tjmakan!=""?$bar->tjmakan:"*")."</tjmakan>
			 <tjsdaerah>".($bar->tjsdaerah!=""?$bar->tjsdaerah:"*")."</tjsdaerah>
			 <tjmahal>".($bar->tjmahal!=""?$bar->tjmahal:"*")."</tjmahal>
			 <tjpembantu>".($bar->tjpembantu!=""?$bar->tjpembantu:"*")."</tjpembantu>
	   </tunjangan>";	       
    }
}
else
{
         echo"<?xml version='1.0' ?>
	     <tunjangan>
			 <tjjabatan>0</tjjabatan>
			 <tjkota>0</tjkota>
			 <tjtransport>0</tjtransport>
			 <tjmakan>0</tjmakan>
			 <tjsdaerah>0</tjsdaerah>
			 <tjmahal>0</tjmahal>
			 <tjpembantu>0</tjpembantu>
	   </tunjangan>";   
}
?>
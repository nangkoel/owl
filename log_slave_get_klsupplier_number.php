<?php
require_once('master_validation.php');
require_once('config/connection.php');
if(isset($_POST['kelompok']))
{
//return supllier/kontraktor id
	$kdkelompok=trim($_POST['kelompok']);
	if($kdkelompok=='')
	{
		echo"";
	}
	else
	{
		$str="select max(supplierid) as id from ".$dbname.".log_5supplier where kodekelompok='".$kdkelompok."'";	

		$res=mysql_query($str);
		while($bar=mysql_fetch_object($res))
		{
			$newkode=$bar->id;
		}
	//remove group code at the begenning	
	    $newkode=substr($newkode,6,4);
	//get year code 2 digit
	    $mid=date('y');
	//create increment for new number		
		$newkode=intval($newkode)+1;
		switch($newkode)
		{
			//create 4 digit code from new number
			case $newkode<10:
			   $newkode='000'.$newkode;
			   break;
			case $newkode<100:
			   $newkode='00'.$newkode;
			   break;
			case $newkode<1000:
			   $newkode='0'.$newkode;
			   break;		   
			default:
	           $newkode=$newkode;   
			break;     
		}
	  $newkode=$kdkelompok.$mid.$newkode;
	  echo $newkode;			
	}
}
else
{
$tipe		=$_POST['tipe'];
$str1="select max(kode) as kode from ".$dbname.".log_5klsupplier where tipe='".$tipe."'";
if($res1=mysql_query($str1))
{
	while($bar1=mysql_fetch_object($res1))
	{
		$kode=$bar1->kode;
	}
	$kode=substr($kode,1,5);
	$newkode=$kode+1;
	switch($newkode)
	{
		case $newkode<10:
		   $newkode='00'.$newkode;
		   break;
		case $newkode<100:
		   $newkode='0'.$newkode;
		   break;
		default:
           $newkode=$newkode;   
		break;     
	}
	if($tipe=='SUPPLIER')
	    $newkode='S'.$newkode;
	else
	    $newkode='K'.$newkode;	
echo $newkode;			
} 
else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
}
?>
<?php
require_once('master_validation.php');
require_once('config/connection.php');

$kodebarang=$_POST['kodebarangx'];
$spec=$_POST['spec'];
$path='photobarang';

  if(is_dir($path))
  {
  	writeFile($path,$conn,$dbname);
  }
  else
  {
  	if(mkdir($path, 0777))
	{
		writeFile($path,$conn,$dbname);
	}
	else
	{
		echo " Gagal, Can't create folder for uploaded file";
		exit(0);
	}
  } 
  
function writeFile($path,$conn,$dbname)
{ 

$lokasi=Array();
	   $dir=$path;
	   for($x=0;$x<count($_FILES['file']['name']);$x++)
	   {
		 $path = $dir."/".basename($_FILES['file']['name'][$x]);
	     if($path!='photobarang/')
		 	$lokasi[$x]=$path;
		 else
		    $lokasi[$x]='';
			 		
		 $size=$_FILES['file']['size'][$x];
		 $max=100000;
		 if($size>$max)
		 {
		    echo"Error : file size beyond limit (100kb)";
			$lokasi[$x]='';
			exit(0);
		 }
		 
		   try{
		   move_uploaded_file($_FILES['file']['tmp_name'][$x], $path);
		   }
		   catch(Exception $e)
		   {
		   	echo "Error:".$e;
			exit();
		   }
	   }
	  $str="delete from ".$dbname.".log_5photobarang where kodebarang='".$_POST['kodebarangx']."'";
	  mysql_query($str);
	  $str="insert into ".$dbname.".log_5photobarang(kodebarang,depan,samping,atas,spesifikasi)
	        values('".$_POST['kodebarangx']."','".$lokasi[0]."','".$lokasi[1]."','".$lokasi[2]."','".$_POST['spec']."')";
	  if(mysql_query($str))
	  {
	  	echo "Uploaded/Saved";
	  } 		
	  else
	  {
	  	echo "Error :".addslashes(mysql_error($conn));
	  }
}
?>
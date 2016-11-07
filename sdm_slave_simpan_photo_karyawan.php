<?php
require_once('master_validation.php');
require_once('config/connection.php');
$karyawanid=$_POST['karyawanid'];
$path='photokaryawan';
if(($_FILES['photo']['size']) <= $_POST['MAX_FILE_SIZE'])
{
//the full path is photokaryawan/$karyawanid.ext
  if(is_dir($path))
  {
  	writeFile($path);
	chmod($path, 0777);
  }
  else
  {
  	if(mkdir($path))
	{
      writeFile($path);
	  chmod($path, 0777);
	}
	else
	{
		echo "<script> alert('Gagal, Can`t create folder for uploaded file');</script>";
		exit(0);
	}
  } 
}
else
{
echo "<script>File size is ".filesize($_FILES['photo']['tmp_name']).", greater then allowed</script>";	
}
  
function writeFile($path)
{ 
	   global $karyawanid;
	   global $conn;
	   global $dbname;
	   $dir=$path;
	     $ext=split('[.]', basename( $_FILES['photo']['name']));
		 $ext=$ext[count($ext)-1];
		 $ext=strtolower($ext);
		 if($ext=='jpg' or $ext=='jpeg' or $ext=='gif' or $ext=='png' or $ext=='bmp')
		 {
		 $path = $dir."/".$karyawanid.".".$ext;
		 //remove if exist;
         //write to a file
		 try{
		 	if(move_uploaded_file($_FILES['photo']['tmp_name'], $path))
			{
			   	$str="update ".$dbname.".datakaryawan set photo='".$path."'
				      where karyawanid=".$karyawanid;
				mysql_query($str);
				//echo mysql_error($conn);
				if(mysql_affected_rows($conn)>0)
				{	  
				echo"<script>
					parent.document.getElementById('displayphoto').removeAttribute('src');
					parent.document.getElementById('displayphoto').setAttribute('src','".$path."');
					//parent.document.getElementById('displayphoto').getAttribute('src').value;
					</script>";
				}	
		     		 chmod($path, 0775);					
			}
		  }
		  catch(Exception $e)
		  {
		  	echo "<script>alert(\"Error Writing File".addslashes($e->getMessage())."\");</script>";
		  }
		 }
		 else
		 {
		  	echo "<script>alert('Filetype not support');</script>";		 	
		 }
}
?>

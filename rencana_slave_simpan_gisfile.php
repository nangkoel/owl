<?php
require_once('master_validation.php');
require_once('config/connection.php');
$path='filegis';
if(($_FILES['photo']['size']) <= $_POST['MAX_FILE_SIZE'])
{
//the full path is photokaryawan/$karyawanid.ext
  if(is_dir($path))
  {
  	writeFile($path);
	//chmod($path, 0777);
  }
  else
  {
  	if(mkdir($path))
	{
                       writeFile($path);
	  //chmod($path, 0777);
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
        global $conn;
        global $dbname;
        $dir=$path;
          $ext=split('[.]', basename( $_FILES['photo']['name']));
              $ext=$ext[count($ext)-1];
              $ext=strtolower($ext);
              if($ext=='zip' or $ext=='rar' or $ext=='gz' or $ext=='tgz' or $ext=='7z' or $ext=='tar' or $ext=='png' or $ext=='jpg' or $ext=='jpeg')
              {
              $path = $dir."/".basename( $_FILES['photo']['name']);
              try{
                     if(move_uploaded_file($_FILES['photo']['tmp_name'], $path))
                     {
                             $str="insert into ".$dbname.".rencana_gis_file (kode, unit, keterangan, namafile, tipe, ukuran,  tanggal, karyawanid)
                                        values('".$_POST['kode']."','".$_POST['kodeorg']."','".$_POST['keterangan']."','".basename( $_FILES['photo']['name'])."','".$ext."',".$_FILES['photo']['size'].",'".date('Y-m-d')."',".$_SESSION['standard']['userid'].")";
                             mysql_query($str);
                             if(mysql_affected_rows($conn)>0)
                             {	  
                             echo"<script>
                                          parent.loadList();
                                     </script>";
                             }
                            else {
                              echo mysql_error($conn).$str;
                            }
                              //chmod($path, 0775);					
                     }
               }
               catch(Exception $e)
               {
                     echo "<script>alert(\"Error Writing File".addslashes($e->getMessage())."\");</script>";
               }
              }
              else
              {
                     echo "<script>alert('Filetype not support:".$ext." or too large');</script>";		 	
              }
}
?>

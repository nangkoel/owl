<?php
require_once('master_validation.php');
require_once('config/connection.php');

$notransaksi=$_POST['notransaksi'];
$spec=$_POST['spec'];
$path='filepad';

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
   if($_POST['aksi']=='del')
   {
        $str="delete from ".$dbname.".pad_photo where idlahan='".$_POST['notransaksi']."' and filename='".$_POST['filename']."'";
        mysql_query($str);   
        if(is_file($path."/".$_POST['filename'])){
            @unlink($path."/".$_POST['filename']);
        }
   }
   else{
    $lokasi=Array();
        $dir=$path;
        for($x=0;$x<count($_FILES['file']['name']);$x++)
        {
              $path = $dir."/".basename($_FILES['file']['name'][$x]);
          if($path!='photoqc/')
                     $lokasi[$x]=$path;
              else
                 $lokasi[$x]='';

              $size=$_FILES['file']['size'][$x];
              $max=75000;
              if($size>$max)
              {
                 echo"Error : file size beyond limit (75kb)";
                     $lokasi[$x]='';
                     exit(0);
              }
             $ext=split('[.]', basename( $_FILES['file']['name'][$x]));
              $ext=$ext[count($ext)-1];
              $ext=strtolower($ext);
              if($ext!='exe' and $ext!='js' and $ext!='php' and $ext!='perl' and $ext!='vbs' and $ext!='bat' and $ext!='com' and $ext!='jar')
              {
                    try{
                        if(basename( $_FILES['file']['name'][$x])=='')
                        {}
                        else{
                        if(move_uploaded_file($_FILES['file']['tmp_name'][$x], $path)){
                                $str="delete from ".$dbname.".pad_photo where idlahan='".$_POST['notransaksi']."' and filename='".basename($_FILES['file']['name'][$x])."'";
                                mysql_query($str);
                                $str="insert into ".$dbname.".pad_photo(idlahan,filename,filetype,filesize)
                                      values('".$_POST['notransaksi']."','".basename($_FILES['file']['name'][$x])."','".basename($_FILES['file']['type'][$x])."',".basename($_FILES['file']['size'][$x]).")";
                                $err='';
                                if(mysql_query($str))
                                {
                                    $err='Uploaded';
                                } 		
                                else
                                {
                                      echo "Error :".addslashes(mysql_error($conn))."<br>";
                                     break;
                                }                            
                        }
                        }
                }
                catch(Exception $e)
                {
                     echo "Error:".$e;
                     exit();
                }                  
              }
              else{
                  echo "<script>alert('Filetype not support:".$ext." or too large');history.go(-1)</script>";
                  exit();
              }
   }
   echo "<script>alert('Done');window.location='pad_uploadPhoto.php?notransaksi=".$_POST['notransaksi']."';</script>";
 }
}
?>
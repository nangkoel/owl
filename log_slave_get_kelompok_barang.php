<?php
require_once('master_validation.php');
require_once('config/connection.php');

        $kode	=$_POST['kode'];
        $nama =$_POST['nama'];
        $nama1 =$_POST['nama1'];        
        $noakun =$_POST['noakun'];
        $method     =$_POST['method'];
        $kelbiaya   =$_POST['kelbiaya'];


        switch($method){
                case 'delete':
                        $strx="delete from ".$dbname.".log_5klbarang where kode='".$kode."'";
                break;
                case 'update':
                        $strx="update ".$dbname.".log_5klbarang set 
                               kelompok='".$nama."',kelompok1='".$nama1."',
                               noakun='".$noakun."',kelompokbiaya='".$kelbiaya."'
                                   where kode='".$kode."'";
                break;	
                case 'insert':
                        $strx="insert into ".$dbname.".log_5klbarang(
                               kode,kelompok,kelompok1,noakun,kelompokbiaya)
                        values('".$kode."','".$nama."','".$nama1."','"
                                 .$noakun."','".$kelbiaya."')";
                break;
                default:
        break;	
        }
  if(mysql_query($strx))
  {}	
  else
        {
                echo " Gagal,".addslashes(mysql_error($conn));
        }	


$str="select * from ".$dbname.".log_5klbarang order by kelompok";
$res=mysql_query($str);
$no=0;	 
while($bar=mysql_fetch_object($res))
{
  $no+=1;	
  echo"<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$bar->kode."</td>
           <td>".$bar->kelompok."</td>
           <td>".$bar->kelompok1."</td>               
           <td>".$bar->kelompokbiaya."</td>
           <td>".$bar->noakun."</td>
                  <td>
                      <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kelompok."','".$bar->kelompok1."','".$bar->kelompokbiaya."','".$bar->noakun."');\"> 
                          <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKelompok('".$bar->kode."','".$bar->kelompok."');\">
                  </td>

          </tr>";	
}   

?>

<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$keluarganama		=$_POST['keluarganama'];
$keluargajk			=$_POST['keluargajk'];
$keluargatmplahir	=$_POST['keluargatmplahir'];

if($_POST['keluargatgllahir']=='')
  $_POST['keluargatgllahir']='00-00-0000';
$keluargatgllahir	=tanggalsystem($_POST['keluargatgllahir']);

$keluargapekerjaan	=$_POST['keluargapekerjaan'];
$keluargatelp		=$_POST['keluargatelp'];
$keluargaemail		=$_POST['keluargaemail'];
$karyawanid			=$_POST['karyawanid'];
$hubungankeluarga	=$_POST['hubungankeluarga'];
$keluargastatus		=$_POST['keluargastatus'];
$keluargapendidikan	=$_POST['keluargapendidikan'];
$keluargatanggungan	=$_POST['keluargatanggungan'];

$method=$_POST['method'];
$karyawanid=$_POST['karyawanid'];
$nomor=$_POST['nomor'];

if($nilai=='')
   $nilai=0;
if(isset($_POST['del']) or ($keluarganama!='') or isset($_POST['queryonly']))
{
        if(isset($_POST['del']) and $_POST['del']=='true')
        {
                $str="delete from ".$dbname.".sdm_karyawankeluarga where nomor=".$nomor;
        }
        else if(isset($_POST['queryonly']))
        {
                $str="select 1=1";
        }
        else
        {
                if($method=='insert')
                {
                $str="insert into ".$dbname.".sdm_karyawankeluarga
                     (	`karyawanid`,
                                `nama`,
                                `jeniskelamin`,
                                `tempatlahir`,
                                `tanggallahir`,
                                `hubungankeluarga`,
                                `status`,
                                `levelpendidikan`,
                                `pekerjaan`,
                                `telp`,
                                `email`,
                                `tanggungan`
                          )
                          values(".$karyawanid.",
                          '".$keluarganama."',
                          '".$keluargajk."',
                          '".$keluargatmplahir."',
                          ".$keluargatgllahir.",
                          '".$hubungankeluarga."',
                          '".$keluargastatus."',
                          '".$keluargapendidikan."',
                          '".$keluargapekerjaan."',
                          '".$keluargatelp."',
                          '".$keluargaemail."',
                          '".$keluargatanggungan."'
                          )";
                }
                else
                {
             $str="update ".$dbname.".sdm_karyawankeluarga set
                     `karyawanid`=".$karyawanid.",
                                `nama`='".$keluarganama."',
                                `jeniskelamin`='".$keluargajk."',
                                `tempatlahir`='".$keluargatmplahir."',
                                `tanggallahir`=".$keluargatgllahir.",
                                `hubungankeluarga`='".$hubungankeluarga."',
                                `status`='".$keluargastatus."',
                                `levelpendidikan`=".$keluargapendidikan.",
                                `pekerjaan`='".$keluargapekerjaan."',
                                `telp`='".$keluargatelp."',
                                `email`='".$keluargaemail."',
                                `tanggungan`=".$keluargatanggungan."
                                where nomor=".$nomor;	
                }
        }
        if(mysql_query($str))
           {
                 $str="select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, 
                       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur
                           from ".$dbname.".sdm_karyawankeluarga a,".$dbname.".sdm_5pendidikan b
                                where a.karyawanid=".$karyawanid." 
                                and a.levelpendidikan=b.levelpendidikan
                                order by hubungankeluarga";	
                 $res=mysql_query($str);
                 $no=0;
                 while($bar=mysql_fetch_object($res))
                 {
                 $no+=1;
              	  if($_SESSION['language']=='EN'){
                                    switch($bar->hubungankeluarga){
                                      case'Pasangan':
                                          $val='Couple';
                                          break;
                                      case'Anak':
                                          $val='Child';
                                          break;
                                      case'Ibu':
                                          $val='Mother';
                                          break;
                                      case'Bapak':
                                          $val='Father';
                                          break;
                                      case'Adik':
                                          $val='Younger brother/sister';
                                          break;        
                                      case'Kakak':
                                          $val='Older brother/sister';
                                          break;      
                                      case'Ibu Mertua':
                                          $val='Monther-in-law';
                                          break;   
                                      case'Bapak Mertua':
                                          $val='Father-in-law';
                                          break;   
                                      case'Sepupu':
                                          $val='Cousin';
                                          break;  
                                      case'Ponakan':
                                          $val='Nephew';
                                          break;                                
                                      default:
                                          $val='Foster child';
                                          break;                         
                                 }
               		 }
					 else
					 $val=$bar->hubungankeluarga;
					 
                     if($_SESSION['language']=='EN' && $bar->status=='Kawin')
                       $gal='Married';
                   if($_SESSION['language']=='EN' && ($bar->status=='Bujang' or $bar->status=='Lajang'))
                          $gal='Single';
					else
						$gal=$bar->status;
					
						                    
                 echo"	  <tr class=rowcontent>
                                  <td class=firsttd>".$no."</td>
                                  <td>".$bar->nama."</td>			  
                                  <td>".$bar->jeniskelamin."</td>
                                  <td>".$val."</td>			  
                                  <td>".$bar->tempatlahir.",".tanggalnormal($bar->tanggallahir)."</td>			  
                                  <td>".$gal."</td>
                                                                  <td>".$bar->umur."Yrs</td>
                                  <td>".$bar->kelompok."</td>
                                  <td>".$bar->pekerjaan."</td>
                                  <td>".$bar->telp."</td>
                                  <td>".$bar->email."</td>
                                  <td>".$bar->tanggungan1."</td>
                                  <td>
                                    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->nama."','".$bar->jeniskelamin."','".$bar->tempatlahir."','".tanggalnormal($bar->tanggallahir)."','".$bar->hubungankeluarga."','".$bar->status."','".$bar->levelpendidikan."','".$bar->pekerjaan."','".$bar->telp."','".$bar->email."','".$bar->tanggungan."','".$bar->nomor."');\"> 
                                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKeluarga('".$karyawanid."','".$bar->nomor."');\">
                                  </td>
                                </tr>";	 	
                 }
            }
                else
                {
                        echo " Gagal:".addslashes(mysql_error($conn)).$str;
                }
}
else
{
        echo " Error; Data incomplete";
}
?>

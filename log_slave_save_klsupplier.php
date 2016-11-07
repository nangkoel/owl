<?php
require_once('master_validation.php');
require_once('config/connection.php');

$tipe		=$_POST['tipe'];
$kode       =$_POST['kode'];
$kelompok   =$_POST['kelompok'];
$noakun     =$_POST['noakun'];
$method     =$_POST['method'];

$strx="select 1=1";
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5klsupplier where kode='".$kode."'";
		break;
		case 'update':
		   $strx="update ".$dbname.".log_5klsupplier set tipe='".$tipe."',
		          kelompok='".$kelompok."', noakun='".$noakun."' 
				  where kode='".$kode."'";		
		break;	
		case 'insert':
			$strx="insert into ".$dbname.".log_5klsupplier(
			       kode,kelompok,noakun,tipe)
			values('".$kode."','".$kelompok."','".$noakun."','".$tipe."')";	   
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
	

$str=" select * from ".$dbname.".log_5klsupplier where tipe='".$tipe."' order by kelompok";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bar->kode."</td>
			  <td>".$bar->kelompok."</td>
			  <td>".$bar->tipe."</td>
			  <td>".$bar->noakun."</td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlSupplier('".$bar->kode."');\"></td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Update' onclick=\"editKlSupplier('".$bar->kode."','".$bar->kelompok."','".$bar->tipe."','".$bar->noakun."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>
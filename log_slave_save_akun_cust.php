<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

	$kode=$_POST['kode'];
	$kelompok=$_POST['kelompok'];
	$noakun=$_POST['noakun'];
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.". pmn_4klcustomer where kode='".$kode."'";
			
		break;
		case 'update':
		$strx="update ".$dbname.". pmn_4klcustomer set kelompok='".$kelompok."',noakun='".$noakun."' where kode='".$kode."'";
		break;	
		case 'insert':

		$strx="insert into ".$dbname.".pmn_4klcustomer(
					   kode,kelompok,noakun)
				values('".$kode."','".$kelompok."','"
						.$noakun."')";
						//echo $strx; exit();
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
	
$str="select * from ".$dbname.".pmn_4klcustomer order by kode desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$noakun=$bar->noakun;
		$spr="select * from  ".$dbname.".keu_5akun where `noakun`='".$noakun."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bar->kode."</td>
			  <td>".$bar->kelompok."</td>
			   <td>".$bar->noakun."</td>
			  <td>".$bas->namaakun."</td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kelompok."','".$bar->noakun."','".$bas->namaakun."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delKlmpkplgn('".$bar->kode."','".$bar->kelompok."','".$bar->noakun."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
?>
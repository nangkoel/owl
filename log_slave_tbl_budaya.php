<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	
	$kode=$_POST['kode'];
	$kodeorg=$_POST['kodeorg'];
	$budidaya=$_POST['budidaya'];
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".kebun_5budidaya where kode='".$kode."'";
			
		break;
		case 'update':
		$strx="update ".$dbname.".kebun_5budidaya set kodeorg='".$kodeorg."',budidaya='".$budidaya."' where kode='".$kode."'";
		break;	
		case 'insert':
		/*print_r($_POST);
		exit();*/
		$strx="insert into ".$dbname.".kebun_5budidaya(
					   kode,kodeorg,budidaya)
				values('".$kode."','".$kodeorg."','"
						.$budidaya."')";
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
	
	//ambil data dari tabel 
	 		
		$srt="select * from ".$dbname.".kebun_5budidaya order by kode desc";  //echo $srt;
		if($rep=mysql_query($srt))
		  {
			while($bar=mysql_fetch_object($rep))
			{
					
			//get akun
			$spr="select * from  ".$dbname.".organisasi where `kodeorganisasi`='".$bar->kodeorg."'";
			$rej=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rej);
			$no+=1;
			echo"<tr class=rowcontent>
				  <td>".$no."</td>
				  <td>".$bas->kodeorganisasi."</td>
				  <td>".$bas->namaorganisasi."</td>
				  <td>".$bar->kode."</td>
				  <td>".$bar->budidaya."</td>
				  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kode."','".$bar->kodeorg."','".$bar->budidaya."');\"></td>
				  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delTbldya('".$bar->kode."');\"></td>
				 </tr>";
			}
		  }
		  else
		 {
			echo " Gagal,".(mysql_error($conn));
		 }
	 ?>
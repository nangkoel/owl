<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	
	$koderorg=$_POST['koderorg'];
	$kapasitasolah=$_POST['kapasitasolah'];
	$berlakusampai=tanggalsystemd($_POST['berlakusampai']);
	$jammulai=tanggalsystemd($_POST['jam_mulai']);
	$jamselesai=tanggalsystemd($_POST['jam_selesai']);
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
		$strx="delete from ".$dbname.".pabrik_5jampengolahan where koderorg='".$koderorg."' ";
			//echo  $strx; exit();
		break;
		case 'update':
		//print_r($_POST);
		$strx="update ".$dbname.".pabrik_5jampengolahan set kapasitasolah='".$kapasitasolah."',jammulai='".$jammulai."',jamselesai='".$jamselesai."',berlakusampai='".$berlakusampai."' where koderorg='".$koderorg."'"; //echo $strx; exit();
		break;	
		case 'insert':
		$strx="insert into ".$dbname.".pabrik_5jampengolahan(
					   koderorg,kapasitasolah,jammulai,jamselesai,berlakusampai)
				values('".$koderorg."','".$kapasitasolah."','".$jammulai."','".$jamselesai."','".$berlakusampai."')";
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
	
  $str="select * from ".$dbname.".pabrik_5jampengolahan order by koderorg desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$noakun=$bar->noakun;
		$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->koderorg."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		//$jam_mulai=substr($bar->jammulai,0,2);
	
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bas->namaorganisasi."</td>
			  <td>".$bar->kapasitasolah."</td>
			  <td>".tanggalnormald($bar->jammulai)."</td>
			  <td>".tanggalnormald($bar->jamselesai)."</td>
			  <td>".tanggalnormald($bar->berlakusampai)."</td>
			  
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->koderorg."','".$bar->kapasitasolah."','".tanggalnormald($bar->jammulai)."','".tanggalnormald($bar->jamselesai)."','".tanggalnormald($bar->berlakusampai)."','".$bas->namaorganisasi."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delJampeng('".$bar->koderorg."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
	
?>
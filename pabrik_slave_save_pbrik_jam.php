<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	
	$koderorg=$_POST['koderorg'];
	$kapasitasolah=$_POST['kapasitasolah'];
	$berlakusampai=tanggalsystemd($_POST['berlakusampai']);
	$jammulai=tanggalsystemd($_POST['jam_mulai']);
	$jamselesai=tanggalsystemd($_POST['jam_selesai']);
	$kapasitaslori = $_POST['kapasitaslori'];
	$method=$_POST['method'];
	
	switch($method){
		case 'delete':
		$strx="delete from ".$dbname.".pabrik_5jampengolahan where koderorg='".$koderorg."' ";
			//echo  $strx; exit();
		break;
		case 'update':
		//print_r($_POST);
		$strx="update ".$dbname.".pabrik_5jampengolahan set kapasitasolah='".$kapasitasolah."',jammulai='".$jammulai."',jamselesai='".$jamselesai."',berlakusampai='".$berlakusampai."',kapasitaslori=".$kapasitaslori." where koderorg='".$koderorg."'"; 
		//echo "warning:".$strx; exit();
		if(mysql_query($strx))			
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;	
		case 'insert':
		$sql="select from ".$dbname.".pabrik_5jampengolahan where kodeorg='".$koderorg."'";
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_row($query);
		if($res<1)
		{
			$strx="insert into ".$dbname.".pabrik_5jampengolahan(
						   koderorg,kapasitasolah,jammulai,jamselesai,berlakusampai,kapasitaslori)
					values('".$koderorg."','".$kapasitasolah."','".$jammulai."','".$jamselesai."','".$berlakusampai."',".$kapasitaslori.")";
			if(mysql_query($strx))				//echo $strx; exit();
			echo"";
			else
			echo " Gagal,".(mysql_error($conn));
		}
		else
		{
			echo"warning:This Factory Already Input";
			exit();
		}
		break;
		case'load_data':
		//echo "warning:Masuk";
		$str="select * from ".$dbname.".pabrik_5jampengolahan order by koderorg desc"; //echo "warning:".$str;exit();
		if($res=mysql_query($str))
		{
		while($bar=mysql_fetch_object($res))
		{
		$noakun=$bar->noakun;
		$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->koderorg."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		
		//echo $minute_selesai; exit();
		echo"<tr class=rowcontent id='tr_".$no."'>
		<td>".$no."</td>
		<td id='nmorg_".$no."'>".$bas->namaorganisasi."</td>
		<td id='kpsits_".$no."'>".$bar->kapasitasolah."</td>
		<td id='strt_".$no."'>".tanggalnormald($bar->jammulai)."</td>
		<td id='end_".$no."'>".tanggalnormald($bar->jamselesai)."</td>
		<td id='tglex_".$no."'>".tanggalnormald($bar->berlakusampai)."</td>
		<td id='kplori_".$no."'>".$bar->kapasitaslori."</td>
		<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->koderorg."','".$bar->kapasitasolah."','".tanggalnormald($bar->jammulai)."','".tanggalnormald($bar->jamselesai)."','".tanggalnormald($bar->berlakusampai)."','".$bar->kapasitaslori."');\"></td>
		<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delJampeng('".$bar->koderorg."');\"></td>
		</tr>";
		}	 	   	
		}	
		else
		{
		echo " Gagal,".(mysql_error($conn));
		}	

		break;
		default:
        break;	
	}
/*	if(mysql_query($strx))
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
			  <td>".$bar->kapasitaslori."</td>
			  <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->koderorg."','".$bar->kapasitasolah."','".tanggalnormald($bar->jammulai)."','".tanggalnormald($bar->jamselesai)."','".tanggalnormald($bar->berlakusampai)."','".$bar->kapasitaslori."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delJampeng('".$bar->koderorg."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
	*/
?>
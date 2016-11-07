<?php
require_once('master_validation.php');
require_once('config/connection.php');

	$jumlah		=$_POST['jumlah'];
	$kodebarang	=$_POST['kodebarang'];
	$dari		=$_POST['dari'];
	$ke			=$_POST['ke'];
	$method 	=$_POST['method'];
	$keterangan =$_POST['keterangan'];


	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".log_5stkonversi where kodebarang='".$kodebarang."' 
			       and satuankonversi='".$ke."'
				   and darisatuan='".$dari."'";
		break;
		case 'update':
			
		break;	
		case 'insert':
			$strx="insert into ".$dbname.".log_5stkonversi(
			       kodebarang,satuankonversi,darisatuan,jumlah,keterangan)
			values('".$kodebarang."','".$ke."','"
			         .$dari."',".$jumlah.",'".$keterangan."')";	   
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
	

$str=" select * from ".$dbname.".log_5stkonversi where kodebarang='".$kodebarang."' order by jumlah";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		echo"<tr class=rowcontent>
		      <td>".$no."</td>
		      <td>".$bar->darisatuan."</td>
			  <td>".$bar->satuankonversi."</td>
			  <td align=right>".$bar->jumlah."</td>
			  <td>".$bar->keterangan."</td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delConversi('".$bar->kodebarang."','".$bar->darisatuan."','".$bar->satuankonversi."');\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
?>
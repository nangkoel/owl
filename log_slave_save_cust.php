<?php
	require_once('master_validation.php');
	require_once('config/connection.php');

	$kodecustomer=$_POST['kodecustomer'];
	$namacustomer=$_POST['namacustomer'];
  	$alamat=$_POST['alamat'];
	$kota=$_POST['kota'];
	$telepon=$_POST['telepon'];
	$kontakperson=$_POST['kontakperson'];
	$akun=$_POST['akun'];
	$plafon=$_POST['plafon'];
	$nilaihutang=$_POST['nilaihutang'];
	$npwp=$_POST['npwp'];
	$noseri=$_POST['noseri'];
	$klcustomer=$_POST['klcustomer'];
	$pk=$_POST['pk'];
	$jpk=$_POST['jpk'];
	$method=$_POST['method'];

	//print_r($_POST);
	switch($method){
		case 'delete':
			$strx="delete from ".$dbname.".pmn_4customer where kodecustomer='".$kodecustomer."'";
			
		break;
		case 'update':
		//print_r($_POST); exit();
		$strx="update ".$dbname.". pmn_4customer set namacustomer='".$namacustomer."',alamat='".$alamat."',kota='".$kota."',
		telepon='".$telepon."',kontakperson='".$kontakperson."',
		akun='".$akun."',plafon='".$plafon."',
		nilaihutang='".$nilaihutang."',npwp='".$npwp."'
		,noseri='".$noseri."',klcustomer='".$klcustomer."',pk='".$pk."',jpk='".$jpk."'
		where kodecustomer='".$kodecustomer."'";
		//exit("Error:$strx");
		break;	
		
		case 'insert':
		
		$strx="insert into ".$dbname.".pmn_4customer
		(`kodecustomer`, `namacustomer`, `alamat`, `kota`, `telepon`, `kontakperson`, `akun`, `plafon`, `nilaihutang`, `npwp`, `noseri`, `klcustomer`,pk,jpk)
		values
		('".$kodecustomer."','".$namacustomer."','".$alamat."','".$kota."','".$telepon."','".$kontakperson."','".$akun."','".$plafon."','".$nilaihutang."','".$npwp."','".$noseri."','".$klcustomer."','".$pk."','".$jpk."')"; //echo $strx; exit();
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
	

	 	//ambil data dari tabel kelompok customer
	 		
		$srt="select * from ".$dbname.".pmn_4customer order by kodecustomer desc";  //echo $srt;
		if($rep=mysql_query($srt))
		  {
			$no=0;
			while($bar=mysql_fetch_object($rep))
			{
			//get kelompok cust
			$sql="select * from ".$dbname.".pmn_4klcustomer where `kode`='".$bar->klcustomer."'";
			$query=mysql_query($sql) or die(mysql_error($conn));
			$res=mysql_fetch_object($query);
			
			//get akun
			$spr="select * from  ".$dbname.".keu_5akun where `noakun`='".$bar->akun."'";
			$rej=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rej);
			$no+=1;
			echo"<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$bar->kodecustomer."</td>
				<td>".$bar->kontakperson."</td>
				<td>".$bar->telepon."</td>
                                <td>".$bar->telepon."</td>
				<td>".$bar->akun."</td>
				<td>".$bas->namaakun."</td>
				<td>".$bar->plafon."</td>
				<td>".$bar->nilaihutang."</td>
				<td>".$res->kelompok."</td>
				<td>".$bar->pk."</td>
				<td>".$bar->jpk."</td>
				<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodecustomer."','".$bar->namacustomer."','".$bar->alamat."','".$bar->kota."','".$bar->telepon."','".$bar->kontakperson."','".$bar->akun."','".$bar->plafon."','".$bar->nilaihutang."','".$bar->npwp."','".$bar->noseri."','".$bar->klcustomer."','".$bas->namaakun."','".$res->kelompok."','".$bar->pk."','".$bar->jpk."');\"></td>
				<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPlgn('".$bar->kodecustomer."');\"></td>
				</tr>";
			}
		  }
		  else
		 {
			echo " Gagal,".(mysql_error($conn));
		 }
	 ?>

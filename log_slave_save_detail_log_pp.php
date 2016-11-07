<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	$nopp=$_POST['rnopp'];
	$tanggal=tanggalsystem($_POST['rtgl_pp']);
	$user_id=$_POST['usr_id'];
	$kodeorg=$_POST['rkd_bag'];
	$method=$_POST['method'];
	
function statusPp($o)
{
	$tes=3;
	for($i=0;$i<$tes;$i++)
	{
		if($i=='0')
		{
			$a="<a href=# onclick=prosPp(".$i.") title=\"Confirm All Data\">Validate</a>";
		}
		elseif($i=='1')
		{
			$a="<a href=# onclick=prosPp(".$i.") title=\"Need Approval\">Need Approval</a>";
		}
		elseif($i=='2')
		{
			$a="<a href=# onclick=prosPp(".$i.") title=\"Can Create PO\">Approved</a>";
		}
		return($a);
	}
}

	switch($method)
	{
		case'update':
		$strx="update ".$dbname.".log_prapoht set tanggal='".$tanggal."',dibuat='".$user_id."' where nopp='".$nopp."'";
		if(!mysql_query($strx))
				{
					echo "Gagal,".(mysql_error($conn));exit();
				}	
		$ql="select `nopp` from ".$dbname.".log_prapodt where `nopp`='".$nopp."'";
		$qry=mysql_query($ql) or die(mysql_error());
		$hsl=mysql_fetch_object($qry);
		if($nopp==$hsl->nopp)
		{
			foreach($_POST['kdbrg'] as $row=>$Act)
			{
				$kdbrg=$Act;
				$nmbrg=$_POST['nmbrg'][$row];
				$rjmlhDiminta=$_POST['rjmlhDiminta'][$row];
				$rkd_angrn=$_POST['rkd_angrn'][$row];
				$rtgl_sdt=tanggalsystem($_POST['rtgl_sdt'][$row]);
				$ktrang=$_POST['ketrng'][$row];
				$sqp="update ".$dbname.".log_prapodt set `jumlah`='".$rjmlhDiminta."',`kd_anggran`='".$rkd_angrn."',`tgl_sdt`='".$rtgl_sdt."',`keterangan`='".$ktrang."' where nopp='".$nopp."' and kodebarang='".$kdbrg."'"; //echo $sqp; exit();
				if(!mysql_query($sqp))
				{
					echo "Gagal,".(mysql_error($conn));exit();
				}	
			
			}
		}
		break;
		case 'insert':
				foreach($_POST['kdbrg'] as $row=>$Act)
				{
					$kdbrg=$Act;
					$nmbrg=$_POST['nmbrg'][$row];
					$rjmlhDiminta=$_POST['rjmlhDiminta'][$row];
					$rkd_angrn=$_POST['rkd_angrn'][$row];
					$rtgl_sdt=tanggalsystem($_POST['rtgl_sdt'][$row]);
					$ketrng=$_POST['ketrng'][$row];
					$sqp="insert into ".$dbname.".log_prapodt(`nopp`, `kodebarang`, `jumlah`,`kd_anggran`,`tgl_sdt`,`keterangan`) values('".$nopp."','".$kdbrg."','".$rjmlhDiminta."','".$rkd_angrn."','".$rtgl_sdt."','".$ketrng."')"; //echo $sqp; exit();
					if(!mysql_query($sqp))
					{
						//echo $sqp; 
						echo "Gagal,".(mysql_error($conn));exit();
					}	
					
				}
		break;
	}
	
  $str="select * from ".$dbname.".log_prapoht order by nopp desc";
  if($res=mysql_query($str))
  {
	while($bar=mysql_fetch_object($res))
	{
		$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$bar->kodeorg."' or induk='".$bar->kodeorg."'";
		$rep=mysql_query($spr) or die(mysql_error($conn));
		$bas=mysql_fetch_object($rep);
		$no+=1;
		
		echo"<tr class=rowcontent id='tr_".$no."'>
		      <td>".$no."</td>
		      <td>".$bar->nopp."</td>
			  <td>".tanggalnormal($bar->tanggal)."</td>
			  <td>".$bar->kodeorg."</td>
			  <td>".$bas->namaorganisasi."</td>
			  <td>".statusPp($bar->close)."</td>
		 <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->nopp."','".tanggalnormal($bar->tanggal)."','".$bar->kodeorg."');\"></td>
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPp('".$bar->nopp."');\"></td>
			  <td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_prapoht','".$bar->nopp."','','log_slave_print_log_pp',event);\"></td>
			 </tr>";
	}	 	   	
  }	
  else
	{
		echo " Gagal,".(mysql_error($conn));
	}	
?>
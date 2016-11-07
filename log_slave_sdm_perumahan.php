<?php
	require_once('master_validation.php');
	require_once('config/connection.php');
	include('lib/nangkoelib.php');
	include_once('lib/zLib.php');
	$kode_org=$_POST['kd_org'];
	$blok=$_POST['blok'];
	$normh=$_POST['no_rmh'];
	$type_rmh=$_POST['tipermh'];
	$thn_bgn=$_POST['thnbgn'];
	$kndsi=$_POST['kndsi_rmh'];
	$catatan=$_POST['note'];
	$alamat=$_POST['almt_rmh'];
	$kde_asset=$_POST['kd_asset'];
	$method=$_POST['method'];
	$user_id=$_SESSION['standard']['userid'];
	$kary_id=$_POST['kd_kary'];
	$org_code=$_POST['code_org'];
	$kompleks=$_POST['kmplk'];
	
	switch($method)
	{
		case 'save_header':
		if(($kode_org=='')||($blok=='')||($normh==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		else
		{
			$sql="select * from ".$dbname.".sdm_perumahanht where kodeorg='".$kode_org."' and blok='".$blok."' and norumah='".$normh."'";
			$query=mysql_query($sql) or die(mysql_error());
			$res=mysql_fetch_row($query);
			if($res<1)
			{
				$insrt="insert into ".$dbname.".sdm_perumahanht (`kodeorg`,`kompleks`,`blok`,`norumah`,`tipe`,`tahunpembuatan`,`kondisi`,`keterangan`,`alamat`,`user`) 
						values ('".$kode_org."','".$kompleks."','".$blok."','".$normh."','".$type_rmh."','".$thn_bgn."','".$kndsi."','".$catatan."','".$alamat."','".$user_id."')";
				if(mysql_query($insrt))
				{
					
				}
				else
				{
					echo " Gagal,".(mysql_error($conn));
				}
			}
			else
			{
				echo "warning:data telah terinput silahkan ulangi kembali";
			}
		}
		break;
			
		case 'save_asset':
		if(($kode_org=='')||($blok=='')||($normh=='')||($kde_asset==''))
		{
			echo"Warning:Please Complete The Form";
			exit();
		}
		else
		{
			$sql="insert into ".$dbname.".sdm_perumahandt (`kodeorg`,`blok`,`norumah`,`kodeasset`) values ('".$kode_org."','".$blok."','".$normh."','".$kde_asset."')";
			if(mysql_query($sql))
			{
			
			}
			else
			{
			echo " Gagal,".(mysql_error($conn));
			}
		}
		break;
		case 'save_penghuni':
		if(($kode_org=='')||($blok=='')||($normh=='')||($kary_id==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
		else
		{
			$sql2="select karyawanid from ".$dbname.".sdm_penghunirumah where `karyawanid`='".$kary_id."'";
			$query2=mysql_query($sql2) or die(mysql_error());
			$res=mysql_fetch_row($query2);
			//echo "warning:".$res."____".$sql2;
			if($res>0)
			{
				echo"Warning:Can`t Use That Name";
				exit();
			}
			else
			{
				$sql="insert into ".$dbname.".sdm_penghunirumah (`kodeorg`,`blok`,`norumah`,`user`,`karyawanid`) values ('".$kode_org."','".$blok."','".$normh."','".$user_id."','".$kary_id."')";//echo "warning :".$sql;
				if(mysql_query($sql))
				{
				
				}
				else
				{
				echo " Gagal,".(mysql_error($conn));
				}
			}
		}
		break;
		
		//al about update 
		case'update_headher':
		$sql="update ".$dbname.".sdm_perumahanht set `kompleks`='".$kompleks."',`tipe`='".$type_rmh."',`tahunpembuatan`='".$thn_bgn."',`kondisi`='".$kndsi."',`keterangan`='".$catatan."',`alamat`='".$alamat."',`user`='".$user_id."' where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		case'update_asset':
		$sql="update ".$dbname.".sdm_perumahandt set kodeasset='".$kde_asset."' where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		case'update_penghuni':
		
		$sql="update ".$dbname.".sdm_penghunirumah set karyawanid='".$kary_id."',`user`='".$user_id."' where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		
		//load new data
		case 'load_new_data':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$sql2="select count(*) as jmlhrow from ".$dbname.".sdm_perumahanht where kodeorg='".$org_code."'"; 
		$str="select * from ".$dbname.".sdm_perumahanht where kodeorg='".$org_code."' order by `update` desc limit ".$offset.",".$limit."";// echo $str;
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$isiOpt=array('B-BD'=>'Baik Bisa Dipakai','B-TD'=>'Baik Tidak Dipakai','R-BD'=>'Rusak Bisa dipakai','R-TD'=>'Rusak Tidak Dipakai');
		$query=mysql_query($str) or die(mysql_error($conn));
		while($res=mysql_fetch_assoc($query))
		{
			$no+=1;
			echo"<tr class=\"rowcontent\" id=detail_tra_".$no.">
			<td>".$no."</td>
			<td>".$res['kodeorg']."</td>
                        <td>".$res['kompleks']."</td>    
			<td>".$res['blok']."</td>
			<td align=center>".$res['norumah']."</td>
			<td>".$res['tipe']."</td>
			<td>".$res['tahunpembuatan']."</td>
			<td align=center>".$isiOpt[$res['kondisi']]."</td>
			<td>".$res['keterangan']."</td><td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."','".$res['tipe']."','".$res['tahunpembuatan']."','".$res['kondisi']."','".$res['keterangan']."','".$res['alamat']."');\" >
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delHeader('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."');\" >
		</td>";
		}
		echo"<tr>
		<td colspan=9 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
		<br>
		<button class=mybutton onclick=cariBast('".($page-1)."','".$org_code."');>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast('".($page+1)."','".$org_code."');>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";  
		break;	
		///load new data  assset
		case 'load_new_data_asset':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$sql2="select count(*) as jmlhrow from ".$dbname.".sdm_perumahandt where kodeorg='".$org_code."'"; 
		$str="select * from ".$dbname.".sdm_perumahandt where kodeorg='".$org_code."' ORDER BY `update` desc limit ".$offset.",".$limit."";// echo $str;
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$query=mysql_query($str) or die(mysql_error($conn));
		while($res=mysql_fetch_assoc($query))
		{
			$sql3="select namasset from ".$dbname.".sdm_daftarasset where `kodeasset`='".$res['kodeasset']."'";
			$query3=mysql_query($sql3) or die(mysql_error());
			$res3=mysql_fetch_assoc($query3);
			//$res['kodeasset']=$res3['namasset'];
			$no+=1;
			echo"<tr class=\"rowcontent\" id=detail_trp_".$no.">
			<td>".$no."</td>
			<td>".$res['kodeorg']."</td>
			<td>".$res['blok']."</td>
			<td align=center>".$res['norumah']."</td>
			<td>".$res3['namasset']."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldAsset('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."','".$res['kodeasset']."');\" >
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delAsset('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."','".$res['kodeasset']."');\" >
		</td>";
		}
		echo"<tr>
		<td colspan=7 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
		<br>
		<button class=mybutton onclick=cariBastAsset('".($page-1)."','".$org_code."');>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBastAsset('".($page+1)."','".$org_code."');>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";  
		break;	
		///load new data  penghuni
		case 'load_new_data_penghuni':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$sql2="select count(*) as jmlhrow from ".$dbname.".sdm_penghunirumah where kodeorg='".$org_code."'"; 
		$str="select * from ".$dbname.".sdm_penghunirumah where kodeorg='".$org_code."' order by `update` desc limit ".$offset.",".$limit."";
		//echo "warning:".$str;
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$query=mysql_query($str) or die(mysql_error($conn));
		while($res=mysql_fetch_assoc($query))
		{
			$sdt_kry="select namakaryawan from ".$dbname.".datakaryawan where `karyawanid`='".$res['karyawanid']."'";
			$qdt_kry=mysql_query($sdt_kry) or die(mysql_error());
			$rdt_kry=mysql_fetch_assoc($qdt_kry);
			$no+=1;
			echo"<tr class=\"rowcontent\" id=detail_tr>
			<td>".$no."</td>
			<td>".$res['kodeorg']."</td>
			<td>".$res['blok']."</td>
			<td>".$res['norumah']."</td>
			<td>".$rdt_kry['namakaryawan']."</td>
			<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldPenghuni('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."','".$res['karyawanid']."');\" >
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPenghuni('".$res['kodeorg']."','".$res['blok']."','".$res['norumah']."','".$res['karyawanid']."');\" >
		</td>";
		}
		echo"<tr>
		<td colspan=7 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
		<br>
		<button class=mybutton onclick=cariBastPenghuni('".($page-1)."','".$org_code."');>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBastPenghuni('".($page+1)."','".$org_code."');>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";  
		break;	
		
		//delete section 
		case'delHeader':
		$sql="delete from ".$dbname.".sdm_perumahanht where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
		if(mysql_query($sql))
		{
			$sql2="delete from ".$dbname.".sdm_perumahandt where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
			//echo "warning:".$sql;
			if(mysql_query($sql2))
			echo"";
			else
			echo " Gagal,".(mysql_error($conn));
			$sql3="delete from ".$dbname.".sdm_penghunirumah where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."'";
			//echo "warning:".$sql;
			if(mysql_query($sql3))
			echo"";
			else
			echo " Gagal,".(mysql_error($conn));
		}
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		
		case'delAsset':
		$sql="delete from ".$dbname.".sdm_perumahandt where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."' and `kodeasset`='".$kde_asset."'";
		//echo "warning:".$sql;
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		case'delPenghuni':
		$sql="delete from ".$dbname.".sdm_penghunirumah where `kodeorg`='".$kode_org."' and `blok`='".$blok."' and `norumah`='".$normh."' and `karyawanid`='".$kary_id."'";
		//echo "warning:".$sql;
		if(mysql_query($sql))
		echo"";
		else
		echo " Gagal,".(mysql_error($conn));
		break;
		case'getData':
		$sGet="select * from ".$dbname.".sdm_perumahanht where kodeorg='".$org_code."' and blok='".$blok."' and norumah='".$normh."'";
		$qGet=mysql_query($sGet) or die(mysql_error());
		$rGet=mysql_fetch_assoc($qGet);
		echo $rGet['tipe']."###".$rGet['tahunpembuatan']."###".$rGet['kondisi']."###".$rGet['keterangan']."###".$rGet['alamat']."###".$rGet['kompleks'];
		break;
		default:
		break;
	}
?>
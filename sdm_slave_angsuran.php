<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

  $kdOrg=$_POST['kdOrg'];
  $method=$_POST['method'];
  
  $nmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');

switch($method)
{	
	case'getKar':
		if($kdOrg=='' or $kdOrg=='0')
		{
			$str1="select * from ".$dbname.".datakaryawan
				 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
				  and tipekaryawan!=0 
				  and lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
				  order by namakaryawan";
			
		}
		else
		{
			$str1="select * from ".$dbname.".datakaryawan
				 where (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") 
				  and tipekaryawan!=0 
				  and lokasitugas='".$kdOrg."'
				  order by namakaryawan";
		}
		$res1=mysql_query($str1) or die (mysql_error($conn));
		while($bar1=mysql_fetch_object($res1))
		{
			$opt1.="<option value=".$bar1->karyawanid.">".$bar1->namakaryawan." -- ".$bar1->nik." -- ".$bar1->lokasitugas."[".$nmOrg[$bar1->lokasitugas]."]</option>";
		}
		echo $opt1;
	break;
	
	
	case'loadData':
	
	
	
			
		$str="select * from ".$dbname.".sdm_ho_component
		where name like '%Angs%'";
		$res=mysql_query($str,$conn);
		$arr=Array();
		$opt='';
		while($bar=mysql_fetch_object($res))
		{
			$opt.="<option value=".$bar->id.">".$bar->name."</option>";
			$arr[$bar->id]=$bar->name;
		}	

		if($kdOrg=='' or $kdOrg=='0')
		{
			$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			  where u.tipekaryawan!=0 and 
			  u.lokasitugas in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
			  order by namakaryawan";	
		}
		else
		{
			$str="select a.*,u.namakaryawan,u.tipekaryawan,u.lokasitugas,u.nik from ".$dbname.".sdm_angsuran a left join ".$dbname.".datakaryawan u on a.karyawanid=u.karyawanid
			  where u.tipekaryawan!=0 and 
			  u.lokasitugas='".$kdOrg."'
			  order by namakaryawan";	
		}
		$res=mysql_query($str,$conn);
		$no=0;
		while($bar=mysql_fetch_object($res))
		{			  
		   $no+=1;
		   echo"<tr class=rowcontent>
					<td class=firsttd>".$no."</td>
					<td>".$bar->nik."</td>
						<td>".$bar->namakaryawan."</td>
						<td>".$bar->lokasitugas." -- ".$nmOrg[$bar->lokasitugas]." </td>
						<td>".$arr[$bar->jenis]."</td>
						<td align=right>".number_format($bar->total,2,'.',',')."</td>
						<td align=center>".$bar->start."</td>
						<td align=center>".$bar->end."</td>
						<td align=right>".$bar->jlhbln."</td>
						<td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
						<td align=center>".($bar->active==1?"Active":"Not Active")."</td>
								<td>
					 <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">
					 &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">		
								</td>				
				  </tr>"; 			
		}
	
	
	
	
	break;
							
	default;
	break;              
}
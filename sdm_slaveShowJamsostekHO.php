<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
//+++++++++++++++++++++++++++++++++++++++++++++
require_once('config/connection.php');
$val=trim($_POST['val']);
$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str,$conn);
//default
$karyawan=0.02;
$perusahaan=4.54;
$angka=1;
while($bar=mysql_fetch_object($res))
{
	if($bar->id=='karyawan')
	{
		$karyawan=$bar->value/100;
		$angka=$bar->value;
	}
	else
	{
		$perusahaan=$bar->value/100;
	}
}
		$str="select e.name,e.startdate,e.nojms,d.value,d.karyawanid,d.periode 
		      from ".$dbname.".sdm_ho_employee e, ".$dbname.".sdm_ho_detailmonthly d
		      where e.karyawanid=d.karyawanid and e.operator='".$_SESSION['standard']['username']."'
			  and d.periode='".$val."' and d.component=3 
		      order by name";
		$res=mysql_query($str,$conn);		
		$no=0;
		$ttl=0;//grand total
		$tvp=0;//total perusahaan
		$tkar=0;//total karyawan
		$total=0;//total per karyawan
		while($bar=mysql_fetch_object($res))
		{			  
		   $valPerusahaan=(($bar->value*-1)/$angka)*100*$perusahaan;//di satupersenkan dulu,di seratuskan dan dikali persentase perush
		   
		   $tvp+=$valPerusahaan;
		   $kar+=($bar->value*-1);
		   
		   $total=$valPerusahaan+($bar->value*-1);
	       $stru="select sum(value) as gjk from ".$dbname.".sdm_ho_detailmonthly where (component=1 or component=2)
			       and updatedby='".$_SESSION['standard']['username']."'
			       and periode='".$val."' and karyawanid=".$bar->karyawanid;
			 $resu=mysql_query($stru,$conn);
             $gjkotor=0;
             while($baru=mysql_fetch_object($resu))
               {
			    $gjkotor=$baru->gjk;
               }		   
		   $no+=1;
		   echo"<tr class=rowcontent>
			    <td class=firsttd>".$no."</td>
			    <td>".$bar->karyawanid."</td>
				<td>".$bar->name."</td>
				<td>".$bar->nojms."</td>
				<td align=right>".tanggalnormal($bar->startdate)."</td>
				<td align=center>".$bar->periode."</td>
				<td align=right>".number_format(($bar->value*-1),2,'.',',')."</td>
				<td align=right>".number_format($valPerusahaan,2,'.',',')."</td>
				<td align=right>".number_format($total,2,'.',',')."</td>
				<td align=right>".number_format($gjkotor,2,'.',',')."</td>
			  </tr>"; 	  			
		}
?>

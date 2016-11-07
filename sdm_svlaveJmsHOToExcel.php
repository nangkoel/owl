<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once('config/connection.php');

$val1=trim($_GET['periode']);	
$val=substr($val1,3,4)."-".substr($val1,0,2);

$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str);
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
	$stream='';		  
	$stream.="<b>Laporan Jamsostek Bulan: ".substr($val,5,2)."-".substr($val,0,4)."</b>"; 	  		     
	$stream.="<table width=900px border=1>
		      <thead>
			  <tr bgcolor='#DFDFDF'>
			    <td align=center><b>No</b>.</td>
				<td align=center><b>No.Karyawan</b></td>
			    <td align=center><b>Nama.Karyawan</b></td>
				<td align=center><b>No.JMS</b></td>
				<td align=center><b>Tgl.Masuk</b></td>
				<td align=center><b>Periode</b></td>
				<td align=center><b>Beban.Karyawan<br>(Rp.)</b></td>
				<td align=center><b>Beban.Perusahaan<br>(Rp.)</b></td>
				<td align=center><b>Total</b></td>
				<td align=center><b>Gj.Kotor</b></td>
			  </tr> 
			  </thead>
			  <tbody id=tbody>";
		$res=mysql_query($str,$conn);
		$no=0;
		$ttl=0;//grand total
		$tvp=0;//total perusahaan
		$tkar=0;//total karyawan
		$total=0;//total per karyawan
		while($bar=mysql_fetch_object($res))
		{			  
		   $valPerusahaan=(($bar->value*-1)/$angka)*100*$perusahaan;
		   $tvp+=$valPerusahaan;
		   $kar+=($bar->value*-1);
		   
		   $total=$valPerusahaan+($bar->value*-1);
	       $stru="select sum(value) as gjk from ".$dbname.".sdm_ho_detailmonthly where (component=1 or component=2)
			       and updatedby='".$_SESSION['standard']['username']."'
			       and periode='".$val."' and karyawanid=".$bar->karyawanid;
			 $resu=mysql_query($stru);
             $gjkotor=0;
             while($baru=mysql_fetch_object($resu))
               {
			    $gjkotor=$baru->gjk;
               }		   
		   $no+=1;
		  $stream.="<tr class=rowcontent>
			    <td class=firsttd>".$no."</td>
			    <td>'".$bar->karyawanid."</td>
				<td>".$bar->name."</td>
				<td>".$bar->nojms."</td>
				<td align=right>".tanggalnormal($bar->startdate)."</td>
				<td align=center>".$bar->periode."</td>
				<td align=right>".number_format(($bar->value*-1),2,'.','')."</td>
				<td align=right>".number_format($valPerusahaan,2,'.','')."</td>
				<td align=right>".number_format($total,2,'.','')."</td>
				<td align=right>".number_format($gjkotor,2,'.','')."</td>
			  </tr>"; 
		  $ttl+=$total;	  			
		}
	$stream.="</tbody>
			  <tfoot></tfoot>
			    <tr class=rowcontent>
			    <td class=firsttd colspan=6 align=center>TOTAL</td>
				<td align=right>".number_format($kar,2,'.','')."</td>
				<td align=right>".number_format($tvp,2,'.','')."</td>				
				<td align=right>".number_format($ttl,2,'.','')."</td>
			  </tr>
		      </table>";  
$nop_="jamsostek".$val1;
if(strlen($stream)>0)
{
if ($handle = opendir('tempExcel')) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            @unlink('tempExcel/'.$file);
        }
    }	
   closedir($handle);
}
 $handle=fopen("tempExcel/".$nop_.".xls",'w');
 if(!fwrite($handle,$stream))
 {
  echo "<script language=javascript1.2>
        parent.window.alert('Can't convert to excel format');
        </script>";
   exit;
 }
 else
 {
  echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls';
        </script>";
 }
closedir($handle);
}			  
?>

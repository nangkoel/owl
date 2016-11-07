<?php //@Copy nangkoelframework
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

//+++++++++++++++++++++++++++++++++++++++++++++
//list employee
$periode=$_GET['periode'];
$tipe=$_GET['tipe'];
$tgltrf=$_GET['tanggaltrf'];
$username=$_GET['username'];
if($username=='')
{
	$username=$_SESSION['standard']['username'];
}
else
{
	$username=$username;
}

$head="<table border=1>
       <thead><tr
	   	<td  bgcolor=#dfdfdf>Acc.No.</td>
		<td align=center bgcolor=#dfdfdf><b>Trans.Amount</b></td>
		<td align=center bgcolor=#dfdfdf><b>Emp.Number</b></td>
		<td align=center bgcolor=#dfdfdf><b>Emp.Name</b></td>
		<td align=center bgcolor=#dfdfdf><b>Dept.</b></td>
		<td align=center bgcolor=#dfdfdf><b>Trans.Date</b></td>
		</tr></thead><tbody>";	

//get All user id from employee table
$str1="select sum(m.value) as val, e.karyawanid,e.name,e.bankaccount from ".$dbname.".sdm_ho_employee e,".$dbname.".sdm_ho_detailmonthly m
       where e.operator='".$username."'
	   and e.karyawanid=m.karyawanid and periode='".$periode."'
	   and `type`='".$tipe."'
       group by m.karyawanid order by e.name";
	   
$res1=mysql_query($str1,$conn);

$no=0;
$grandTotal=0;
while($bar1=mysql_fetch_object($res1))
{
	//ambil departemen
	$strt="select bagian from ".$dbname.".datakaryawan where karyawanid=".$bar1->karyawanid;
    $bagian='';
	$rest=mysql_query($strt);
	while($bart=mysql_fetch_object($rest))
	{
		$bagian=$bart->bagian;
	}
$head.="<tr>
        <td>'".$bar1->bankaccount."</td>
		<td>".$bar1->val."</td>
		<td>'".$bar1->karyawanid."</td>
		<td>".$bar1->name."</td>
		<td>".$bagian."</td>
		<td>$tgltrf</b></td>
		</tr>";
}
$head.="</tbody><tfoot></tfoot></table>";
$stream=$head;

$nop_='payroll_'.$tipe."_".$periode;		  

//write exel   
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
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
//+++++++++++++++++++++++++++++++++++++++++++++
//list employee
$periode=$_POST['periode'];
$tipe=$_POST['tipe'];
$username=$_POST['username'];
if($username=='')
{
	$username=$_SESSION['standard']['username'];
}
else
{
	$username=$username;
}


$str="select id,name from ".$dbname.".sdm_ho_component order by id";
$res=mysql_query($str);
$head="<table cellspacing=1 class=data border=0 width=1400px>
       <thead><tr chass=rowheader>
	   	<td class=firsttd>No.</td>
		<td align=center>No.Karyawan</td>
		<td align=center>Nama.Karyawan</td>
		<td align=center>Periode<br>Gaji</td>
		<td align=center>Bank</td>
		<td align=center>Bank.A/C</td>		
		<td align=center>Total T.H.P<br>(Rp.)</td>";
$arrUrut=Array();		
$arrVal=Array();
while($bar=mysql_fetch_object($res))
{
	array_push($arrUrut,$bar->id);
	$head.="<td align=center>".str_replace(" ","<br>",$bar->name)."</td>";
}  
$head.="</tr></thead><tbody>";	

//get All user id from employee table
$str1="select distinct e.karyawanid,e.name,e.bank,e.bankaccount from ".$dbname.".sdm_ho_employee e,".$dbname.".sdm_ho_detailmonthly m
       where e.operator='".$username."'
	   and e.karyawanid=m.karyawanid and periode='".$periode."'
       order by e.name";	   
$res1=mysql_query($str1);
$no=0;
$grandTotal=0;
while($bar1=mysql_fetch_array($res1))
{
	$no+=1;
	$total=0;	
	//makesure the value always zero on loop each employee
	for($z=0;$z<count($arrUrut);$z++)
	{
			$arrVal[$z]=0;
	} 
   //loop each employe to monthlypayment
	$str2="select component,value from ".$dbname.".sdm_ho_detailmonthly
		   where karyawanid=".$bar1[0]." and periode='".$periode."'
		   and `type`='".$tipe."' order by component";
	$res2=mysql_query($str2);
	while($bar2=mysql_fetch_object($res2))
	{
		//fill arrVal base on component
		for($z=0;$z<count($arrUrut);$z++)
		{
			if($arrUrut[$z]==$bar2->component)
			{
				$arrVal[$z]=$bar2->value;
			}
		}
	 $total+=$bar2->value;	
	}
//assign to string
$head.="<tr class=rowcontent>
        <td class=firsttd rowspan=2>".$no."</td>
		<td rowspan=2>".$bar1[0]."</td>
		<td rowspan=2>".$bar1[1]."</td>
		<td rowspan=2 align=center>".substr($periode,5,2)."-".substr($periode,0,4)."</td>
		<td rowspan=2>".$bar1[2]."</td>
		<td rowspan=2>".$bar1[3]."</td>
		<td align=right class=firsttd ><b>".number_format($total,2,'.',',')."</b></td>";
 for($c=0;$c<count($arrVal);$c++)
 {
 	$head.="<td align=right>".number_format($arrVal[$c],2,'.',',')."</td>";
 }		
$head.="</tr>";
//add terbilang below value row
$terbilang='-';
$str3="select terbilang from ".$dbname.".sdm_ho_payrollterbilang
        where userid=".$bar1[0]." and `type`='".$tipe."'
		and periode='".$periode."' limit 1";	
$res3=mysql_query($str3);
while($bar3=mysql_fetch_object($res3))
{
	$terbilang=$bar3->terbilang;
}		
$head.="<tr><td bgcolor=#ffffff colspan='".(count($arrVal)+1)."'>".$terbilang."</td></tr>"; 
$grandTotal+=$total; 		   
}	     
$head.="</tbody><tfoot>
        <tr><td colspan=6>Grand Total</td>
		<td align=right>".number_format($grandTotal,2,'.',',')."</td>
		<td clspan=".count($arrVal)."></td>
		</tfoot></table>";
print $head;
?>
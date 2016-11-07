<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
$opt3='';
for($z=0;$z<=36;$z++)
{
	$da=mktime(0,0,0,date('m')-$z,date('d'),date('Y'));
	$opt3.="<option value='".date('Y-m',$da)."'>".date('m-Y',$da)."</option>";
}

$str="select * from ".$dbname.".sdm_ho_hr_jms_porsi";
$res=mysql_query($str,$conn);
//default
$karyawan=0.02;
$perusahaan=4.54;
while($bar=mysql_fetch_object($res))
{
	if($bar->id=='karyawan')
	{
		$karyawan=$bar->value/100;
	}
	else
	{
		$perusahaan=$bar->value/100;
	}
}
	OPEN_BOX('','<b>JAMSOSTEK REPORT</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('JAMSOSTEK:');
        echo"<br>
		      Periode:<select id=bln onchange=getJmsvalue(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>
			 ";

		$str="select e.name,e.startdate,e.nojms,d.value,d.karyawanid,d.periode from 
		     ".$dbname.".sdm_ho_employee e, ".$dbname.".sdm_ho_detailmonthly d
		      where e.karyawanid=d.karyawanid and e.operator='".$_SESSION['standard']['username']."'
			  and d.periode='".date('Y-m')."' and d.component=3 
		      order by name";
		echo"<hr><br>Laporan Jamsostek Bulan:<b><span id=caption>".date('m-Y')."</span></b>
			  <img src=images/excel.jpg height=17px style='cursor:pointer;' onclick=convertJmsExcel()>
			  <div style='display:none;'>
			  <iframe id=ifrm></iframe>
			  </div>
			  "; 	  		     
		echo"<table class=sortable width=900px border=0 cellspacing=1>
		      <thead>
			  <tr class=rowheader>
			    <td align=center>No.</td>
				<td align=center>No.Karyawan</td>
			    <td align=center>Nama.Karyawan</td>
				<td align=center>No.JMS</td>
				<td align=center>Tgl.Masuk</td>
				<td align=center>Periode</td>
				<td align=center>Beban.Karyawan<br>(Rp.)</td>
				<td align=center>Beban.Perusahaan<br>(Rp.)</td>
				<td align=center>Total</td>
				<td align=center>GJ.Kotor</td>
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
		   $valPerusahaan=(($bar->value*-1)/2)*100*$perusahaan;
		   $tvp+=$valPerusahaan;
		   $kar+=($bar->value*-1);
		   
		   $total=$valPerusahaan+($bar->value*-1);
		    $stru="select sum(value) as gjk from ".$dbname.".sdm_ho_detailmonthly where (component=1 or component=2)
			       and updatedby='".$_SESSION['standard']['username']."'
			       and periode='".date('Y-m')."' and userid=".$bar->userid;
			 $resu=mysql_query($stru,$conn);
             $gjkotor=0;
             while($baru=mysql_fetch_object($resu))
               {
			    $gjkotor=$baru->gjk;
               }			   
		   $no+=1;
		   echo"<tr class=rowcontent>
			    <td class=firsttd>".$no."</td>
			    <td>".$bar->userid."</td>
				<td>".$bar->name."</td>
				<td>".$bar->nojms."</td>
				<td align=right>".tanggalnormal($bar->startdate)."</td>
				<td align=center>".$bar->periode."</td>
				<td align=right>".number_format(($bar->value*-1),2,'.',',')."</td>
				<td align=right>".number_format($valPerusahaan,2,'.',',')."</td>
				<td align=right>".number_format($total,2,'.',',')."</td>
				<td align=right>".number_format($gjkotor,2,'.',',')."</td>
			  </tr>"; 
		  $ttl+=$total;	  			
		}
		echo"</tbody>
			  <tfoot></tfoot>
			    <tr class=rowcontent>
		      </table>";  	  			 
		echo"</div>";
		echo CLOSE_THEME();		
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
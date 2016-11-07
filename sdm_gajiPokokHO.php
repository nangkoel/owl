<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
	OPEN_BOX('','<b>BASIC SALARY SETUP:</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('Basic Salary Form:');
//cek if new employee exist
		 $prestr="select distinct karyawanid from ".$dbname.".sdm_ho_employee order by karyawanid";
		
		 $preres=mysql_query($prestr,$conn);	
		 $arrid='';
		 while($prebar=mysql_fetch_object($preres))
		 {
		 	if($arrid=='')
			 $arrid.=$prebar->karyawanid;
			else 
			 $arrid.=",".$prebar->karyawanid;			
		 }
         if($arrid=='')
		 {
		 	$arrid="'null'";
		 }

		 $str="select karyawanid,namakaryawan,statuspajak,tanggalkeluar,npwp from ".$dbname.".datakaryawan
		       where karyawanid not in(".$arrid.") and alokasi=1";
	   
		$newempl=mysql_num_rows(mysql_query($str,$conn));	 
		if($newempl>0)
		{
	       	echo "Warning!!!<br>
				      <img src=images/onebit_36.png height=50px align=middle>
					  Ada karyawan baru yang belum terdaftar di payroll, lakukan <b>Sinkronisasi</b> data jika perlu.";		
		}
//		else
//		{
			//ccheck if all employee has been assign to payroll operator
			$str="select count(*) as d from ".$dbname.".sdm_ho_employee
			      where operator is null or operator=''";
			$res=mysql_query($str,$conn);
			$count=0;
			while($bar=mysql_fetch_object($res))
			{
				$count=$bar->d;
			}
	        if($count>0){
	        	echo "Forbidden!!!<br>
				      <img src=images/stop1.png height=100px align=middle>
					  Masih ada karyawan yang belum di <b>set operator</b> payroll-nya.";
	        }
			else
			{
	     //ccheck if all employee bankaccount has been set
/*
				$str="select count(*) as d from ".$dbname1.".employee
				      where bank is null or bankaccount='' or length(firstpayment)<>7";
				$res=mysql_query($str,$conn1);
				$count=0;
				while($bar=mysql_fetch_object($res))
				{
					$count=$bar->d;
				}
		        if($count>0){
		        	echo "Forbidden!!!<br>
					      <img src=images/stop1.png height=100px align=middle>
						  Account bank karyawan atau periode gaji pertama belum si set, lakukan <b>Setup Employee's Payroll Data</b>.";
		        }
				else
				{
*/
					echo"<table><thead></thead>
					     <tbody><tr><td>";
					$stra="select id,name from ".$dbname.".sdm_ho_component where type='basic'
					       order by name";
					$resa=mysql_query($stra);
					$arrName=Array();
					$arrIdx=Array();
					while($bara=mysql_fetch_object($resa))	   
					{
					  array_push($arrIdx,$bara->id);
					  array_push($arrName,$bara->name);	
					}
					
					$str="select karyawanid,name from ".$dbname.".sdm_ho_employee 
					      where operator='".$_SESSION['standard']['username']."'
						  order by name";  
						  
					$res=mysql_query($str,$conn);
					echo"<table class=data celspacong=1 border=0>
					     <thead>
					     <tr class=rowheader align=center>
						     <td><b>No.</b></td>
							 <td><b>No.Karyawan</b></td>
						     <td><b>Nama.Karyawan</b></td>
							 <td><b>Basic Salary</b></td></tr>
						</thead>
						<tbody>";
						$n=0;
					while($bar=mysql_fetch_object($res))
					{
						$n+=1;
						echo"<tr class=rowcontent>
						<td class=firsttd>".$n."</td>
						<td>".$bar->karyawanid."</td>
						<td>".$bar->name."</td>
						<td>";
						 echo"<table class=data celspacong=1 border=0>
						      <thead>
							  <tr class=rowheader align=center>
							  <td>Component</td><td>Value(Rp.)</td>
							  <td>**</td>
							  </tr>
							  </thead>";
						for($x=0;$x<count($arrName);$x++)
						{
							$strf="select `value` from ".$dbname.".sdm_ho_basicsalary 
							      where karyawanid=".$bar->karyawanid."
								  and component=".$arrIdx[$x];
							$val=0;	  
							$resf=mysql_query($strf,$conn);
							while($barf=mysql_fetch_array($resf))
							{
								$val=$barf[0];
							}
							echo"<tr class=rowcontent>
							     <td>".$arrName[$x]."</td>
								 <td>
								 	<input type=text class=myinputtextnumber value=".number_format($val,2,'.',',')." id=value".$n.$x." size=13 onkeypress=\"return angka_doang(event);\" onblur=\"change_number(this);\" maxlength=14>
								 </td>
								 <td><img src=images/save.png height=13px class=dellicon title='Save' onclick=saveBSalary('".$bar->karyawanid."','".$arrIdx[$x],"','value".$n.$x."')>
								 </td>
								 </tr>
								"; 
						}	  
						echo"<tfoot></tfoot></table>";	  
						echo"</td></tr>";
					}    
					
					echo"</tbody><tfoot></tfoot>
					</table>";
				echo"</td><td valign=top>";
				echo"<fieldset style='width:300px'>
 				 <legend>
				 <img src=images/info.png align=left height=35px valign=asmiddle>
				 </legend><p>
				 Setiap karyawan harus di set basic salary-nya. Basic salary ini akan otomatis menjadi default
				 pada saat pembuatan rekap gaji bulanan. Untuk menambahkan komponen lain sebagai basic salary, gunakan menu
				 <b>Payroll Component</b>, dan jadikan tipe komponen-nya menjadi <b>Penambah</b>.
				 </p><p>Basic Salary di-Update hanya pada saat adanya perubahan gaji(kenaikan/penurunan).
				     </p>
					 </fieldset>";
				echo"</td></tr></tbody><tfoot></tfoot></table>";	
//				}				
				
			}
//       }
		echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
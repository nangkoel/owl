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
  $periode=$_SESSION['bonusperiode'];
  $periodegaji=$_SESSION['periodegaji'];
  $tglbns=str_replace("-","",$periode)."01";

	OPEN_BOX('','<b>JASA PRODUKSI ENTRY:</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('Create Bonus: PERIOD <font color=red>'.substr($periode,5,2)."/".substr($periode,0,4))."</font>";
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
	       	echo "<font size=4 color=orange><b>Warning!!!</b></font><br>
				      <img src=images/onebit_36.png height=30px align=middle>
					  Ada karyawan baru yang belum terdaftar di payroll.<br>
					  ";		
		}

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
				$str="select count(*) as d from ".$dbname.".sdm_ho_employee
				      where bank is null or bankaccount='' or length(firstpayment)<>7";
				$res=mysql_query($str,$conn);
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

					echo"<table class=sortable cellspacing=1 border=0>
					     <thead>
					     <tr class=rowheader align=center>
						     <td><b>No.</b></td>
							 <td><b>No.Karyawan</b></td>
						     <td><b>Nama.Karyawan</b></td>
							 <td><b>TMK</b></td>
							 <td><b>Periode<br>Bonus</b></td>
							 <td><b>MasaKerja<br>(Thn)</b></td>
							 <td><b>Jasa.Produksi</b></td>
							 <td>Terbilang</td>
						  </tr>
						</thead>
						<tbody>";
					$str="select karyawanid,name,firstpayment,lastpayment,startdate,
					      ROUND(DATEDIFF(".$tglbns.",startdate)/365,2) as masakerja
					      from ".$dbname.".sdm_ho_employee 
					      where operator='".$_SESSION['standard']['username']."'
						  and (firstpayment<='".$periode."' or firstpayment='')
						  and (lastpayment>='".$periode."' or lastpayment='')
						  order by masakerja";  	  
					$res=mysql_query($str);					
					$no=0;
					while($bar=mysql_fetch_object($res))
					{
						$no+=1;
						$tmk=tanggalnormal($bar->startdate);
						
/*
						if($bar->masakerja==0)
						  $porsi=0;
						else if($bar->masakerja<12)
						  $porsi=$bar->masakerja/12;
						else
						  $porsi=1;
*/
					   //get salary
					    $str1="select sum(value) as gaji from ".$dbname.".sdm_ho_detailmonthly
						       where karyawanid=".$bar->karyawanid." and periode='".$periode."'
							   and type='regular' and component in(select component from ".$dbname.".sdm_ho_bonus_setup)";
						$gaji=0;
						$res1=mysql_query($str1);

						while($bar1=mysql_fetch_object($res1))
						{
							$gaji=$bar1->gaji;
						}	  
						$color='white';
						//$thr=$gaji*$porsi; 
						$bns=$gaji; 
						//get old thr
						$str2="select value from ".$dbname.".sdm_ho_detailmonthly where periode='".$periode."' and karyawanid=".$bar->karyawanid." and type='jaspro'";
						$res2=mysql_query($str2);
						while($bar2=mysql_fetch_object($res2))
						{
							//$thr=$bar2->value;
							$bns=$bar2->value;
							$color='gray';
						}	    
					   echo"<tr class=rowcontent>
						     <td>".$no."</td>
							 <td id=userid".$no.">".$bar->karyawanid."</td>
						     <td>".$bar->name."</td>
							 <td>".tanggalnormal($bar->startdate)."</td>
							 <td>".$periode."</td>
							 <td align=right>".$bar->masakerja."</td>
							 <td><input style='background-color:".$color.";' type=text id=bns".$no." value=".number_format($bns,2,'.',',')." class=myinputtextnumber onkeypress=\"return angka_doang(event);\"  onblur=\"change_number(this);loadTerbilang(this,'".$no."',this.value);\"></td>
						     <td id=terbilang".$no."></td>
						  </tr>";						
						
					}	  	
					
					echo"</tbody><tfoot></tfoot>
					</table>
					<center><button onclick=saveBonus('".$no."')>Save</button></center>
					";
				}				
				
			}

		echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
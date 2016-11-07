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
//list employee
	OPEN_BOX('','<b>PAYROLL ENTRY:</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('Create monthly payroll: PERIOD <font color=red>'.substr($_SESSION['pyperiode'],5,2)."/".substr($_SESSION['pyperiode'],0,4))."</font>";
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
					  Ada karyawan baru yang belum terdaftar di payroll, lakukan <b>Sinkronisasi</b> data terlebih dahulu, jika memang harus.<br>
					  .";		
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
					$stra="select id,name,type,plus,`lock` from ".$dbname.".sdm_ho_component
					       order by type desc ,name";
					$resa=mysql_query($stra);
					$arrName=Array();
					$arrIdx=Array();
					$arrType=Array();
					$arrPlus=Array();
					$arrLock=Array();
					while($bara=mysql_fetch_object($resa))	   
					{
					  array_push($arrIdx,$bara->id);
					  array_push($arrName,$bara->name);	
					  array_push($arrType,$bara->type);
					  array_push($arrPlus,$bara->plus);
					  array_push($arrLock,$bara->lock);
					}
				//get jms porsi
				$strd="select value from ".$dbname.".sdm_ho_hr_jms_porsi where id='karyawan'";
				$resd=mysql_query($strd,$conn);
				$jms=0.02;//default = 2%
				while($bard=mysql_fetch_array($resd))
				{
					$jms=$bard[0]/100;
				}	
					
					$str="select karyawanid,name,jmsstart,firstpayment,lastpayment,firstvol,lastvol
					      from ".$dbname.".sdm_ho_employee 
					      where operator='".$_SESSION['standard']['username']."'
						  and (firstpayment<='".$_SESSION['pyperiode']."' or firstpayment='')
						  and (lastpayment>='".$_SESSION['pyperiode']."' or lastpayment='')
						  order by name";  
					$res=mysql_query($str,$conn);
					echo"<table class=data cellspacing=1 border=0 width=700px>
					     <thead>
					     <tr class=rowheader align=center>
						     <td><b>No.</b></td>
							 <td><b>No.Karyawan</b></td>
						     <td><b>Nama.Karyawan</b></td>
							 <td width=350px><b>Basic Salary</b></td></tr>
						</thead>
						<tbody>";
						$n=0;
					while($bar=mysql_fetch_object($res))
					{
						$n+=1;
						echo"<tr class=rowcontent id='".$bar->karyawanid."'>
						<td class=firsttd>".$n."</td>
						<td>".$bar->karyawanid."</td>
						<td>".$bar->name."</td>
						<td align=center>";
						 echo"<table class=data celspacong=1 border=0 width=290px>
						      <thead>
							  <tr class=rowheader align=center>
							  <td>Component</td><td>Value(Rp.)</td>
							  </tr>
							  </thead>";
						$gp=0;	
						$ttl=0;  
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
								
								//check wether first payment
								if($_SESSION['pyperiode']==$bar->firstpayment)
								{
									$val=$bar->firstvol/100*$val;
								}
								//check wether last payment
								if($_SESSION['pyperiode']==$bar->lastpayment)
								{
									$val=$bar->lastvol/100*$val;
								}								
							}
							
							if($arrIdx[$x]==1)
							{
								$gp=$val;
							}
							//set potongan jms
							if($arrIdx[$x]==3 or strpos($arrName[$x],'jamso')>-1 or strpos($arrName[$x],'jms')>-1)
							{
/* setup potongan koperasi, menghindari jms masuk ke jamsostek
								$strd="select sum(value) as ttl from  ".$dbname1.".basicsalary where 
								      userid=".$bar->userid;
*/
  							//get potongan jms berdasarkan GP+Tunjangan
								$strd="select sum(value) as ttl from  ".$dbname.".sdm_ho_basicsalary where 
								      karyawanid=".$bar->karyawanid ."
									  and component in(select id from ".$dbname.".sdm_ho_component where plus=1)";
								$resd=mysql_query($strd,$conn);
								$thp=0;
								while($bard=mysql_fetch_object($resd))
								{
									$thp=$bard->ttl;
								}	  
								//check wether jms periode is match
								if($_SESSION['pyperiode']>=$bar->jmsstart && $bar->jmsstart!='')
								  {
								  	$val=$thp*$jms;
								  }
								else
								{
									$val=0;
								}  
							}
							
							if(strpos($arrName[$x],'ngsur')>-1)
							{
								//get potongan angsuran
								$stre="select bulanan from ".$dbname.".sdm_angsuran
								       where karyawanid=".$bar->karyawanid." and jenis=".$arrIdx[$x]."
									   and active=1 and `start`<='".$_SESSION['pyperiode']."'
									   and `end`>='".$_SESSION['pyperiode']."'";   
								$rese=mysql_query($stre,$conn);
								$pot=0;
								while($bere=mysql_fetch_array($rese))
								{
									$pot=$bere[0];
								}	  
								$val+=$pot; 
						     }
							echo"<tr class=rowcontent>
							     <td>".$arrName[$x]."</td>
								 <td align=right> ".($arrPlus[$x]==1?"+":"-");
							//if($arrLock[$x]==1 and $arrName[$x]!='Potongan Jamsostek')//hilangkan dari AND ke belakang untuk mengunci field jamsostek	 
							if($arrLock[$x]==1)
								 echo"<input type=text class=myinputtextnumber value='".number_format($val,2,'.',',')."' id='value".$bar->karyawanid.$x."' size=13  disabled>";
							else
							     echo"<input type=text class=myinputtextnumber value='".number_format($val,2,'.',',')."' id='value".$bar->karyawanid.$x."' size=13 onkeypress=\"return angka_doang(event);\" onblur=\"calculatePayroll(this,".(count($arrName)-1).",'".$bar->karyawanid."');\" maxlength=14>";
							echo"<input type=hidden id='component".$bar->karyawanid.$x."' value='".$arrIdx[$x]."'>";
							echo"<input type=hidden id='plus".$bar->karyawanid.$x."' value='".$arrPlus[$x]."'>"; 
							echo "</td>
								 </tr>
								"; 
							if($arrPlus[$x]==1)
							    $ttl=$ttl+$val;
							else
							    $ttl=$ttl-$val;		
						}	  
						echo" <tr class=rowcontent><td>Total(Rp.)</td><td align=right><input type=text class=myinputtextnumber id='total".$bar->karyawanid."' value='".number_format($ttl,2,'.',',')."' size=13  disabled></td></tr>
						      </tbody><tfoot></tfoot></table>
							  <div id='terbilang".$bar->karyawanid."' style='width:300px;background-color:#ffffff;'></div>
						     <button class=mybutton id=btn".$bar->karyawanid."  onclick=saveMonthlySalary('".$bar->karyawanid."',".(count($arrName)-1).")>Save</button>
							 ";	  
						echo"</td></tr>";
					}    
					
					echo"</tbody><tfoot></tfoot>
					</table>";
				}				
				
			}

		echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
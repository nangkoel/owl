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
$opt="<option value='01'>01</option>";
$opt.="<option value='02'>02</option>";
$opt.="<option value='03'>03</option>";
$opt.="<option value='04'>04</option>";
$opt.="<option value='05'>05</option>";
$opt.="<option value='06'>06</option>";
$opt.="<option value='07'>07</option>";
$opt.="<option value='08'>08</option>";
$opt.="<option value='09'>09</option>";
$opt.="<option value='10'>10</option>";	
$opt.="<option value='11'>11</option>";	
$opt.="<option value='12'>12</option>";	

for($x=-1;$x<=50;$x++)
{
	$opt1.="<option value='".(date('Y')-$x)."'>".(date('Y')-$x)."</option>";	
}
	OPEN_BOX('','<b>'.$_SESSION['lang']['akunbank'].'</b>');
		echo"<center><div>";
         echo OPEN_THEME('<font color=white>'.$_SESSION['lang']['akunbanknote'].':</font>');
		 echo"<fieldset>
		         <legend>
				 <img src=images/info.png align=left height=35px valign=asmiddle>
				 </legend>
				".$_SESSION['lang']['akunbankinfo']."				 
		      </fieldset>";
			  
		 $prestr="select * from ".$dbname.".sdm_ho_employee order by karyawanid";
		 $preres=mysql_query($prestr,$conn);	
		echo"<table class=sortable cellspacing=1 border=0>
		     <thead>
			   <tr class=rowheader>
			    <td>".$_SESSION['lang']['pilih']."</td>
			    <td>No.</td>
			    <td>".$_SESSION['lang']['nokaryawan']."</td>
				<td>".$_SESSION['lang']['namakaryawan']."</td>
				<td>".$_SESSION['lang']['namabank']."</td>
				<td>".$_SESSION['lang']['norekeningbank']."</td>
				<td>".$_SESSION['lang']['jms']."</td>
				<td>JMS.Start</td>
				<td>#1st.Pymnt<br>Period</td>
				<td>#1st.Vol<br>(%)</td>
				<td>Last.Pymnt<br>Period</td>
				<td>Last.Vol<br>(%)</td>
				<td>".$_SESSION['lang']['save']."</td>
				</tr>
			 </thead>
			 <tbody id=tablebody>
			 ";
			 $no=0;
			 
		 while($bar=mysql_fetch_object($preres))
		 {
		 	$no+=1;
			if($bar->bank=='' or $bar->bankaccount==''){
			 $stat='';
			 $ch ='checked';
			}
			else{
			 $stat='disabled'; 
			 $ch='';
			}
			echo"<tr class=rowcontent id=row".$no.">
			     <td><input type=checkbox id=check".$no." ".$ch." onclick=vLine(this,'".$no."')></td>
			     <td class=firsttd>".$no."</td>
				 <td id=userid".$no.">".$bar->karyawanid."</td>
				 <td id=nama".$no.">".$bar->name."</td>
				 <td><input type=text class=myinputtext id=bank".$no." value='".$bar->bank."' ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>
				 <td><input type=text class=myinputtext id=bankac".$no." value='".$bar->bankaccount."' size=8 ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>
				 <td><input type=text class=myinputtext id=jms".$no." value='".$bar->nojms."' size=6 ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>		
				 <td>
				 	<select id='jmsstartbl".$no."'  ".$stat."><option value='".substr($bar->jmsstart,5,2)."'>".substr($bar->jmsstart,5,2)."</option>
					".$opt."
					</select>
				 	<select id='jmsstartth".$no."'   ".$stat."><option value='".substr($bar->jmsstart,0,4)."'>".substr($bar->jmsstart,0,4)."</option>
					".$opt1."
					</select>					
				 </td>
				 <td>
				 	<select id='firstbl".$no."'  ".$stat."> <option value='".substr($bar->firstpayment,5,2)."'>".substr($bar->firstpayment,5,2)."</option>
					".$opt."
					</select>
				 	<select id='firstth".$no."'   ".$stat."> <option value='".substr($bar->firstpayment,0,4)."'>".substr($bar->firstpayment,0,4)."</option>
					".$opt1."
					</select>					
				 </td>
				 <td><input type=text class=myinputtext id=firstvol".$no." value='".$bar->firstvol."' size=4 ".$stat." onkeypress=\"return angka_doang(event);\" maxlength=5></td>
				 <td>
				 	<select id='lastbl".$no."'   ".$stat."> <option value='".substr($bar->lastpayment,5,2)."'>".substr($bar->lastpayment,5,2)."</option>
					".$opt."
					</select>
				 	<select id='lastth".$no."'   ".$stat."> <option value='".substr($bar->lastpayment,0,4)."'>".substr($bar->lastpayment,0,4)."</option>
					".$opt1."
					</select>					 
				 </td>
				 <td><input type=text class=myinputtext id='lastvol".$no."' value='".$bar->lastvol."' size=4 ".$stat."  onkeypress=\"return angka_doang(event);\" maxlength=5></td>				 
				 <td><button class=mybutton id=butt".$no." style='padding:0px;' title='Save this line' ".$stat." onclick=saOneLine('".$no."')><img src='images/save.png' height=12px></button></td>
				 </tr>";
		 }	 
	    echo"</tbody>
		     <tfoot>
			 </tfoot>
			 </table>
			 <center><button class=mybutton onclick=saveAll('".$no."')>Save All Checked</button></center>
			 ";  
		 echo CLOSE_THEME('');
		echo"</div></center>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
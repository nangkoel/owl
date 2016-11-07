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
//list employee

$limit=0;
if(isset($_GET['limit']))
   $limit=$_GET['limit'];
   
	OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['sinkronisasidatapy']).'</b>');
		echo"<div>";
         echo OPEN_THEME($_SESSION['lang']['pilihdata'].':');
		echo"<table><tr><td>";
//get all registred employee
		 $arrid=Array();
		 $prestr="select distinct karyawanid from ".$dbname.".sdm_ho_employee order by karyawanid";
		 $preres=mysql_query($prestr,$conn);	
		 while($prebar=mysql_fetch_object($preres))
		 {
		 	array_push($arrid,$prebar->karyawanid);			
		 }
		 $str="select karyawanid,namakaryawan,statuspajak,tanggalmasuk,tanggalkeluar,npwp from ".$dbname.".datakaryawan where alokasi=1  limit ".$limit.",500";  
		 $res=mysql_query($str,$conn);
		echo "<input type=checkbox onclick=checkAll(this,".mysql_num_rows($res).")>".$_SESSION['lang']['pilihsemua']; 
		echo " &nbsp &nbsp &nbsp &nbsp &nbsp 
		        <a href='?limit=".($_GET['limit']-500>-1?$_GET['limit']-500:0)."'>".$_SESSION['lang']['pref']."</a> &nbsp <a href='?limit=".($_GET['limit']+500)."'>".$_SESSION['lang']['lanjut']."</a>";
		
		echo"<table class=sortable cellspacing=1 border=0>
		     <thead>
			   <tr class=rowheader>
			   <td>".$_SESSION['lang']['pilih']."</td>
			    <td>No.</td>
			    <td>".$_SESSION['lang']['id']."</td>
				<td>".$_SESSION['lang']['namakaryawan']."</td>
				<td>".$_SESSION['lang']['statuspajak']."</td>
				<td>".$_SESSION['lang']['npwp']."</td>
				<td>".$_SESSION['lang']['tanggalmasuk']."</td>
				<td>".$_SESSION['lang']['tanggalkeluar']."</td>
				</tr>
			 </thead>
			 <tbody id=tablebody>
			 ";
			 $no=0;
		 while($bar=mysql_fetch_object($res))
		 {
		 	$no+=1;
			echo"<tr class=rowcontent id=row".$no.">";
		//if its a new employee then check the checkbox	
				if (in_array($bar->karyawanid, $arrid)) {
				 echo"<td><input type=checkbox id='chk".$no."'></td>";
				 
				}
				else
				{
				 echo"<td style='background-color:orange'><input type=checkbox id='chk".$no."' checked>".$_SESSION['lang']['new']."</td>";	
				}
			echo"<td class=firsttd>".($no+$limit)."</td>
				 <td id=userid".$no.">".$bar->karyawanid."</td>
				 <td id=nama".$no.">".$bar->namakaryawan."</td>
				 <td id=mstatus".$no.">".$bar->statuspajak."</td>
				 <td id=npwp".$no.">".$bar->npwp."</td>
				 <td id=start".$no.">".tanggalnormal($bar->tanggalmasuk)."</td>
				 <td id=resign".$no.">".tanggalnormal($bar->tanggalkeluar)."</td>
				 </tr>";
		 }	 
	    echo"</tbody>
		     <tfoot>
			 </tfoot>
			 </table>";  
		echo"</td>
		     <td valign=top>
			     <fieldset style='text-align:center'>
				   <legend id=legend><b>".$_SESSION['lang']['panelsinkronisasi']."</b></legend>
				   ".$_SESSION['lang']['sinkronisasiinfo']."<br>
				   <button id=synbutton onclick=sync(".$no.")>Synchronize</button>
				   <button id=stpbutton onclick=stopSync(".($no+1).") disabled>".$_SESSION['lang']['stop']."</button>
				 </fieldset>
			 </td>
			 </tr>
			 </table>
			 ";	 
	 
		 echo CLOSE_THEME('');
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>

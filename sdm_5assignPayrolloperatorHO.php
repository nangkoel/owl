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
   $str="select uname from ".$dbname.".sdm_ho_payroll_user order by uname";
   $res=mysql_query($str,$conn);
   while($bar=mysql_fetch_object($res))
   {
   	$opt.="<option value='".$bar->uname."'>".$bar->uname."</option>";
   }	
	OPEN_BOX('','<b>'.$_SESSION['lang']['assignpyoperator'].'</b>');
		echo"<div id=EList>";
		echo OPEN_THEME($_SESSION['lang']['pilihoperator'].':');
		$str="select karyawanid,name,operator from ".$dbname.".sdm_ho_employee order by karyawanid";
		$res=mysql_query($str,$conn);
		$no=0;
		echo"<table><tr><td>";
		echo"<table class=sortable cellspacing=1 width=500px border=0>
		     <thead>
			   <tr class=rowheader><td>No.</td><td>".$_SESSION['lang']['nokaryawan']."</td>
			   <td>".$_SESSION['lang']['namakaryawan']."</td><td>".$_SESSION['lang']['pilihoperator']."</td></tr>
			 </thead>
			 <tbody id=tablebody>
			 ";
		while($bar=mysql_fetch_object($res))
		{
			$no+=1;
			echo "<tr class=rowcontent><td class=fisttd>".$no."</td>
			      <td id='user".$no."'>".$bar->karyawanid."</td>
			      <td>".$bar->name."</td>
				  <td><select id=operator".$no." onchange=saveOperator('".$no."')>
				  <option value='".$bar->operator."'>".$bar->operator."</option>
				  ".$opt."
				  </td>			  
				  </tr>";
		}
		echo"</tbody>
		     <tfoot>
			 </tfoot>
			 </table>";
		echo"</td>
		     <td valign=top> 
		       <fieldset>
		         <legend>
				 <img src=images/info.png align=left height=35px valign=asmiddle>
				 </legend>
				 ".$_SESSION['lang']['operatorinfo']."
		      </fieldset>	
		     </td></tr>
			 </table>";	 
		echo CLOSE_THEME();
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>

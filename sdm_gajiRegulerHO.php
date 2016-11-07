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
	    echo"<div id=period>";
 			 $optc="<option value='".date('Y-m')."'>".date('m-Y')."</option>";
			  for($v=-2;$v<3;$v++)
			  {
			  	 $per=mktime(0,0,0,date('m')-$v,15,date('Y'));
			  	$optc.="<option value=".date('Y-m',$per).">".date('m-Y',$per)."</option>";
			  }
			echo"<fieldset style='width:300px'>
 				 <legend><b>Periode Penggajian:</b>
				 </legend>
				 Pilih Periode Penggajian:<select id=periode>".$optc."</select>
				 <button class=mybutton onclick=setPayrollPeriod()>OK</button>
				 </fieldset>";			
		echo"</div>";
	CLOSE_BOX();	

//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
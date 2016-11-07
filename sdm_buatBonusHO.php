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
	OPEN_BOX('','<b>Jasa Produksi:</b>');
	    echo"<div id=period>";
 			 $optc="<option value='".date('Y-m')."'>".date('m-Y')."</option>";
			  for($v=-2;$v<16;$v++)
			  {
			  	 $per=mktime(0,0,0,date('m')-$v,15,date('Y'));
			  	$optc.="<option value=".date('Y-m',$per).">".date('m-Y',$per)."</option>";
			  }
			echo"<fieldset style='width:500px'>
 				 <legend><b>Periode Bonus:</b>
				 </legend>
				 Periode pembayaran Bonus:<select id=periode>".$optc."</select><br>
				 Dengan base gaji periode &nbsp : <select id=periodegaji>".$optc."</select><br>	
				 <button class=mybutton onclick=setBonusPeriod()>OK</button><br>
				 </fieldset>";			
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
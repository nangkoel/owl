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
	OPEN_BOX('','<b>THR:</b>');
	    echo"<div id=period>";
 			 $optc="<option value='".date('Y-m')."'>".date('m-Y')."</option>";
			  for($v=-2;$v<3;$v++)
			  {
			  	 $per=mktime(0,0,0,date('m')-$v,15,date('Y'));
			  	$optc.="<option value=".date('Y-m',$per).">".date('m-Y',$per)."</option>";
			  }
			echo"<fieldset style='width:500px'>
 				 <legend><b>Periode THR:</b>
				 </legend>
				 Pilih Periode pembayaran THR:<select id=periode>".$optc."</select>
				 Tanggal Hari Raya:<input type text id=tglthr onmousemove=setCalendar(this.id) class=myinputtext size=10 onkeypress=\"return false;\" value=".date('d-m-Y').">
				 <button class=mybutton onclick=setTHRPeriod()>OK</button><br>
				 Note:<i>Tanggal THR berfungsi untuk menghitung masa kerja pada perhitungan proporsional gaji ke THR.</i>
				 </fieldset>";			
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>
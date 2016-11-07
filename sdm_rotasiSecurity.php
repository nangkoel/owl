<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/datakaryawan.js></script>
<script language=javascript src='js/sdm_rotasiSecurity.js'></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
OPEN_BOX('',$_SESSION['lang']['rotasisecurity']);
echo "<fieldset><legend>".$_SESSION['lang']['rotasisecurity']."</legend>
        <fieldset>Apply only at the beginning of month</fieldset>
      ".$_SESSION['lang']['caripadanama'].":<input type=text id=txtnama class=myinputtext onclick=\"return tanpa_kutip(event);\" size=25>
	  
	  ".$_SESSION['lang']['nik'].":<input type=text id=nik class=myinputtext onclick=\"return tanpa_kutip(event);\" size=10>
	  <button class=mybutton onclick=cariNama()>".$_SESSION['lang']['find']."</button>
	  <br>
	  <fieldset style='width:500px'>
	  <div id=container style='width:480px; height:400px; overflow:scroll'>
	  </div>
	  </fieldset>
	  </fieldset>";
CLOSE_BOX();
echo close_body();
?>
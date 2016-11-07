<?php //@Copy nangkoelframework
//-----------------ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
echo open_body();


?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript src='js/sdm_prosesAbs.js'></script>



<?php

    $optOrg="<option value='L0ROTR'>L0ROTR</option>";
    $optOrg.="<option value='P0ROTR'>P0ROTR</option>";
    $tk="<option value='3'>KBL</option>";
    $tk.="<option value='4'>KHT</option>";
			
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##unit##tk";	

echo "<fieldset style='float:left;'><legend><b>HK Bulanan</b></legend>
<table>";

echo "  <tr>
            <td>Unit</td>
            <td>:</td>
            <td><select id=unit style='width:155px;'>".$optOrg."</select></td>
	</tr>
        <tr>
            <td>TK</td>
            <td>:</td>
            <td><select id=tk style='width:155px;'>".$tk."</select></td>
	</tr>";
	
	

	
echo "	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('sdm_slave_prosesAbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                <button onclick=zExcel(event,'sdm_slave_prosesAbs.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>

		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";



echo "
<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['list']."</b></legend>
<div id='printContainer'>
</div></fieldset>";// style='overflow:auto;height:350px;max-width:1220px'; 

CLOSE_BOX();
echo close_body();




?>
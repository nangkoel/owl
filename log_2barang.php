<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>Material Group List & Material List</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<?php

$optB="<option value=''>".$_SESSION['lang']['all']."</option>";
$i="select * from ".$dbname.".log_5klbarang order by kelompok ";
$n=mysql_query($i) or die (mysql_error($conn));
while($d=mysql_fetch_assoc($n))
{
	$optB.="<option value='".$d['kode']."'>".$d['kelompok']."</option>";
}

$arr2="##kel";	
echo"<fieldset style='float:left;'>
		<legend>Kelompok Barang</legend>
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td colspan=4>
					<button onclick=zPreview('log_slave_2klbarang','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
					<button onclick=zExcel(event,'log_slave_2klbarang.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
					</td>
				</tr>
			</table>
	  </fieldset>";


echo"<fieldset style='float:left;'>
		<legend>Kelompok Barang</legend>
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['kelompokbarang']."</td>
					<td>:</td>
					<td><select id=kel style=\"width:150px;\">".$optB."</select></td>
				</tr>
				<tr>
					<td colspan=4>
					<button onclick=zPreview('log_slave_2barang','".$arr2."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
					<button onclick=zExcel(event,'log_slave_2barang.php','".$arr2."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
					</td>
				</tr>
			</table>
		</fieldset>";

CLOSE_BOX();
?>


<?php
OPEN_BOX();

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:400px;max-width:1220px'; >
</div></fieldset>";//<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
//<div id='printContainer'>

CLOSE_BOX();
echo close_body();					
?>
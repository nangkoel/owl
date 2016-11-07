<?php //@Copy nangkoelframework
//-----------------ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();


?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript src='js/kebun_premiMuatTbs.js'></script>


<?php
$sql="SELECT periode FROM ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optPer.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
			
					
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kodeorg##per";	

echo "<fieldset style='float:left;'><legend><b>Premi Muat TBS</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><input type=text  id=kodeorg onkeypress=\"return char_only(event);\" disabled value='".$_SESSION['empl']['lokasitugas']."' class=myinputtext style=\"width:150px;\"></td>
	</tr>
	<tr>
		<td>Periode</td>
		<td>:</td>
		<td><select id=per style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreview onclick=zPreview('kebun_slave_premiMuatTbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		
		
		<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
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
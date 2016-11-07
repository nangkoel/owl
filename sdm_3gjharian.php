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
<script language=javascript src='js/sdm_3gjharian.js'></script>


<?php
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct periode FROM ".$dbname.".sdm_5periodegaji order by periode desc limit 12";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optPer.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}	
			
	//print_r($_SESSION['empl']);		
$optKdOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
	$sql="SELECT * FROM ".$dbname.".organisasi where length(kodeorganisasi)='4'";
else
	$sql="SELECT * FROM ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ";	

if($_SESSION['empl']['bagian']=='IT'){
    $sql="SELECT * FROM ".$dbname.".organisasi where tipe in ('KANWIL','KEBUN','PLASMA','PABRIK') and kodeorganisasi in (SELECT kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
}

$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optKdOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}				
			
					
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##per";
$arrx="##kdorgx##perx";	

echo "<fieldset style='float:left;'><legend><b>List Data KHT</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=per style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreview onclick=zPreview('sdm_slave_3gjharian','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcel onclick=zExcel(event,'sdm_slave_3gjharian.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";


/*echo "<fieldset style='float:left;'><legend><b>List Data SKS</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorgx style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=perx style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreviewx onclick=zPreview('sdm_slave_3gjharianSks','".$arrx."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcelx onclick=zExcel(event,'sdm_slave_3gjharianSks.php','".$arrx."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() id=tBatalx class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";*/


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
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
<script language=javascript src='js/vhc_3premi.js'></script>


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
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optKdOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}				
			
					
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arrDt="##kdorgDt##perDt";


echo "<fieldset style='float:left;'><legend><b>List Dump Truck & Tracktor</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorgDt style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=perDt style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreviewDt onclick=zPreview('vhc_slave_3premi_dt','".$arrDt."','printContainer') class=mybutton name=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcelDt onclick=zExcel(event,'vhc_slave_3premi_dt.php','".$arrDt."') class=mybutton name=preview> ".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() id=tBatalDt class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";


$arrAb="##kdorgAb##perAb";
echo "<fieldset style='float:left;'><legend><b>Alat Berat</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorgAb style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=perAb style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreviewAb onclick=zPreview('vhc_slave_3premi_Ab','".$arrAb."','printContainer') class=mybutton name=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcelAb onclick=zExcel(event,'vhc_slave_3premi_Ab.php','".$arrAb."') class=mybutton name=preview> ".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() id=tBatalAb class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";


$arrCu="##kdorgCu##perCu";
echo "<fieldset style='float:left;'><legend><b>Cuci</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorgCu style='width:155px;'>".$optKdOrg."</select></td>
	</tr>
	
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=perCu style='width:155px;'>".$optPer."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button id=tPreviewCu onclick=zPreview('vhc_slave_3premi_Cu','".$arrCu."','printContainer') class=mybutton name=preview>".$_SESSION['lang']['preview']."</button>
		<button id=tExcelCu onclick=zExcel(event,'vhc_slave_3premi_Cu.php','".$arrCu."') class=mybutton name=preview> ".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() id=tBatalCu class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>
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
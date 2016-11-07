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
<script language=javascript src='js/sdm_premiKehadiran.js'></script>



<?php
$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct periode FROM ".$dbname.".sdm_5periodegaji";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optper.="<option value=".$data['periode'].">".$data['periode']."</option>";
			}			
$sql="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
				$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}			
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##per##unit";	

echo "<fieldset style='float:left;'><legend><b>Premi Kehadiran</b></legend>
<table>
	<tr>
		<td>Periode</td>
		<td>:</td>
		<td><select id=per style='width:155px;'>".$optper."</select></td>
	</tr>";

$it="hidden";
if($_SESSION['empl']['bagian']=='IT'){
$it="";    
}
echo "  <tr>
		<td ".$it.">Unit</td>
		<td ".$it.">:</td>
		<td ".$it."><select id=unit style='width:155px;'>".$optOrg."</select></td>
	</tr>";
	
	

	
echo "	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('sdm_slave_premiKehadiran','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		
		
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
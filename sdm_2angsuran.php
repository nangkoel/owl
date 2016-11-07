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



<?php

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$i="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc";
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
	$i="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."' and kodeunit not like '%HO%') order by namaorganisasi asc";
}
else
{
	$i="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";

}

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$n = mysql_query($i) or die ("SQL ERR : ".mysql_error());
while ($d=mysql_fetch_assoc($n))
{
	$optOrg.="<option value=".$d['kodeorganisasi'].">".$d['namaorganisasi']."</option>";
}	


$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql="SELECT distinct periode FROM ".$dbname.".sdm_5periodegaji order by periode desc limit 12";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optPer.="<option value=".$data['periode'].">".$data['periode']."</option>";
}



$optTipe="
	<option value=lunas>Sudah Lunas</option>
	<option value=blmlunas>Belum Lunas</option>
	<option value=active>Active</option>
	<option value=notactive>Not Active</option>";
					
?>



<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdorg##per##tipe";	

echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['angsuran']."</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['kodeorg']."</td>
		<td>:</td>
		<td><select id=kdorg style='width:200px;'>".$optOrg."</select></td>
	</tr>
	<tr>
		<td>Tampilkan Angsuran Bulan</td>
		<td>:</td>
		<td><select id=per style='width:155px;'>".$optPer."</select></td>
	</tr>
	<tr>
		<td>Tipe</td>
		<td>:</td>
		<td><select id=tipe style='width:155px;'>".$optTipe."</select></td>
	</tr>
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('sdm_slave_2angsuran','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'sdm_slave_2angsuran.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() id=tBatal class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";



echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer'  >
</div></fieldset>";//style='overflow:auto;height:350px;max-width:1500px';


CLOSE_BOX();
echo close_body();




?>
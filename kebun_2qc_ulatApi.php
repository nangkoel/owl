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

<script language=javascript>
	function batal()
	{
		document.getElementById('div').value='';
		document.getElementById('per').value='';	
		document.getElementById('printContainer').innerHTML='';	
	}
</script>

<?php
#divisi (kebun)
$optDiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN' ";
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optDiv.="<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi']."</option>";
}

#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_ulatapiht order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}
	
	
$optUlat="<option value='jlhdarnatrima'>Darna Trima</option>";	
$optUlat.="<option value='jlhsetothosea'>Setothosea Asigna</option>";	
$optUlat.="<option value='jlhsetoranitens'>Setora Nitens</option>";	
$optUlat.="<option value='jlhulatkantong'>Ulat Kantong</option>";	

	
			
?>






<?php
include('master_mainMenu.php');
OPEN_BOX();
$arr="##div##per##ulat";	

echo "<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['laporan']." QC ".$_SESSION['lang']['ulatapi']."</b></legend>
<table>
	<tr>
		<td>".$_SESSION['lang']['divisi']."</td>
		<td>:</td>
		<td><select id=div style='width:200px;'>".$optDiv."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['periode']."</td>
		<td>:</td>
		<td><select id=per style='width:200px;'>".$optPer."</select></td>
	</tr>
	<tr>
		<td>Ulat</td>
		<td>:</td>
		<td><select id=ulat style='width:200px;'>".$optUlat."</select></td>
	</tr>
	
	
	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('kebun_slave_2qc_ulatApi','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'kebun_slave_2qc_ulatApi.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		<button onclick=batal() class=mybutton name=btnBatal>".$_SESSION['lang']['cancel']."</button>
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
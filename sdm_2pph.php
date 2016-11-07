<?php
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>PPH 21</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<?php

$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

if(($_SESSION['empl']['tipelokasitugas']=='HOLDING')or($_SESSION['empl']['tipelokasitugas']=='KANWIL'))
{
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','TRAKSI','KANWIL') order by namaorganisasi asc ";	
}
else
{
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$sOrg="select distinct substr(periodegaji,1,4) as periodegaji from ".$dbname.".sdm_gaji order by periodegaji desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optTahun.="<option value=".$rOrg['periodegaji'].">".$rOrg['periodegaji']."</option>";
}
$arrTipe=array('3'=>'KBL','4'=>'KHT');
foreach($arrTipe as $rwTp=>$val){
	$optTipe.="<option value='".$rwTp."'>".$val."</option>";
}


$arr="##kdorg##tahun##tpkar";	



echo"<fieldset style='float:left;'>
		<legend>PPH 21</legend>
			<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['unit']."</td>
					<td>:</td>
					<td><select id=kdorg style=\"width:150px;\">".$optOrg."</select></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['tahun']."</td>
					<td>:</td>
					<td><select id=tahun style=\"width:150px;\">".$optTahun."</select></td>
				</tr>
				               <tr>
					<td>".$_SESSION['lang']['tipekaryawan']."</td>
					<td>:</td>
					<td><select id=tpkar style=\"width:150px;\">".$optTipe."</select></td>
				</tr>
				<tr>
					<td colspan=4>
					<button onclick=zPreview('sdm_slave_2pph','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
					<button onclick=zExcel(event,'sdm_slave_2pph.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
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
<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
//ambil periode gaji sesuai dengan lokasi tugas
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$optPeriode="<option value''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where sudahproses='0' order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
}
 
//ambil kodeorgannisasi dan organisasi dibawahnya
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
               where tipe='KEBUN' and CHAR_LENGTH(kodeorganisasi)=4 and kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
               order by namaorganisasi asc";	
        //$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
}
else if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
               where tipe='KEBUN' and CHAR_LENGTH(kodeorganisasi)=4 
               order by namaorganisasi asc";	
        
//        $optOrg.="<option value='SULAWESI'>SULAWESI</option>";
//        $optOrg.="<option value='KALIMANTAN'>KALIMANTAN</option>";
        
}
else{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi "
            . "where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']."-".$rOrg['namaorganisasi']."</option>";
}


$arr="##periode##kdUnit";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php    
echo"<div>
<fieldset style=\"float: left;\">
<legend><b>".$_SESSION['lang']['rekapsatuan']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">".$optOrg."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"center\">
<button onclick=\"zPreview('sdm_slave_2rekapsatuan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>"
."<button onclick=\"zExcel(event,'sdm_slave_2rekapsatuan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
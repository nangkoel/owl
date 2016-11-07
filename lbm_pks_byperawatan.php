<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

$arr="##kdOrg##periode##judul";
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optOrg;
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where  tipe='PABRIK' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
   $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}

echo"
<fieldset style=\"float: left;\">
<legend><b>".$_POST['judul']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['organisasi']."</label></td><td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\">".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>



<tr height=\"20\"><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$_POST['judul']."'></td></tr>
<tr><td colspan=\"2\">
<button onclick=\"zPreview('lbm_slave_pks_byperawatan','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
<button onclick=\"zExcel(event,'lbm_slave_pks_byperawatan.php','".$arr."','excel')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
<button onclick=\"zPdf('lbm_slave_pks_byperawatan','".$arr."','reportcontainer')\" class=\"mybutton\">PDF</button></td></tr>    


</table>
</fieldset>";

?>

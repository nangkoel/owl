<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

$arr="##kdUnit##periode";
$optUnit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPeriode=$optUnit;
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' and tipe='KEBUN' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error());
while($rUnit=mysql_fetch_assoc($qUnit))
{
   $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
}
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_panen_vw where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by tanggal desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}



echo"
<fieldset style=\"float: left;\">
<legend><b>".$_POST['judul']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\">".$optUnit."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>



<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\">
<button onclick=\"zPreview('lbm_slave_kualitas_angkut_buah','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
<button onclick=\"zPdf('lbm_slave_kualitas_angkut_buah','".$arr."','reportcontainer')\" class=\"mybutton\">PDF</button>    
<button onclick=\"zExcel(event,'lbm_slave_kualitas_angkut_buah.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>

</table>
</fieldset>";

?>

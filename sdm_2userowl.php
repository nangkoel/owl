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
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc ";	
}
else
{
    $sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}
 
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kodeorg";
?>

<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>

<link rel=stylesheet type='text/css' href='style/zTable.css'>

<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['user']?> OWL</b></legend>
<table cellspacing="1" border="0" >
<tr>
    <td><label><?php echo $_SESSION['lang']['unit']?></label></td>
    <td><select id="kodeorg" name="kodeorg" style="width:150px"><!--<option value="">
        <?php echo $_SESSION['lang']['all']?></option>--><?php echo $optOrg?>
    </select></td>
</tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('sdm_slave_2userowl','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
<!--<button onclick="zPdf('sdm_slave_2prasarana','<?php echo $arr?>','printContainer')" class="mybutton" name="excel" id="excel">PDF</button>
    <button onclick="zExcel(event,'sdm_slave_2prasarana.php','<?php echo $arr?>')" class="mybutton" name="pdf" id="pdf">Excel</button>
<button onclick="zExcel(event,'sdm_slave_2rekapabsen.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>-->
<!--<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button>-->
</td></tr>
</table>
</fieldset>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>
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
$optUnitId="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optUnitId.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$arr="##unitId##tglDari##tglSmp";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b>Payroll Remise I</b></legend>
<table cellspacing="1" border="0" >
    <tr><td><label><?php echo $_SESSION['lang']['unit'];?></label></td><td><select id="unitId" style="width:150px;" ><?php echo $optUnitId; ?></select></td>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']." ".$_SESSION['lang']['dari'];?></label></td><td>
       <input type=text class=myinputtext id=tglDari name=tglDari onmousemove="setCalendar(this.id)" onkeypress="return false;"   maxlength="10"  style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']." ".$_SESSION['lang']['tglcutisampai'];?></label></td><td>
       <input type="text" class="myinputtext" id="tglSmp" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px" />  </td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2upah_remise','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zPdf('sdm_slave_2upah_remise','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
        <button onclick="zExcel(event,'sdm_slave_2upah_remise.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

</table>
</fieldset>
</div>

<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
<?php
//echo"<pre>";
//print_r($_SESSION);
//echo"</pre>";
?>
</div></fieldset>

<?php

CLOSE_BOX();
echo close_body();
?>
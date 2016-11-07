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
    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 
                          and kodeorganisasi not like '%HO' order by namaorganisasi asc";
$optKodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$qKodeOrg=mysql_query($sKodeorg) or die(mysql_error());
while($rKodeorg=mysql_fetch_assoc($qKodeOrg))
{
    $optKodeorg.="<option value='".$rKodeorg['kodeorganisasi']."'>".$rKodeorg['namaorganisasi']."</option>";
}
$arr="##kdUnit";
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript1.2 src='js/pad_pembebasan.js'></script> <!-- sambungin dengan pad_daftarPembebasan.php untuk link PDF -->

<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['pembebasan']." ".$_SESSION['lang']['lahan']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kodetraksi']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">
<?php echo $optKodeorg?></select></td></tr>
<tr><td colspan="2"><button onclick="zPreview('pad_2slave_pembebasanLahan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'pad_2slave_pembebasanLahan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
<?php
CLOSE_BOX();
echo close_body();
?>
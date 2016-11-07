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
$optbatch="<option value=''>".$_SESSION['lang']['all']."</option>";
//    $sBatch="select distinct batch from ".$dbname.".bibitan_mutasi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
//$qBatch=mysql_query($sBatch) or die(mysql_error());
//while($rBatch=mysql_fetch_assoc($qBatch))
//{
//    $optBatch.="<option value='".$rBatch['batch']."'>".$rBatch['batch']."</option>";
//}

$optkodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$kodeorg="select distinct kodeorganisasi,namaorganisasi 
    from ".$dbname.".organisasi where tipe='KEBUN' 
    order by namaorganisasi asc";
$query=mysql_query($kodeorg) or die(mysql_error());
while($result=mysql_fetch_assoc($query))
{
    $optkodeorg.="<option value='".$result['kodeorganisasi']."'>".$result['namaorganisasi']."</option>";
}

$arr="##kodeunit##kodebatch";
?>
<script language='javascript' src='js/zTools.js'></script>
<script language='javascript' src='js/zReport.js'></script>
<link rel='stylesheet' type='text/css' href='style/zTable.css'>
<script language='javascript1.2' src='js/bibit_2kartu.js'></script>      
<?php
echo'
<div style="margin-bottom: 30px;">
    <fieldset style="float: left;">
    <legend><b>'.$_SESSION['lang']['laporanStockBIbit'].'</b></legend>
    <table cellspacing="1" border="0" >
    <tr>
        <td><label>'.$_SESSION['lang']['unit'].'</label></td>
        <td><select id="kodeunit" name="kodeunit" onchange="ambilbatch(this.value);" style="width:150px">'.$optkodeorg.'</select>
        </td>
    </tr>
    <tr>
        <td><label>'.$_SESSION['lang']['batch'].'</label></td>
        <td><select id="kodebatch" name="kodebatch" style="width:150px">'.$optbatch.'</select></td>
    </tr>
    <tr>
        <td colspan="2">
        <button onclick="zPreview(\'bibit_slave_2kartu\',\''.$arr.'\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zExcel(event,\'bibit_slave_2kartu.php\',\''.$arr.'\')" class="mybutton" name="preview" id="preview">Excel</button>
        </td>
    </tr>
    </table>
    </fieldset>
</div>
<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>
    <div id=\'printContainer\' style=\'overflow:auto;height:350px;max-width:1220px\'>
    </div>
</fieldset>    
    ';
//        <button onclick="zPdf(\'bibit_2_slave_keluar_masuk\',\''.$arr.'\',\'printContainer\')" class="mybutton" name="preview" id="preview">PDF</button>

CLOSE_BOX();
echo close_body();
?>
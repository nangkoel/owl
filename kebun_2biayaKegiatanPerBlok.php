<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'  order by kodeorganisasi";

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kodeorg##kegiatan##tgl1##tgl2";
if($_SESSION['language']=='EN'){
    $zz='namakegiatan1 as namaakun';
}else{
    $zz='namakegiatan as namaakun';
}
$kegiatan="";
$str="select kodekegiatan as noakun,".$zz." from ".$dbname.".setup_kegiatan
      order by kodekegiatan,namakegiatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $kegiatan.="<option value='".$bar->noakun."'>".$bar->noakun." - ".$bar->namaakun."</option>";
}
if ($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
    $whr="where regional='".$_SESSION['empl']['regional']."' ";
}
$str="select kodekegiatan,noakun,namakegiatan as namaakun,regional from ".$dbname.".vhc_kegiatan ".$whr."
      order by kodekegiatan,namakegiatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if ($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
        $kegiatan.="<option value='".$bar->noakun.$bar->kodekegiatan."'>".$bar->noakun." - ".$bar->namaakun."</option>";
    } else {
        $kegiatan.="<option value='".$bar->noakun.$bar->kodekegiatan."'>".$bar->kodekegiatan." - ".$bar->namaakun." (".$bar->regional.")</option>";
        
    }
}

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>


<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo "Cost per Block Report"; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kebun']?></label></td><td><select id="kodeorg" name="kdOrg" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kegiatan']?></label></td><td><select id="kegiatan" name="kdAfd" style="width:150px"><?php echo $kegiatan?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td>
<input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /> s.d.
<input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <button onclick="zPreview('kebun_slave_2kegiatanPerBlok','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
    <button onclick="zPdf('kebun_slave_2kegiatanPerBlok','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
    <button onclick="zExcel(event,'kebun_slave_2kegiatanPerBlok.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>

</table>
</fieldset>
</div>
<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both;'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto; height:350px; max-width:1220px;'>

</div></fieldset>
<?php

CLOSE_BOX();
echo close_body();
?>
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
$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$intex=array('0'=>'External','1'=>'Internal','2'=>'Afiliasi');
$optTbs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTbsRe="<option value='3'>".$_SESSION['lang']['all']."</option>";
foreach($intex as $dt => $rw)
{
	$optTbs.="<option value=".$dt.">".$rw."</option>";
        $optTbsRe.="<option value=".$dt.">".$rw."</option>";
}

$arrRe="##kdPabrik##tgl1##tgl2";

$optPabrik="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOrg2=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optPabrik.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}
$sOrg="select distinct kodeorg from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by kodeorg";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
$unitintimbangan='(';
while($rData=mysql_fetch_assoc($qOrg))
{
        $optUnit.="<option value=".$rData['kodeorg'].">".$rData['kodeorg']."</option>";
        $unitintimbangan.="'".$rData['kodeorg']."',";
}
$unitintimbangan=substr($unitintimbangan,0,-1);
$unitintimbangan.=')';
$sOrg="select kodeorganisasi from ".$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan." order by kodeorganisasi";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$optAfdeling2="<option value=''>".$_SESSION['lang']['all']."</option>";
while($rData=mysql_fetch_assoc($qOrg))
{
        $optAfdeling2.="<option value=".$rData['kodeorganisasi'].">".$rData['kodeorganisasi']."</option>";
}
$sOrg="select distinct substr(tanggal,1,7) as periode from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$optPeriode="<option value=''></option>";
while($rData=mysql_fetch_assoc($qOrg))
{
        $optPeriode.="<option value=".$rData['periode'].">".$rData['periode']."</option>";
}

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
      <div>
<fieldset style="float: left;">
<legend><b>CPO & Kernel Loses</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pabrik']?></label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:170px"><?php echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type="text" class="myinputtext" id="tgl1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" />
        s.d. <input type="text" class="myinputtext" id="tgl2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" />
</td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2loses','<?php echo $arrRe?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('pabrik_slave_2loses','<?php echo $arrRe?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
        <button onclick="zExcel(event,'pabrik_slave_2loses.php','<?php echo $arrRe?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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
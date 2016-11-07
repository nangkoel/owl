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
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$lksiTugas."'";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}
$optBag="<option value=''>".$_SESSION['lang']['all']."</option>";
$sBag="select kode,nama from ".$dbname.".sdm_5departemen order by nama asc";//$optBag
$qBag=mysql_query($sBag) or die(mysql_error());
while($rBag=mysql_fetch_assoc($qBag))
{
	$optBag.="<option value=".$rBag['kode'].">".$rBag['nama']."</option>";
}
if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$optOrg="<select id=kdOrg name=kdOrg onchange=getPeriode() style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL') order by namaorganisasi asc ";	
}
else
{
	$optOrg="<select id=kdOrg name=kdOrg style=\"width:150px;\"><option value=''>".$_SESSION['lang']['all']."</option>";
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
}

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
	$optOrg2.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optOrg.="</select>";
$optOrg2.="</select>";
$optSisGaji="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrSisGaji=array("0"=>"Harian","1"=>"Bulanan");
foreach($arrSisGaji as $dt => $isi)
{
    $optSisGaji.="<option value=".$isi.">".$_SESSION['lang'][strtolower($isi)]."</option>";
}
$arr="##kdOrg##periode##kdBag##tgl1##tgl2##sisGaji";


?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/sdm_2rekapabsen.js'></script>
<link rel=stylesheet type='text/css href=style/zTable.css'>
<script>
function bersihForm()
{
    document.getElementById('tgl1').value='';
    document.getElementById('tgl2').value='';
    document.getElementById('tgl1').disabled=false;
    document.getElementById('tgl2').disabled=false;
    document.getElementById('kdOrg').value='';
    document.getElementById('sisGaji').value='';
    document.getElementById('kdBag').value='';
    document.getElementById('periode').value='';
    document.getElementById('printContainer').innerHTML='';
}
</script>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['rinciGajiBag']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><?php echo $optOrg?></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" onchange="getTgl()"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['bagian']?></label></td><td><select id="kdBag" name="kdBag" style="width:150px"><?php echo $optBag?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['sistemgaji']?></label></td><td>
        <select id="sisGaji" name="sisGaji" style="width:150px">
        <?php echo $optSisGaji; ?>
        </select></td></tr>

<tr height="20"><td colspan="2"><input type="hidden" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
<input type="hidden" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
</td></tr>
<tr><td colspan="2"><button onclick="zPreview('sdm_slave_2rincianGajiBagian','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('sdm_slave_2rincianGajiBagian','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'sdm_slave_2rincianGajiBagian.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="bersihForm()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

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
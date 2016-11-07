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

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$opttipekaryawan="<option value=''>".$_SESSION['lang']['all']."</option>";
$optOrg=$optPeriode;
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','PABRIK','KANWIL') order by namaorganisasi asc ";	
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}
$str="select * from ".$dbname.".sdm_5tipekaryawan where id<>0 order by tipe";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
$opttipekaryawan.="<option value='".$bar->id."'>".$bar->tipe."</option>";	
}	

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kdUnit##tpKary##periode";
//$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";
?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function Clear1()
{
    document.getElementById('kdUnit').value='';
    document.getElementById('periode').value='';
    document.getElementById('tpKary').value='';
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['npwp']?></b></legend>
<table cellspacing="1" border="0" >
<tr>
	<td><label><?php echo $_SESSION['lang']['unit']?></label></td>
	<td><select id="kdUnit" name="kdUnit" style="width:150px"><?php echo $optOrg?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['periode']?></label></td>
	<td><select id="periode" name="periode" style="width:150px">
		<?php echo $optPeriode?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['tipekaryawan']?></label></td>
	<td><select id="tpKary" name="tpKary" style="width:150px">
		<?php echo $opttipekaryawan?>
	</select></td>
</tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
	<button onclick="zPreview('sdm_slave_2daftarKaryNpwp','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
	<!--<button onclick="zPdf('sdm_slave_2daftarKaryNpwp','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
	<button onclick="zExcel(event,'sdm_slave_2daftarKaryNpwp.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
	<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button>
</td></tr>
</table>
</fieldset>
</div>


<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>
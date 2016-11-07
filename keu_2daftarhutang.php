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
$optNamaOrganisasi=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$sPeriode="select distinct substring(tanggal,1,7) as periode from ".$dbname.".keu_tagihanht order by substring(tanggal,1,7) desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    if(substr($rPeriode['periode'],5,2)=='12')
    {
//        $optPeriode.="<option value=".substr($rPeriode['periode'],0,4).">".substr($rPeriode['periode'],0,4)."</option>";
        $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
    }
    else
    {
        $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
    }
}

$optOrg="<select id=kdOrg name=kdOrg style=\"width:150px;\" ><option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select distinct kodeorg from ".$dbname.".keu_tagihanht order by kodeorg asc ";	
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorg'].">".$optNamaOrganisasi[$rOrg['kodeorg']]."</option>";
}
$optOrg.="</select>";
//$arr="##kdOrg##tgl1##tgl2##statTagihan";
$arr="##kdOrg##periode##statTagihan##periode2";

$arrOpt=array("0"=>"Belum Terbayar","1"=>"Sudah Terbayar");
$optStatus="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrOpt as $listBrs =>$dtStat)
{
    $optStatus.="<option value='".$listBrs."'>".$dtStat."</option>";
}
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function Clear1()
{
	document.location.reload();
/*    document.getElementById('kdOrg').value='';
    document.getElementById('tgl1').value='';
    document.getElementById('tgl2').value='';
    document.getElementById('statTagihan').value='';
    document.getElementById('printContainer').innerHTML='';*/
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['daftarHutang']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><?php echo $optOrg?></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['dari']." ".$_SESSION['lang']['periode']?></label></td><td><select id='periode' style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tglcutisampai']." ".$_SESSION['lang']['periode']?></label></td><td><select id='periode2' style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['status']?></label></td><td><select id="statTagihan" name="statTagihan" style="width:150px"><?php echo $optStatus?></select></td></tr>
<!--<tr><td><label><?php echo $_SESSION['lang']['tanggal']." ".$_SESSION['lang']['tagihan']." ".$_SESSION['lang']['dari']?></label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td><td><input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>-->

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('keu_slave_2daftarhutang','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('keu_slave_2daftarhutang','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'keu_slave_2daftarhutang.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button><button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

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
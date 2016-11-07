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

$arrRe="##thnKontrak##kdKomoditi";

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$sOrg="select distinct kodebarang,namabarang from ".$dbname.".log_5masterbarang where left(kodebarang,1)='4'  order by namabarang asc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rData=mysql_fetch_assoc($qOrg)){
    $optPeriode.="<option value=".$rData['kodebarang'].">".$rData['namabarang']."</option>";
}
?>
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
      <div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['daftarkontrak'] ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['tahunkontrak']?></label></td><td><input type="text" class="myinputtextnumber" id="thnKontrak" onkeypress='return angka_doang(event)' style="width:170px" /></td></tr>        
<tr><td><label><?php echo $_SESSION['lang']['komoditi']?></label></td><td><select id="kdKomoditi" name="kdKomoditi"  style="width:170px"><?php echo $optPeriode?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2daftarkontrak','<?php echo $arrRe?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('pabrik_slave_2loses','<?php echo $arrRe?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
        <button onclick="zExcel(event,'pabrik_slave_2daftarkontrak.php','<?php echo $arrRe?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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
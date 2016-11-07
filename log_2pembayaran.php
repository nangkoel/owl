<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_2pembayaran.js"></script>
<?php
if($_SESSION['language']=='EN'){
    $zz='kelompok1';
}else{
    $zz='kelompok';
}
$optKelompok=makeOption($dbname, 'log_5klbarang', 'kode,'.$zz);
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optSupplr=$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sPeriodeCari="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_po_vw where statuspo=3 order by substr(tanggal,1,7) desc";
$qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());
while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
{
   $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
}
$optSupp=$optNopo="<option value=''>".$_SESSION['lang']['all']."</option>";
$optjenis="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$stataPP=array("0"=>"Contract","1"=>"PO");
foreach($stataPP as $dataIni=>$listNama)
{
   $optjenis.="<option value='".$dataIni."'>".$listNama."</option>";
}
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$sOrg="select distinct supplierid,namasupplier,substr(kodekelompok,1,1) as tipe from ".$dbname.".log_5supplier where namasupplier!='' order by namasupplier asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optSupplr.="<option value='".$rOrg['supplierid']."'>".$rOrg['tipe']."-".$rOrg['namasupplier']."</option>";
}

$arr="##lstPo##kdUnit##periode##jenisId##suppId##periode2";
$arr2="##tgl_cari##tgl_cari2##jenisId2##kdUnit2##cariNopo##suppId2";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<!--<fieldset style="float: left;">
<legend><b>Payment History</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['dari']?> <?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px" onchange=getPt()><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['sampai']?> <?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode2" name="periode2" style="width:150px" ><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['jenis']?></label></td><td><select id="jenisId" name="jenisId" style="width:150px" onchange=getNopo()><?php echo $optjenis?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px" onchange=getAll()><?php echo $optNopo;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['nopo']?>/ No Kontrak</label></td><td><select id="lstPo" name="lstPo" style="width:150px"><?php echo $optNopo?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['supplier']?></label></td><td><select id="suppId" name="suppId" style="width:150px"><?php echo $optSupp?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2pembayaran','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'log_slave_2pembayaran.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
</table>
</fieldset>-->

<fieldset style="float: left;">
<legend><b>Free Query</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['dar']?> <?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['sampai']?> <?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type=text class=myinputtext id=tgl_cari2 onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['jenis']?></label></td><td><select id="jenisId2" name="jenisId2" style="width:150px"><?php echo $optjenis?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="kdUnit2" name="kdUnit2" style="width:150px" ><?php echo $optOrg;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['nopo']?>/ No Kontrak</label></td><td><input type=text id="cariNopo" class="myinputtext" style="width:150px;" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['supplier']?></label></td><td><select id="suppId2" name="suppId2" style="width:150px"><?php echo $optSupplr?></select>
<?php echo"<img src=images/search.png class=resicon title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['find']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\">"; ?></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2pembayaran2','<?php echo $arr2?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'log_slave_2pembayaran2.php','<?php echo $arr2?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>
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
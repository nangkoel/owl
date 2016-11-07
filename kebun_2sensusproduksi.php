<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$sOrg="select distinct substr(kodeorg,1,4) as kodeorg from ".$dbname.".kebun_rencanapanen_vw order by kodeorg asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorg'].">".$rOrg['kodeorg']."</option>";
}

$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_rencanapanen_vw order by tanggal asc";
$qPeriode=mysql_query($sPeriode); 
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($rPeriode=mysql_fetch_object($qPeriode))
{
	$optPeriode.="<option value='".$rPeriode->periode."'>".$rPeriode->periode."</option>";       
}
$arr="##kodeorg##periode";

?>
<script language=javascript src='js/zMaster.js'></script> 
<script language=javascript src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script languange=javascript1.2 src='js/formReport.js'></script>
<script languange=javascript1.2 src='js/zGrid.js'></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['sensusproduksi']; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kodeorg']?></label></td><td><select id="kodeorg" name="kodeorg" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <?php 
    echo "<button onclick=\"zPreview('kebun_slave_2sensusproduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
          <button onclick=\"zExcel(event,'kebun_slave_2sensusproduksi.php','".$arr."','printContainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
          <button onclick=\"zPdf('kebun_slave_2sensusproduksi','".$arr."','printContainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>"; 
    ?>
    </td>
</tr>
</table>
</fieldset>
<?php
CLOSE_BOX();
OPEN_BOX('','Result:');

echo"<fieldset><legend>List</legend>
         <div id='printContainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
     </fieldset>"; 
CLOSE_BOX();
close_body();

?>
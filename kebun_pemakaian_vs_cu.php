<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
       where tipe in ('kebun','pabrik') and length(kodeorganisasi)=4
       order by namaorganisasi";

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arr="##kodeorg##tgl1##tgl2";

?>
<script language=javascript src='js/zMaster.js'></script> 
<script language=javascript src='js/zSearch.js'></script>
<script languange=javascript1.2 src='js/formTable.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script languange=javascript1.2 src='js/formReport.js'></script>
<script languange=javascript1.2 src='js/zGrid.js'></script>

<script language=javascript src='js/kebun_pemakaian_vs_cu.js'></script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['pakaibarang']; ?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kodeorg']?></label></td><td><select id="kodeorg" name="kdOrg" style="width:150px"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td>
<input type="text" class="myinputtext" id="tgl1" name="tgl1" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /> s.d.
<input type="text" class="myinputtext" id="tgl2" name="tgl2" onmousemove="setCalendar(this.id);" onkeypress="return false;"  maxlength="10" style="width:60px;" /></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
    <?php 
    echo "<button onclick=\"zPrevi('kebun_slave_pemakaian_vs_cu','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
          <button onclick=\"zExcel(event,'kebun_slave_pemakaian_vs_cu.php','".$arr."','printContainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
          <button onclick=\"zPdf('kebun_slave_pemakaian_vs_cu','".$arr."','printContainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>"; 
    ?>
    </td>
</tr>
</table>
</fieldset>
<?php
CLOSE_BOX();
OPEN_BOX('','Result:');

echo"<fieldset><legend>Periode <span id=tgl_1></span> s/d <span id=tgl_2></span></legend>
         <div id='printContainer' style='width:100%;height:550px;overflow:scroll;background-color:#FFFFFF;'></div> 
     </fieldset>"; 
CLOSE_BOX();
close_body();

?>
<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script>
dtAll='<?php echo"##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer"; ?>';
</script>
<?php
for($x=0;$x<=24;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optPeriode.="<option value=".date("Y-m",$dt).">".date("Y-m",$dt)."</option>";
}

$sLokasi="select id,lokasi from ".$dbname.".setup_remotetimbangan order by lokasi asc";
$qLokasi=mysql_query($sLokasi) or die(mysql_error());
while($rLokasi=mysql_fetch_assoc($qLokasi))
{
	$optLksi.="<option value=".$rLokasi['id'].">".$rLokasi['lokasi']."</option>";
}

$lokasiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

/*if(eregi("e$",$lokasiTugas))
{
	$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang='40000003'";
	$qBrg=mysql_query($sBrg) or die(mysql_error());
	while($rBrg=mysql_fetch_assoc($qBrg))
	{
		$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
	}
}
elseif(eregi("m$",$lokasiTugas))
{
	$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
	$qBrg=mysql_query($sBrg) or die(mysql_error());
	while($rBrg=mysql_fetch_assoc($qBrg))
	{
		$optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
	}
}

*/
$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where kodebarang in ('40000003','40000001','40000002','40000004','40000005')";
	$qBrg=mysql_query($sBrg) or die(mysql_error());
	while($rBrg=mysql_fetch_assoc($qBrg))
	{
		$optBrg.="".$rBrg['namabarang']."<br />";
	}
$arr="##dbnm##prt##pswrd##ipAdd##period##usrName##lksiServer";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src=js/pabrik_3uploadtimbangan.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div style="margin-bottom: 30px;">
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['uploadTimbangan']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['lokasi']?></label></td><td>:</td><td>
<select id="lksiServer" name="lksiServer" style="width:150px" onchange="getDt()"><option value=""></option>
<?php echo $optLksi?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td>:</td><td>
<input type="text" class="myinputtext" id="period" name="period" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" />
<!--<select id="period" name="period" style="width:150px"><?php //echo $optPeriode?></select>--></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['komoditi']?></label></td><td>:</td><td><?php echo $optBrg?><!--<select id="kdBrg" name="kdBrg" style="width:150px" >
<?php echo $optBrg?>--></select></td></tr>

<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_3uploadtimbangan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="unLockForm()" class="mybutton" name="cancel" id="cancel"><?php echo $_SESSION['lang']['cancel']?></button><!--<button onclick="zPdf('kebun_slave_2pengangkutan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'kebun_slave_2pengangkutan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>--></td></tr>
</table>
<input type="hidden" name="dbnm" id="dbnm" />
<input type="hidden" name="prt" id="prt" />
<input type="hidden" name="pswrd" id="pswrd" />
<input type="hidden" name="ipAdd" id="ipAdd" />
<input type="hidden" name="usrName" id="usrName" />
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer'>

</div></fieldset>
<?php
CLOSE_BOX();
echo close_body();
?>
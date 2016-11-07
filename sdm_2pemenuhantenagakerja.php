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
$optDept=makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optPeriode="<option value=''>".$_SESSION['lang']['all']."</option>";
$optPeriode2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sprd="select distinct departemen from ".$dbname.".sdm_permintaansdm where stpersetujuanhrd=1 order by tanggal desc";
$qprd=mysql_query($sprd) or die(mysql_error($conn));
while($rprd=mysql_fetch_assoc($qprd)){
	$optPeriode.="<option value='".$rprd['departemen']."'>".$optDept[$rprd['departemen']]."</option>";
}
$sprd="select distinct  left(tanggal,7) as periode  from ".$dbname.".sdm_permintaansdm where  stpersetujuanhrd=1  order by tanggal desc";
$qprd=mysql_query($sprd) or die(mysql_error($conn));
while($rprd=mysql_fetch_assoc($qprd)){
	$optPeriode2.="<option value='".$rprd['periode']."'>".$rprd['periode']."</option>";
}
$arr="##deptId##periode##periodesmp";
 

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function masterPDF(table,column,cond,page,event) {
	// Prep Param
       
	param = "table="+table;
	param += "&emailDt="+column;
	
	// Prep Condition
	param += "&cond="+cond;
	
	// Post to Slave
	if(page==null) {
		page = 'null';
	}
	if(page=='null') {
		page = "slave_master_pdf";
	}
	
	showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px' src='"+page+".php?proses=cvData&"+param+"'></iframe>",'800','400',event);
	var dialog = document.getElementById('dynamic1');
	dialog.style.top = '50px';
	dialog.style.left = '15%';
}
function masterPDF2(table,column,cond,page,event) {
	// Prep Param
       
	param = "table="+table;
	param += "&column="+column;
	
	// Prep Condition
	param += "&cond="+cond;
	
	// Post to Slave
	if(page==null) {
		page = 'null';
	}
	if(page=='null') {
		page = "slave_master_pdf";
	}
	
	showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px' src='"+page+".php?proses=pdfDt&"+param+"'></iframe>",'800','400',event);
	var dialog = document.getElementById('dynamic1');
	dialog.style.top = '50px';
	dialog.style.left = '15%';
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b>Laporan Pemenuhan Tenaga Kerja</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['departemen']?></label></td><td><select id="deptId" name="deptId" style="width:150px"><?php echo $optPeriode;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']." ".$_SESSION['lang']['dari']?></label></td><td><select id="periode" name="periode" style="width:150px"><?php echo $optPeriode2;?></select></td></tr>
        <tr><td><label><?php echo $_SESSION['lang']['periode']." ".$_SESSION['lang']['sampai']?></label></td><td><select id="periodesmp" name="periodesmp" style="width:150px"><?php echo $optPeriode2;?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
        <button onclick="zPreview('sdm_slave_2pemenuhantenagakerja','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('sdm_slave_2progressRekruitment','<?php echo $arr?>','printContainer')" class="mybutton">PDF</button>-->
        <button onclick="zExcel(event,'sdm_slave_2pemenuhantenagakerja.php','<?php echo $arr?>')" class="mybutton">Excel</button>
       </td></tr>

</table>
</fieldset>
</div>
      <div>


<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
























<?php

CLOSE_BOX();
echo close_body();
?>
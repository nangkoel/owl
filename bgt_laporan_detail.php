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
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThn="select distinct  tahunbudget from ".$dbname.". bgt_budget order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optThn.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$arr="##thnBudget##kdWS";
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>

function summForm()
{
	//closeDialog();
	width='350';
	height='200';
	content="<div id=container style='overflow:auto;width:100%;height:190px;'></div>";
	ev='event';
	title="Detail Alokasi";
	showDialog1(title,content,width,height,ev);
}
//function getAlokasi(kdWS,kdkend,thnbdget)
function summForm2()
{
	//closeDialog();
	width='650';
	height='350';
	content="<div id=container2 style='overflow:auto;width:100%;height:330px;'></div>";
	ev='event';
	title="Detail Alokasi";
	showDialog2(title,content,width,height,ev);
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='200';
   height='150';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function Clear1()
{
    document.getElementById('thnBudget').value='';
    document.getElementById('kdWS').value='';
    document.getElementById('printContainer').innerHTML='';
}
</script>

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['budget']." ".$_SESSION['lang']['detail']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['budgetyear']?></label></td><td><select id='thnBudget' style="width:150px;"><?php echo $optThn?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['kodeorg']?></label></td><td><select id='kdWS'  style="width:150px;"><?php echo $optOrg?></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><!--<button onclick="zPreview('bgt_slave_laporan_detail','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
                    <button onclick="zPdf('bgt_slave_laporan_detail','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>
                    --><button onclick="zExcel(event,'bgt_slave_laporan_detail.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
                    <!--<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button></td></tr>

--></table>
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
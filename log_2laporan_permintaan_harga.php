<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

$arr="##kdBrg##tglDr##tglSmp";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<script>

function searchBrg(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findBrg()
{
	txt=trim(document.getElementById('no_brg').value);
	if(txt=='')
	{
		alert('Text is obligatory');
	}
	else if(txt.length<3)
	{
		alert('Too Short');	
	}
	else
	{
		param='txtfind='+txt;
		tujuan='log_slave_get_brg.php';
		post_response_text(tujuan, param, respog);
	}
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							document.getElementById('containerBrg').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
}
function setBrg(kdbrg,namabrg,satuan)
{
         document.getElementById('kdBrg').value=kdbrg;
	 document.getElementById('nmBrg').value=namabrg;
	 //document.getElementById('sat_'+nomor).value=satuan;
	 closeDialog();
}
</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['lapPenawaran']?></b></legend>
<table cellspacing="1" border="0" >
    <tr><td><label><?php echo $_SESSION['lang']['namabarang']?></label></td><td><input type="text" class="myinputtext" id="nmBrg" name="nmBrg" disabled style="width:150px;"  />&nbsp;<img src=images/search.png class=dellicon title=<?php echo $_SESSION['lang']['find'] ?> onclick="searchBrg('<?php echo $_SESSION['lang']['findBrg'] ?>','<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg'] ?></legend> Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=containerBrg></div>',event)";>
     <input type="hidden" id="kdBrg" name="kdBrg" /></td></tr>
    <tr><td><label><?php echo $_SESSION['lang']['tgldari']?></label></td><td><input type="text" class="myinputtext" id="tglDr" name="tglDr" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr><td><?php echo $_SESSION['lang']['tanggalsampai']?></td><td><input type="text" class="myinputtext" id="tglSmp" name="tglSmp" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_2laporan_permintaan_harga','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('log_slave_2laporan_permintaan_harga','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'log_slave_2laporan_permintaan_harga.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

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
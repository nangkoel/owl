<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';


?>

<script type="text/javascript" src="js/log_2alokasibiaya.js"></script>
<script type="text/javascript" src="js/zMaster.js"></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
    function zExceldetail(ev,tujuan,passParam)
{
	judul='Report Excel';
        var passP = passParam.split('##');
	
    var param = "proses=exceldetail";
    for(i=0;i<passP.length;i++) {
       // var tmp = document.getElementById(passP[i]);
	   	a=i;
        param += "&"+passP[a]+"="+passP[i+1];
    }
	
	printFile(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
</script>

<?php
$arr="##periodeBeli";
$arr2="##periode";
$str="select distinct periode from ".$dbname.".log_5saldobulanan order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$optPeriode.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}
OPEN_BOX('',"<b>PURCHASE SOURCE AND GOODS ALLOCATIONs</b><br>");
$frm[0].="<div>
<fieldset style='float: left;'>
<legend><b>".$_SESSION['lang']['pembelianBarang']."</b></legend>";
$frm[0].="<table cellspacing=1 border=0 >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=periodeBeli name=periode style='width:150px'>".$optPeriode."</select></td></tr>
<tr height=20><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2><button onclick=\"zPreview('log_slave_2alokasibiayaPembelian','".$arr."','printContainer')\" class=mybutton name=preview id=preview>Preview</button>
    <!--<button onclick=\"zPdf('sdm_slave_2slipGajiHarian','".$arr."','printContainer')\" class=mybutton name=preview  id=preview>PDF</button>-->
        <button onclick=\"zExcel(event,'log_slave_2alokasibiayaPembelian.php','".$arr."')\" class=mybutton name=preview id=preview>Excel</button></td></tr>

</table>
</fieldset>
</div>";

$frm[0].="<div style='margin-bottom: 30px;'>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
		";
$frm[0].="</tbody></table></fieldset>";

//assseettt
$frm[1].="<div>
<fieldset style='float: left;'>
<legend><b>".$_SESSION['lang']['pemakaianBarang']."</b></legend>";
$frm[1].="<table cellspacing=1 border=0 >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=periode name=periode style='width:150px'>".$optPeriode."</select></td></tr>
<tr height=20><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2>
<button onclick=\"zPreview('log_slave_2alokasibiayaPemakaian','".$arr2."','printContainer2')\" class=mybutton name=preview id=preview>Preview</button>
    <!--<button onclick=\"zPdf('sdm_slave_2slipGajiHarian','".$arr2."','printContainer2')\" class=mybutton name=preview  id=preview>PDF</button>-->
        <button onclick=\"zExcel(event,'log_slave_2alokasibiayaPemakaian.php','".$arr2."')\" class=mybutton name=preview id=preview>Excel</button>
</td>
</tr>

</table>
</fieldset>
</div>";

$frm[1].="<div style='margin-bottom: 30px;'>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
		";
$frm[1].="</tbody></table></fieldset>";



//========================
$hfrm[0]=$_SESSION['lang']['pembelianBarang'];
$hfrm[1]=$_SESSION['lang']['pemakaianBarang'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,220,930);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>
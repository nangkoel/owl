<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';
?>
<script type="text/javascript" src="js/zMaster.js"></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/pmn_2hargaharian.js'></script>

<?php
$arr="##periodePsr##barang";
$arr2="##komodoti##periodePsr2";
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optBrg=$optPeriode;
$str="select distinct substr(tanggal,1,7) as periode, kodeproduk from ".$dbname.".pmn_hargapasar order by tanggal desc";
$res=mysql_query($str);
$listBarang = "";
$period = array();
while($bar=mysql_fetch_object($res))
{
	if(!empty($listBarang)) {$listBarang .= ',';}
	$listBarang .= "'".$bar->kodeproduk."'";
	$period[$bar->periode] = $bar->periode;
}
foreach($period as $p) {
	$optPeriode .= "<option value='".$p."'>".$p."</option>";
}

$sBrng="select distinct kodebarang,namabarang from ".$dbname.".log_5masterbarang where kodebarang in (".$listBarang.") order by namabarang asc";
$qBrng=mysql_query($sBrng) or die(mysql_error($conn));
while($rBarang=mysql_fetch_assoc($qBrng))
{
    $optBrg.="<option value='".$rBarang['kodebarang']."'>".$rBarang['namabarang']."</option>";
}

OPEN_BOX('',"<b>Daily Price</b><br>");
$frm[0].="<div>
<fieldset style='float: left;'>
<legend><b>Trend Harga Harian</b></legend>";
$frm[0].="
	<div style='margin-bottom:3px'>
	<div style='width:100px;float:left'>".$_SESSION['lang']['komoditi']."</div>
	<select id=barang name=barang style='width:150px'>".$optBrg."</select>
	</div>
	<div style='margin-bottom:3px'>
	<div style='width:100px;float:left'>".$_SESSION['lang']['periode']."</div>
	<select id=periodePsr name=periode style='width:150px'>".$optPeriode."</select>
	</div>
	<button onclick=\"zPreview('pmn_slave_2hargapasar','".$arr."','printContainer')\" class=mybutton name=preview id=preview>Preview</button>
	<button onclick=\"zPdf('pmn_slave_2hargapasar_pdf','".$arr."','printContainer')\" class=mybutton name=preview id=preview>PDF</button>";
// $frm[0].="<button onclick=\"zExcel(event,'pmn_slave_2hargapasar.php','".$arr."')\" class=mybutton name=preview id=preview>Excel</button>
    // <button onclick=\"grafikProduksi(event)\" class=mybutton name=preview id=preview>Jpgraph</button></td></tr>";
$frm[0].="</fieldset>
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
<legend><b>Trend Harga Bulanan</b></legend>";
$frm[1].="<table cellspacing=1 border=0 >
<tr><td style='width:100px;'><label>".$_SESSION['lang']['komoditi']."</label></td><td><select id=komodoti name=komodoti style='width:150px'>".$optBrg."</select></td></tr>
<tr><td style='width:100px;'><label>".$_SESSION['lang']['periode']."</label></td><td>".makeElement('periodePsr2','date','',array('style'=>'width:150px'))."</td></tr>
<tr><td colspan=2>
<button onclick=\"zPreview('pmn_slave_2hargapasar_2','".$arr2."','printContainer2')\" class=mybutton name=preview id=preview>Preview</button>
<button onclick=\"zPdf('pmn_slave_2hargapasar_2_pdf','".$arr2."','printContainer2')\" class=mybutton name=preview id=preview>PDF</button>
</td>
</tr>

</table>
</fieldset>
</div>";//<button onclick=\"zPdf('pmn_slave_2hargapasar_2_pdf','".$arr2."','printContainer')\" class=mybutton name=preview id=preview>PDF</button>

$frm[1].="<div style='margin-bottom: 30px;'>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
		";
$frm[1].="</tbody></table></fieldset>";



//========================
$hfrm[0]="Trend Harga Harian";
$hfrm[1]="Trend Harga Bulanan";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,220,930);
//===============================================	
?>

<?php
CLOSE_BOX();
echo close_body();
?>
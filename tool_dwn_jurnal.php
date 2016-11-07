<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<?php
$arr="##periodeDt##ptId##tpId";
include('master_mainMenu.php');
OPEN_BOX();
$opt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$pil=array("1"=>$_SESSION['lang']['kasbank'],"3"=>$_SESSION['lang']['kontrak'],"4"=>$_SESSION['lang']['tbm']."/".$_SESSION['lang']['tm']."/".$_SESSION['lang']['panen'],"5"=>$_SESSION['lang']['traksi']);
foreach($pil as $dtl=>$vw)
{
    $opt.="<option value='".$dtl."'>".$vw."</option>";
}
$optUnit2=$optUnit=$optPrd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPrd.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit2.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['kodeorganisasi']." - ".$rUnit['namaorganisasi']."</option>";
}
$arrTipe=array("PNN"=>"Panen","TM"=>"Perawatan TM","TMINVK"=>"Pemakaian Brg TM","TBM"=>"Perawatan TBM","TBMINVK"=>"Pemakaian Brg TBM","BBT"=>"Bibitan","BBTINVK"=>"Pemakaian Brg Bibitan","1"=>"Penerimaan Gudang","3"=>"Penerimaan Mutasi","7"=>"Pengeluaran Mutasi","5"=>"Pengeluaran Barang","B"=>"Bank","K"=>"Kas Bank","VP"=>"Voucher Payable");
foreach($arrTipe as $rwTp=>$lstTipe){
	$optTipe.="<option value='".$rwTp."'>".$lstTipe."</option>";
}
echo"<table><tr><td valign=top><fieldset style=width:350px;>
     <legend>Verivikasi Jurnal</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['periode']."</td>
	   <td><select id=periodeDt style=width:150px>".$optPrd."</select></td>
	 </tr>
     <tr>
	   <td>".$_SESSION['lang']['pt']."</td>
	   <td><select id=ptId style=width:150px>".$optUnit2."</select></td>
	 </tr>
	  <tr>
	   <td>".$_SESSION['lang']['data']."</td>
	   <td><select id=tpId style=width:150px>".$optTipe."</select></td>
	 </tr>
	
	 </table>
	 <button onclick=\"zExcel(event,'tool_slave_dwn_jurnal.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
	 <!--<button class=mybutton id=tmblDt onclick=saveFranco('tool_slave_dwn_jurnal','".$arr."')>".$_SESSION['lang']['proses']."</button>-->
     </fieldset>";
echo"</td></tr></table>";

CLOSE_BOX();

echo"<div id=listData style=display:none>";
OPEN_BOX();
echo"<fieldset style=height:550px;width:650px;><legend>".$_SESSION['lang']['list']."</legend>
    <div id=container style=overflow:auto;height:450px;width:650px;>";
echo"</div></fieldset>";
CLOSE_BOX();
echo"</div>";
echo close_body();
?>
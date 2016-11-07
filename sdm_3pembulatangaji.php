<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();

?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/sdm_3pmbulatan.js></script>

<?php
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optOrg=$optPeriode;
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
       kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$sPrd="select distinct periode from ".$dbname.".sdm_5periodegaji where left(periode,4)='".date("Y")."' and sudahproses=0 "
    . "and kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by periode desc"; 
$qPrd=  mysql_query($sPrd) or die(mysql_error($conn));
while($rPrd=  mysql_fetch_assoc($qPrd)){
    $optPrd.="<option value='".$rPrd['periode']."'>".$rPrd['periode']."</option>";
}
$optTp.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTipe = array('3'=>'KBL','4'=>'KHT');
foreach($optTipe as $dtTp=>$lsTp){
    $optTp.="<option value='".$dtTp."'>".$lsTp."</option>";
}
 
$arr="##kdOrg##periodeGaji##tpKary";


echo"<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style=\"float: left;\">
<legend><b>Pembulatan Gaji</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['kodeorg']."</label></td><td><select id=\"kdOrg\" name=\"kdOrg\" style=\"width:150px\" onchange=getPeriodeGaji()>".$optOrg."</select></td></tr>"
."<tr><td><label>".$_SESSION['lang']['periodegaji']."</label></td><td><select id=periodeGaji style=\"width:150px;\" >".$optPrd."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tipekaryawan']."</label></td><td><select id=tpKary style=\"width:150px;\" >".$optTp."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_3pembulatangaji','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>";

CLOSE_BOX();
echo close_body();
?>
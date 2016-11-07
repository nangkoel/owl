<?php 
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$arr="##periode##judul##kdPt##regDt";
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
?>

    
<?php
$optRegional.="<option value=''>".$_SESSION['lang']['all']."</option>";
$sRegion="select distinct regional from ".$dbname.".bgt_regional where regional not in('DKI','LAMPUNG') order by regional asc";
$qRegion=mysql_query($sRegion) or die(mysql_error($conn));
while($rRegion=  mysql_fetch_assoc($qRegion))
{
    $optRegional.="<option value='".$rRegion['regional']."'>".$rRegion['regional']."</option>";
}
$arrTipe=array("1"=>"Kapital","2"=>"Non Kapital");
$optPt=$optTipe="<option value=''>".$_SESSION['lang']['all']."</option>";
foreach($arrTipe as $lstTipe=>$dtTipe)
{
    $optTipe.="<option value='".$lstTipe."'>".$dtTipe."</option>";
}

$optperiode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optperiode.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
}

$derk=1;
echo"
<table cellspacing=\"1\" border=\"0\" >
    <tr><td colspan=4>".$judul."</td></tr>
    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:150px;\">".$optperiode."</select></td></tr>";
echo"<tr><td><label>".$_SESSION['lang']['regional']."</label></td><td><select id='regDt' style=\"width:150px;\" onchange=getDtPt()>".$optRegional."</select></td></tr>";
echo"<tr><td><label>".$_SESSION['lang']['pt']."</label></td><td><select id='kdPt' style=\"width:150px;\">".$optPt."</select></td></tr>";
echo"<tr><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>
    <tr><td colspan=\"4\">
    <button onclick=\"zPreview('lbm_slave_proc_realisasi_fisik','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'lbm_slave_proc_realisasi_fisik.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
   </td></tr>
</table>
";
?>
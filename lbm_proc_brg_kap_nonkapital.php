<?php 
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$arr="##periode##judul##kdPt##regDt##smbrData";
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];
?>

    
<?php
$optRegional.="<option value=''>".$_SESSION['lang']['all']."</option>";
$sRegion="select distinct regional from ".$dbname.".bgt_regional where regional not in ('DKI','LAMPUNG') order by regional asc";
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
$arrSmbr=array("3"=>"Default","2"=>"Semua PO yang Di buat");

foreach($arrSmbr as $lstSmbr=>$dtSmbr){
    $optSmbr.="<option value='".$lstSmbr."'>".$dtSmbr."</option>";
}
//$sPt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
//$qPt=mysql_query($sPt) or die(mysql_error($conn));
//while($rPt=  mysql_fetch_assoc($qPt))
//{
//     $optPt.="<option value=".$rPt['kodeorganisasi'].">".$rPt['namaorganisasi']."</option>";
//}
$derk=1;
//echo"
//<table cellspacing=\"1\" border=\"0\" >
//    <tr><td colspan=4>".$judul."</td></tr>
//    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:150px;\">".$optperiode."</select></td>";
//echo"<td><label>".$_SESSION['lang']['regional']."</label></td><td><select id='regDt' style=\"width:150px;\" onchange=getDtPt()>".$optRegional."</select></td>";
//echo"<td><label>".$_SESSION['lang']['pt']."</label></td><td><select id='kdPt' style=\"width:150px;\">".$optPt."</select></td></tr>";
//$sKurs="select distinct kode from ".$dbname.".setup_matauang where kode!='IDR' and kode!='' order by kode desc";
//$qKurs=mysql_query($sKurs) or die(mysql_error($conn));
//while($rKurs=mysql_fetch_assoc($qKurs))
//{
//    $ardt+=1;
//    $drKurs[$ardt]=$rKurs['kode'];
////    echo"<tr><td><input type=hidden  id='mtUang_".$ard."' value='".$rKurs['kode']."' /><label>".$rKurs['kode']."</label></td><td><input type=text id='kurs_".$ard."' class='myinputtextnumber' onkeypress='return angka_doang(event)' /></td></tr>";
//}
//$rowdt=count($drKurs);
//for($ard=1;$ard<=$rowdt;$ard++)
//{
//    $arr.="##mtUang_".$ard."";
//    $arr.="##kurs_".$ard."";
//    echo"<tr><td><input type=hidden  id='mtUang_".$ard."' value='".$drKurs[$ard]."' /><label>".$drKurs[$ard]."</label></td>
//         <td><input type=text id='kurs_".$ard."' class='myinputtextnumber' onkeypress='return angka_doang(event)' /></td>";
//    if($ard!=$rowdt)
//    {
//    $nek=1+$ard;
//    $arr.="##mtUang_".$nek."";
//    $arr.="##kurs_".$nek."";
//    echo"<td><input type=hidden  id='mtUang_".$nek."' value='".$drKurs[$nek]."' /><label>".$drKurs[$nek]."</label></td>
//         <td><input type=text id='kurs_".$nek."' class='myinputtextnumber' onkeypress='return angka_doang(event)' /></td></tr>";
//    $ard=$nek;
//    }
//}
echo"
<table cellspacing=\"1\" border=\"0\" >
    <tr><td colspan=4>".$judul."</td></tr>
    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:150px;\">".$optperiode."</select></td></tr><tr>";
echo"<td><label>".$_SESSION['lang']['regional']."</label></td><td><select id='regDt' style=\"width:150px;\" onchange=getDtPt()>".$optRegional."</select></td></tr>
    ";

echo"<tr><td><label>".$_SESSION['lang']['pt']."</label></td><td><select id='kdPt' style=\"width:150px;\">".$optPt."</select></td></tr>";
echo"<tr>";
echo"<td><label>".$_SESSION['lang']['data']."</label></td><td><select id='smbrData' style=\"width:150px;\">".$optSmbr."</select></td></tr>
    ";
echo"<tr><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>
    <tr><td colspan=\"4\">
    <button onclick=\"zPreview('lbm_slave_proc_brg_kap_nonkapital','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'lbm_slave_proc_brg_kap_nonkapital.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
   <!--<button onclick=\"zPdf('lbm_slave_proc_brg_kap_nonkapital','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>
    <button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>--></td></tr>
</table>
";
?>
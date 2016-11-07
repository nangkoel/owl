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

$arr0="##tanggal"; 
?>
<script language=javascript src='js/zTools.js'></script>
<script type="text/javascript" src="js/sdm_2biayapengobatan.js"></script>
<script>


</script>

<link rel='stylesheet' type='text/css' href='style/zTable.css'>

<?php
$title[1]=$_SESSION['lang']['biayapengobatan'];

$optPt.="<option value=''>".$_SESSION['lang']['all']."</option>";
$spt="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qpt=mysql_query($spt) or die(mysql_error($conn));
while($rpt=  mysql_fetch_assoc($qpt)){
    $optPt.="<option value='".$rpt['kodeorganisasi']."'>".$rpt['namaorganisasi']."</option>";
}

$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
$sdr="select distinct left(periodegaji,4) as periode from ".$dbname.".sdm_gaji order by periodegaji desc";
$qdr=mysql_query($sdr) or die(mysql_error($conn));
while($rdr=  mysql_fetch_assoc($qdr)){
    $optPrdSmp.="<option value='".$rdr['periode']."'>".$rdr['periode']."</option>";
}
$arrsmstr=array("I"=>"Satu","II"=>"Dua");
foreach($arrsmstr as $lstsmtr=>$nmsstr){
    $optsmstr.="<option value='".$lstsmtr."'>".$nmsstr."</option>";
}
$arrdata=array("0"=>"Default","1"=>"Rumah Sakit");
foreach($arrdata as $lstsmtr=>$nmsstr){
    $optsmstr2.="<option value='".$lstsmtr."'>".$nmsstr."</option>";
}
 $arr="##ptId2##unitId2##thn##smstr";
echo"<fieldset style=\"float: left;\">
<legend><b>".$title[1]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >";
echo"<tr><td>".$_SESSION['lang']['pt']."</td>";
echo"<td><select id=ptId2  onchange='getUnit2()'  style=width:150px;>".$optPt."</select></td>";
echo"</tr>";
echo"<tr><td>".$_SESSION['lang']['lokasitugas']."</td>
          <td><select id=unitId2 style=width:150px;>".$optUnit."</select></td>
          </tr>";
echo"<tr><td>".$_SESSION['lang']['tahun']."</td>
          <td><select id=thn style=width:150px;>".$optPrdSmp."</select></td>
          </tr>";
echo"<tr><td>".$_SESSION['lang']['semester']."</td>
          <td><select id=smstr style=width:150px;>".$optsmstr."</select></td>
          </tr>";
//echo"<tr><td>".$_SESSION['lang']['data']."</td>
//          <td><select id=smbrData style=width:150px; onchange=getTmbl()>".$optsmstr2."</select></td>
//          </tr>";
 
echo"<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\">
        <button class=mybutton onclick=zPreview('sdm_slave_2biayapengobatan','".$arr."','printContainer2')>".$_SESSION['lang']['proses']."</button>
        <button onclick=\"zExcel(event,'sdm_slave_2biayapengobatan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>

<div id='printContainer2' style='overflow:auto;height:250px;max-width:1220px;'>
</div>

<div id='printContainer5' style='overflow:auto;height:250px;max-width:1220px;display:none;'>
</div>

<div id='printContainer7' style='overflow:auto;height:250px;max-width:1220px;display:none;'>
</div>
 
</fieldset>";



CLOSE_BOX();
echo close_body();
?>
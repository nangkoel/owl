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
//ambil periode gaji sesuai dengan lokasi tugas
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$optKary="<option value''>".$_SESSION['lang']['all']."</option>";
//$optKary=$optPeriode;
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg in (select distinct kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."') order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode)){
	$optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
}
 
//ambil kodeorgannisasi dan organisasi dibawahnya
if($_SESSION['empl']['tipelokasitugas']!='HOLDING'){
	$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' and kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
}else{
	$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	//$optOrg.="<option value=".$rOrg['regional'].">".$rOrg['regional']."</option>";
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$sTipe="select id,tipe from ".$dbname.".sdm_5tipekaryawan where id!=0";
$qTipe=mysql_query($sTipe) or die(mysql_error($conn));
while($rTipe=mysql_fetch_assoc($qTipe)){
	$optTipe.="<option value=".$rTipe['id'].">".$rTipe['tipe']."</option>";
}
$arr="##periode##regionId##tpKary";
$arr2="##periode2##regionId2##tpKary2";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script>
    function getKary(idr){
        if(idr==1){
            unitId=document.getElementById('kdUnit');
            unitId=unitId.options[unitId.selectedIndex].value;
        }
        if(idr==2){
            unitId=document.getElementById('afdId');
            unitId=unitId.options[unitId.selectedIndex].value;
        }
        param='kdUnit='+unitId;
        post_response_text('sdm_slave_2kartukerjaperkary.php?proses=getKary', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
					if(idr==1){
						dtdipisah=con.responseText.split("####");
						document.getElementById('afdId').innerHTML = dtdipisah[0];
						document.getElementById('karyId').innerHTML = dtdipisah[1];
					}else{
						document.getElementById('karyId').innerHTML = con.responseText;
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
  
    }
</script>
<?php    
echo"<div>
<fieldset style=\"float: left;\">
<legend><b>Rekap Gaji Per Kegiatan</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['pt']."</label></td><td><select id=\"regionId\" name=\"regionId\" style=\"width:150px\">".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tipekaryawan']."</label></td><td><select id=\"tpKary\" name=\"tpKary\" style=\"width:150px\">".$optTipe."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"center\">
<button onclick=\"zPreview('sdm_slave_2rekapperpekerjaan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>"
."<button onclick=\"zExcel(event,'sdm_slave_2rekapperpekerjaan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
<fieldset style=\"float: left;\">
<legend><b>Rekap Gaji Departemen</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode2\" name=\"periode2\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['pt']."</label></td><td><select id=\"regionId2\" name=\"regionId2\" style=\"width:150px\">".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tipekaryawan']."</label></td><td><select id=\"tpKary2\" name=\"tpKary2\" style=\"width:150px\">".$optTipe."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"center\">
<button onclick=\"zPreview('sdm_slave_2rekapperpekerjaan_v2','".$arr2."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>"
."<button onclick=\"zExcel(event,'sdm_slave_2rekapperpekerjaan_v2.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
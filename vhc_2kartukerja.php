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
$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['periode'].">".$rPeriode['periode']."</option>";
} 
//ambil kodeorgannisasi dan organisasi dibawahnya
$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi "
   . " where induk in (select distinct kodeorganisasi from ".$dbname.".organisasi where tipe='KANWIL' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe='TRAKSI'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	$optAfd.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']."-".$rOrg['namaorganisasi']."</option>";
}
$arrTp=array("3"=>"KBL","4"=>"KHT");
foreach($arrTp as $rwTp=>$nmTp){
    $optTip.="<option value='".$rwTp."'>".$nmTp."</option>";
}

$arr="##periode##afdId##karyId##tpKaryId";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script>
    function getKary(){
            unitId=document.getElementById('afdId');
            unitId=unitId.options[unitId.selectedIndex].value;
            tpKary=document.getElementById('tpKaryId');
            tpKary=tpKary.options[tpKary.selectedIndex].value;
        
        param='afdId='+unitId;
        param+='&tpKary='+tpKary;
        post_response_text('vhc_slave_2kartuperkary.php?proses=getKary', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('karyId').innerHTML = con.responseText;
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
<legend><b>".$_SESSION['lang']['rekapkerjakary']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['traksi']."</label></td><td><select id=\"afdId\" name=\"afdId\" style=\"width:150px\" onchange=getKary()>".$optAfd."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['tipekaryawan']."</label></td><td><select id=\"tpKaryId\" name=\"tpKaryId\" style=\"width:150px\" onchange=getKary()>".$optTip."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['namakaryawan']."</label></td><td><select id=\"karyId\" name=\"karyId\" style=\"width:150px\">".$optKary."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"center\">
<button onclick=\"zPreview('vhc_slave_2kartuperkary','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>"
."<button onclick=\"zExcel(event,'vhc_slave_2kartuperkary.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
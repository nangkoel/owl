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
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
               where kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')
               order by namaorganisasi asc";	
        //$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
}
else if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi 
               where tipe='KEBUN' and induk in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."') 
               order by namaorganisasi asc";	
        
//        $optOrg.="<option value='SULAWESI'>SULAWESI</option>";
//        $optOrg.="<option value='KALIMANTAN'>KALIMANTAN</option>";
        
}
else{
	$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi "
            . "where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg)){
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['kodeorganisasi']."-".$rOrg['namaorganisasi']."</option>";
}
$optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



$arr="##periode##kdUnit##karyId##afdId##karyId";
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script>
    function getKary(idr){
        periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
        if(idr==1){
            unitId=document.getElementById('kdUnit');
            unitId=unitId.options[unitId.selectedIndex].value;
        }
        if(idr==2){
            unitId=document.getElementById('afdId');
            unitId=unitId.options[unitId.selectedIndex].value;
        }
        param='kdUnit='+unitId+'&periode='+periode;
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
<legend><b>".$_SESSION['lang']['rekapkerjakary']."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id=\"periode\" name=\"periode\" style=\"width:150px\">".$optPeriode."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id=\"kdUnit\" name=\"kdUnit\" style=\"width:150px\" onchange=getKary(1)>".$optOrg."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id=\"afdId\" name=\"afdId\" style=\"width:150px\" onchange=getKary(2)>".$optAfd."</select></td></tr>
<tr><td><label>".$_SESSION['lang']['namakaryawan']."</label></td><td><select id=\"karyId\" name=\"karyId\" style=\"width:150px\">".$optKary."</select></td></tr>
<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
<tr><td colspan=\"2\" align=\"center\">
<button onclick=\"zPreview('sdm_slave_2kartukerjaperkary','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>"
."<button onclick=\"zExcel(event,'sdm_slave_2kartukerjaperkary.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr>
</table>
</fieldset>
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
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
$optOrg.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPer.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAfd.="<option value=''>".$_SESSION['lang']['all']."</option>";
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

// kebun
$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

// periode 
$sOrg="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_taksasi order by tanggal desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optPer.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
}
 
//$arr0="##kebun0##afdeling0##mandor0##periode0"; 
$arr0="##kebun0##afdeling0##periode0"; 
$arr="##kebun##afdeling##tanggal"; 
$arr2="##kebun2##afdeling2##periode2"; 
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function getPeriode(tab){
    if(tab==0){
        kebun=document.getElementById('kebun0').options[document.getElementById('kebun0').selectedIndex].value;        
        param='kebun='+kebun+'&proses=getAfdeling0';
    }
    if(tab==1){
        kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;        
        param='kebun='+kebun+'&proses=getAfdeling';
    }
    if(tab==2){
        kebun=document.getElementById('kebun2').options[document.getElementById('kebun2').selectedIndex].value;        
        param='kebun='+kebun+'&proses=getAfdeling';
    }

    tujuan='kebun_slave_2taksasipanen.php';
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    cor=con.responseText.split("####");
                    if(tab==0){
                        document.getElementById('afdeling0').innerHTML=cor[0];                        
                        document.getElementById('mandor0').innerHTML=cor[1];                        
                    }
                    if(tab==1){
                        document.getElementById('afdeling').innerHTML=cor[0];                        
                    }
                    if(tab==2){
                        document.getElementById('afdeling2').innerHTML=cor[0];                        
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function pindahtanggal(kebun,afdeling,tanggal) {
    var workField = document.getElementById('printContainer');
    var param = "kebun="+kebun+"&afdeling="+afdeling+"&tanggal="+tanggal;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    document.getElementById('tanggal').value=tanggal;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_2taksasipanen.php?proses=preview', param, respon);
}
</script>

<link rel='stylesheet' type='text/css' href='style/zTable.css'>

<?php


$title[0]=$_SESSION['lang']['laporan']." ".$_SESSION['lang']['rencanapanen']." ".$_SESSION['lang']['harian'];
$title[1]=$_SESSION['lang']['laporan']." ".$_SESSION['lang']['rencanapanen'];
$title[2]=$_SESSION['lang']['laporan']." ".$_SESSION['lang']['rencanapanen']." ".$_SESSION['lang']['bulanan'];

//<tr>
//    <td><label>".$_SESSION['lang']['mandor']."</label></td>
//    <td><select id=\"mandor0\" name=\"mandor0\"  style=\"width:150px\"><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td>
//</tr>
$frm[0].="<fieldset style=\"float: left;\">
<legend><b>".$title[0]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['kebun']."</label></td>
    <td><select id=\"kebun0\" name=\"kebun0\"  style=\"width:150px\" onchange=getPeriode(0)>".$optOrg."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['afdeling']."</label></td>
    <td><select id=\"afdeling0\" name=\"afdeling0\"  style=\"width:150px\">".$optAfd."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['periode']."</label></td>
    <td><select id=\"periode0\" name=\"periode0\"  style=\"width:150px\">".$optPer."</select></td>
</tr>

<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\">
        <button onclick=\"zPreview('kebun_slave_2taksasipanen0','".$arr0."','printContainer0')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen0.php','".$arr0."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer0' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$frm[1].="<fieldset style=\"float: left;\">
<legend><b>".$title[1]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['kebun']."</label></td>
    <td><select id=\"kebun\" name=\"kebun\"  style=\"width:150px\" onchange=getPeriode(1)>".$optOrg."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['afdeling']."</label></td>
    <td><select id=\"afdeling\" name=\"afdeling\"  style=\"width:150px\">".$optAfd."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['tanggal']."</label></td>
    <td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\"></td>
</tr>

<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\"> 
        <button onclick=\"zPreview('kebun_slave_2taksasipanen','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

$frm[2].="<fieldset style=\"float: left;\">
<legend><b>".$title[2]."</b></legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['kebun']."</label></td>
    <td><select id=\"kebun2\" name=\"kebun2\"  style=\"width:150px\" onchange=getPeriode(2)>".$optOrg."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['afdeling']."</label></td>
    <td><select id=\"afdeling2\" name=\"afdeling2\"  style=\"width:150px\">".$optAfd."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['periode']."</label></td>
    <td><select id=\"periode2\" name=\"periode2\"  style=\"width:150px\">".$optPer."</select></td>
</tr>

<tr height=\"20\">
    <td colspan=\"2\">&nbsp;</td>
</tr>
<tr>
    <td colspan=\"2\">
        <button onclick=\"zPreview('kebun_slave_2taksasipanen2','".$arr2."','printContainer2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'kebun_slave_2taksasipanen2.php','".$arr2."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
    </td>    
</tr>    
</table>
</fieldset>

<div style=\"margin-bottom: 30px;\">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";

//========================
$hfrm[0]=$title[0];
$hfrm[1]=$title[1];
$hfrm[2]=$title[2];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1100);
//===============================================


CLOSE_BOX();
echo close_body();
?>
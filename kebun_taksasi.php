<?php //@Copy nangkoelframework
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/kebun_taksasi.js'></script> 
<link rel=stylesheet type='text/css' href='style/zTable.css'>
<?php

OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['rencanapanen']."</h3></div>";
echo "<div><table align='center'><tr>";
echo"<td style='min-width:100px' v-align='middle'><img class=delliconBig src=images/skyblue/addbig.png title='".$_SESSION['lang']['new']."' onclick='showAdd()'><br>".$_SESSION['lang']['new']."</td>";
echo"<td style='min-width:100px' v-align='middle'><img class=delliconBig src=images/skyblue/list.png title='".$_SESSION['lang']['list']."' onclick='loadData(0)'><br>".$_SESSION['lang']['list']."</td>";
echo"<td style='min-width:100px' v-align='middle'><fieldset><legend>".$_SESSION['lang']['find']."</legend>";
echo $_SESSION['lang']['tanggal']." <input id=\"sNoTrans\" name=\"sNoTrans\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\">";
echo "<button onclick=\"cariData(0)\" class=\"mybutton\" name=\"sFind\" id=\"sFind\">".$_SESSION['lang']['find']."</button>";
echo"</legend></fieldset></td>";
echo "</tr></table></div>";
CLOSE_BOX();
 $arr="##tanggal##afdeling##blok##seksi##proses##hasisa##haesok##jmlhpokok##persenbuahmatang##jjgmasak##jjgoutput##hkdigunakan##bjr";
echo"<input type=hidden id=proses value=insert /><div id=formData style='display:none'>";
OPEN_BOX();
echo"<fieldset style='float:left'><legend><b>".$_SESSION['lang']['form']."</b></legend>";
echo"<table border=0 style='float:left;'>";
 
echo"<tr>";
echo"<td>".$_SESSION['lang']['tanggal']."</td>";
echo"<td><input id=\"tanggal\" name=\"tanggal\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\" onchange='' style=\"width:150px\" readonly=\"readonly\" onmousemove=\"setCalendar(this.id)\" type=\"text\">
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['kebun']."</td>";
echo"<td><select id='kebundt' style=\"width:150px\" onchange='getAfdeling(0,0,0)'><option value=''></option>";
$sorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."'";
//echo $sorg;
$qorg=mysql_query($sorg) or die(mysql_error($conn));
while($rorg=mysql_fetch_assoc($qorg)){
    echo"<option value='".$rorg['kodeorganisasi']."'>".$rorg['namaorganisasi']."</option>";
}
echo"</select></td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['afdeling']."</td>";
echo"<td><select id='afdeling' style=\"width:150px\">";
echo"<option value=''></option>";

echo"</select></td></tr>";
//echo"<tr>";
//echo"<td>".$_SESSION['lang']['mandor']."</td>";
//echo"<td><select id='mandor' style=\"width:150px\">";
//echo"<option value=''></option></select>
//     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['blok']."</td>";
//echo"<td><input id=\"blok\" name=\"blok\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:150px\" maxlength=45 type=\"text\">
//     </td></tr>"; 
echo"<td><select id='blok' style=\"width:150px\" onchange='getSPH()'>";
echo"<option value=''></option></select>
     </td></tr>";
echo"<tr>";
// SPH diambil dari SETUP - BLOK
echo"<td>SPH</td>";
echo"<td><input id=\"sph\" name=\"sph\" class=\"myinputtextnumber\" style=\"width:100px\" maxlength=45 type=\"text\" disabled>
     </td></tr>";
echo"<tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['section']."</td>";
echo"<td><input id=\"seksi\" name=\"seksi\" class=\"myinputtext\" onkeypress=\"return tanpa_kutip(event)\"  style=\"width:100px\" maxlength=45 type=\"text\">
     </td></tr>";
echo"<tr>";
echo"<td><button id=\"addHead\" name=\"addHead\" class=\"mybutton\" onclick=\"saveData('kebun_slave_taksasi','".$arr."')\">".$_SESSION['lang']['save']."</button></td>";
echo"</tr>";
echo"</table>";
#form cpo
echo"<table border=0 style=float:left;>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['hasisa']."</td>";
echo"<td><input id=\"hasisa\" name=\"hasisa\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\" onchange='getPokok()' style=\"width:100px\" type=\"text\">
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['haesok']."</td>";
echo"<td><input id=\"haesok\" name=\"haesok\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\" onchange='getPokok()' style=\"width:100px\" type=\"text\">
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['jmlhpokok']."</td>";
echo"<td><input id=\"jmlhpokok\" name=\"jmlhpokok\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"  style=\"width:100px\" type=\"text\" >
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['persenbuahmatang']."</td>";
echo"<td><input id=\"persenbuahmatang\" name=\"persenbuahmatang\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"  style=\"width:100px\" type=\"text\">
     </td></tr>";
echo"</table>";
#form kernel
echo"<table border=0 style=float:left;>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['jjgmasak']."</td>";
echo"<td><input id=\"jjgmasak\" name=\"jjgmasak\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"   style=\"width:100px\" type=\"text\" >
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['jjgoutput']."</td>";
echo"<td><input id=\"jjgoutput\" name=\"jjgoutput\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"   style=\"width:100px\" type=\"text\" >
     </td></tr>";
echo"<tr>";
echo"<td>HK Output</td>";
echo"<td><input id=\"hkdigunakan\" name=\"hkdigunakan\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"   style=\"width:100px\" type=\"text\" >
     </td></tr>";
echo"<tr>";
echo"<td>".$_SESSION['lang']['bjr']."</td>";
echo"<td><input id=\"bjr\" name=\"bjr\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"   style=\"width:100px\" type=\"text\" >
     </td></tr>";
echo"<tr>";
/*echo"<td>HK Dipekerjakan</td>";
echo"<td><input id=\"bisapanen\" name=\"bisapanen\" class=\"myinputtextnumber\" onkeypress=\"return angka_doang(event)\"   style=\"width:100px\" type=\"text\" >
     </td></tr>";*/
 
echo"</table>";
echo"</fieldset>";
CLOSE_BOX();
echo"</div>";

echo"<div id=dataList>";
# List
OPEN_BOX();
 
echo"<fieldset style='clear:left'><legend><b>".$_SESSION['lang']['list']."</b></legend>";
echo"<div id=container><script>loadData(0);</script></div>";
echo"</fieldset>";
CLOSE_BOX();
echo"</div>";
echo close_body();
?>
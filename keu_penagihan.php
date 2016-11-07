<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX("","<b>".strtoupper($_SESSION['lang']['penagihan'])."</b>"); //1 O
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript src=js/zTools.js></script>
<script type="text/javascript" src="js/keu_penagihan.js" /></script>



<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=loadData(0)>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['noinvoice'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariData(0)>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 

CLOSE_BOX();

OPEN_BOX();
echo"<div id=listData>";
echo"<fieldset><legend>".$_SESSION['lang']['data']."</legend>";
/*echo"<img src=\"images/pdf.jpg\" onclick=\"masterPDF('log_prapoht','','','log_print_pdf_pp',event)\" width=\"20\" height=\"20\" />
<img onclick=\"javascript:print()\" style=\"width: 20px; height: 20px; cursor: pointer;\" title=\"Print Page\" src=\"images/printer.png\">";*/
echo"<table cellpading=1 cellspacing=1 border=0 class=sortable style=width:100%>";
echo"<thead>";
echo"<tr><td>".$_SESSION['lang']['noinvoice']."</td>";
echo"<td>".$_SESSION['lang']['unit']."</td>";
echo"<td>".$_SESSION['lang']['tanggal']."</td>";
echo"<td>".$_SESSION['lang']['nodo']."</td>";
echo"<td>".$_SESSION['lang']['jumlah']."</td>";
echo"<td>".$_SESSION['lang']['keterangan']."</td>";
echo"<td colspan=4>".$_SESSION['lang']['action']."</td>";
echo"</tr></thead><tbody id=continerlist>";
echo"<script>loadData(0)</script>";
echo"</tbody>";
$skeupenagih="select count(*) as rowd from ".$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$qkeupenagih=mysql_query($skeupenagih) or die(mysql_error($conn));
$rkeupenagih=mysql_num_rows($qkeupenagih);
$totrows=ceil($rkeupenagih/10);
if($totrows==0){
    $totrows=1;
}
for($er=1;$er<=$totrows;$er++){
    $isiRow.="<option value='".$er."'>".$er."</option>";
}
echo"<tfoot id=footData>";
//<tr>";
//if($totrows==1){
//   echo"<td colspan=\"10\" align=\"center\">
//    <img src=\"images/skyblue/first.png\">&nbsp;
//    <img src=\"images/skyblue/prev.png\">&nbsp;
//    <select id=\"pages\" name=\"pages\" style=\"width:50px\" onchange=\"getPage()\">".$isiRow."
//    </select>&nbsp;
//    <img src=\"images/skyblue/next.png\">&nbsp;<img src=\"images/skyblue/last.png\"></td>";
//}else{
//echo"<td colspan=\"10\" align=\"center\">
//    <img src=\"images/skyblue/first.png\" onclick='loadPage(0)' style=curosr:pointer >&nbsp;
//    <img src=\"images/skyblue/prev.png\">&nbsp;
//    <select id=\"pages\" name=\"pages\" style=\"width:50px\" onchange=\"getPage()\">".$isiRow."
//    </select>&nbsp;
//    <img src=\"images/skyblue/next.png\">&nbsp;<img src=\"images/skyblue/last.png\" onclick=loadPage(".$totrows.") style=curosr:pointer></td>";
//
//}
echo"</tfoot></table></fieldset>";
echo"</div><input type=hidden id=proses value=insert />";
#byr ke
$whereJam=" kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
$sakun="select distinct noakun,namaakun from ".$dbname.".keu_5akun 
        where  ".$whereJam." order by namaakun asc";
$qakun=mysql_query($sakun) or die(mysql_error($conn));
while($rakun=  mysql_fetch_assoc($qakun)){
    $optAkun.="<option value='".$rakun['noakun']."'>".$rakun['noakun']."-".$rakun['namaakun']."</option>";
}
#kodepelanggan

$sakun="select distinct kodecustomer,namacustomer from ".$dbname.".pmn_4customer where  klcustomer='06'
        order by namacustomer asc";
$qakun=mysql_query($sakun) or die(mysql_error($conn));
while($rakun=  mysql_fetch_assoc($qakun)){
    $optCust.="<option value='".$rakun['kodecustomer']."'>".$rakun['kodecustomer']."-".$rakun['namacustomer']."</option>";
}
#akuun debet
$sakundbt="select distinct noakun,namaakun from ".$dbname.".keu_5akun where left(noakun,1)='5' and char_length(noakun)>1
        order by namaakun asc";
$qakun=mysql_query($sakundbt) or die(mysql_error($conn));
while($rakun=  mysql_fetch_assoc($qakun)){
    $optDebet.="<option value='".$rakun['noakun']."'>".$rakun['noakun']."-".$rakun['namaakun']."</option>";
}
$sakundbt="select distinct noakun,namaakun from ".$dbname.".keu_5akun where noakun like '113%' and char_length(noakun)=7
        order by namaakun asc";
$qakun=mysql_query($sakundbt) or die(mysql_error($conn));
while($rakun=  mysql_fetch_assoc($qakun)){
    $optKredit.="<option value='".$rakun['noakun']."'>".$rakun['noakun']."-".$rakun['namaakun']."</option>";
}
$arr="##noinvoice##jatuhtempo##kodeorganisasi##nofakturpajak##tanggal##bayarke##proses";
$arr.="##kodecustomer##uangmuka##noorder##nilaippn##keterangan##nilaiinvoice##debet##kredit";
echo"<div id=formInput style=display:none;>";
echo"<fieldset style=float:left;><legend>".$_SESSION['lang']['form']."</legend>
    <table style=width:100%;>";
echo"<tr><td>".$_SESSION['lang']['noinvoice']."</td><td><input type=text id=noinvoice class=myinputtext style=width:150px;  readonly></td>";
echo"<td>".$_SESSION['lang']['jatuhtempo']."</td><td><input type=text class=myinputtext id=jatuhtempo onmousemove=setCalendar(this.id) onkeypress=return false;  style=width:150px;  maxlength=10 /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td><input type=text id=kodeorganisasi class=myinputtext style=width:150px; readonly value='".$_SESSION['empl']['lokasitugas']."' /></td>";
echo"<td>".$_SESSION['lang']['nofaktur']."</td><td><input type=text class=myinputtext id=nofakturpajak  style=width:150px;  onkeypress='return tanpa_kutip(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  style=width:150px;  maxlength=10 /></td>";
echo"<td>".$_SESSION['lang']['bayarke']."</td><td><select id=bayarke  style=width:150px;>".$optAkun."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kodecustomer']."</td><td><select id=kodecustomer style=width:150px>".$optCust."</select></td>";
echo"<td>".$_SESSION['lang']['uangmuka']."</td><td><input type=text id=uangmuka class=myinputtextnumber style=width:150px; /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['nodo']."</td><td><input type=text id=noorder class=myinputtext style=width:150px; readonly onclick=\"searchNosibp('".$_SESSION['lang']['find']." ".$_SESSION['lang']['nosipb']."','<div id=formPencariandata></div>',event)\" /></td>";
echo"<td>".$_SESSION['lang']['nilaippn']."</td><td><input type=text id=nilaippn class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan class=myinputtext style=width:150px; onkeypress='return tanpa_kutip(event)'  /></td>";
echo"<td>".$_SESSION['lang']['nilaiinvoice']."</td><td><input type=text id=nilaiinvoice class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['debet']."</td><td><select id=debet style=width:150px;>".$optKredit."</select></td>";
echo"<td>".$_SESSION['lang']['kredit']."</td><td><select id=kredit style=width:150px;>".$optDebet."</select></td></tr>";
echo"<tr><td colspan=4><button class=mybutton onclick=saveData('keu_slave_penagihan','".$arr."')>".$_SESSION['lang']['save']."</button>&nbsp;
         <button class=mybutton onclick=cancelData()>".$_SESSION['lang']['cancel']."</button></td></tr>";


echo"</table></fieldset>"; 
echo"</div>";
CLOSE_BOX();
echo close_body(); ?>

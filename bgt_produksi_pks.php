<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
?>

<?php
include('master_mainMenu.php');
?>
<script language="javascript" src="js/zMaster.js"></script>
<script language=javascript1.2 src='js/bgt_produksi_pks.js'></script>

<?php
//$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
        $optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
}

$arr=array("External","Internal","Afliasi");
$opttbs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arr as $isi =>$eia)
{
        $opttbs.="<option value=".$isi." >".$eia."</option>";
}
$optsup="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



$optthnttp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optorgclose="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//untuk header sort
$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

OPEN_BOX('',"<b>".$_SESSION['lang']['produksipks']."</b>");

echo"<br /><br /><fieldset style='float:left;'>
                <legend>".$_SESSION['lang']['entryForm']."</legend> 
                        <table border=0 cellpadding=1 cellspacing=1>
                                 <tr><td width=95>".$_SESSION['lang']['budgetyear']."</td><td td width=7>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress=\"return angka_doang(event);\" style=\"width:175px;\" maxlength=4 /></td></tr>
                                 <tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id=kdpks name=kdpks style=\"width:175px;\">".$optOrg."</select></td></tr>
                                 <tr><td>".$_SESSION['lang']['statusBuah']."</td><td>:</td><td><select id=ktbs name=ktbs style=\"width:175px;\">".$opttbs."</select></td></tr>

                                <tr><td></td><td></td><td><br /><div id=tmblSave>
                                         <button onclick=savehead(0) class=mybutton name=saveDt id=saveDt>".$_SESSION['lang']['save']."</button> 
                                         <button class=mybutton onclick=batal() name=btl id=btl>".$_SESSION['lang']['cancel']."</button></div></td></tr>
                        </table></fieldset><input type=hidden id=method value=saveData />";


echo"<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div id=closetab><table>
                <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><select id=thnttp style='widht:150px'>".$optthnttp."</select></td></tr>
                <tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td><select id=lkstgs style='widht:150px'>".$optorgclose."</select></td></tr>";

echo"<tr><td></td><td></td><td><br /><button class=\"mybutton\"  id=\"saveData\" onclick='closepks()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
echo"</div></fieldset>";

echo"<div id='printContainer' style=display:none;>
      <fieldset style='clear:both;float: left;'><legend>Sebaran Bulanan</legend>";

$arrBln=array(
"1"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jan'],0,3),
"2"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['peb'],0,3),
"3"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['mar'],0,3),
"4"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['apr'],0,3),
"5"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['mei'],0,3),
"6"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jun'],0,3),
"7"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['jul'],0,3),
"8"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['agt'],0,3),
"9"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['sep'],0,3),
"10"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['okt'],0,3),
"11"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['nov'],0,3),
"12"=>$_SESSION['lang']['kgtbs']." ".substr($_SESSION['lang']['dec'],0,3)
);

$tot=count($arrBln);
echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>";
echo"
        <td>".$_SESSION['lang']['kodesupplier']."</td>
        <td>".$_SESSION['lang']['kgtbs']."</td>
        <td>".$_SESSION['lang']['oer']."(CPO)</td>
        <td>".$_SESSION['lang']['oer']."(Ker)</td>";

foreach($arrBln as $brs=>$dtBln)
{
        echo"<td>".$dtBln."</td>";
}
echo"<td>".$_SESSION['lang']['action']."</td></tr></thead>";
echo"<tbody><tr class=rowcontent>";
echo"
        <td><select id=kdsup name=kdsup style=\"width:150px;\">".$optsup."</select></td>
        <td><input type=text class=myinputtextnumber id=kgtbs name=kgtbs onblur=bagi() onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>
        <td><input type=text class=myinputtextnumber id=oerc name=oerc onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>
        <td><input type=text class=myinputtextnumber id=oerk name=oerk onkeypress=\"return angka_doang(event);\" style=\"width:50px;\"  /></td>";

foreach($arrBln as $brs2=>$dtBln2)
{
        echo"<td><input type='text' class='myinputtextnumber' id=brt_x".$brs2." value=0 style='width:50px' onkeypress=\"return angka_doang(event);\" /></td>";
}
echo"<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveBrt(".$tot.")\" src='images/save.png'/></td></tr></tbody></table>";
echo "</fieldset></div>";
CLOSE_BOX();

OPEN_BOX();

echo"<div>".$_SESSION['lang']['budgetyear'].": <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></div>";
echo"<fieldset><legend><b>".$_SESSION['lang']['list']."</b></legend>";
echo"<div id=contain><script>loadData()</script></div>";
echo"</fieldset>";
CLOSE_BOX();
echo close_body();
?>
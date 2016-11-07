<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/budget_5blok.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php

$optOrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('AFDELING','BIBITAN') and induk='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optOrg2.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}

OPEN_BOX('',"<b>".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['blok']."</b>");
$frm[0].="<fieldset><legend>".$_SESSION['lang']['bloklm']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=thnAnggran name=thnAnggran maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><select id=idAfd name=idAfd style=width:150px;>".$optOrg2."</select></td></tr>
<tr><td colspan=3>
<button class=mybutton id=save_kepala name=save_kepala onclick=cekData()>Preview</button>
<button class=mybutton id=btlTmbl name=btlTmbl onclick=batal()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /><br />
<div id=dataList style=display:none;>
<fieldset><legend>".$_SESSION['lang']['list']."</legend>

<div id=isiContainer>
</div>
</fildset>
</div>

";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kebun']."</td>
            <td>".$_SESSION['lang']['afdeling']."</td>
            <td>".$_SESSION['lang']['status']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containData><script>loadDataLama()</script>
		";
$frm[0].="</tbody></table></fieldset>";

$frm[1].="<fieldset><legend>".$_SESSION['lang']['blokbr']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=thnAnggranBr name=thnAnggranBr maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><select id=idAfdBr name=idAfdBr style=width:150px;>".$optOrg2."</select></td></tr>


<tr><td colspan=3>
<button class=mybutton id=save_kepalaBr name=save_kepalaBr onclick=cekDataBr()>Preview</button>
<button class=mybutton id=btlTmbl name=btlTmbl onclick=batalBr()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br />
<div id=dataListBr style=display:none;>
<fieldset><legend>".$_SESSION['lang']['list']."</legend>
<!--<div id=isiContainer style=overflow:auto;height:650px;width:750px;>-->
<div id=isiContainerBr>
</div>
</fildset>
</div>
<input type=hidden id=prosesBr name=prosesBr value=insert_baru >
";


$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['kebun']."</td>
            <td>".$_SESSION['lang']['afdeling']."</td>
            <td>".$_SESSION['lang']['blok']."</td>
            <td>".$_SESSION['lang']['hathnini']."</td>
            <td>".$_SESSION['lang']['pokokthnini']."</td>
            <td>".$_SESSION['lang']['statusblok']."</td>
            <td>".$_SESSION['lang']['topografi']."</td>
            <td>".$_SESSION['lang']['thntnm']."</td>
            <td>".$_SESSION['lang']['lcthnini']."</td>
            <td>".$_SESSION['lang']['hanonproduktif']."</td>
            <td>".$_SESSION['lang']['pkkproduktif']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDetail>
		";
$frm[1].="</tbody></table></fieldset>";
$optThn="<option value=''>".$_SESSION['lang']['budgetyear']."</option>";


$frm[2].="<fieldset><legend>".$_SESSION['lang']['blokcls']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['ttpBudget']."</td><td>:</td><td>
<select id=thnBudget style='width:100px;'>".$optThn."</select></td></tr>

<tr><td colspan=3>
<button class=mybutton onclick=prosesClose() >".$_SESSION['lang']['proses']."</button>
<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />
</td></tr>
</table>";

$frm[2].="</fieldset>";

//========================
$hfrm[0]=$_SESSION['lang']['bloklm'];
$hfrm[1]=$_SESSION['lang']['blokbr'];
$hfrm[2]=$_SESSION['lang']['close'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>


<?php
CLOSE_BOX();
echo close_body();
?>
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
<script type="text/javascript" src="js/bgt_alokasi_supervisi.js"></script>
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

OPEN_BOX('',"<b>".$_SESSION['lang']['alokasisupervisi']."</b>");
$frm[0].= OPEN_THEME($_SESSION['lang']['keterangan'].":");
$frm[0].="<fieldset  style='text-align:left;width:300px;'><legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>";
$frm[0].="<div align=justify>".$_SESSION['lang']['infoSupervisi']."</div>";
$frm[0].="</fieldset>";
$frm[0].= CLOSE_THEME();

$frm[1].="<fieldset style='width:300px'><legend>".$_SESSION['lang']['hksupervisi']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=thnAnggran  name=thnAnggran maxlength=4 onkeypress=\"return angka_doang(event);\" style=width:150px; onblur=getHk() /></td></tr>
<tr><td>".$_SESSION['lang']['kodebudget']."</td><td>:</td><td><input type=text disabled class=myinputtext  id=kdBudget name=kdBudget  onkeypress=\"return tanpa_kutip(event);\" style=width:150px; value='SUPERVISI' /></td></tr>
<tr><td>".$_SESSION['lang']['upahsupervisi']."</td><td>:</td><td><input type=text  class=myinputtextnumber  id=uphSupervisi name=uphSupervisi  onkeypress=\"return angka_doang(event);\" style=width:150px; onblur=kalikan()  /></td></tr>
<tr><td>".$_SESSION['lang']['jmlhPersonel']."</td><td>:</td><td><input type=text  class=myinputtextnumber  id=jmlhPersonel name=jmlhPersonel  onkeypress=\"return angka_doang(event);\" style=width:150px;  onblur=kalikan() /></td></tr>
<tr><td>".$_SESSION['lang']['totalUpahSpr']."</td><td>:</td><td><input type=text disabled class=myinputtext  id=totUpah name=totUpah  onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>


<tr><td colspan=3>
<button class=mybutton id=save_kepalaBr name=save_kepalaBr onclick=tampilKan()>".$_SESSION['lang']['preview']."</button>
<button class=mybutton id=btlTmbl name=btlTmbl onclick=batalBr()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><input type='hidden' id='hkEfektif' name='hkEfektif' />
";


$frm[1].="</fieldset>";
$frm[1].="<div id=listPrevData style='display:none;'><fieldset style=width:1100px><legend>".$_SESSION['lang']['list']."</legend>";
$frm[1].="<button class=mybutton id=saveAwal name=saveAwal onclick=saveAll(1) >".$_SESSION['lang']['save']."</button>&nbsp;<button class=mybutton id=lnjutTmbl name=lnjutTmbl onclick=lanjutkan() style='display:none'>".$_SESSION['lang']['lanjut']."</button>";
$frm[1].="<table cellpadding=1 cellspacing=1 border=0 class=sortable widht=100%>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rotasi']."</td>
            <td>".$_SESSION['lang']['jumlahhk']."</td>
            <td>".$_SESSION['lang']['namakegiatan']."</td>
            <td>".$_SESSION['lang']['hksupervisi']."</td>
            <td>".$_SESSION['lang']['supervisi']."</td>
            </tr>
            </thead><tbody id=containDetail>
		";
$frm[1].="</tbody></table></fieldset></div>";
$optThn="<option value=''>".$_SESSION['lang']['budgetyear']."</option>";


$frm[2].="<fieldset style='width:250px;'><legend>".$_SESSION['lang']['sebaran']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><input type=text class=myinputtextnumber id=thnBudget style='width:100px;' onkeypress='return angka_doang(event)' maxlength=4 /></td></tr>

<tr><td colspan=3>
<button class=mybutton onclick=prevSebaran() id=tmblPrev>".$_SESSION['lang']['preview']."</button>

</td></tr>
</table></fieldset><br />";

$frm[2].="<div id=contentSebaran style=width:100%; ></div>";

$frm[3].="<fieldset style='width:250px;'><legend>".$_SESSION['lang']['ulang']."</legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td><input type=text class=myinputtextnumber id=thnBudgetUlg style='width:100px;' onkeypress='return angka_doang(event)' maxlength=4 /></td></tr>

<tr><td colspan=3>
<button class=mybutton onclick=delAll()>".$_SESSION['lang']['delete']."</button>

</td></tr>
</table><br />";
$frm[3].="</fieldset>";
$frm[3].="<div id=contentSebaran></div>";

//========================
$hfrm[0]=$_SESSION['lang']['keterangan'];
$hfrm[1]=$_SESSION['lang']['hksupervisi'];
$hfrm[2]=$_SESSION['lang']['sebaran'];
$hfrm[3]=$_SESSION['lang']['ulang'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,1100);
//===============================================	
?>


<?php
CLOSE_BOX();
echo close_body();
?>
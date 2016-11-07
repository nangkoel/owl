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
<script>
pilh=" <?php echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/budget_vhc.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$optTraksi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sVhc="select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%'";
//echo $sTraksi;
$qVhc=mysql_query($sVhc) or die(mysql_error());
while($rVhc=mysql_fetch_assoc($qVhc))
{
    $optVhc.="<option value='".$rVhc['kodevhc']."'>".$rVhc['kodevhc']."</option>";
}

$sTraksi="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI'";
//echo $sTraksi;
$qTraksi=mysql_query($sTraksi) or die(mysql_error());
while($rTraksi=mysql_fetch_assoc($qTraksi))
{
    $optTraksi.="<option value='".$rTraksi['kodeorganisasi']."'>".$rTraksi['namaorganisasi']."</option>";
}
$optKdbdgt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%SDM%' order by nama asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optKdbdgt.="<option value=".$rOrg2['kodebudget'].">".$rOrg2['nama']."</option>";
}

OPEN_BOX('',"<b>".$_SESSION['lang']['anggaran']."  Kendaraan/Mesin/AB</b>");
echo"<br /><br /><fieldset style='float:left;'><legend>".$_SESSION['lang']['form']."</legend> <table border=0 cellpadding=1 cellspacing=1>";
echo"<tr><td>".$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='TRK' id='tipeBudget' style=width:150px; /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' /></td></tr>";
//echo"<tr><td>".$_SESSION['lang']['kodetraksi']."</td><td><select style='width:150px;' id='kdTraksi' onchange='getKdvhc(0,0)'>".$optTraksi."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kodetraksi']."</td><td><select style='width:150px;' id='kdTraksi'>".$optTraksi."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kodevhc']."</td><td><select style='width:150px;' id='kodeVhc'>".$optVhc."</select></td></tr>";
echo"<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData\" onclick='saveData()'>".$_SESSION['lang']['save']."</button><button  class=\"mybutton\"  id=\"newData\" onclick='newData()'>".$_SESSION['lang']['baru']."</button></td></tr>";
echo"</table></fieldset>";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['sdm']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudget' style='width:150px;' onchange='jumlahkan(1)'>".$optKdbdgt."</select></td></tr>
<tr><td>".$_SESSION['lang']['hkefektif']."</td><td>:</td><td><input type='text' class='myinputtextnumber' disabled style='width:150px;' id='hkEfektif' /></td></tr>
<tr><td>".$_SESSION['lang']['jmlhPersonel']."</td><td>:</td><td><input type='text' class='myinputtextnumber' style='width:150px;' id='jmlh_1' onblur='jumlahkan(1)' onkeypress='return angka_doang(event)' /> ".$_SESSION['lang']['setahun']."</td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='totBiaya' value='0' onkeypress='return false' /></td></tr>
<tr><td colspan=3>

<button class=mybutton id=btlTmbl name=btlTmbl onclick=saveBudget(1)  >".$_SESSION['lang']['save']."</button></td></tr></table><br /><br />

";
$frm[0].="</fieldset>";
CLOSE_BOX();
$optData="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where  kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and tipebudget='TRK'";
$qTHn=mysql_query($sThn) or die(mysql_error($sThn));
while($rThn=mysql_fetch_assoc($qTHn))
{
    $optData.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
echo"<div id='listDatHeader' style='display:block'>";
OPEN_BOX();
echo"<table><tr>
    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='dataHeader()'>".$optData."</select></td>
    <td>".$_SESSION['lang']['kodevhc']." <select id=kdVhcHead style='width:100px' onchange='dataHeader()'>".$optVhc."</select></td>
    
    </tr></table>";
echo"<div id='listDatHeader2'>";
echo"<script>dataHeader()</script></div>";
CLOSE_BOX();
echo"</div>";


echo"<div id='formIsian' style='display:none;'>";
OPEN_BOX();
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataSDM>
		";
$frm[0].="</tbody></table></fieldset>";
$optKdbdgtM="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrgm="select kodebudget,nama from ".$dbname.".bgt_kode where substr(kodebudget,1,1)='M' order by kodebudget asc";
$qOrgm=mysql_query($sOrgm) or die(mysql_error());
while($rOrgm=mysql_fetch_assoc($qOrgm))
{
	$optKdbdgtM.="<option value='".$rOrgm['kodebudget']."'>".$rOrgm['kodebudget']." [".$rOrgm['nama']."]</option>";
}
$frm[1].="<fieldset><legend>".$_SESSION['lang']['material']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetM' style='width:150px;' onchange='getKlmpkbrg()'>".$optKdbdgtM."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td><input type='text' class='myinputtext' id='kdBarang' style='width:150px;' onkeypress='return angka_doang(event)' />&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrg('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrg><button class=mybutton onclick=findBrg()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarang style=overflow=auto;height=380;width=485></div>',event);\">
    <span id='namaBrg'></span></td></tr>
    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_2' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(2)' /> ".$_SESSION['lang']['setahun']."&nbsp;<span id='satuan'></span></td></tr>
<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHarga' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        


<tr><td colspan=3>
<button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='saveBudget(2)'   >".$_SESSION['lang']['save']."</button></td></tr></table><br />

<input type=hidden id=prosesBr name=prosesBr value=insert_baru >
";
//$frm[0].="</fieldset>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['kodebarang']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataBrg>
		";
$frm[1].="</tbody></table></fieldset>";
$optKdbdgt_S="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrgs="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%SERVICE%' order by nama asc";
//echo $sOrgs;
$qOrgs=mysql_query($sOrgs) or die(mysql_error());
while($rOrgs=mysql_fetch_assoc($qOrgs))
{
	$optKdbdgt_S.="<option value='".$rOrgs['kodebudget']."'>".$rOrgs['nama']."</option>";
}

$frm[2].="<fieldset><legend>".$_SESSION['lang']['service']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetS' style='width:150px;'>".$optKdbdgt_S."</select></td></tr>
<tr><td>".$_SESSION['lang']['kdWorks']."</td><td>:</td><td><select id='kdWorkshop' style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
    <tr><td>".$_SESSION['lang']['jmThn']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_3' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(3)' /> ".$_SESSION['lang']['setahun']."</td></tr>
<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHargaJam' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        


<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(3)>".$_SESSION['lang']['save']."</button>
<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />
</td></tr>
</table>";

$frm[2].="</fieldset>";
$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataSrvc>
		";
$frm[2].="</tbody></table></fieldset>";
$optKdbdgt_B="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrgB="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%TRANSIT%' order by nama asc";
//echo $sOrgs;
$qOrgB=mysql_query($sOrgB) or die(mysql_error());
while($rOrgB=mysql_fetch_assoc($qOrgB))
{
	$optKdbdgt_B.="<option value='".$rOrgB['kodebudget']."'>".$rOrgB['nama']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJns="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
$frm[3].="<fieldset><legend>".$_SESSION['lang']['biayalain']."</legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetB' style='width:150px;'>".$optKdbdgt_B."</select></td></tr>
<tr><td>".$_SESSION['lang']['jenisbiaya']."</td><td>:</td><td><select id='noAkun' style='width:150px;'>".$optAkun."</select></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totBiayaB' style='width:150px;' onkeypress='return angka_doang(event)' value='0' /> ".$_SESSION['lang']['setahun']."</td></tr>


<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(4) >".$_SESSION['lang']['save']."</button>
<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />
</td></tr>
</table>";

$frm[3].="</fieldset>";
$frm[3].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
            <td>".$_SESSION['lang']['namaakun']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataLain>
		";
$frm[3].="</tbody></table></fieldset>";
$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$frm[4].="<fieldset><legend>".$_SESSION['lang']['tutup']."</legend>
    <div><table><tr><td>".$_SESSION['lang']['budgetyear']."</td><td><select id='thnBudgetTutup' style='width:150px'>".$optThnTtp."</select></td></tr>";
$frm[4].="<tr><td colspan=2 align=center><button class=\"mybutton\"  id=\"saveData\" onclick='closeBudget()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
$frm[4].="</div></fieldset>";



//========================
$hfrm[0]=$_SESSION['lang']['sdm'];
$hfrm[1]=$_SESSION['lang']['material'];
$hfrm[2]=$_SESSION['lang']['service'];
$hfrm[3]=$_SESSION['lang']['biayalain'];
$hfrm[4]=$_SESSION['lang']['tutup'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>


<?php
CLOSE_BOX();
echo"</div>";
echo close_body();
?>
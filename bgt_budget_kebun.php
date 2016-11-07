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
<script type="text/javascript" src="js/bgt_budget_kebun.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAfdeling=$optBlok;
$optKdbdgt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%SDM%' order by nama asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optKdbdgt.="<option value=".$rOrg2['kodebudget'].">".$rOrg2['nama']."</option>";
}

$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok='".$rStatus['statusblok']."'  order by kodekegiatan asc";
$sKeg="select distinct kodekegiatan,namakegiatan,kelompok from ".$dbname.".setup_kegiatan where  kelompok in ('PNN','TBM','TM','BBT','TB')  order by kodekegiatan asc";
$qKeg=mysql_query($sKeg) or die(mysql_error());
while($rKeg=mysql_fetch_assoc($qKeg))
{
    if($kegId!='')
    {
        $optKeg.="<option value=".$rKeg['kodekegiatan']." ".($rKeg['kodekegiatan']==$kegId?'selected':'').">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
    }
    else
    {
        $optKeg.="<option value=".$rKeg['kodekegiatan'].">".$rKeg['kodekegiatan']." [".$rKeg['namakegiatan']."][".$rKeg['kelompok']."]</option>";
    }
}

OPEN_BOX('',"<b>".$_SESSION['lang']['anggaran']." ".$_SESSION['lang']['kebun']."</b>");
echo"<br /><br /><fieldset style='float:left;'><legend>".$_SESSION['lang']['entryForm']."</legend> <table border=0 cellpadding=1 cellspacing=1>";
echo"<tr><td>".$_SESSION['lang']['budgetyear']."</td><td><input type='text' class='myinputtextnumber' id='thnBudget' style='width:150px;' maxlength='4' onkeypress='return angka_doang(event)' onblur='getKodeblok(0,0,0)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['tipe']."</td><td><input type='text' class='myinputtext' disabled value='ESTATE' id='tipeBudget' style=width:150px; /></td></tr>";
//echo"<tr><td>".$_SESSION['lang']['kodeblok']."</td><td><select style='width:150px;' id='kdBlok' onchange='getKegiatan(0,0)'>".$optBlok."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kodeblok']."</td><td><select style='width:150px;' id='kdBlok' onchange=isiLuas(this)>".$optBlok."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['kegiatan']."</td><td><select style='width:150px;' id='kegId' onchange='getSatuan()'>".$optKeg."</select></td></tr>";
echo"<tr><td>".$_SESSION['lang']['noakun']."</td><td><input type='text' class='myinputtextnumber' id='noAkun' disabled style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['fisik']."</td><td><input type='text' class='myinputtextnumber' id='volKeg' style='width:150px;' onkeypress='return angka_doang(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['satuan']."</td><td><input type='text' class='myinputtext' id='satKeg' style='width:150px;' onkeypress='return tanpa_kutip(event)' /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td><td><input type='text' class='myinputtextnumber' id='rotThn' style='width:150px;' onkeypress='return tanpa_kutip(event)' value='1' /></td></tr>";
echo"<tr><td colspan='2'><button class=\"mybutton\"  id=\"saveData\" onclick='saveData()'>".$_SESSION['lang']['save']."</button><button  class=\"mybutton\"  id=\"newData\" onclick='newData()'>".$_SESSION['lang']['baru']."</button></td></tr>";
echo"</table></fieldset>";
$optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

echo"<fieldset  style='float:left'><legend>".$_SESSION['lang']['tutup']."</legend>
    <div><table><tr><td>".$_SESSION['lang']['budgetyear']."</td><td><select id='thnBudgetTutup' style='width:150px'>".$optThnTtp."</select></td></tr>";
echo"<tr><td colspan=2 align=center><button class=\"mybutton\"  id=\"saveData\" onclick='closeBudget()'>".$_SESSION['lang']['tutup']."</button></td></tr></table>";
echo"</div></fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['sdm']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudget' style='width:150px;' onchange='jumlahkan(1)'>".$optKdbdgt."</select><input type='hidden' class='myinputtextnumber'  style='width:150px;' id='hkEfektif' /></td></tr>
<tr><td>".$_SESSION['lang']['jhk']."</td><td>:</td><td><input type='text' class='myinputtextnumber' style='width:150px;' id='jmlh_1' onblur='jumlahkan(1)' onkeypress='return angka_doang(event)' value='0' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='totBiaya' value='0' onkeypress='return false' /> ".$_SESSION['lang']['setahun']."</td></tr>
<tr><td colspan=3>

<button class=mybutton id=btlTmbl name=btlTmbl onclick=saveBudget(1)  >".$_SESSION['lang']['save']."</button></td></tr></table><br /><br />

";
$frm[0].="</fieldset>";
CLOSE_BOX();

echo"<div id='listDatHeader' style='display:block'>";
OPEN_BOX();
$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
$sThn="select distinct tahunbudget from ".$dbname.".bgt_budget where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tipebudget='ESTATE' and kodebudget!='UMUM' order by tahunbudget desc";
$qThn=mysql_query($sThn) or die(mysql_error($conn));
while($rThn=mysql_fetch_assoc($qThn))
{
    $optTahunBudgetHeader.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
}
$optBlok="<option value=''>".$_SESSION['lang']['all']."</option>";
$sBlok="select distinct kodeblok from ".$dbname.".bgt_blok where kodeblok like '".$_SESSION['empl']['lokasitugas']."%'order by kodeblok asc";
$qBlok=mysql_query($sBlok) or die(mysql_error());
while($rBlok=mysql_fetch_assoc($qBlok))
{
    $optBlok.="<option value='".$rBlok['kodeblok']."'>".$rBlok['kodeblok']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['all']."</option>";
$sAkun="select distinct a.noakun,b.namaakun from ".$dbname.".bgt_budget a
        left join ".$dbname.".keu_5akun b on a.noakun=b.noakun
        where tipebudget='ESTATE' and kodebudget!='UMUM' order by noakun asc";
$qAkun=mysql_query($sAkun) or die(mysql_error($sAkun));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['noakun']."-".$rAkun['namaakun']."</option>";
}
    
echo"<div><table><tr><td>".$_SESSION['lang']['budgetyear'].": <select id='thnbudgetHeader' style='width:150px;' onchange='ubah_list()'>".$optTahunBudgetHeader."</select></td>
    <td>".$_SESSION['lang']['blok'].":<select id=kdBlokCari style='width:150px;' onchange='ubah_list()'>".$optBlok."</select></td><td>".$_SESSION['lang']['noakun'].":<select id=noakunCari style='width:150px;' onchange='ubah_list()'>".$optAkun."</select></td></tr></table></div>";
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
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
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
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataBrg>
		";
$frm[1].="</tbody></table></fieldset>";
//$optKdbdgtL="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrgm="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget='TOOL' order by kodebudget asc";
$qOrgm=mysql_query($sOrgm) or die(mysql_error());
while($rOrgm=mysql_fetch_assoc($qOrgm))
{
	$optKdbdgtL.="<option value='".$rOrgm['kodebudget']."'>".$rOrgm['kodebudget']." [".$rOrgm['nama']."]</option>";
}
$frm[2].="<fieldset><legend>".$_SESSION['lang']['peralatan']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetL' style='width:150px;' disabled>".$optKdbdgtL."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodebarang']."</td><td>:</td><td><input type='text' class='myinputtext' id='kdBarangL' style='width:150px;' onkeypress='return angka_doang(event)' />&nbsp;<img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."' onclick=\"searchBrgL('".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."','<fieldset><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['namabarang']."</legend>".$_SESSION['lang']['find']."&nbsp;<input type=text class=myinputtext id=nmBrgL><button class=mybutton onclick=findBrgL()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerBarangL style=overflow=auto;height=380;width=485></div>',event);\">
    <span id='namaBrgL'></span></td></tr>
    <tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlh_3' style='width:150px;' onkeypress='return angka_doang(event)' onblur='jumlahkan(3)' /> ".$_SESSION['lang']['setahun']."&nbsp;<span id='satuanL'></span></td></tr>
<tr><td>".$_SESSION['lang']['totalharga']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totHargaL' style='width:150px;' onkeypress='return false'  value='0' /></td></tr>        


<tr><td colspan=3>
<button class=mybutton id=btlTmbl2 name=btlTmbl2 onclick='saveBudget(3)'   >".$_SESSION['lang']['save']."</button></td></tr></table><br />

<input type=hidden id=prosesBr name=prosesBr value=insert_baru >
";
//$frm[0].="</fieldset>";

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
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataTool>
		";
$frm[2].="</tbody></table></fieldset>";

$sOrgB="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%KONTRAK%' order by nama asc";
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
$frm[3].="<fieldset><legend>".$_SESSION['lang']['kontrak']."</legend>";
$frm[3].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetK' style='width:150px;' disabled>".$optKdbdgt_B."</select></td></tr>
<tr><td>".$_SESSION['lang']['volume']."</td><td>:</td><td><input type='text' id='volKontrak' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><input type='text' id='satKontrak' class='myinputtextnumber' onkeypress='return tanpa_kutip(event)' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totBiayaK' style='width:150px;' onkeypress='return angka_doang(event)' value='0' /> ".$_SESSION['lang']['setahun']."</td></tr>


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
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
             <td>".$_SESSION['lang']['rotasi']."/".$_SESSION['lang']['tahun']."</td>
            <td>".$_SESSION['lang']['volume']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataLain>
		";
$frm[3].="</tbody></table></fieldset>";


$sOrgv="select kodebudget,nama from ".$dbname.".bgt_kode where kodebudget like '%VHC%' order by nama asc";
//echo $sOrgs;
$qOrgv=mysql_query($sOrgv) or die(mysql_error());
while($rOrgv=mysql_fetch_assoc($qOrgv))
{
	$optKdbdgt_V.="<option value='".$rOrgv['kodebudget']."'>".$rOrgv['nama']."</option>";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sJns="select noakun,namaakun from ".$dbname.".keu_5akun where detail=1 and tipeakun='BIAYA' order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
$optVhc="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$frm[4].="<fieldset><legend>".$_SESSION['lang']['kndran']."</legend>";
$frm[4].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>
<select id='kdBudgetV' style='width:150px;' disabled>".$optKdbdgt_V."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><select id='kdVhc' style='width:150px;' onchange='ambil_biaya()'>".$optVhc."</select></td></tr>
<tr><td>".$_SESSION['lang']['jmlhJam']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='jmlhJam' style='width:150px;' onkeypress='return angka_doang(event)'   onblur='ambil_biaya()' /> ".$_SESSION['lang']['setahun']."</td></tr>
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><input type='text' id='satVhc' class='myinputtextnumber' disabled value='HM/KM' style='width:150px;' /></td></tr>
<tr><td>".$_SESSION['lang']['totalbiaya']."</td><td>:</td><td><input type='text' class='myinputtextnumber' id='totBiayaKend' style='width:150px;' onkeypress='return false' value=0 /></td></tr>        


<tr><td colspan=3>
<button class=mybutton onclick=saveBudget(5) >".$_SESSION['lang']['save']."</button>

</td></tr>
</table>";

$frm[4].="</fieldset>";
$frm[4].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>         
            <td>No</td>
            <td>".$_SESSION['lang']['index']."</td>
            <td>".$_SESSION['lang']['budgetyear']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tipeBudget']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['noakun']."</td>
           <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['rp']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['satuan']."</td>
            <td>Action</td>
            </tr>
            </thead><tbody id=containDataKend>
		";
$frm[4].="</tbody></table></fieldset>";

$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
$frm[5].="<fieldset><legend>Sebaran</legend>
    <table><tr>";
    foreach($arrBln as $brsBulan =>$listBln)
        {
            $frm[5].="<td>".$listBln."</td>";
        }
       
$sNamaAkun58="select distinct noakun,namaakun  from ".$dbname.".keu_5akun order by namaakun asc";
$qNamaAkun58=mysql_query($sNamaAkun58) or die(mysql_error());
while($rNamaAkun58=  mysql_fetch_assoc($qNamaAkun58))
{
    $namaAkun58[$rNamaAkun58['noakun']]=$rNamaAkun58['namaakun'];
}


$optNoakunData58="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOptNoakun58="select distinct noakun from ".$dbname.".bgt_budget where tipebudget='ESTATE' and kodebudget!='UMUM' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by noakun asc";
$qOptNoakun58=mysql_query($sOptNoakun58) or die(mysql_error($sOptNoakun58));
while($rOptNoakun58=mysql_fetch_assoc($qOptNoakun58))
{
    $optNoakunData58.="<option value='".$rOptNoakun58['noakun']."'>".$rOptNoakun58['noakun']."-".$namaAkun58[$rOptNoakun58['noakun']]."</option>";
}
$sAfd="select distinct kodeorganisasi from ".$dbname.".organisasi where tipe='AFDELING' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
$qAfd=mysql_query($sAfd) or die(mysql_error($conn));
while($rAfd=  mysql_fetch_assoc($qAfd))
{
    $optAfdeling.="<option value='".$rAfd['kodeorganisasi']."'>".$rAfd['kodeorganisasi']."</option>";
}
$frm[5].="<td></td></tr>
    <tr>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss1 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss2 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss3 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss4 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss5 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss6 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss7 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss8 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss9 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss10 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss11 value=1></td>
    <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss12 value=1></td>
    <td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td>
    </tr>
    </table>  ";


    $frm[5].="<table><tr class=rowcontent>
              <td>".$_SESSION['lang']['afdeling']."</td><td><select id=AfdSebaran onchange='loadDetailTotal()'>".$optAfdeling."</select></td>
              <td>".$_SESSION['lang']['kodeblok']."</td><td><select id=kdblokSebaran onchange='loadDetailTotal()'>".$optBlok."</select></td>
              <td>".$_SESSION['lang']['noakun']."</td><td><select id=kdNoakunData onchange='loadDetailTotal()'>".$optNoakunData58."</select></td>
              <td>Goto Page</td><td id='pagingDrop'>&nbsp;<select id='pageSebaran' onchange='loadDetailTotal()'><option value=''></option></select><span id=awalPageSebaran></span> &nbsp;".$_SESSION['lang']['dari']." &nbsp;<span id=totalPageSebaran></span> <button class=mybutton id=refresh name=refresh onclick=loadDetailTotal()>Refresh List</button></td>
              </tr>
			  </table>";
    
   $frm[5].="<div id='detailDataSebaran'style=overflow:auto;width:1030px;height:300px;>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>
            <thead>
            <tr class=rowheader>
            <td></td>               
            <td>No</td>
            <td>".$_SESSION['lang']['kodeblok']."</td>
            <td>".$_SESSION['lang']['kodeanggaran']."</td>
            <td>".$_SESSION['lang']['namakegiatan']."</td>
            <td>".$_SESSION['lang']['namabarang']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['total']."</td>";
foreach($arrBln as $brsBulan =>$listBln)
{
    $frm[5].="<td>".$listBln." Rp</td>";
}
        
     $frm[5].="<td>Action</td>
            </tr>
            </thead><tbody id=containDataTotal>
		";
$frm[5].="</tbody></table></div></fieldset>";


//========================
$hfrm[0]=$_SESSION['lang']['sdm'];
$hfrm[1]=$_SESSION['lang']['material'];
$hfrm[2]=$_SESSION['lang']['peralatan'];
$hfrm[3]=$_SESSION['lang']['kontrak'];
$hfrm[4]=$_SESSION['lang']['kndran'];
$hfrm[5]="Sebaran";
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,1100);
//===============================================	
?>


<?php
CLOSE_BOX();
echo"</div>";
echo close_body();
?>
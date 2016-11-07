<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
include_once('lib/zLib.php');

echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';
$frm[3]='';
$frm[4]='';
$frm[5]='';


?>
<script>
pilh=" <?php echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script>plh="<?php echo $_SESSION['lang']['pilihdata'];?>";</script>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/bibit_keluar_masuk.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKdorg2=$optKdorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodeorg from ".$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg asc";
 //echo $sOrg2;
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optKdorg.="<option value=".$rOrg2['kodeorg'].">".$optNmOrg[$rOrg2['kodeorg']]."</option>";
}
$sOrg22="select kodeorg from ".$dbname.".setup_blok where  statusblok='BBT' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%MN%' order by kodeorg asc";
 //echo $sOrg2;
$qOrg22=mysql_query($sOrg22) or die(mysql_error());
while($rOrg22=mysql_fetch_assoc($qOrg22))
{
	$optKdorg2.="<option value=".$rOrg22['kodeorg'].">".$optNmOrg[$rOrg22['kodeorg']]."</option>";
}
$optKdorg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg3="select kodeorg from ".$dbname.".setup_blok where  statusblok='BBT'  order by kodeorg asc";
 //echo $sOrg2;
$qOrg3=mysql_query($sOrg3) or die(mysql_error());
while($rOrg3=mysql_fetch_assoc($qOrg3))
{
	$optKdorg2.="<option value=".$rOrg3['kodeorg'].">".$optNmOrg[$rOrg3['kodeorg']]."</option>";
}
$optJnsBbt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sBbt="select distinct  jenisbibit from  ".$dbname.".setup_jenisbibit order by jenisbibit";
$qBbt=mysql_query($sBbt) or die(mysql_error($sBbt));
while($rBbt=mysql_fetch_assoc($qBbt))
{
    $optJnsBbt.="<option value='".$rBbt['jenisbibit']."'>".$rBbt['jenisbibit']."</option>";
}
$optSup="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optStatPos=$optSup;
$arrStata=array("0"=>"Not Posted","1"=>"Posted");
foreach($arrStata as $lstStat=>$dtstat)
{
    $optStatPos.="<option value='".$lstStat."'>".$dtstat."</option>";
}

$sSupplier="select distinct supplierid,namasupplier from ".$dbname.".log_5supplier where supplierid like 'S%' order by namasupplier asc";
$qSupplier=mysql_query($sSupplier) or die(mysql_error($sSupplier));
while($rSupplier=mysql_fetch_assoc($qSupplier))
{
    $optSup.="<option value='".$rSupplier['supplierid']."'>".$rSupplier['namasupplier']."</option>";
}

$tglHrini=date("Ymd");

echo"<div id='formIsian' style='display:block;'>";
OPEN_BOX('',"<b>".$_SESSION['lang']['masukkeluarbibit']."</b>");
$frm[0].="<input type='hidden' id='proses1' value='saveTab1' /><input type='hidden' id='oldJnsbibit'  /><fieldset style='width:350px;float:left'><legend>".$_SESSION['lang']['tnmbibit']."</legend>";
if($_SESSION['language']=='EN'){
    $frm[0].="Including receipt of seeds directly in the Main Nursery (from other sources)<br>";
}else{
    $frm[0].="Termasuk penerimaan bibit langsung ke MN dari tempat lain<br>";
}
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksi' value='TMB'  disabled /></td></tr>
<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><input type='text' class='myinputtext' style='width:150px;' id='batch'  disabled /></td></tr>
<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id=kodeorgBibitan style=width:150px>".$optKdorg."</select></td></tr>
<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhBibitan' onkeypress='return angka_doang(event)' value='0' />&nbsp;Biji</td></tr>
<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ket' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['tgltanam']."</td><td>:</td><td><input type=text class=myinputtext id=tglTnm style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>";
$frm[0].="<tr><td colspan=3>&nbsp;</td></tr></table>";
$optKebun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKebun="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qKebun=mysql_query($sKebun) or die(mysql_error());
while($rKebun=mysql_fetch_assoc($qKebun))
{
    $optKebun.="<option value='".$rKebun['kodeorganisasi']."'>".$rKebun['namaorganisasi']."</option>";
}
$frm[0].="</fieldset>";
$frm[0].="<fieldset style='width:300px;'><legend>".$_SESSION['lang']['sumber']."</legend><table cellspacing=1 border=0>";
$frm[0].="<tr><td>".$_SESSION['lang']['jenisbibit']."</td><td>:</td><td><select id=jnsBibitan style=width:150px>".$optJnsBbt."</select></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['supplier']."</td><td>:</td><td><select id=supplier_id style=width:150px>".$optSup."</select><img src=\"images/search.png\" class=\"resicon\" title='".$_SESSION['lang']['findRkn']."' onclick=\"searchSupplier('".$_SESSION['lang']['findRkn']."','<fieldset><legend>".$_SESSION['lang']['find']."</legend>". $_SESSION['lang']['namasupplier']."&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()>".$_SESSION['lang']['find']."</button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);\"></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['tglproduksi']."</td><td>:</td><td><input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['nodo']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='nodo' onkeypress='return tanpa_kutip(event)' /></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['jumlah']." PD DO</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlh' onkeypress='return angka_doang(event)' value='0' />&nbsp;Biji</td></tr>";
$frm[0].="<tr><td> ".$_SESSION['lang']['diterima']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhTrima' onkeypress='return angka_doang(event)' value='0' /></td></tr>";
$frm[0].="<tr><td>".$_SESSION['lang']['afkirbibit']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='afkirKcmbh' onkeypress='return angka_doang(event)' value='0' /></td></tr>";
$frm[0].="</table></fieldset>";
$frm[0].="<div style=float:left;><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(1)  >".$_SESSION['lang']['save']."</button><button class=mybutton id=canbtlTmbl name=canbtlTmbl onclick=cancelData1()  >".$_SESSION['lang']['cancel']."</button></div>";
$frm[0].="<div style=clear:both;>&nbsp;</div>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type='text' class='myinputtext' id='tglCari2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['batch']."</td>
        <td><input type='text' class='myinputtext' id='batchCari2'  style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['status']."</td>
        <td><select id=statCari2  style=\"width:150px;\">".$optStatPos."</select></td>
    </tr>
    </table>
    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData1()  >".$_SESSION['lang']['find']."</button>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>No</td>
            <td>".$_SESSION['lang']['kodetransaksi']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            
            <td>".$_SESSION['lang']['tgltanam']."</td>
            <td>".$_SESSION['lang']['jenisbibit']."</td>
             <td>".$_SESSION['lang']['supplier']."</td>
            <td>".$_SESSION['lang']['tglproduksi']."</td>
            <td colspan=2>Action</td>
            </tr>
            </thead><tbody id=containData1><script>loadData1()</script> 
		";
$frm[0].="</tbody></table></fieldset>";

#################################################

$optbatch="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$xbatch="select distinct batch from ".$dbname.".bibitan_mutasi where kodeorg like '".$_SESSION['empl']['lokasitugas']."%' order by batch desc";
$ybatch=mysql_query($xbatch) or die(mysql_error($xbatch));
while($zbatch=mysql_fetch_assoc($ybatch))
{
    $optbatch.="<option value='".$zbatch['batch']."'>".$zbatch['batch']."</option>";
}

////
$nott="Termasuk Pemindahan dari PN ke PN tempat lain, maupun ke MN tempat lain";
if($_SESSION['language']=='EN'){
    $nott="Include seed movement from Pre Nursery to other Pre Nursery, or from Main Nursery to other Nursery";
}
$frm[3].="<input type='hidden' id='proses2' value='saveTab2' /><fieldset style=width:650px;><legend>Mutasi / ".$_SESSION['lang']['transplatingbibit']."</legend>
   <fieldset style='text-align:left;width:300px;float:right;'>
				   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>
				   <p>".$nott." 
				   </p>
				   </fieldset>	
   
<table cellspacing=1 border=0>

<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksiTp' value='TPB'  disabled /></td></tr>

<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchTp' style=width:150px onchange='getKodeorg()'>".$optbatch."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id=kodeOrgTp style=width:150px onchange='cekSamaGak()'>".$optKdorg."</select></td></tr>

<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id=tglTp style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>



<tr><td>".$_SESSION['lang']['tujuan']."</td><td>:</td><td><select id=kodeOrgTjnTp style=width:150px onchange='cekSamaGak()'>".$optKdorg2."</select></td></tr>

<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhTpBbtn' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>

<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketTp' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";

$frm[3].="<tr><td colspan=3><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(2)  >".$_SESSION['lang']['save']."</button><button class=mybutton id=canbtlTmbl name=canbtlTmbl onclick=cancelData2()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>
";

$frm[3].="<fieldset ><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type='text' class='myinputtext' id='tglCari3' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['batch']."</td>
        <td><input type='text' class='myinputtext' id='batchCari3'  style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['status']."</td>
        <td><select id=statCari3  style=\"width:150px;\">".$optStatPos."</select></td>
    </tr>
    </table>
    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData2()  >".$_SESSION['lang']['find']."</button>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['kodetransaksi']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['tujuan']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['keterangan']."</td>
            <td colspan=2>".$_SESSION['lang']['action']."</td>
            </tr>
            </thead><tbody id=containData2>
		";
$frm[3].="</tbody></table></fieldset>";


############################################################################




$frm[2].="<input type='hidden' id='proses3' value='saveTab3' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['afkirbibit']."</legend>
<table cellspacing=1 border=0>

<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransAfk' value='APB'  disabled /></td></tr>

<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchAfk' style='width:150px' onchange='getKodeorg2()'>".$optbatch."</select></td></tr>

<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglAfkirBibit' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>

<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgAfk' style=width:150px>".$optKdorg."</select></td></tr>

<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhAfk' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>

<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketAfk' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";

$frm[2].="<tr><td colspan=3><button class=mybutton   name=btlTmbl onclick=saveData(3)  >".$_SESSION['lang']['save']."</button><button class=mybutton   name=canbtlTmbl onclick=cancelData3()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>
";

$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type='text' class='myinputtext' id='tglCari4' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['batch']."</td>
        <td><input type='text' class='myinputtext' id='batchCari4'  style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['status']."</td>
        <td><select id=statCari4  style=\"width:150px;\">".$optStatPos."</select></td>
    </tr>
    </table>
    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData3()  >".$_SESSION['lang']['find']."</button>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['kodetransaksi']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
	    <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['keterangan']."</td>
            <td colspan=2>".$_SESSION['lang']['action']."</td>
            </tr>
            </thead><tbody id=containData3>
		";
$frm[2].="</tbody></table></fieldset>";


###############################################################################################

###################################################################################################

$frm[1].="<input type='hidden' id='proses5' value='saveTab5' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['doubletoon']."</legend>
<table cellspacing=1 border=0>

<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransaksiDbt' value='DBT'  disabled /></td></tr>

<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchDbt' style='width:150px' onchange='getKodeorg3()'>".$optbatch."</select></td></tr>

<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglDbt' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>

<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgDbt' style=width:150px>".$optKdorg."</select></td></tr>

<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhDbt' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>

<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketDbt' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";

$frm[1].="<tr><td colspan=3><button class=mybutton id=btlTmbl name=btlTmbl onclick=saveData(5)  >".$_SESSION['lang']['save']."</button><button class=mybutton id='' name=canbtlTmbl onclick=cancelData5()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>
";

$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type='text' class='myinputtext' id='tglCari5' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['batch']."</td>
        <td><input type='text' class='myinputtext' id='batchCari5'  style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['status']."</td>
        <td><select id=statCari5  style=\"width:150px;\">".$optStatPos."</select></td>
    </tr>
    </table>
    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData5()  >".$_SESSION['lang']['find']."</button>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['kodetransaksi']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
			<td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['keterangan']."</td>
            <td colspan=2>".$_SESSION['lang']['action']."</td>
            </tr>
            </thead><tbody id=containData5>
		";
$frm[1].="</tbody></table></fieldset>";

###################################################################################################
$optKegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arragama=getEnum($dbname,'bibitan_mutasi','jenistanam');
foreach($arragama as $kei=>$fal)
{
        $optKegiatan.="<option value='".$kei."'>".$fal."</option>";
}  
$arr=array("External","Internal","Afliasi");
$optintex="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arr as $isi =>$eia)
{
	$optintex.="<option value=".$isi." >".$eia."</option>";
}
 $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
 $optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
 $optKaryawan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
 $sKaryawan="select distinct karyawanid,namakaryawan,nik ,subbagian from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' 
 			and kodejabatan in ('32','95') and karyawanid!='".$_SESSION['standard']['userid']."'";
 $qKaryawan=mysql_query($sKaryawan) or die(mysql_error($sKaryawan));
 while($rKaryawan=mysql_fetch_assoc($qKaryawan))
 {
     $optKaryawan.="<option value='".$rKaryawan['karyawanid']."'>".$rKaryawan['nik']." ".$rKaryawan['namakaryawan']." [ ".$rKaryawan['subbagian']." ]</option>";
 }
$frm[4].="<input type='hidden' id='proses7' value='saveTab7' /><fieldset  style=width:650px;><legend>".$_SESSION['lang']['pengirimanBibit']."</legend>
<table cellspacing=1 border=0>

<tr><td>".$_SESSION['lang']['kodetransaksi']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdTransPnb' value='PNB'  disabled /></td></tr>
<tr><td>".$_SESSION['lang']['batch']."</td><td>:</td><td><select id='batchPnb' style=width:150px onchange='getKodeorgN()'>".$optbatch."</select></td></tr>
<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td><input type=text class=myinputtext id='tglPnb' style='width:150px;' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>
<tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><select id='kdOrgPnb' style=width:150px>".$optKdorg2."</select></td></tr>
<tr><td>".$_SESSION['lang']['jumlah']."</td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlhPnb' onkeypress='return angka_doang(event)' value='0' />&nbsp;Seed(Bibit)</td></tr>
<tr><td>".$_SESSION['lang']['nospb']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='ketPnb' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>";


$frm[4].="
<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='kdvhc' onkeypress='return tanpa_kutip(event)' maxlength=8 /></td></tr>
<tr><td>Rit </td><td>:</td><td><input type='text' class='myinputtextnumber'  style='width:150px;' id='jmlRit' onkeypress='return angka_doang(event)' maxlength=20 /></td></tr>
<tr><td>".$_SESSION['lang']['sopir']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='nmSupir' onkeypress='return tanpa_kutip(event)' maxlength=20 /></td></tr>

<tr><td>".$_SESSION['lang']['Intex']."</td><td>:</td><td><select id='intexDt' style=width:150px onchange='getCustdata(0,0,0)'>".$optintex."</select></td></tr>
<tr><td>".$_SESSION['lang']['customerlist']."</td><td>:</td><td><select id='custId' style=width:150px onchange='getKodeorgBlok()'>".$optKode."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodeblok']."</td><td>:</td><td><select id='kdAfdeling' style=width:150px disabled >".$optAfd."</select></td></tr>   
<tr><td>".$_SESSION['lang']['lokasi']." ".$_SESSION['lang']['detailPengiriman']."</td><td>:</td><td><input type='text' class='myinputtext'  style='width:150px;' id='detPeng' onkeypress='return tanpa_kutip(event)' maxlength=45 /></td></tr>
<tr><td>".$_SESSION['lang']['kegiatan']."</td><td>:</td><td><select id='kegId' style=width:150px>".$optKegiatan."</select></td></tr>
<tr><td>".$_SESSION['lang']['asisten']."</td><td>:</td><td><select id='assistenPnb' style=width:150px>".$optKaryawan."</select></td></tr>";



$frm[4].="<tr><td colspan=3><button class=mybutton id='' name=btlTmbl onclick=saveData(7)  >".$_SESSION['lang']['save']."</button><button class=mybutton id='' name=canbtlTmbl onclick=cancelData7()  >".$_SESSION['lang']['cancel']."</button></td></tr></table><br /></fieldset>
";

$frm[4].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
    <table cellpadding=1 cellspacing=1 border=0>
    <tr>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type='text' class='myinputtext' id='tglCari7' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['batch']."</td>
        <td><input type='text' class='myinputtext' id='batchCari7'  style=\"width:150px;\" /></td>
        <td>".$_SESSION['lang']['status']."</td>
        <td><select id=statCari7  style=\"width:150px;\">".$optStatPos."</select></td>
    </tr>
    </table>
    <button class=mybutton id=btlTmbl name=btlTmbl onclick=loadData7()  >".$_SESSION['lang']['find']."</button>
    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['kodetransaksi']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['tanggal']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
            <td>".$_SESSION['lang']['jumlah']."</td>
            <td>".$_SESSION['lang']['kegiatan']."</td>
            <td>".$_SESSION['lang']['nospb']."</td>
            <td>".$_SESSION['lang']['kodevhc']."</td>
            <td>".$_SESSION['lang']['customerlist']."</td>
            <td>".$_SESSION['lang']['kodeblok']."</td>
            <td>".$_SESSION['lang']['asisten']."</td>
            <td colspan=2>".$_SESSION['lang']['action']."</td>
            </tr>
            </thead><tbody id=containData7>
		";
$frm[4].="</tbody></table></fieldset>";


###################################################################################################



$frm[5].="<fieldset  style=width:650px;><legend>".$_SESSION['lang']['stockdetail']."</legend>

    <table cellpadding=1 cellspacing=1 border=0 class=sortable>
            <thead>
            <tr class=rowheader>
            <td>".$_SESSION['lang']['nomor']."</td>
            <td>".$_SESSION['lang']['batch']."</td>
            <td>".$_SESSION['lang']['kodeorg']."</td>
			<td>".$_SESSION['lang']['saldo']."</td>
			<td>".$_SESSION['lang']['supplier']."</td>
            <td>".$_SESSION['lang']['umur']."(".$_SESSION['lang']['bulan'].")</td>
            </tr>
            </thead><tbody id=containDataStock>
		";
$frm[5].="</tbody></table></fieldset>";







###################################################################################################



//========================
$hfrm[0]=$_SESSION['lang']['tnmbibit'];
$hfrm[1]=$_SESSION['lang']['doubletoon'];
$hfrm[2]=$_SESSION['lang']['afkirbibit'];
$hfrm[3]=$_SESSION['lang']['transplatingbibit'];
$hfrm[4]=$_SESSION['lang']['pengirimanBibit'];
$hfrm[5]=$_SESSION['lang']['stockdetail'];
//$hfrm[6]=$_SESSION['lang']['prosesUlang'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,150,1100);
//===============================================	
?>


<?php
CLOSE_BOX();

echo"</div>";

echo close_body();
?>
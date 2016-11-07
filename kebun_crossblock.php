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

?>
<script>
pilh=" <?php echo $_SESSION['lang']['pilihdata'] ?>";
</script>
<script>plh="<?php echo $_SESSION['lang']['pilihdata'];?>";</script>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/kebun_crossblock.js"></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php

//$optkodeorg1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
    where tipe ='kebun'
    order by kodeorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optkodeorg1.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}

//$optperiode1="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select distinct substr(tanggal,1,7) as periode from ".$dbname.". kebun_crossblock_ht 
    order by tanggal desc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optperiode1.="<option value=".$rOrg2['periode'].">".$rOrg2['periode']."</option>";
}

$optkodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi 
    where tipe in ('blok', 'afdeling') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'
    order by tipe desc, kodeorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optkodeorg.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}

$optkaryawan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select karyawanid,namakaryawan from ".$dbname.". datakaryawan
    where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%'
        and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")
        and tipekaryawan<=3
    order by namakaryawan";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optkaryawan.="<option value=".$rOrg2['karyawanid'].">".$rOrg2['namakaryawan']."</option>";
}

$optcek="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optcek.="<option value='0'>Cek</option>";
$optcek.="<option value='1'>Ricek</option>";

$optkelompok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optkelompok.="<option value='APANEN'>Ancak Panen</option>";
$optkelompok.="<option value='MUTUTPH'>Mutu TPH</option>";


$optkegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select id, nama from ".$dbname.".qc_5parameter 
    where tipe ='XBLOK'
    order by nama asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optkegiatan.="<option value=".$rOrg2['id'].">".$rOrg2['nama']."</option>";
}

$arrjabatan=array('Manager','Askep','Asisten','Mandor');
$optjabatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrjabatan as $asjab)
{
    $optjabatan.="<option value='".$asjab."'>".$asjab."</option>";
}

$optqcid="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOrg2="select id, tipe, nama from ".$dbname.".qc_5parameter
    order by tipe, nama
    ";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
    $optqcid.="<option value=".$rOrg2['id'].">".$rOrg2['tipe']." - ".$rOrg2['nama']."</option>";
}

OPEN_BOX('',"<b>Cross Block</b>");
$frm[0].="<fieldset style='width:800px;float:left'><legend>".$_SESSION['lang']['form']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr>
    <td>".$_SESSION['lang']['tanggal']."</td>
    <td>:</td>
    <td><input type='hidden' id='proses0' value='savedata0'/><input type='hidden' id='id' value=''/><input type='text' class='myinputtext' id='tanggal' onmousemove='setCalendar(this.id)' onkeypress='return false;'  
    size='10' maxlength='10' style=\"width:150px;\"/></td>
</tr>
<tr>
    <td>".$_SESSION['lang']['kodeorg']."</td>
    <td>:</td>
    <td><select id=kodeorg style=width:150px>".$optkodeorg."</select></td>
</tr>
<tr>
    <td>".$_SESSION['lang']['jabatan']."</td>
    <td>:</td>
    <td><select id=jabatan style=width:150px>".$optjabatan."</select></td>
</tr>
<tr>
    <td>".$_SESSION['lang']['namakaryawan']."</td>
    <td>:</td>
    <td><select id=karyawan style=width:150px>".$optkaryawan."</select></td>
</tr>
<tr>
    <td>Check/Re-Check</td>
    <td>:</td>
    <td><select id=cek style=width:150px>".$optcek."</select></td>
</tr>
<tr>
    <td style=width:200px;>".$_SESSION['lang']['kelompok']."</td>
    <td>:</td>
    <td><select id=kelompok style=width:150px onchange=openkegiatan()>".$optkelompok."</select></td>
</tr>
<tr>
    <td colspan=3><input type='hidden' id='jumlahkegiatan' value='0'/><div id=container2></div></td>
</tr>
<tr>
    <td>".$_SESSION['lang']['keterangan']."</td>
    <td>:</td>
    <td><input type='text' class='myinputtext' style='width:150px;' id='keterangan' onkeypress='return tanpa_kutip(event)' maxlength=100/></td>
</tr>";
$frm[0].="<tr>
    <td colspan=3>
        <button class=mybutton id=save0 name=save0 onclick=savedata0()>".$_SESSION['lang']['save']."</button>
        <button class=mybutton id=cancel0 name=cancel0 onclick=canceldata0()>".$_SESSION['lang']['cancel']."</button>
    </td></tr>";
$frm[0].="</table></fieldset>";

$frm[0].="<fieldset style=width:800px;><legend>".$_SESSION['lang']['datatersimpan']."</legend>";
$frm[0].="<table cellpadding=1 cellspacing=1 border=0 class=sortable>
    <thead>
        <tr class=rowheader>
        <td>".$_SESSION['lang']['nomor']."</td>
        <td>".$_SESSION['lang']['tanggal']."</td>
        <td>".$_SESSION['lang']['kodeblok']."</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['jabatan']."</td>
        <td>Check/Re-Check</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
        <td colspan=2>".$_SESSION['lang']['action']."</td>
        </tr>
    </thead>
    <tbody id=container0><script>loaddata0()</script>
    ";
$frm[0].="</tbody></table></fieldset>";

$arr="##kodeorg1##periode1";
$frm[1].="<fieldset style='width:800px;float:left'>
<legend>List</legend>
<table cellspacing=\"1\" border=\"0\" >
<tr>
    <td><label>".$_SESSION['lang']['kodeorg']."</label></td>
    <td>:</td>
    <td><select id=\"kodeorg1\" name=\"kodeorg1\" style=\"width:150px\" onchange=hideById('container1')>".$optkodeorg1."</select></td>
</tr>
<tr>
    <td><label>".$_SESSION['lang']['periode']."</label></td>
    <td>:</td>
    <td><select id=\"periode1\" name=\"periode1\" style=\"width:150px\" onchange=hideById('container1')>".$optperiode1."</select></td>
</tr>
<tr>
    <td colspan=\"3\">
        <button onclick=\"zPreview('kebun_slave_crossblock','".$arr."','container1'); showById('container1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'kebun_slave_crossblock.php','".$arr."'); \" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>
    </td>
</tr>
</table>
</fieldset>";
$frm[1].="<fieldset style=width:800px;><legend>Print Area</legend>
<div id='container1'>
</div></fieldset>";

//========================
$hfrm[0]="Form";
$hfrm[1]="List";
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,150,1240);
//========================
?>

<?php
CLOSE_BOX();
echo"</div>";
echo close_body();
?>
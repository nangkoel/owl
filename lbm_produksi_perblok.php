<?php 
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');

$arr="##unit##periode##judul##afdId"; 
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

//echo'<script type="text/javascript" src="js/lbm_karyawan_perumahan.js"></script>'; // taken from bgt_laporan_kapital

$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optperiode=$optunit;

$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' and tipe='KEBUN' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optunit.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
 
$sOrg="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optperiode.="<option value=".$rOrg['periode'].">".$rOrg['periode']."</option>";
}
$optNmKeg=makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optNmKlmpk=makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,kelompok');
$optafd="<option value=''>".$_SESSION['lang']['all']."</option>";
$optkeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrData=array('1260602'=>'TBM','1260701'=>'TBM','1260604'=>'TBM','6210102'=>'TM','6210104'=>'TM','6210201'=>'TM');
foreach($arrData as $lsKeg=>$dtunt)
{
    $optkeg.="<option value='".$lsKeg."'>".$lsKeg."-".$optNmKeg[$lsKeg]."-".$dtunt."</option>";    
}
echo"
<table cellspacing=\"1\" border=\"0\" >
    <tr><td colspan=2>".$judul."</td></tr>
    <tr><td><label>".$_SESSION['lang']['periode']."</label></td><td><select id='periode' style=\"width:200px;\">".$optperiode."</select></td></tr>
    <tr><td><label>".$_SESSION['lang']['unit']."</label></td><td><select id='unit' style=\"width:200px;\" onchange=getAfd(this)>".$optunit."</select></td></tr>    
    <tr><td><label>".$_SESSION['lang']['afdeling']."</label></td><td><select id='afdId' style=\"width:200px\">".$optafd."</select></td></tr>
    <tr height=\"20\"><td colspan=\"2\"><input type=hidden id=judul name=judul value='".$judul."'></td></tr>
    <tr><td colspan=\"2\"> 
    <button onclick=\"zPreview('lbm_slave_produksi_perblok','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">".$_SESSION['lang']['preview']."</button>
    <button onclick=\"zExcel(event,'lbm_slave_produksi_perblok.php','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"excel\" id=\"excel\">".$_SESSION['lang']['excel']."</button>    
    <!--<button onclick=\"zPdf('lbm_slave_pemupukan_afd','".$arr."','reportcontainer')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">". $_SESSION['lang']['pdf']."</button>-->
    <!--<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>--></td></tr>
</table>
";
?>
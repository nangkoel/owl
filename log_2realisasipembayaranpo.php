<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>

<?php
//$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

//$optPt1="<option value=''>".$_SESSION['lang']['all']."</option>";
//$optStat1=$optPt1;
//$optStat=$optPt1;
//$optTer1=$optPt1;

//status po1
//$arrDt1=array("0"=>"Pusat","1"=>"Lokal");
//foreach($arrDt1 as $dtlst=>$dtklrm)
//{
//    $optStat1.="<option value='".$dtlst."'>".$dtklrm."</option>";
//}

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPt="<option value=''>".$_SESSION['lang']['all']."</option>";
$optSupplier="<option value=''>".$_SESSION['lang']['all']."</option>";
$optKelompok="<option value=''>".$_SESSION['lang']['all']."</option>";

//semua pt
$sPt="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
    $optPt.="<option value='".$rPt['kodeorganisasi']."'>".$rPt['namaorganisasi']."</option>";
}

//semua kelompok
$sPt="select kode, kelompok from ".$dbname.".log_5klbarang 
    where 1
    order by kode";
$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
    $optKelompok.="<option value='".$rPt['kode']."'>".$rPt['kode']." - ".$rPt['kelompok']."</option>";
}

//semua supplier PO
$sPt="select kodesupplier, namasupplier from ".$dbname.".log_po_vw 
    where 1
    group by kodesupplier
    order by namasupplier";
$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
    $optSupplier.="<option value='".$rPt['kodesupplier']."'>".$rPt['namasupplier']."</option>";
}

//periode akuntansi
$sPeriode="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_poht order by substr(tanggal,1,7) desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal($rPeriode['periode']),1,7)."</option>";
}

////status po
//$arrDt=array("0"=>"Pusat","1"=>"Lokal");
//foreach($arrDt as $dtlst=>$dtklrm)
//{
//    $optStat.="<option value='".$dtlst."'>".$dtklrm."</option>";
//}

////status terima po
//$arrDt=array("0"=>"Belum Selesai","1"=>"Sudah Selesai","2"=>"Dapat Dikirim","3"=>"Diterima Gudang");
//foreach($arrDt as $dtlst=>$dtklrm)
//{
//    $optTer1.="<option value='".$dtlst."'>".$dtklrm."</option>";
//}

$arr="##pt##periode##supplier##kelompok##periode1##namasupplier";
//$arr1="##tgl1##tgl2##status1##pt1##terima1";

//$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";
?>

<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function clear()
{
    document.getElementById('pt').value='';
//    document.getElementById('statId').value='';
    document.getElementById('periode').value='';
}
</script>
<link rel=stylesheet type='text/css' href='style/zTable.css'>
       
<?php

/*
INSERT INTO `bahasa` (`legend`, `ID`, `location`, `idx`, `MY`, `EN`, `TH`) VALUES ('realisasipembayaranpo', 'Realisasi Pembayaran PO', 'purchasing', NULL, 'Realisasi Pembayaran PO', 'PO Payments', 'PO Payments');
*/

$title[0]=$_SESSION['lang']['realisasipembayaranpo'];
//$title[1]='Detail Laporan Status PO';

$frm[0].="<div>
    <fieldset style=\"float: left;\">
    <legend><b>".$_SESSION['lang']['form']."</b></legend>
    <table cellspacing=\"1\" border=\"0\" >
    <tr>
        <td><label>".$_SESSION['lang']['pt']."</label></td>
        <td><select id=\"pt\" name=\"pt\" style=\"width:150px\">".$optPt."</select></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['periode']."</label></td>
        <td><select id=\"periode\" name=\"periode\" style=\"width:70px\">".$optPeriode."</select> - 
            <select id=\"periode1\" name=\"periode1\" style=\"width:70px\">".$optPeriode."</select></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['kodesupplier']."</label></td>
        <td><select id=\"supplier\" name=\"supplier\" style=\"width:150px\">".$optSupplier."</select></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['namasupplier']."</label></td>
        <td><input type=text class=myinputtext id=namasupplier onkeypress=\"return tanpa_kutip(event);\" size=30 maxlength=30></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['kodekelompok']."</label></td>
        <td><select id=\"kelompok\" name=\"kelompok\" style=\"width:150px\">".$optKelompok."</select></td>
    </tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr><td colspan=\"2\">
        <button onclick=\"zPreview('log_slave_2realisasipembayaranpo','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
        <button onclick=\"zExcel(event,'log_slave_2realisasipembayaranpo.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
<!--        <button onclick=\"clear()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button> -->
    </td></tr>
    </table>
    </fieldset>
    </div>

    <div style=\"margin-bottom: 30px;\">
    </div>
    <fieldset style='clear:both'><legend><b>Print Area</b></legend>
    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

    </div></fieldset>
    ";

//$frm[1].="<div>
//    <fieldset style=\"float: left;\">
//    <legend><b>".$_SESSION['lang']['form']." ".$_SESSION['lang']['listpo']."</b></legend>
//    <table cellspacing=\"1\" border=\"0\" >
//    <tr>
//        <td><label>".$_SESSION['lang']['tanggal']."</label></td>
//        <td><input type=\"text\" class=\"myinputtext\" id=\"tgl1\" name=\"tgl1\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" />
//        s.d.
//        <input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onmousemove=\"setCalendar(this.id);\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:60px;\" /></td>
//    </tr>
//    <tr>
//        <td><label>".$_SESSION['lang']['status']."</label></td>
//        <td><select id=\"status1\" name=\"status1\" style=\"width:150px\">".$optStat1."</select></td>
//    </tr>
//    <tr>
//        <td><label>".$_SESSION['lang']['pt']."</label></td>
//        <td><select id=\"pt1\" name=\"pt1\" style=\"width:150px\">".$optPt1."</select></td>
//    </tr>
//    <tr>
//        <td><label>".$_SESSION['lang']['status']." ".$_SESSION['lang']['diterima']."</label></td>
//        <td><select id=\"terima1\" name=\"terima1\" style=\"width:150px\">".$optTer1."</select></td>
//    </tr> 
//
//    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
//    <tr><td colspan=\"2\">
//        <button onclick=\"zPreview('log_slave_2laporan_statuspo1','".$arr1."','printContainer1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
//        <button onclick=\"zExcel(event,'log_slave_2laporan_statuspo1.php','".$arr1."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>
//        <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>
//    </td></tr>
//    </table>
//    </fieldset>
//    </div>
//
//    <div style=\"margin-bottom: 30px;\">
//    </div>
//    <fieldset style='clear:both'><legend><b>Print Area</b></legend>
//    <div id='printContainer1' style='overflow:auto;height:350px;max-width:1220px'>
//
//    </div></fieldset>
//    ";

//========================
$hfrm[0]=$title[0];
//$hfrm[1]=$title[1];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,1220);
//===============================================

CLOSE_BOX();
echo close_body();
?>
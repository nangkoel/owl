<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); 
$lokasitugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$arr="##tanggal1##tanggal2##karyawanid";
$arr1="##tahun";
$arr2="##tanggal21##tanggal22##karyawanid2";
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/sdm_2rekapabsenho.js'></script>

<link rel=stylesheet type='text/css' href='style/zTable.css'>
<div>
<fieldset style="float: left;">

<?php    

$daritahun=9999;
$sampaitahun=0;

// karyawan ijin & cuti
$optTahun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sOpt="select darijam, sampaijam from ".$dbname.".sdm_ijin where stpersetujuan1 = '1' and stpersetujuanhrd = '1'";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt))
{
    if(substr($rOpt['darijam'],0,4)!='0000')if($daritahun>substr($rOpt['darijam'],0,4))$daritahun=substr($rOpt['darijam'],0,4);
    if(substr($rOpt['sampaijam'],0,4)!='0000')if($sampaitahun<substr($rOpt['sampaijam'],0,4))$sampaitahun=substr($rOpt['sampaijam'],0,4);
}

// karyawan dinas
$sOpt="select tanggalperjalanan, tanggalkembali from ".$dbname.".sdm_pjdinasht where statuspersetujuan='1' and statushrd='1'";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt))
{
    if(substr($rOpt['tanggalperjalanan'],0,4)!='0000')if($daritahun>substr($rOpt['tanggalperjalanan'],0,4))$daritahun=substr($rOpt['tanggalperjalanan'],0,4);
    if(substr($rOpt['tanggalkembali'],0,4)!='0000')if($sampaitahun<substr($rOpt['tanggalkembali'],0,4))$sampaitahun=substr($rOpt['tanggalkembali'],0,4);    
}

// karyawan absen
$sOpt="select scan_date from ".$dbname.".att_log where 1";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt))
{
    if(substr($rOpt['scan_date'],0,4)!='0000')if($daritahun>substr($rOpt['scan_date'],0,4))$daritahun=substr($rOpt['scan_date'],0,4);
    if(substr($rOpt['scan_date'],0,4)!='0000')if($sampaitahun<substr($rOpt['scan_date'],0,4))$sampaitahun=substr($rOpt['scan_date'],0,4);    
}

//echo $daritahun."-".$sampaitahun;

for ($i = $daritahun; $i <= $sampaitahun; $i++) {
    $optTahun.="<option value=".$i.">".$i."</option>";
}

//ambil query untuk data karyawan
$skaryawan="select a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from ".$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
    where a.lokasitugas like '%HO' 
    order by namakaryawan asc";    
//    where a.lokasitugas like '%HO' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar='0000-00-00')
//echo $skaryawan;
$rkaryawan=fetchData($skaryawan);
$optkaryawan="<option value=''>".$_SESSION['lang']['all']."</option>";
foreach($rkaryawan as $row => $kar)
{
    $optkaryawan.="<option value='".$kar['karyawanid']."'>".$kar['namakaryawan']." - ".$kar['namajabatan']."</option>";
}  


$frm[0].="<div style=margin-bottom: 30px;>";
$frm[0].="<fieldset>
<legend><b>".$_SESSION['lang']['rkpAbsen']." HO</b></legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr>
        <td><label>".$_SESSION['lang']['tanggalmulai']."</label></td>
        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal1\" name=\"tanggal1\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['tanggalsampai']."</label></td>
        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal2\" name=\"tanggal2\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
    </tr>
    <tr><td>".$_SESSION['lang']['namakaryawan']."</td>
        <td><select id=karyawanid name=karyawanid style='width:300px;'>".$optkaryawan."</select></td>
    </tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr>
        <td colspan=\"2\">
            <button onclick=\"zPreview('sdm_slave_2rekapabsenho','".$arr."','container')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
            <button onclick=\"zPdf('sdm_slave_2rekapabsenho','".$arr."','container')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>
            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho.php','".$arr."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>
            <button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>
        </td>
    </tr>
</table>
</fieldset>";
$frm[0].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='container' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";   

$frm[1].="<div style=margin-bottom: 30px;>";
$frm[1].="<fieldset>
<legend><b>".$_SESSION['lang']['rkpAbsen']." HO Annually</b></legend>";
$frm[1].="<table cellspacing=1 border=0>
    <tr>
        <td><label>".$_SESSION['lang']['tahun']."</label></td>
        <td><select id=tahun name=tahun style=width:100px>".$optTahun."</select></td>
    </tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr>
        <td colspan=\"2\">
            <button onclick=\"zPreview('sdm_slave_2rekapabsenho1','".$arr1."','container1')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
            <button onclick=\"zPdf('sdm_slave_2rekapabsenho1','".$arr1."','container1')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>
            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho1.php','".$arr1."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>
            <button onclick=\"Clear2()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>
        </td>
    </tr>
</table>
</fieldset>";
$frm[1].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='container1' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";   
    
$frm[2].="<div style=margin-bottom: 30px;>";
$frm[2].="<fieldset>
<legend><b>".$_SESSION['lang']['laporanLembur']." HO</b></legend>";
$frm[2].="<table cellspacing=1 border=0>
    <tr>
        <td><label>".$_SESSION['lang']['tanggalmulai']."</label></td>
        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal21\" name=\"tanggal21\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
    </tr>
    <tr>
        <td><label>".$_SESSION['lang']['tanggalsampai']."</label></td>
        <td><input type=\"text\" class=\"myinputtext\" id=\"tanggal22\" name=\"tanggal22\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td>
    </tr>
    <tr><td>".$_SESSION['lang']['namakaryawan']."</td>
        <td><select id=karyawanid2 name=karyawanid2 style='width:300px;'>".$optkaryawan."</select></td>
    </tr>
    <tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>
    <tr>
        <td colspan=\"2\"> 
            <button onclick=\"zPreview('sdm_slave_2rekapabsenho2','".$arr2."','container2')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
            <!--<button onclick=\"zPdf('sdm_slave_2rekapabsenho2','".$arr2."','container2')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">PDF</button>-->
            <button onclick=\"zExcel(event,'sdm_slave_2rekapabsenho2.php','".$arr2."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>
            <button onclick=\"Clear3()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">".$_SESSION['lang']['cancel']."</button>
        </td>
    </tr>
</table>
</fieldset>";
$frm[2].="</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='container2' style='overflow:auto;height:350px;max-width:1220px'>
</div></fieldset>";   


//========================
$hfrm[0]=$_SESSION['lang']['rkpAbsen'].' HO';
$hfrm[1]=$_SESSION['lang']['rkpAbsen'].' HO Annually';
$hfrm[2]=$_SESSION['lang']['laporanLembur'].' HO';
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,200,900);
//========================    
   
?>

<?php
CLOSE_BOX();
echo close_body();
?>
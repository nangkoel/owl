<?php//Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>THR SIL/SIP</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript src='js/sdm_thrSil.js'></script>
<?php

//print_r($_SESSION['empl']);
if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $iOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi in "
            . " (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') "
            . " order by namaorganisasi asc";	
}
else
{
    $iOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}
$nOrg=mysql_query($iOrg) or die(mysql_error($conn));
while($dOrg=mysql_fetch_assoc($nOrg))
{
	$optOrg.="<option value=".$dOrg['kodeorganisasi'].">".$dOrg['namaorganisasi']."</option>";
}

/*$iPer="select periode from ".$dbname.".sdm_5periodegaji
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='H' and sudahproses=0";
$nPer=mysql_query($iPer) or die(mysql_error($conn));
while($dPer=mysql_fetch_assoc($nPer))
{
	$optPer.="<option value=".$dPer['periode'].">".$dPer['periode']."</option>";
}*/


$iTk="select * from ".$dbname.".sdm_5tipekaryawan where id!=0";
$nTk=mysql_query($iTk) or die(mysql_error($conn));
while($dTk=mysql_fetch_assoc($nTk))
{
	$optTk.="<option value=".$dTk['id'].">".$dTk['tipe']."</option>";
}

$optThn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$iThn="select distinct(tahun) as tahun from ".$dbname.".sdm_5periodethr";
$nThn=mysql_query($iThn) or die(mysql_error($conn));
while($dThn=mysql_fetch_assoc($nThn))
{
	$optThn.="<option value=".$dThn['tahun'].">".$dThn['tahun']."</option>";
}

$optAgama="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrAgama=getEnum($dbname,'datakaryawan','agama');
foreach($arrAgama as $kei=>$fal)
{
        $optAgama.="<option value='".$kei."'>".$fal."</option>";
}

$arr="##kdOrg##per##tk##agama##tgl##tahun";	
echo"<fieldset style='float:left;'>
        <legend><b>Form</b></legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kdOrg style=\"width:150px;\">".$optOrg."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tipekaryawan']."</td>
                    <td>:</td>
                    <td><select id=tk style=\"width:150px;\">".$optTk."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['agama']."</td>
                    <td>:</td>
                    <td><select id=agama onchange=getPer(); style=\"width:150px;\">".$optAgama."</select></td>
                </tr>
                
                <tr>
                    <td>".$_SESSION['lang']['tahun']."</td>
                    <td>:</td>
                    <td><select id=tahun onchange=getPer(); style=\"width:150px;\">".$optThn."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['periode']."</td>
                    <td>:</td>
                    <td><input type=text maxlength=7 disabled id=per nkeypress=\"return_tanpa_kutip(event);\"   class=myinputtext style=\"width:75px;\"></td>
                </tr>
                
                
                <tr>
                    <td>".$_SESSION['lang']['tanggal']." Cut Off</td>
                    <td>:</td>
                    <td><input type=text class=myinputtext disabled id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
                </tr>
                <tr>
                    <td colspan=4>
                    <button onclick=zPreview('sdm_slave_thrSil','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    <button onclick=zExcel(event,'sdm_slave_thrSil.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
                    </td>
                </tr>
            </table>
</fieldset>";


echo"<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['keterangan']."</b></legend>
    <table>
        <tr>
            <td>Kode Organisasi</td>
            <td>:</td>
            <td>Unit dimana yang akan melakukan proses THR</td>
        </tr>
        <tr>
            <td>Tahun</td>
            <td>:</td>
            <td>Tahun dimana THR akan diberikan dan sumber gaji yang akan diambil</td>
        </tr>
        <tr>
            <td>Periode </td>
            <td>:</td>
            <td>Periode THR akan diberikan</td>
        </tr>
        <tr>
            <td>Tipe Karyawan</td>
            <td>:</td>
            <td>Tipe dari karyawan yang akan mendapatkan THR (KBL=Bulanan, KHT=Harian)</td>
        </tr>
        <tr>
            <td>Agama</td>
            <td>:</td>
            <td>Agama dari karyawan yang mendapatkan THR</td>
        </tr>
        <tr>
            <td>Tanggal Cut Off</td>
            <td>:</td>
            <td>Tanggal dimana cut off periode THR</td>
        </tr>
    </table></fieldset>";


CLOSE_BOX();

OPEN_BOX();
echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:400px;max-width:1220px'; >
</div></fieldset>";//<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
//<div id='printContainer'>
CLOSE_BOX();
echo close_body();					
?>
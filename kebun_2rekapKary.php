<?php //Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>Rekap Karyawan</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript src='js/kebun_3bjr.js'></script>

<script>
</script>


<?php



if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $iAfd="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='afdeling' and induk in "
            . " (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') "
            . " order by namaorganisasi asc";	
}
else
{
    $iAfd="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='afdeling' and induk='".$_SESSION['empl']['lokasitugas']."'";
}

$nAfd=mysql_query($iAfd) or die(mysql_error($conn));
while($dAfd=mysql_fetch_assoc($nAfd))
{
	$optAfd.="<option value=".$dAfd['kodeorganisasi'].">".$dAfd['namaorganisasi']."</option>";
}

$optKeg="<option value=''>".$_SESSION['lang']['all']."</option>";
$iKeg="select * from ".$dbname.".setup_kegiatan where kelompok in ('TM','TBM','BBT','TB') order by namakegiatan";
$nKeg=mysql_query($iKeg) or die(mysql_error($conn));
while($dKeg=mysql_fetch_assoc($nKeg))
{
	$optKeg.="<option value=".$dKeg['kodekegiatan'].">".$dKeg['namakegiatan']." [".$dKeg['kodekegiatan']."] [".$dKeg['kelompok']."]</option>";
}

$optKar="<option value=''>".$_SESSION['lang']['all']."</option>";
if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
    $iAfd="select * from ".$dbname.".datakaryawan where tipekaryawan='4' and lokasitugas in "
            . " (select kodeunit from ".$dbname.".bgt_regional_assignment where "
            . " regional='".$_SESSION['empl']['regional']."') order by namakaryawan asc ";
           	
}
else
{
    $iKar="select * from ".$dbname.".datakaryawan where tipekaryawan='4' and "
            . " lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan asc";
}
$nKar=mysql_query($iKar) or die(mysql_error($conn));
while($dKar=mysql_fetch_assoc($nKar))
{
	$optKar.="<option value=".$dKar['karyawanid'].">".$dKar['namakaryawan']." [".$dKar['nik']."]</option>";
}

$arr="##kdAfd##kdKeg##tgl1##tgl2##kar";	
echo"<fieldset style='float:left;'>
        <legend><b>Form</b></legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kdAfd style=\"width:150px;\">".$optAfd."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['namakegiatan']."</td>
                    <td>:</td>
                    <td><select id=kdKeg style=\"width:150px;\">".$optKeg."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tanggal']."</td>
                    <td>:</td>
                    <td>
                        <input type=text class=myinputtext  id=tgl1 onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/> S/D 
                        <input type=text class=myinputtext  id=tgl2 onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/>
                    </td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['namakaryawan']."</td>
                    <td>:</td>
                    <td><select id=kar style=\"width:150px;\">".$optKar."</select></td>
                </tr>
                <tr>
                    <td colspan=4>
                    <button onclick=zPreview('kebun_slave_2rekapKary','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    <button onclick=zExcel(event,'kebun_slave_2rekapKary.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
                </tr>
            </table>
</fieldset>";





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
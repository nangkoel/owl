<?php//Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>Rekap Gaji</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<script language=javascript src='js/sdm_rekapgaji.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc ";	
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    if ($_SESSION['empl']['kodeorganisasi']==$rOrg['kodeorganisasi']){
        $optOrg.="<option value=".$rOrg['kodeorganisasi']." selected>".$rOrg['namaorganisasi']."</option>";
    } else {
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
    }
}

$sUnit="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in ('KANWIL','KEBUN','PABRIK') and induk='".$_SESSION['empl']['kodeorganisasi']."' order by namaorganisasi asc ";	
$qUnit=mysql_query($sUnit) or die(mysql_error($conn));
$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
while($rUnit=mysql_fetch_assoc($qUnit))
{
    $optUnit.="<option value=".$rUnit['kodeorganisasi'].">".$rUnit['namaorganisasi']."</option>";
}

$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
$iPer="select distinct periodegaji as periodegaji from ".$dbname.".sdm_gaji order by periodegaji desc limit 12";
$nPer=mysql_query($iPer) or die(mysql_error($conn));
while($dPer=mysql_fetch_assoc($nPer))
{
	$optPer.="<option value=".$dPer['periodegaji'].">".$dPer['periodegaji']."</option>";
}

$optTp="<option value=''>".$_SESSION['lang']['all']."</option>";
$iTp="select * from ".$dbname.".sdm_5tipekaryawan where id<>'0'";
$nTp=mysql_query($iTp) or die(mysql_error($conn));
while($dTp=mysql_fetch_assoc($nTp))
{
	$optTp.="<option value=".$dTp['id'].">".$dTp['tipe']."</option>";
}

$arr="##pt##per##tp##unit";	
echo"<fieldset style='float:left;'>
        <legend>Form</legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['pt']."</td>
                    <td>:</td>
                    <td><select id=pt style=\"width:150px;\" onchange=getkebun()>".$optOrg."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['unit']."</td>
                    <td>:</td>
                    <td><select id=unit style=\"width:150px;\">".$optUnit."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tahun']."</td>
                    <td>:</td>
                    <td><select id=per style=\"width:150px;\">".$optPer."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tipekaryawan']."</td>
                    <td>:</td>
                    <td><select id=tp style=\"width:150px;\">".$optTp."</select></td>
                </tr>
                <tr>
                    <td colspan=4>
                    <button onclick=zPreview('sdm_slave_2rekapGaji','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    <button onclick=zExcel(event,'sdm_slave_2rekapGaji.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
                    </td>
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
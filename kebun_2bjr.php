<?php //Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>BJR Harian</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
$iOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='kebun' order by namaorganisasi asc ";	
$nOrg=mysql_query($iOrg) or die(mysql_error($conn));
while($dOrg=mysql_fetch_assoc($nOrg))
{
	$optOrg.="<option value=".$dOrg['kodeorganisasi'].">".$dOrg['namaorganisasi']."</option>";
}

$iPer="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_spbht
      where substr(tanggal,1,7)!='0000-00' order by substr(tanggal,1,7) asc limit 12";
$nPer=mysql_query($iPer) or die(mysql_error($conn));
while($dPer=mysql_fetch_assoc($nPer))
{
	$optPer.="<option value=".$dPer['periode'].">".$dPer['periode']."</option>";
}




$arr="##kdOrg##per";	
echo"<fieldset style='float:left;'>
        <legend><b>Form</b></legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kdOrg style=\"width:150px;\">".$optOrg."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['periode']."</td>
                    <td>:</td>
                    <td><select id=per style=\"width:150px;\">".$optPer."</select></td>
                </tr>
                <tr>
                    <td colspan=4>
                    <button onclick=zPreview('kebun_slave_2bjr','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    <button onclick=zExcel(event,'kebun_slave_2bjr.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
                    </td>
                </tr>
            </table>
</fieldset>";

echo"<fieldset style=float:left;width:470px;><legend><b>Info</b></legend>";
echo"<ol>
    <li>Jika pada tanggal tidak ada angka / kosong, maka hari itu tidak ada pengiriman SPB atau belum diinputkan SPBnya</li>
    <li>Jika pada tanggal muncul angka <b>0</b>, maka ada SPB belum dilakukan proses ambil Kg timbangan</li></ol>";
echo"</fieldset>";

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
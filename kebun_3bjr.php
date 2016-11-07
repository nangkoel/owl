<?php //Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>Update BJR Bulan Lalu</b><br /><br />");
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
//echo $iAfd;
$nAfd=mysql_query($iAfd) or die(mysql_error($conn));
while($dAfd=mysql_fetch_assoc($nAfd))
{
	$optAfd.="<option value=".$dAfd['kodeorganisasi'].">".$dAfd['namaorganisasi']."</option>";
}

$arr="##kdAfd##tahun";	
echo"<fieldset style='float:left;'>
        <legend><b>Form</b></legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kdAfd style=\"width:150px;\">".$optAfd."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tahun']."</td>
                    <td>:</td>
                    <td><input type=text maxlength=4 id=tahun onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:75px;\"></td>
                </tr>
                <tr>
                    <td colspan=4>
                    <button onclick=zPreview('kebun_slave_3bjr','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    
                </tr>
            </table>
</fieldset>";


echo"<fieldset style='float:left;'><legend><b>".$_SESSION['lang']['keterangan']."</b></legend>
    Proses menu ini dijalankan jika <b>Gaji sudah di proses dan tidak ada transaksi panen untuk bulan lalu</b>
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
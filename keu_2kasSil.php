<?php//Ind
require_once('master_validation.php');
require_once('lib/nangkoelib.php');
require_once ('config/connection.php');
require_once('lib/zLib.php');
echo open_body();
require_once('master_mainMenu.php');

OPEN_BOX('',"<b>KAS HARIAN</b><br /><br />");
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>


<script>
</script>


<?php




$iAfd="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi in "
            . " (select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') "
            . " order by namaorganisasi asc";	


$nAfd=mysql_query($iAfd) or die(mysql_error($conn));
while($dAfd=mysql_fetch_assoc($nAfd))
{
	$optAfd.="<option value=".$dAfd['kodeorganisasi'].">".$dAfd['namaorganisasi']."</option>";
}





$iAkun="select * from ".$dbname.".keu_5akun where length(noakun)=7 and left(noakun,2)=11 and (namaakun like '%KAS%' or namaakun like '%BANK%')  "
            . " and pemilik in ('L0RO','P0RO') order by noakun asc";	


$nAkun=mysql_query($iAkun) or die(mysql_error($conn));
while($dAkun=mysql_fetch_assoc($nAkun))
{
	$optAkun.="<option value=".$dAkun['noakun'].">".$dAkun['noakun']." ".$dAkun['namaakun']."</option>";
}



$arr="##kdOrg##akunKas##akunBank##tgl1##tgl2";	
echo"<fieldset style='float:left;'>
        <legend><b>Form</b></legend>
            <table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kdOrg style=\"width:150px;\">".$optAfd."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['noakun']." Kas</td>
                    <td>:</td>
                    <td><select id=akunKas style=\"width:150px;\">".$optAkun."</select></td>
                </tr>
                 <tr>
                    <td>".$_SESSION['lang']['noakun']." Bank</td>
                    <td>:</td>
                    <td><select id=akunBank style=\"width:150px;\">".$optAkun."</select></td>
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
                    <td colspan=4>
                    <button onclick=zPreview('keu_slave_kasSil','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
                    <button onclick=zExcel(event,'keu_slave_kasSil.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
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
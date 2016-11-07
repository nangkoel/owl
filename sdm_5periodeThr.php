<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/sdm_5periodeThr.js'></script>



<?php

$optAgama='';
$arrAgama=getEnum($dbname,'datakaryawan','agama');
foreach($arrAgama as $kei=>$fal)
{
        $optAgama.="<option value='".$kei."'>".$fal."</option>";
}

OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
    echo"<legend>Periode THR</legend>";
        echo"<table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['agama']."</td>
                    <td>:</td>
                    <td><select id=agama style=\"width:75px;\">".$optAgama."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['tahun']."</td>
                    <td>:</td>
                    <td><input type=text maxlength=4 id=tahun onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:75px;\"></td>
                </tr>
                
                <tr>
                    <td>".$_SESSION['lang']['periode']." </td> 
                    <td>:</td>
                    <td>
                        <input type=text maxlength=7 id=perMulai keypress=\"return_tanpa_kutip(event);\"   class=myinputtext style=\"width:75px;\"> S/D
                        <input type=text maxlength=7 id=perSampai nkeypress=\"return_tanpa_kutip(event);\"   class=myinputtext style=\"width:75px;\">
                    </td>
                </tr>
                 <tr>
                    <td>".$_SESSION['lang']['periode']." Bayar</td> 
                    <td>:</td>
                    <td><input type=text maxlength=7 id=perBayar nkeypress=\"return_tanpa_kutip(event);\"   class=myinputtext style=\"width:75px;\"></td>
                </tr>
                <tr>
                    <td>Tanggal Cut Off</td> 
                    <td>:</td>
                    <td><input type=\"text\" class=\"myinputtext\" id=\"tgl\" name=\"tgl\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:75px;\" /></td></td>
                </tr>



                <tr><td colspan=2></td>
                        <td colspan=3>
                                <button class=mybutton onclick=simpan()>Simpan</button>
                                <button class=mybutton onclick=cancel()>Hapus</button>
                        </td>
                </tr>

        </table></fieldset>
                        <input type=hidden id=method value='insert'>";
        


CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
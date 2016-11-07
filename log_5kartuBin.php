<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/log_5kartuBin.js'></script>

<?php


#buat kodegudang
$optGudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$iGudang="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('GUDANG','GUDANGTEMP') "
        . " and induk='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc ";
$nGudang=mysql_query($iGudang) or die (mysql_error($conn));
while($dGudang=mysql_fetch_assoc($nGudang))
{
    $optGudang.="<option value='".$dGudang['kodeorganisasi']."'>".$dGudang['namaorganisasi']."</option>";
}

OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
    echo"<legend>".$_SESSION['lang']['nokartubin']."</legend>";
        echo"<table border=0 cellpadding=1 cellspacing=1>
                <tr>
                    <td>".$_SESSION['lang']['gudang']."</td> 
                    <td>:</td>
                    <td><select id=kdOrg style=\"width:150px;\">".$optGudang."</select></td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['kodebarang']."</td> 
                    <td>:</td>
                    <td><input type=text id=kdBarang disabled class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\">
                        <img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariBarang class=resicon onclick=cariBarang('".$_SESSION['lang']['find']."',event)>
                    </td>
                </tr>
                <tr>
                    <td>".$_SESSION['lang']['nokartubin']."</td> 
                    <td>:</td>
                    <td><input type=text  id=noKartu onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
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
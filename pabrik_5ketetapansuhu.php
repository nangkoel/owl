<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/pabrik_5ketetapansuhu.js'></script>

<?php
OPEN_BOX();

$optPabrik="<option value=\"\">".$_SESSION['lang']['pilihdata']."</option>>";
$sOpt = selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='PABRIK' AND kodeorganisasi LIKE'".$_SESSION['empl']['lokasitugas']."'");
//$sOpt="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PABRIK' AND kodeorganisasi LIKE '".$_SESSION['empl']['lokasitugas']."'";
$qOpt=mysql_query($sOpt) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qOpt)){
    $optPabrik.="<option value=".$rOpt['kodeorganisasi'].">".$rOpt['namaorganisasi']."</option>";
}

echo"<fieldset style='float:left;'>";
if($_SESSION['language']=='ID')
    echo"<legend>Suhu Tangki</legend>";
else
    echo"<legend>Tank Temperature</legend>";

    echo"<table border=0 cellpadding=1 cellspacing=1>
            <tr>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>:</td>
                    <td><select id=kodeorg name=kodeorg style=width:150px onchange=getTangki()>".$optPabrik."</select></td>
            </tr>
            <tr>
                    <td>".$_SESSION['lang']['kodetangki']."</td>
                    <td>:</td>
                    <td><select id=kodetangki name=kodetangki style=width:150px></select></td>
            </tr>
            <tr>
                    <td>".$_SESSION['lang']['suhu']."</td> 
                    <td>:</td>
                    <td><input type=text id=suhu value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>

            <tr>
                    <td>".$_SESSION['lang']['kepadatan']."</td> 
                    <td>:</td>
                    <td><input type=text id=kepadatan value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>

            <tr>
                    <td>".$_SESSION['lang']['ketetapan']."</td> 
                    <td>:</td>
                    <td><input type=text id=ketetapan value='0' onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>

            <tr><td colspan=2></td>
                    <td colspan=3>
                            <button class=mybutton onclick=simpan()>Simpan</button>
                    </td>
            </tr>

    </table></fieldset>
                    <input type=hidden id=method value='insert'>";


CLOSE_BOX();
?>



<?php
OPEN_BOX();
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
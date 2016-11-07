<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/it_5stNilaiKegiatan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',"Standard Nilai Kegiatan");
$arr="##kdkegiatan##ket##satuan##nilsngtbaik##nilbaik##nilckp##nilkrg##method";
echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodekegiatan']."</td><td><input type=text id=kdkegiatan size=8 maxlength=8 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=ket size=40 maxlength=25 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=8  maxlength=4 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext></td></tr>
             <tr><td>Sangat Baik</td><td><input type=text id=nilsngtbaik size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>
                 <tr><td>Baik</td><td><input type=text id=nilbaik size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>
                     <tr><td>Cukup</td><td><input type=text id=nilckp size=9  maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>
                         <tr><td>Kurang</td><td><input type=text id=nilkrg size=9  maxlength=12 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber ></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	  <input type=hidden id=eduid value=''>
	 <button class=mybutton onclick=simpanPendidikan('it_slave_5stNilaiKegiatan','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme();
echo "<div id=container>";
echo"<script>loadData()</script>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>
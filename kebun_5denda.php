<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kebun_5denda.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','');

echo"<fieldset style='float:left;'><legend>".$_SESSION['lang']['denda']."</legend><table>
     <tr><td>".$_SESSION['lang']['kode']."</td><td><input type=text class=myinputtext id=regId maxlength=5 style=width:50px onkeypress='return tanpa_kutip(event)' /></td></tr>
	 <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=tgl class=myinputtext style=width:150px onkeypress='return tanpa_kutip(event)'  /></td></tr>
	 <tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=ktrngan onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>     
	 </table>
	 <input type=hidden id=method value='insert'>
         <input type=hidden id=tglOld value=''>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";

echo "<fieldset style='clear:both;float:left;'><legend>".$_SESSION['lang']['data']."</legend>";

echo "<div id=container>";
echo"<script>loadData(0)</script>";	
echo "</div></fieldset>";

CLOSE_BOX();
echo close_body();
?>
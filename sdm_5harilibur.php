<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_5harilibur.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','');

$sreg="select distinct regional from ".$dbname.".bgt_regional_assignment 
                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg=mysql_query($sreg) or die(mysql_error($conn));
$rreg=mysql_fetch_assoc($qreg);
echo"<fieldset style='float:left;'><legend>".$_SESSION['lang']['harilibur']."</legend><table>
     <tr><td>".$_SESSION['lang']['regional']."</td><td><input type=text class=myinputtext id=regId disabled value='".$rreg['regional']."' style=width:150px /></td></tr>
	 <tr><td>".$_SESSION['lang']['tanggal']."</td><td><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td></tr>
	 <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=ktrngan onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=width:150px /></td></tr>     
	 </table>
	 <input type=hidden id=method value='insert'>
         <input type=hidden id=tglOld value=''>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";

echo "<fieldset style='clear:both;float:left;'><legend>".$_SESSION['lang']['data']."</legend>";
echo"<table><tr>";
echo"<td>".$_SESSION['lang']['tanggal']."</td>
     <td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>
     <td>".$_SESSION['lang']['keterangan']."</td>
     <td><input type=text id=ktrnganCr onkeypress=\"return tanpa_kutip(event);\" class=myinputtext style=width:150px /></td>
     </tr></table><button class=mybutton onclick=loadData(0)>".$_SESSION['lang']['find']."</button>";
echo "<div id=container>";
echo"<script>loadData(0)</script>";	
echo "</div></fieldset>";

CLOSE_BOX();
echo close_body();
?>
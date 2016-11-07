<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/log_penerimaanKonosemen.js'></script>
<script language="javascript" src="js/zMaster.js"></script>
<link rel=stylesheet type=text/css href="style/zTable.css">


<?php

OPEN_BOX('',"<b>Penerimaan Konosemen</b>"); //1 O
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=loadData()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo " ".$_SESSION['lang']['nokonosemen']." :<input type=text id=txt size=25 maxlength=30 class=myinputtext>";
		//	echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />Status<select id=status><option value=0>Seluruhnya</option><option value=1>Belum Selesai</option></select>";
			echo"<button class=mybutton onclick=cari()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
CLOSE_BOX();


OPEN_BOX();
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:250px;'>"; 
echo OPEN_BOX('','');
echo OPEN_THEME('Total Penerimaan TBS Internal Per Unit Per Tanggal');
echo OPEN_BOX();
echo"<table>
	   <tr><td>Tanggal </td><td>:&nbsp;<input type=text id=tgllap tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"></td></tr>
		</table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='2' class=tombol2 value=GO onclick=totalPerUnitPerTglInt();></td></tr>
	</table>  
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"    <br>
	<br>
	<div id=tabcontainer>
	</div> ";

?>

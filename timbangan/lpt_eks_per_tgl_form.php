<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:450px;'>"; 
echo OPEN_BOX('','');
echo OPEN_THEME('Penerimaan TBS Eksternal Per Tanggal');
echo OPEN_BOX();
$stg="select TRPCODE,TRPNAME from ".$dbname.".msvendortrp order by TRPNAME";
//echo $stg;
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Seluruhnya...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
echo"<table>
	   <tr><td>Tanggal </td><td>:&nbsp;<input type=text id=tgllap tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\">
	       S/d <input type=text id=tgllap2 tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\">                  
	   </td></tr>
	   <tr><td>Supplier</td><td><select id=supplier>".$opt_unit."</select></td></tr>
		</table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='2' class=tombol2 value=GO onclick=perTglEks();></td></tr>
		 </table>
    
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>

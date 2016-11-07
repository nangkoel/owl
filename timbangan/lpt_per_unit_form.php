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
echo OPEN_THEME('Penerimaan TBS Internal Per Unit');
echo OPEN_BOX();
$stg="select UNITCODE,UNITNAME from ".$dbname.".msunit order by UNITCODE";
//echo $stg;
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
echo"<table>
	   <tr>
	   <tr><td>Tanggal </td><td>:&nbsp;<input type=text id=tgllap tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"></td></tr>
		 <tr><td>Unit </td><td>:&nbsp;<select id=unitcode tabindex='2' style='height:18px;text-align:left;font-size:11px;'>".$opt_unit."</select></td></tr></table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='3' class=tombol2 value=GO onclick=unit();></td></tr>
		 </table>
    
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>

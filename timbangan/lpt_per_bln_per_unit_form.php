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
echo OPEN_THEME('Monitoring TBS Internal Per Unit/Bulan/Afd');
echo OPEN_BOX();
$stg="select UNITCODE,UNITNAME from ".$dbname.".msunit order by UNITCODE";
//echo $stg;
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
for ($i=0;$i<=12;$i++)		 	   
    {
			 $now = mktime(0, 0, 0, date('m')-$i, 20,date('Y'));
		    $adqty.="<option value=".date('Y-m',$now).">".date('m-Y',$now)."</option>"; 
			//echo"<option>".date('m-Y',$now)."</option>";
    }	
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_unit.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}	
echo"<table>
	   <tr><td>Periode </td><td>:&nbsp;<select id=periode>".$adqty."</select></td></tr>
	   <tr><td>Unit </td><td>:&nbsp;<select id=unit>".$opt_unit."</select></td></tr>
		</table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='2' class=tombol2 value=GO onclick=perBlnperUnit();></td></tr>
		 </table>
    
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>

<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:325px;'>"; 
echo OPEN_BOX('','');
echo OPEN_THEME('Realisasi SIPB');
echo OPEN_BOX();
$stg="select * from ".$dbname.".msproduct where PRODUCTCODE like '40%'  and PRODUCTCODE not in ('40000001','40000002') order by PRODUCTCODE";
//echo $stg;
$reg=mysql_query($stg);
$opt_product="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_product.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
echo"<table>
		 <tr><td>Product </td><td>:&nbsp;<select id=product tabindex='1' onchange=rubahX(this.options[selectedIndex].value,'Unit') style='height:18px;text-align:left;font-size:11px;'>".$opt_product."</select></td></tr>
		 <tr><td>No.SIPB </td><td>:&nbsp;<select id=SIPBNO tabindex='2' style='height:18px;text-align:left;font-size:11px;'></select></td></tr></table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='3' class=tombol2 value=GO onclick=lptRealisasi();></td></tr>
		 </table>
    
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>

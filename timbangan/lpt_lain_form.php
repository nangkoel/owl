<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:350px;'>"; 
echo OPEN_BOX('','');
echo OPEN_THEME('Penerimaan Barang Selain TBS');
echo OPEN_BOX();
$stg="select * from ".$dbname.".msproduct where PRODUCTCODE not like '10%' and PRODUCTCODE not like '40%' order by PRODUCTCODE";
//echo $stg;
$reg=mysql_query($stg);
$opt_product="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_product.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
echo"<table>
	   <tr>
	   <tr><td>Tanggal </td><td>:&nbsp;<input type=text id=tgllap tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"></td></tr>
		 <tr><td>Product </td><td>:&nbsp;<select id=product tabindex='2' style='height:18px;text-align:left;font-size:11px;'>".$opt_product."</select></td></tr></table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='3' class=tombol2 value=GO onclick=lptLain();></td></tr>
		 </table>
    
     ";

echo CLOSE_BOX();
echo CLOSE_THEME();
echo CLOSE_BOX();
echo"</div>";

?>

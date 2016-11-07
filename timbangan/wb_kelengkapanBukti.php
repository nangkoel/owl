<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/rpt.js></script>
<?php
include('master_mainMenu.php');
echo"<div align=left id=b style='width:450px;'>"; 
echo OPEN_BOX('','Kelengkapan Data Bukti Pengiriman:');
$stg="select PRODUCTCODE,PRODUCTNAME from ".$dbname.".msproduct 
      where PRODUCTCODE IN('40000001','40000002','40000005')
	  order by PRODUCTNAME";
//echo $stg;
$reg=mysql_query($stg);
$opt_unit="<option value='0'>Silakan Pilih...</option>";
	while($bag=mysql_fetch_array($reg))
		{
			$opt_product.="<option value='".$bag[0]."'>".$bag[1]."</option>";
		}
echo"<table>
       <tr><td>Product </td><td>:&nbsp;<select id=product>".$opt_product."</select></td></tr>
	   <tr><td>Tanggal </td><td>:&nbsp;<input type=text id=tgllap tabindex='1' class=myinputtext size=10 onmousemove=setCalendar(this.id) onkeypress=\"return false;\"></td></tr>
		</table>
		<table align=center> 
		 <tr><td><input type=button id=button1 tabindex='2' class=tombol2 value=Tampilkan onclick=displayExisting();></td></tr>
		 </table> 
     ";
echo CLOSE_BOX();
echo"</div>";

echo "<div id=containex>
      </div>
	 ";

?>

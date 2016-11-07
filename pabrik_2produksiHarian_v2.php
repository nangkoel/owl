<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/pabrik_produksi_v2.js'></script>
<?php
include('master_mainMenu.php');
$str="select kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'
      order by kodeorganisasi";
$res=mysql_query($str);
$optpabrik="<option value=*></option>";
while($bar=mysql_fetch_object($res))
{
	$optpabrik.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
}	  
$optper="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

OPEN_BOX('',"<b>".$_SESSION['lang']['rprodksiPabrik']." :</b>");
echo "<fieldset style='width:500px;'>
      <table>
	  <tr><td>".$_SESSION['lang']['kodeorganisasi']."</td><td>:</td>
	  <td><select id=pabrik  onchange=getPeriode()>".$optpabrik."</select></td></tr>
      <tr><td>".$_SESSION['lang']['periode']."</td><td>:</td><td><select id=periode>".$optper."</select></td></tr></table>
	  <button class=mybutton onclick=getLaporanPrdPabrik()>".$_SESSION['lang']['preview']."</button>
	 ";

CLOSE_BOX();
OPEN_BOX('','');
echo"<div id=container style='width:1020px;height:500px overflow:scroll'>

     </div>"; 
CLOSE_BOX();
close_body();
?>
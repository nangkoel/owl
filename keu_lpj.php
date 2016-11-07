<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/keu_lpj.js'></script>
<?php
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
                or tipe='HOLDING')";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}

OPEN_BOX('','BAHAN LAPORAN LPJ');
echo "<fieldset><table>
      <tr><td>".$_SESSION['lang']['tanggal']."</td><td>:
             <input class='myinputtext' id=dari  onmousemove='setCalendar(this.id)' onkeypress=\"return false;\" maxlength=\"10\" style=\"width: 100px;\" type=\"text\">
             ".$_SESSION['lang']['tanggalsampai']."<input class='myinputtext' id=sampai  onmousemove='setCalendar(this.id)' onkeypress=\"return false;\" maxlength=\"10\" style=\"width: 100px;\" type=\"text\">    
      </td></tr>
      <tr><td>".$_SESSION['lang']['unit']."</td><td>:<select id=unit>".$optgudang."</select></td></tr>
      <tr><td colspan=2><button class=mybutton onclick=preview()>".$_SESSION['lang']['tampilkan']."</button></td></tr>    
      </table>
      </fieldset>    
	  <div id=container style='height:400px; overflow:scroll'>
	  </div>";
CLOSE_BOX();
echo close_body();
?>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_laporanRealisasiSPK.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['realisasispk']).'</b>');

//=================ambil unit;  
$str="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi
      where length(kodeorganisasi)=4 order by namaorganisasi";

$res=mysql_query($str);
$optunit="<option value=''>".$_SESSION['lang']['all']."</option>";
$optunit="<option value=''></option>";
while($bar=mysql_fetch_object($res))
{
	$optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

echo"<fieldset>
     <legend>".$_SESSION['lang']['realisasispk']."</legend>
	 ".$_SESSION['lang']['unit']."<select id=unit style='width:150px;'>".$optunit."</select>
	 ".$_SESSION['lang']['tgldari']." <input type=\"text\" class=\"myinputtext\" id=\"tglAwal\" name=\"tglAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
         ".$_SESSION['lang']['tglsmp']." <input type=\"text\" class=\"myinputtext\" id=\"tglAkhir\" name=\"tglAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />
	 <button class=mybutton onclick=getBiayaTotalPerKendaraan()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');

echo"<span id=printPanel style='display:none;'>
     <img onclick=biayaLaporanRealisasiKeExcel(event,'log_slave_laporanRealisasiSPK.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
      <div id=container>
     </div>";
CLOSE_BOX();
close_body();
  //<td align=center>".$_SESSION['lang']['periode']."</td>
?>
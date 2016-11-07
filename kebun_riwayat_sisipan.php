<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript src='js/kebun_riwayat_sisipan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['riwayatsisipan']).'</b>');

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_aktifitas
      where tipetransaksi = 'PNN' order by periode desc";
  
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
//=================ambil PT;  
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
      where tipe='KEBUN' order by namaorganisasi asc";
//echo $str;
$res=mysql_query($str);
$optpt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

}
$arr="##unitId##tgl1##tgl2";

echo"<fieldset style=width:150px;>
     <legend>".$_SESSION['lang']['riwayatsisipan']."</legend>
         <table cellpadding=1 cellspacing=1 border=0><tr><td>
	 ".$_SESSION['lang']['unit']."</td><td> :</td><td> "."<select id=unitId style='width:200px;'>".$optpt."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td> <td>
          <input type=text class=myinputtext id=tgl1 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> -
	  <input type=text class=myinputtext id=tgl2 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10></td></tr>
          <tr><td colspan=3 align=center>
   <button onclick=\"zPreview('kebun_slave_riwayat_sisipan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>
    <button onclick=\"zPdf('kebun_slave_riwayat_sisipan','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button>
    <button onclick=\"zExcel(event,'kebun_slave_riwayat_sisipan.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button></td></tr></table>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX();
?>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>
<?php
CLOSE_BOX();
close_body();
?>
<?php
//@Copy nangkoelframework

//---ind---
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>


<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script language=javascript1.2 src=js/budget_traksi_total_jam_bengkel.js></script>


<?php
include('master_mainMenu.php');

$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='TRAKSI' and induk='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$sql = "select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' and tipe='TRAKSI' order by namaorganisasi asc";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
$optws="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>


<?php
OPEN_BOX();

echo"<fieldset style='width:380px;'>
     <legend><b>".$_SESSION['lang']['totJamBengkel']."</b></legend>
	 <table>
		 <tr><td width=100>".$_SESSION['lang']['budgetyear']."</td><td width=10>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress=\"return angka_doang(event);\" style=\"width:250px;\" maxlength=4 /></td></tr>
		 <tr><td>".$_SESSION['lang']['kodetraksi']." </td><td width=10>:</td><td><select id=kdorg name=kdorg onchange=getws(0,0) style=\"width:250px;\">".$optOrg."</select></td></tr>
		 <tr><td>".$_SESSION['lang']['workshop']."</td><td width=10>:</td><td><select id=kdtrak name=kdtrak style=\"width:250px;\">".$optws."</select></td></tr>
		 <tr><td>".$_SESSION['lang']['totJamThn']."</td><td width=10>:</td><td><input type=text class=myinputtextnumber  id=totjamthn name=totjamthn onkeypress=\"return angka_doang(event);\" style=\"width:250px;\" /></td></tr>
	 <tr>
	 </table>
	 
	 <table>
	 <tr>
	 <td width=113></td><td>
		 <div id=tmblSave>
		 <button onclick=saveHead() class=mybutton name=saveDt id=saveDt>".$_SESSION['lang']['save']."</button>	 
     	 <button class=mybutton onclick=batal() name=btl id=btl>".$_SESSION['lang']['cancel']."</button></div>
	</td></tr>
 	</table>
     </fieldset><input type=hidden id=method value=saveData />";
?>


<?php
echo"<div id='printContainer' style=display:none;>
      <fieldset style='clear:both;float: left;'><legend>".$_SESSION['lang']['sebaran']." ".$_SESSION['lang']['bulanan']."</legend>";
	  
	  
$arrBln=array(
"1"=>substr($_SESSION['lang']['jan'],0,3),
"2"=>substr($_SESSION['lang']['peb'],0,3),
"3"=>substr($_SESSION['lang']['mar'],0,3),
"4"=>substr($_SESSION['lang']['apr'],0,3),
"5"=>substr($_SESSION['lang']['mei'],0,3),
"6"=>substr($_SESSION['lang']['jun'],0,3),
"7"=>substr($_SESSION['lang']['jul'],0,3),
"8"=>substr($_SESSION['lang']['agt'],0,3),
"9"=>substr($_SESSION['lang']['sep'],0,3),
"10"=>substr($_SESSION['lang']['okt'],0,3),
"11"=>substr($_SESSION['lang']['nov'],0,3),
"12"=>substr($_SESSION['lang']['dec'],0,3),
);
$tot=count($arrBln);
echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader >";
foreach($arrBln as $brs=>$dtBln)
{
	echo"<td align=center>".$dtBln."</td>";
}
echo"<td>".$_SESSION['lang']['save']."</td></tr></thead>";
echo"<tbody><tr class=rowcontent>";
foreach($arrBln as $brs2=>$dtBln2)
{
	echo"<td><input type='text' class='myinputtextnumber'  id=jam_x".$brs2." value=0 style='width:50px' onkeypress=\"return angka_doang(event);\" /></td>";
}
echo"<td align=center style='cursor:pointer;'><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"saveJam(".$tot.")\" src='images/save.png'/></td>";

echo "</tr></tbody></table></fieldset></div>";
CLOSE_BOX();
/*
*/
?>


<?php
OPEN_BOX();
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend>";
echo"<div id=contain><script>loadData()</script></div>";
echo"</fieldset>";
CLOSE_BOX();
echo close_body();
?>
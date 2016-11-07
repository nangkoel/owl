<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/kebun_qc_panen.js'></script>
<script language=javascript src=js/zTools.js></script>

<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>



<?php
	
#keg	
$optKeg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$a="select * from ".$dbname.".setup_kegiatan order by namakegiatan asc";
$b=mysql_query($a) or die (mysql_error($conn));
while($c=mysql_fetch_assoc($b))
{
	$optKeg.="<option value='".$c['kodekegiatan']."'>".$c['namakegiatan']." - ".$c['kelompok']." - ".$c['satuan']."</option>";
}


#divisi (kebun)
$optDiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h))
{
	$optDiv.="<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi']."</option>";
}

#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggalcek,1,7) as periode from ".$dbname.".kebun_qc_panenht order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j))
{
	$optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}


$optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



?>


<?php

$frm[0]='';
$frm[1]='';


OPEN_BOX();
$frm[0].="<fieldset>";
$frm[0].="<legend><b>".$_SESSION['lang']['header']."</b></legend>";

		$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
			$frm[0].="
			<tr>
				<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['cek']."</td> 
				<td>:</td>
				<td><input type=text class=myinputtext  id=tanggalcek onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['panen']."</td> 
				<td>:</td>
				<td><input type=text class=myinputtext  id=tanggalpanen onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
			
			</tr>
			<tr>
				<td>".$_SESSION['lang']['divisi']."</td> 
				<td>:</td>
				<td><select id=kdDiv onchange=getAfd() style=\"width:100px;\">".$optDiv."</select></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['diperiksa']."</td> 
				<td>:</td> 
				<td><select id=diperiksa style=\"width:100px;\">".$optKadiv."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['afdeling']."</td> 
				<td>:</td>
				<td><select id=kdAfd  onchange=getBlok() style=\"width:100px;\">".$optAfd."</select></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['pendamping']."</td> 
				<td>:</td> 
				<td><select id=pendamping style=\"width:100px;\">".$optKar."</select></td>
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['blok']."</td> 
				<td>:</td>
				<td><select id=kdBlok style=\"width:100px;\">".$optBlok."</select></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['mengetahui']."</td> 
				<td>:</td> 
				<td><select id=mengetahui style=\"width:100px;\">".$optKadiv."</select></td>	
			</tr> 
			<tr>
				<td>".$_SESSION['lang']['pusingan']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=pusingan onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>
				<button class=mybutton id=saveHeader onclick=saveHeader()>".$_SESSION['lang']['save']."</button>
				<button class=mybutton id=cancelHeader onclick=cancel()>".$_SESSION['lang']['baru']."</button>	
				</td>
			</tr>			
</table></fieldset>";	


$frm[0].="<div id=detailForm  style='display:none;'>";
$frm[0].="<fieldset>";
$frm[0].="<legend><b>".$_SESSION['lang']['detail']."</b></legend>";
$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
			
$frm[0].="	<tr>
				<td>".$_SESSION['lang']['nourut']." ".$_SESSION['lang']['pokok']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=nopokok onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['brondolan']." ".$_SESSION['lang']['tdkdikutip']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=brdtdkdikutip onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['panen']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=jjgpanen onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['rumpukan']."</td>
				<td>:</td>
				<td><input type=checkbox id=rumpukan value=0 /></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['panen']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=jjgtdkpanen onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['piringan']."</td>
				<td>:</td>
				<td><input type=checkbox id=piringan value=0 /></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jjg']."  ".$_SESSION['lang']['tidakdikumpul']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=jjgtdkkumpul onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['jalur']." ".$_SESSION['lang']['panen']."</td>
				<td>:</td>
				<td><input type=checkbox id=jalurpanen value=0 /></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['mentah']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=jjgmentah onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
				<td style=\"width:25px;\"/></td>
				<td>".$_SESSION['lang']['tukulan']."</td>
				<td>:</td>
				<td><input type=checkbox id=tukulan value=0 /></td>
			</tr>
			<tr>
				<td>".$_SESSION['lang']['jjg']." ".$_SESSION['lang']['menggantung']."</td> 
				<td>:</td>
				<td><input type=text maxlength=10 id=jjggantung onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:100px;\"></td>
			</tr>
			<tr>
				<td>
				<button class=mybutton id=saveDetail onclick=saveDetail()>".$_SESSION['lang']['save']."</button>
				<button class=mybutton id=cancelDetail onclick=cancel()>".$_SESSION['lang']['selesai']."</button>	
				</td>
			</tr>	";	
				
			
$frm[0].="</table></fieldset>";


$frm[0].="	<div id=containList  style='display:none;'>
			</div>";	


/*$frm[0].="<div id=containList  style='display:none;'>
			<script>loadDataDetail()</script>
			</div>";*/	





$frm[1].="<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['divisi']." : <select id=kdDivSch style=\"width:100px;\" onchange=loadData()>".$optDiv."</select>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>		
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
		
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,800);		
		
CLOSE_BOX();
echo close_body();			
?>
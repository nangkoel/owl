<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
include_once('lib/zLib.php');
?>

<script language=javascript1.2 src='js/kebun_qc_pemupukan.js'></script>
<script language=javascript src=js/zTools.js></script>

<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<?php
#jam & menit
for($i=0;$i<24;) {
    if(strlen($i)<2) {
        $i="0".$i;
    }
   $jam.="<option value=".$i.">".$i."</option>";
   $i++;
}
for($i=0;$i<60;) {
    if(strlen($i)<2) {
        $i="0".$i;
    }
   $mnt.="<option value=".$i.">".$i."</option>";
   $i++;
}
	
#divisi (kebun)
$optDiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$g="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi  where tipe='KEBUN' and induk='".$_SESSION['empl']['kodeorganisasi']."'";
$h=mysql_query($g) or die (mysql_error($conn));
while($i=mysql_fetch_assoc($h)) {
    $optDiv.="<option value='".$i['kodeorganisasi']."'>".$i['namaorganisasi']."</option>";
}

#periode for searching 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_qc_pemupukanht order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j)) {
    $optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}

$optAfd="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optBlok="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optAstn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optKadiv="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
#barang
$optBarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$d="select * from ".$dbname.".log_5masterbarang  where kelompokbarang='045' order by namabarang asc";
$e=mysql_query($d) or die (mysql_error($conn));
while($f=mysql_fetch_assoc($e))
{
	$optBarang.="<option value='".$f['kodebarang']."'>".$f['namabarang']." - ".$f['satuan']."</option>";
}
?>

<?php

$frm[0]='';
$frm[1]='';

OPEN_BOX();
$frm[0].="<fieldset style=\"width:975px;\">";
$frm[0].="<legend><b>".$_SESSION['lang']['header']."</b></legend>";

		$frm[0].="<table border=0 cellpadding=1 cellspacing=1>";
			$frm[0].="
                            <tr>
                                <td>".$_SESSION['lang']['tanggal']."</td><td>:</td>
                                <td><input type=text class=myinputtext  id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
                                <td>".$_SESSION['lang']['jamkerja']."</td><td>:</td>
                                <td><select id=jamMulai>".$jam."</select>:<select id=mntMulai>".$mnt."</select></td><td>S/d</td>
                                <td><select id=jamSelesai>".$jam."</select>:<select id=mntSelesai>".$mnt."</select></td>
                            </tr>
                            <tr>
                                <td>Divisi</td><td>:</td>
                                <td><select id=kodedivisi name=kodedivisi style=width:150px onchange=getAfdeling()>".$optDiv."</select></td>
                                <td>".$_SESSION['lang']['jumlahpekerja']."</td><td>:</td>
                                <td colspan=5><input type=text id=jumlahpekerja name=jumlahpekerja value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\"></td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['afdeling']."</td><td>:</td>
                                <td><select id=kodeafdeling name=kodeafdeling style=width:250px onchange=getBlok()>".$optAfd."</select></td>
                              
							  
							
					<td>Pupuk & Dosis</td>
					<td>:</td>
					<td><select id=barang style=\"width:100px;\">".$optBarang."</select></td>
					<td colspan=5><input type=text id=dosis name=dosis value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:50px;\"></td>
                            </tr>
							
                            <tr>
                                <td>".$_SESSION['lang']['blok']."</td><td>:</td>
                                <td><select id=kodeblok name=kodeblok style=width:250px>".$optBlok."</select></td>
                                <td>".$_SESSION['lang']['teraplikasi']."</td><td>:</td>
                                <td colspan=5><input type=text id=teraplikasi name=teraplikasi value='0' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;Sak</td>
                            </tr>
                            <tr>
                                <td>Nama Pengawas</td><td>:</td>
                                <td><select id=namapengawas name=namapengawas style=width:150px>".$optAstn."</select></td>
                                <td>".$_SESSION['lang']['kondisilahan']."</td><td>:</td>
                                <td colspan=5><input type=text id=kondisilahan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtextstyle=\"width:150px;\"></td>
                            </tr>
                            <tr>
                                <td valign=top>".$_SESSION['lang']['comment']."</td> 
                                <td valign=top>:</td>
                                <td><textarea cols=35 rows=5 id=comment onkeypress=\"return tanpa_kutip(event);\"></textarea></td>
                            </tr>
                            <tr>
                                <td>Petugas.QC</td><td>:</td>
                                <td><select id=pengawas name=pengawas style=width:150px>".$optMandor."</select></td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['pendamping']."</td><td>:</td>
                                <td><select id=asisten name=asisten style=width:150px>".$optAstn."</select></td>
                            </tr>
                            <tr>
                                <td>".$_SESSION['lang']['mengetahui']."</td><td>:</td>
                                <td><select id=mengetahui name=mengetahui style=width:150px>".$optKadiv."</select></td>
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
			
$frm[0].="  <tr>
                <td>No. Jalur</td> 
                <td>:</td>
                <td><input type=text maxlength=10 id=nojalur onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['pokok']." ".$_SESSION['lang']['dipupuk']."</td> 
                <td>:</td>
                <td><input type=text maxlength=10 id=pkkdipupuk onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['pokok']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['dipupuk']."</td> 
                <td>:</td>
                <td><input type=text maxlength=10 id=pkktdkdipupuk onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['apl']." ".$_SESSION['lang']['no']." ".$_SESSION['lang']['standar']."</td> 
                <td>:</td>
                <td><input type=text maxlength=10 id=apltdkstandar onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:150px;\"></td>
            </tr>
            <tr>
                <td valign=top>".$_SESSION['lang']['keterangan']."</td> 
                <td valign=top>:</td>
                <td><textarea cols=35 rows=5 id=keterangan onkeypress=\"return tanpa_kutip(event);\"></textarea></td>
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

$frm[1].="<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		".$_SESSION['lang']['kebun']." : <select id=kdKebunSch style=\"width:100px;\">".$optDiv."</select>
		".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\">".$optPer."</select>
                <button class=mybutton id=preview onclick=loadDataPrev()>".$_SESSION['lang']['preview']."</button>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
		
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,250,1000);		
		
CLOSE_BOX();
echo close_body();			
?>
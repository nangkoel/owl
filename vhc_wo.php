<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language="javascript" src="js/zMaster.js"></script>
<script   language=javascript1.2 src='js/vhc_wo.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();

$jam=$mnt=0;
for($i=0;$i<24;){
    if(strlen($i)<2){
        $i="0".$i;
    }
   $jam.="<option value=".$i.">".$i."</option>";
   $i++;
}
for($i=0;$i<60;)
{
    if(strlen($i)<2){
        $i="0".$i;
    }
   $mnt.="<option value=".$i.">".$i."</option>";
   $i++;
}

$optSebabRusak="<option value='UMUM'>UMUM</option>";
$optSebabRusak.="<option value='KECELAKAAN'>KECELAKAAN</option>";

$optTraksi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGet=selectQuery($dbname,'organisasi','kodeorganisasi,namaorganisasi',"tipe='TRAKSI'");
$qGet=mysql_query($sGet) or die(mysql_error());
while($rGet=mysql_fetch_assoc($qGet)){
    $optTraksi.="<option value=".$rGet['kodeorganisasi'].">".$rGet['namaorganisasi']."</option>";
}

$optKaryawan="";
$sGet=selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan',"right(lokasitugas,2)='RO' and kodegolongan>='4C'");
$qGet=mysql_query($sGet) or die(mysql_error());
while($rGet=mysql_fetch_assoc($qGet)){
    $optKaryawan.="<option value=".$rGet['karyawanid'].">".$rGet['namakaryawan']."</option>";
}

$optKaryawan2="";
$sGet=selectQuery($dbname,'datakaryawan','karyawanid,namakaryawan',"lokasitugas='".$_SESSION['empl']['lokasitugas']."' and left(kodegolongan,1)>=4");
$qGet=mysql_query($sGet) or die(mysql_error());
while($rGet=mysql_fetch_assoc($qGet)){
    $optKaryawan2.="<option value=".$rGet['karyawanid'].">".$rGet['namakaryawan']."</option>";
}

#periode 
$optPer="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$i="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_wo order by periode desc limit 10";
$j=mysql_query($i) or die (mysql_error($conn));
while($k=mysql_fetch_assoc($j)) {
    $optPer.="<option value='".$k['periode']."'>".$k['periode']."</option>";
}

$optAlat = $optOperator = '';

echo"<fieldset style='width:500px;'>
    <legend>Work Order</legend>
    <table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['tanggal']."</td>
        <td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:100px;\"/></td>
    </tr>
    <tr><td>".$_SESSION['lang']['jam']."</td>
        <td><select id=jam>".$jam."</select>:<select id=mnt>".$mnt."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['kodetraksi']."</td>
        <td><select id=kodetraksi style='width:150px;' onchange=getAlat()>".$optTraksi."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['kodealat']."</td>
        <td><select id=kodealat style='width:150px;' onchange=getOperator()>".$optAlat."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['operator']."</td>
        <td><select id=operator style='width:150px;'>".$optOperator."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['posisihm']."</td>
        <td><input type=text id=posisihm value='0' onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
    </tr>
    <tr><td>".$_SESSION['lang']['namapelapor']."</td>
        <td><input type=text id=namapelapor onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
    </tr>
    <tr><td valign=top>".$_SESSION['lang']['indikasikerusakan']."</td>
        <td><textarea cols=35 rows=5 id=indikasikerusakan onkeypress=\"return tanpa_kutip(event);\"></textarea></td>
    </tr>
    <tr><td>".$_SESSION['lang']['penyebabrusak']."</td>
        <td><select id=penyebabrusak style='width:150px;' onchange='cekBA()'>".$optSebabRusak."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['noberitaacara']."</td>
        <td><select id=noberitaacara style='width:150px;'><option value=''></option></select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['hedept']."</td>
        <td><select id=hedept style='width:150px;'>".$optKaryawan."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['divmanager']."</td>
        <td><select id=divmanager style='width:150px;'>".$optKaryawan2."</select></td>
    </tr>
    <tr><td>".$_SESSION['lang']['workshop']."</td>
        <td><select id=workshop style='width:150px;'>".$optKaryawan."</select></td>
    </tr>
    </table>
    <input type=hidden value=insert id=method>
    <input type=hidden value='' id=notransaksi>
    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
    <button class=mybutton onclick=batal()>".$_SESSION['lang']['new']."</button>	 
    </table></fieldset>";
CLOSE_BOX();

OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
                ".$_SESSION['lang']['periode']." : <select id=perSch style=\"width:100px;\" onchange=loadData()>".$optPer."</select>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();
?>

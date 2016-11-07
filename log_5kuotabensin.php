<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/log_5kuotabensin.js'></script>

<?php
OPEN_BOX();

$optKary.="<option value=''></option>";
$sKary="select nik,namakaryawan,karyawanid from ".$dbname.".datakaryawan 
        where tanggalkeluar='0000-00-00' and kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'
        order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qKary)){
    $optKary.="<option value=".$rOpt['karyawanid'].">".$rOpt['nik']."-".$rOpt['namakaryawan']."</option>";
}

echo"<fieldset style='float:left;'>";
if($_SESSION['language']=='ID'){
    echo"<legend>Kuota Bensin</legend>";
}else{
    echo"<legend>Gasoline Quota</legend>";
}   
    echo"<table border=0 cellpadding=1 cellspacing=1>
            <tr>
                <td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td><select id=karyawanid name=karyawan style=width:250px>".$optKary."</select></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['kuotaperbulan']."</td><td>:</td><td><input type=text id=jumlahkuota value='' maxlength='5' onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:80px;\">&nbsp;Liter</td>
            </tr>
            <tr>
                <td colspan=2></td><td colspan=3><button class=mybutton onclick=simpan()>Simpan</button><button class=mybutton onclick=batal()>Batal</button></td>
            </tr>
    </table></fieldset>
    <input type=hidden id=bhs value='".$_SESSION['language']."'>
    <input type=hidden id=method value='insert'>";    

CLOSE_BOX();
?>

<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
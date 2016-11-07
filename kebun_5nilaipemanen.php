<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/kebun_5nilaipemanen.js'></script>

<?php
OPEN_BOX();

$optKary.="<option value=''></option>";
$sKary="select nik,namakaryawan,karyawanid from ".$dbname.".datakaryawan 
        where tanggalkeluar='0000-00-00' and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan=4
        order by namakaryawan asc";
if ($_SESSION['empl']['bagian']=='IT'){
    $sKary="select nik,namakaryawan,karyawanid from ".$dbname.".datakaryawan 
            where tanggalkeluar='0000-00-00' and tipekaryawan=4 order by namakaryawan asc";
}
$qKary=mysql_query($sKary) or die(mysql_error());
while($rOpt=mysql_fetch_assoc($qKary)){
    $optKary.="<option value=".$rOpt['karyawanid'].">".$rOpt['nik']."-".$rOpt['namakaryawan']."</option>";
}
$sPrd="select distinct periode from ".$dbname.".sdm_5periodegaji where left(periode,4)='".date("Y")."' and sudahproses=0 "
    . "and kodeorg in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by periode desc"; 
$qPrd=  mysql_query($sPrd) or die(mysql_error($conn));
while($rPrd=  mysql_fetch_assoc($qPrd)){
    $optPrd.="<option value='".$rPrd['periode']."'>".$rPrd['periode']."</option>";
}
$sPrd="select periode from ".$dbname.".sdm_5periodegaji where left(periode,4)='".date("Y")."'"
    . "and kodeorg='".$_SESSION['empl']['lokasitugas']."' order by periode desc"; 
$qPrd=  mysql_query($sPrd) or die(mysql_error($conn));
while($rPrd=  mysql_fetch_assoc($qPrd)){
    $optPrd2.="<option value='".$rPrd['periode']."'>".$rPrd['periode']."</option>";
}

$nilai=array('1'=>'A','2'=>'B','3'=>'C');
foreach($nilai as $key=>$val){
    $optNilai.="<option value=".$key.">".$val."</option>";
}

echo"<fieldset style='float:left;'>";
if($_SESSION['language']=='ID'){
    echo"<legend>Nilai Pemanen</legend>";
}else{
    echo"<legend>Harvester Grade</legend>";
}   
    echo"<table border=0 cellpadding=1 cellspacing=1>
            <tr>
                <td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td><select id=karyawanid name=karyawan style=width:250px>".$optKary."</select></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['periode']."</td><td>:</td><td><select id=periode name=periode style=width:80px>".$optPrd."</select></td>
            </tr>
            <tr>
                <td>".$_SESSION['lang']['nilai']."</td><td>:</td><td><select id=nilai name=nilai style=width:80px>".$optNilai."</select></td>
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
echo"<fieldset style=width:480px;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>
    <div style=float:left;><img height=20 src=images/orgicon.png title='".$_SESSION['lang']['list']."' onclick=displatList() ></div>
     <table border=0>
     <tr>
     <td>".$_SESSION['lang']['periode']."</td><td><select id=optper>".$optPrd2."</select></td>
     <td>".$_SESSION['lang']['namakaryawan']."</td><td><input type=text class=myinputtext  id=nmKar name=nmKar onkeypress=\"return validat(event);\" style=\"width:150px;\" /></td>
     <td colspan4><button onclick=loadData() class=mybutton>".$_SESSION['lang']['find']."</button>  </td>
     </tr>
     </table></fieldset>";
echo "<fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData()</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
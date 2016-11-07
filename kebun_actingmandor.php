<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src=js/kebun_actingmandor.js></script>
<?php
$arr="##afdId##nikMandor##nikMandorAct##periode##method";
include('master_mainMenu.php');
OPEN_BOX();
$optMandor2=$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGaji="select distinct tanggalmulai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$qGaji=mysql_query($sGaji) or die(mysql_error($conn));
$rGaji=mysql_fetch_assoc($qGaji);
$prd=explode("-",$rGaji['tanggalmulai']);
$tglgaji=$prd[0].$prd[1].$prd[2];
$whereKary = "a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
$whereKary .= " and (a.tanggalkeluar = '0000-00-00' or a.tanggalkeluar > ".$tglgaji.")";
$sMandor="select a.karyawanid,a.namakaryawan,a.nik,b.namajabatan from ".$dbname.".datakaryawan a ".
        "left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.namajabatan like '%mandor%' or ".
        "b.namajabatan like '%krani%' or b.namajabatan like '%recorder%'".
        " or b.namajabatan like '%pengawas%') and ".$whereKary.
        " order by a.nik asc";
$qMandor=  mysql_query($sMandor) or die(mysql_error($conn));
while($rMandor=  mysql_fetch_assoc($qMandor)){
    $optMandor.="<option value='".$rMandor['karyawanid']."'>".$rMandor['nik']."-".$rMandor['namakaryawan']."</option>";
}
$sMandor="select karyawanid,namakaryawan,nik from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."'  and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$tglgaji.") and tipekaryawan in ('3','4') order by nik asc";
$qMandor=  mysql_query($sMandor) or die(mysql_error($conn));
while($rMandor=  mysql_fetch_assoc($qMandor)){
    $optMandor2.="<option value='".$rMandor['karyawanid']."'>".$rMandor['nik']."-".$rMandor['namakaryawan']."</option>";
}
$sGaji2="select distinct tanggalmulai,periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$qGaji2=mysql_query($sGaji2) or die(mysql_error($conn));
while($rGaji2=mysql_fetch_assoc($qGaji2)){
    $optPeriode.="<option value='".$rGaji2['periode']."'>".$rGaji2['periode']."</option>";
}
$sAfd="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['lokasitugas']."' and tipe='AFDELING'";
$qAfd=mysql_query($sAfd) or die(mysql_error($conn));
while($rAfd=mysql_fetch_assoc($qAfd)){
	$optAfd.="<option value='".$rAfd['kodeorganisasi']."'>".$rAfd['kodeorganisasi']."-".$rAfd['namaorganisasi']."</option>";
}
echo"<fieldset style=float:left>
     <legend>".$_SESSION['lang']['actingmandor']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['afdeling']."</td>
	   <td><select id=afdId style=width:150px>".$optAfd."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['nikmandor']."</td>
	   <td><select id=nikMandor style=width:150px>".$optMandor2."</select></td>
	 </tr>
         <tr>
	   <td>".$_SESSION['lang']['periode']."</td>
	   <td><select id=periode style=width:150px>".$optPeriode."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['nikmandoracting']."</td>
	   <td><select id=nikMandorAct style=width:150px>".$optMandor2."</select></td>
	 </tr>
	 	
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('kebun_slave_actingmandor','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset>";
CLOSE_BOX();
OPEN_BOX();
echo"<fieldset style=float:left><legend>".$_SESSION['lang']['list']."</legend>
	 <div id=container>";
	 echo"<script>loadData()</script></div>"
. "</fieldset>";
CLOSE_BOX();
echo close_body();
?>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kebun_5basispanen.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','');
$optReg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sreg="select distinct regional from ".$dbname.".bgt_regional_assignment 
                where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$qreg=mysql_query($sreg) or die(mysql_error($conn));
while($rreg=mysql_fetch_assoc($qreg)){
    $optReg.="<option value='".$rreg['regional']."'>".$rreg['regional']."</option>";
    $regDt=$rreg['regional'];
}
$sreg2="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                where left(kodeorganisasi,4) 
                in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$regDt."') 
                and tipe='AFDELING'";
$qreg2=mysql_query($sreg2) or die(mysql_error($conn));
while($rreg2=mysql_fetch_assoc($qreg2)){
    $optReg.="<option value='".$rreg2['kodeorganisasi']."'>".$rreg2['namaorganisasi']."</option>";
}
$optagama='';
$arragama=getEnum($dbname,'kebun_5basispanen','jenis');
foreach($arragama as $kei=>$fal){
        $optagama.="<option value='".$kei."'>".$fal."</option>";
}  
$arrDt=array("0"=>"Tidak","1"=>"Iya");
foreach($arrDt as $kei=>$fal){
    $optdenda.="<option value='".$kei."'>".$fal."</option>";
}
echo"<fieldset style='float:left;'><legend>".$_SESSION['lang']['basispanen']."</legend><table>
     <tr><td>".$_SESSION['lang']['unit']."/".$_SESSION['lang']['regional']."</td>
         <td><select id=regId style=width:150px>".$optReg."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['jenis']."</td><td><select id=jnsId style=width:150px>".$optagama."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['bjr']."</td><td><input type=text id=bjr onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>    
         <tr><td>".$_SESSION['lang']['basisjjg']."</td><td><input type=text id=basisjjg onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>
         <tr><td>".$_SESSION['lang']['rpperkg']."</td><td><input type=text id=rpperkg onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>
         <tr><td>".$_SESSION['lang']['denda']."</td><td><select id=denda style=width:150px>".$optdenda."</select></td></tr>
         <tr><td>".$_SESSION['lang']['insentif']." ".$_SESSION['lang']['topografi']."</td><td><input type=text id=insentif onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=width:150px /></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
         <input type=hidden id=oldReg value=''>
         <input type=hidden id=oldJns value=''>
         <input type=hidden id=oldBjr value=''>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";

echo "<fieldset style='clear:both;float:left;'><legend>".$_SESSION['lang']['data']."</legend>";

echo "<div id=container>";
echo"<script>loadData(0)</script>";	
echo "</div></fieldset>";

CLOSE_BOX();
echo close_body();
?>
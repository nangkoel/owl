<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=Javascript1.2 src=js/log_5integrity.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
#organisasi
$optOrg='';
if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO'){
   $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi";
}
else{
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi";
}
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";
}
#periode
$optPeriode='';
$str="select distinct periode from ".$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."WH' order by periode desc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res)){
    $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
echo"<fieldset><legend>Inventory Integrity Check</legend>";
echo "<table>
    <tr><td>".$_SESSION['lang']['unit']."</td><td><select id=kodeorg>".$optOrg."</select></td></tr>
    <tr><td>".$_SESSION['lang']['periode']."</td><td><select id=periode>".$optPeriode."</select></td></tr>
</table>";         
echo "<button class=mybutton onclick=getNotSync()>".$_SESSION['lang']['preview']."</button>
            <button class=mybutton onclick=saveNotSync()>".$_SESSION['lang']['save']."</button>    
   </fieldset>";
CLOSE_BOX();

OPEN_BOX('',$_SESSION['lang']['list']);
echo"<fieldset><div id=container></div></fieldset>";
CLOSE_BOX();
echo close_body();
?>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/vhc_premiperawatan.js'></script>
<?php
include('master_mainMenu.php');
//ambil periode penggajian
$str="select distinct periode from ".$dbname.".sdm_5periodegaji 
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res)){
    $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
$optPremi="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$skdprem="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
          where induk='".$_SESSION['empl']['lokasitugas']."' and tipe='TRAKSI' order by namaorganisasi asc";
$qkdprem=mysql_query($skdprem) or die(mysql_error($conn));
while($rkdprem=mysql_fetch_assoc($qkdprem)){
    $optPremi.="<option value='".$rkdprem['kodeorganisasi']."'>".$rkdprem['namaorganisasi']."</option>";
}
OPEN_BOX('','<b>'.$_SESSION['lang']['premiperawatan'].'</b>');

$frm[0].="<fieldset><legend>Form</legend>
              <table>
              <tr><td>".$_SESSION['lang']['periode']."<td><td><select id=periode style=width:150px>".$optPeriode."</select></td></tr> 
              <tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>
              <tr><td>".$_SESSION['lang']['kodetraksi']."<td><td><select id=kdpremi style=width:150px>".$optPremi."</select></td></tr>     
             </table>
             <button class=mybutton onclick=getData()>".$_SESSION['lang']['preview']."</button>
             <button class=mybutton onclick=getExcel(event,'vhc_slave_premiperawatan.php','RAWATKD')>".$_SESSION['lang']['excel']."</button>
             </fieldset>
             <div id=container style='width:850px;height:400px;overflow:scroll;'>
             </div>";
 



//========================
$hfrm[0]=$_SESSION['lang']['form'];
 

//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,300,900);
//===============================================
CLOSE_BOX();
echo close_body();
?>
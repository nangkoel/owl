<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/kebun_premimandorbaru.js'></script>
<?php
include('master_mainMenu.php');
//ambil periode penggajian
$str="select distinct periode from ".$dbname.".sdm_5periodegaji 
      where kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=0 order by periode desc";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res)){
    $optPeriode.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
//ambil organisasi
$str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$_SESSION['empl']['kodeorganisasi']."'";
$res=mysql_query($str);
while($bar=  mysql_fetch_object($res)){
    $optOrg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
}
 
$tipDt=array("nikmandor"=>"MANDOR","nikasisten"=>"RECORDER","keranimuat"=>"KERANI","nikmandor1"=>"CONDUCTOR");
foreach($tipDt as $lstDt=>$nmdt){
	$optTipedt.="<option value='".$lstDt."'>".$nmdt."</option>";
}
OPEN_BOX('','<b>'.$_SESSION['lang']['premimandor'].'</b>');
$frm[0].="<fieldset><legend>Form</legend>
              <table>
              <tr><td>".$_SESSION['lang']['periode']."<td><td><select id=periode style=width:150px>".$optPeriode."</select></td></tr> ";
if($_SESSION['empl']['bagian']=='IT'){
    $frm[0].="<tr><td>".$_SESSION['lang']['kodeorg']."<td><td><select id=kodeorg style=width:150px>".$optOrg."</select></td></tr>";
} else {
    $frm[0].="<tr><td>".$_SESSION['lang']['kodeorg']."<td><td><input type=text id=kodeorg disabled class=myinputtext value='".$_SESSION['empl']['lokasitugas']."'></td></tr>";
}

$frm[0].="<tr><td>".$_SESSION['lang']['tipe']."<td><td><select id=tpDt style=width:150px>".$optTipedt."</select></td></tr> 
	      <tr><td>".$_SESSION['lang']['hk']."<td><td><input type=text id=hkpanen class=myinputtextnumber onkeypress=\"return angka_doang(event);\" value='23'></td></tr> 
             </table>
             <button class=mybutton onclick=getData()>".$_SESSION['lang']['preview']."</button>
			 <button class=mybutton onclick=getExcel(event,'kebun_slave_premimandorbaru.php')>".$_SESSION['lang']['excel']."</button>
             </fieldset>
             <div id=container style='width:1000px;height:400px;overflow:scroll;'>
             </div>";
/* $frm[1].="<fieldset style='float:left;'><legend>".$_SESSION['lang']['premimandor']."</legend>
		  <table class=sortable cellspacing=1 border=0>
		  <thead>
		  <tr class=rowheader>
		  <td>No.</td>
		  <td>".$_SESSION['lang']['periode']."</td>
		  <td>".$_SESSION['lang']['tipe']."</td>
		  <td>".$_SESSION['lang']['action']."</td>
		   </tr>
		  <tbody id=containerlist><script>loadKemandoran(0)</script>";
$frm[1].="</tbody>
			<tfoot id=footerDt>
			</tfoot>
			</table>"; */



//========================
$hfrm[0]=$_SESSION['lang']['form'];
//$hfrm[1]=$_SESSION['lang']['daftar'];

//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,300,1020);
//===============================================
CLOSE_BOX();
echo close_body();
?>


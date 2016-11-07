<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/setup_5qcpenilaian.js'></script>
<?php
$optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTipe="select distinct * from ".$dbname.".qc_5parameter where tipe!='PUPUK' order by id asc";
$qTipe=mysql_query($sTipe) or die(mysql_error($conn));
while($rTipe=mysql_fetch_assoc($qTipe))
{
    $optTipe.="<option value='".$rTipe['id']."'>".$rTipe['tipe']."-".$rTipe['nama']."(".$rTipe['satuan'].")</option>";
}
$frm[0]='';
$frm[1]='';
$arr="##tipeDt##maxData##nilData##method";
$arr2="##kdData##nmData##nilData2##method2##maxData2";
include('master_mainMenu.php');
OPEN_BOX();

$frm[0].="<fieldset>
     <legend>".$_SESSION['lang']['qcnilai']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tipe']."</td>
	   <td><select id=tipeDt style=\"width:150px;\"  >".$optTipe."</select></td>
	 </tr>
	 <tr>
	   <td>Max</td>
	   <td>
           <input type=text class=myinputtextnumber id=maxData name=maxData onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" />".$_SESSION['lang']['warning']." </td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['nilai']."</td>
	   <td><input type=text class=myinputtextnumber id=nilData name=nilData  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=45></td>
	 </tr>	 
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('setup_slave_5qcpenilaian','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idData2 name=idData2 value='' />";

$frm[0].="<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['tipe']."</td>
	   <td>Max</td>
	   <td>".$_SESSION['lang']['nilai']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

$frm[0].="</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";

$frm[1].="<fieldset>
     <legend>".$_SESSION['lang']['qcnilaipupuk']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['kodeabs']."</td>
	   <td><input type=text class=myinputtext id=kdData onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=5></td>
	 </tr>
         <tr>
	   <td>".$_SESSION['lang']['nama']."</td>
	   <td><input type=text class=myinputtext id=nmData onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=30></td>
	 </tr>
	 <tr>
	   <td>Max</td>
	   <td>
           <input type=text class=myinputtextnumber id=maxData2 name=maxData onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" />".$_SESSION['lang']['warning']."</td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['nilai']."</td>
	   <td><input type=text class=myinputtextnumber id=nilData2 name=nilData2  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=45></td>
	 </tr>
	 </table>
	 <input type=hidden value=insert id=method2>
	 <button class=mybutton onclick=saveFranco2('setup_slave_5qcpenilaian2','".$arr2."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi2()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idData3 name=idData3 value='' />";

$frm[1].="<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['kode']."</td>
           <td>".$_SESSION['lang']['nama']."</td>
	   <td>Max</td>
	   <td>".$_SESSION['lang']['nilai']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container2>";
	 

$frm[1].="</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
$hfrm[0]=$_SESSION['lang']['qcnilai'];
$hfrm[1]=$_SESSION['lang']['qcnilaipupuk'];

//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,700);
?>
<?php
CLOSE_BOX();
echo close_body();
?>
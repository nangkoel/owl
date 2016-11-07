<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/setup_5qcparameter.js'></script>
<?php
$arrTipe=array('ANCAK','BUAH','PUPUK','TANAM','TBM','TM');
$optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
foreach($arrTipe as $dtTipe)
{
    $optTipe.="<option value='".$dtTipe."'>".$dtTipe."</option>";
}
$arr="##tipeDt##idData##nmQc##klmpkQc##satuan##method";
include('master_mainMenu.php');
OPEN_BOX();

echo"<fieldset>
     <legend>QC Parameter</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tipe']."</td>
	   <td><select id=tipeDt style=\"width:150px;\" onchange=getData() >".$optTipe."</select></td>
	 </tr>
	 <tr>
	   <td>Id</td>
	   <td><input type=text class=myinputtextnumber id=idData name=idData onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" /> </td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['nama']."</td>
	   <td><input type=text class=myinputtext id=nmQc name=nmQc  onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=45></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['kelompok']."</td>
	   <td><input type=text class=myinputtext id=klmpkQc name=klmpkQc  onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=45></td>
	 </tr>
         <tr>
	   <td>".$_SESSION['lang']['satuan']."</td>
	   <td><input type=text class=myinputtext id=satuan name=satuan  onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" maxlength=15></td>
	 </tr>
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('setup_slave_5qcparameter','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$str="select * from ".$dbname.".setup_franco order by id_franco desc";
$res=mysql_query($str);
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>".$_SESSION['lang']['tipe']."</td>
	   <td>ID</td>
	   <td>".$_SESSION['lang']['nama']."</td>
	   <td>".$_SESSION['lang']['kelompok']."</td>
	   <td>".$_SESSION['lang']['satuan']."</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>
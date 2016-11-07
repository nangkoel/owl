<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/sdm_5standarUsaku.js'></script>
<?php
$arr="##thnBudget##kdGol##ungSaku##ungMkn##htel##method";
include('master_mainMenu.php');
OPEN_BOX();
$optGol="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sGol="select * from ".$dbname.".sdm_5golongan order by namagolongan asc";
$qGol=mysql_query($sGol) or die(mysql_error($sGol));
while($rGol=  mysql_fetch_assoc($qGol))
{
    $optGol.="<option value='".$rGol['kodegolongan']."'>".$rGol['namagolongan']."</option>";
}

echo"<input type='hidden' id='method' name='method' value='insert' />";

echo"<fieldset style=width:250px;>
     <legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['standarduangsaku']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td><input type=text class=myinputtextnumber id=thnBudget name=thnBudget onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=4 /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['kodegolongan']."</td>
	   <td><select id=kdGol name=kdGol style=\"width:150px;\" >".$optGol."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['uangsaku']."</td>
	   <td><input type=text class=myinputtextnumber id=ungSaku name=ungSaku style=\"width:150px;\" onkeypress=\"return angka_doang(event);\"  maxlength=20/></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['uangmakan']."</td>
	   <td><input type=text class=myinputtextnumber id=ungMkn name=ungMkn  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	 
	  <tr>
	   <td>".$_SESSION['lang']['hotel']."</td>
	   <td><input type=text class=myinputtextnumber id=htel name=htel  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=20></td>
	 </tr>	
	 </table>
	 
	 <button class=mybutton onclick=saveFranco('sdm_slave_5standardUsaku','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=idFranco name=idFranco />";
CLOSE_BOX();
OPEN_BOX();
$optData="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$str="select distinct tahunbudget from ".$dbname.".sdm_5sakupjd order by tahunbudget desc";
$res=mysql_query($str) or die(mysql_error($str));
while($rData=mysql_fetch_assoc($res))
{
    $optData.="<option value='".$rData['tahunbudget']."'>".$rData['tahunbudget']."</option>";
}
echo"<table><tr>
    <td>".$_SESSION['lang']['budgetyear']." <select id=thnBudgetHead style='width:100px' onchange='loadData()'>".$optData."</select></td>
    <td>".$_SESSION['lang']['kodegolongan']." <select id=kdGOlHead style='width:100px' onchange='loadData()'>".$optGol."</select></td>
    
    </tr></table>";
echo"<fieldset style='width:450px;'><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td>".$_SESSION['lang']['kodegolongan']."</td>
	   <td>".$_SESSION['lang']['uangsaku']."</td>
	   <td>".$_SESSION['lang']['uangmakan']."</td>
	   <td>".$_SESSION['lang']['hotel']."</td>
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
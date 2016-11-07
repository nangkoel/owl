<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/budget_5harikerja.js'></script>

<?php
$arr="##tahunbudget##hrsetahun##hrminggu##hrlibur##hrliburminggu##hkeffektif##method##oldtahunbudget";

include('master_mainMenu.php');
OPEN_BOX();

echo"<fieldset>
     <legend>".$_SESSION['lang']['jumlah']." ".$_SESSION['lang']['hk']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td><input type=text class=myinputtextnumber id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=4 /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['JumHrSetahun']."</td>
	   <td><input type=text class=myinputtextnumber id=hrsetahun name=hrsetahun onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=4 value=365 onchange=tambah() /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['JumHrMinggu']."</td>
	   <td><input type=text class=myinputtextnumber id=hrminggu name=hrminggu onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['JumHrLibur']."</td>
	   <td><input type=text class=myinputtextnumber id=hrlibur name=hrlibur onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['JumHrLiburMinggu']."</td>
	   <td><input type=text class=myinputtextnumber id=hrliburminggu name=hrliburminggu onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=100 onchange=tambah() /></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['hkefektif']."</td>
	   <td><input type=text class=myinputtextnumber id=hkeffektif name=hkeffektif onkeypress=\"return tanpa_kutip(event);\" style=\"width:100px;\" maxlength=100 disabled /></td>
	 </tr>
	 </table>
		 <input type=hidden value=insert id=method>
		 <button class=mybutton onclick=savehk('log_slave_budget_5harikerja','".$arr."')>".$_SESSION['lang']['save']."</button>
		 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset><input type='hidden' id=oldtahunbudget name=oldtahunbudget />";
CLOSE_BOX();

OPEN_BOX();
$str="select * from ".$dbname.".bgt_hk order by tahunbudget desc";
$res=mysql_query($str);
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['budgetyear']."</td>
	   <td>".$_SESSION['lang']['JumHrSetahun']."</td>
	   <td>".$_SESSION['lang']['JumHrMinggu']."</td>
	   <td>".$_SESSION['lang']['JumHrLibur']."</td>
	   <td>".$_SESSION['lang']['JumHrLiburMinggu']."</td>
	   <td>".$_SESSION['lang']['hkefektif']."</td>
	   <td>".$_SESSION['lang']['action']."</td>
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
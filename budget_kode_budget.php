<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/budget_kode_budget.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['input'].' '.$_SESSION['lang']['kodeanggaran']);
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
if($_SESSION['language']=='ID'){
    $dd='namaakun';
}else{
    $dd='namaakun1 as namaakun';
}
$sJns="select noakun,".$dd." from ".$dbname.".keu_5akun where CHAR_LENGTH(noakun)>5  order by noakun asc";
$qJns=mysql_query($sJns) or die(mysql_error($conn));
while($rJns=mysql_fetch_assoc($qJns))
{
    $optAkun.="<option value='".$rJns['noakun']."'>".$rJns['noakun']." - [".$rJns['namaakun']."]</option>";
}
echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td><input type='text' id='kode' class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style='width:150px' /></td></tr>
	 <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=nama size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
             <tr><td>".$_SESSION['lang']['noakun']."</td><td><select id='noakunId' style='width:150px'>".$optAkun."</select></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);

	$str1="select * from ".$dbname.".bgt_kode   order by kodebudget 	";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeanggaran']."</td><td>".$_SESSION['lang']['nama']."</td><td>".$_SESSION['lang']['noakun']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodebudget."</td><td>".$bar1->nama."</td><td>".$bar1->noakun."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodebudget."','".$bar1->nama."','".$bar1->noakun."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";

echo close_theme();
CLOSE_BOX();
echo close_body();
?>
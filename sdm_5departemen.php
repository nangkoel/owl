<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/departement.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['input'].' '.$_SESSION['lang']['departemen']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kode']."</td><td><input type=text id=kode size=3  maxlength5 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=nama size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelDep()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['avaidepartement']);

	$str1="select * from ".$dbname.".sdm_5departemen order by kode";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kode']."</td><td>".$_SESSION['lang']['nama']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kode."</td><td>".$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->nama."');\"></td></tr>";
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
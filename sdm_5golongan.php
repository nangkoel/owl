<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/golongan.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['pengaturangolongan']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['levelcode']."</td><td><input type=text id=kodegolongan size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['levelname']."</td><td><input type=text id=namagolongan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanGolongan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelGolongan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availavel']);
echo "<div id=container>";
	$str1="select * from ".$dbname.".sdm_5golongan order by kodegolongan";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['levelcode']."</td><td>".$_SESSION['lang']['levelname']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodegolongan."</td><td>".$bar1->namagolongan."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodegolongan."','".$bar1->namagolongan."');\"></td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>
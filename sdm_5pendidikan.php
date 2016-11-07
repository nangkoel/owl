<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/pendidikan.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['educationentry']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['edulevel']."</td><td><input type=text id=edulevel size=3 maxlength=2 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['eduname']."</td><td><input type=text id=eduname size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['edugroup']."</td><td><input type=text id=edugroup size=8  maxlength=4 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	  <input type=hidden id=eduid value=''>
	 <button class=mybutton onclick=simpanPendidikan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelPendidikan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availedu']);
echo "<div id=container>";
	$str1="select * from ".$dbname.".sdm_5pendidikan order by levelpendidikan";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:600px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['edulevel']."</td><td>".$_SESSION['lang']['eduname']."</td><td>".$_SESSION['lang']['edugroup']."</td><td style='width:70px;'>*</td></tr>
		 </thead>
		 <tbody>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->levelpendidikan."</td><td>".$bar1->pendidikan."</td><td>".$bar1->kelompok."</td><td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->levelpendidikan."','".$bar1->pendidikan."','".$bar1->kelompok."',".$bar1->idpendidikan.");\"> <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPendidikan(".$bar1->idpendidikan.");\"></td></tr>";
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
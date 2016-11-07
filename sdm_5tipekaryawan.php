<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/tipekaryawan.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['tipekaryawan']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['id']."</td><td><input type=text id=kode size=3  maxlength5 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['tipekaryawan']."</td><td><input type=text id=nama size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanTipeKar()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelTipeKar()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['list'] .' '.$_SESSION['lang']['tipekaryawan']);

	$str1="select * from ".$dbname.".sdm_5tipekaryawan order by id";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['id']."</td><td>".$_SESSION['lang']['tipekaryawan']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->id."</td><td>".$bar1->tipe."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->id."','".$bar1->tipe."');\"></td></tr>";
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
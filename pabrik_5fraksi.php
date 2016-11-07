<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/pabrik_5fraksi.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['kodefraksi']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeabs']."</td><td><input type=text id=kode size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text id=nama size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
                    <tr><td>".$_SESSION['lang']['nama']."(EN)</td><td><input type=text id=nama1 size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     <tr><td>".$_SESSION['lang']['satuan']."</td><td><input type=text id=satuan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJabatan()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJabatan()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme();
echo "<div>";
	$str1="select * from ".$dbname.".pabrik_5fraksi order by kode";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader><td style='width:150px;'>".$_SESSION['lang']['kodeabs']."</td><td>".$_SESSION['lang']['nama']."</td><td>".$_SESSION['lang']['nama']."(EN)</td><td>".$_SESSION['lang']['satuan']."</td><td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kode."</td><td>".$bar1->keterangan."</td><td>".$bar1->keterangan1."</td><td>".$bar1->type."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->keterangan."','".$bar1->type."','".$bar1->keterangan1."');\"></td></tr>";
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
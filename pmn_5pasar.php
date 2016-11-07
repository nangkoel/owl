<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<link rel="stylesheet" type="text/css" href="style/zTable.css">
<script language=javascript1.2 src='js/pmn_5pasar.js'></script>
<?php

include('master_mainMenu.php');
OPEN_BOX('','');

echo"<fieldset style='width:500px;'>
    <legend><b>Pasar <span id=modeStr>: Add Mode</span></b></legend>
	<input type=hidden id=idPasar>
	<input type=hidden id=mode value=insert>
    <table>
        <tr><td>Nama Pasar</td>
        <td><input type=text id=namapasar size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
    </table>
    <input type=hidden id=method value='insert'>
    <button class=mybutton onclick=simpanDep()>".$_SESSION['lang']['save']."</button>
	<button class=mybutton onclick=addMode() id=addModeBtn disabled>".$_SESSION['lang']['addmode']."</button>
</fieldset>";
echo open_theme($_SESSION['lang']['list']);

	$str1="select * from ".$dbname.".pmn_5pasar
        order by namapasar";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:300px;'>
	     <thead>
		 <tr class=rowheader><td>Pasar</td><td></td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
            <td align=center>".$bar1->namapasar."</td>
            <td>
                   <img src=images/skyblue/edit.png class=zImgBtn  caption='Edit' onclick=\"editField(".$bar1->id.",'".$bar1->namapasar."');\">
                   </td></tr>";
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
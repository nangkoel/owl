<?php //@Copy nangkoelframework
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/keu_5akunbank.js'></script>
<?php
if($_SESSION['language']=='EN'){
    $zz="namaakun1 as namaakun";
}else{
    $zz="namaakun";
}
$optAkun="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sAkun="select  noakun,".$zz." from ".$dbname.".keu_5akun where noakun!='11102' and noakun like '11102%' order by noakun desc";
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $optAkun.="<option value='".$rAkun['noakun']."'>".$rAkun['namaakun']."</option>";
}
include('master_mainMenu.php');
OPEN_BOX('');

echo"<fieldset style='width:500px;'><table>
	 <tr><td>".$_SESSION['lang']['noakun']."</td><td><select id=grup style=width:150px>".$optAkun."</select></td></tr>
     <tr><td>".$_SESSION['lang']['namaakun']."</td><td><input type=text id=jumlahhk maxlength=80 style=width:150px onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme('');
echo "<div>";
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['noakun']."</td>
			<td>".$_SESSION['lang']['namaakun']."</td>
			
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
                echo"<script>loadData()</script>";
		echo" </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>
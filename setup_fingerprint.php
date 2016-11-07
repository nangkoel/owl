<?php //@Copy nangkoelframework
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/setup_fingerprint.js'></script>
<?php
$optKar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKar="select  karyawanid, namakaryawan from ".$dbname.".datakaryawan where lokasitugas like '%HO' and ((tanggalkeluar = '0000-00-00') or (tanggalkeluar is NULL))
    order by namakaryawan";
$qKar=mysql_query($sKar) or die(mysql_error($conn));
while($rKar=mysql_fetch_assoc($qKar))
{ 
    $optKar.="<option value='".$rKar['karyawanid']."'>".$rKar['namakaryawan']."</option>";
}
$optDev="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKar="select * from ".$dbname.".sdm_5fingerprint";
$qKar=mysql_query($sKar) or die(mysql_error($conn));
while($rKar=mysql_fetch_assoc($qKar))
{ 
    $optDev.="<option value='".$rKar['deviceid']."'>".$rKar['keterangan']."</option>";
}
include('master_mainMenu.php');
OPEN_BOX('');

echo"<fieldset style='width:300px;'><table>
	 <tr><td>".$_SESSION['lang']['namakaryawan']."</td><td><select id=karyawanid style=width:150px>".$optKar."</select></td></tr>
     <tr><td>PIN Fingerprint</td><td><input type=text id=pin maxlength=80 style=width:150px onkeypress='return tanpa_kutip(event);' class=myinputtext></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme('');
echo "<div>";
    echo"<table class=sortable cellspacing=1 border=0 style='width:300px;'>
    <thead>
        <tr class=rowheader>
        <td style='width:150px;'>".$_SESSION['lang']['namakaryawan']."</td>
        <td>PIN Fingerprint</td>
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
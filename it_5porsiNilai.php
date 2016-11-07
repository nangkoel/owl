<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/it_slave_5porsiNilai.js'></script>
<?php

include('master_mainMenu.php');
OPEN_BOX('',"Standard Kepuasan");
$arr="##kode##jmlhPorsi##method";
$optagama="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optank=$optagama;
$arragama=getEnum($dbname,'it_presentasenilai','kode');
foreach($arragama as $kei=>$fal)
{
        $optagama.="<option value='".$kei."'>".$fal."</option>";
}  
 
echo"<fieldset style='width:500px;'><table>
<tr><td>".$_SESSION['lang']['kodeabs']."</td><td><select id=kode style=width:105px>".$optagama."</select></td></tr>
<tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jmlhPorsi size=45  maxlength=45 onkeypress=\"return angka_doang(event);\" class=myinputtext></td></tr>
           
     </table>
	 <input type=hidden id=method value='insert'>
	  <input type=hidden id=eduid value=''>
	 <button class=mybutton onclick=simpanPendidikan('it_slave_5porsiNilai','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme();
echo "<div id=container>";
echo"<script>loadData()</script>";
echo "</div>";
echo close_theme();
CLOSE_BOX();
echo close_body();
?>
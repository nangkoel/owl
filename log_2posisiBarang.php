<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/log_2posisiBarang.js'></script>
<script language="javascript" src="js/zMaster.js"></script>
<!--<link rel=stylesheet type=text/css href="style/zTable.css">-->
<script language=javascript src='js/iReport.js'></script>
<script language=javascript src=js/zTools.js></script>

<?php
$arr="##nopo";	
OPEN_BOX('',"<b>Laporan Posisi Barang</b>"); //1 O
echo"<table>
     <tr>
	
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo " ".$_SESSION['lang']['nopo']." :<input type=text id=nopo size=25 maxlength=30 disabled class=myinputtext>
			<img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=tmblCariNoPo class=resicon onclick=cariNoPo('".$_SESSION['lang']['find']."',event)>
		
		<button onclick=iPreview('log_slave_2posisiBarang','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=iExcel(event,'log_slave_2posisiBarang.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
CLOSE_BOX();


OPEN_BOX();
echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer'  >
</div></fieldset>";//style='overflow:auto;height:350px;max-width:1500px';
CLOSE_BOX();
echo close_body();					
?>
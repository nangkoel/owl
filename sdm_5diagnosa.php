<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_rumahsakit.js></script>
<link rel=stylesheet type=text/css href=style/medocal.css>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.$_SESSION['lang']['diagnosabaru'].'</b>');
echo OPEN_THEME($_SESSION['lang']['formdiagnosa']);
echo "<br>Id &nbsp &nbsp : <input type=text class=myinputtext id=idx disabled size=3><br>";
echo "Name:<input type=text class=myinputtext id=name maxlength=100 size=45 onkeypress=\"return tanpa_kutip(event);\"><br>
      <br><button class=mybutton onclick=saveDiagnosa()>".$_SESSION['lang']['save']."</button>";

echo CLOSE_THEME();
CLOSE_BOX();
OPEN_BOX('',$_SESSION['lang']['list'].':');
$str="select * from ".$dbname.".sdm_5diagnosa order by diagnosa";
$res=mysql_query($str);
echo"<div style='wicth=100%; height:300px;overflow:scroll;'>
     <table class=sortable cellspacing=1 border=0>
     <thead>
	   <tr class=rowheader><td>Id</td><td>".$_SESSION['lang']['nama']."</td><td>Edit</td></tr>
	 </tr>
	 </thead>
	 <tbody id=tbody>";
while($bar=mysql_fetch_object($res))
{
	echo"<tr class=rowcontent>
	      <td class=firsttd>".$bar->id."</td>
		  <td>".$bar->diagnosa."</td>
		  <td><img src=images/edit.png align=middle style='cursor:pointer;' onclick=\"editDiagnosa('".$bar->id."','".$bar->diagnosa."');\" height=17px align=right title='Edit data for ".$bar->diagnosa."'></td>
	     </tr>";
}
echo "</tbody>
      <tfoot>
	  </tfoot>
	  </table>
	  </div>";
CLOSE_BOX();
echo close_body();
?>
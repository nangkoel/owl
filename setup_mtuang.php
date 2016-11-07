<?php //@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>

<script language=javascript1.2 src='js/setup_mtuang.js'></script>

<?php
OPEN_BOX('',"<font size=3><u><b>Mata Uang</b></u><font>");

echo"<br /><br /><fieldset style='float:left;'>
		<legend><font size=2.5><b>Header Mata Uang</b></legend></font>		
			<table class=sortable cellspacing=1 border=0>
				<tr class=rowheader>		
					<td align=center>Kode Jurnal</td>
					<td align=center>Mata Uang</td>
					<td align=center>Simbol</td>
					<td align=center>Kode ISO</td>
					<td align=center>*</td>
				</tr>";
				
			
$ha="select * from ".$dbname.".setup_matauang";
$hi=mysql_query($ha) or die (mysql_error($conn));
while($hu=mysql_fetch_assoc($hi))
{
	echo"<tr class=rowcontent>
			<td><input type=text maxlength=3 id=kode".$hu['kode']." value=".$hu['kode']." onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=matauang".$hu['kode']." value=".$hu['matauang']." onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=simbol".$hu['kode']." value=".$hu['simbol']." onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=kodeiso".$hu['kode']." value=".$hu['kodeiso']." onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td>
				<img src=images/application/application_edit.png class=resicon  title='Update' onclick=\"edithead('".$hu['kode']."');\" >
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delhead('".$hu['kode']."','".$hu['matauang']."','".$hu['simbol']."','".$hu['kodeiso']."');\" >
				<img src=images/application/application_go.png class=resicon  title='View' onclick=loadData('".$hu['kode']."')>
			
			</td>
     	</tr>";
}
			echo"<tr class=rowcontent>
			<td><input type=text maxlength=3 id=kodetambah onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=matauangtambah onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=simboltambah onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><input type=text  id=kodeisotambah onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:75px;\"></td>
			<td><img src=images/application/application_add.png class=resicon  title='Save'  onclick=simpanbaru()></td>
			</tr>";
			echo"</table></fieldset>
					<input type=hidden id=method value='insert'>";//application_add
					
					
echo "<fieldset style='float:left;'>
		<legend><font size=2.5><b>Detail Mata Uang</b></legend></font>
		<input type=hidden id=kodedetail value=''>
		<div id=container> 
			
		</div>
	</fieldset>";//<script>loadData()</script>

CLOSE_BOX();
echo close_body();					
?>
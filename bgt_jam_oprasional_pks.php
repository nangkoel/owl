<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>


<script language=javascript1.2 src=js/bgt_jam_oprasional_pks.js></script>


<?php
include('master_mainMenu.php');
OPEN_BOX();
//'',$_SESSION['lang']['input'].' '.$_SESSION['lang']['']
?>


<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
$optws="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>

<?php
echo"<fieldset style='width:500px;'>
	  <legend>Jam Operasional PKS</legend><table>
      <tr><td>Tahun Budget</td><td>:</td><td><input type=text class=myinputtextnumber id=thnbudget name=thnbudget onkeypress=\"return angka_doang(event);\" style=\"width:200px;\" maxlength=4 /></td></tr>
		 <tr><td>Kode PKS</td><td>:</td><td><select id=kdpks name=kdpks style=\"width:200px;\">".$optOrg."</select></td></tr>
		 <tr><td>Jam Olah/Tahun</td><td>:</td><td><input type=text class=myinputtextnumber id=jamo name=jmo onkeypress=\"return angka_doang(event);\" style=\"width:200px;\"  /></td></tr>
		 <tr><td>Jam Breakdown/Tahun</td><td>:</td><td><input type=text class=myinputtextnumber id=jamb name=jamb onkeypress=\"return angka_doang(event);\" style=\"width:200px;\"  /></td></tr>
     </table> 
	 <table>
	  <tr>
		 <td style='width:130px;'></td>
			 <input type=hidden id=method value='insert'>
		 <td>
			 <button class=mybutton onclick=simpanpks()>".$_SESSION['lang']['save']."</button>
			 <button class=mybutton onclick=batalpks()>".$_SESSION['lang']['cancel']."</button>
		 <td>
	  <tr>
	 </table>
	 </fieldset>";
echo open_theme($_SESSION['lang']['datatersimpan']);

	$str1="select * from ".$dbname.".bgt_jam_operasioal_pks order by tahunbudget ";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		     <td style='width:5px'>No</td>
			 <td style='width:75px;'>Tahun Budget</td>
			 <td style='width:75px'>Kode PKS</td>
			 <td style='width:75px'>Total Jam</td>
			 <td style='width:75px'>Total Breakdown</td>
			 <td style='width:30px;'>Aksi</td>
		 </tr>		 
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;	
		echo"<tr class=rowcontent>
			<td align=center>".$no."</td>
			<td align=right>".$bar1->tahunbudget."</td>
			<td align=center>".$bar1->millcode."</td>
			<td align=right>".$bar1->jamolah."</td>
			<td align=right>".$bar1->breakdown."</td>			
			<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->millcode."','".$bar1->jamolah."','".$bar1->breakdown."');\"></td>
		</tr>";
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
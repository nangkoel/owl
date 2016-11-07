<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>


<script language=javascript1.2 src=js/borong_panen.js></script>

<?php
include('master_mainMenu.php');
OPEN_BOX();
//'',$_SESSION['lang']['input'].' '.$_SESSION['lang']['']
?>


<?php
$optOrg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi where tipe='AFDELING' and induk='".$_SESSION['empl']['lokasitugas']."' ORDER BY kodeorganisasi";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optOrg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}
$optws="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
?>


<?php
echo"<fieldset style='width:275px;'>
	  <legend><b>".$_SESSION['lang']['borongpanen']."</b></legend><table>
      <tr><td style='width:90px;'>".$_SESSION['lang']['budgetyear']."<td style='width:10px;'>:</td></td><td><input type=text id=tahunbudget size=10 onkeypress=\"return angka(event,'0123456789');validatefn(event);\" class=myinputtext maxlength=4 style=\"width:150px;\"></td></tr>
		 <tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td><select id=kodeorg name=kodeorg style=\"width:150px;\">".$optOrg."</select></td></tr>
		 <tr><td>".$_SESSION['lang']['siapborong']."</td><td>:</td><td><input type=text class=myinputtextnumber id=sb name=sb onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  /></td></tr>
		 <tr><td>".$_SESSION['lang']['lebihborong']."</td><td>:</td><td><input type=text class=myinputtextnumber id=lb name=lb onkeypress=\"return angka_doang(event);\" style=\"width:150px;\"  /></td></tr>
     </table> 
	 <table>
	  <tr>
		 <td style='width:103px;'></td>
			 <input type=hidden id=method value='insert'>
			<input type=hidden id=oldtahunbudget value='insert'>
			<input type=hidden id=oldkodeorg value='insert'>
		 <td>
			 <button class=mybutton onclick=simpanbor()>".$_SESSION['lang']['save']."</button>
			 <button class=mybutton onclick=batalbor()>".$_SESSION['lang']['cancel']."</button>
		 <td>
	  <tr>
	 </table>
	 </fieldset>";
	 
	 
echo open_theme($_SESSION['lang']['datatersimpan']);

/*	$str1="select * from ".$dbname.".bgt_borong_panen order by tahunbudget ";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		     <td style='width:5px'>No</td>
			 <td style='width:75px;'>Tahun Budget</td>
			 <td style='width:75px'>Kode Afdeling</td>
			 <td style='width:75px'>Siap Borong</td>
			 <td style='width:75px'>Lebih Borong</td>
			 <td style='width:30px;'>*</td>
		 </tr>		 
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;	
		echo"<tr class=rowcontent>
			<td align=center>".$no."</td>
			<td align=right>".$bar1->tahunbudget."</td>
			<td align=center>".$bar1->kodeorg."</td>
			<td align=right>".$bar1->siapborong."</td>
			<td align=right>".$bar1->lebihborong."</td>			
			<td align=center><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->tahunbudget."','".$bar1->kodeorg."','".$bar1->siapborong."','".$bar1->lebihborong."');\"></td>
		</tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";*/
	echo "<div id=container>";
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		     <td style='width:5px'>".substr($_SESSION['lang']['nomor'],0,2)."</td>
			 <td style='width:100px;'>".$_SESSION['lang']['budgetyear']."</td>
			 <td style='width:100px'>".$_SESSION['lang']['afdeling']."</td>
			 <td style='width:100px'>".$_SESSION['lang']['siapborong']."</td>
			 <td style='width:100px'>".$_SESSION['lang']['lebihborong']."</td>
			 <td style='width:30px;'>".$_SESSION['lang']['edit']."</td></tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>";
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
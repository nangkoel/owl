<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/departement.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','Standar Fraksi');

echo"<fieldset style='width:500px;'><table>
     <tr><td>Kode</td><td><input type=text id=kode size=15  maxlength=15 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>Keterangan</td><td><input type=text id=nama size=45 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>Potongan</td><td><input type=text id=potongan size=3 maxlength=4 onkeypress=\"return angka_doang(event);\" class=myinputtext>%</td></tr>
                     <tr><td>Satuan</td><td><input type=text id=satuan size=3 maxlength=5 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanDep()>Simpan</button>
	 <button class=mybutton onclick=cancelDep()>Batal</button>
	 </fieldset>";
echo open_theme('Standar Fraksi');

	$str1="select * from ".$dbname.".msfraksi order by kodefraksi";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
                                                 <td style='width:150px;'>Kode</td>
                                                 <td>Keterangan</td>
                                                 <td>Potongan</td>
                                                 <td>Satuan</td>
                                                 <td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>";
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent><td align=center>".$bar1->kodefraksi."</td>
                                                  <td>".$bar1->keterangan."</td>
                                                  <td>".$bar1->potongan."</td>
                                                  <td>".$bar1->satuan."</td>    
                                                  <td><img src=images/edit.png style='height:20px'  caption='Edit' onclick=\"fillField('".$bar1->kodefraksi."','".$bar1->keterangan."','".$bar1->potongan."','".$bar1->satuan."');\"></td></tr>";
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
<!--ind-->

<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/kebun_5nourutmandor.js'></script>


<?php
include('master_mainMenu.php');			

$optaktif="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optaktif.="<option value=0>Tidak Aktif</option>";
$optaktif.="<option value=1>Aktif</option>";


$optnik="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql = "SELECT namakaryawan,karyawanid FROM ".$dbname.".datakaryawan where kodejabatan='37' ";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
			{
			$optnik.="<option value=".$data['karyawanid'].">".$data['namakaryawan']."</option>";
			//$optnik.="<option value=".(int)$data['karyawanid'].">".$data['namakaryawan']."</option>";
			}	


$optkar="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sql1 = "SELECT namakaryawan,karyawanid FROM ".$dbname.".datakaryawan where tipekaryawan in('2','3') ";
$qry1 = mysql_query($sql1) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry1))
			{
			$optkar.="<option value=".$data['karyawanid'].">".$data['namakaryawan']."</option>";
			}	
?>


<?php
OPEN_BOX('',"<b>Karyawan Kemandoran</b>");

//<tr><td width=100>Nik Mandor<td width=10>:</td></td><td><input type=text id=nm size=10 class=myinputtext maxlength=50 style=\"width:200px;\"></td></tr>
//<tr><td width=100>Karyawan ID<td width=10>:</td></td><td><input type=text id=ki size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:200px;\"></td></tr>

echo"<br /><br /><fieldset style='float:left;'>
		<legend>".$_SESSION['lang']['entryForm']."</legend> 
			<table border=0 cellpadding=1 cellspacing=1>
				
				<tr><td>Nik. Mandor<td>:</td></td><td><select id=nm style=\"width:125px;\" >".$optnik."</select></td></tr>
				
				<tr><td width=100>No. Urut<td width=10>:</td></td><td><input type=text id=nu size=10 class=myinputtext maxlength=50 onkeypress=\"return angka_doang(event);\"  style=\"width:25px;\"></td></tr>
				
				<tr><td>Karyawan ID<td>:</td></td><td><select id=ki style=\"width:125px;\" >".$optkar."</select></td></tr>
				
				
	
				<tr><td>Status<td>:</td></td><td><select id=st style=\"width:125px;\" >".$optaktif."</select></td></tr>
				
				<tr><td></td><td></td><br />
					<td><br /><button class=mybutton onclick=simpan()>Simpan</button>
					<button class=mybutton onclick=hapus()>Hapus</button></td></tr>
			</table></fieldset>
					<input type=hidden id=method value='insert'>
					<input type=hidden id=oldnm value='insert'>
					<input type=hidden id=oldnu value='insert'>
					<input type=hidden id=oldki value='insert'>";
CLOSE_BOX();
?>



<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>";
echo "<legend><b>".$_SESSION['lang']['datatersimpan']."</b></legend>";
echo "<div id=container>";
	
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
			 <td align=center style='width:5px;'>No</td>
			 <td align=center style='width:125px;'>Nik Mandor</td>
			 <td align=center style='width:50px;'>No. Urut</td>
			 <td align=center style='width:125px;'>Karyawan ID</td>
			 <td align=center style='width:75px;'>Status Aktif</td>
			 <td align=center style='width:40px;'>Aksi</td></tr>
		 </thead>
		 <tbody id='containerData'><script>loadData()</script>";
        
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div>";
echo close_theme();
echo "</fieldset>";
CLOSE_BOX();
echo close_body();					
?>
<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_5absensi.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['jenisabsensi']);

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeabs']."</td><td><input type=text id=kode size=3 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['keterangan']."</td><td><input type=text id=keterangan size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext></td></tr>
	 <tr><td>".$_SESSION['lang']['grup']."</td><td><select id=grup><option value=0>".$_SESSION['lang']['tidakdibayar']."</option><option value=1>".$_SESSION['lang']['dibayar']."</option></select></td></tr>
     <tr><td>".$_SESSION['lang']['jumlahhk']."</td><td><input type=text id=jumlahhk size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=3 value=0 onblur=change_number(this)></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availvhc']);
echo "<div>";
	$str1="select *,
	     case kelompok when 1 then '".$_SESSION['lang']['dibayar']."'
		 when 0 then '".$_SESSION['lang']['tidakdibayar']."'
		 end as ketgroup 
	     from ".$dbname.".sdm_5absensi order by kodeabsen";
	$res1=mysql_query($str1);

	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kodeabs']."</td>
			<td>".$_SESSION['lang']['keterangan']."</td>
			<td>".$_SESSION['lang']['grup']."</td>
			<td>".$_SESSION['lang']['jumlahhk']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeabsen."</td>
				   <td>".$bar1->keterangan."</td>
				   <td>".$bar1->ketgroup."</td>
				   <td>".$bar1->nilaihk."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeabsen."','".$bar1->keterangan."','".$bar1->kelompok."','".$bar1->nilaihk."');\"></td></tr>";
	}	 
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
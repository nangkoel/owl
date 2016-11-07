<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_5lembur.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['tipelembur']);

$tipelembur='';
$tipelembur="<option value=0>".$_SESSION['lang']['haribiasa']."</option>
            <option value=1>".$_SESSION['lang']['hariminggu']."</option>
			<option value=2>".$_SESSION['lang']['harilibur']."</option>
			<option value=3>".$_SESSION['lang']['hariraya']."</option>
			";

echo"<fieldset style='width:500px;'><table>
     <tr><td>".$_SESSION['lang']['kodeorg']."</td><td><select id=kodeorg><option value='".substr($_SESSION['empl']['lokasitugas'],0,4)."'>".substr($_SESSION['empl']['lokasitugas'],0,4)."</option></select></td></tr>
	 <tr><td>".$_SESSION['lang']['tipelembur']."</td><td><select id=tipelembur>".$tipelembur."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['jamaktual']."</td><td><input type=text id=jamaktual size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=0 onblur=change_number(this)></td></tr>
     <tr><td>".$_SESSION['lang']['jamlembur']."</td><td><input type=text id=jamlembur size=3 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4 value=0 onblur=change_number(this)></td></tr>
	 </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanJ()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelJ()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo open_theme($_SESSION['lang']['availfunct']);
echo "<div>";
	$str1="select *,
	     case tipelembur when '0' then '".$_SESSION['lang']['haribiasa']."'
		 when '1' then '".$_SESSION['lang']['hariminggu']."'
		 when '2' then '".$_SESSION['lang']['harilibur']."'
		 when '3' then '".$_SESSION['lang']['hariraya']."'
		 end as ketgroup 
	     from ".$dbname.".sdm_5lembur 
		 where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		 order by tipelembur,jamaktual";
	$res1=mysql_query($str1);

	echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
	     <thead>
		 <tr class=rowheader>
		    <td style='width:150px;'>".$_SESSION['lang']['kodeorg']."</td>
			<td>".$_SESSION['lang']['tipelembur']."</td>
			<td>".$_SESSION['lang']['jamaktual']."</td>
			<td>".$_SESSION['lang']['jamlembur']."</td>
			<td style='width:30px;'>*</td></tr>
		 </thead>
		 <tbody id=container>"; 
	while($bar1=mysql_fetch_object($res1))
	{
		echo"<tr class=rowcontent>
		           <td align=center>".$bar1->kodeorg."</td>
				   <td>".$bar1->ketgroup."</td>
				   <td align=center>".$bar1->jamaktual."</td>
				   <td align=center>".$bar1->jamlembur."</td>
				   <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."','".$bar1->jamlembur."');\">
					<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$bar1->kodeorg."','".$bar1->tipelembur."','".$bar1->jamaktual."');\"></td></tr>";

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